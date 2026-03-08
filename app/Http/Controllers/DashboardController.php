<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Busca notificações não lidas para o sistema de Web Push
     */
    public function getUnreadNotifications(): JsonResponse
    {
        $user = Auth::user();
        $notifications = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->with('notification')
            ->latest()
            ->get()
            ->map(function($un) {
                return [
                    'id' => $un->id,
                    'title' => $un->notification->title,
                    'message' => $un->notification->message,
                    'type' => $un->notification->type,
                    'is_pushed' => $un->is_pushed,
                    'created_at_human' => $un->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $notifications->count(),
        ]);
    }

    /**
     * Marca uma notificação como enviada via Push
     */
    public function markNotificationPushed($id): JsonResponse
    {
        $un = UserNotification::where('user_id', Auth::id())->where('id', $id)->first();
        if ($un) {
            $un->update(['is_pushed' => true]);
        }
        return response()->json(['success' => true]);
    }

    /**
     * Exibe o dashboard principal
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Carrega wallet com eager loading para evitar query extra
        $wallet = $user->wallet;

        // Filtros
        $periodo = $request->get('periodo', 'hoje');
        $produto = $request->get('produto', 'todos');

        // Query base de transações
        $transactionsQuery = Transaction::where('user_id', $user->id);

        // Filtro por período
        if ($periodo === 'hoje') {
            $transactionsQuery->whereDate('created_at', today());
        } elseif ($periodo === 'semana') {
            $transactionsQuery->where('created_at', '>=', now()->startOfWeek());
        } elseif ($periodo === 'mes') {
            $transactionsQuery->whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year);
        }

        // Filtro por produto/tipo
        if ($produto !== 'todos') {
            $transactionsQuery->where('type', $produto);
        }

        // Cache para estatísticas pesadas (5 minutos) - Versão 3 para invalidar cache antigo
        $cacheKey = "dashboard.stats_v3.{$user->id}.{$periodo}.{$produto}";
        
        $stats = Cache::remember($cacheKey, 300, function () use ($user, $periodo, $produto) {
            // Recria queries dentro do closure para evitar problemas de serialização
            $transactionsQuery = Transaction::where('user_id', $user->id);
            
            // Filtro por período
            if ($periodo === 'hoje') {
                $transactionsQuery->whereDate('created_at', today());
            } elseif ($periodo === 'semana') {
                $transactionsQuery->where('created_at', '>=', now()->startOfWeek());
            } elseif ($periodo === 'mes') {
                $transactionsQuery->whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year);
            }
    
            // Filtro por produto/tipo
            if ($produto !== 'todos') {
                $transactionsQuery->where('type', $produto);
            }

            // Recebido Hoje (apenas PIX, exclui débitos administrativos e outros métodos)
            $receivedToday = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where('type', 'pix')
                ->where('gateway_provider', '!=', 'admin')
                ->whereDate('created_at', today())
                ->sum('amount_gross');

            // Faturamento Total (todas as vendas aprovadas, excluindo débitos administrativos)
            $totalBilling = $this->getBillingTransactionsQuery($user->id)
                ->sum('amount_gross');

            // Vendas realizadas (aprovadas/completadas) no período (excluindo débitos administrativos)
            $salesRealized = (clone $transactionsQuery)
                ->where('status', 'completed')
                ->where(function($query) {
                    $query->where('type', '!=', 'debit')
                        ->orWhere(function($q) {
                            $q->where('type', 'debit')
                              ->where('gateway_provider', '!=', 'admin');
                        });
                })
                ->sum('amount_gross');

            // Quantidade de vendas (excluindo débitos administrativos)
            $salesQuantity = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where(function($query) {
                    $query->where('type', '!=', 'debit')
                        ->orWhere(function($q) {
                            $q->where('type', 'debit')
                              ->where('gateway_provider', '!=', 'admin');
                        });
                })
                ->count();
            
            // Ticket médio
            $averageTicket = $salesQuantity > 0 ? ($totalBilling / $salesQuantity) : 0.00;
            
            // Saldo a Liberar
            $balanceToRelease = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where('type', 'credit') // Apenas cartão de crédito
                ->whereNull('released_at')
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('available_at')
                            ->where('available_at', '>', now());
                    })->orWhere(function($q) {
                        $q->whereNull('available_at');
                    });
                })
                ->sum('amount_net');
                
            // Média diária
            $firstTransaction = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where(function($query) {
                    $query->where('type', '!=', 'debit')
                        ->orWhere(function($q) {
                            $q->where('type', 'debit')
                              ->where('gateway_provider', '!=', 'admin');
                        });
                })
                ->orderBy('created_at', 'asc')
                ->first();
            
            $dailyAverage = 0.00;
            if ($firstTransaction) {
                $daysDiff = max(1, now()->diffInDays($firstTransaction->created_at) + 1);
                $dailyAverage = $totalBilling / $daysDiff;
            } elseif ($totalBilling > 0) {
                $dailyAverage = $totalBilling;
            }
            
            // Estatísticas por método
            $allTransactionsQuery = Transaction::where('user_id', $user->id);
            $pixStats = $this->getPaymentMethodStats($allTransactionsQuery, 'pix');
            $creditStats = $this->getPaymentMethodStats($allTransactionsQuery, 'credit');
            $boletoStats = $this->getPaymentMethodStats($allTransactionsQuery, 'boleto');
            
            // Estatísticas gerais
            $totalTransactions = Transaction::where('user_id', $user->id)->count();
            $completedTransactions = Transaction::where('user_id', $user->id)->where('status', 'completed')->count();
            $generalConversion = $totalTransactions > 0 ? (($completedTransactions / $totalTransactions) * 100) : 0;
            
            // Taxa de estorno
            $chargebackTransactions = Transaction::where('user_id', $user->id)
                ->whereIn('status', ['chargeback', 'mediation'])
                ->count();
            $chargebackRate = $totalTransactions > 0 ? (($chargebackTransactions / $totalTransactions) * 100) : 0.00;
            
            // Crescimento
            $lastMonthSales = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->where(function($query) {
                    $query->where('type', '!=', 'debit')
                        ->orWhere(function($q) {
                            $q->where('type', 'debit')
                              ->where('gateway_provider', '!=', 'admin');
                        });
                })
                ->sum('amount_gross');
            
            $thisMonthSales = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where(function($query) {
                    $query->where('type', '!=', 'debit')
                        ->orWhere(function($q) {
                            $q->where('type', 'debit')
                              ->where('gateway_provider', '!=', 'admin');
                        });
                })
                ->sum('amount_gross');
            
            $growthPercentage = $lastMonthSales > 0 
                ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100 
                : 0;
                
            // Clientes ativos
            $activeClients = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereNotNull('payer_email')
                ->distinct('payer_email')
                ->count('payer_email');
            
            // Dados do gráfico (agora dentro do cache)
            $chartData = $this->getBillingChartData();
                
            return compact(
                'receivedToday', 'totalBilling', 'salesRealized', 'salesQuantity', 
                'averageTicket', 'balanceToRelease', 'dailyAverage', 'pixStats', 
                'creditStats', 'boletoStats', 'generalConversion', 'chargebackRate',
                'growthPercentage', 'activeClients', 'chartData'
            );
        });
        
        // Extrai variáveis do cache
        extract($stats);

        // Saldo disponível (sempre em tempo real)
        $availableBalance = $wallet ? $wallet->balance : 0.00;
        $pendingBalance = $wallet ? $wallet->frozen_balance : 0.00;

        // Bloqueio Cautelar (sempre em tempo real)
        $cautionaryBlock = $wallet ? $wallet->frozen_balance : 0.00;

        // Taxa de extorno do usuário (sempre em tempo real)
        $userRefundFee = floatval($user->taxa_extorno ?? 0.00);

        // Últimas transações (sempre em tempo real, mas limitadas)
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Rating (mockado por enquanto, pode ser calculado de avaliações se existir)
        $rating = 4.8;

        // Últimas transações limitadas (reutiliza a query anterior se possível)
        $recentTransactionsLimited = $recentTransactions->take(5);

        return view('dashboard.index', compact(
            'availableBalance',
            'pendingBalance',
            'receivedToday',
            'cautionaryBlock',
            'totalBilling',
            'salesRealized',
            'salesQuantity',
            'averageTicket',
            'balanceToRelease',
            'dailyAverage',
            'pixStats',
            'creditStats',
            'boletoStats',
            'generalConversion',
            'chargebackRate',
            'userRefundFee',
            'recentTransactions',
            'recentTransactionsLimited',
            'chartData',
            'growthPercentage',
            'activeClients',
            'rating',
            'periodo',
            'produto'
        ));
    }

    /**
     * Retorna query base para transações de faturamento (excluindo débitos administrativos)
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getBillingTransactionsQuery(int $userId)
    {
        return Transaction::where('user_id', $userId)
            ->where('status', 'completed')
            ->where(function($query) {
                $query->where('type', '!=', 'debit')
                    ->orWhere(function($q) {
                        $q->where('type', 'debit')
                          ->where('gateway_provider', '!=', 'admin');
                    });
            });
    }

    /**
     * Calcula estatísticas por método de pagamento
     */
    private function getPaymentMethodStats($query, string $type): array
    {
        $methodQuery = (clone $query)->where('type', $type);
        $total = $methodQuery->count();
        $completed = (clone $methodQuery)->where('status', 'completed')->count();
        $conversionRate = $total > 0 ? (($completed / $total) * 100) : 0;
        $value = (clone $methodQuery)->where('status', 'completed')->sum('amount_gross');

        return [
            'conversion_rate' => $conversionRate,
            'approval_rate' => $conversionRate, // Mantido para compatibilidade
            'value' => $value,
            'total' => $total,
            'completed' => $completed,
        ];
    }

    /**
     * Gera dados para o gráfico de faturamento (entradas e saídas)
     * Otimizado: usa uma única query com GROUP BY ao invés de 20 queries separadas
     *
     * @return array
     */
    private function getBillingChartData(): array
    {
        $user = Auth::user();
        $dates = [];
        $entries = []; // Entradas (recebimentos)
        $exits = []; // Saídas (saques)

        // Calcula o range de datas (últimos 10 dias)
        $startDate = now()->subDays(9)->startOfDay();
        $endDate = now()->endOfDay();

        // Busca todas as entradas de uma vez usando GROUP BY (1 query ao invés de 10)
        $entriesData = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where(function($query) {
                $query->where('type', '!=', 'debit')
                    ->orWhere(function($q) {
                        $q->where('type', 'debit')
                          ->where('gateway_provider', '!=', 'admin');
                    });
            })
            ->selectRaw('DATE(created_at) as date, SUM(amount_gross) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Busca todas as saídas de uma vez usando GROUP BY (1 query ao invés de 10)
        $exitsData = Withdrawal::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Preenche os arrays para os últimos 10 dias
        for ($i = 9; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $dates[] = $date->format('d M');
            
            // Usa os dados agrupados ou 0 se não houver
            $entries[] = floatval($entriesData[$dateKey] ?? 0);
            $exits[] = floatval($exitsData[$dateKey] ?? 0);
        }

        return [
            'dates' => $dates,
            'entries' => $entries,
            'exits' => $exits,
        ];
    }
}


