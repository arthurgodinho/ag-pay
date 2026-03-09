<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagarmeGateway implements PaymentGatewayInterface
{
    private string $baseUrl = 'https://api.pagar.me/core/v5';
    private ?string $secretKey = null;

    public function __construct(?string $secretKey = null, ?string $ignored = null)
    {
        // Pagar.me usa apenas o Secret Key na V5 (passado como username no Basic Auth)
        $this->secretKey = $secretKey;
    }

    /**
     * Faz uma requisição autenticada para a Pagar.me
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        if (!$this->secretKey) {
            throw new \Exception('Pagar.me: Chave secreta não configurada.');
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->{strtolower($method)}("{$this->baseUrl}{$endpoint}", $data);

        if (!$response->successful()) {
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? $response->body();
            
            Log::error("Pagar.me: Erro na requisição {$method} {$endpoint}", [
                'status' => $response->status(),
                'response' => $errorData,
                'payload' => $data
            ]);

            throw new \Exception("Pagar.me: {$errorMessage}");
        }

        return $response->json();
    }

    /**
     * Cria um pagamento PIX
     */
    public function createPix(float $amount, array $payerData): array
    {
        try {
            $externalId = $payerData['external_id'] ?? \Illuminate\Support\Str::uuid()->toString();
            
            // Pagar.me usa valores em centavos (inteiro)
            $amountInCents = (int) round($amount * 100);

            $payload = [
                'items' => [
                    [
                        'amount' => $amountInCents,
                        'description' => $payerData['description'] ?? 'Pagamento PIX',
                        'quantity' => 1,
                        'code' => $externalId
                    ]
                ],
                'customer' => [
                    'name' => $payerData['name'] ?? 'Cliente',
                    'email' => $payerData['email'] ?? 'cliente@email.com',
                    'type' => 'individual',
                    'document' => preg_replace('/\D/', '', $payerData['cpf'] ?? $payerData['document'] ?? ''),
                    'phones' => [
                        'mobile_phone' => [
                            'country_code' => '55',
                            'area_code' => substr(preg_replace('/\D/', '', $payerData['phone'] ?? '11999999999'), 0, 2),
                            'number' => substr(preg_replace('/\D/', '', $payerData['phone'] ?? '11999999999'), 2),
                        ]
                    ]
                ],
                'payments' => [
                    [
                        'payment_method' => 'pix',
                        'pix' => [
                            'expires_in' => 300, // 5 minutos
                        ]
                    ]
                ],
                'code' => $externalId
            ];

            $response = $this->makeRequest('POST', '/orders', $payload);

            $charge = $response['charges'][0] ?? null;
            $pixData = $charge['last_transaction'] ?? null;

            return [
                'status' => 'pending',
                'qr_code' => $pixData['qr_code'] ?? '',
                'external_id' => $externalId,
                'transaction_id' => $charge['id'] ?? null,
                'expires_at' => $pixData['expires_at'] ?? now()->addMinutes(5)->toIso8601String(),
                'raw_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error('Pagar.me: Erro ao criar PIX', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Cria um pagamento com cartão de crédito
     */
    public function createCreditCard(float $amount, array $cardData, array $payerData): array
    {
        try {
            $externalId = $payerData['external_id'] ?? \Illuminate\Support\Str::uuid()->toString();
            $amountInCents = (int) round($amount * 100);

            // Formata expiração
            $expiry = explode('/', $cardData['expiry'] ?? $cardData['expiration'] ?? '');
            $expMonth = trim($expiry[0] ?? '');
            $expYear = trim($expiry[1] ?? '');
            
            // Se o ano for 2 dígitos, tenta converter para 4 (Pagar.me aceita 4 dígitos)
            if (strlen($expYear) === 2) {
                $expYear = '20' . $expYear;
            }

            $payload = [
                'items' => [
                    [
                        'amount' => $amountInCents,
                        'description' => $payerData['description'] ?? 'Pagamento Cartão',
                        'quantity' => 1,
                        'code' => $externalId
                    ]
                ],
                'customer' => [
                    'name' => $payerData['name'] ?? $cardData['holder_name'] ?? 'Cliente',
                    'email' => $payerData['email'] ?? 'cliente@email.com',
                    'type' => 'individual',
                    'document' => preg_replace('/\D/', '', $payerData['cpf'] ?? $payerData['document'] ?? ''),
                    'phones' => [
                        'mobile_phone' => [
                            'country_code' => '55',
                            'area_code' => substr(preg_replace('/\D/', '', $payerData['phone'] ?? '11999999999'), 0, 2),
                            'number' => substr(preg_replace('/\D/', '', $payerData['phone'] ?? '11999999999'), 2),
                        ]
                    ]
                ],
                'payments' => [
                    [
                        'payment_method' => 'credit_card',
                        'credit_card' => [
                            'card' => [
                                'number' => preg_replace('/\D/', '', $cardData['number'] ?? ''),
                                'holder_name' => $cardData['holder_name'] ?? ($payerData['name'] ?? ''),
                                'exp_month' => (int) $expMonth,
                                'exp_year' => (int) $expYear,
                                'cvv' => $cardData['cvv'] ?? '',
                            ],
                            'installments' => (int) ($cardData['installments'] ?? 1),
                            'statement_descriptor' => substr(config('app.name', 'AGPAY'), 0, 13)
                        ]
                    ]
                ],
                'code' => $externalId
            ];

            $response = $this->makeRequest('POST', '/orders', $payload);

            $charge = $response['charges'][0] ?? null;
            $status = 'pending';
            
            if (isset($charge['status'])) {
                $statusMap = [
                    'paid' => 'completed',
                    'pending' => 'pending',
                    'failed' => 'failed',
                    'overpaid' => 'completed',
                    'underpaid' => 'pending',
                    'processing' => 'processing',
                ];
                $status = $statusMap[strtolower($charge['status'])] ?? 'pending';
            }

            return [
                'status' => $status,
                'external_id' => $externalId,
                'transaction_id' => $charge['id'] ?? null,
                'raw_response' => $response,
            ];
        } catch (\Exception $e) {
            Log::error('Pagar.me: Erro ao criar transação de cartão', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Consulta o status de uma transação
     */
    public function consultTransaction(string $transactionId): array
    {
        try {
            // Pode ser ID do pedido ou da cobrança
            if (strpos($transactionId, 'or_') === 0) {
                return $this->makeRequest('GET', "/orders/{$transactionId}");
            } else {
                return $this->makeRequest('GET', "/charges/{$transactionId}");
            }
        } catch (\Exception $e) {
            Log::error('Pagar.me: Erro ao consultar transação', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Pagar.me V5 suporta Boleto, mas implementaremos se necessário.
     */
    public function createBoleto(float $amount, array $payerData): array
    {
        throw new \Exception('Pagar.me: Método de Boleto ainda não implementado neste sistema.');
    }
}
