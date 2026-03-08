<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\Setting;
use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashoutApiController extends Controller
{
    /**
     * Cria um saque (cashout) via PIX
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPix(Request $request): JsonResponse
    {
        // Valor mínimo de saque
        $withdrawalMinValue = floatval(Setting::get('withdrawal_min_value', '10.00'));
        
        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $withdrawalMinValue],
            'pix_key' => 'required|string|min:10',
        ], [
            'amount.min' => "O valor mínimo para saque é R$ " . number_format($withdrawalMinValue, 2, ',', '.'),
        ]);

        $user = $request->user();

        // Verifica se o usuário está aprovado
        if (!$user->is_approved || $user->kyc_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Você precisa estar aprovado para realizar saques. Complete seu cadastro enviando os documentos para verificação (KYC).',
            ], 403);
        }

        // Verifica se o usuário pode sacar
        if ($user->bloquear_saque) {
            return response()->json([
                'success' => false,
                'message' => 'Seus saques estão bloqueados. Entre em contato com o suporte.',
            ], 403);
        }

        // O valor informado é o valor líquido desejado
        $desiredNetAmount = floatval($request->amount);

        // Calcula taxas
        // SEMPRE usa as taxas do painel
        $cashoutPixFixo = $user->getCashoutPixFixo();
        $cashoutPixPercentual = $user->getCashoutPixPercentual();
        $cashoutPixMinima = floatval(Setting::get('cashout_pix_minima', '0.80'));

        // Calcula valor bruto necessário
        $amountGross = $desiredNetAmount;
        $maxIterations = 10;
        $tolerance = 0.01;
        
        for ($i = 0; $i < $maxIterations; $i++) {
            $feePercentual = ($amountGross * $cashoutPixPercentual) / 100;
            $fee = max($feePercentual, $cashoutPixMinima) + $cashoutPixFixo;
            $calculatedNet = $amountGross - $fee;
            
            if (abs($calculatedNet - $desiredNetAmount) <= $tolerance) {
                break;
            }
            
            $difference = $desiredNetAmount - $calculatedNet;
            $amountGross += $difference;
        }
        
        // Recalcula valores finais
        $feePercentual = ($amountGross * $cashoutPixPercentual) / 100;
        $fee = max($feePercentual, $cashoutPixMinima) + $cashoutPixFixo;
        $amountNet = $amountGross - $fee;

        // Arredonda
        $amountGross = round($amountGross, 2);
        $fee = round($fee, 2);
        $amountNet = round($amountNet, 2);

        // Verifica saldo
        $wallet = $user->wallet;
        if (!$wallet || $wallet->balance < $amountGross) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente. Para receber R$ ' . number_format($amountNet, 2, ',', '.') . ' líquido, você precisa de R$ ' . number_format($amountGross, 2, ',', '.') . ' (incluindo taxa de R$ ' . number_format($fee, 2, ',', '.') . ').',
            ], 400);
        }

        try {
            // Obtém o token da API usado (se disponível)
            $apiToken = null;
            if ($request->hasHeader('X-Client-ID')) {
                $clientId = $request->header('X-Client-ID');
                $apiToken = ApiToken::where('client_id', $clientId)
                    ->where('user_id', $user->id)
                    ->first();
            }

            // Verifica modo de saque
            $withdrawalMode = $apiToken?->withdrawal_mode ?? 'manual';
            $isAutomatic = $withdrawalMode === 'automatic';

            // Se for automático, verifica se há IP permitido
            if ($isAutomatic) {
                $clientIp = $request->ip();
                $hasAllowedIp = $apiToken && $apiToken->allowedIps()
                    ->where('ip_address', $clientIp)
                    ->exists();

                if (!$hasAllowedIp) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Para saques automáticos, você precisa adicionar o IP do seu servidor nas configurações da API. IP atual: ' . $clientIp,
                    ], 403);
                }
            }

            $withdrawal = null;
            
            DB::transaction(function () use ($user, $amountGross, $amountNet, $fee, $request, $isAutomatic, &$withdrawal) {
                $wallet = $user->wallet;
                if (!$wallet) {
                    $wallet = Wallet::create([
                        'user_id' => $user->id,
                        'balance' => 0.00,
                        'frozen_balance' => 0.00,
                    ]);
                }

                if ($wallet->balance < $amountGross) {
                    throw new \Exception('Saldo insuficiente');
                }

                // Bloqueia o valor
                $wallet->decrement('balance', $amountGross);
                $wallet->increment('frozen_balance', $amountGross);

                // Cria o saque
                $withdrawal = Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $amountNet,
                    'amount_gross' => $amountGross,
                    'fee' => $fee,
                    'pix_key' => $request->pix_key,
                    'status' => $isAutomatic ? 'processing' : 'pending',
                ]);

                // Se for automático, processa imediatamente
                if ($isAutomatic) {
                    $withdrawalService = new WithdrawalService();
                    $withdrawalService->processWithdrawal($withdrawal);
                }
            });

            return response()->json([
                'success' => true,
                'message' => $isAutomatic 
                    ? 'Saque criado e processado automaticamente.' 
                    : 'Saque criado com sucesso. Aguardando aprovação manual.',
                'withdrawal' => [
                    'id' => $withdrawal->id,
                    'amount' => $amountNet,
                    'amount_gross' => $amountGross,
                    'fee' => $fee,
                    'status' => $withdrawal->status,
                    'pix_key' => $withdrawal->pix_key,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('CashoutApiController: Erro ao criar saque', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar saque: ' . $e->getMessage(),
            ], 500);
        }
    }
}
