<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Services\ShopifyIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class ShopifyWebhookController extends Controller
{
    protected $integrationService;
    
    public function __construct(ShopifyIntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }
    
    /**
     * Processa webhook de pedido criado na Shopify
     */
    public function handleOrderCreate(Request $request)
    {
        try {
            // Valida assinatura do webhook
            $hmac = $request->header('X-Shopify-Hmac-Sha256');
            $data = $request->getContent();
            $shopDomain = $request->header('X-Shopify-Shop-Domain');
            
            // Busca integração primeiro para obter o webhook_secret específico
            $integration = null;
            if ($shopDomain) {
                $integration = Integration::where('platform', 'shopify')
                    ->where('store_url', $shopDomain)
                    ->where('is_active', true)
                    ->first();
            }
            
            // Valida assinatura usando o secret da integração ou o global
            $webhookSecret = $integration->webhook_secret ?? config('services.shopify.webhook_secret', '');
            if ($webhookSecret && !$this->verifyWebhook($data, $hmac, $webhookSecret)) {
                Log::warning('Shopify: Webhook com assinatura inválida', [
                    'shop' => $shopDomain,
                    'hmac_received' => substr($hmac, 0, 10) . '...'
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }
            
            $order = $request->json('order');
            $orderId = $order['id'] ?? null;
            
            if (!$orderId || !$shopDomain) {
                return response()->json(['error' => 'Invalid request'], 400);
            }
            
            // Se não encontrou antes, busca novamente
            if (!$integration) {
                $integration = Integration::where('platform', 'shopify')
                    ->where('store_url', $shopDomain)
                    ->where('is_active', true)
                    ->first();
            }
            
            if (!$integration) {
                Log::warning('Shopify: Integração não encontrada', ['shop' => $shopDomain]);
                return response()->json(['error' => 'Integration not found'], 404);
            }
            
            // Verifica se o pedido já foi processado (evita duplicação)
            $existingTransaction = \App\Models\Transaction::where('external_id', 'shopify_' . $orderId)
                ->where('user_id', $integration->user_id)
                ->first();
            
            if ($existingTransaction) {
                Log::info('Shopify: Pedido já processado', [
                    'order_id' => $orderId,
                    'transaction_id' => $existingTransaction->id
                ]);
                return response()->json(['success' => true, 'message' => 'Order already processed'], 200);
            }
            
            // Processa pagamento (pode ser em background se queue estiver configurado)
            try {
                $result = $this->integrationService->processOrderPayment($orderId, $integration);
                
                if ($result) {
                    return response()->json(['success' => true, 'message' => 'Payment processed successfully'], 200);
                } else {
                    return response()->json(['error' => 'Payment processing failed'], 500);
                }
            } catch (\Exception $e) {
                Log::error('Shopify: Erro ao processar pagamento', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage()
                ]);
                return response()->json(['error' => 'Payment processing error'], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Shopify: Erro ao processar webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'shop' => $request->header('X-Shopify-Shop-Domain')
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Verifica assinatura do webhook
     */
    private function verifyWebhook($data, $hmac, $secret)
    {
        $calculated = base64_encode(hash_hmac('sha256', $data, $secret, true));
        return hash_equals($hmac, $calculated);
    }
}


