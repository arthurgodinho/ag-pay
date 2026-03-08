<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Helpers\WebhookUrlHelper;
use App\Models\Setting;

class PagueMaxGateway implements PaymentGatewayInterface
{
    private string $baseUrl;
    private ?string $clientId = null;
    private ?string $clientSecret = null;

    public function __construct(?string $clientId = null, ?string $clientSecret = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->baseUrl = Setting::get('paguemax_api_url', 'https://paguemax.com/api/v1');
        
        // Remove trailing slash if exists
        $this->baseUrl = rtrim($this->baseUrl, '/');
        
        // Corrigir caso o usuário tenha colado a URL completa do endpoint PIX na configuração
        // Se a URL terminar com /payments/pix, removemos para ficar apenas a base
        if (str_ends_with($this->baseUrl, '/payments/pix')) {
            $this->baseUrl = str_replace('/payments/pix', '', $this->baseUrl);
            // Remove trailing slash again just in case
            $this->baseUrl = rtrim($this->baseUrl, '/');
        }
    }

    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        if (!$this->clientId || !$this->clientSecret) {
            throw new \Exception('PagueMax: Credenciais (Client ID ou Token Secret) não configuradas');
        }

        // Endpoint construction: if endpoint starts with http, use it, else append to base
        $url = str_starts_with($endpoint, 'http') ? $endpoint : "{$this->baseUrl}{$endpoint}";

        // Headers configuration - assuming Bearer Token (using client_secret as token) or Client-ID/Secret headers
        // Based on "Autenticação via Token Bearer", likely the client_secret is the token or we request one.
        // User said "Cliente ID e Token Secret". I'll send both or use Secret as Bearer.
        // Common pattern: Authorization: Bearer <secret> OR Basic Auth.
        // I will assume Bearer Token using the Secret, and maybe Client-ID in body or header.
        
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->clientSecret, // Assuming Secret is the Bearer token
            'X-Client-ID' => $this->clientId, // Sending Client ID as header just in case
        ];

        $response = Http::withHeaders($headers)->{strtolower($method)}($url, $data);

        if (!$response->successful()) {
            Log::error("PagueMax: Erro na requisição {$endpoint}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            // Try to parse error message
            $errorMsg = "PagueMax: Erro na requisição - " . $response->status();
            $json = $response->json();
            
            if (isset($json['message'])) {
                $errorMsg .= " - " . $json['message'];
            }
            
            if (isset($json['errors']) && is_array($json['errors'])) {
                $errorMsg .= " (" . json_encode($json['errors']) . ")";
            }
            
            throw new \Exception($errorMsg);
        }

        return $response->json();
    }

    public function createPix(float $amount, array $payerData): array
    {
        $externalId = $payerData['external_id'] ?? Str::uuid()->toString();
        $postbackUrl = $payerData['postback_url'] ?? WebhookUrlHelper::generateUrl('api.webhooks.paguemax');
        
        // Prepare data
        $name = $payerData['name'] ?? 'Cliente';
        $email = $payerData['email'] ?? 'cliente@email.com';
        $document = $payerData['cpf'] ?? '00000000000';
        $phone = $payerData['phone'] ?? '';
        
        // Clean document (digits only)
        $document = preg_replace('/[^0-9]/', '', $document);

        // Construct payload with multiple formats to maximize compatibility
        $payload = [
            'amount' => $amount,
            'external_id' => $externalId,
            'webhook_url' => $postbackUrl,
            'description' => $payerData['description'] ?? 'Pagamento',
            
            // Format 1: Nested payer object
            'payer' => [
                'name' => $name,
                'document' => $document,
                'email' => $email,
                'phone' => $phone,
            ],
            
            // Format 2: Flat snake_case fields (common in Laravel APIs)
            'payer_name' => $name,
            'payer_email' => $email,
            'payer_document' => $document,
            'payer_cpf' => $document,
            'payer_phone' => $phone,
            
            // Format 3: Customer object (alternative common name)
            'customer' => [
                'name' => $name,
                'document' => $document,
                'email' => $email,
                'phone' => $phone,
            ]
        ];

        // User specifically mentioned /payments/pix
        $response = $this->makeRequest('POST', '/payments/pix', $payload);
        
        Log::info('PagueMax: Create PIX Response', ['payload' => $payload, 'response' => $response]);
        
        // Helper to find key recursively or in specific paths
        $findKey = function($array, $keys) {
            foreach ($keys as $key) {
                if (isset($array[$key]) && !empty($array[$key])) return $array[$key];
            }
            // Check common nested paths
            foreach (['data', 'pix', 'payment', 'attributes'] as $parent) {
                if (isset($array[$parent]) && is_array($array[$parent])) {
                    foreach ($keys as $key) {
                        if (isset($array[$parent][$key]) && !empty($array[$parent][$key])) return $array[$parent][$key];
                    }
                }
            }
            return '';
        };
        
        // Mapping response fields (adjust as needed based on real API)
        $qrCode = $findKey($response, ['qrcode', 'qr_code', 'payload', 'emv', 'emv_payload', 'code']);
        $qrCodeBase64 = $findKey($response, ['qrcode_base64', 'qr_code_base64', 'image', 'base64', 'qrcode_image']);
        $transactionId = $findKey($response, ['id', 'transaction_id', 'uuid', 'payment_id']) ?: $externalId;
        
        return [
            'status' => 'pending',
            'qr_code' => $qrCode,
            'qr_code_base64' => $qrCodeBase64,
            'transaction_id' => $transactionId,
            'external_id' => $externalId,
            'raw_response' => $response
        ];
    }

    public function createCreditCard(float $amount, array $cardData, array $payerData): array
    {
        throw new \Exception('PagueMax: Pagamento com cartão não implementado.');
    }

    public function createBoleto(float $amount, array $payerData): array
    {
        throw new \Exception('PagueMax: Boleto não implementado.');
    }

    /**
     * Realiza um saque via PIX (Pagamento para terceiros)
     * Implementação do método exigido pelo WithdrawalService (mesmo que não esteja na interface padrão)
     */
    public function createPixPayment(float $amount, array $recipientData, ?string $externalId = null, ?string $description = null): array
    {
        try {
            $externalId = $externalId ?? Str::uuid()->toString();
            
            $payload = [
                'amount' => (float) $amount,
                'pix_key' => $recipientData['pix_key'] ?? '',
                'pix_key_type' => $recipientData['key_type'] ?? 'CPF',
                'external_id' => $externalId,
                'description' => $description ?? 'Saque',
                'recipient' => [
                    'name' => $recipientData['name'] ?? '',
                    'document' => $recipientData['document'] ?? '',
                ]
            ];

            // Endpoint correto fornecido pelo suporte (padrão: cashout/pix, mas configurável)
            $endpoint = Setting::get('paguemax_withdrawal_api_url', '/cashout/pix');
            $response = $this->makeRequest('POST', $endpoint, $payload);

            return [
                'status' => 'processing', 
                'transaction_id' => $response['id'] ?? null,
                'external_id' => $externalId,
                'raw_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error('PagueMax: Erro ao realizar saque PIX', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function consultTransaction(string $transactionId): array
    {
        return $this->makeRequest('GET', "/transactions/{$transactionId}");
    }
    
    /**
     * Verifica o saldo disponível na conta PagueMax
     * Opcional, usado pelo WithdrawalService
     */
    public function getBalance(): array
    {
        // Assuming /balance endpoint
        return $this->makeRequest('GET', '/balance');
    }
}
