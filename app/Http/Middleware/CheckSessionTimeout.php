<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity');
            $timeout = config('session.lifetime'); // Usa configuração do .env (minutos)
            
            // Se não há registro de última atividade, cria um
            if (!$lastActivity) {
                session(['last_activity' => now()->timestamp]);
            } else {
                // Calcula o tempo decorrido em minutos
                $elapsed = (now()->timestamp - $lastActivity) / 60;
                
                // Se passou mais de 5 minutos, faz logout
                if ($elapsed > $timeout) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    // Se não é uma requisição AJAX, redireciona para login
                    if (!$request->expectsJson()) {
                        return redirect()->route('login')
                            ->with('info', 'Sua sessão expirou por inatividade. Por favor, faça login novamente.');
                    }
                    
                    return response()->json(['message' => 'Sessão expirada'], 401);
                }
            }
            
            // Atualiza a última atividade a cada requisição
            session(['last_activity' => now()->timestamp]);
        }
        
        return $next($request);
    }
}
