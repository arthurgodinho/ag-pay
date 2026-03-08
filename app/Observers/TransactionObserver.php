<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Events\PaymentReceived;
use App\Events\PaymentFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Verifica se o status mudou
        if ($transaction->wasChanged('status')) {
            $oldStatus = $transaction->getOriginal('status');
            $newStatus = $transaction->status;
            
            // IMPORTANTE: Se há chargeback cancelado ou aprovado, NÃO processa nada
            // O admin já gerenciou o saldo
            $hasProcessedChargeback = \App\Models\Chargeback::where('transaction_id', $transaction->id)
                ->whereIn('status', ['approved', 'cancelled'])
                ->exists();
            
            if ($hasProcessedChargeback) {
                Log::info('TransactionObserver: Chargeback já processado pelo admin, ignorando Observer completamente', [
                    'transaction_id' => $transaction->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);
                return;
            }
            
            // Se mudou para chargeback ou mediation (disputa)
            // IMPORTANTE: Só processa se houver um registro de Chargeback na tabela
            // Não processa apenas porque o status mudou (evita processamento incorreto)
            if (in_array($newStatus, ['chargeback', 'mediation']) && $oldStatus === 'completed') {
                // Verifica se há um chargeback registrado para esta transação
                $hasChargeback = \App\Models\Chargeback::where('transaction_id', $transaction->id)->exists();
                
                if (!$hasChargeback) {
                    Log::warning('TransactionObserver: Status mudou para chargeback/mediation mas não há registro de Chargeback - IGNORANDO', [
                        'transaction_id' => $transaction->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                    ]);
                    return; // Não processa se não houver chargeback registrado
                }
                
                $this->handleChargebackOrDispute($transaction);
            }
            
            // Se estava em chargeback/mediation e mudou para outro status
            // Remove o saldo do frozen_balance se não houver chargeback pendente
            // COMENTADO: A lógica de reversão já é tratada nos Controllers (AdminTransactionController e AdminChargebackController)
            // Manter aqui causaria duplicidade na devolução do saldo (double refund)
            /*
            if (in_array($oldStatus, ['chargeback', 'mediation']) && !in_array($newStatus, ['chargeback', 'mediation'])) {
                $this->handleChargebackOrDisputeReversal($transaction, $oldStatus);
            }
            */
            
            // Dispara eventos de email quando status muda
            if ($newStatus === 'completed' && $oldStatus !== 'completed' && $transaction->type === 'credit') {
                // Pagamento recebido
                try {
                    event(new PaymentReceived($transaction));
                } catch (\Exception $e) {
                    Log::error('Erro ao disparar evento PaymentReceived', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            if ($newStatus === 'failed' && $oldStatus !== 'failed') {
                // Pagamento falhou
                try {
                    event(new PaymentFailed($transaction));
                } catch (\Exception $e) {
                    Log::error('Erro ao disparar evento PaymentFailed', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
    
    /**
     * Move o saldo líquido recebido para o saldo bloqueado cautelar quando há chargeback/disputa
     */
    private function handleChargebackOrDispute(Transaction $transaction): void
    {
        try {
            // Verifica se já foi processado (evita processamento duplicado)
            // Se a transação já tem chargebacks aprovados ou cancelados, não processa novamente
            $hasProcessedChargeback = \App\Models\Chargeback::where('transaction_id', $transaction->id)
                ->whereIn('status', ['approved', 'cancelled'])
                ->exists();
            
            if ($hasProcessedChargeback) {
                Log::info('TransactionObserver: Chargeback já foi processado pelo admin, ignorando', [
                    'transaction_id' => $transaction->id,
                ]);
                return;
            }
            
            // Verifica se já há um chargeback pendente para esta transação
            // Se houver, verifica se o saldo já está no frozen_balance (evita duplicação)
            $hasPendingChargeback = \App\Models\Chargeback::where('transaction_id', $transaction->id)
                ->where('status', 'pending')
                ->exists();
            
            if ($hasPendingChargeback) {
                $user = $transaction->user;
                if ($user && $user->wallet) {
                    $wallet = $user->wallet;
                    $amountNet = $transaction->amount_net ?? 0;
                    
                    // Recarrega o wallet para ter valores atualizados
                    $wallet->refresh();
                    
                    // Se o frozen_balance já tem pelo menos o amount_net, não processa novamente
                    // (pode ter sido processado anteriormente)
                    if ($amountNet > 0 && $wallet->frozen_balance >= $amountNet) {
                        Log::info('TransactionObserver: Saldo já está no bloqueio cautelar, ignorando processamento duplicado', [
                            'transaction_id' => $transaction->id,
                            'frozen_balance' => $wallet->frozen_balance,
                            'amount_net' => $amountNet,
                        ]);
                        return;
                    }
                }
            }
            
            DB::transaction(function () use ($transaction) {
                $user = $transaction->user;
                
                if (!$user) {
                    Log::warning('TransactionObserver: Usuário não encontrado para transação', [
                        'transaction_id' => $transaction->id,
                    ]);
                    return;
                }
                
                $wallet = $user->wallet;
                
                if (!$wallet) {
                    $wallet = Wallet::create([
                        'user_id' => $user->id,
                        'balance' => 0.00,
                        'frozen_balance' => 0.00,
                        'negative_balance' => 0.00,
                    ]);
                }
                
                // Valor líquido que o cliente recebeu
                $amountNet = $transaction->amount_net ?? 0;
                
                if ($amountNet <= 0) {
                    Log::warning('TransactionObserver: amount_net inválido ou zero', [
                        'transaction_id' => $transaction->id,
                        'amount_net' => $amountNet,
                    ]);
                    return;
                }
                
                // Verifica se o saldo disponível tem o valor necessário
                $availableBalance = $wallet->balance;
                
                if ($availableBalance >= $amountNet) {
                    // Move o valor de balance para frozen_balance (bloqueio cautelar)
                    $wallet->decrement('balance', $amountNet);
                    $wallet->increment('frozen_balance', $amountNet);
                    
                    Log::info('TransactionObserver: Saldo movido para bloqueio cautelar', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'amount_net' => $amountNet,
                        'status' => $transaction->status,
                        'old_balance' => $availableBalance,
                        'new_balance' => $wallet->fresh()->balance,
                        'new_frozen_balance' => $wallet->fresh()->frozen_balance,
                    ]);
                } else {
                    // Se não tem saldo suficiente, move o que tem e registra o restante
                    $amountToFreeze = $availableBalance;
                    $remainingAmount = $amountNet - $availableBalance;
                    
                    if ($amountToFreeze > 0) {
                        $wallet->decrement('balance', $amountToFreeze);
                        $wallet->increment('frozen_balance', $amountToFreeze);
                    }
                    
                    // O restante fica como saldo negativo
                    if ($remainingAmount > 0) {
                        $wallet->increment('negative_balance', $remainingAmount);
                    }
                    
                    Log::warning('TransactionObserver: Saldo insuficiente, parte movida para bloqueio e parte para negativo', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'amount_net' => $amountNet,
                        'amount_frozen' => $amountToFreeze,
                        'amount_negative' => $remainingAmount,
                        'status' => $transaction->status,
                    ]);
                }
                
                // Bloqueia saques automaticamente
                $user->update(['bloquear_saque' => true]);
                
                // Aplica taxa de extorno (aumenta a taxa do usuário)
                $this->applyRefundFee($user);
            });
        } catch (\Exception $e) {
            Log::error('TransactionObserver: Erro ao processar chargeback/disputa', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
    
    /**
     * Aplica taxa de extorno ao usuário quando há chargeback/disputa
     * Método público para ser chamado de controllers
     */
    public function applyRefundFee($user): void
    {
        try {
            // Taxa de extorno padrão do sistema (pode ser configurada)
            $defaultRefundFee = floatval(\App\Models\Setting::get('refund_fee_percentage', '1.00'));
            
            // Taxa atual do usuário
            $currentRefundFee = floatval($user->taxa_extorno ?? 0);
            
            // Aumenta a taxa de extorno
            $newRefundFee = $currentRefundFee + $defaultRefundFee;
            
            $user->update(['taxa_extorno' => $newRefundFee]);
            
            Log::info('TransactionObserver: Taxa de extorno aplicada', [
                'user_id' => $user->id,
                'old_fee' => $currentRefundFee,
                'new_fee' => $newRefundFee,
                'fee_increase' => $defaultRefundFee,
            ]);
        } catch (\Exception $e) {
            Log::error('TransactionObserver: Erro ao aplicar taxa de extorno', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Remove taxa de extorno quando chargeback é cancelado
     * Método público para ser chamado de controllers
     */
    public function removeRefundFee($user): void
    {
        try {
            // Taxa de extorno padrão do sistema
            $defaultRefundFee = floatval(\App\Models\Setting::get('refund_fee_percentage', '1.00'));
            
            // Taxa atual do usuário
            $currentRefundFee = floatval($user->taxa_extorno ?? 0);
            
            // Remove a taxa de extorno (não pode ficar negativo)
            $newRefundFee = max(0, $currentRefundFee - $defaultRefundFee);
            
            $user->update(['taxa_extorno' => $newRefundFee]);
            
            Log::info('TransactionObserver: Taxa de extorno removida', [
                'user_id' => $user->id,
                'old_fee' => $currentRefundFee,
                'new_fee' => $newRefundFee,
                'fee_decrease' => $defaultRefundFee,
            ]);
        } catch (\Exception $e) {
            Log::error('TransactionObserver: Erro ao remover taxa de extorno', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
