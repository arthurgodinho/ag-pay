<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Setting;
use App\Events\UserRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login
     *
     * @return View|RedirectResponse
     */
    public function showLogin(): View|RedirectResponse
    {
        // Se já está logado, redireciona para o dashboard apropriado
        if (Auth::check()) {
            $user = Auth::user();
            
            // Administradores vão para o dashboard admin
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            
            // Gerentes vão para o dashboard
            if ($user->is_manager) {
                return redirect()->route('dashboard.index');
            }
            
            // Usuários normais precisam estar aprovados para acessar
            if (!$user->is_approved || $user->kyc_status !== 'approved') {
                return redirect()->route('kyc.index')
                    ->with('info', 'Complete seu cadastro enviando os documentos para aprovação.');
            }
            
            // Se está aprovado mas não tem PIN, redireciona para criar PIN
            if (!$user->pin_configured || empty($user->pin)) {
                return redirect()->route('pin.create')
                    ->with('info', 'Seu cadastro foi aprovado! Por favor, configure seu PIN de 6 dígitos para continuar.');
            }
            
            return redirect()->route('dashboard.index');
        }
        
        return view('auth.login');
    }

    /**
     * Exibe o formulário de registro
     *
     * @return View|RedirectResponse
     */
    public function showRegister(): View|RedirectResponse
    {
        // Se já está logado, redireciona para o dashboard apropriado
        if (Auth::check()) {
            $user = Auth::user();
            
            // Administradores vão para o dashboard admin
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            
            // Gerentes vão para o dashboard
            if ($user->is_manager) {
                return redirect()->route('dashboard.index');
            }
            
            // Usuários normais precisam estar aprovados para acessar
            if (!$user->is_approved || $user->kyc_status !== 'approved') {
                return redirect()->route('kyc.index')
                    ->with('info', 'Complete seu cadastro enviando os documentos para aprovação.');
            }
            
            // Se está aprovado mas não tem PIN, redireciona para criar PIN
            if (!$user->pin_configured || empty($user->pin)) {
                return redirect()->route('pin.create')
                    ->with('info', 'Seu cadastro foi aprovado! Por favor, configure seu PIN de 6 dígitos para continuar.');
            }
            
            return redirect()->route('dashboard.index');
        }
        
        return view('auth.register');
    }

    /**
     * Processa o registro
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'cpf_cnpj' => 'required|string|max:18',
            'birth_date' => 'required|date|before:today',
            'phone' => 'required|string|max:20',
            'monthly_billing' => 'nullable|numeric|min:0',
        ]);

        // Verifica se há código de afiliado
        $managerId = null;
        if ($request->has('ref') && $request->ref) {
            $manager = User::where('affiliate_code', $request->ref)->first();
            if ($manager) {
                $managerId = $manager->id;
            }
        }

        // Obtém as taxas do painel para o novo usuário
        $cashinPixPercentual = Setting::get('cashin_pix_percentual', Setting::get('cashin_percentual', '3.00'));
        $cashinPixFixo = Setting::get('cashin_pix_fixo', Setting::get('cashin_fixo', '1.00'));
        $cashoutPixPercentual = Setting::get('cashout_pix_percentual', Setting::get('cashout_percentual', '2.00'));
        $cashoutPixFixo = Setting::get('cashout_pix_fixo', Setting::get('cashout_fixo', '1.00'));

        // Cria o usuário com as taxas do painel
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'cpf_cnpj' => $validated['cpf_cnpj'],
            'birth_date' => $validated['birth_date'],
            'phone' => $validated['phone'],
            'monthly_billing' => $validated['monthly_billing'] ?? 0.00,
            'manager_id' => $managerId,
            'is_approved' => false, // Precisa ser aprovado
            'kyc_status' => null, // Status inicial (não enviado ainda)
            'documents_sent' => false, // Documentos ainda não foram enviados
            // Taxas do painel (sempre vinculadas)
            'taxa_entrada' => $cashinPixPercentual,
            'taxa_entrada_fixo' => $cashinPixFixo,
            'taxa_saida' => $cashoutPixPercentual,
            'taxa_saida_fixo' => $cashoutPixFixo,
            'withdrawal_mode' => 'auto',
        ]);

        // Cria a carteira vazia inicial
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0.00,
            'frozen_balance' => 0.00,
        ]);

        // Dispara evento de usuário registrado (para envio de emails)
        event(new UserRegistered($user));

        // Autentica o usuário automaticamente após o registro
        Auth::login($user);
        
        // Define a última atividade
        session(['last_activity' => now()->timestamp]);

        // Administradores vão direto para o dashboard admin
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Gerentes também não precisam de KYC
        if ($user->is_manager) {
            return redirect()->route('dashboard.index');
        }

        // Usuários novos devem completar o KYC antes de acessar o sistema
        // Redireciona para KYC se não estiver aprovado
        if (!$user->is_approved || $user->kyc_status !== 'approved') {
            return redirect()->route('kyc.index')
                ->with('info', 'Complete seu cadastro enviando os documentos para aprovação. Você precisa preencher o endereço, enviar os documentos e fazer a biometria facial.');
        }

        // Se 2FA está ativado, redireciona para verificação
        if ($user->google2fa_enabled && !session('2fa_verified')) {
            return redirect()->route('2fa.verify.show')
                ->with('info', 'Digite o código do seu aplicativo de autenticação para continuar.');
        }

        // Verifica se o usuário precisa criar o PIN após aprovação do KYC
        if (!$user->pin_configured || empty($user->pin)) {
            return redirect()->route('pin.create')
                ->with('info', 'Seu cadastro foi aprovado! Por favor, configure seu PIN de 6 dígitos para continuar.');
        }

        return redirect()->route('dashboard.index');
    }

    /**
     * Processa o login
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Define a última atividade
            session(['last_activity' => now()->timestamp]);

            // Verifica se está bloqueado
            if ($user->is_blocked) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Sua conta foi bloqueada. Entre em contato com o suporte.',
                ])->onlyInput('email');
            }

            // Administradores vão direto para o dashboard admin
            if ($user->is_admin) {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Gerentes também não precisam de KYC
            if ($user->is_manager) {
                return redirect()->intended(route('dashboard.index'));
            }

            // Usuários novos devem completar o KYC antes de acessar o sistema
            // Redireciona para KYC se não estiver aprovado
            if (!$user->is_approved || $user->kyc_status !== 'approved') {
                return redirect()->route('kyc.index')
                    ->with('info', 'Complete seu cadastro enviando os documentos para aprovação. Você precisa preencher o endereço, enviar os documentos e fazer a biometria facial.');
            }

            // Se 2FA está ativado, redireciona para verificação
            if ($user->google2fa_enabled && !session('2fa_verified')) {
                return redirect()->route('2fa.verify.show')
                    ->with('info', 'Digite o código do seu aplicativo de autenticação para continuar.');
            }

            // Verifica se o usuário precisa criar o PIN após aprovação do KYC
            if (!$user->pin_configured || empty($user->pin)) {
                return redirect()->route('pin.create')
                    ->with('info', 'Seu cadastro foi aprovado! Por favor, configure seu PIN de 6 dígitos para continuar.');
            }

            return redirect()->intended(route('dashboard.index'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    /**
     * Processa o logout
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}


