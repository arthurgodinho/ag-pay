<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Transaction;
use App\Services\Gateways\GatewayFactory;
use App\Models\SystemGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ShopifyIntegrationService
{
    /**
     * Processa um pagamento quando um pedido é criado na Shopify
     */
    public function processOrderPayment($orderId, $integration)
    {
        try {
            // Busca informações do pedido na Shopify
            $order = $this->getShopifyOrder($orderId, $integration);
            
            if (!$order) {
                Log::error('Shopify: Pedido não encontrado', ['order_id' => $orderId]);
                return false;
            }
            
            $amount = floatval($order['total_price'] ?? 0);
            $customer = $order['customer'] ?? [];
            $billingAddress = $order['billing_address'] ?? [];
            
            if ($amount <= 0) {
                Log::error('Shopify: Valor do pedido inválido', ['order_id' => $orderId, 'amount' => $amount]);
                return false;
            }
            
            // Determina método de pagamento
            $paymentMethod = $order['payment_gateway_names'][0] ?? 'pix';
            $isPix = str_contains(strtolower($paymentMethod), 'pix');
            
            // Prepara dados do pagador
            $payerData = [
                'name' => $customer['first_name'] . ' ' . ($customer['last_name'] ?? ''),
                'email' => $customer['email'] ?? $billingAddress['email'] ?? '',
                'cpf' => $this->extractCpfFromNote($order) ?? '',
                'external_id' => 'shopify_' . $orderId,
                'description' => 'Pedido Shopify #' . $orderId,
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
                    // Atualiza pedido na Shopify com informações do pagamento
                    $this->updateShopifyOrder($orderId, $integration, $result);
                    
                    DB::commit();
                    return true;
                } else {
                    DB::rollBack();
                    Log::error('Shopify: Erro ao processar pagamento', [
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
            Log::error('Shopify: Erro ao processar pedido', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Busca informações do pedido na Shopify
     */
    private function getShopifyOrder($orderId, $integration)
    {
        try {
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => $integration->api_key,
            ])->get("https://{$integration->store_url}/admin/api/2024-01/orders/{$orderId}.json");
            
            if ($response->successful()) {
                return $response->json()['order'] ?? null;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Shopify: Erro ao buscar pedido', [
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
            Log::error('Shopify: Erro ao processar PIX', [
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
            
            // Extrai dados do cartão do pedido
            $paymentDetails = $order['payment_details'] ?? [];
            $cardData = [
                'number' => $paymentDetails['number'] ?? '',
                'expiry_month' => $paymentDetails['expiry_month'] ?? '',
                'expiry_year' => $paymentDetails['expiry_year'] ?? '',
                'cvv' => $paymentDetails['cvv'] ?? '',
                'holder_name' => $payerData['name'] ?? '',
            ];
            
            // Se não houver dados do cartão no pedido, tenta buscar de transações anteriores
            if (empty($cardData['number'])) {
                // Verifica se há token salvo para este cliente
                $customerId = $order['customer']['id'] ?? null;
                if ($customerId) {
                    $savedCard = $this->getSavedCardForCustomer($customerId, $userId);
                    if ($savedCard) {
                        $cardData = $savedCard;
                    }
                }
            }
            
            if (empty($cardData['number'])) {
                return ['success' => false, 'message' => 'Dados do cartão não disponíveis. Configure o gateway de pagamento na Shopify.'];
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
                'installments' => $order['payment_details']['installments'] ?? 1,
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
            Log::error('Shopify: Erro ao processar cartão', [
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
     * Atualiza pedido na Shopify com informações do pagamento
     */
    private function updateShopifyOrder($orderId, $integration, $paymentResult)
    {
        try {
            $note = "Pagamento processado via Gateway. ";
            if (isset($paymentResult['qr_code'])) {
                $note .= "QR Code PIX gerado. ";
            }
            $note .= "Transaction ID: " . ($paymentResult['transaction_uuid'] ?? 'N/A');
            
            Http::withHeaders([
                'X-Shopify-Access-Token' => $integration->api_key,
            ])->put("https://{$integration->store_url}/admin/api/2024-01/orders/{$orderId}.json", [
                'order' => [
                    'note' => $note,
                    'tags' => 'gateway-payment-pending',
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Shopify: Erro ao atualizar pedido', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Extrai CPF de notas do pedido
     */
    private function extractCpfFromNote($order)
    {
        $note = $order['note'] ?? '';
        $noteAttributes = $order['note_attributes'] ?? [];
        
        // Tenta encontrar CPF em note_attributes
        foreach ($noteAttributes as $attr) {
            if (str_contains(strtolower($attr['name'] ?? ''), 'cpf')) {
                return preg_replace('/\D/', '', $attr['value'] ?? '');
            }
        }
        
        // Tenta extrair CPF da nota
        if (preg_match('/\d{11}/', $note, $matches)) {
            return $matches[0];
        }
        
        return null;
    }
}


