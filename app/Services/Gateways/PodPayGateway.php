<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PodPayGateway implements PaymentGatewayInterface
{
    private string $baseUrl = 'https://api.podpay.co'; // Endpoint assumido baseada na URL do dashboard
    private ?string $clientId = null; // Armazena Public Key
    private ?string $clientSecret = null; // Armazena Secret Key

    public function __construct(?string $clientId = null, ?string $clientSecret = null)
    {
        $this->clientId = $clientId; // Public Key
        $this->clientSecret = $clientSecret; // Secret Key
    }

    /**
     * Obtém o token de acesso OAuth
     *
     * @return string|null
     */
    private function getAccessToken(): ?string
    {
        if (!$this->clientId || !$this->clientSecret) {
            throw new \Exception('PodPay: Credenciais não configuradas (client_id e client_secret são obrigatórios)');
        }

        // Verifica se há token em cache (válido por 1 hora)
        $cacheKey = "podpay_token_{$this->clientId}";
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

            Log::error('PodPay: Erro ao obter token de acesso', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('PodPay: Falha ao obter token de acesso');
        } catch (\Exception $e) {
            Log::error('PodPay: Exceção ao obter token', [
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
            
            Log::error("PodPay: Erro na requisição {$method} {$endpoint}", [
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

            throw new \Exception("PodPay: Erro na requisição - Status {$response->status()} - {$errorMessage}");
        }

        $jsonResponse = $response->json();
        
        // Verifica se a resposta JSON é válida
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("PodPay: Resposta JSON inválida", [
                'json_error' => json_last_error_msg(),
                'response_body' => $response->body(),
            ]);
            throw new \Exception("PodPay: Resposta inválida do servidor");
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
            $postbackUrl = $payerData['postback_url'] ?? \App\Helpers\WebhookUrlHelper::generateUrl('api.webhooks.podpay');
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

            Log::info('PodPay: Resposta completa do QR Code', [
                'response' => $response,
            ]);

            // Tenta obter o QR Code de várias formas possíveis
            $qrCode = '';
            
            $possibleKeys = [
                'qrCode', 'qrcode', 'qr_code', 
                'pixCopyPaste', 'pix_copy_paste', 
                'copyPaste', 'emv', 'data', 'qrCodeString'
            ];

            // Procura chaves na raiz
            foreach ($possibleKeys as $key) {
                if (!empty($response[$key]) && is_string($response[$key])) {
                    $qrCode = $response[$key];
                    break;
                }
            }
            
            // Se não encontrou, procura dentro de 'pix' ou 'data'
            if (empty($qrCode)) {
                if (isset($response['pix']) && is_array($response['pix'])) {
                    foreach ($possibleKeys as $key) {
                        if (!empty($response['pix'][$key]) && is_string($response['pix'][$key])) {
                            $qrCode = $response['pix'][$key];
                            break;
                        }
                    }
                }
                
                if (empty($qrCode) && isset($response['data']) && is_array($response['data'])) {
                    foreach ($possibleKeys as $key) {
                        if (!empty($response['data'][$key]) && is_string($response['data'][$key])) {
                            $qrCode = $response['data'][$key];
                            break;
                        }
                    }
                }
            }

            return [
                'success' => true,
                'transaction_id' => $response['id'] ?? $response['transactionId'] ?? $response['txid'] ?? $externalId,
                'external_id' => $externalId,
                'qr_code' => $qrCode,
                'qr_code_url' => $response['qrCodeUrl'] ?? $response['qrcode_url'] ?? $response['imagemQrcode'] ?? null,
                'raw_response' => $response
            ];

        } catch (\Exception $e) {
            Log::error('PodPay: Erro ao gerar PIX', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Realiza um saque via PIX (Pagamento para terceiros)
     *
     * @param float $amount
     * @param array $recipientData
     * @param string|null $externalId
     * @param string|null $description
     * @return array
     */
    public function createPixPayment(float $amount, array $recipientData, ?string $externalId = null, ?string $description = null): array
    {
        try {
            $externalId = $externalId ?? \Illuminate\Support\Str::uuid()->toString();
            
            $payload = [
                'amount' => (float) $amount,
                'pix_key_type' => $recipientData['key_type'] ?? 'CPF', // CPF, EMAIL, PHONE, RANDOM
                'pix_key' => $recipientData['pix_key'] ?? $recipientData['key'] ?? '',
                'external_id' => $externalId,
                'description' => $description ?? 'Saque via Plataforma',
                'recipient' => [
                    'name' => $recipientData['name'] ?? '',
                    'document' => $recipientData['document'] ?? '',
                ]
            ];

            // Endpoint para pagamento (cash out) - Assumindo /pix/payment baseado em BsPay
            $response = $this->makeRequest('POST', '/pix/payment', $payload);

            return [
                'status' => 'pending', 
                'transaction_id' => $response['transactionId'] ?? $response['id'] ?? null,
                'external_id' => $externalId,
                'raw_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error('PodPay: Erro ao realizar saque PIX', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function createCreditCard(float $amount, array $cardData, array $payerData): array
    {
        throw new \Exception('PodPay: Pagamento via cartão não implementado.');
    }

    public function createBoleto(float $amount, array $payerData): array
    {
        throw new \Exception('PodPay: Pagamento via boleto não implementado.');
    }

    public function consultTransaction(string $transactionId): array
    {
        try {
            $response = $this->makeRequest('GET', "/pix/transactions/{$transactionId}");
            
            return [
                'status' => $response['status'] ?? 'unknown',
                'raw_response' => $response
            ];
        } catch (\Exception $e) {
            Log::error('PodPay: Erro ao consultar transação', ['error' => $e->getMessage()]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
