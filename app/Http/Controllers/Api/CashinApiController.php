<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Gateways\GatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CashinApiController extends Controller
{
    /**
     * Cria um depósito (cashin) via PIX
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPix(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1.00',
            'payer_name' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        // Calcula taxas
        $amountGross = floatval($request->amount);
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

        // Gera UUID único
        $transactionId = Str::uuid();

        // Obtém gateway configurado para cash-in PIX
        $userPreferredGateway = $user->preferred_gateway;
        $gatewayConfig = \App\Models\SystemGatewayConfig::getDefaultForCashinPix($userPreferredGateway);
        
        if (!$gatewayConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum gateway PIX configurado.',
            ], 400);
        }

        if (empty($gatewayConfig->client_secret) || (!in_array($gatewayConfig->provider_name, ['hypercash', 'zoompag']) && empty($gatewayConfig->client_id))) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais do gateway não configuradas.',
            ], 400);
        }

        try {
            $gateway = GatewayFactory::make(
                $gatewayConfig->provider_name,
                $gatewayConfig->client_id,
                $gatewayConfig->client_secret
            );

            $externalIdForGateway = $transactionId->toString();
            
            // Determina o postback URL baseado no gateway
            $postbackRoute = 'api.webhooks.bspay'; // Default
            if ($gatewayConfig->provider_name === 'venit') {
                $postbackRoute = 'api.webhooks.venit';
            } elseif ($gatewayConfig->provider_name === 'asaas') {
                $postbackRoute = 'api.webhooks.asaas';
            } elseif ($gatewayConfig->provider_name === 'pluggou') {
                $postbackRoute = 'api.webhooks.pluggou';
            } elseif ($gatewayConfig->provider_name === 'paguemax') {
                $postbackRoute = 'api.webhooks.paguemax';
            } elseif ($gatewayConfig->provider_name === 'zoompag') {
                $postbackRoute = 'api.webhooks.zoompag';
            }
            
            $payerData = [
                'name' => $request->payer_name ?? $user->name,
                'email' => $user->email,
                'cpf' => $user->cpf_cnpj,
                'phone' => $user->phone,
                'external_id' => $externalIdForGateway,
                'postback_url' => \App\Helpers\WebhookUrlHelper::generateUrl($postbackRoute), // URL absoluta com domínio do .env
                'description' => 'Depósito via PIX - API',
            ];

            // Cria QR Code PIX
            $response = $gateway->createPix($amountGross, $payerData);
            
            if (!is_array($response)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resposta inválida do gateway.',
                ], 500);
            }

            // Extrai QR Code
            $qrCodeString = $response['qr_code'] 
                ?? $response['qrCode'] 
                ?? $response['qrcode'] 
                ?? $response['pixCopyPaste'] 
                ?? $response['pix_copy_paste'] 
                ?? $response['copyPaste'] 
                ?? $response['emv'] 
                ?? '';

            if (empty($qrCodeString) && isset($response['raw_response'])) {
                $raw = $response['raw_response'];
                $qrCodeString = $raw['qrCode'] 
                    ?? $raw['qrcode'] 
                    ?? $raw['qr_code'] 
                    ?? $raw['pixCopyPaste'] 
                    ?? $raw['pix_copy_paste'] 
                    ?? $raw['copyPaste'] 
                    ?? $raw['emv'] 
                    ?? '';
            }

            if (empty($qrCodeString)) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code não foi retornado pelo gateway.',
                ], 400);
            }

            // Cria transação
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'uuid' => $transactionId,
                'type' => 'pix',
                'amount_gross' => $amountGross,
                'amount_net' => $amountNet,
                'fee' => $fee,
                'status' => 'pending',
                'gateway_provider' => $gatewayConfig->provider_name,
                'external_id' => $response['external_id'] ?? $externalIdForGateway,
            ]);

            // Data de expiração
            $expiresAt = now()->addMinutes(5);

            return response()->json([
                'success' => true,
                'transaction' => [
                    'uuid' => $transaction->uuid,
                    'external_id' => $transaction->external_id,
                    'amount_gross' => $amountGross,
                    'amount_net' => $amountNet,
                    'fee' => $fee,
                    'status' => $transaction->status,
                    'expires_at' => $expiresAt->toIso8601String(),
                ],
                'qr_code' => $qrCodeString,
                'qr_code_image_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrCodeString),
            ]);
        } catch (\Exception $e) {
            Log::error('CashinApiController: Erro ao criar depósito', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar depósito: ' . $e->getMessage(),
            ], 500);
        }
    }
}
