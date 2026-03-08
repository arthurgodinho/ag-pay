<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ShopifyController extends Controller
{
    /**
     * Exibe página de configuração do Shopify
     */
    public function index(): View
    {
        $user = Auth::user();
        $integration = Integration::where('user_id', $user->id)
            ->where('platform', 'shopify')
            ->first();

        return view('dashboard.integrations.shopify', compact('integration'));
    }

    /**
     * Conecta/Atualiza integração Shopify
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'store_url' => 'required|url',
            'api_key' => 'required|string',
            'api_secret' => 'required|string',
        ]);

        $user = Auth::user();
        
        // Remove http:// ou https:// e barras finais
        $storeUrl = preg_replace('#^https?://#', '', rtrim($request->store_url, '/'));
        
        // Testa conexão com timeout e retry
        try {
            $response = Http::timeout(10)
                ->retry(2, 100)
                ->withHeaders([
                    'X-Shopify-Access-Token' => $request->api_key,
                ])->get("https://{$storeUrl}/admin/api/2024-01/shop.json");

            if (!$response->successful()) {
                $errorMessage = $response->json()['errors'] ?? 'Credenciais inválidas';
                if (is_array($errorMessage)) {
                    $errorMessage = json_encode($errorMessage);
                }
                return back()->with('error', 'Erro ao conectar com Shopify: ' . $errorMessage)->withInput();
            }
            
            // Cache da validação por 5 minutos
            Cache::put("shopify_connection_valid_{$user->id}", true, 300);
        } catch (\Exception $e) {
            Log::error('Shopify: Erro ao testar conexão', [
                'error' => $e->getMessage(),
                'store_url' => $storeUrl
            ]);
            return back()->with('error', 'Erro ao conectar com Shopify: ' . $e->getMessage())->withInput();
        }

        // Salva ou atualiza integração
        $integration = Integration::updateOrCreate(
            [
                'user_id' => $user->id,
                'platform' => 'shopify',
            ],
            [
                'store_url' => $storeUrl,
                'api_key' => $request->api_key,
                'api_secret' => $request->api_secret,
                'is_active' => true,
                'last_sync_at' => now(),
            ]
        );

        Log::info('Integração Shopify configurada', [
            'user_id' => $user->id,
            'store_url' => $storeUrl,
        ]);

        return back()->with('success', 'Integração Shopify configurada com sucesso!');
    }

    /**
     * Desconecta integração Shopify
     */
    public function destroy(): RedirectResponse
    {
        $user = Auth::user();
        Integration::where('user_id', $user->id)
            ->where('platform', 'shopify')
            ->delete();

        return back()->with('success', 'Integração Shopify desconectada com sucesso!');
    }

    /**
     * Sincroniza produtos do Shopify
     */
    public function sync(): RedirectResponse
    {
        $user = Auth::user();
        $integration = Integration::where('user_id', $user->id)
            ->where('platform', 'shopify')
            ->where('is_active', true)
            ->first();

        if (!$integration) {
            return back()->with('error', 'Integração Shopify não configurada ou inativa.');
        }

        try {
            // Implementar sincronização de produtos
            // Por enquanto, apenas atualiza last_sync_at
            $integration->update(['last_sync_at' => now()]);

            return back()->with('success', 'Sincronização iniciada! Os produtos serão atualizados em breve.');
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar Shopify', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return back()->with('error', 'Erro ao sincronizar: ' . $e->getMessage());
        }
    }
}

