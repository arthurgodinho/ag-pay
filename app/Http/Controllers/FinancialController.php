<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class FinancialController extends Controller
{
    /**
     * Exibe a página financeiro
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        // IMPORTANTE: Quando um saque é solicitado:
        // - balance é decrementado (valor bruto é removido do saldo disponível)
        // - frozen_balance é incrementado (valor bruto é bloqueado)
        // 
        // Saldo disponível = balance (já não inclui valores bloqueados)
        // Saldo pendente (Aguardando Aprovação) = apenas saques manuais pendentes (status = 'pending')
        // Saques automáticos não aparecem em "Aguardando Aprovação", apenas debitam de "Disponível para Saque"
        
        $availableBalance = $wallet ? max(0, $wallet->balance) : 0.00;
        
        // Calcula apenas saques manuais pendentes (status = 'pending')
        // Saques automáticos ficam com status 'processing' ou 'paid' e não aparecem aqui
        $pendingBalance = Withdrawal::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount_gross');
        
        // Saldo a Liberar (transações de cartão de crédito que ainda não foram liberadas)
        $balanceToRelease = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('type', 'credit')
            ->whereNull('released_at')
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNotNull('available_at')
                      ->where('available_at', '>', now());
                })->orWhere(function($q) {
                    $q->whereNull('available_at');
                });
            })
            ->sum('amount_net');
        
        // Taxa de antecipação
        $advanceFeePercentage = floatval(Setting::get('advance_fee_percentage', '38.00'));

        // Taxas PIX do usuário ou padrão do sistema (usando as novas taxas detalhadas)
        // SEMPRE usa as taxas do painel
        $cashinPixFixo = $user->getCashinPixFixo();
        $cashinPixPercentual = $user->getCashinPixPercentual();
        $cashoutPixFixo = $user->getCashoutPixFixo();
        $cashoutPixPercentual = $user->getCashoutPixPercentual();
        
        // Taxas mínimas
        $cashinPixMinima = floatval(Setting::get('cashin_pix_minima', '0.80'));
        $cashoutPixMinima = floatval(Setting::get('cashout_pix_minima', '0.80'));
        
        // Para compatibilidade, mantemos as variáveis antigas
        $cashinFixo = $cashinPixFixo;
        $cashinPercentual = $cashinPixPercentual;
        $cashoutFixo = $cashoutPixFixo;
        $cashoutPercentual = $cashoutPixPercentual;
        
        // Para exibição
        $cashinFee = $cashinPercentual;
        $cashoutFee = $cashoutPercentual;
        
        // Valor mínimo de depósito
        $depositMinValue = floatval(Setting::get('deposit_min_value', '5.00'));
        
        // Valor mínimo de saque
        $withdrawalMinValue = floatval(Setting::get('withdrawal_min_value', '10.00'));
        
        // Identifica se é PF ou PJ para buscar limites de saque diário
        $documentType = \App\Helpers\DocumentHelper::getDocumentType($user->cpf_cnpj ?? '');
        $withdrawalsPerDay = $documentType === 'cnpj' 
            ? Setting::get('withdrawals_per_day_pj', '3')
            : Setting::get('withdrawals_per_day_pf', '3');

        // Últimos depósitos (transações que creditam na carteira)
        $recentDeposits = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['pix', 'credit'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Últimos saques
        $recentWithdrawals = Withdrawal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Verifica se o usuário pode sacar
        $canWithdraw = !$user->bloquear_saque;

        return view('dashboard.financial.index', compact(
            'availableBalance',
            'pendingBalance',
            'withdrawalMinValue',
            'depositMinValue',
            'cashoutPixMinima',
            'cashinFee',
            'cashoutFee',
            'cashinFixo',
            'cashinPercentual',
            'cashoutFixo',
            'cashoutPercentual',
            'depositMinValue',
            'withdrawalsPerDay',
            'recentDeposits',
            'recentWithdrawals',
            'canWithdraw',
            'balanceToRelease',
            'advanceFeePercentage'
        ));
    }

    /**
     * Verifica status de uma transação de depósito
     *
     * @param string $transactionUuid
     * @return JsonResponse
     */
    public function checkTransactionStatus(string $transactionUuid): JsonResponse
    {
        $user = Auth::user();
        
        $transaction = Transaction::where('uuid', $transactionUuid)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $isCompleted = $transaction->status === 'completed';
        
        $redirectUrl = null;
        $message = null;
        
        if ($isCompleted) {
            $redirectUrl = route('dashboard.financial.index');
            $message = 'Depósito confirmado com sucesso! Redirecionando para o dashboard...';
        }
        
        return response()->json([
            'status' => $transaction->status,
            'completed' => $isCompleted,
            'redirect_url' => $redirectUrl,
            'message' => $message,
            'amount_gross' => $transaction->amount_gross,
            'amount_net' => $transaction->amount_net,
            'type' => $transaction->type,
        ]);
    }

    /**
     * Gera QR Code para depósito
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateDepositQr(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Valida valor mínimo de depósito
        $depositMinValue = floatval(Setting::get('deposit_min_value', '5.00'));
        
        $request->validate([
            'payer_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:' . $depositMinValue],
        ], [
            'payer_name.required' => 'Por favor, informe o nome completo.',
            'amount.min' => "O valor mínimo para depósito é R$ " . number_format($depositMinValue, 2, ',', '.'),
        ]);

        $amountGross = floatval($request->amount);

        // Calcula taxa de cashin PIX (fixo + percentual) - usando as novas taxas detalhadas
        // SEMPRE usa as taxas do painel
        $cashinPixFixo = $user->getCashinPixFixo();
        $cashinPixPercentual = $user->getCashinPixPercentual();
        $cashinPixMinima = floatval(Setting::get('cashin_pix_minima', '0.00'));
        
        // Calcula taxa percentual
        $feePercentual = ($amountGross * $cashinPixPercentual) / 100;
        // Aplica taxa mínima se necessário
        $fee = max($feePercentual, $cashinPixMinima) + $cashinPixFixo;
        $amountNet = $amountGross - $fee;

        // Arredonda para 2 casas decimais
        $amountGross = round($amountGross, 2);
        $fee = round($fee, 2);
        $amountNet = round($amountNet, 2);

        // Gera UUID único para esta transação
        $transactionId = Str::uuid();

        // Data de expiração (5 minutos)
        $expiresAt = now()->addMinutes(5);

        // Tenta usar o gateway configurado para Cash-in PIX especificamente
        // Usa o método getDefaultForCashinPix que busca primeiro o gateway específico configurado
        $userPreferredGateway = $user->preferred_gateway;
        $gatewayConfig = \App\Models\SystemGatewayConfig::getDefaultForCashinPix($userPreferredGateway);
        
        // Log para debug
        \Log::info('FinancialController: Gateway selecionado para depósito PIX', [
            'gateway_encontrado' => $gatewayConfig ? $gatewayConfig->provider_name : null,
            'gateway_id' => $gatewayConfig ? $gatewayConfig->id : null,
            'has_credentials' => $gatewayConfig ? (!empty($gatewayConfig->client_id) && !empty($gatewayConfig->client_secret)) : false,
            'user_preferred' => $userPreferredGateway,
            'setting_cashin' => Setting::get('default_gateway_for_cashin_pix', 'NOT_SET'),
        ]);
        
        if (!$gatewayConfig) {
            \Log::error('FinancialController: Nenhum gateway PIX configurado', [
                'user_preferred' => $userPreferredGateway,
                'setting_cashin' => Setting::get('default_gateway_for_cashin_pix'),
                'setting_pix' => Setting::get('default_gateway_for_pix'),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Nenhum gateway PIX configurado. Por favor, configure um gateway no painel administrativo.',
            ], 400);
        }

        // Validação de credenciais (HyperCash e ZoomPag não precisam de client_id)
        $isClientSecretRequired = true;
        $isClientIdRequired = !in_array($gatewayConfig->provider_name, ['hypercash', 'zoompag']);

        if (($isClientIdRequired && empty($gatewayConfig->client_id)) || ($isClientSecretRequired && empty($gatewayConfig->client_secret))) {
            \Log::error('FinancialController: Credenciais incompletas', [
                'provider' => $gatewayConfig->provider_name,
                'has_client_id' => !empty($gatewayConfig->client_id),
                'has_client_secret' => !empty($gatewayConfig->client_secret),
                'is_client_id_required' => $isClientIdRequired,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Keys de pagamentos não configuradas. As credenciais do gateway não estão configuradas. Por favor, configure as credenciais no painel administrativo.',
            ], 400);
        }

        try {
            // Log das credenciais antes de criar o gateway (sem mostrar valores sensíveis completos)
            \Log::info('FinancialController: Criando gateway', [
                'gateway_name' => $gatewayConfig->provider_name,
                'has_client_id' => !empty($gatewayConfig->client_id),
                'client_id_length' => $gatewayConfig->client_id ? strlen($gatewayConfig->client_id) : 0,
                'client_id_prefix' => $gatewayConfig->client_id ? substr($gatewayConfig->client_id, 0, 8) . '...' : null,
                'has_client_secret' => !empty($gatewayConfig->client_secret),
                'client_secret_length' => $gatewayConfig->client_secret ? strlen($gatewayConfig->client_secret) : 0,
                'client_secret_prefix' => $gatewayConfig->client_secret ? substr($gatewayConfig->client_secret, 0, 8) . '...' : null,
            ]);

            $gateway = \App\Services\Gateways\GatewayFactory::make(
                $gatewayConfig->provider_name,
                $gatewayConfig->client_id,
                $gatewayConfig->client_secret
            );

            $externalIdForGateway = $transactionId->toString();
            
            // Determina o postback URL baseado no gateway
            $postbackRoute = 'api.webhooks.bspay'; // Default
            if ($gatewayConfig->provider_name === 'venit') {
                $postbackRoute = 'api.webhooks.venit';
            }

            // IMPORTANTE: Os dados enviados para o adquirente devem ser os do USUÁRIO DO GATEWAY (usuário logado),
            // que é quem está fazendo o depósito manual.
            
            // Valida que o usuário tem os dados obrigatórios
            if (empty($user->cpf_cnpj)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro: Você não possui CPF/CNPJ cadastrado. É necessário completar o cadastro antes de fazer um depósito.',
                ], 400);
            }
            
            $payerData = [
                'name' => $user->name, // Nome do usuário logado (usuário do gateway)
                'email' => $user->email, // Email do usuário logado
                'cpf' => $user->cpf_cnpj, // CPF/CNPJ do usuário logado (obrigatório)
                'phone' => $user->phone ?? '',
                'external_id' => $externalIdForGateway,
                'postback_url' => \App\Helpers\WebhookUrlHelper::generateUrl($postbackRoute), // URL absoluta com domínio do .env
                'description' => 'Depósito via PIX',
            ];

            // Tenta criar o QR Code PIX via gateway ANTES de criar a transação
            try {
                $response = $gateway->createPix($amountGross, $payerData);
            } catch (\Exception $gatewayException) {
                // Garante que a mensagem de erro é sempre string
                $errorMsg = $gatewayException->getMessage();
                if (!is_string($errorMsg)) {
                    if (is_array($errorMsg)) {
                        $errorMsg = json_encode($errorMsg, JSON_UNESCAPED_UNICODE);
                    } elseif (is_scalar($errorMsg)) {
                        $errorMsg = (string) $errorMsg;
                    } else {
                        $errorMsg = 'Erro desconhecido ao comunicar com o gateway';
                    }
                }
                
                \Log::error('FinancialController: Erro ao chamar gateway->createPix()', [
                    'error' => $errorMsg,
                    'error_type' => gettype($gatewayException->getMessage()),
                    'trace' => $gatewayException->getTraceAsString(),
                    'gateway' => $gatewayConfig->provider_name,
                    'amount' => $amountGross,
                ]);
                
                // Mensagem de erro amigável
                $userMessage = 'Erro ao comunicar com o gateway. ';
                if (str_contains($errorMsg, 'Array to string')) {
                    $userMessage .= 'Erro interno ao processar dados. Por favor, tente novamente ou entre em contato com o suporte.';
                } else {
                    $userMessage .= $errorMsg . '. Verifique as credenciais e tente novamente.';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $userMessage,
                ], 500);
            }
            
            // Verifica se a resposta é válida
            if (!is_array($response)) {
                \Log::error('FinancialController: Resposta inválida do gateway', [
                    'response_type' => gettype($response),
                    'response' => $response,
                    'gateway' => $gatewayConfig->provider_name,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Resposta inválida do gateway. Tente novamente.',
                ], 500);
            }
            
            // Função helper para garantir que o valor é string e não array
            $ensureStringValue = function($value) {
                if (is_string($value) && !empty(trim($value))) {
                    return trim($value);
                } elseif (is_array($value)) {
                    // Se for array, tenta extrair string de dentro
                    if (isset($value['qrcode']) && is_string($value['qrcode'])) {
                        return trim($value['qrcode']);
                    } elseif (isset($value[0]) && is_string($value[0])) {
                        return trim($value[0]);
                    }
                    // Se não conseguir, retorna vazio
                    return '';
                } elseif (is_object($value)) {
                    // Se for objeto, converte para string JSON
                    return json_encode($value, JSON_UNESCAPED_UNICODE);
                } elseif (is_scalar($value)) {
                    return trim((string) $value);
                }
                return '';
            };
            
            // Obtém o QR Code e a chave PIX da resposta - tenta múltiplas formas ANTES de criar transação
            $qrCodeString = '';
            
            // Primeiro tenta pegar diretamente da resposta principal
            $possibleKeys = ['qr_code', 'qrCode', 'qrcode', 'pixCopyPaste', 'pix_copy_paste', 'copyPaste', 'emv', 'qrcodeString', 'qr_code_string'];
            
            foreach ($possibleKeys as $key) {
                if (isset($response[$key])) {
                    $value = $ensureStringValue($response[$key]);
                    if (!empty($value)) {
                        $qrCodeString = $value;
                        break;
                    }
                }
            }
            
            // Se ainda não encontrou, tenta usar raw_response
            if (empty($qrCodeString) && isset($response['raw_response'])) {
                $rawResponse = $response['raw_response'];
                
                if (is_array($rawResponse)) {
                    foreach ($possibleKeys as $key) {
                        if (isset($rawResponse[$key])) {
                            $value = $ensureStringValue($rawResponse[$key]);
                            if (!empty($value)) {
                                $qrCodeString = $value;
                                break;
                            }
                        }
                    }
                    
                    // Tenta buscar em estrutura aninhada (pix.qrcode)
                    if (empty($qrCodeString) && isset($rawResponse['pix']) && is_array($rawResponse['pix'])) {
                        foreach (['qrcode', 'qrCode', 'qr_code'] as $key) {
                            if (isset($rawResponse['pix'][$key])) {
                                $value = $ensureStringValue($rawResponse['pix'][$key]);
                                if (!empty($value)) {
                                    $qrCodeString = $value;
                                    break;
                                }
                            }
                        }
                    }
                } elseif (is_string($rawResponse)) {
                    // Se raw_response já for string, pode ser o QR Code direto
                    $qrCodeString = trim($rawResponse);
                }
            }
            
            // Garante que qrCodeString é string válida ANTES de usar strlen
            if (!is_string($qrCodeString)) {
                $qrCodeString = '';
            }
            $qrCodeString = trim($qrCodeString);
            
            // Log detalhado para debug (sem usar strlen se estiver vazio)
            \Log::info('FinancialController: Análise da resposta do gateway', [
                'qr_code_encontrado' => !empty($qrCodeString) && is_string($qrCodeString),
                'qr_code_length' => is_string($qrCodeString) ? strlen($qrCodeString) : 0,
                'qr_code_type' => gettype($qrCodeString),
                'response_keys' => is_array($response) ? array_keys($response) : 'not array',
                'has_raw_response' => isset($response['raw_response']),
                'raw_response_type' => isset($response['raw_response']) ? gettype($response['raw_response']) : 'N/A',
                'gateway' => $gatewayConfig->provider_name,
            ]);

            // Verifica se o QR Code foi gerado ANTES de criar a transação
            if (empty($qrCodeString) || !is_string($qrCodeString)) {
                \Log::error('FinancialController: QR Code não foi retornado pelo gateway', [
                    'gateway' => $gatewayConfig->provider_name,
                    'response' => is_array($response) ? json_encode($response, JSON_PRETTY_PRINT) : $response,
                    'transaction_uuid' => $transactionId->toString(),
                    'external_id' => $externalIdForGateway,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar QR Code. O gateway não retornou o código PIX. Por favor, verifique se as credenciais do gateway estão configuradas corretamente no painel administrativo.',
                ], 400);
            }

            // Se chegou aqui, o QR Code foi obtido com sucesso - agora cria a transação
            // O gateway pode retornar um transaction_id diferente, mas mantemos o external_id original
            $gatewayTransactionId = $response['transaction_id'] ?? $response['transactionId'] ?? null;

            // Obtém expires_at da resposta do gateway ou usa o padrão (5 minutos)
            $gatewayExpiresAt = isset($response['expires_at']) 
                ? \Carbon\Carbon::parse($response['expires_at']) 
                : $expiresAt;

            // Prioriza o transaction_id retornado pelo gateway, mas mantém o UUID original para busca
            $finalExternalId = $gatewayTransactionId ?? $response['external_id'] ?? $externalIdForGateway;
            
            $transaction = Transaction::create([
                'uuid' => $transactionId,
                'user_id' => $user->id,
                'amount_gross' => $amountGross,
                'amount_net' => $amountNet,
                'fee' => $fee,
                'type' => 'pix',
                'status' => 'pending',
                'external_id' => $finalExternalId,
                'gateway_provider' => $gatewayConfig->provider_name,
                'expires_at' => $gatewayExpiresAt,
                'payer_name' => $request->payer_name,
                'payer_email' => $user->email,
            ]);
            
            Log::info('FinancialController: Transação criada para depósito com sucesso', [
                'transaction_id' => $transaction->id,
                'uuid' => $transaction->uuid,
                'external_id' => $transaction->external_id,
                'gateway_transaction_id' => $gatewayTransactionId,
                'amount_gross' => $amountGross,
                'fee' => $fee,
                'amount_net' => $amountNet,
                'has_qr_code' => !empty($qrCodeString),
            ]);

            // Gera imagem do QR Code localmente usando BaconQrCode
            try {
                $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                    new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400, 1),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                );
                $writer = new \BaconQrCode\Writer($renderer);
                $qrCodeSvg = $writer->writeString($qrCodeString);
                
                // Força o SVG a ocupar 100% do container
                $qrCodeSvg = str_replace('<svg', '<svg style="width: 100%; height: 100%;"', $qrCodeSvg);
                
                $qrCode = '<div class="w-64 h-64 mx-auto bg-white p-2 rounded-lg">' . $qrCodeSvg . '</div>';
            } catch (\Exception $qrException) {
                // Fallback para API externa em caso de erro na geração local
                \Log::warning('Erro ao gerar QR Code localmente, usando fallback: ' . $qrException->getMessage());
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrCodeString);
                $qrCode = '<img src="' . $qrCodeUrl . '" alt="QR Code PIX" class="w-64 h-64 mx-auto rounded-lg" />';
            }

            return response()->json([
                'success' => true,
                'qr_code' => $qrCode,
                'qr_code_string' => $qrCodeString,
                'pix_key' => $qrCodeString, // Chave PIX copia e cola
                'transaction_id' => $transaction->uuid, // UUID da transação para verificação de status
                'transaction_uuid' => $transaction->uuid, // Alias para compatibilidade
                'external_id' => $response['external_id'] ?? null,
                'amount_gross' => $amountGross,
                'amount_net' => $amountNet,
                'fee' => $fee,
                'fee_percentage' => $cashinPixPercentual,
                'expires_at' => $gatewayExpiresAt->toIso8601String(),
                'expires_in_seconds' => max(0, $gatewayExpiresAt->diffInSeconds(now())),
                'transaction' => [
                    'id' => $transaction->id,
                    'uuid' => $transaction->uuid,
                    'status' => $transaction->status,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('FinancialController: Erro ao gerar QR Code via gateway', [
                'error' => $e->getMessage(),
                'gateway' => $gatewayConfig->provider_name ?? 'unknown',
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Se a transação foi criada, marca como failed
            if (isset($transaction)) {
                try {
                    $transaction->update(['status' => 'failed']);
                } catch (\Exception $updateException) {
                    \Log::error('FinancialController: Erro ao atualizar transação para failed', [
                        'error' => $updateException->getMessage(),
                    ]);
                }
            }

            // Retorna erro específico com mensagem clara
            $errorMessage = 'Erro ao gerar QR Code. ';
            if (str_contains($e->getMessage(), 'Credenciais')) {
                $errorMessage .= 'Verifique se as credenciais do gateway estão configuradas corretamente no painel administrativo.';
            } elseif (str_contains($e->getMessage(), 'token') || str_contains($e->getMessage(), 'auth')) {
                $errorMessage .= 'Erro de autenticação com o gateway. Verifique as credenciais.';
            } else {
                $errorMessage .= $e->getMessage() . '. Por favor, tente novamente ou entre em contato com o suporte.';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 500);
        }
    }

    /**
     * Solicita saque
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function requestWithdrawal(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Verifica se o usuário está aprovado (apenas saques requerem aprovação)
        if (!$user->is_approved || $user->kyc_status !== 'approved') {
            return back()->with('error', 'Você precisa estar aprovado para realizar saques. Complete seu cadastro enviando os documentos para verificação (KYC).')
                ->with('info', 'Acesse a página de KYC para enviar seus documentos.');
        }

        // Verifica se o usuário pode sacar
        if ($user->bloquear_saque) {
            return back()->with('error', 'Seus saques estão bloqueados. Entre em contato com o suporte.');
        }

        // Valor mínimo de saque
        $withdrawalMinValue = floatval(Setting::get('withdrawal_min_value', '10.00'));
        
        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $withdrawalMinValue],
            'pix_key' => 'required|string|min:10',
            'pin' => 'required|string|size:6',
        ], [
            'amount.min' => "O valor mínimo para saque é R$ " . number_format($withdrawalMinValue, 2, ',', '.'),
            'pin.required' => 'O PIN é obrigatório para confirmar o saque.',
            'pin.size' => 'O PIN deve ter exatamente 6 dígitos.',
        ]);
        
        // Verifica se o usuário tem PIN configurado
        if (!$user->pin_configured) {
            return back()->with('error', 'Você precisa configurar um PIN antes de realizar saques. Configure no seu perfil.');
        }
        
        // Valida o PIN
        if (!Hash::check($request->pin, $user->pin)) {
            return back()->with('error', 'PIN incorreto. Tente novamente.');
        }

        // O valor informado é o valor que o usuário quer receber (valor líquido desejado)
        $desiredNetAmount = $request->amount;

        // SEMPRE usa as taxas do painel
        $cashoutPixFixo = $user->getCashoutPixFixo();
        $cashoutPixPercentual = $user->getCashoutPixPercentual();
        $cashoutPixMinima = floatval(Setting::get('cashout_pix_minima', '0.80'));

        // Calcula o valor bruto necessário para que após a taxa ele receba o valor desejado
        // Considerando taxa mínima: taxa = max(percentual, mínima) + fixo
        // Usa iteração para encontrar o valor exato
        $amountGross = $desiredNetAmount;
        $maxIterations = 10;
        $tolerance = 0.01;
        
        for ($i = 0; $i < $maxIterations; $i++) {
            // Calcula a taxa percentual sobre o valor bruto atual
            $feePercentual = ($amountGross * $cashoutPixPercentual) / 100;
            
            // Aplica a taxa mínima se necessário
            $fee = max($feePercentual, $cashoutPixMinima) + $cashoutPixFixo;
            
            // Calcula o valor líquido resultante
            $calculatedNet = $amountGross - $fee;
            
            // Se estiver dentro da tolerância, para
            if (abs($calculatedNet - $desiredNetAmount) <= $tolerance) {
                break;
            }
            
            // Ajusta o valor bruto para a próxima iteração
            $difference = $desiredNetAmount - $calculatedNet;
            $amountGross += $difference;
        }
        
        // Recalcula valores finais
        $feePercentual = ($amountGross * $cashoutPixPercentual) / 100;
        $fee = max($feePercentual, $cashoutPixMinima) + $cashoutPixFixo;
        $amountNet = $amountGross - $fee;

        // Arredonda para 2 casas decimais
        $amountGross = round($amountGross, 2);
        $fee = round($fee, 2);
        $amountNet = round($amountNet, 2);

        // Verifica saldo disponível (precisa ter saldo livre para o valor bruto + taxa)
        // Saldo disponível = balance (já não inclui valores bloqueados em frozen_balance)
        $wallet = $user->wallet;
        $availableBalance = $wallet ? max(0, $wallet->balance) : 0.00;
        
        if (!$wallet || $availableBalance < $amountGross) {
            $neededBalance = number_format($amountGross, 2, ',', '.');
            $currentBalance = number_format($availableBalance, 2, ',', '.');
            $feeFormatted = number_format($fee, 2, ',', '.');
            $netFormatted = number_format($amountNet, 2, ',', '.');
            
            return back()->with('error', "Saldo insuficiente! Para receber R$ {$netFormatted} líquido, você precisa de R$ {$neededBalance} (incluindo taxa de R$ {$feeFormatted}). Seu saldo disponível é R$ {$currentBalance}.");
        }

        try {
            // Verifica se é o primeiro saque do usuário
            $isFirstWithdrawal = !$user->first_withdrawal_completed;
            
            // Verifica modo de saque ANTES da transação
            // O saque manual só deve ocorrer se o administrador alterar no usuário para Saque Manual.
            // Por padrão (auto ou null), deve ser automático.
            $userWithdrawalMode = $user->withdrawal_mode;
            $shouldProcessAutomatically = true;
            
            // Se o usuário tiver explicitamente definido como manual, respeita
            if ($userWithdrawalMode === 'manual') {
                $shouldProcessAutomatically = false;
            }
            
            // Verifica a configuração global de saques
            // Se houver uma configuração global definindo como 'manual', ela deve prevalecer sobre o padrão 'auto' do usuário
            // Mas se o usuário tiver 'automatic' explicitamente, deve prevalecer (opcional, dependendo da regra de negócio)
            // Aqui assumimos que se o usuário não tem preferência (null), usamos o global.
            // Se o global não existir, o padrão é 'automatic'.
            
            // Para garantir que saques automáticos funcionem, vamos forçar 'automatic' como padrão
            // a menos que especificamente configurado como 'manual' no usuário
            
            $withdrawal = null;
            
            DB::transaction(function () use ($user, $amountGross, $amountNet, $fee, $request, &$withdrawal) {
                // Bloqueia o valor na carteira
                $wallet = $user->wallet;
                if (!$wallet) {
                    $wallet = Wallet::create([
                        'user_id' => $user->id,
                        'balance' => 0.00,
                        'frozen_balance' => 0.00,
                    ]);
                }

                // Verifica novamente o saldo disponível antes de bloquear
                // Saldo disponível = balance (já não inclui valores bloqueados)
                if ($wallet->balance < $amountGross) {
                    throw new \Exception('Saldo insuficiente');
                }

                // Bloqueia o valor bruto (incluindo a taxa)
                $wallet->decrement('balance', $amountGross);
                // REMOVIDO: O saldo não deve ir para frozen_balance em saques, apenas em MED/Chargeback
                // $wallet->increment('frozen_balance', $amountGross);

                // Cria solicitação de saque
                $withdrawal = Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $amountNet, // Valor líquido que será pago
                    'amount_gross' => $amountGross, // Valor bruto bloqueado
                    'fee' => $fee, // Taxa cobrada
                    'pix_key' => $request->pix_key,
                    'status' => 'pending',
                ]);

                // Dispara webhook de saque pendente
                try {
                    $webhookService = app(\App\Services\WebhookService::class);
                    $webhookService->dispatch('withdrawal.pending', [
                        'withdrawal_id' => $withdrawal->id,
                        'amount' => (float) $withdrawal->amount,
                        'amount_gross' => (float) $withdrawal->amount_gross,
                        'fee' => (float) $withdrawal->fee,
                        'pix_key' => $withdrawal->pix_key,
                        'created_at' => $withdrawal->created_at->toIso8601String(),
                    ], $user->id);
                } catch (\Exception $e) {
                    Log::error('Erro ao disparar webhook de saque pendente', [
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $e->getMessage(),
                    ]);
                }

            });
            
            // Verifica se o saque foi criado
            if (!$withdrawal) {
                return back()->with('error', 'Erro ao criar solicitação de saque. Tente novamente.');
            }
            
            // Processa automaticamente FORA da transação se o modo global ou individual for automático
            Log::info('FinancialController: Verificando processamento automático', [
                'user_id' => $user->id,
                'withdrawal_id' => $withdrawal->id,
                'should_process_automatically' => $shouldProcessAutomatically,
                'user_mode' => $userWithdrawalMode,
            ]);
            
            if ($shouldProcessAutomatically) {
                try {
                    Log::info('FinancialController: Iniciando processamento automático', [
                        'withdrawal_id' => $withdrawal->id,
                    ]);
                    
                    $withdrawalService = new \App\Services\WithdrawalService();
                    $result = $withdrawalService->processWithdrawal($withdrawal);
                    
                    // Atualiza o saque para pegar o status atualizado
                    $withdrawal->refresh();
                    
                    Log::info('FinancialController: Resultado do processamento automático', [
                        'withdrawal_id' => $withdrawal->id,
                        'success' => $result['success'] ?? false,
                        'status' => $withdrawal->status,
                    ]);
                    
                    // Se o processamento automático falhou, verifica o motivo
                    if (!$result['success']) {
                        // Se foi rejeitado (ex: saldo insuficiente), mantém rejeitado e exibe erro
                        if (($result['status'] ?? '') === 'rejected') {
                             return back()->with('error', $result['message'] ?? 'Saque recusado pelo processador.');
                        }

                        // Se falhou por outro motivo (erro técnico), mantém como pending para aprovação manual
                        $withdrawal->update(['status' => 'pending']);
                        
                        // Cria notificação de falha
                        try {
                            \App\Models\Notification::create([
                                'title' => 'Saque Não Realizado Automaticamente',
                                'message' => 'Seu saque não pôde ser processado automaticamente e foi enviado para aprovação manual.',
                                'type' => 'warning',
                                'is_active' => true,
                            ]);
                            
                            $notification = \App\Models\Notification::latest()->first();
                            \App\Models\UserNotification::create([
                                'user_id' => $user->id,
                                'notification_id' => $notification->id,
                                'is_read' => false,
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Erro ao criar notificação de saque falhado', [
                                'user_id' => $user->id,
                                'withdrawal_id' => $withdrawal->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                        
                        return back()->with('warning', 'O saque não pôde ser processado automaticamente, mas foi enviado para aprovação manual do administrador.');
                    } else {
                        // Cria notificação de sucesso para saque automático
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
                            Log::error('Erro ao criar notificação de saque processado', [
                                'user_id' => $user->id,
                                'withdrawal_id' => $withdrawal->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao processar saque automaticamente', [
                        'user_id' => $user->id,
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Se der erro, mantém como pending
                    $withdrawal->update(['status' => 'pending']);
                }
            } else {
                // Cria notificação para saque manual aguardando aprovação
                try {
                    \App\Models\Notification::create([
                        'title' => 'Saque Realizado com Sucesso',
                        'message' => "Seu saque de R$ " . number_format($withdrawal->amount, 2, ',', '.') . " foi solicitado com sucesso e está aguardando aprovação do administrador.",
                        'type' => 'info',
                        'is_active' => true,
                    ]);
                    
                    $notification = \App\Models\Notification::latest()->first();
                    \App\Models\UserNotification::create([
                        'user_id' => $user->id,
                        'notification_id' => $notification->id,
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erro ao criar notificação de saque pendente', [
                        'user_id' => $user->id,
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Verifica se o saque foi criado
            if (!$withdrawal) {
                return back()->with('error', 'Erro ao criar solicitação de saque. Tente novamente.');
            }
            
            $feeFormatted = number_format($fee, 2, ',', '.');
            $netFormatted = number_format($amountNet, 2, ',', '.');
            $grossFormatted = number_format($amountGross, 2, ',', '.');
            
            // Atualiza o saque para pegar o status atualizado
            $withdrawal->refresh();
            
            // Verifica se foi processado automaticamente
            if ($withdrawal->status === 'processing' || $withdrawal->status === 'paid') {
                return back()->with('success', "Saque Realizado com Sucesso! Seu saque foi processado automaticamente e está sendo enviado para sua conta. Você receberá R$ {$netFormatted} líquido (taxa de R$ {$feeFormatted} sobre R$ {$grossFormatted}).");
            } else {
                return back()->with('success', "Saque Realizado com Sucesso, Aguardando aprovação. Seu saque foi solicitado e está aguardando aprovação do administrador. Você receberá R$ {$netFormatted} líquido (taxa de R$ {$feeFormatted} sobre R$ {$grossFormatted}).");
            }
        } catch (\Exception $e) {
            // Log do erro
            \App\Services\ErrorLogService::log(
                'Erro ao solicitar saque',
                $e->getMessage(),
                'withdrawal_error',
                'error',
                [
                    'user_id' => $user->id,
                    'amount' => $amountGross,
                ],
                $e
            );

            return back()->with('error', 'Erro ao processar solicitação de saque: ' . $e->getMessage());
        }
    }

    /**
     * Solicita antecipação de saldo a liberar (cartão de crédito)
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function requestAdvance(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $request->validate([
            'accept_terms' => 'required|accepted',
        ], [
            'accept_terms.accepted' => 'Você deve aceitar os termos de antecipação para continuar.',
        ]);

        try {
            // Busca transações de cartão que ainda não foram liberadas
            $transactionsToAdvance = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where('type', 'credit')
                ->whereNull('released_at')
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('available_at')
                          ->where('available_at', '>', now());
                    })->orWhere(function($q) {
                        $q->whereNull('available_at');
                    });
                })
                ->get();

            if ($transactionsToAdvance->isEmpty()) {
                return back()->with('error', 'Você não possui saldo a liberar para antecipar.');
            }

            $totalAmountToRelease = $transactionsToAdvance->sum('amount_net');
            
            if ($totalAmountToRelease <= 0) {
                return back()->with('error', 'Você não possui saldo a liberar para antecipar.');
            }

            // Taxa de antecipação (padrão 38% ou configurável pelo admin)
            $advanceFeePercentage = floatval(Setting::get('advance_fee_percentage', '38.00'));
            $advanceFee = ($totalAmountToRelease * $advanceFeePercentage) / 100;
            $amountAfterFee = $totalAmountToRelease - $advanceFee;

            // Arredonda valores
            $advanceFee = round($advanceFee, 2);
            $amountAfterFee = round($amountAfterFee, 2);

            DB::transaction(function () use ($user, $transactionsToAdvance, $totalAmountToRelease, $advanceFee, $amountAfterFee, $advanceFeePercentage) {
                $wallet = $user->wallet ?? Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0.00,
                    'frozen_balance' => 0.00,
                ]);

                // Credita o valor líquido (após taxa) na carteira
                $wallet->increment('balance', $amountAfterFee);

                // Marca todas as transações como liberadas (released_at)
                foreach ($transactionsToAdvance as $transaction) {
                    $transaction->update([
                        'released_at' => now(),
                        'available_at' => now(), // Atualiza para agora
                    ]);
                }

                // Registra transação de antecipação (débito para registrar a taxa)
                Transaction::create([
                    'uuid' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'amount_gross' => $totalAmountToRelease,
                    'amount_net' => $amountAfterFee,
                    'fee' => $advanceFee,
                    'type' => 'advance',
                    'status' => 'completed',
                    'gateway_provider' => 'system',
                    'payer_name' => $user->name,
                    'payer_email' => $user->email,
                    'description' => "Antecipação de saldo a liberar (Taxa: {$advanceFeePercentage}%)",
                    'released_at' => now(),
                    'available_at' => now(),
                ]);

                Log::info('Antecipação de saldo processada', [
                    'user_id' => $user->id,
                    'total_amount' => $totalAmountToRelease,
                    'advance_fee' => $advanceFee,
                    'amount_credited' => $amountAfterFee,
                    'transactions_count' => $transactionsToAdvance->count(),
                ]);
            });

            $feeFormatted = number_format($advanceFee, 2, ',', '.');
            $amountFormatted = number_format($amountAfterFee, 2, ',', '.');
            $totalFormatted = number_format($totalAmountToRelease, 2, ',', '.');

            return back()->with('success', "Antecipação processada com sucesso! Você recebeu R$ {$amountFormatted} líquido (taxa de R$ {$feeFormatted} sobre R$ {$totalFormatted}).");
        } catch (\Exception $e) {
            Log::error('Erro ao processar antecipação', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erro ao processar antecipação: ' . $e->getMessage());
        }
    }
}
