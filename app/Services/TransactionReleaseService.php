<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionReleaseService
{
    /**
     * Libera uma transação para saque
     * Para PIX: libera imediatamente
     * Para Cartão: libera após o prazo configurado
     */
    public function releaseTransaction(Transaction $transaction): void
    {
        if ($transaction->type === 'pix') {
            // PIX é liberado imediatamente
            $this->releaseImmediately($transaction);
        } elseif ($transaction->type === 'credit') {
            // Cartão tem prazo de liberação
            $this->scheduleRelease($transaction);
        }
    }

    /**
     * Libera PIX imediatamente
     */
    public function releaseImmediately(Transaction $transaction): void
    {
        if ($transaction->released_at) {
            return; // Já foi liberado
        }

        // Não inicia uma nova transação se já estiver dentro de uma
        $shouldCommit = !DB::transactionLevel();
        
        if ($shouldCommit) {
            DB::beginTransaction();
        }
        
        try {
            $wallet = $transaction->user->wallet ?? Wallet::create([
                'user_id' => $transaction->user_id,
                'balance' => 0.00,
                'frozen_balance' => 0.00,
            ]);

            // Verifica se já foi creditado (double check)
            if (!$transaction->fresh()->released_at) {
                $wallet->increment('balance', $transaction->amount_net);
                // Incrementa o saldo acumulado (ganhos totais) para gamificação
                $wallet->increment('accumulated_balance', $transaction->amount_net);
            }
            
            $transaction->update([
                'released_at' => now(),
                'available_at' => now(),
            ]);

            Log::info('Transaction released immediately (PIX)', [
                'transaction_id' => $transaction->id,
                'amount_net' => $transaction->amount_net,
            ]);
            
            if ($shouldCommit) {
                DB::commit();
            }
        } catch (\Exception $e) {
            if ($shouldCommit) {
                DB::rollBack();
            }
            Log::error('Error releasing transaction immediately', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Agenda liberação de cartão após prazo
     */
    public function scheduleRelease(Transaction $transaction): void
    {
        if ($transaction->available_at) {
            return; // Já foi agendado
        }

        $releaseDays = (int) Setting::get('card_release_days', 5);
        $availableAt = now()->addDays($releaseDays);

        $transaction->update([
            'available_at' => $availableAt,
        ]);

        Log::info('Transaction release scheduled (Card)', [
            'transaction_id' => $transaction->id,
            'release_days' => $releaseDays,
            'available_at' => $availableAt,
        ]);
    }

    /**
     * Libera transações de cartão que atingiram o prazo
     */
    public function releaseExpiredCardTransactions(): int
    {
        $released = 0;

        $transactions = Transaction::where('type', 'credit')
            ->where('status', 'completed')
            ->whereNull('released_at')
            ->whereNotNull('available_at')
            ->where('available_at', '<=', now())
            ->get();

        foreach ($transactions as $transaction) {
            try {
                DB::transaction(function () use ($transaction) {
                    $wallet = $transaction->user->wallet ?? Wallet::create([
                        'user_id' => $transaction->user_id,
                        'balance' => 0.00,
                        'frozen_balance' => 0.00,
                    ]);

                    // Credita o valor líquido na carteira
                    $wallet->increment('balance', $transaction->amount_net);
                    $wallet->increment('accumulated_balance', $transaction->amount_net);
                    
                    // Processa splits automaticamente
                    try {
                        $splitService = new \App\Services\PaymentSplitService();
                        $splitService->processSplits($transaction);
                    } catch (\Exception $e) {
                        Log::error('Error processing splits for card transaction: ' . $transaction->id, [
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    $transaction->update([
                        'released_at' => now(),
                    ]);

                    Log::info('Card transaction released after period', [
                        'transaction_id' => $transaction->id,
                        'amount_net' => $transaction->amount_net,
                        'available_at' => $transaction->available_at,
                    ]);
                });

                $released++;
            } catch (\Exception $e) {
                Log::error('Error releasing transaction', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $released;
    }
}

