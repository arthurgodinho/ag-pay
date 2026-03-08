<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Setting;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminTransactionController extends Controller
{
    /**
     * Lista todas as transações (Depósitos e Saques)
     */
    public function index(Request $request): View
    {
        $query = Transaction::with(['user']);
        $withdrawalsQuery = Withdrawal::with('user');

        // Filtros para transações
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('uuid', 'like', "%{$search}%")
                  ->orWhere('external_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            if ($request->type === 'pix') {
                $query->where('type', 'pix');
            }
        }

        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        // Filtros para saques
        if ($request->filled('search')) {
            $search = $request->search;
            $withdrawalsQuery->where(function($q) use ($search) {
                $q->where('pix_key', 'like', "%{$search}%")
                  ->orWhere('external_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('withdrawal_status') && $request->withdrawal_status !== 'todos') {
            $withdrawalsQuery->where('status', $request->withdrawal_status);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20, ['*'], 'transactions_page')->withQueryString();
        $withdrawals = $withdrawalsQuery->orderBy('created_at', 'desc')->paginate(20, ['*'], 'withdrawals_page')->withQueryString();

        // Estatísticas
        $stats = [
            'total_deposits' => Transaction::count(),
            'total_withdrawals' => Withdrawal::count(),
            'pending_deposits' => Transaction::where('status', 'pending')->count(),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
            'total_amount' => Transaction::where('status', 'completed')->sum('amount_net'),
        ];

        return view('admin.transactions.index', compact('transactions', 'withdrawals', 'stats'));
    }

    /**
     * Edita uma transação (depósito)
     */
    public function editTransaction(int $id): View
    {
        $transaction = Transaction::with(['user'])->findOrFail($id);
        
        return view('admin.transactions.edit-transaction', compact('transaction'));
    }

    /**
     * Atualiza uma transação (depósito)
     */
    public function updateTransaction(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,cancelled,chargeback,mediation',
            'amount_gross' => 'nullable|numeric|min:0.01',
        ]);

        $transaction = Transaction::with(['user'])->findOrFail($id);
        $oldStatus = $transaction->status;
        $newStatus = $request->status;

        try {
            DB::transaction(function () use ($transaction, $oldStatus, $newStatus, $request) {
                $user = $transaction->user;
                $wallet = $user->wallet ?? Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0.00,
                    'frozen_balance' => 0.00,
                ]);

                // Se está marcando como "completed" (pago) e não estava antes
                if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                    // Recalcula as taxas baseado no tipo de transação (se o valor foi alterado)
                    $amountGross = $request->amount_gross ?? $transaction->amount_gross;
                    
                    // Se o valor foi alterado, recalcula as taxas
                    if ($request->filled('amount_gross') && $amountGross != $transaction->amount_gross) {
                        $fee = $this->calculateFee($transaction, $amountGross);
                        $amountNet = $amountGross - $fee;
                    } else {
                        // Mantém os valores originais
                        $fee = $transaction->fee;
                        $amountNet = $transaction->amount_net;
                    }

                    // Atualiza os valores da transação
                    $transaction->update([
                        'amount_gross' => $amountGross,
                        'fee' => $fee,
                        'amount_net' => $amountNet,
                        'status' => 'completed',
                    ]);

                    // Usa TransactionReleaseService para processar a liberação corretamente
                    $releaseService = app(\App\Services\TransactionReleaseService::class);
                    if ($transaction->type === 'pix') {
                        // PIX: libera imediatamente
                        $releaseService->releaseImmediately($transaction);
                    } elseif ($transaction->type === 'credit') {
                        // Cartão: agenda liberação
                        $releaseService->scheduleRelease($transaction);
                    }

                    // Processa splits automaticamente
                    try {
                        $splitService = new \App\Services\PaymentSplitService();
                        $splitService->processSplits($transaction);
                    } catch (\Exception $e) {
                        Log::error('Error processing splits for transaction: ' . $transaction->id, [
                            'error' => $e->getMessage()
                        ]);
                    }

                    Log::info('Transação marcada como paga pelo admin', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'amount_gross' => $amountGross,
                        'fee' => $fee,
                        'amount_net' => $amountNet,
                        'type' => $transaction->type,
                        'released_at' => $transaction->released_at,
                        'available_at' => $transaction->available_at,
                    ]);

                } elseif ($oldStatus === 'completed' && $newStatus !== 'completed') {
                    // Se está mudando para chargeback ou mediation, move saldo para bloqueio cautelar
                    if (in_array($newStatus, ['chargeback', 'mediation'])) {
                        $amountNet = $transaction->amount_net;
                        
                        // Move saldo de balance para frozen_balance (bloqueio cautelar)
                        if ($wallet->balance >= $amountNet) {
                            $wallet->decrement('balance', $amountNet);
                            $wallet->increment('frozen_balance', $amountNet);
                        } else {
                            // Se não tem saldo suficiente, move o que tem
                            $amountToFreeze = $wallet->balance;
                            if ($amountToFreeze > 0) {
                                $wallet->decrement('balance', $amountToFreeze);
                                $wallet->increment('frozen_balance', $amountToFreeze);
                            }
                            // O restante fica como negativo
                            $remainingAmount = $amountNet - $amountToFreeze;
                            if ($remainingAmount > 0) {
                                $wallet->increment('negative_balance', $remainingAmount);
                            }
                        }
                        
                        // Bloqueia saques imediatamente
                        $user->update(['bloquear_saque' => true]);
                        
                        // Aplica taxa de extorno
                        $observer = new \App\Observers\TransactionObserver();
                        $observer->applyRefundFee($user);
                        
                        Log::info('Transação movida para chargeback/mediation - Bloqueio cautelar aplicado', [
                            'transaction_id' => $transaction->id,
                            'user_id' => $user->id,
                            'amount_net' => $amountNet,
                            'new_status' => $newStatus,
                            'balance_before' => $wallet->balance + $amountNet,
                            'balance_after' => $wallet->fresh()->balance,
                            'frozen_balance_after' => $wallet->fresh()->frozen_balance,
                        ]);
                        
                        // Atualiza o status
                        $transaction->update(['status' => $newStatus]);
                    } else {
                        // Para outros status (cancelled, failed), reverte o crédito e aplica taxa de extorno
                        $amountToRevert = $transaction->amount_net;
                        if ($wallet->balance >= $amountToRevert) {
                            $wallet->decrement('balance', $amountToRevert);
                        } else {
                            // Se não tem saldo suficiente, zera o saldo (ou poderia deixar negativo)
                            $wallet->update(['balance' => 0.00]);
                        }
                        
                        // Aplica taxa de extorno quando cancela uma transação processada
                        if ($newStatus === 'cancelled') {
                            $observer = new \App\Observers\TransactionObserver();
                            $observer->applyRefundFee($user);
                        }
                        
                        Log::info('Crédito revertido - Transação mudada de pago para outro status', [
                            'transaction_id' => $transaction->id,
                            'user_id' => $user->id,
                            'amount_net' => $amountToRevert,
                            'new_status' => $newStatus,
                        ]);
                        
                        // Atualiza apenas o status
                        $transaction->update(['status' => $newStatus]);
                    }
                } elseif (in_array($oldStatus, ['chargeback', 'mediation']) && $newStatus === 'completed') {
                    // Se estava em chargeback/mediation e volta para completed, move saldo de volta
                    $amountNet = $transaction->amount_net;
                    
                    // Move de frozen_balance de volta para balance
                    if ($wallet->frozen_balance >= $amountNet) {
                        $wallet->decrement('frozen_balance', $amountNet);
                        $wallet->increment('balance', $amountNet);
                    } else {
                        // Move o que tem de frozen_balance
                        $amountToRelease = $wallet->frozen_balance;
                        if ($amountToRelease > 0) {
                            $wallet->decrement('frozen_balance', $amountToRelease);
                            $wallet->increment('balance', $amountToRelease);
                        }
                        // Remove também do negative_balance se houver
                        $remainingAmount = $amountNet - $amountToRelease;
                        if ($remainingAmount > 0 && $wallet->negative_balance >= $remainingAmount) {
                            $wallet->decrement('negative_balance', $remainingAmount);
                        }
                    }
                    
                    // Remove taxa de extorno
                    $observer = new \App\Observers\TransactionObserver();
                    $observer->removeRefundFee($user);
                    
                    // Verifica se há outros MEDs pendentes antes de desbloquear saques
                    $otherPendingChargebacks = \App\Models\Transaction::where('user_id', $user->id)
                        ->whereIn('status', ['mediation', 'chargeback'])
                        ->where('id', '!=', $transaction->id)
                        ->exists();
                    
                    if (!$otherPendingChargebacks) {
                        $user->update(['bloquear_saque' => false]);
                    }
                    
                    Log::info('Transação voltou para completed - Bloqueio cautelar removido', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'amount_net' => $amountNet,
                        'old_status' => $oldStatus,
                        'balance_after' => $wallet->fresh()->balance,
                        'frozen_balance_after' => $wallet->fresh()->frozen_balance,
                    ]);
                    
                    // Atualiza o status
                    $transaction->update(['status' => $newStatus]);
                    
                } elseif (in_array($oldStatus, ['chargeback', 'mediation']) && $newStatus === 'cancelled') {
                    // Se estava em chargeback/mediation e vai para cancelled
                    // O valor sai de frozen_balance e SOMEM (não volta para balance)
                    $amountNet = $transaction->amount_net;
                    
                    if ($wallet->frozen_balance >= $amountNet) {
                        $wallet->decrement('frozen_balance', $amountNet);
                    } else {
                        // Zera o frozen se não tiver o suficiente (consistência)
                        $wallet->update(['frozen_balance' => 0]);
                    }
                    
                    Log::info('Transação em chargeback foi cancelada - Valor removido do bloqueio cautelar e sumiu', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'amount_net' => $amountNet,
                        'old_status' => $oldStatus,
                        'frozen_balance_after' => $wallet->fresh()->frozen_balance,
                    ]);
                    
                    // Atualiza o status
                    $transaction->update(['status' => $newStatus]);

                } else {
                    // Para outros status, apenas atualiza sem mexer no saldo
                    $updateData = ['status' => $newStatus];
                    if ($request->filled('amount_gross')) {
                        $updateData['amount_gross'] = $request->amount_gross;
                    }
                    $transaction->update($updateData);
                }
            });

            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transação atualizada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar transação', [
                'transaction_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.transactions.index')
                ->with('error', 'Erro ao atualizar transação: ' . $e->getMessage());
        }
    }

    /**
     * Edita um saque
     */
    public function editWithdrawal(int $id): View
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($id);
        
        return view('admin.transactions.edit-withdrawal', compact('withdrawal'));
    }

    /**
     * Atualiza um saque
     */
    public function updateWithdrawal(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,paid,failed,cancelled,rejected',
        ]);

        $withdrawal = Withdrawal::with(['user'])->findOrFail($id);
        $oldStatus = $withdrawal->status;
        $newStatus = $request->status;

        try {
            DB::transaction(function () use ($withdrawal, $oldStatus, $newStatus, $request) {
                $user = $withdrawal->user;
                $wallet = $user->wallet;

                if (!$wallet) {
                    throw new \Exception('Carteira do usuário não encontrada');
                }

                // Lógica para quando marca como PAGO
                if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                    // O saldo já foi debitado na solicitação, então não precisa mexer no saldo.
                    // Apenas confirma o pagamento.

                    // Marca o primeiro saque como completado
                    if (!$user->first_withdrawal_completed) {
                        $user->update(['first_withdrawal_completed' => true]);
                    }

                    Log::info('Saque marcado como pago pelo admin', [
                        'withdrawal_id' => $withdrawal->id,
                        'user_id' => $user->id,
                        'amount_gross' => $withdrawal->amount_gross,
                    ]);

                } 
                // Lógica para quando reverte de PAGO para outro status
                elseif ($oldStatus === 'paid' && $newStatus !== 'paid') {
                    // Se estava pago e mudou para Cancelado/Falha/Rejeitado, devolve o dinheiro.
                    if (in_array($newStatus, ['cancelled', 'failed', 'rejected'])) {
                        $wallet->increment('balance', $withdrawal->amount_gross);
                    }
                    // Se mudou para Pendente/Processando, o dinheiro continua debitado (voando), aguardando novo pagamento.
                    
                    Log::info('Saque revertido de status pago', [
                        'withdrawal_id' => $withdrawal->id,
                        'user_id' => $user->id,
                        'amount_gross' => $withdrawal->amount_gross,
                        'new_status' => $newStatus,
                    ]);
                }

                // Lógica para Cancelamento/Rejeição de saque PENDENTE/PROCESSANDO
                // Se estava pendente/processando e foi cancelado/falhou/rejeitado
                if (in_array($oldStatus, ['pending', 'processing']) && in_array($newStatus, ['cancelled', 'failed', 'rejected'])) {
                    // Devolve o valor para o saldo disponível (balance)
                    $wallet->increment('balance', $withdrawal->amount_gross);

                    Log::info('Saque cancelado/rejeitado - Valor estornado para saldo', [
                        'withdrawal_id' => $withdrawal->id,
                        'user_id' => $user->id,
                        'amount_gross' => $withdrawal->amount_gross
                    ]);
                }
                
                // Atualiza o registro do saque
                $updateData = ['status' => $newStatus];
                
                if ($newStatus === 'paid') {
                    $updateData['paid_at'] = now();
                    if ($request->has('proof')) {
                        $updateData['proof'] = $request->proof;
                    }
                }
                
                if ($newStatus === 'rejected' && $request->filled('rejection_reason')) {
                    $updateData['rejection_reason'] = $request->rejection_reason;
                }
                
                $withdrawal->update($updateData);
            });

            return redirect()->back()
                ->with('success', 'Saque atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar saque', [
                'withdrawal_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao atualizar saque: ' . $e->getMessage());
        }
    }

    /**
     * Processa manualmente uma transação pendente (quando o webhook não foi recebido)
     */
    public function processPendingTransaction(int $id): RedirectResponse
    {
        $transaction = Transaction::with(['user'])->findOrFail($id);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Esta transação já foi processada. Status atual: ' . $transaction->status);
        }

        try {
            DB::transaction(function () use ($transaction) {
                $user = $transaction->user;
                $wallet = $user->wallet ?? Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0.00,
                    'frozen_balance' => 0.00,
                    'negative_balance' => 0.00,
                ]);

                // Salva status antigo
                $oldStatus = $transaction->status;
                
                // Atualiza o status para completed
                $transaction->update(['status' => 'completed']);
                
                // Dispara evento de pagamento recebido (após commit)
                if ($oldStatus !== 'completed') {
                    try {
                        event(new \App\Events\PaymentReceived($transaction));
                    } catch (\Exception $e) {
                        Log::error('Erro ao disparar evento PaymentReceived no AdminTransactionController', [
                            'transaction_id' => $transaction->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Usa TransactionReleaseService para processar a liberação corretamente
                $releaseService = app(\App\Services\TransactionReleaseService::class);
                if ($transaction->type === 'pix') {
                    // PIX: libera imediatamente
                    $releaseService->releaseImmediately($transaction);
                } elseif ($transaction->type === 'credit') {
                    // Cartão: agenda liberação
                    $releaseService->scheduleRelease($transaction);
                }

                // Processa splits automaticamente
                try {
                    $splitService = new \App\Services\PaymentSplitService();
                    $splitService->processSplits($transaction);
                } catch (\Exception $e) {
                    Log::error('Error processing splits for transaction: ' . $transaction->id, [
                        'error' => $e->getMessage()
                    ]);
                }



                Log::info('Transação processada manualmente pelo admin', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'amount_gross' => $transaction->amount_gross,
                    'amount_net' => $transaction->amount_net,
                    'type' => $transaction->type,
                ]);
            });

            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transação processada com sucesso! O saldo foi creditado na wallet do usuário.');

        } catch (\Exception $e) {
            Log::error('Erro ao processar transação manualmente', [
                'transaction_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.transactions.index')
                ->with('error', 'Erro ao processar transação: ' . $e->getMessage());
        }
    }

    /**
     * Remove a transaction (Deposit)
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->delete();

            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transação excluída com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir transação', [
                'transaction_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.transactions.index')
                ->with('error', 'Erro ao excluir transação: ' . $e->getMessage());
        }
    }

    /**
     * Remove a withdrawal
     */
    public function destroyWithdrawal(int $id): RedirectResponse
    {
        try {
            $withdrawal = Withdrawal::with('user')->findOrFail($id);

            if ($withdrawal->user && $withdrawal->user->wallet) {
                $wallet = $withdrawal->user->wallet;
                // Se estava pago, estorna o valor para o saldo disponível
                if ($withdrawal->status === 'paid') {
                    $wallet->increment('balance', $withdrawal->amount_gross);
                }
            }

            $withdrawal->delete();

            return redirect()->route('admin.transactions.index')
                ->with('success', 'Saque excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir saque', [
                'withdrawal_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.transactions.index')
                ->with('error', 'Erro ao excluir saque: ' . $e->getMessage());
        }
    }

    /**
     * Calcula a taxa baseado no tipo de transação (usa as taxas padrão configuradas pelo admin)
     */
    private function calculateFee(Transaction $transaction, float $amountGross): float
    {
        $user = $transaction->user;

        // SEMPRE usa as taxas do painel (não mais taxas personalizadas do usuário)
        if ($transaction->type === 'pix' || $transaction->type === 'boleto') {
            // Taxa PIX - sempre do painel
            $feeFixo = $user->getCashinPixFixo();
            $feePercentual = $user->getCashinPixPercentual();
        } else {
            // Taxa Cartão - sempre do painel
            $feeFixo = $user->getCashinCardFixo();
            $feePercentual = $user->getCashinCardPercentual();
        }

        // Calcula: Taxa Fixa + (Valor Bruto * Taxa Percentual / 100)
        $fee = $feeFixo + (($amountGross * $feePercentual) / 100);
        
        return round($fee, 2);
    }
}
