<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Aceita Client ID + Token ou apenas Token (compatibilidade)
        $clientId = $request->header('X-Client-ID') ?? $request->input('client_id');
        $token = $request->bearerToken() ?? $request->header('X-API-Token') ?? $request->input('api_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de API não fornecido',
            ], 401);
        }

        // Se Client ID foi fornecido, busca por Client ID + Token
        if ($clientId) {
            $apiToken = ApiToken::where('client_id', $clientId)
                ->where('token', $token)
                ->first();
        } else {
            // Fallback: busca apenas por token (compatibilidade)
            $apiToken = ApiToken::findByToken($token);
        }

        if (!$apiToken || !$apiToken->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais de API inválidas ou expiradas',
            ], 401);
        }

        // Verifica IP permitido (se houver IPs cadastrados)
        $allowedIps = $apiToken->allowedIps;
        if ($allowedIps->count() > 0) {
            $clientIp = $request->ip();
            $isAllowed = $allowedIps->contains('ip_address', $clientIp);
            
            if (!$isAllowed) {
                return response()->json([
                    'success' => false,
                    'message' => 'IP não autorizado para esta credencial',
                ], 403);
            }
        }

        // Atualiza último uso
        $apiToken->update(['last_used_at' => now()]);

        // Adiciona o usuário à requisição
        $request->merge(['api_user' => $apiToken->user]);

        return $next($request);
    }
}
