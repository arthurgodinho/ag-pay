<?php

namespace App\Services;

use App\Models\Withdrawal;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalService
{
    /**
     * Processa um saque automaticamente
     * Verifica saldo no gateway antes de processar
     *
     * @param Withdrawal $withdrawal
     * @return array ['success' => bool, 'message' => string, 'status' => string]
     */
    public function processWithdrawal(Withdrawal $withdrawal): array
    {
        if ($withdrawal->status !== 'pending') {
            return [
                'success' => false,
                'message' => 'Saque já foi processado',
                'status' => $withdrawal->status,
            ];
        }

        try {
            $result = DB::transaction(function () use ($withdrawal) {
                $user = $withdrawal->user;
                $wallet = $user->wallet;

                if (!$wallet) {
                    throw new \Exception('Carteira não encontrada');
                }

                // Tenta processar o saque via gateway configurado
                $gatewayConfig = \App\Models\SystemGatewayConfig::getDefaultForWithdrawals($user->preferred_gateway);
                
                if (!$gatewayConfig || empty($gatewayConfig->client_secret) || (!in_array(strtolower($gatewayConfig->provider_name), ['hypercash', 'zoompag', 'pagarme']) && empty($gatewayConfig->client_id))) {
                    // Se não há gateway configurado, marca como pendente para processamento manual
                    $withdrawal->update([
                        'status' => 'pending',
                    ]);

                    Log::info('Withdrawal aguardando processamento manual - Gateway não configurado', [
                        'withdrawal_id' => $withdrawal->id,
                    ]);

                    return [
                        'success' => false,
                        'message' => 'Nenhum adquirente configurado para saques. O saque será processado manualmente pelo administrador.',
                        'status' => 'pending',
                    ];
                }

                // Verifica se é BsPay, Venit, PodPay, HyperCash, Efi, PagueMax, ZoomPag ou Pluggou (suportam saques PIX)
                if (!in_array(strtolower($gatewayConfig->provider_name), ['bspay', 'venit', 'podpay', 'hypercash', 'efi', 'paguemax', 'zoompag', 'pluggou', 'pagarme'])) {
                    $withdrawal->update([
                        'status' => 'pending',
                    ]);

                    return [
                        'success' => false,
                        'message' => 'Adquirente não suporta saques PIX. O saque será processado manualmente pelo administrador.',
                        'status' => 'pending',
                    ];
                }

                $gateway = \App\Services\Gateways\GatewayFactory::make(
                    $gatewayConfig->provider_name,
                    $gatewayConfig->client_id,
                    $gatewayConfig->client_secret
                );

                // VERIFICA SALDO NO GATEWAY ANTES DE PROCESSAR
                $hasBalance = false;
                $balanceError = null;
                try {
                    if (method_exists($gateway, 'getBalance')) {
                        $balanceResponse = $gateway->getBalance();
                        
                        $balance = 0.00;
                        if (is_array($balanceResponse)) {
                            $balance = floatval($balanceResponse['balance'] ?? $balanceResponse['available'] ?? $balanceResponse['value'] ?? 0.00);
                        } else {
                            $balance = floatval($balanceResponse);
                        }
                        
                        Log::info('WithdrawalService: Saldo verificado no gateway', [
                            'gateway' => $gatewayConfig->provider_name,
                            'balance' => $balance,
                            'amount_needed' => $withdrawal->amount,
                        ]);

                        // Verifica se há saldo suficiente
                        if ($balance >= floatval($withdrawal->amount)) {
                            $hasBalance = true;
                        } else {
                            $balanceError = "Saldo insuficiente no adquirente. Saldo disponível: R$ " . number_format($balance, 2, ',', '.') . ". Valor necessário: R$ " . number_format($withdrawal->amount, 2, ',', '.');
                        }
                    } else {
                        // Se o gateway não suporta verificação de saldo, assume que tem saldo e tenta processar
                        // O erro será detectado ao tentar processar o pagamento
                        $hasBalance = true;
                        Log::warning('WithdrawalService: Gateway não suporta verificação de saldo', [
                            'gateway' => $gatewayConfig->provider_name,
                        ]);
                    }
                } catch (\Exception $e) {
                    // Se falhar a verificação, tenta processar mesmo assim
                    // O erro será detectado ao tentar processar o pagamento
                    $hasBalance = true;
                    Log::warning('WithdrawalService: Erro ao verificar saldo (continuando)', [
                        'gateway' => $gatewayConfig->provider_name,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Se não tem saldo, recusa o saque
                if (!$hasBalance && $balanceError) {
                    // Estorna o valor para o saldo disponível (não usa mais frozen_balance)
                    $wallet->increment('balance', $withdrawal->amount_gross);

                    $withdrawal->update([
                        'status' => 'rejected',
                        'admin_note' => $balanceError,
                    ]);

                    Log::warning('WithdrawalService: Saque recusado - Saldo insuficiente no gateway', [
                        'withdrawal_id' => $withdrawal->id,
                        'balance_error' => $balanceError,
                    ]);
                    
                    // Cria notificação de falha para o usuário
                    try {
                        \App\Models\Notification::create([
                            'title' => 'Saque Não Realizado',
                            'message' => 'Seu saque não pôde ser processado. Entre em contato com o seu gerente.',
                            'type' => 'error',
                            'is_active' => true,
                        ]);
                        
                        $notification = \App\Models\Notification::latest()->first();
                        \App\Models\UserNotification::create([
                            'user_id' => $user->id,
                            'notification_id' => $notification->id,
                            'is_read' => false,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Erro ao criar notificação de saque recusado', [
                            'user_id' => $user->id,
                            'withdrawal_id' => $withdrawal->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    return [
                        'success' => false,
                        'message' => 'Seu Saque foi Recusado, Entre em contato com o Administrador.',
                        'status' => 'rejected',
                        'reason' => $balanceError,
                    ];
                }

                // Se tem saldo, processa o saque
                $pixKey = $withdrawal->pix_key;
                
                // Determina a rota de webhook correta baseada no gateway
                $postbackRoute = 'api.webhooks.bspay'; // Default
                if (strtolower($gatewayConfig->provider_name) === 'venit') {
                    $postbackRoute = 'api.webhooks.venit';
                } elseif (strtolower($gatewayConfig->provider_name) === 'podpay') {
                    $postbackRoute = 'api.webhooks.podpay';
                } elseif (strtolower($gatewayConfig->provider_name) === 'hypercash') {
                    $postbackRoute = 'api.webhooks.hypercash';
                } elseif (strtolower($gatewayConfig->provider_name) === 'efi') {
                    $postbackRoute = 'api.webhooks.efi';
                } elseif (strtolower($gatewayConfig->provider_name) === 'paguemax') {
                    $postbackRoute = 'api.webhooks.paguemax';
                }
                
                $postbackUrl = \App\Helpers\WebhookUrlHelper::generateUrl($postbackRoute);
                
                $keyType = 'CPF'; // Padrão
                
                // Tenta identificar o tipo de chave
                if (filter_var($pixKey, FILTER_VALIDATE_EMAIL)) {
                    $keyType = 'EMAIL';
                } elseif (preg_match('/^\+55\d{10,11}$/', $pixKey)) {
                    $keyType = 'PHONE';
                } elseif (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $pixKey)) {
                    $keyType = 'EVP';
                } elseif (preg_match('/^\d{11}$/', $pixKey)) {
                    $keyType = 'CPF';
                } elseif (preg_match('/^\d{14}$/', $pixKey)) {
                    $keyType = 'CNPJ';
                }

                $recipientData = [
                    'pix_key' => $pixKey,
                    'key_type' => $keyType,
                    'name' => $user->name,
                    'document' => $user->cpf_cnpj ?? '',
                    'postback_url' => $postbackUrl,
                ];

                $externalId = \Illuminate\Support\Str::uuid()->toString();
                
                try {
                    $response = $gateway->createPixPayment(
                        $withdrawal->amount, // Valor líquido a ser enviado
                        $recipientData,
                        $externalId,
                        "Saque #{$withdrawal->id}"
                    );

                    // Verifica se houve erro na resposta
                    $responseStatus = strtolower($response['status'] ?? 'processing');
                    if (isset($response['error']) || in_array($responseStatus, ['error', 'failed', 'rejected', 'cancelled'])) {
                        $errorMessage = $response['message'] ?? $response['error'] ?? $response['error_message'] ?? 'Erro desconhecido ao processar pagamento';
                        
                        // Verifica se é erro de saldo insuficiente
                        $errorMessageLower = strtolower($errorMessage);
                        if (stripos($errorMessageLower, 'saldo') !== false || 
                            stripos($errorMessageLower, 'balance') !== false || 
                            stripos($errorMessageLower, 'insufficient') !== false ||
                            stripos($errorMessageLower, 'funds') !== false ||
                            stripos($errorMessageLower, 'sem saldo') !== false) {
                            
                            // Estorna o valor para o saldo disponível
                            $wallet->increment('balance', $withdrawal->amount_gross);

                            $withdrawal->update([
                                'status' => 'rejected',
                                'admin_note' => 'Saldo insuficiente no adquirente: ' . $errorMessage,
                            ]);
                            
                            // Cria notificação de falha para o usuário
                            try {
                                \App\Models\Notification::create([
                                    'title' => 'Saque Não Realizado',
                                    'message' => 'Seu saque não pôde ser processado. Entre em contato com o seu gerente.',
                                    'type' => 'error',
                                    'is_active' => true,
                                ]);
                                
                                $notification = \App\Models\Notification::latest()->first();
                                \App\Models\UserNotification::create([
                                    'user_id' => $user->id,
                                    'notification_id' => $notification->id,
                                    'is_read' => false,
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Erro ao criar notificação de saque recusado (saldo insuficiente)', [
                                    'user_id' => $user->id,
                                    'withdrawal_id' => $withdrawal->id,
                                    'error' => $e->getMessage(),
                                ]);
                            }

                            return [
                                'success' => false,
                                'message' => 'Seu Saque foi Recusado, Entre em contato com o Administrador.',
                                'status' => 'rejected',
                                'reason' => 'Saldo insuficiente no adquirente',
                            ];
                        }
                        
                        throw new \Exception('Erro ao processar pagamento no adquirente: ' . $errorMessage);
                    }

                    // Atualiza o saque com o external_id do gateway
                    $finalExternalId = $response['external_id'] ?? $externalId;
                    
                    // Verifica se o pagamento foi confirmado imediatamente
                    $responseStatus = strtolower($response['status'] ?? 'processing');
                    $isImmediatelyPaid = in_array($responseStatus, ['paid', 'completed', 'approved', 'confirmed', 'success']);
                    
                    if ($isImmediatelyPaid) {
                        // Pagamento confirmado imediatamente - nada mais a fazer no saldo pois já saiu do balance no início
                        Log::info('WithdrawalService: Saque pago (confirmação imediata)', [
                            'user_id' => $user->id,
                            'withdrawal_id' => $withdrawal->id,
                            'amount_gross' => $withdrawal->amount_gross,
                        ]);
                        
                        $withdrawal->update([
                            'status' => 'paid',
                            'external_id' => $finalExternalId,
                            'processed_at' => now(),
                        ]);
                    } else {
                        // Aguarda confirmação do webhook
                        $withdrawal->update([
                            'status' => 'processing',
                            'external_id' => $finalExternalId,
                            'processed_at' => now(),
                        ]);
                    }

                    Log::info('WithdrawalService: Saque processado com sucesso via gateway', [
                        'gateway' => $gatewayConfig->provider_name,
                        'withdrawal_id' => $withdrawal->id,
                        'external_id' => $finalExternalId,
                        'transaction_id' => $response['transaction_id'] ?? null,
                        'status' => $isImmediatelyPaid ? 'paid' : 'processing',
                    ]);

                    // O status será atualizado para 'paid' quando o webhook confirmar (se não foi confirmado imediatamente)
                    return [
                        'success' => true,
                        'message' => $isImmediatelyPaid ? 'Seu Saque foi Processado e Confirmado com sucesso.' : 'Seu Saque foi Processado com sucesso.',
                        'status' => $isImmediatelyPaid ? 'paid' : 'processing',
                    ];
                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $errorMessageLower = strtolower($errorMessage);
                    
                    // Verifica se é erro de saldo
                    if (stripos($errorMessageLower, 'saldo') !== false || 
                        stripos($errorMessageLower, 'balance') !== false || 
                        stripos($errorMessageLower, 'insufficient') !== false ||
                        stripos($errorMessageLower, 'funds') !== false ||
                        stripos($errorMessageLower, 'sem saldo') !== false) {
                        
                        // Estorna o valor para o saldo disponível
                        $wallet->increment('balance', $withdrawal->amount_gross);

                        $withdrawal->update([
                            'status' => 'rejected',
                            'admin_note' => 'Saldo insuficiente no adquirente: ' . $errorMessage,
                        ]);
                        
                        // Cria notificação de falha para o usuário
                        try {
                            \App\Models\Notification::create([
                                'title' => 'Saque Não Realizado',
                                'message' => 'Seu saque não pôde ser processado. Entre em contato com o seu gerente.',
                                'type' => 'error',
                                'is_active' => true,
                            ]);
                            
                            $notification = \App\Models\Notification::latest()->first();
                            \App\Models\UserNotification::create([
                                'user_id' => $user->id,
                                'notification_id' => $notification->id,
                                'is_read' => false,
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Erro ao criar notificação de saque recusado (catch)', [
                                'user_id' => $user->id,
                                'withdrawal_id' => $withdrawal->id,
                                'error' => $e->getMessage(),
                            ]);
                        }

                        return [
                            'success' => false,
                            'message' => 'Seu Saque foi Recusado, Entre em contato com o Administrador.',
                            'status' => 'rejected',
                            'reason' => 'Saldo insuficiente no adquirente',
                        ];
                    }
                    
                    // Para outros erros, mantém como pending para processamento manual
                    $withdrawal->update([
                        'status' => 'pending',
                        'admin_note' => 'Erro ao processar: ' . $errorMessage,
                    ]);

                    Log::error('WithdrawalService: Erro ao processar saque via gateway', [
                        'withdrawal_id' => $withdrawal->id,
                        'gateway' => $gatewayConfig->provider_name,
                        'error' => $errorMessage,
                    ]);

                    throw $e;
                }
            });

            return $result;
        } catch (\Exception $e) {
            Log::error('WithdrawalService: Erro ao processar saque automaticamente', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);

            // Atualiza status para failed apenas se não foi definido no processamento
            if ($withdrawal->status === 'pending') {
                $withdrawal->update([
                    'status' => 'failed',
                    'admin_note' => 'Erro ao processar: ' . $e->getMessage(),
                ]);
            }

            return [
                'success' => false,
                'message' => 'Erro ao processar saque. O saque será processado manualmente pelo administrador.',
                'status' => $withdrawal->status,
            ];
        }
    }
}

