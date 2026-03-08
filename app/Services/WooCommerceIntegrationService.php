<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Transaction;
use App\Services\Gateways\GatewayFactory;
use App\Models\SystemGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WooCommerceIntegrationService
{
    /**
     * Processa um pagamento quando um pedido é criado no WooCommerce
     */
    public function processOrderPayment($orderId, $integration)
    {
        try {
            // Busca informações do pedido no WooCommerce
            $order = $this->getWooCommerceOrder($orderId, $integration);
            
            if (!$order) {
                Log::error('WooCommerce: Pedido não encontrado', ['order_id' => $orderId]);
                return false;
            }
            
            $amount = floatval($order['total'] ?? 0);
            $billing = $order['billing'] ?? [];
            
            if ($amount <= 0) {
                Log::error('WooCommerce: Valor do pedido inválido', ['order_id' => $orderId, 'amount' => $amount]);
                return false;
            }
            
            // Determina método de pagamento
            $paymentMethod = $order['payment_method'] ?? 'pix';
            $isPix = str_contains(strtolower($paymentMethod), 'pix');
            
            // Prepara dados do pagador
            $payerData = [
                'name' => trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? '')),
                'email' => $billing['email'] ?? '',
                'cpf' => $this->extractCpfFromMeta($order) ?? '',
                'external_id' => 'woocommerce_' . $orderId,
                'description' => 'Pedido WooCommerce #' . $orderId,
            ];
            
            DB::beginTransaction();
            
            try {
                if ($isPix) {
                    // Processa PIX
                    $result = $this->processPixPayment($amount, $payerData, $integration->user_id);
                } else {
                    // Processa Cartão (requer dados do cartão do pedido)
                    $result = $this->processCardPayment($amount, $payerData, $order, $integration->user_id);
                }
                
                if ($result['success']) {
                    // Atualiza pedido no WooCommerce com informações do pagamento
                    $this->updateWooCommerceOrder($orderId, $integration, $result);
                    
                    DB::commit();
                    return true;
                } else {
                    DB::rollBack();
                    Log::error('WooCommerce: Erro ao processar pagamento', [
                        'order_id' => $orderId,
                        'error' => $result['message'] ?? 'Erro desconhecido'
                    ]);
                    return false;
                }
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('WooCommerce: Erro ao processar pedido', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Busca informações do pedido no WooCommerce
     */
    private function getWooCommerceOrder($orderId, $integration)
    {
        try {
            $response = Http::withBasicAuth($integration->api_key, $integration->api_secret)
                ->get("https://{$integration->store_url}/wp-json/wc/v3/orders/{$orderId}");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('WooCommerce: Erro ao buscar pedido', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Processa pagamento PIX
     */
    private function processPixPayment($amount, $payerData, $userId)
    {
        try {
            $gatewayConfig = SystemGatewayConfig::getActiveForPix();
            
            if (!$gatewayConfig) {
                return ['success' => false, 'message' => 'Nenhum gateway PIX ativo'];
            }
            
            $gateway = GatewayFactory::make($gatewayConfig->provider_name);
            $response = $gateway->createPix($amount, $payerData);
            
            if (!isset($response['qr_code']) && !isset($response['qrCode'])) {
                return ['success' => false, 'message' => 'QR Code não retornado pelo gateway'];
            }
            
            $qrCode = $response['qr_code'] ?? $response['qrCode'] ?? '';
            
            // Cria transação
            $transaction = Transaction::create([
                'user_id' => $userId,
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                'type' => 'pix',
                'amount_gross' => $amount,
                'amount_net' => $amount, // Será calculado após confirmação
                'status' => 'pending',
                'gateway_provider' => $gatewayConfig->provider_name,
                'external_id' => $payerData['external_id'],
            ]);
            
            return [
                'success' => true,
                'transaction_id' => $transaction->id,
                'qr_code' => $qrCode,
                'transaction_uuid' => $transaction->uuid,
            ];
            
        } catch (\Exception $e) {
            Log::error('WooCommerce: Erro ao processar PIX', [
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Processa pagamento com cartão
     */
    private function processCardPayment($amount, $payerData, $order, $userId)
    {
        try {
            $gatewayConfig = SystemGatewayConfig::getActiveForCard();
            
            if (!$gatewayConfig) {
                return ['success' => false, 'message' => 'Nenhum gateway de cartão ativo'];
            }
            
            // Extrai dados do cartão dos meta_data do pedido
            $metaData = $order['meta_data'] ?? [];
            $cardData = [];
            
            foreach ($metaData as $meta) {
                $key = strtolower($meta['key'] ?? '');
                if (str_contains($key, 'card_number')) {
                    $cardData['number'] = preg_replace('/\D/', '', $meta['value'] ?? '');
                } elseif (str_contains($key, 'card_expiry')) {
                    $expiry = $meta['value'] ?? '';
                    if (preg_match('/(\d{2})\/(\d{4})/', $expiry, $matches)) {
                        $cardData['expiry_month'] = $matches[1];
                        $cardData['expiry_year'] = $matches[2];
                    }
                } elseif (str_contains($key, 'card_cvv') || str_contains($key, 'card_cvc')) {
                    $cardData['cvv'] = $meta['value'] ?? '';
                } elseif (str_contains($key, 'card_holder')) {
                    $cardData['holder_name'] = $meta['value'] ?? $payerData['name'];
                }
            }
            
            // Se não houver dados do cartão, verifica se há token salvo
            if (empty($cardData['number'])) {
                $customerId = $order['customer_id'] ?? null;
                if ($customerId) {
                    $savedCard = $this->getSavedCardForCustomer($customerId, $userId);
                    if ($savedCard) {
                        $cardData = $savedCard;
                    }
                }
            }
            
            if (empty($cardData['number'])) {
                return ['success' => false, 'message' => 'Dados do cartão não disponíveis. Configure o gateway de pagamento no WooCommerce.'];
            }
            
            $gateway = GatewayFactory::make(
                $gatewayConfig->provider_name,
                $gatewayConfig->client_id,
                $gatewayConfig->client_secret
            );
            
            // Prepara dados do cartão no formato esperado
            $formattedCardData = [
                'number' => preg_replace('/\D/', '', $cardData['number'] ?? ''),
                'cvv' => $cardData['cvv'] ?? '',
                'expiry_month' => str_pad($cardData['expiry_month'] ?? '', 2, '0', STR_PAD_LEFT),
                'expiry_year' => $cardData['expiry_year'] ?? '',
                'holder_name' => $cardData['holder_name'] ?? $payerData['name'],
                'installments' => $order['meta_data']['_installments'] ?? 1,
            ];
            
            // Valida dados do cartão
            if (empty($formattedCardData['number']) || empty($formattedCardData['cvv'])) {
                return ['success' => false, 'message' => 'Dados do cartão incompletos'];
            }
            
            $response = $gateway->createCreditCard($amount, $formattedCardData, $payerData);
            
            // Verifica se o gateway suporta cartão
            if (isset($response['error']) || (isset($response['message']) && str_contains(strtolower($response['message']), 'não suporta'))) {
                return ['success' => false, 'message' => $response['message'] ?? 'Gateway não suporta pagamentos com cartão'];
            }
            
            if (!isset($response['transaction_id']) && !isset($response['id']) && !isset($response['external_id'])) {
                return ['success' => false, 'message' => $response['message'] ?? 'Erro ao processar pagamento com cartão'];
            }
            
            $transactionId = $response['transaction_id'] ?? $response['id'] ?? $response['external_id'] ?? '';
            
            // Cria transação
            $transaction = Transaction::create([
                'user_id' => $userId,
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                'type' => 'card',
                'amount_gross' => $amount,
                'amount_net' => $amount, // Será calculado após confirmação
                'status' => $response['status'] ?? 'pending',
                'gateway_provider' => $gatewayConfig->provider_name,
                'external_id' => $payerData['external_id'],
                'gateway_transaction_id' => $transactionId,
            ]);
            
            return [
                'success' => true,
                'transaction_id' => $transaction->id,
                'transaction_uuid' => $transaction->uuid,
                'status' => $transaction->status,
            ];
            
        } catch (\Exception $e) {
            Log::error('WooCommerce: Erro ao processar cartão', [
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Busca cartão salvo para um cliente
     */
    private function getSavedCardForCustomer($customerId, $userId)
    {
        // Implementar busca de cartão salvo se houver sistema de tokens
        // Por enquanto retorna null
        return null;
    }
    
    /**
     * Atualiza pedido no WooCommerce com informações do pagamento
     */
    private function updateWooCommerceOrder($orderId, $integration, $paymentResult)
    {
        try {
            $note = "Pagamento processado via Gateway. ";
            if (isset($paymentResult['qr_code'])) {
                $note .= "QR Code PIX gerado. ";
            }
            $note .= "Transaction ID: " . ($paymentResult['transaction_uuid'] ?? 'N/A');
            
            Http::withBasicAuth($integration->api_key, $integration->api_secret)
                ->put("https://{$integration->store_url}/wp-json/wc/v3/orders/{$orderId}", [
                    'customer_note' => $note,
                    'meta_data' => [
                        [
                            'key' => '_gateway_transaction_id',
                            'value' => $paymentResult['transaction_uuid'] ?? ''
                        ],
                        [
                            'key' => '_gateway_qr_code',
                            'value' => $paymentResult['qr_code'] ?? ''
                        ]
                    ]
                ]);
            
        } catch (\Exception $e) {
            Log::error('WooCommerce: Erro ao atualizar pedido', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Extrai CPF de meta_data do pedido
     */
    private function extractCpfFromMeta($order)
    {
        $metaData = $order['meta_data'] ?? [];
        
        foreach ($metaData as $meta) {
            $key = strtolower($meta['key'] ?? '');
            if (str_contains($key, 'cpf') || str_contains($key, 'document')) {
                return preg_replace('/\D/', '', $meta['value'] ?? '');
            }
        }
        
        return null;
    }
}


