<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Helpers\WebhookUrlHelper;

class HyperCashGateway implements PaymentGatewayInterface
{
    private string $baseUrl = 'https://api.hypercashbrasil.com.br';
    private ?string $apiToken = null;

    public function __construct(?string $apiToken = null)
    {
        $this->apiToken = $apiToken;
    }

    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        if (!$this->apiToken) {
            throw new \Exception('HyperCash: Token de API não configurado');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->{strtolower($method)}("{$this->baseUrl}{$endpoint}", $data);

        if (!$response->successful()) {
            Log::error("HyperCash: Erro na requisição {$endpoint}", ['body' => $response->body()]);
            throw new \Exception("HyperCash: Erro na requisição - " . $response->status());
        }

        return $response->json();
    }

    public function createPix(float $amount, array $payerData): array
    {
        $externalId = $payerData['external_id'] ?? Str::uuid()->toString();
        $postbackUrl = $payerData['postback_url'] ?? WebhookUrlHelper::generateUrl('api.webhooks.hypercash');
        
        $document = preg_replace('/\D/', '', $payerData['cpf'] ?? $payerData['document'] ?? '');
        if (empty($document) || !in_array(strlen($document), [11, 14])) {
            $document = '19100000000'; // CPF de teste VÁLIDO
        }

        $payload = [
            'amount' => $amount,
            'external_id' => $externalId,
            'payer' => [
                'name' => $payerData['name'] ?? 'Cliente',
                'document' => $document,
                'email' => $payerData['email'] ?? 'cliente@email.com',
            ],
            'postbackUrl' => $postbackUrl,
        ];

        $response = $this->makeRequest('POST', '/pix/qrcode', $payload);
        
        $qrCode = $response['qrcode'] ?? $response['qr_code'] ?? '';
        
        return [
            'status' => 'pending',
            'qr_code' => $qrCode,
            'transaction_id' => $response['id'] ?? $externalId,
            'raw_response' => $response
        ];
    }

    public function createCreditCard(float $amount, array $cardData, array $payerData): array
    {
        $externalId = $payerData['external_id'] ?? Str::uuid()->toString();
        $postbackUrl = $payerData['postback_url'] ?? WebhookUrlHelper::generateUrl('api.webhooks.hypercash');

        $payload = [
            'amount' => $amount,
            'external_id' => $externalId,
            'payment_method' => 'credit_card',
            'card' => [
                'number' => $cardData['number'],
                'holder_name' => $cardData['holder_name'],
                'expiration_month' => $cardData['expiration_month'],
                'expiration_year' => $cardData['expiration_year'],
                'cvv' => $cardData['cvv'],
            ],
            'payer' => [
                'name' => $payerData['name'] ?? '',
                'document' => $payerData['cpf'] ?? '',
                'email' => $payerData['email'] ?? '',
            ],
            'postbackUrl' => $postbackUrl,
            'installments' => $cardData['installments'] ?? 1,
        ];

        $response = $this->makeRequest('POST', '/transactions', $payload);

        return [
            'status' => $response['status'] ?? 'pending',
            'transaction_id' => $response['id'] ?? $externalId,
            'raw_response' => $response
        ];
    }

    public function createBoleto(float $amount, array $payerData): array
    {
        throw new \Exception('HyperCash: Boleto não implementado.');
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
            $externalId = $externalId ?? Str::uuid()->toString();
            
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

            // Endpoint para pagamento (cash out)
            $response = $this->makeRequest('POST', '/pix/payment', $payload);

            return [
                'status' => 'pending', 
                'transaction_id' => $response['transactionId'] ?? $response['id'] ?? null,
                'external_id' => $externalId,
                'raw_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error('HyperCash: Erro ao realizar saque PIX', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function consultTransaction(string $transactionId): array
    {
        return $this->makeRequest('GET', "/transactions/{$transactionId}");
    }
}
