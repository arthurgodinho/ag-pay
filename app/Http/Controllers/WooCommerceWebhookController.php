<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Services\WooCommerceIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WooCommerceWebhookController extends Controller
{
    protected $integrationService;
    
    public function __construct(WooCommerceIntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }
    
    /**
     * Processa webhook de pedido criado no WooCommerce
     */
    public function handleOrderCreate(Request $request)
    {
        try {
            // Valida autenticação básica
            $authHeader = $request->header('Authorization');
            if (!$authHeader) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $order = $request->json();
            $orderId = $order['id'] ?? null;
            $storeUrl = $request->header('X-Store-Url');
            
            if (!$orderId || !$storeUrl) {
                return response()->json(['error' => 'Invalid request'], 400);
            }
            
            // Extrai credenciais do header
            $credentials = $this->extractCredentials($authHeader);
            if (!$credentials) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
            
            // Busca integração
            $integration = Integration::where('platform', 'woocommerce')
                ->where('store_url', $storeUrl)
                ->where('api_key', $credentials['key'])
                ->where('api_secret', $credentials['secret'])
                ->where('is_active', true)
                ->first();
            
            if (!$integration) {
                Log::warning('WooCommerce: Integração não encontrada', [
                    'store' => $storeUrl,
                    'key_prefix' => substr($credentials['key'], 0, 5) . '...'
                ]);
                return response()->json(['error' => 'Integration not found'], 404);
            }
            
            // Verifica se o pedido já foi processado (evita duplicação)
            $existingTransaction = \App\Models\Transaction::where('external_id', 'woocommerce_' . $orderId)
                ->where('user_id', $integration->user_id)
                ->first();
            
            if ($existingTransaction) {
                Log::info('WooCommerce: Pedido já processado', [
                    'order_id' => $orderId,
                    'transaction_id' => $existingTransaction->id
                ]);
                return response()->json(['success' => true, 'message' => 'Order already processed'], 200);
            }
            
            // Processa pagamento
            try {
                $result = $this->integrationService->processOrderPayment($orderId, $integration);
                
                if ($result) {
                    return response()->json(['success' => true, 'message' => 'Payment processed successfully'], 200);
                } else {
                    return response()->json(['error' => 'Payment processing failed'], 500);
                }
            } catch (\Exception $e) {
                Log::error('WooCommerce: Erro ao processar pagamento', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage()
                ]);
                return response()->json(['error' => 'Payment processing error'], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('WooCommerce: Erro ao processar webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'store' => $request->header('X-Store-Url')
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Extrai credenciais do header Authorization
     */
    private function extractCredentials($authHeader)
    {
        if (preg_match('/Basic\s+(.+)/', $authHeader, $matches)) {
            $decoded = base64_decode($matches[1]);
            list($key, $secret) = explode(':', $decoded, 2);
            return ['key' => $key, 'secret' => $secret];
        }
        return null;
    }
}


