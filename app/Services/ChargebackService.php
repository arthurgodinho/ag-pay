<?php

namespace App\Services;

use App\Models\Chargeback;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChargebackService
{
    /**
     * Processa um MED automaticamente quando recebido do adquirente
     *
     * @param string $transactionExternalId ID externo da transação
     * @param float $amount Valor do MED
     * @param string|null $reason Motivo do MED
     * @param string|null $externalId ID do MED no adquirente
     * @return Chargeback|null
     */
    public function processChargeback(
        string $transactionExternalId,
        float $amount,
        ?string $reason = null,
        ?string $externalId = null
    ): ?Chargeback {
        try {
            // Busca a transação pelo external_id
            $transaction = Transaction::where('external_id', $transactionExternalId)
                ->where('status', 'completed')
                ->first();

            if (!$transaction) {
                Log::error('Chargeback: Transaction not found', [
                    'external_id' => $transactionExternalId,
                    'amount' => $amount,
                ]);
                return null;
            }

            $user = $transaction->user;
            $wallet = $user->wallet;

            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0.00,
                    'frozen_balance' => 0.00,
                    'negative_balance' => 0.00,
                ]);
            }

            return DB::transaction(function () use ($transaction, $user, $wallet, $amount, $reason, $externalId) {
                // Cria o registro de chargeback
                $chargeback = Chargeback::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'status' => 'pending',
                    'external_id' => $externalId,
                    'reason' => $reason ?? 'MED recebido do adquirente',
                ]);

                // Bloqueia saques automaticamente
                $user->update(['bloquear_saque' => true]);
                $chargeback->update(['withdrawal_blocked' => true]);

                // Atualiza status da transação para chargeback
                // O Observer vai detectar essa mudança e mover o amount_net para frozen_balance automaticamente
                $transaction->update(['status' => 'chargeback']);

                Log::info('Chargeback processed automatically - Observer vai mover saldo para bloqueio cautelar', [
                    'chargeback_id' => $chargeback->id,
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'amount_net' => $transaction->amount_net,
                ]);

                return $chargeback;
            });
        } catch (\Exception $e) {
            Log::error('Error processing chargeback', [
                'transaction_external_id' => $transactionExternalId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}



