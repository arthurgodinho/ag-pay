<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Models\SystemGatewayConfig;
use App\Services\Gateways\GatewayFactory;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminWithdrawalController extends Controller
{
    /**
     * Lista saques
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Withdrawal::with('user');

        // Filtro por status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $withdrawals = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    /**
     * Paga o saque (verifica saldo no adquirente e faz débito real)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function pay(int $id): RedirectResponse
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Este saque já foi processado.');
        }

        $user = $withdrawal->user;
        $amountToPay = $withdrawal->amount ?? ($withdrawal->amount_gross - ($withdrawal->fee ?? 0)); // Valor líquido a pagar
        $amountGross = $withdrawal->amount_gross ?? $withdrawal->amount; // Valor bruto para desbloquear do frozen

        $isImmediatelyPaid = false;

        try {
            DB::transaction(function () use ($withdrawal, $user, $amountToPay, $amountGross, &$isImmediatelyPaid) {
                // 1. Obtém o gateway padrão configurado para Saques PIX
                $gatewayConfig = SystemGatewayConfig::getDefaultForWithdrawals($user->preferred_gateway);

                if (!$gatewayConfig || empty($gatewayConfig->client_secret) || (!in_array(strtolower($gatewayConfig->provider_name), ['hypercash', 'zoompag']) && empty($gatewayConfig->client_id))) {
                    throw new \Exception('Nenhum adquirente configurado para saques. Configure um adquirente padrão para saques no painel administrativo em Adquirentes.');
                }

                // 2. Cria instância do gateway
                $gateway = GatewayFactory::make(
                    $gatewayConfig->provider_name,
                    $gatewayConfig->client_id,
                    $gatewayConfig->client_secret
                );

                // 3. Verifica saldo disponível no adquirente (opcional - se falhar, continua sem verificar)
                $balanceChecked = false;
                try {
                    if (method_exists($gateway, 'getBalance')) {
                        $balanceResponse = $gateway->getBalance();
                        
                        // getBalance pode retornar array ou float, trata ambos
                        if (is_array($balanceResponse)) {
                            $balance = floatval($balanceResponse['balance'] ?? $balanceResponse['available'] ?? $balanceResponse['value'] ?? 0.00);
                        } else {
                            $balance = floatval($balanceResponse);
                        }
                        
                        if ($balance <= 0) {
                            throw new \Exception('Saldo não disponível ou inválido no adquirente.');
                        }

                        Log::info('Saldo verificado no adquirente', [
                            'gateway' => $gatewayConfig->provider_name,
                            'balance' => $balance,
                            'amount_needed' => $amountToPay,
                        ]);

                        // Verifica se há saldo suficiente
                        if ($balance < floatval($amountToPay)) {
                            throw new \Exception("Saldo insuficiente no adquirente. Saldo disponível: R$ " . number_format($balance, 2, ',', '.') . ". Valor necessário para o saque: R$ " . number_format($amountToPay, 2, ',', '.') . ". Recarregue o saldo no adquirente antes de processar este saque.");
                        }
                        
                        $balanceChecked = true;
                    } else {
                        Log::warning('Gateway não suporta verificação de saldo', [
                            'gateway' => $gatewayConfig->provider_name,
                        ]);
                    }
                } catch (\Exception $e) {
                    // Se for erro 405 (Method Not Allowed) ou endpoint não disponível, apenas loga e continua
                    // O erro de saldo será detectado quando tentar processar o pagamento
                    if (stripos($e->getMessage(), '405') !== false || 
                        stripos($e->getMessage(), 'method not allowed') !== false ||
                        stripos($e->getMessage(), 'not allowed') !== false) {
                        Log::warning('Verificação de saldo não disponível no gateway (endpoint não suportado)', [
                            'gateway' => $gatewayConfig->provider_name,
                            'error' => $e->getMessage(),
                        ]);
                        // Continua sem verificar saldo - o erro será detectado ao processar o pagamento
                    } else {
                        // Para outros erros, loga mas também continua (o erro será detectado ao processar)
                        Log::warning('Erro ao verificar saldo no adquirente (continuando sem verificação)', [
                            'gateway' => $gatewayConfig->provider_name,
                            'error' => $e->getMessage(),
                        ]);
                    }
                    // Não lança exceção - permite que o processo continue
                    // O erro de saldo será detectado quando tentar processar o pagamento
                }

                // 4. Processa o pagamento PIX via gateway
                $pixKey = $withdrawal->pix_key;
                if (empty($pixKey)) {
                    throw new \Exception('Chave PIX não informada no saque.');
                }

                // Determina o tipo de chave PIX
                $keyType = 'CPF'; // Padrão
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

                $externalId = \Illuminate\Support\Str::uuid()->toString();
                // Usa helper para garantir URL absoluta com domínio do .env
                $postbackUrl = \App\Helpers\WebhookUrlHelper::generateUrl('api.webhooks.bspay');

                $recipientData = [
                    'pix_key' => $pixKey,
                    'key_type' => $keyType,
                    'name' => $user->name,
                    'document' => $user->cpf_cnpj ?? '',
                    'postback_url' => $postbackUrl,
                ];

                // Verifica se o gateway suporta saques PIX
                if (!method_exists($gateway, 'createPixPayment')) {
                    throw new \Exception('O adquirente ' . ucfirst($gatewayConfig->provider_name) . ' não suporta saques PIX no momento.');
                }

                // 5. Tenta fazer o débito no adquirente
                $paymentResponse = $gateway->createPixPayment(
                    $amountToPay,
                    $recipientData,
                    $externalId,
                    "Saque #{$withdrawal->id} - {$user->name}"
                );

                Log::info('Tentativa de pagamento PIX iniciada no adquirente', [
                    'withdrawal_id' => $withdrawal->id,
                    'gateway' => $gatewayConfig->provider_name,
                    'external_id' => $externalId,
                    'amount' => $amountToPay,
                    'response' => $paymentResponse,
                ]);

                // Verifica se o pagamento foi iniciado com sucesso
                $responseStatus = strtolower($paymentResponse['status'] ?? 'processing');
                
                if (isset($paymentResponse['error']) || in_array($responseStatus, ['error', 'failed', 'rejected', 'cancelled'])) {
                    $errorMessage = $paymentResponse['message'] ?? $paymentResponse['error'] ?? $paymentResponse['error_message'] ?? 'Erro desconhecido ao processar pagamento';
                    
                    // Verifica se é erro de saldo insuficiente no adquirente
                    $errorMessageLower = strtolower($errorMessage);
                    if (stripos($errorMessageLower, 'saldo') !== false || 
                        stripos($errorMessageLower, 'balance') !== false || 
                        stripos($errorMessageLower, 'insufficient') !== false ||
                        stripos($errorMessageLower, 'funds') !== false ||
                        stripos($errorMessageLower, 'sem saldo') !== false) {
                        throw new \Exception('Saldo insuficiente no adquirente. Saldo disponível insuficiente para processar o saque de R$ ' . number_format($amountToPay, 2, ',', '.'));
                    }
                    
                    throw new \Exception('Erro ao processar pagamento no adquirente: ' . $errorMessage);
                }

                // 6. Verifica se o pagamento foi confirmado imediatamente ou se precisa aguardar webhook
                $isImmediatelyPaid = in_array($responseStatus, ['paid', 'completed', 'approved', 'confirmed', 'success']);
                
                if ($isImmediatelyPaid) {
                    // Pagamento confirmado imediatamente - marca como paid
                    // Não removemos do frozen_balance pois o saldo não é movido para lá na solicitação
                    $withdrawal->update([
                        'status' => 'paid',
                        'external_id' => $paymentResponse['external_id'] ?? $externalId,
                        'processed_at' => now(),
                    ]);

                    // Marca primeiro saque como completado (permite saques automáticos futuros)
                    if (!$user->first_withdrawal_completed) {
                        $user->update(['first_withdrawal_completed' => true]);
                        Log::info('Primeiro saque completado - Usuário pode fazer saques automáticos', [
                            'user_id' => $user->id,
                            'withdrawal_id' => $withdrawal->id,
                        ]);
                    }

                    Log::info('Saque pago e confirmado imediatamente via adquirente', [
                        'withdrawal_id' => $withdrawal->id,
                        'user_id' => $user->id,
                        'gateway' => $gatewayConfig->provider_name,
                        'external_id' => $paymentResponse['external_id'] ?? $externalId,
                        'amount' => $amountToPay,
                    ]);
                    
                    // Cria notificação de sucesso para o usuário
                    try {
                        \App\Models\Notification::create([
                            'title' => 'Saque Realizado com Sucesso',
                            'message' => "Seu saque de R$ " . number_format($withdrawal->amount, 2, ',', '.') . " foi processado com sucesso e está sendo enviado para sua conta.",
                            'type' => 'success',
                            'is_active' => true,
                        ]);
                        
                        $notification = \App\Models\Notification::latest()->first();
                        \App\Models\UserNotification::create([
                            'user_id' => $user->id,
                            'notification_id' => $notification->id,
                            'is_read' => false,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Erro ao criar notificação de saque processado (admin)', [
                            'user_id' => $user->id,
                            'withdrawal_id' => $withdrawal->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                } else {
                    // Pagamento iniciado mas precisa aguardar confirmação do webhook
                    // Marca como 'processing' - o frozen_balance será removido quando o webhook confirmar
                    $withdrawal->update([
                        'status' => 'processing',
                        'external_id' => $paymentResponse['external_id'] ?? $externalId,
                        'processed_at' => now(),
                    ]);

                    // Marca primeiro saque como completado (permite saques automáticos futuros)
                    if (!$user->first_withdrawal_completed) {
                        $user->update(['first_withdrawal_completed' => true]);
                        Log::info('Primeiro saque processado - Usuário pode fazer saques automáticos', [
                            'user_id' => $user->id,
                            'withdrawal_id' => $withdrawal->id,
                        ]);
                    }

                    Log::info('Saque processado via adquirente - Aguardando confirmação do webhook', [
                        'withdrawal_id' => $withdrawal->id,
                        'user_id' => $user->id,
                        'gateway' => $gatewayConfig->provider_name,
                        'external_id' => $paymentResponse['external_id'] ?? $externalId,
                        'amount' => $amountToPay,
                    ]);
                }

                // NOTA: Se não foi confirmado imediatamente, o frozen_balance será removido quando o webhook confirmar o pagamento
                // Isso garante que só removemos o saldo bloqueado quando o pagamento realmente for confirmado pelo adquirente
            });

            // Mensagem de sucesso baseada no status
            if ($isImmediatelyPaid) {
                return back()->with('success', '✅ Saque pago e confirmado com sucesso! O valor foi debitado do adquirente e será enviado para o cliente.');
            } else {
                return back()->with('success', '✅ Saque processado com sucesso! O pagamento foi iniciado no adquirente. O status será atualizado quando a confirmação do pagamento for recebida.');
            }

        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento de saque', [
                'withdrawal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Registra no sistema de logs de erro
            ErrorLogService::logWithdrawalError(
                $e->getMessage(),
                null,
                [
                    'withdrawal_id' => $id,
                    'user_id' => $withdrawal->user->id ?? null,
                    'amount' => $withdrawal->amount,
                    'amount_gross' => $withdrawal->amount_gross,
                    'pix_key' => $withdrawal->pix_key,
                ],
                $e
            );

            // Identifica tipo de erro para mensagem mais específica
            $errorMessage = $e->getMessage();
            $errorMessageLower = strtolower($errorMessage);
            $userFriendlyMessage = 'Erro ao processar o saque.';

            // Verifica se é erro de saldo insuficiente
            if (stripos($errorMessageLower, 'saldo insuficiente') !== false || 
                stripos($errorMessageLower, 'saldo não disponível') !== false ||
                stripos($errorMessageLower, 'insufficient balance') !== false ||
                stripos($errorMessageLower, 'insufficient funds') !== false ||
                (stripos($errorMessageLower, 'balance') !== false && stripos($errorMessageLower, 'insufficient') !== false) ||
                stripos($errorMessageLower, 'sem saldo') !== false) {
                $userFriendlyMessage = '❌ Não foi possível processar o saque: Saldo insuficiente no adquirente configurado para saques. Verifique o saldo disponível no adquirente antes de processar o saque.';
            } elseif (stripos($errorMessageLower, 'adquirente configurado') !== false || 
                     stripos($errorMessageLower, 'gateway não configurado') !== false ||
                     stripos($errorMessageLower, 'nenhum adquirente') !== false) {
                $userFriendlyMessage = '⚠️ Não foi possível processar o saque: Nenhum adquirente configurado para saques. Configure um adquirente padrão para saques no painel administrativo em Adquirentes.';
            } elseif (stripos($errorMessageLower, 'api') !== false || 
                     stripos($errorMessageLower, 'connection') !== false ||
                     stripos($errorMessageLower, 'timeout') !== false ||
                     stripos($errorMessageLower, 'network') !== false ||
                     stripos($errorMessageLower, 'connection refused') !== false) {
                $userFriendlyMessage = '🔌 Erro de comunicação com o adquirente. Tente novamente em alguns instantes. Se o problema persistir, verifique as credenciais do adquirente e a conexão com a API.';
            } elseif (stripos($errorMessageLower, 'token') !== false || 
                     stripos($errorMessageLower, 'auth') !== false ||
                     stripos($errorMessageLower, 'unauthorized') !== false ||
                     stripos($errorMessageLower, 'credential') !== false) {
                $userFriendlyMessage = '🔑 Erro de autenticação com o adquirente. Verifique se as credenciais (Client ID e Client Secret) estão corretas no painel administrativo.';
            } elseif (stripos($errorMessageLower, 'não suporta saques') !== false || 
                     stripos($errorMessageLower, 'not support') !== false) {
                $userFriendlyMessage = '⚠️ O adquirente selecionado não suporta saques PIX. Configure outro adquirente que suporte saques no painel administrativo.';
            } else {
                $userFriendlyMessage = '⚠️ Erro ao processar o saque: ' . $errorMessage . '. Verifique os logs do sistema para mais detalhes.';
            }

            return back()->with('error', $userFriendlyMessage);
        }
    }

    /**
     * Reembolsa o saque (estorna saldo para o usuário)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function refund(int $id): RedirectResponse
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Este saque já foi processado.');
        }

        // Estorna o valor bruto para a carteira (desbloqueia o frozen_balance)
        $amountToRefund = $withdrawal->amount_gross ?? $withdrawal->amount;
        $wallet = $withdrawal->user->wallet;
        if ($wallet) {
            // Devolve para balance (não mexe em frozen_balance pois não foi usado)
            $wallet->increment('balance', $amountToRefund);
        }

        // Atualiza status
        $withdrawal->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Saque reembolsado e valor estornado para o usuário!');
    }

    /**
     * Cancela o saque (sem estornar)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function cancel(int $id): RedirectResponse
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Este saque já foi processado.');
        }

        // Apenas cancela, sem estornar o valor
        // O valor permanece bloqueado (frozen_balance)
        $withdrawal->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Saque cancelado!');
    }

    /**
     * Aprova saque (mantido para compatibilidade)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function approve(int $id): RedirectResponse
    {
        return $this->pay($id);
    }

    /**
     * Rejeita saque (mantido para compatibilidade)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function reject(int $id): RedirectResponse
    {
        return $this->refund($id);
    }

    /**
     * Pagamento automático (mantido para compatibilidade)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function autoPay(int $id): RedirectResponse
    {
        return $this->pay($id);
    }
}
