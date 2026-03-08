<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PixUpGateway implements PaymentGatewayInterface
{
    private string $baseUrl = 'https://api.pixup.com/v1';
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
            throw new \Exception('PixUp: Credenciais não configuradas (client_id e client_secret são obrigatórios)');
        }

        // Verifica se há token em cache (válido por 1 hora)
        $cacheKey = "pixup_token_{$this->clientId}";
        $cachedToken = Cache::get($cacheKey);
        
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $response = Http::asForm()->post("{$this->baseUrl}/oauth/token", [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;

                if ($token) {
                    Cache::put($cacheKey, $token, now()->addMinutes(50));
                    return $token;
                }
            }

            Log::error('PixUp: Erro ao obter token de acesso', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('PixUp: Falha ao obter token de acesso');
        } catch (\Exception $e) {
            Log::error('PixUp: Exceção ao obter token', [
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
            
            if (is_array($errorJson)) {
                $errorMessage = $errorJson['message'] ?? $errorJson['error'] ?? $errorJson['error_description'] ?? $errorBody;
            } else {
                $errorMessage = $errorBody;
            }
            
            Log::error("PixUp: Erro na requisição {$method} {$endpoint}", [
                'status' => $response->status(),
                'response' => $errorBody,
                'error_message' => $errorMessage,
                'data' => $data,
            ]);

            throw new \Exception("PixUp: Erro na requisição - Status {$response->status()} - {$errorMessage}");
        }

        $jsonResponse = $response->json();
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("PixUp: Resposta JSON inválida", [
                'json_error' => json_last_error_msg(),
                'response_body' => $response->body(),
            ]);
            throw new \Exception("PixUp: Resposta inválida do servidor");
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
            $description = $payerData['description'] ?? 'Pagamento via PIX';

            // Formata documento e limpa dados
            $document = preg_replace('/\D/', '', $payerData['cpf'] ?? $payerData['document'] ?? $payerData['payer_cpf'] ?? '');
            
            // Fallback para documento se vazio ou inválido (evita erro 422 em alguns gateways)
            if (empty($document) || !in_array(strlen($document), [11, 14])) {
                $document = '19100000000'; // CPF de teste VÁLIDO
            }

            $payload = [
                'amount' => (float) $amount,
                'external_id' => (string) $externalId,
                'description' => $description,
                'payer' => [
                    'name' => $payerData['name'] ?? $payerData['payer_name'] ?? 'Cliente',
                    'document' => $document,
                    'email' => $payerData['email'] ?? $payerData['payer_email'] ?? 'cliente@email.com',
                ],
                'webhook_url' => $postbackUrl,
            ];

            $response = $this->makeRequest('POST', '/pix/qrcode', $payload);

            $qrCode = $response['qr_code'] ?? $response['qrcode'] ?? $response['pix_copy_paste'] ?? '';
            $expiresAt = isset($response['expires_at']) 
                ? \Carbon\Carbon::parse($response['expires_at']) 
                : now()->addMinutes(5);

            return [
                'status' => 'pending',
                'qr_code' => $qrCode,
                'external_id' => $externalId,
                'transaction_id' => $response['transaction_id'] ?? $response['id'] ?? null,
                'expires_at' => $expiresAt->toIso8601String(),
            ];
        } catch (\Exception $e) {
            Log::error('PixUp: Erro ao criar QR Code PIX', [
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

            $payload = [
                'amount' => (float) $amount,
                'description' => $description ?? 'Saque via PIX',
                'external_id' => (string) $externalId,
                'pix_key' => $recipientData['pix_key'] ?? $recipientData['key'] ?? '',
                'pix_key_type' => $recipientData['key_type'] ?? $recipientData['keyType'] ?? 'CPF',
                'recipient' => [
                    'name' => $recipientData['name'] ?? '',
                    'document' => $recipientData['document'] ?? $recipientData['cpf'] ?? '',
                ],
            ];

            $response = $this->makeRequest('POST', '/pix/payment', $payload);

            return [
                'status' => 'processing',
                'external_id' => $externalId,
                'transaction_id' => $response['transaction_id'] ?? $response['id'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('PixUp: Erro ao criar pagamento PIX', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'recipientData' => $recipientData,
            ]);

            throw $e;
        }
    }

    /**
     * Cria um pagamento com cartão de crédito
     *
     * @param float $amount
     * @param array $cardData
     * @param array $payerData
     * @return array
     */
    public function createCreditCard(float $amount, array $cardData, array $payerData): array
    {
        try {
            $externalId = \Illuminate\Support\Str::uuid()->toString();

            $payload = [
                'amount' => (float) $amount,
                'external_id' => $externalId,
                'card' => [
                    'number' => $cardData['number'] ?? '',
                    'cvv' => $cardData['cvv'] ?? '',
                    'expiry' => $cardData['expiry'] ?? '',
                    'holder' => $cardData['holder'] ?? '',
                ],
                'payer' => [
                    'name' => $payerData['name'] ?? '',
                    'document' => $payerData['cpf'] ?? $payerData['document'] ?? '',
                    'email' => $payerData['email'] ?? '',
                ],
                'installments' => $cardData['installments'] ?? 1,
            ];

            $response = $this->makeRequest('POST', '/credit-card/payment', $payload);

            return [
                'status' => $response['status'] ?? 'pending',
                'external_id' => $externalId,
                'transaction_id' => $response['transaction_id'] ?? $response['id'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('PixUp: Erro ao criar pagamento com cartão', [
                'error' => $e->getMessage(),
                'amount' => $amount,
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

            Log::info('PixUp: Saldo consultado', [
                'balance' => $balance,
                'available' => $available,
            ]);

            return [
                'balance' => $balance,
                'available' => $available,
            ];
        } catch (\Exception $e) {
            Log::error('PixUp: Erro ao consultar saldo', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

