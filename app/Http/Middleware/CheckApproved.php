<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckApproved
{
    /**
     * Handle an incoming request.
     * 
     * Bloqueia acesso ao sistema até que o usuário complete o KYC e seja aprovado.
     * Fluxo: Criar conta → Preencher endereço → Enviar documentos → Biometria → Aguardar aprovação
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Administradores e gerentes não precisam de verificação
        if ($user->is_admin || $user->is_manager) {
            return $next($request);
        }

        // Se o usuário está bloqueado, permite apenas acesso ao dashboard (que mostrará mensagem de bloqueio) e página de falar com gerente
        if ($user->is_blocked) {
            if (!$request->routeIs('dashboard.index') && !$request->routeIs('manager.contact')) {
                return redirect()->route('dashboard.index')
                    ->with('error', 'Você foi bloqueado. Fale com seu Gerente.');
            }
        }

        // Se o KYC foi reprovado, bloqueia acesso ao sistema
        if ($user->kyc_status === 'rejected') {
            if (!$request->routeIs('kyc.*') && !$request->routeIs('dashboard.manager-contact.*')) {
                return redirect()->route('kyc.index')
                    ->with('error', 'Seu cadastro foi reprovado. Entre em contato com o suporte para mais informações.');
            }
        }

        // Se não está aprovado, redireciona para KYC (exceto se já estiver na página de KYC ou rotas permitidas)
        if (!$user->is_approved || $user->kyc_status !== 'approved') {
            // Permite acesso apenas às rotas de KYC e algumas rotas específicas
            $allowedRoutes = [
                'kyc.*',
                'pin.*',
                'dashboard.settings.*',
                'dashboard.manager-contact.*',
                '2fa.verify*',
                '2fa.*',
            ];
            
            $isAllowed = false;
            foreach ($allowedRoutes as $route) {
                if ($request->routeIs($route)) {
                    $isAllowed = true;
                    break;
                }
            }
            
            if (!$isAllowed) {
                return redirect()->route('kyc.index')
                    ->with('info', 'Complete seu cadastro enviando os documentos para aprovação. Você precisa preencher o endereço, enviar os documentos e fazer a biometria facial antes de acessar o sistema.');
            }
        }

        // Se está aprovado no KYC mas não tem PIN configurado, redireciona para criar PIN
        if (($user->is_approved && $user->kyc_status === 'approved') && (!$user->pin_configured || empty($user->pin))) {
            // Permite acesso apenas à rota de criação de PIN
            if (!$request->routeIs('pin.*')) {
                return redirect()->route('pin.create')
                    ->with('info', 'Seu cadastro foi aprovado! Por favor, configure seu PIN de 6 dígitos para continuar.');
            }
        }

        // Se 2FA está ativado e não foi verificado nesta sessão, redireciona para verificação
        if ($user->google2fa_enabled && !session('2fa_verified') && !$request->routeIs('2fa.verify*') && !$request->routeIs('2fa.*')) {
            return redirect()->route('2fa.verify.show')
                ->with('info', 'Digite o código do seu aplicativo de autenticação para continuar.');
        }

        return $next($request);
    }
}
