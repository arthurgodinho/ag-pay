<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\SystemGatewayConfig;
use App\Events\PaymentReceived;
use App\Services\Gateways\GatewayFactory;
use App\Services\TransactionReleaseService;
use App\Services\PaymentSplitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionStatusService
{
    /**
     * Verifica e atualiza status de todas as transações pendentes
     * 
     * @return array Estatísticas do processamento
     */
    public function checkPendingTransactions(): array
    {
        $stats = [
            'checked' => 0,
            'updated' => 0,
            'errors' => 0,
            'by_gateway' => [],
        ];

        // Busca transações pendentes criadas nas últimas 48 horas
        $pendingTransactions = Transaction::where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(48))
            ->whereNotNull('external_id')
            ->whereNotNull('gateway_provider')
            ->with('user')
            ->get();

        Log::info('TransactionStatusService: Iniciando verificação de transações pendentes', [
            'total' => $pendingTransactions->count(),
        ]);

        foreach ($pendingTransactions as $transaction) {
            try {
                $stats['checked']++;
                
                $result = $this->checkTransactionStatus($transaction);
                
                if ($result['updated']) {
                    $stats['updated']++;
                    
                    $gateway = $transaction->gateway_provider ?? 'unknown';
                    if (!isset($stats['by_gateway'][$gateway])) {
                        $stats['by_gateway'][$gateway] = 0;
                    }
                    $stats['by_gateway'][$gateway]++;
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error('TransactionStatusService: Erro ao verificar transação', [
                    'transaction_id' => $transaction->id,
                    'transaction_uuid' => $transaction->uuid,
                    'gateway' => $transaction->gateway_provider,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('TransactionStatusService: Verificação concluída', $stats);

        return $stats;
    }

    /**
     * Verifica o status de uma transação específica no gateway
     * 
     * @param Transaction $transaction
     * @return array ['updated' => bool, 'status' => string]
     */
    public function checkTransactionStatus(Transaction $transaction): array
    {
        if (!$transaction->external_id || !$transaction->gateway_provider) {
            Log::warning('TransactionStatusService: Transação sem external_id ou gateway_provider', [
                'transaction_id' => $transaction->id,
            ]);
            return ['updated' => false, 'status' => $transaction->status];
        }

        // Busca configuração do gateway
        $gatewayConfig = SystemGatewayConfig::where('provider_name', $transaction->gateway_provider)
            ->where('is_active_for_pix', true)
            ->first();

        if (!$gatewayConfig || empty($gatewayConfig->client_id) || empty($gatewayConfig->client_secret)) {
            Log::warning('TransactionStatusService: Gateway não configurado ou inativo', [
                'transaction_id' => $transaction->id,
                'gateway' => $transaction->gateway_provider,
            ]);
            return ['updated' => false, 'status' => $transaction->status];
        }

        try {
            // Cria instância do gateway
            $gateway = GatewayFactory::make(
                $gatewayConfig->provider_name,
                $gatewayConfig->client_id,
                $gatewayConfig->client_secret
            );

            // Consulta status no gateway usando external_id (transaction_id do gateway)
            $gatewayResponse = $gateway->consultTransaction($transaction->external_id);

            // Extrai status da resposta
            $gatewayStatus = $this->extractStatusFromResponse($transaction->gateway_provider, $gatewayResponse);

            // Se o status mudou para pago, processa a liberação
            if ($gatewayStatus === 'completed' && $transaction->status !== 'completed') {
                return $this->processCompletedTransaction($transaction, $gatewayResponse);
            }

            // Se o status mudou para falhou, atualiza
            if (in_array($gatewayStatus, ['failed', 'expired', 'cancelled']) && $transaction->status !== $gatewayStatus) {
                $transaction->update(['status' => $gatewayStatus]);
                
                Log::info('TransactionStatusService: Status atualizado para falhou', [
                    'transaction_id' => $transaction->id,
                    'old_status' => $transaction->status,
                    'new_status' => $gatewayStatus,
                ]);
                
                return ['updated' => true, 'status' => $gatewayStatus];
            }

            return ['updated' => false, 'status' => $transaction->status];

        } catch (\Exception $e) {
            Log::error('TransactionStatusService: Erro ao consultar gateway', [
                'transaction_id' => $transaction->id,
                'gateway' => $transaction->gateway_provider,
                'external_id' => $transaction->external_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Extrai status da resposta do gateway
     * 
     * @param string $gatewayProvider
     * @param array $response
     * @return string
     */
    private function extractStatusFromResponse(string $gatewayProvider, array $response): string
    {
        // Tenta extrair status de várias formas possíveis
        $status = $response['status'] 
            ?? $response['payment_status'] 
            ?? $response['situation']
            ?? $response['transaction_status']
            ?? null;

        if (!$status) {
            return 'pending';
        }

        // Normaliza status baseado no gateway
        return match(strtolower($gatewayProvider)) {
            'bspay' => $this->normalizeBsPayStatus($status),
            'venit' => $this->normalizeVenitStatus($status),
            default => 'pending',
        };
    }

    /**
     * Normaliza status da BsPay
     */
    private function normalizeBsPayStatus($status): string
    {
        $statusStr = is_numeric($status) ? (string) $status : strtoupper((string) $status);
        
        return match($statusStr) {
            'PAID', '1', 'CONFIRMED', 'APPROVED' => 'completed',
            'PENDING', '0', 'WAITING' => 'pending',
            'FAILED', 'REJECTED', 'CANCELLED' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Normaliza status da Venit
     */
    private function normalizeVenitStatus($status): string
    {
        $statusStr = strtolower((string) $status);
        
        return match($statusStr) {
            'paid' => 'completed',
            'waiting_payment' => 'pending',
            'refused', 'failed' => 'failed',
            'canceled', 'cancelled' => 'cancelled',
            'expired' => 'expired',
            default => 'pending',
        };
    }



    /**
     * Processa transação completada
     * 
     * @param Transaction $transaction
     * @param array $gatewayResponse
     * @return array
     */
    private function processCompletedTransaction(Transaction $transaction, array $gatewayResponse): array
    {
        return DB::transaction(function () use ($transaction, $gatewayResponse) {
            // Salva status antigo
            $oldStatus = $transaction->status;
            
            // Atualiza status para completed
            $transaction->update(['status' => 'completed']);

            $user = $transaction->user;
            
            if (!$user) {
                Log::error('TransactionStatusService: Usuário não encontrado', [
                    'transaction_id' => $transaction->id,
                ]);
                return ['updated' => false, 'status' => $transaction->status];
            }

            // Usa TransactionReleaseService para processar a liberação corretamente
            $releaseService = app(TransactionReleaseService::class);
            
            if ($transaction->type === 'pix') {
                // PIX: libera imediatamente
                $releaseService->releaseImmediately($transaction);
            } elseif ($transaction->type === 'credit') {
                // Cartão: agenda liberação
                $releaseService->scheduleRelease($transaction);
            }

            // Processa splits automaticamente
            try {
                $splitService = new PaymentSplitService();
                $splitService->processSplits($transaction);
            } catch (\Exception $e) {
                Log::error('TransactionStatusService: Erro ao processar splits', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
            }

            Log::info('TransactionStatusService: Transação processada e saldo creditado', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'amount_net' => $transaction->amount_net,
            ]);
            
            // Dispara evento de pagamento recebido (fora da transação)
            if ($oldStatus !== 'completed') {
                try {
                    event(new PaymentReceived($transaction));
                } catch (\Exception $e) {
                    Log::error('Erro ao disparar evento PaymentReceived no TransactionStatusService', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return ['updated' => true, 'status' => 'completed'];
        });
    }
}

