<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    /**
     * Exibe a página de afiliados
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();

        // Gera código de afiliado se não existir
        if (!$user->affiliate_code) {
            $user->affiliate_code = strtoupper(substr(md5($user->id . $user->email), 0, 8));
            $user->save();
        }

        // Link de indicação
        $affiliateLink = config('app.url') . '/register?ref=' . $user->affiliate_code;

        // Usuários que se cadastraram com o código deste usuário
        $referrals = User::where('manager_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Comissões pendentes (mockado - em produção, calcular baseado em transações)
        $pendingCommissions = 0.00; // TODO: Calcular comissões reais

        return view('dashboard.affiliates.index', compact(
            'affiliateLink',
            'referrals',
            'pendingCommissions'
        ));
    }
}
