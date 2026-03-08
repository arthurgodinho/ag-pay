<?php

namespace App\Http\Controllers;

use App\Models\Award;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AwardController extends Controller
{
    public function index()
    {
        $awards = Award::orderBy('goal_amount', 'asc')->get();
        $wallet = Auth::user()->wallet;
        
        // Calcula o Saldo Acumulado baseado no Faturamento Total (mesma lógica do Dashboard)
        $currentBalance = \App\Models\Transaction::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->where(function($query) {
                $query->where('type', '!=', 'debit')
                    ->orWhere(function($q) {
                        $q->where('type', 'debit')
                          ->where('gateway_provider', '!=', 'admin');
                    });
            })
            ->sum('amount_gross');

        return view('dashboard.awards.index', compact('awards', 'currentBalance'));
    }
}
