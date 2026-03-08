<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        // Taxas do sistema (usando as novas taxas detalhadas)
        // CashIn PIX
        $cashinPixFixo = Setting::get('cashin_pix_fixo', Setting::get('cashin_fixo', '1.00'));
        $cashinPixPercentual = Setting::get('cashin_pix_percentual', Setting::get('cashin_percentual', '3.00'));
        
        // CashIn Cartão
        $cashinCardFixo = Setting::get('cashin_card_fixo', '1.00');
        $cashinCardPercentual = Setting::get('cashin_card_percentual', '4.00');
        
        // CashOut PIX
        $cashoutPixFixo = Setting::get('cashout_pix_fixo', Setting::get('cashout_fixo', '1.00'));
        $cashoutPixPercentual = Setting::get('cashout_pix_percentual', Setting::get('cashout_percentual', '2.00'));
        
        // CashOut Cripto
        $cashoutCryptoPercentual = Setting::get('cashout_crypto_percentual', '7.00');
        
        // Taxa mínima
        $cashoutPixMinima = Setting::get('cashout_pix_minima', '0.80');

        // SEMPRE usa as taxas do painel (não mais taxas personalizadas do usuário)
        // As taxas dos usuários estão sempre sincronizadas com as taxas do painel
        $finalCashinPixFixo = $cashinPixFixo;
        $finalCashinPixPercentual = $cashinPixPercentual;
        $finalCashoutPixFixo = $cashoutPixFixo;
        $finalCashoutPixPercentual = $cashoutPixPercentual;
        
        // Para exibição: sempre mostra que é taxa padrão do sistema
        $userCashinFixo = null;
        $userCashinPercentual = null;
        $userCashoutFixo = null;
        $userCashoutPercentual = null;

        // Estatísticas
        $totalTransactions = $user->transactions()->count();
        $totalVolume = $user->transactions()->where('status', 'completed')->sum('amount_gross');
        $totalWithdrawals = $user->withdrawals()->count();

        return view('dashboard.profile.index', compact(
            'user',
            'wallet',
            'cashinPixFixo',
            'cashinPixPercentual',
            'cashinCardFixo',
            'cashinCardPercentual',
            'cashoutPixFixo',
            'cashoutPixPercentual',
            'cashoutCryptoPercentual',
            'cashoutPixMinima',
            'totalTransactions',
            'totalVolume',
            'totalWithdrawals'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // Apenas permite editar email, não nome e CPF
        $user->update($request->only(['email']));

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'pin' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ]);

        $user = Auth::user();

        // Verifica senha atual
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        // Verifica PIN
        if (!\Illuminate\Support\Facades\Hash::check($request->pin, $user->pin)) {
            return back()->withErrors(['pin' => 'PIN incorreto.']);
        }

        // Atualiza senha
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', 'Senha alterada com sucesso!');
    }
}