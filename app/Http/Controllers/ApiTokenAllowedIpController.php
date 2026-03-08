<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\ApiTokenAllowedIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ApiTokenAllowedIpController extends Controller
{
    /**
     * Adiciona um IP permitido
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'api_token_id' => 'required|exists:api_tokens,id',
            'ip_address' => 'required|ip',
        ]);

        $user = Auth::user();
        
        // Verifica se o token pertence ao usuário
        $apiToken = ApiToken::where('id', $request->api_token_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Verifica se já existe
        $exists = ApiTokenAllowedIp::where('api_token_id', $apiToken->id)
            ->where('ip_address', $request->ip_address)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Este IP já está cadastrado.');
        }

        ApiTokenAllowedIp::create([
            'api_token_id' => $apiToken->id,
            'ip_address' => $request->ip_address,
        ]);

        return back()->with('success', 'IP adicionado com sucesso!');
    }

    /**
     * Remove um IP permitido
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = Auth::user();
        
        $allowedIp = ApiTokenAllowedIp::with('apiToken')
            ->whereHas('apiToken', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);

        $allowedIp->delete();

        return back()->with('success', 'IP removido com sucesso!');
    }
}









