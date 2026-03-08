<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Helpers\WebhookUrlHelper;
use App\Models\Setting;

class ZoomPagGateway implements PaymentGatewayInterface
{
    private string $baseUrl = 'https://api.zoompag.com';
    private ?string $apiKey = null;
    private ?string $postUrl = null;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey;
        // Recupera o Link Post configurado (API URL) ou usa um padrão
        $this->postUrl = Setting::get('zoompag_post_url', 'https://api.zoompag.com');
        
        if (!empty($this->postUrl)) {
            $this->baseUrl = rtrim($this->postUrl, '/');
        }
    }

    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        if (!$this->apiKey) {
            throw new \Exception('ZoomPag: Chave de API não configurada');
        }

        $headers = [
            'x-api-key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $url = str_starts_with($endpoint, 'http') ? $endpoint : "{$this->baseUrl}{$endpoint}";
        
        Log::info("ZoomPag: Request {$method} {$url}", ['data' => $data]);

        $response = Http::withHeaders($headers)->{strtolower($method)}($url, $data);

        if (!$response->successful()) {
            Log::error("ZoomPag: Erro na requisição {$endpoint}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception("ZoomPag: Erro na requisição - " . $response->status() . " - " . $response->body());
        }

        return $response->json();
    }

    public function createPix(float $amount, array $payerData): array
    {
        $externalId = $payerData['external_id'] ?? Str::uuid()->toString();
        
        $webhookUrl = WebhookUrlHelper::generateUrl('api.webhooks.zoompag');
        
        // Ensure phone number is formatted correctly (digits only) or use a placeholder
        $phone = preg_replace('/\D/', '', $payerData['phone'] ?? '');
        if (empty($phone)) {
            $phone = '11999999999'; // Default placeholder if phone is missing
        }

        // Get document number (digits only)
        $documentNumber = preg_replace('/\D/', '', $payerData['cpf'] ?? $payerData['document'] ?? '');
        
        // Fallback para documento se vazio ou inválido (evita erro 422 em alguns gateways)
        if (empty($documentNumber) || !in_array(strlen($documentNumber), [11, 14])) {
            $documentNumber = '19100000000'; // CPF de teste VÁLIDO
        }
        
        $documentType = strlen($documentNumber) > 11 ? 'CNPJ' : 'CPF';
        
        $payload = [
            'method' => 'PIX',
            'amount' => (int) ($amount * 100), // Valor em centavos
            'reference_id' => $externalId,
            'customer' => [
                'name' => $payerData['name'] ?? '',
                'document' => $documentNumber,
                'documentType' => $documentType,
                'email' => $payerData['email'] ?? '',
                'phone' => $phone,
            ],
            'items' => [
                [
                    'title' => 'Depósito PIX',
                    'quantity' => 1,
                    'amount' => (int) ($amount * 100),
                    'tangible' => false,
                ]
            ],
            'postback_url' => $webhookUrl,
        ];

        // Endpoint para criação de cobrança Pix (CashIn)
        $response = $this->makeRequest('POST', '/transactions', $payload);
        
        Log::info('ZoomPag: createPix Response', ['response' => $response]);

        // Tenta extrair o QR Code de várias localizações possíveis na resposta
        $qrCode = $response['data']['pix']['qrcode'] 
            ?? $response['data']['pix']['qrcodeUrl'] 
            ?? $response['qrcode'] 
            ?? $response['qr_code'] 
            ?? $response['payload'] 
            ?? $response['pixCopiaECola'] // Common field name
            ?? '';

        $transactionId = $response['data']['id'] 
            ?? $response['id'] 
            ?? $response['transaction_id'] 
            ?? $externalId;
        
        return [
            'status' => 'pending',
            'qr_code' => $qrCode,
            'transaction_id' => $transactionId,
            'raw_response' => $response
        ];
    }

    public function createCreditCard(float $amount, array $cardData, array $payerData): array
    {
        $externalId = $payerData['external_id'] ?? Str::uuid()->toString();
        $webhookUrl = WebhookUrlHelper::generateUrl('api.webhooks.zoompag');

        $payload = [
            'method' => 'CREDIT_CARD',
            'amount' => (int) ($amount * 100), // Valor em centavos
            'reference_id' => $externalId,
            'card' => [
                'number' => $cardData['number'],
                'holder_name' => $cardData['holder_name'],
                'expiration_month' => $cardData['expiration_month'],
                'expiration_year' => $cardData['expiration_year'],
                'cvv' => $cardData['cvv'],
            ],
            'customer' => [
                'name' => $payerData['name'] ?? '',
                'cpf' => preg_replace('/\D/', '', $payerData['cpf'] ?? $payerData['document'] ?? ''),
                'email' => $payerData['email'] ?? '',
            ],
            'items' => [
                [
                    'title' => 'Pagamento Cartão',
                    'quantity' => 1,
                    'unit_price' => (int) ($amount * 100),
                    'tangible' => false,
                ]
            ],
            'installments' => $cardData['installments'] ?? 1,
            'postback_url' => $webhookUrl,
        ];

        // Endpoint para cartão
        $response = $this->makeRequest('POST', '/transactions', $payload);

        return [
            'status' => $response['status'] ?? 'pending',
            'transaction_id' => $response['id'] ?? $externalId,
            'raw_response' => $response
        ];
    }

    public function createBoleto(float $amount, array $payerData): array
    {
        throw new \Exception('ZoomPag: Boleto não implementado.');
    }

    public function createPixPayment(float $amount, array $recipientData, ?string $externalId = null, ?string $description = null): array
    {
        try {
            $externalId = $externalId ?? Str::uuid()->toString();
            
            // Mapeamento dos tipos de chave conforme solicitado
            $keyTypeMap = [
                'CPF' => 'CPF',
                'CNPJ' => 'CNPJ',
                'EMAIL' => 'EMAIL',
                'PHONE' => 'PHONE',
                'RANDOM' => 'EVP', // EVP = Chave aleatória
                'EVP' => 'EVP'
            ];

            $keyType = strtoupper($recipientData['key_type'] ?? 'CPF');
            $pixKeyType = $keyTypeMap[$keyType] ?? 'CPF';

            $payload = [
                'amount' => (int) ($amount * 100), // Valor em centavos
                'pix_key_type' => $pixKeyType,
                'pix_key' => $recipientData['pix_key'] ?? $recipientData['key'] ?? '',
                'external_id' => $externalId,
                'description' => $description ?? 'Saque via Plataforma',
                'recipient' => [
                    'name' => $recipientData['name'] ?? '',
                    'document' => $recipientData['document'] ?? '',
                ]
            ];

            // Endpoint provável para CashOut
            $response = $this->makeRequest('POST', '/api/v1/pix/cashout', $payload);

            return [
                'status' => 'pending', 
                'transaction_id' => $response['transactionId'] ?? $response['id'] ?? null,
                'external_id' => $externalId,
                'raw_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error('ZoomPag: Erro ao realizar saque PIX', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function consultTransaction(string $transactionId): array
    {
        return $this->makeRequest('GET', "/api/v1/transactions/{$transactionId}");
    }
}
