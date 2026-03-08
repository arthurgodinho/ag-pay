<?php

namespace App\Services;

use App\Models\PaymentSplit;
use App\Models\Transaction;
use App\Models\TransactionSplit;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentSplitService
{
    /**
     * Processa splits automaticamente para uma transação completada
     *
     * @param Transaction $transaction
     * @return void
     */
    public function processSplits(Transaction $transaction): void
    {
        $user = $transaction->user;
        if (!$user) {
            return;
        }

        // 1. Identifica se há splits configurados (Produto ou Gerente)
        $hasProductSplits = $transaction->product_id ? true : false;
        $hasManagerSplit = ($user->manager_id && ($user->split_fixed > 0 || $user->split_variable > 0));

        if (!$hasProductSplits && !$hasManagerSplit) {
            return; // Nada a processar
        }

        $amountNet = $transaction->amount_net;
        $availableAmount = $amountNet;
        $totalSplitAmount = 0;
        $processedSplits = [];

        // Busca splits de produto se houver
        $productSplits = collect();
        if ($hasProductSplits) {
            $productSplits = PaymentSplit::where('user_id', $transaction->user_id)
                ->where('is_active', true)
                ->orderBy('priority', 'desc')
                ->orderBy('id', 'asc')
                ->get();
        }

        // Não inicia uma nova transação se já estiver dentro de uma
        $shouldCommit = !DB::transactionLevel();
        
        if ($shouldCommit) {
            DB::beginTransaction();
        }
        
        try {
            // 1. Processa Splits de Produto (Prioridade)
            foreach ($productSplits as $split) {
                $splitAmount = 0;

                if ($split->split_type === 'percentage') {
                    $splitAmount = ($amountNet * $split->split_value) / 100;
                } else {
                    $splitAmount = min($split->split_value, $availableAmount - $totalSplitAmount);
                }

                // Garante que não ultrapasse o valor disponível
                if ($totalSplitAmount + $splitAmount > $availableAmount) {
                    $splitAmount = $availableAmount - $totalSplitAmount;
                }

                if ($splitAmount > 0) {
                    $this->executeSplit($transaction, $split->recipient_user_id, $splitAmount, 'product_split', $split->id);
                    
                    $totalSplitAmount += $splitAmount;
                    $processedSplits[] = [
                        'type' => 'product',
                        'recipient_id' => $split->recipient_user_id,
                        'amount' => $splitAmount
                    ];
                }
            }

            // 2. Processa Split de Gerente (Se houver saldo restante)
            if ($hasManagerSplit) {
                $managerAmount = 0;
                
                // Cálculo Fixo
                if ($user->split_fixed > 0) {
                    $managerAmount += floatval($user->split_fixed);
                }
                
                // Cálculo Variável (Sobre o valor líquido total da transação)
                if ($user->split_variable > 0) {
                    $managerAmount += ($amountNet * floatval($user->split_variable)) / 100;
                }
                
                // Verifica saldo restante
                $remainingAmount = $availableAmount - $totalSplitAmount;
                
                if ($managerAmount > $remainingAmount) {
                    $managerAmount = $remainingAmount;
                }
                
                if ($managerAmount > 0) {
                    $this->executeSplit($transaction, $user->manager_id, $managerAmount, 'manager_split');
                    
                    $totalSplitAmount += $managerAmount;
                    $processedSplits[] = [
                        'type' => 'manager',
                        'recipient_id' => $user->manager_id,
                        'amount' => $managerAmount
                    ];
                }
            }

            // Se houve splits processados, desconta do valor líquido do usuário original
            if ($totalSplitAmount > 0) {
                $userWallet = $transaction->user->wallet;
                if ($userWallet) {
                    // Decrementa o valor que foi dividido
                    $userWallet->decrement('balance', $totalSplitAmount);
                }

                Log::info('Payment splits processed', [
                    'transaction_id' => $transaction->id,
                    'total_split' => $totalSplitAmount,
                    'splits' => $processedSplits,
                ]);
            }

            if ($shouldCommit) {
                DB::commit();
            }
        } catch (\Exception $e) {
            if ($shouldCommit) {
                DB::rollBack();
            }
            Log::error('Error processing payment splits', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Executa a movimentação financeira de um split
     */
    private function executeSplit(Transaction $transaction, int $recipientId, float $amount, string $type, ?int $paymentSplitId = null): void
    {
        // Credita na carteira do recebedor
        $recipientWallet = Wallet::firstOrCreate(
            ['user_id' => $recipientId],
            ['balance' => 0.00, 'frozen_balance' => 0.00]
        );
        $recipientWallet->increment('balance', $amount);

        // Registra o split processado
        TransactionSplit::create([
            'transaction_id' => $transaction->id,
            'payment_split_id' => $paymentSplitId, // Pode ser null para manager split
            'recipient_user_id' => $recipientId,
            'amount' => $amount,
            'split_type' => $type, // 'product_split' ou 'manager_split'
            'split_value' => $amount, // Valor efetivo
        ]);
    }

    /**
     * Calcula o valor que será dividido em splits (para preview)
     *
     * @param int $userId
     * @param float $amountNet
     * @return array
     */
    public function calculateSplits(int $userId, float $amountNet): array
    {
        $user = \App\Models\User::find($userId);
        if (!$user) return [];

        $results = [];
        $totalSplit = 0;

        // 1. Product Splits
        $splits = PaymentSplit::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($splits as $split) {
            $splitAmount = 0;

            if ($split->split_type === 'percentage') {
                $splitAmount = ($amountNet * $split->split_value) / 100;
            } else {
                $splitAmount = min($split->split_value, $amountNet - $totalSplit);
            }

            if ($totalSplit + $splitAmount > $amountNet) {
                $splitAmount = $amountNet - $totalSplit;
            }

            if ($splitAmount > 0) {
                $results[] = [
                    'recipient' => $split->recipient->name ?? 'N/A',
                    'amount' => $splitAmount,
                    'type' => 'Produto: ' . $split->split_type,
                ];
                $totalSplit += $splitAmount;
            }
        }

        // 2. Manager Split
        if ($user->manager_id && ($user->split_fixed > 0 || $user->split_variable > 0)) {
            $managerAmount = 0;
            if ($user->split_fixed > 0) $managerAmount += floatval($user->split_fixed);
            if ($user->split_variable > 0) $managerAmount += ($amountNet * floatval($user->split_variable)) / 100;

            $remaining = $amountNet - $totalSplit;
            if ($managerAmount > $remaining) $managerAmount = $remaining;

            if ($managerAmount > 0) {
                $managerName = $user->manager->name ?? 'Gerente';
                $results[] = [
                    'recipient' => $managerName . ' (Gerente)',
                    'amount' => $managerAmount,
                    'type' => 'Split Gerente',
                ];
                $totalSplit += $managerAmount;
            }
        }

        return [
            'splits' => $results,
            'total_split' => $totalSplit,
            'remaining' => $amountNet - $totalSplit,
        ];
    }
}
