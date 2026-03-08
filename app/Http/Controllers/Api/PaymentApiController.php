<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemGatewayConfig;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Gateways\GatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentApiController extends Controller
{
    /**
     * Cria um pagamento PIX
     */
    public function createPix(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payer_name' => 'required|string|max:255',
            'payer_email' => 'required|email',
            'payer_cpf' => 'required|string|max:14',
            'description' => 'nullable|string|max:500',
        ]);

        $user = $request->get('api_user');
        // Usa o gateway específico para cash-in PIX
        $gatewayConfig = SystemGatewayConfig::getDefaultForCashinPix($user->preferred_gateway);

        if (!$gatewayConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum gateway PIX ativo configurado',
            ], 400);
        }

        // Calcula taxas - usando as novas taxas detalhadas
        $amountGross = floatval($request->amount);
        // SEMPRE usa as taxas do painel
        $cashinPixFixo = $user->getCashinPixFixo();
        $cashinPixPercentual = $user->getCashinPixPercentual();
        $cashinPixMinima = floatval(\App\Models\Setting::get('cashin_pix_minima', '0.00'));
        
        // Calcula taxa percentual
        $feePercentual = ($amountGross * $cashinPixPercentual) / 100;
        // Aplica taxa mínima se necessário
        $fee = max($feePercentual, $cashinPixMinima) + $cashinPixFixo;
        $amountNet = $amountGross - $fee;
        
        // Arredonda para 2 casas decimais
        $amountGross = round($amountGross, 2);
        $fee = round($fee, 2);
        $amountNet = round($amountNet, 2);
        
        // Data de expiração padrão (5 minutos)
        $expiresAt = now()->addMinutes(5);

        try {
            $gateway = GatewayFactory::make(
                $gatewayConfig->provider_name,
                $gatewayConfig->client_id,
                $gatewayConfig->client_secret
            );
            $transactionUuid = Str::uuid();
            $externalIdForGateway = $transactionUuid->toString();
            
            // Determina o postback URL baseado no gateway
            $postbackRoute = 'api.webhooks.bspay'; // Default
            if ($gatewayConfig->provider_name === 'venit') {
                $postbackRoute = 'api.webhooks.venit';
            } elseif ($gatewayConfig->provider_name === 'asaas') {
                $postbackRoute = 'api.webhooks.asaas';
            } elseif ($gatewayConfig->provider_name === 'pluggou') {
                $postbackRoute = 'api.webhooks.pluggou';
            }
            
            $payerData = [
                'name' => $request->payer_name,
                'email' => $request->payer_email,
                'cpf' => $request->payer_cpf,
                'external_id' => $externalIdForGateway,
                'postback_url' => \App\Helpers\WebhookUrlHelper::generateUrl($postbackRoute), // URL absoluta com domínio do .env
                'description' => $request->description ?? 'Pagamento via PIX',
            ];

            $response = $gateway->createPix($amountGross, $payerData);

            // Obtém expires_at da resposta do gateway ou usa o padrão (5 minutos)
            $gatewayExpiresAt = isset($response['expires_at']) 
                ? \Carbon\Carbon::parse($response['expires_at']) 
                : $expiresAt;

            // Obtém o transaction_id retornado pelo gateway (pode ser diferente do external_id enviado)
            $gatewayTransactionId = $response['transaction_id'] ?? $response['transactionId'] ?? null;
            $finalExternalId = $gatewayTransactionId ?? $response['external_id'] ?? $externalIdForGateway;

            // Cria transação com expires_at
            $transaction = Transaction::create([
                'uuid' => $transactionUuid,
                'user_id' => $user->id,
                'amount_gross' => $amountGross,
                'amount_net' => $amountNet,
                'fee' => $fee,
                'type' => 'pix',
                'status' => $response['status'] ?? 'pending',
                'gateway_provider' => $gatewayConfig->provider_name,
                'external_id' => $finalExternalId,
                'description' => $request->description,
                'payer_name' => $request->payer_name,
                'payer_email' => $request->payer_email,
                'payer_cpf' => $request->payer_cpf,
                'expires_at' => $gatewayExpiresAt,
            ]);
            
            Log::info('PaymentApiController: Transação criada via API', [
                'transaction_id' => $transaction->id,
                'uuid' => $transaction->uuid,
                'external_id' => $transaction->external_id,
                'gateway_transaction_id' => $gatewayTransactionId,
                'amount_gross' => $amountGross,
                'fee' => $fee,
                'amount_net' => $amountNet,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_uuid' => $transaction->uuid,
                    'amount' => $amountGross,
                    'fee' => $fee,
                    'amount_net' => $amountNet,
                    'status' => $transaction->status,
                    'qr_code' => $response['qr_code'] ?? null,
                    'pix_code' => $response['qr_code'] ?? null,
                    'pix_key' => $response['qr_code'] ?? null,
                    'expires_at' => $gatewayExpiresAt->toIso8601String(),
                    'expires_in_seconds' => max(0, $gatewayExpiresAt->diffInSeconds(now())),
                ],
            ], 201);

        } catch (\Exception $e) {
            // Log do erro
            $transactionId = isset($transaction) && isset($transaction->id) ? $transaction->id : null;
            
            \App\Services\ErrorLogService::log(
                'Erro ao processar pagamento PIX via API',
                $e->getMessage(),
                'payment_error',
                'error',
                [
                    'transaction_id' => $transactionId,
                    'gateway' => $gatewayConfig->provider_name ?? null,
                    'payment_type' => 'pix',
                    'user_id' => $user->id ?? null,
                ],
                $e
            );

            // Atualiza transação para failed se já foi criada
            if (isset($transaction) && isset($transaction->id)) {
                try {
                    $transaction->update(['status' => 'failed']);
                } catch (\Exception $updateException) {
                    \Log::error('Erro ao atualizar transação para failed', [
                        'error' => $updateException->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento. Motivo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cria um pagamento com cartão de crédito
     */
    public function createCreditCard(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'card_number' => 'required|string',
            'card_cvv' => 'required|string|max:4',
            'card_expiry' => 'required|string',
            'card_holder' => 'required|string|max:255',
            'payer_name' => 'required|string|max:255',
            'payer_email' => 'required|email',
            'payer_cpf' => 'required|string|max:14',
            'installments' => 'nullable|integer|min:1|max:12',
            'description' => 'nullable|string|max:500',
        ]);

        $user = $request->get('api_user');
        $gatewayConfig = SystemGatewayConfig::getActiveForCard();

        if (!$gatewayConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum gateway de cartão ativo configurado',
            ], 400);
        }

        // SEMPRE usa as taxas do painel
        $amountGross = floatval($request->amount);
        $cashinCardFixo = $user->getCashinCardFixo();
        $cashinCardPercentual = $user->getCashinCardPercentual();
        $cashinCardMinima = floatval(\App\Models\Setting::get('cashin_card_minima', '0.00'));
        
        // Calcula taxa percentual
        $feePercentual = ($amountGross * $cashinCardPercentual) / 100;
        // Aplica taxa mínima se necessário
        $fee = max($feePercentual, $cashinCardMinima) + $cashinCardFixo;
        $amountNet = $amountGross - $fee;
        
        // Arredonda para 2 casas decimais
        $amountGross = round($amountGross, 2);
        $fee = round($fee, 2);
        $amountNet = round($amountNet, 2);

        // Cria transação
        $transaction = Transaction::create([
            'uuid' => Str::uuid()->toString(),
            'user_id' => $user->id,
            'amount_gross' => $amountGross,
            'amount_net' => $amountNet,
            'fee' => $fee,
            'type' => 'credit',
            'status' => 'pending',
            'gateway_provider' => $gatewayConfig->provider_name,
            'description' => $request->description,
            'payer_name' => $request->payer_name,
            'payer_email' => $request->payer_email,
            'payer_cpf' => $request->payer_cpf,
        ]);

        try {
            $gateway = GatewayFactory::make($gatewayConfig->provider_name);
            $cardData = [
                'number' => $request->card_number,
                'cvv' => $request->card_cvv,
                'expiry' => $request->card_expiry,
                'holder' => $request->card_holder,
                'installments' => $request->installments ?? 1,
            ];

            $payerData = [
                'name' => $request->payer_name,
                'email' => $request->payer_email,
                'cpf' => $request->payer_cpf,
            ];

            $response = $gateway->createCreditCard($amountGross, $cardData, $payerData);

            $transaction->update([
                'external_id' => $response['external_id'] ?? null,
                'status' => $response['status'] ?? 'pending',
            ]);

            // Se aprovado, processa liberação baseado no tipo
            if ($response['status'] === 'completed' || $response['status'] === 'paid') {
                DB::transaction(function () use ($transaction, $user) {
                    $wallet = $user->wallet ?? Wallet::create([
                        'user_id' => $user->id,
                        'balance' => 0.00,
                        'frozen_balance' => 0.00,
                    ]);
                    
                    if ($transaction->type === 'pix') {
                        // PIX: credita imediatamente
                        $wallet->increment('balance', $transaction->amount_net);
                        $transaction->update([
                            'status' => 'completed',
                            'released_at' => now(),
                            'available_at' => now(),
                        ]);
                    } else {
                        // Cartão: NÃO credita direto, apenas agenda liberação após prazo
                        // O saldo vai para "Saldo a Liberar" e só será creditado após o prazo configurado
                        $cardReleaseDays = (int) \App\Models\Setting::get('card_release_days', 5);
                        $transaction->update([
                            'status' => 'completed',
                            'available_at' => now()->addDays($cardReleaseDays),
                            // released_at fica null até ser liberado pelo comando agendado
                        ]);
                        // NÃO credita na carteira ainda - fica em "Saldo a Liberar"
                    }
                    
                    // Processa splits automaticamente (apenas para PIX, pois cartão ainda não foi creditado)
                    if ($transaction->type === 'pix') {
                        try {
                            $splitService = new \App\Services\PaymentSplitService();
                            $splitService->processSplits($transaction);
                        } catch (\Exception $e) {
                            // Log do erro mas não interrompe o fluxo
                            \Log::error('Error processing splits for transaction: ' . $transaction->id, [
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                });
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_uuid' => $transaction->uuid,
                    'amount' => $amountGross,
                    'fee' => $fee,
                    'amount_net' => $amountNet,
                    'status' => $transaction->status,
                    'payment_id' => $response['payment_id'] ?? null,
                ],
            ], 201);

        } catch (\Exception $e) {
            // Log do erro
            \App\Services\ErrorLogService::log(
                'Erro ao processar pagamento Cartão via API',
                $e->getMessage(),
                'payment_error',
                'error',
                [
                    'transaction_id' => $transaction->id ?? null,
                    'gateway' => $gatewayConfig->provider_name ?? null,
                    'payment_type' => 'credit',
                    'user_id' => $user->id ?? null,
                ],
                $e
            );

            // Atualiza transação para failed se já foi criada
            if (isset($transaction) && $transaction->id) {
                $transaction->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento. Motivo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Busca informações de um pagamento
     */
    public function getPayment(string $uuid): JsonResponse
    {
        $user = request()->get('api_user');
        $transaction = Transaction::where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'uuid' => $transaction->uuid,
                'amount' => $transaction->amount_gross,
                'fee' => $transaction->fee,
                'amount_net' => $transaction->amount_net,
                'type' => $transaction->type,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at->toISOString(),
                'updated_at' => $transaction->updated_at->toISOString(),
            ],
        ]);
    }
}
