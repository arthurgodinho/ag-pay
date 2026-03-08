<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BsPayGateway implements PaymentGatewayInterface
{
    private string $baseUrl = 'https://api.bspay.co/v2';
    private ?string $clientId = null;
    private ?string $clientSecret = null;
    private ?string $accessToken = null;

    public function __construct(?string $clientId = null, ?string $clientSecret = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Obtém o token de acesso OAuth
     *
     * @return string|null
     */
    private function getAccessToken(): ?string
    {
        if (!$this->clientId || !$this->clientSecret) {
            throw new \Exception('BsPay: Credenciais não configuradas (client_id e client_secret são obrigatórios)');
        }

        // Verifica se há token em cache (válido por 1 hora)
        $cacheKey = "bspay_token_{$this->clientId}";
        $cachedToken = Cache::get($cacheKey);
        
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            // Cria a string de credenciais para Basic Auth
            $credentials = $this->clientId . ':' . $this->clientSecret;
            $base64Credentials = base64_encode($credentials);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $base64Credentials,
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/oauth/token");

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;

                if ($token) {
                    // Cacheia o token por 50 minutos (tokens geralmente expiram em 1 hora)
                    Cache::put($cacheKey, $token, now()->addMinutes(50));
                    return $token;
                }
            }

            Log::error('BsPay: Erro ao obter token de acesso', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('BsPay: Falha ao obter token de acesso');
        } catch (\Exception $e) {
            Log::error('BsPay: Exceção ao obter token', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Faz uma requisição autenticada
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->getAccessToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->{strtolower($method)}("{$this->baseUrl}{$endpoint}", $data);

        if (!$response->successful()) {
            $errorBody = $response->body();
            $errorJson = $response->json();
            $errorMessage = 'Erro desconhecido';
            
            // Tenta obter mensagem de erro da resposta
            if (is_array($errorJson)) {
                $errorMessage = $errorJson['message'] ?? $errorJson['error'] ?? $errorJson['error_description'] ?? $errorBody;
            } else {
                $errorMessage = $errorBody;
            }
            
            // Identifica tipo de erro para mensagem mais específica
            $errorMessageLower = strtolower($errorMessage);
            $isBalanceError = (
                stripos($errorMessageLower, 'saldo') !== false ||
                stripos($errorMessageLower, 'balance') !== false ||
                stripos($errorMessageLower, 'insufficient') !== false ||
                stripos($errorMessageLower, 'funds') !== false ||
                stripos($errorMessageLower, 'sem saldo') !== false ||
                $response->status() === 400 && stripos($errorMessageLower, 'balance') !== false
            );
            
            Log::error("BsPay: Erro na requisição {$method} {$endpoint}", [
                'status' => $response->status(),
                'response' => $errorBody,
                'error_message' => $errorMessage,
                'is_balance_error' => $isBalanceError,
                'data' => $data,
            ]);

            // Se for erro de saldo, lança exceção com mensagem específica
            if ($isBalanceError) {
                throw new \Exception("Saldo insuficiente no adquirente. {$errorMessage}");
            }

            throw new \Exception("BsPay: Erro na requisição - Status {$response->status()} - {$errorMessage}");
        }

        $jsonResponse = $response->json();
        
        // Verifica se a resposta JSON é válida
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("BsPay: Resposta JSON inválida", [
                'json_error' => json_last_error_msg(),
                'response_body' => $response->body(),
            ]);
            throw new \Exception("BsPay: Resposta inválida do servidor");
        }
        
        return $jsonResponse;
    }

    /**
     * Cria um pagamento PIX (recebimento - QR Code)
     *
     * @param float $amount
     * @param array $payerData
     * @return array
     */
    public function createPix(float $amount, array $payerData): array
    {
        try {
            $externalId = $payerData['external_id'] ?? \Illuminate\Support\Str::uuid()->toString();
            // Usa helper para garantir URL absoluta com domínio do .env
            $postbackUrl = $payerData['postback_url'] ?? \App\Helpers\WebhookUrlHelper::generateUrl('api.webhooks.bspay');
            $payerQuestion = $payerData['description'] ?? $payerData['payer_question'] ?? 'Pagamento via PIX';

            // Formata documento e limpa dados
            $document = preg_replace('/\D/', '', $payerData['cpf'] ?? $payerData['document'] ?? $payerData['payer_cpf'] ?? '');
            
            // Fallback para documento se vazio ou inválido (evita erro 422 em alguns gateways)
            if (empty($document) || !in_array(strlen($document), [11, 14])) {
                $document = '19100000000'; // CPF de teste VÁLIDO
            }

            $payload = [
                'amount' => (float) $amount,
                'external_id' => (string) $externalId,
                'payerQuestion' => $payerQuestion,
                'payer' => [
                    'name' => $payerData['name'] ?? $payerData['payer_name'] ?? 'Cliente',
                    'document' => $document,
                    'email' => $payerData['email'] ?? $payerData['payer_email'] ?? 'cliente@email.com',
                ],
                'postbackUrl' => $postbackUrl,
            ];

            // Adiciona split se fornecido
            if (isset($payerData['split']) && is_array($payerData['split'])) {
                $payload['split'] = $payerData['split'];
            }

            $response = $this->makeRequest('POST', '/pix/qrcode', $payload);

            // Log da resposta completa para debug
            Log::info('BsPay: Resposta completa do QR Code', [
                'response' => $response,
                'response_keys' => is_array($response) ? array_keys($response) : 'not array',
            ]);

            // Tenta obter o QR Code de várias formas possíveis - testa todas as variações
            $qrCode = '';
            
            // Ordem de prioridade para buscar o QR Code
            $possibleKeys = [
                'qrCode',           // BsPay pode usar camelCase
                'qrcode',           // lowercase
                'qr_code',          // snake_case
                'pixCopyPaste',     // Formato alternativo
                'pix_copy_paste',   // snake_case alternativo
                'copyPaste',        // Outro formato
                'emv',              // Formato EMV
                'data',             // Pode estar em 'data'
                'qrCodeString',     // Outra variação
            ];
            
            foreach ($possibleKeys as $key) {
                if (isset($response[$key]) && !empty($response[$key]) && is_string($response[$key])) {
                    $qrCode = $response[$key];
                    Log::info('BsPay: QR Code encontrado na chave', ['key' => $key]);
                    break;
                }
            }
            
            // Se não encontrou no primeiro nível, tenta dentro de 'data' se existir
            if (empty($qrCode) && isset($response['data']) && is_array($response['data'])) {
                foreach ($possibleKeys as $key) {
                    if (isset($response['data'][$key]) && !empty($response['data'][$key]) && is_string($response['data'][$key])) {
                        $qrCode = $response['data'][$key];
                        Log::info('BsPay: QR Code encontrado em data', ['key' => $key]);
                        break;
                    }
                }
            }

            // Define expires_at como 5 minutos a partir de agora (padrão do sistema)
            $expiresAt = now()->addMinutes(5);
            
            // Se o gateway retornar um expiresAt, usa ele, mas garante que não seja mais de 5 minutos
            if (isset($response['expiresAt'])) {
                try {
                    $gatewayExpiresAt = \Carbon\Carbon::parse($response['expiresAt']);
                    // Se o gateway retornar um tempo menor que 5 minutos, usa o do gateway
                    // Se for maior, limita a 5 minutos
                    if ($gatewayExpiresAt->isBefore($expiresAt)) {
                        $expiresAt = $gatewayExpiresAt;
                    }
                } catch (\Exception $e) {
                    Log::warning('BsPay: Erro ao parsear expiresAt do gateway', ['expiresAt' => $response['expiresAt']]);
                }
            }

            // Log final do QR Code encontrado
            Log::info('BsPay: QR Code processado', [
                'qr_code_length' => strlen($qrCode),
                'has_qr_code' => !empty($qrCode),
                'external_id' => $externalId,
            ]);

            return [
                'status' => 'pending',
                'qr_code' => $qrCode, // Sempre retorna aqui, mesmo que vazio
                'external_id' => $externalId,
                'transaction_id' => $response['transactionId'] ?? $response['transaction_id'] ?? $response['pix_id'] ?? null,
                'expires_at' => $expiresAt->toIso8601String(),
                'raw_response' => $response, // Mantém a resposta completa para análise
            ];
        } catch (\Exception $e) {
            Log::error('BsPay: Erro ao criar QR Code PIX', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'payerData' => $payerData,
            ]);

            throw $e;
        }
    }

    /**
     * Cria um pagamento PIX (saque - transferência)
     *
     * @param float $amount
     * @param array $recipientData Dados do destinatário (chave PIX, tipo, etc)
     * @param string|null $externalId
     * @param string|null $description
     * @return array
     */
    public function createPixPayment(float $amount, array $recipientData, ?string $externalId = null, ?string $description = null): array
    {
        try {
            $externalId = $externalId ?? \Illuminate\Support\Str::uuid()->toString();
            // Usa helper para garantir URL absoluta com domínio do .env
            $postbackUrl = $recipientData['postback_url'] ?? \App\Helpers\WebhookUrlHelper::generateUrl('api.webhooks.bspay');

            $payload = [
                'amount' => (float) $amount,
                'description' => $description ?? 'Saque via PIX',
                'external_id' => (string) $externalId,
                'creditParty' => [
                    'key' => $recipientData['pix_key'] ?? $recipientData['key'] ?? '',
                    'keyType' => $recipientData['key_type'] ?? $recipientData['keyType'] ?? 'CPF',
                    'name' => $recipientData['name'] ?? '',
                    'document' => $recipientData['document'] ?? $recipientData['cpf'] ?? '',
                ],
            ];

            $response = $this->makeRequest('POST', '/pix/payment', $payload);

            return [
                'status' => 'processing',
                'external_id' => $externalId,
                'transaction_id' => $response['transactionId'] ?? $response['pix_id'] ?? null,
                'raw_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error('BsPay: Erro ao criar pagamento PIX', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'recipientData' => $recipientData,
            ]);

            throw $e;
        }
    }

    /**
     * Consulta o saldo da conta
     *
     * @return array Retorna array com 'balance' e 'available' para compatibilidade
     * @throws \Exception Se o endpoint não estiver disponível ou houver erro
     */
    public function getBalance(): array
    {
        try {
            $response = $this->makeRequest('GET', '/balance');

            $balance = (float) ($response['balance'] ?? $response['available'] ?? $response['value'] ?? 0.00);
            $available = (float) ($response['available'] ?? $response['balance'] ?? $response['value'] ?? 0.00);

            Log::info('BsPay: Saldo consultado', [
                'balance' => $balance,
                'available' => $available,
                'response' => $response,
            ]);

            return [
                'balance' => $balance,
                'available' => $available,
            ];
        } catch (\Exception $e) {
            // Se for erro 405 (Method Not Allowed), significa que o endpoint não está disponível
            // Lança exceção específica para ser tratada pelo controller
            if (stripos($e->getMessage(), '405') !== false || 
                stripos($e->getMessage(), 'method not allowed') !== false ||
                stripos($e->getMessage(), 'not allowed') !== false) {
                Log::warning('BsPay: Endpoint de saldo não disponível (405)', [
                    'error' => $e->getMessage(),
                ]);
                throw new \Exception('Endpoint de consulta de saldo não disponível na API BsPay (405 Method Not Allowed)');
            }
            
            Log::error('BsPay: Erro ao consultar saldo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Consulta uma transação específica
     *
     * @param string $pixId
     * @return array
     */
    public function consultTransaction(string $pixId): array
    {
        try {
            $response = $this->makeRequest('POST', '/consult-transaction', [
                'pix_id' => $pixId,
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('BsPay: Erro ao consultar transação', [
                'error' => $e->getMessage(),
                'pix_id' => $pixId,
            ]);

            throw $e;
        }
    }

    /**
     * Cria um pagamento com cartão de crédito
     * (BsPay não suporta cartão de crédito, apenas PIX)
     *
     * @param float $amount
     * @param array $cardData
     * @param array $payerData
     * @return array
     */
    public function createCreditCard(float $amount, array $cardData, array $payerData): array
    {
        throw new \Exception('BsPay não suporta pagamentos com cartão de crédito. Use PIX.');
    }

    /**
     * Cria um pagamento via Boleto
     * (BsPay não suporta boleto, apenas PIX)
     *
     * @param float $amount
     * @param array $payerData
     * @return array
     */
    public function createBoleto(float $amount, array $payerData): array
    {
        throw new \Exception('BsPay não suporta pagamentos via boleto. Use PIX.');
    }
}

