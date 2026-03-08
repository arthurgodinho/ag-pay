<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportsController extends Controller
{
    /**
     * Página inicial de relatórios (redireciona para transações)
     */
    public function index(): View
    {
        return $this->transactions();
    }

    /**
     * Exibe relatórios de vendas
     */
    public function sales(): View
    {
        $user = Auth::user();
        
        // Base query for completed sales
        $query = Transaction::where('user_id', $user->id)
            ->where('status', 'completed');

        // Statistics
        $totalSales = (clone $query)->sum('amount_gross');
        $salesCount = (clone $query)->count();
        $averageTicket = $salesCount > 0 ? $totalSales / $salesCount : 0;
        
        $salesToday = (clone $query)->whereDate('created_at', now())->sum('amount_gross');
        $salesMonth = (clone $query)->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('amount_gross');

        // Recent transactions with pagination
        $transactions = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.reports.sales', compact(
            'transactions', 
            'totalSales', 
            'salesCount', 
            'averageTicket', 
            'salesToday', 
            'salesMonth'
        ));
    }

    /**
     * Exibe relatórios de transações
     */
    public function transactions(): View
    {
        $user = Auth::user();
        
        $transactions = Transaction::where('user_id', $user->id)
            ->with('user') // Carrega o relacionamento para evitar N+1 queries
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.reports.transactions', compact('transactions'));
    }

    /**
     * Exibe relatórios financeiros
     */
    public function financial()
    {
        $user = auth()->user();

        // Calcular totais
        $totalDeposited = Transaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->sum('amount_gross');

        $totalWithdrawn = Transaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'approved')
            ->sum('amount_gross');
            
        $totalCheckoutSales = Transaction::where('user_id', $user->id)
            ->where('type', 'sale')
            ->where('status', 'approved')
            ->whereNotNull('product_id') // Identifica vendas de checkout
            ->sum('amount_gross');

        // Buscar últimas transações de cada tipo
        $recentDeposits = Transaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->latest()
            ->take(5)
            ->get();

        $recentWithdrawals = Transaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->latest()
            ->take(5)
            ->get();
            
        $recentCheckoutSales = Transaction::where('user_id', $user->id)
            ->where('type', 'sale')
            ->whereNotNull('product_id')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.reports.financial', compact(
            'totalDeposited', 
            'totalWithdrawn', 
            'totalCheckoutSales',
            'recentDeposits', 
            'recentWithdrawals',
            'recentCheckoutSales'
        ));
    }
}
