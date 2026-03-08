<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    /**
     * Exibe a página de API
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Busca todos os tokens do usuário com IPs permitidos
        $tokens = ApiToken::with('allowedIps')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.api.index', compact('tokens'));
    }

    /**
     * Cria um novo token de API
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
            'withdrawal_mode' => 'nullable|in:automatic,manual',
            'webhook_url' => 'nullable|url|max:255',
        ]);

        $user = Auth::user();

        // Gera Client ID
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $user->name));
        $timestamp = time() * 1000;
        $random = rand(1000, 9999);
        $clientId = $username . '_' . $timestamp . $random;

        // Gera um token único
        $prefix = 'nxp_';
        $randomToken = Str::random(64);
        $fullToken = $prefix . $randomToken;

        // Cria o token de API
        $apiToken = ApiToken::create([
            'user_id' => $user->id,
            'client_id' => $clientId,
            'project' => $request->project,
            'name' => $request->name,
            'token' => $fullToken,
            'expires_at' => $request->expires_at ? $request->expires_at : null,
            'is_active' => true,
            'withdrawal_mode' => $request->withdrawal_mode ?? 'manual',
            'webhook_url' => $request->webhook_url,
        ]);

        // Retorna apenas uma vez o token completo (para exibição)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Credenciais criadas com sucesso!',
                'client_id' => $clientId,
                'token' => $fullToken,
                'apiToken' => $apiToken,
            ]);
        }

        // Redireciona para a página de API (mantém na mesma página)
        return redirect()->route('dashboard.api.index')
            ->with('success', 'Credencial criada com sucesso! Ela já está disponível na lista abaixo.');
    }

    /**
     * Atualiza um token de API
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        
        $apiToken = ApiToken::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'project' => 'nullable|string|max:255',
            'withdrawal_mode' => 'nullable|in:automatic,manual',
            'webhook_url' => 'nullable|url|max:255',
        ]);

        $apiToken->update([
            'name' => $request->name,
            'project' => $request->project,
            'withdrawal_mode' => $request->withdrawal_mode ?? 'manual',
            'webhook_url' => $request->webhook_url,
        ]);

        return back()->with('success', 'Credencial atualizada com sucesso!');
    }

    /**
     * Revoga (desativa) um token de API
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function revoke(int $id): RedirectResponse
    {
        $user = Auth::user();
        
        $apiToken = ApiToken::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $apiToken->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Token revogado com sucesso!');
    }

    /**
     * Reativa um token de API
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function reactivate(int $id): RedirectResponse
    {
        $user = Auth::user();
        
        $apiToken = ApiToken::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($apiToken->isExpired()) {
            return back()->with('error', 'Não é possível reativar um token expirado.');
        }

        $apiToken->update([
            'is_active' => true,
        ]);

        return back()->with('success', 'Token reativado com sucesso!');
    }

    /**
     * Deleta um token de API
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = Auth::user();
        
        $apiToken = ApiToken::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $apiToken->delete();

        return back()->with('success', 'Token deletado com sucesso!');
    }

    /**
     * Atualiza a data de último uso do token
     *
     * @param string $token
     * @return void
     */
    public static function updateLastUsed(string $token): void
    {
        $apiToken = ApiToken::findByToken($token);
        if ($apiToken) {
            $apiToken->update([
                'last_used_at' => now(),
            ]);
        }
    }
}
