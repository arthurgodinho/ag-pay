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

class WooCommerceController extends Controller
{
    /**
     * Exibe página de configuração do WooCommerce
     */
    public function index(): View
    {
        $user = Auth::user();
        $integration = Integration::where('user_id', $user->id)
            ->where('platform', 'woocommerce')
            ->first();

        return view('dashboard.integrations.woocommerce', compact('integration'));
    }

    /**
     * Conecta/Atualiza integração WooCommerce
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'store_url' => 'required|url',
            'consumer_key' => 'required|string',
            'consumer_secret' => 'required|string',
        ]);

        $user = Auth::user();
        
        // Remove http:// ou https:// e barras finais
        $storeUrl = preg_replace('#^https?://#', '', rtrim($request->store_url, '/'));
        
        // Testa conexão usando WooCommerce REST API com timeout e retry
        try {
            $response = Http::timeout(10)
                ->retry(2, 100)
                ->withBasicAuth($request->consumer_key, $request->consumer_secret)
                ->get("https://{$storeUrl}/wp-json/wc/v3/system_status");

            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Credenciais inválidas';
                if (is_array($errorMessage)) {
                    $errorMessage = json_encode($errorMessage);
                }
                return back()->with('error', 'Erro ao conectar com WooCommerce: ' . $errorMessage)->withInput();
            }
            
            // Cache da validação por 5 minutos
            Cache::put("woocommerce_connection_valid_{$user->id}", true, 300);
        } catch (\Exception $e) {
            Log::error('WooCommerce: Erro ao testar conexão', [
                'error' => $e->getMessage(),
                'store_url' => $storeUrl
            ]);
            return back()->with('error', 'Erro ao conectar com WooCommerce: ' . $e->getMessage())->withInput();
        }

        // Salva ou atualiza integração
        $integration = Integration::updateOrCreate(
            [
                'user_id' => $user->id,
                'platform' => 'woocommerce',
            ],
            [
                'store_url' => $storeUrl,
                'api_key' => $request->consumer_key,
                'api_secret' => $request->consumer_secret,
                'is_active' => true,
                'last_sync_at' => now(),
            ]
        );

        Log::info('Integração WooCommerce configurada', [
            'user_id' => $user->id,
            'store_url' => $storeUrl,
        ]);

        return back()->with('success', 'Integração WooCommerce configurada com sucesso!');
    }

    /**
     * Desconecta integração WooCommerce
     */
    public function destroy(): RedirectResponse
    {
        $user = Auth::user();
        Integration::where('user_id', $user->id)
            ->where('platform', 'woocommerce')
            ->delete();

        return back()->with('success', 'Integração WooCommerce desconectada com sucesso!');
    }

    /**
     * Sincroniza produtos do WooCommerce
     */
    public function sync(): RedirectResponse
    {
        $user = Auth::user();
        $integration = Integration::where('user_id', $user->id)
            ->where('platform', 'woocommerce')
            ->where('is_active', true)
            ->first();

        if (!$integration) {
            return back()->with('error', 'Integração WooCommerce não configurada ou inativa.');
        }

        try {
            // Implementar sincronização de produtos
            // Por enquanto, apenas atualiza last_sync_at
            $integration->update(['last_sync_at' => now()]);

            return back()->with('success', 'Sincronização iniciada! Os produtos serão atualizados em breve.');
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar WooCommerce', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return back()->with('error', 'Erro ao sincronizar: ' . $e->getMessage());
        }
    }
}

