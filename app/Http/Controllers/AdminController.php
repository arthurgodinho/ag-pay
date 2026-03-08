<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SystemGatewayConfig;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    /**
     * Página inicial do admin (redireciona para dashboard)
     *
     * @return View
     */
    public function index(): View
    {
        return $this->dashboard();
    }

    /**
     * Dashboard Admin
     *
     * @return View
     */
    public function dashboard(): View
    {
        // Cache por 10 minutos para estatísticas do dashboard admin
        $stats = Cache::remember('admin.dashboard.stats_v2', 600, function () {
            // Lucro Total (soma de todas as taxas das transações completadas)
            $totalProfit = Transaction::where('status', 'completed')
                ->sum('fee');

            // Saldo Total dos Usuários (soma de todos os saldos das carteiras)
            $totalUserBalance = Wallet::sum('balance');

            // Estatísticas adicionais
            $totalUsers = User::count();
            $pendingKyc = User::where('kyc_status', 'pending')->count();
            $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
            
            // Chargebacks pendentes
            try {
                $totalChargebacks = \App\Models\Chargeback::where('status', 'pending')->count();
            } catch (\Exception $e) {
                $totalChargebacks = 0;
            }

            // Novas estatísticas que estavam na view
            $transactionsToday = Transaction::whereDate('created_at', today())->count();
            $transactionsCompleted = Transaction::where('status', 'completed')->count();
            $transactionsPending = Transaction::where('status', 'pending')->count();
            $totalTransactions = Transaction::count();
            $saldoCongelado = Wallet::sum('frozen_balance');

            // Dados para o gráfico (últimos 7 dias)
            $chartData = [];
            $dates = [];
            $volumes = [];
            $profits = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dates[] = $date->format('d/m');
                
                // Volume total do dia (completed)
                $dayVolume = Transaction::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('amount_gross');
                
                // Lucro do dia (fees)
                $dayProfit = Transaction::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('fee');
                
                $volumes[] = floatval($dayVolume);
                $profits[] = floatval($dayProfit);
            }

            $chartData = [
                'dates' => $dates,
                'volumes' => $volumes,
                'profits' => $profits
            ];

            return compact(
                'totalProfit', 'totalUserBalance', 'totalUsers', 
                'pendingKyc', 'pendingWithdrawals', 'totalChargebacks',
                'transactionsToday', 'transactionsCompleted', 'transactionsPending',
                'totalTransactions', 'saldoCongelado', 'chartData'
            );
        });
        
        return view('admin.dashboard', $stats);
    }

    /**
     * Lista todos os usuários
     *
     * @return View
     */
    public function users(): View
    {
        $users = User::with('wallet')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Aprova ou rejeita KYC de um usuário
     *
     * @param Request $request
     * @param int $userId
     * @return RedirectResponse
     */
    public function updateKyc(Request $request, int $userId): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $user = User::findOrFail($userId);

        $user->update([
            'kyc_status' => $request->action === 'approve' ? 'approved' : 'rejected',
            'is_approved' => $request->action === 'approve' ? true : false,
        ]);

        return back()->with('success', 'Status KYC atualizado com sucesso!');
    }

    /**
     * Lista saques pendentes
     *
     * @return View
     */
    public function withdrawals(): View
    {
        $withdrawals = Withdrawal::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Valor máximo para pagamento automático (configurável via env ou config)
        $autoPayMaxAmount = config('app.auto_pay_max_amount', 50.00);

        return view('admin.withdrawals', compact('withdrawals', 'autoPayMaxAmount'));
    }

    /**
     * Processa um saque
     *
     * @param Request $request
     * @param int $withdrawalId
     * @return RedirectResponse
     */
    public function processWithdrawal(Request $request, int $withdrawalId): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $withdrawal = Withdrawal::with('user')->findOrFail($withdrawalId);

        if ($request->action === 'approve') {
            // Redireciona para o método correto de pagamento que processa via gateway
            // O valor já foi bloqueado (movido para frozen_balance) quando o saque foi solicitado
            return redirect()->route('admin.withdrawals.pay', $withdrawalId);
        } else {
            // Reembolsa o saque (estorna o valor bloqueado)
            $amountToRefund = $withdrawal->amount_gross ?? $withdrawal->amount;
            $wallet = $withdrawal->user->wallet;
            if ($wallet) {
                // Remove do frozen_balance e devolve para balance
                if ($wallet->frozen_balance >= $amountToRefund) {
                    $wallet->decrement('frozen_balance', $amountToRefund);
                    $wallet->increment('balance', $amountToRefund);
                } else {
                    // Fallback: apenas devolve para balance
                    $wallet->increment('balance', $amountToRefund);
                }
            }

            $withdrawal->update([
                'status' => 'cancelled',
                'admin_note' => $request->admin_note,
            ]);

            return back()->with('success', 'Saque rejeitado e valor estornado!');
        }
    }

    /**
     * Pagamento automático de saque (para valores <= limite)
     *
     * @param int $withdrawalId
     * @return RedirectResponse
     */
    public function autoPayWithdrawal(int $withdrawalId): RedirectResponse
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($withdrawalId);
        $autoPayMaxAmount = config('app.auto_pay_max_amount', 50.00);

        if ($withdrawal->amount > $autoPayMaxAmount) {
            return back()->with('error', 'Este saque excede o valor máximo para pagamento automático!');
        }

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Este saque já foi processado!');
        }

        try {
            // Usa o WithdrawalService para processar o pagamento via gateway
            // O serviço já lida corretamente com frozen_balance e chamadas ao gateway
            $withdrawalService = new \App\Services\WithdrawalService();
            $result = $withdrawalService->processWithdrawal($withdrawal);
            
            if ($result['success']) {
                return back()->with('success', $result['message'] ?? 'Saque processado com sucesso!');
            } else {
                return back()->with('error', 'Erro ao processar saque: ' . ($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            \Log::error('Erro em AdminController::autoPayWithdrawal', [
                'withdrawal_id' => $withdrawalId,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Erro interno ao processar saque: ' . $e->getMessage());
        }
    }

    /**
     * Exibe formulário de configuração de gateways
     *
     * @return View
     */
    public function gateways(): View
    {
        // Busca todas as configurações existentes e organiza por provider_name
        $configs = SystemGatewayConfig::orderBy('provider_name')->get()->keyBy('provider_name');
        
        // Lista de todos os provedores disponíveis
        $providers = [
            'bspay' => 'BSPay',
            'venit' => 'Venit',
            'podpay' => 'PodPay',
            'hypercash' => 'HyperCash',
            'paguemax' => 'PagueMax',
            'zoompag' => 'ZoomPag',
            'efi' => 'Efi Bank',
            'pluggou' => 'Pluggou',
        ];

        // Busca os adquirentes padrão
        $defaultGatewayForAllUsers = Setting::get('default_gateway_for_all_users', '');
        $defaultGatewayForCashinPix = Setting::get('default_gateway_for_cashin_pix', '');
        $defaultGatewayForPix = Setting::get('default_gateway_for_pix', '');
        $defaultGatewayForWithdrawals = Setting::get('default_gateway_for_withdrawals', '');
        $defaultGatewayForCard = Setting::get('default_gateway_for_card', '');
        $defaultGatewayForCheckoutPix = Setting::get('default_gateway_for_checkout_pix', '');
        $defaultGatewayForCheckoutCard = Setting::get('default_gateway_for_checkout_card', '');

        return view('admin.gateways.index', compact(
            'configs', 
            'providers', 
            'defaultGatewayForAllUsers',
            'defaultGatewayForCashinPix',
            'defaultGatewayForPix',
            'defaultGatewayForWithdrawals',
            'defaultGatewayForCard',
            'defaultGatewayForCheckoutPix',
            'defaultGatewayForCheckoutCard'
        ));
    }

    /**
     * Exibe formulário de configuração de gateways (método antigo - mantido para compatibilidade)
     *
     * @return View
     */
    public function gatewayConfigs(): View
    {
        return $this->gateways();
    }

    /**
     * Atualiza múltiplas configurações de gateways de uma vez
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateGateways(Request $request): RedirectResponse
    {
        // Log ANTES da validação para debug
        \Log::info('AdminController::updateGateways: Dados recebidos', [
            'all_inputs' => $request->all(),
            'checkout_card_raw' => $request->input('default_gateway_for_checkout_card'),
            'has_gateways' => $request->has('gateways'),
            'gateways_count' => is_array($request->input('gateways')) ? count($request->input('gateways')) : 0,
        ]);
        
        try {
            // Valida os campos, permitindo valores vazios para os selects de gateway padrão
            $validated = $request->validate([
                'gateways' => 'required|array',
                'gateways.*.provider_name' => 'required|string|in:bspay,venit,podpay,hypercash,efi,paguemax,zoompag,pluggou',
                'gateways.*.client_id' => 'nullable|string|max:255',
                'gateways.*.client_secret' => 'nullable|string',
                'gateways.*.pix_key' => 'nullable|string|max:255',
                'gateways.*.certificate_path' => 'nullable|string',
                'gateways.*.is_active_for_pix' => 'nullable|boolean',
                'gateways.*.is_active_for_card' => 'nullable|boolean',
                'default_gateway_for_all_users' => 'nullable|string',
                'default_gateway_for_cashin_pix' => 'nullable|string',
                'default_gateway_for_pix' => 'nullable|string',
                'default_gateway_for_withdrawals' => 'nullable|string',
                'default_gateway_for_card' => 'nullable|string',
                'default_gateway_for_checkout_pix' => 'nullable|string',
                'withdrawal_mode' => 'required|in:auto,manual',
                'credit_card_transaction_fee_percent' => 'nullable|numeric|min:0|max:100',
                'credit_card_transaction_fee_fixed' => 'nullable|numeric|min:0',
                'paguemax_api_url' => 'nullable|url',
                'paguemax_withdrawal_api_url' => 'nullable|string',
                'zoompag_post_url' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('AdminController::updateGateways: Erro de validação', [
                'errors' => $e->errors(),
                'all_inputs' => $request->all(),
            ]);
            return back()->withErrors($e->errors())->withInput();
        }
        
        // Valida manualmente os valores dos gateways padrão se não forem vazios
        $allowedGateways = ['bspay', 'venit', 'podpay', 'hypercash', 'efi', 'paguemax', 'zoompag', 'pluggou'];
        $gatewayFields = [
            'default_gateway_for_all_users',
            'default_gateway_for_cashin_pix',
            'default_gateway_for_pix',
            'default_gateway_for_withdrawals',
            'default_gateway_for_checkout_pix',
        ];
        
        foreach ($gatewayFields as $field) {
            $value = $request->input($field);
            if (!empty($value) && !in_array($value, $allowedGateways)) {
                return back()->withErrors([$field => 'Gateway inválido selecionado.'])->withInput();
            }
        }
        
        // Log detalhado para debug
        \Log::info('AdminController: Dados recebidos do formulário', [
            'default_gateway_for_checkout_card' => $request->input('default_gateway_for_checkout_card'),
            'default_gateway_for_checkout_card_type' => gettype($request->input('default_gateway_for_checkout_card')),
            'all_inputs' => $request->all(),
        ]);

        // Atualiza cada gateway
        foreach ($request->gateways as $gatewayData) {
            // Limpa espaços em branco das credenciais
            $clientId = !empty($gatewayData['client_id']) ? trim($gatewayData['client_id']) : null;
            $clientSecret = !empty($gatewayData['client_secret']) ? trim($gatewayData['client_secret']) : null;
            $pixKey = !empty($gatewayData['pix_key']) ? trim($gatewayData['pix_key']) : null;
            $certificatePath = !empty($gatewayData['certificate_path']) ? trim($gatewayData['certificate_path']) : null;
            
            SystemGatewayConfig::updateOrCreate(
                ['provider_name' => $gatewayData['provider_name']],
                [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'pix_key' => $pixKey,
                    'certificate_path' => $certificatePath,
                    'is_active_for_pix' => isset($gatewayData['is_active_for_pix']) && $gatewayData['is_active_for_pix'] == '1',
                    'is_active_for_card' => isset($gatewayData['is_active_for_card']) && $gatewayData['is_active_for_card'] == '1',
                ]
            );
        }

        // Atualiza os adquirentes padrão globais
        // Normaliza valores vazios para string vazia antes de salvar
        $normalizeEmpty = function($value) {
            if ($value === null) return '';
            $trimmed = trim((string) $value);
            return $trimmed === '' ? '' : $trimmed;
        };
        
        $gatewayDefaults = [
            'default_gateway_for_all_users' => $normalizeEmpty($request->input('default_gateway_for_all_users')),
            'default_gateway_for_cashin_pix' => $normalizeEmpty($request->input('default_gateway_for_cashin_pix')),
            'default_gateway_for_pix' => $normalizeEmpty($request->input('default_gateway_for_pix')),
            'default_gateway_for_withdrawals' => $normalizeEmpty($request->input('default_gateway_for_withdrawals')),
            'default_gateway_for_checkout_pix' => $normalizeEmpty($request->input('default_gateway_for_checkout_pix')),
            'default_gateway_for_checkout_card' => $normalizeEmpty($request->input('default_gateway_for_checkout_card')),
            'credit_card_transaction_fee_percent' => $request->input('credit_card_transaction_fee_percent', 0),
            'credit_card_transaction_fee_fixed' => $request->input('credit_card_transaction_fee_fixed', 0),
        ];

        if ($request->has('paguemax_api_url')) {
            $gatewayDefaults['paguemax_api_url'] = $request->input('paguemax_api_url');
        }
        if ($request->has('paguemax_withdrawal_api_url')) {
            $gatewayDefaults['paguemax_withdrawal_api_url'] = $request->input('paguemax_withdrawal_api_url');
        }
        
        if ($request->has('zoompag_post_url')) {
            $gatewayDefaults['zoompag_post_url'] = $request->input('zoompag_post_url');
        }
        
        // Log ANTES de salvar
        \Log::info('AdminController: Salvando configurações de gateway', [
            'gateway_defaults' => $gatewayDefaults,
        ]);
        
        // Salva cada configuração
        $savedKeys = [];
        $errors = [];
        foreach ($gatewayDefaults as $key => $value) {
            try {
                Setting::set($key, $value);
                $savedKeys[] = $key;
                \Log::info("AdminController: Configuração salva: $key = " . ($value ?: '(vazio)'));
            } catch (\Exception $e) {
                $errors[] = "$key: " . $e->getMessage();
                \Log::error("AdminController: Erro ao salvar $key", [
                    'error' => $e->getMessage(),
                    'value' => $value,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
        
        try {
            $oldWithdrawalMode = Setting::get('withdrawal_mode', 'manual');
            $newWithdrawalMode = $request->input('withdrawal_mode', 'manual');
            
            Setting::set('withdrawal_mode', $newWithdrawalMode);
            
            // Se o modo de saque global foi alterado, atualiza TODOS os usuários
            if ($oldWithdrawalMode !== $newWithdrawalMode) {
                try {
                    // Atualiza TODOS os usuários para o novo modo global
                    DB::table('users')
                        ->update([
                            'withdrawal_mode' => $newWithdrawalMode,
                            'updated_at' => now(),
                        ]);
                    
                    \Log::info('AdminController: Modo de saque global alterado - Todos os usuários atualizados', [
                        'old_mode' => $oldWithdrawalMode,
                        'new_mode' => $newWithdrawalMode,
                        'users_updated' => DB::table('users')->count(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('AdminController: Erro ao atualizar modo de saque dos usuários', [
                        'error' => $e->getMessage(),
                        'new_mode' => $newWithdrawalMode,
                    ]);
                    $errors[] = "Erro ao atualizar modo de saque dos usuários: " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $errors[] = "Configurações adicionais: " . $e->getMessage();
            \Log::error("AdminController: Erro ao salvar configurações adicionais", [
                'error' => $e->getMessage(),
            ]);
        }
        
        // Verifica se foi salvo corretamente
        $savedCheckoutCard = Setting::get('default_gateway_for_checkout_card', '');
        \Log::info('AdminController: Configurações de gateway salvas - Verificação', [
            'gateway_defaults' => $gatewayDefaults,
            'saved_checkout_card' => $savedCheckoutCard,
            'saved_keys' => $savedKeys,
            'errors' => $errors,
            'card_release_days' => $request->input('card_release_days'),
            'withdrawal_mode' => $request->input('withdrawal_mode'),
        ]);
        
        if (!empty($errors)) {
            \Log::warning('AdminController: Alguns erros ocorreram ao salvar', ['errors' => $errors]);
            return back()->with('warning', 'Configurações salvas, mas alguns erros ocorreram: ' . implode(', ', $errors));
        }

        return back()->with('success', 'Configurações dos gateways salvas com sucesso!');
    }

    /**
     * Salva ou atualiza configuração de gateway (método antigo - mantido para compatibilidade)
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeGatewayConfig(Request $request): RedirectResponse
    {
        $request->validate([
            'provider_name' => 'required|string|in:bspay,venit,podpay',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'wallet_id' => 'nullable|string',
            'is_active_for_pix' => 'boolean',
            'is_active_for_card' => 'boolean',
            'priority' => 'required|integer|min:0|max:100',
        ]);

        SystemGatewayConfig::updateOrCreate(
            ['provider_name' => $request->provider_name],
            [
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'wallet_id' => $request->wallet_id,
                'is_active_for_pix' => $request->boolean('is_active_for_pix'),
                'is_active_for_card' => $request->boolean('is_active_for_card'),
                'priority' => $request->priority,
            ]
        );

        return back()->with('success', 'Configuração do gateway salva com sucesso!');
    }

    /**
     * Remove configuração de gateway
     *
     * @param int $configId
     * @return RedirectResponse
     */
    public function deleteGatewayConfig(int $configId): RedirectResponse
    {
        $config = SystemGatewayConfig::findOrFail($configId);
        $config->delete();

        return back()->with('success', 'Configuração removida com sucesso!');
    }

    /**
     * Lista usuários com KYC pendente
     *
     * @return View
     */
    public function kyc(): View
    {
        $users = User::where('kyc_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.kyc.index', compact('users'));
    }

    /**
     * Retorna os documentos KYC de um usuário em JSON
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getKycDocuments(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $kycPath = 'kyc/' . $user->id;
        
        $documents = [];
        
        // Busca os arquivos no storage (disco local)
        if (Storage::disk('local')->exists($kycPath)) {
            $files = Storage::disk('local')->files($kycPath);
            
            foreach ($files as $file) {
                $filename = basename($file);
                
                // Gera URL temporária para acesso aos arquivos
                if (strpos($filename, 'document_front') !== false) {
                    $documents['document_front'] = route('admin.kyc.document', ['userId' => $user->id, 'type' => 'front']);
                } elseif (strpos($filename, 'document_back') !== false) {
                    $documents['document_back'] = route('admin.kyc.document', ['userId' => $user->id, 'type' => 'back']);
                } elseif (strpos($filename, 'selfie') !== false) {
                    $documents['selfie'] = route('admin.kyc.document', ['userId' => $user->id, 'type' => 'selfie']);
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'document_front' => $documents['document_front'] ?? null,
            'document_back' => $documents['document_back'] ?? null,
            'selfie' => $documents['selfie'] ?? null,
        ]);
    }

    /**
     * Retorna um documento KYC específico
     *
     * @param int $userId
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function getKycDocument(int $userId, string $type)
    {
        $user = User::findOrFail($userId);
        
        // Mapeia o tipo para o nome do arquivo
        $filenameMap = [
            'front' => 'document_front',
            'back' => 'document_back',
            'selfie' => 'selfie',
            'cnpj_proof' => 'cnpj_proof',
            'biometric' => 'facial_biometrics',
        ];
        
        if (!isset($filenameMap[$type])) {
            abort(404);
        }
        
        $filename = $filenameMap[$type];
        $filePath = null;
        
        // Busca primeiro na nova pasta public/IMG/kyc/user_id
        $newKycPath = public_path('IMG/kyc/' . $user->id);
        if (is_dir($newKycPath)) {
            $files = scandir($newKycPath);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                if (strpos($file, $filename) !== false) {
                    $filePath = $newKycPath . DIRECTORY_SEPARATOR . $file;
                    if (file_exists($filePath)) {
                        return response()->file($filePath);
                    }
                }
            }
        }
        
        // Se não encontrou, busca no banco de dados para biometria
        if ($type === 'biometric' && $user->facial_biometrics) {
            $biometricPath = public_path($user->facial_biometrics);
            if (file_exists($biometricPath)) {
                return response()->file($biometricPath);
            }
        }
        
        // Busca na pasta antiga (compatibilidade)
        $oldKycPath = 'kyc/' . $user->id;
        if (Storage::disk('local')->exists($oldKycPath)) {
            $files = Storage::disk('local')->files($oldKycPath);
            
            foreach ($files as $file) {
                if (strpos(basename($file), $filename) !== false) {
                    if (Storage::disk('local')->exists($file)) {
                        return Storage::disk('local')->response($file);
                    }
                }
            }
        }
        
        abort(404);
    }

    /**
     * Aprova o KYC de um usuário
     *
     * @param int $userId
     * @return RedirectResponse
     */
    public function approveKyc(int $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);
        
        if ($user->kyc_status !== 'pending') {
            return back()->with('error', 'Este usuário não possui KYC pendente.');
        }
        
        $wasApproved = $user->is_approved;
        
        $user->update([
            'kyc_status' => 'approved',
            'is_approved' => true,
        ]);
        
        // Dispara evento se acabou de ser aprovado
        if (!$wasApproved && $user->is_approved) {
            \App\Events\UserApproved::dispatch($user);
        }
        
        return back()->with('success', 'KYC aprovado com sucesso!');
    }

    /**
     * Rejeita o KYC de um usuário
     *
     * @param int $userId
     * @return RedirectResponse
     */
    public function rejectKyc(int $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);
        
        if ($user->kyc_status !== 'pending') {
            return back()->with('error', 'Este usuário não possui KYC pendente.');
        }
        
        $user->update([
            'kyc_status' => 'rejected',
            'is_approved' => false,
        ]);
        
        return back()->with('success', 'KYC rejeitado.');
    }

    /**
     * Visualiza documentos KYC de um usuário (método antigo - mantido para compatibilidade)
     *
     * @param int $userId
     * @return View
     */
    public function viewKycDocuments(int $userId): View
    {
        $user = User::findOrFail($userId);
        $documents = [];
        
        // Busca documentos na nova pasta public/IMG/kyc/user_id
        $newKycPath = public_path('IMG/kyc/' . $user->id);
        
        // Busca documentos na pasta antiga (compatibilidade)
        $oldKycPath = 'kyc/' . $user->id;
        
        // Verifica se existe a nova pasta
        if (is_dir($newKycPath)) {
            $files = scandir($newKycPath);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $filePath = $newKycPath . DIRECTORY_SEPARATOR . $file;
                if (is_file($filePath)) {
                    if (strpos($file, 'document_front') !== false) {
                        $documents['front'] = 'IMG/kyc/' . $user->id . '/' . $file;
                    } elseif (strpos($file, 'document_back') !== false) {
                        $documents['back'] = 'IMG/kyc/' . $user->id . '/' . $file;
                    } elseif (strpos($file, 'selfie') !== false && strpos($file, 'facial_biometrics') === false) {
                        $documents['selfie'] = 'IMG/kyc/' . $user->id . '/' . $file;
                    } elseif (strpos($file, 'cnpj_proof') !== false) {
                        $documents['cnpj_proof'] = 'IMG/kyc/' . $user->id . '/' . $file;
                    } elseif (strpos($file, 'facial_biometrics') !== false) {
                        $documents['biometric'] = 'IMG/kyc/' . $user->id . '/' . $file;
                    }
                }
            }
        }
        
        // Se não encontrou na nova pasta, busca na antiga (compatibilidade)
        if (empty($documents) && Storage::disk('local')->exists($oldKycPath)) {
            $files = Storage::disk('local')->files($oldKycPath);
            
            foreach ($files as $file) {
                $basename = basename($file);
                if (strpos($basename, 'document_front') !== false) {
                    $documents['front'] = $file;
                } elseif (strpos($basename, 'document_back') !== false) {
                    $documents['back'] = $file;
                } elseif (strpos($basename, 'selfie') !== false && strpos($basename, 'facial_biometrics') === false) {
                    $documents['selfie'] = $file;
                } elseif (strpos($basename, 'cnpj_proof') !== false) {
                    $documents['cnpj_proof'] = $file;
                } elseif (strpos($basename, 'facial_biometrics') !== false) {
                    $documents['biometric'] = $file;
                }
            }
        }
        
        // Também verifica se o usuário tem facial_biometrics no banco de dados
        if ($user->facial_biometrics && strpos($user->facial_biometrics, 'IMG/kyc/') === 0) {
            $biometricPath = public_path($user->facial_biometrics);
            if (file_exists($biometricPath)) {
                $documents['biometric'] = $user->facial_biometrics;
            }
        }
        
        return view('admin.kyc.view', compact('user', 'documents'));
    }

    /**
     * Atualiza status de ativação do gateway (PIX ou Cartão)
     *
     * @param Request $request
     * @param int $configId
     * @return \Illuminate\Http\JsonResponse|RedirectResponse
     */
    public function toggleGatewayStatus(Request $request, int $configId)
    {
        $request->validate([
            'field' => 'required|in:is_active_for_pix,is_active_for_card',
            'value' => 'required|boolean',
        ]);

        $config = SystemGatewayConfig::findOrFail($configId);
        $config->update([
            $request->field => $request->boolean('value'),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Status atualizado com sucesso!');
    }

    /**
     * Resumo Financeiro Admin
     *
     * @return View
     */
    public function financial(): View
    {
        // Lucro Total
        $totalProfit = Transaction::where('status', 'completed')->sum('fee');
        
        // Saldo Total dos Usuários
        $totalUserBalance = Wallet::sum('balance');
        
        // Total de Saques
        $totalWithdrawals = Withdrawal::where('status', 'completed')->sum('amount');
        
        // Total de Transações (entrada)
        $totalDeposits = Transaction::where('status', 'completed')
            ->whereIn('type', ['pix', 'credit', 'deposit'])
            ->sum('amount_gross');
        
        // Saques Pendentes
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->sum('amount');
        
        return view('admin.financial.index', compact(
            'totalProfit',
            'totalUserBalance',
            'totalWithdrawals',
            'totalDeposits',
            'pendingWithdrawals'
        ));
    }

    /**
     * Criar Transação Admin
     *
     * @return View
     */
    public function createTransaction(): View
    {
        $users = User::where('is_admin', false)->get();
        return view('admin.transactions.create', compact('users'));
    }
}

