<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQRCode;
use Illuminate\Support\Facades\Log;

class TwoFactorAuthController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Exibe a página de configuração do 2FA
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Se o usuário ainda não tem secret, gera uma nova
        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        try {
            // Cria o serviço de QR Code usando Bacon explicitamente
            $baconService = new \PragmaRX\Google2FAQRCode\QRCode\Bacon();
            $google2faQRCode = new Google2FAQRCode($baconService);
            
            $appName = \App\Helpers\LogoHelper::getSystemName();
            $qrCodeUrl = $google2faQRCode->getQRCodeInline(
                $appName,
                $user->email,
                $user->google2fa_secret,
                400
            );
            
            // Corrige o tamanho do SVG para ser responsivo de forma segura usando Regex
            $qrCodeUrl = preg_replace('/width="\d+"/', 'width="100%"', $qrCodeUrl);
            $qrCodeUrl = preg_replace('/height="\d+"/', 'height="100%"', $qrCodeUrl);
            $qrCodeUrl = preg_replace('/<svg/', '<svg class="w-full h-full"', $qrCodeUrl, 1);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar QR Code 2FA', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: gera URL manual do QR Code usando serviço online
            $appName = \App\Helpers\LogoHelper::getSystemName();
            $otpUrl = sprintf(
                'otpauth://totp/%s:%s?secret=%s&issuer=%s',
                urlencode($appName),
                urlencode($user->email),
                $user->google2fa_secret,
                urlencode($appName)
            );
            
            // Usa um serviço online como fallback
            $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($otpUrl);
            
            // Se for URL externa, envolve em tag IMG
            $qrCodeUrl = sprintf('<img src="%s" alt="QR Code 2FA" class="w-full h-full object-contain">', $qrCodeUrl);
        }

        return view('dashboard.settings.two-factor', [
            'user' => $user,
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $user->google2fa_secret,
        ]);
    }

    /**
     * Ativa o 2FA após verificação do código
     */
    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user->google2fa_secret) {
            return back()->withErrors(['code' => 'Secret não encontrado. Por favor, recarregue a página.']);
        }

        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Código inválido. Por favor, tente novamente.']);
        }

        $user->google2fa_enabled = true;
        $user->save();

        // Remove a verificação 2FA da sessão para forçar nova verificação
        session()->forget('2fa_verified');
        
        // Faz logout automático para que o usuário faça login novamente e teste o 2FA
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Autenticação de dois fatores ativada com sucesso! Faça login novamente e use o código do seu aplicativo de autenticação.');
    }

    /**
     * Desativa o 2FA
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user->google2fa_enabled) {
            return back()->withErrors(['code' => '2FA não está ativado.']);
        }

        if (!$user->google2fa_secret) {
            return back()->withErrors(['code' => 'Secret não encontrado.']);
        }

        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Código inválido. Por favor, tente novamente.']);
        }

        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->save();

        return redirect()->route('dashboard.settings.index', ['tab' => 'security'])
            ->with('success', 'Autenticação de dois fatores desativada com sucesso!');
    }

    /**
     * Exibe a página de verificação 2FA durante o login
     */
    public function showVerify(): View
    {
        $user = Auth::user();

        if (!$user->google2fa_enabled) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.verify-2fa');
    }

    /**
     * Verifica o código 2FA durante o login
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user->google2fa_secret) {
            return back()->withErrors(['code' => '2FA não configurado.']);
        }

        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Código inválido. Por favor, tente novamente.']);
        }

        // Marca que o 2FA foi verificado nesta sessão
        session(['2fa_verified' => true]);

        // Redireciona para o dashboard apropriado
        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard.index'));
    }
}
