<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chargeback;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminChargebackController extends Controller
{
    /**
     * Lista todos os chargebacks/MED
     * Inclui transações com status 'mediation' ou 'chargeback' diretamente
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Busca transações com status mediation ou chargeback
        $transactionsQuery = Transaction::with('user')
            ->whereIn('status', ['mediation', 'chargeback']);

        // Busca chargebacks da tabela chargebacks
        $chargebacksQuery = Chargeback::with(['user', 'transaction']);

        // Filtro por status
        $statusFilter = $request->has('status') && $request->status !== '' ? $request->status : 'pending';
        
        if ($statusFilter === 'pending') {
            // Para pendentes, mostra transações com status mediation/chargeback E chargebacks pendentes
            $chargebacksQuery->where('status', 'pending');
        } else {
            $chargebacksQuery->where('status', $statusFilter);
        }

        $chargebacks = $chargebacksQuery->orderBy('created_at', 'desc')->get();
        $transactions = $transactionsQuery->orderBy('created_at', 'desc')->get();

        // Combina ambos em uma lista unificada
        $allItems = collect();
        
        // Adiciona transações diretas (que não têm chargeback registrado)
        foreach ($transactions as $transaction) {
            // Verifica se já existe um chargeback para esta transação
            $hasChargeback = Chargeback::where('transaction_id', $transaction->id)->exists();
            if (!$hasChargeback) {
                $allItems->push([
                    'id' => 't_' . $transaction->id,
                    'type' => 'transaction',
                    'transaction' => $transaction,
                    'user' => $transaction->user,
                    'amount' => $transaction->amount_net ?? $transaction->amount_gross,
                    'status' => $transaction->status === 'mediation' ? 'pending' : ($transaction->status === 'chargeback' ? 'pending' : 'pending'),
                    'created_at' => $transaction->created_at,
                ]);
            }
        }
        
        // Adiciona chargebacks da tabela
        foreach ($chargebacks as $chargeback) {
            $allItems->push([
                'id' => 'c_' . $chargeback->id,
                'type' => 'chargeback',
                'chargeback' => $chargeback,
                'transaction' => $chargeback->transaction,
                'user' => $chargeback->user,
                'amount' => $chargeback->amount,
                'status' => $chargeback->status,
                'created_at' => $chargeback->created_at,
            ]);
        }

        // Ordena por data e pagina
        $allItems = $allItems->sortByDesc('created_at')->values();
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $items = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        // Cria paginator manual
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.chargebacks.index', [
            'items' => $paginator,
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Aprova a transação (remove do chargeback, volta para completed)
     *
     * @param int $transactionId
     * @return RedirectResponse
     */
    public function approveTransaction(int $transactionId): RedirectResponse
    {
        try {
            DB::transaction(function () use ($transactionId) {
                $transaction = Transaction::with('user')->findOrFail($transactionId);
                
                if (!in_array($transaction->status, ['mediation', 'chargeback'])) {
                    throw new \Exception('Esta transação não está em mediação ou chargeback.');
                }

                // Atualiza status para completed
                Transaction::withoutEvents(function () use ($transaction) {
                    $transaction->update(['status' => 'completed']);
                });

                // Remove qualquer chargeback pendente relacionado
                Chargeback::where('transaction_id', $transaction->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'cancelled',
                        'processed_at' => now(),
                    ]);

                // Libera frozen_balance se houver
                $wallet = $transaction->user->wallet;
                if ($wallet && $wallet->frozen_balance > 0) {
                    $amountNet = $transaction->amount_net ?? $transaction->amount_gross;
                    if ($wallet->frozen_balance >= $amountNet) {
                        $wallet->decrement('frozen_balance', $amountNet);
                        $wallet->increment('balance', $amountNet);
                    }
                }

                Log::info('Transaction approved from chargeback/mediation', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                ]);
            });

            return back()->with('success', 'Transação aprovada! Status alterado para concluído.');
        } catch (\Exception $e) {
            Log::error('Error approving transaction', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erro ao aprovar transação: ' . $e->getMessage());
        }
    }

    /**
     * Aprova o MED (muda status para MED EFETUADO e debita o saldo do usuário)
     *
     * @param string $id (formato: "t_123" para transação ou "c_123" para chargeback)
     * @return RedirectResponse
     */
    public function approveMed(string $id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $chargeback = null;
                $transaction = null;
                $user = null;
                
                // Pode ser um chargeback ou uma transação direta
                if (str_starts_with($id, 't_')) {
                    // É uma transação direta
                    $transactionId = (int) str_replace('t_', '', $id);
                    $transaction = Transaction::with('user')->findOrFail($transactionId);
                    $user = $transaction->user;
                    
                    // Cria ou atualiza chargeback
                    $chargeback = Chargeback::firstOrCreate(
                        ['transaction_id' => $transaction->id],
                        [
                            'user_id' => $transaction->user_id,
                            'amount' => $transaction->amount_net ?? $transaction->amount_gross,
                            'status' => 'pending',
                            'processed_at' => null,
                        ]
                    );
                    
                    $chargeback->update([
                        'status' => 'approved',
                        'processed_at' => now(),
                    ]);
                    
                    // Mantém status da transação como chargeback (não muda para completed)
                    // O status "chargeback" indica que o MED foi efetuado
                } else {
                    // É um chargeback da tabela
                    $chargebackId = (int) str_replace('c_', '', $id);
                    $chargeback = Chargeback::with(['user', 'transaction'])->findOrFail($chargebackId);
                    $transaction = $chargeback->transaction;
                    $user = $chargeback->user;
                    
                    $chargeback->update([
                        'status' => 'approved',
                        'processed_at' => now(),
                    ]);
                }

                // DEBITA O SALDO DO USUÁRIO (MED foi aprovado, então desconta)
                if ($user && $transaction) {
                    $wallet = $user->wallet;
                    if (!$wallet) {
                        $wallet = Wallet::create([
                            'user_id' => $user->id,
                            'balance' => 0.00,
                            'frozen_balance' => 0.00,
                            'negative_balance' => 0.00,
                        ]);
                    }

                    $amountNet = $transaction->amount_net ?? $chargeback->amount;
                    
                    // Remove do frozen_balance (bloqueio cautelar) - o saldo simplesmente some
                    if ($wallet->frozen_balance >= $amountNet) {
                        $wallet->decrement('frozen_balance', $amountNet);
                    } else {
                        // Remove o que tem de frozen_balance
                        $amountToRemove = $wallet->frozen_balance;
                        if ($amountToRemove > 0) {
                            $wallet->decrement('frozen_balance', $amountToRemove);
                        }
                        // O restante já estava como negativo ou será perdido
                    }

                    Log::info('MED aprovado - Saldo debitado do usuário', [
                        'chargeback_id' => $chargeback->id ?? null,
                        'transaction_id' => $transaction->id ?? null,
                        'user_id' => $user->id,
                        'amount_net' => $amountNet,
                        'frozen_balance_before' => $wallet->frozen_balance + $amountNet,
                        'frozen_balance_after' => $wallet->fresh()->frozen_balance,
                    ]);
                }

                Log::info('MED approved', [
                    'chargeback_id' => $chargeback->id ?? null,
                    'transaction_id' => $chargeback->transaction_id ?? null,
                ]);
            });

            return back()->with('success', 'MED aprovado! O saldo foi debitado do usuário.');
        } catch (\Exception $e) {
            Log::error('Error approving MED', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erro ao aprovar MED: ' . $e->getMessage());
        }
    }

    /**
     * Aprova o MED e debita o saldo do usuário (método antigo - mantido para compatibilidade)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function approve(int $id): RedirectResponse
    {
        $chargeback = Chargeback::with(['user', 'transaction'])->findOrFail($id);

        if ($chargeback->status !== 'pending') {
            return back()->with('error', 'Este MED já foi processado.');
        }

        try {
            DB::transaction(function () use ($chargeback) {
                $user = $chargeback->user;
                $wallet = $user->wallet;
                $transaction = $chargeback->transaction;

                if (!$wallet) {
                    $wallet = Wallet::create([
                        'user_id' => $user->id,
                        'balance' => 0.00,
                        'frozen_balance' => 0.00,
                        'negative_balance' => 0.00,
                    ]);
                }

                // Valor líquido que estava bloqueado (amount_net da transação)
                $amountNet = $transaction->amount_net ?? $chargeback->amount;
                
                // Recarrega o wallet para ter os valores atualizados
                $wallet->refresh();
                
                $frozenBalanceBefore = $wallet->frozen_balance;
                $balanceBefore = $wallet->balance;
                
                // Libera o bloqueio cautelar: move de frozen_balance de volta para balance
                if ($wallet->frozen_balance >= $amountNet) {
                    $wallet->decrement('frozen_balance', $amountNet);
                    $wallet->increment('balance', $amountNet);
                    
                    Log::info('Chargeback approved - Movido de frozen_balance para balance', [
                        'chargeback_id' => $chargeback->id,
                        'transaction_id' => $transaction->id,
                        'amount_net' => $amountNet,
                        'frozen_balance_before' => $frozenBalanceBefore,
                        'frozen_balance_after' => $wallet->fresh()->frozen_balance,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $wallet->fresh()->balance,
                    ]);
                } else {
                    // Se não tem frozen_balance suficiente, libera o que tem
                    $amountToRelease = $wallet->frozen_balance;
                    if ($amountToRelease > 0) {
                        $wallet->decrement('frozen_balance', $amountToRelease);
                        $wallet->increment('balance', $amountToRelease);
                    }
                    
                    // Se havia saldo negativo relacionado, também remove
                    if ($transaction->status === 'chargeback' || $transaction->status === 'mediation') {
                        $remainingAmount = $amountNet - $amountToRelease;
                        if ($remainingAmount > 0 && $wallet->negative_balance >= $remainingAmount) {
                            $wallet->decrement('negative_balance', $remainingAmount);
                        }
                    }
                    
                    Log::info('Chargeback approved - Movido parcialmente de frozen_balance para balance', [
                        'chargeback_id' => $chargeback->id,
                        'transaction_id' => $transaction->id,
                        'amount_net' => $amountNet,
                        'amount_released' => $amountToRelease,
                        'frozen_balance_before' => $frozenBalanceBefore,
                        'frozen_balance_after' => $wallet->fresh()->frozen_balance,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $wallet->fresh()->balance,
                    ]);
                }
                
                // Recarrega novamente para garantir valores atualizados
                $wallet->refresh();

                // Atualiza status do chargeback PRIMEIRO
                // O Observer verifica se há chargeback processado antes de fazer qualquer coisa
                $chargeback->update([
                    'status' => 'approved',
                    'processed_at' => now(),
                    'balance_debited' => false, // Não foi debitado, foi liberado
                ]);
                
                // Força refresh do chargeback para garantir que está atualizado
                $chargeback->refresh();
                
                // Pequeno delay para garantir que o banco processou
                usleep(100000); // 0.1 segundo

                // Atualiza status da transação de volta para completed
                // Usa withoutEvents para evitar que o Observer processe
                // O Observer não vai processar porque o chargeback já está aprovado
                if ($transaction) {
                    Transaction::withoutEvents(function () use ($transaction) {
                        $transaction->update(['status' => 'completed']);
                    });
                }

                // Desbloqueia saques se não houver outros MEDs pendentes
                $otherPendingChargebacks = Chargeback::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->where('id', '!=', $chargeback->id)
                    ->exists();

                if (!$otherPendingChargebacks) {
                    $user->update(['bloquear_saque' => false]);
                }

                Log::info('Chargeback approved - Bloqueio cautelar liberado', [
                    'chargeback_id' => $chargeback->id,
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id ?? null,
                    'amount_net' => $amountNet,
                    'frozen_balance_before' => $wallet->frozen_balance + $amountNet,
                    'frozen_balance_after' => $wallet->frozen_balance,
                    'balance_after' => $wallet->balance,
                ]);
            });

            return back()->with('success', 'MED aprovado e bloqueio cautelar liberado! O saldo foi retornado para o usuário.');
        } catch (\Exception $e) {
            Log::error('Error approving chargeback', [
                'chargeback_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Erro ao aprovar MED: ' . $e->getMessage());
        }
    }

    /**
     * Cancela o MED e reverte as ações
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function cancel(int $id): RedirectResponse
    {
        $chargeback = Chargeback::with(['user', 'transaction'])->findOrFail($id);

        if ($chargeback->status !== 'pending') {
            return back()->with('error', 'Este MED já foi processado.');
        }

        try {
            DB::transaction(function () use ($chargeback) {
                $user = $chargeback->user;
                $wallet = $user->wallet;
                $transaction = $chargeback->transaction;

                // Atualiza status do chargeback PRIMEIRO (antes de qualquer outra operação)
                // Isso evita que o Observer processe quando mudarmos o status da transação
                // O Observer verifica se há chargeback processado antes de fazer qualquer coisa
                $chargeback->update([
                    'status' => 'cancelled',
                    'processed_at' => now(),
                ]);
                
                // Força refresh do chargeback para garantir que está atualizado
                $chargeback->refresh();
                
                // Pequeno delay para garantir que o banco processou
                usleep(100000); // 0.1 segundo

                if ($wallet && $transaction) {
                    // Valor líquido que estava bloqueado
                    $amountNet = $transaction->amount_net ?? $chargeback->amount;
                    
                    // Recarrega o wallet para ter os valores atualizados
                    $wallet->refresh();
                    
                    $frozenBalanceBefore = $wallet->frozen_balance;
                    
                    // Remove o bloqueio cautelar E RETORNA para o saldo disponível
                    // Se estamos cancelando o MED, o dinheiro deve voltar para o usuário
                    if ($wallet->frozen_balance >= $amountNet) {
                        $wallet->decrement('frozen_balance', $amountNet);
                        $wallet->increment('balance', $amountNet);
                        
                        Log::info('Chargeback cancelled - Movido de frozen_balance para balance', [
                            'chargeback_id' => $chargeback->id,
                            'transaction_id' => $transaction->id,
                            'amount_net' => $amountNet,
                            'frozen_balance_before' => $frozenBalanceBefore,
                            'frozen_balance_after' => $wallet->fresh()->frozen_balance,
                            'balance_after' => $wallet->fresh()->balance,
                        ]);
                    } else {
                        // Remove o que tem de frozen_balance e devolve para balance
                        $amountToRelease = $wallet->frozen_balance;
                        if ($amountToRelease > 0) {
                            $wallet->decrement('frozen_balance', $amountToRelease);
                            $wallet->increment('balance', $amountToRelease);
                        }
                        
                        // Remove também do saldo negativo se houver
                        $remainingAmount = $amountNet - $amountToRelease;
                        if ($remainingAmount > 0 && $wallet->negative_balance >= $remainingAmount) {
                            $wallet->decrement('negative_balance', $remainingAmount);
                        }
                        
                        Log::info('Chargeback cancelled - Movido parcialmente de frozen_balance para balance', [
                            'chargeback_id' => $chargeback->id,
                            'transaction_id' => $transaction->id,
                            'amount_net' => $amountNet,
                            'amount_released' => $amountToRelease,
                            'frozen_balance_before' => $frozenBalanceBefore,
                            'frozen_balance_after' => $wallet->fresh()->frozen_balance,
                            'balance_after' => $wallet->fresh()->balance,
                        ]);
                    }
                    
                    // Recarrega novamente para garantir valores atualizados
                    $wallet->refresh();
                }

                // Remove taxa de extorno quando cancela
                $observer = new \App\Observers\TransactionObserver();
                $observer->removeRefundFee($user);

                // Atualiza status da transação de volta para completed
                // Usa withoutEvents para evitar que o Observer processe
                // O Observer não vai processar porque o chargeback já está cancelado
                if ($transaction) {
                    Transaction::withoutEvents(function () use ($transaction) {
                        $transaction->update(['status' => 'completed']);
                    });
                }

                // Desbloqueia saques se não houver outros MEDs pendentes
                $otherPendingChargebacks = Chargeback::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->where('id', '!=', $chargeback->id)
                    ->exists();

                if (!$otherPendingChargebacks) {
                    $user->update(['bloquear_saque' => false]);
                }

                // Recarrega o wallet para ter os valores finais atualizados
                if ($wallet) {
                    $wallet->refresh();
                }

                Log::info('Chargeback cancelled - Bloqueio cautelar removido e taxa de extorno removida', [
                    'chargeback_id' => $chargeback->id,
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id ?? null,
                    'amount_net' => $transaction->amount_net ?? $chargeback->amount,
                    'frozen_balance_after' => $wallet->frozen_balance ?? 0,
                    'balance_after' => $wallet->balance ?? 0,
                ]);
            });

            return back()->with('success', 'MED cancelado! O bloqueio cautelar foi removido.');
        } catch (\Exception $e) {
            Log::error('Error cancelling chargeback', [
                'chargeback_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Erro ao cancelar MED: ' . $e->getMessage());
        }
    }

    /**
     * Bloqueia saques do usuário
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function blockWithdrawal(int $id): RedirectResponse
    {
        $chargeback = Chargeback::with('user')->findOrFail($id);

        $chargeback->user->update(['bloquear_saque' => true]);
        $chargeback->update(['withdrawal_blocked' => true]);

        return back()->with('success', 'Saques bloqueados para este usuário!');
    }

    /**
     * Desbloqueia saques do usuário
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function unblockWithdrawal(int $id): RedirectResponse
    {
        $chargeback = Chargeback::with('user')->findOrFail($id);

        // Verifica se há outros MEDs pendentes
        $otherPendingChargebacks = Chargeback::where('user_id', $chargeback->user_id)
            ->where('status', 'pending')
            ->where('id', '!=', $chargeback->id)
            ->exists();

        if (!$otherPendingChargebacks) {
            $chargeback->user->update(['bloquear_saque' => false]);
        }

        $chargeback->update(['withdrawal_blocked' => false]);

        return back()->with('success', 'Saques desbloqueados para este usuário!');
    }

    /**
     * Debita saldo manualmente
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function debitBalance(int $id): RedirectResponse
    {
        $chargeback = Chargeback::with(['user', 'transaction'])->findOrFail($id);

        if ($chargeback->status !== 'pending') {
            return back()->with('error', 'Este MED já foi processado.');
        }

        try {
            DB::transaction(function () use ($chargeback) {
                $user = $chargeback->user;
                $wallet = $user->wallet;

                if (!$wallet) {
                    $wallet = Wallet::create([
                        'user_id' => $user->id,
                        'balance' => 0.00,
                        'frozen_balance' => 0.00,
                        'negative_balance' => 0.00,
                    ]);
                }

                $availableBalance = $wallet->balance - $wallet->frozen_balance;

                if ($availableBalance >= $chargeback->amount) {
                    $wallet->decrement('balance', $chargeback->amount);
                    $chargeback->update(['balance_debited' => true]);
                } else {
                    $remainingAmount = $chargeback->amount - $availableBalance;
                    
                    if ($availableBalance > 0) {
                        $wallet->decrement('balance', $availableBalance);
                    }
                    
                    $wallet->increment('negative_balance', $remainingAmount);
                    $chargeback->update([
                        'balance_debited' => $availableBalance > 0,
                        'account_negativated' => true,
                        'negative_balance' => $remainingAmount,
                    ]);
                }
            });

            return back()->with('success', 'Saldo debitado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao debitar saldo: ' . $e->getMessage());
        }
    }
}
