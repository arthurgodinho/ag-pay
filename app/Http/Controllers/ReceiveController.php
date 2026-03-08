<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Setting;
use App\Models\SystemGatewayConfig;
use App\Services\Gateways\GatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ReceiveController extends Controller
{
    /**
     * Exibe a página de recebimento manual
     *
     * @return View
     */
    public function index(): View
    {
        return view('dashboard.receive.index');
    }

    /**
     * Gera QR Code Pix
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateQrCode(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Valida valor mínimo de depósito
        $depositMinValue = floatval(Setting::get('deposit_min_value', '5.00'));
        
        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $depositMinValue],
        ], [
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

        // Tenta usar o gateway configurado para cash-in PIX especificamente
        $userPreferredGateway = $user->preferred_gateway;
        $gatewayForCashinPix = Setting::get('default_gateway_for_cashin_pix', '');
        
        // Log antes de buscar o gateway
        Log::info('ReceiveController: Iniciando busca de gateway para cash-in PIX', [
            'user_id' => $user->id,
            'user_preferred_gateway' => $userPreferredGateway,
            'gateway_configurado_cashin' => $gatewayForCashinPix,
        ]);
        
        // Primeiro tenta o gateway específico para cash-in PIX
        $gatewayConfig = null;
        if (!empty($gatewayForCashinPix)) {
            $gatewayConfig = SystemGatewayConfig::where('provider_name', $gatewayForCashinPix)
                ->where('is_active_for_pix', true)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('client_id')->where('client_id', '!=', '');
                    })->orWhereIn('provider_name', ['hypercash', 'zoompag']);
                })
                ->whereNotNull('client_secret')
                ->where('client_secret', '!=', '')
                ->first();
        }
        
        // Se não encontrou, usa o método padrão
        if (!$gatewayConfig) {
            $gatewayConfig = SystemGatewayConfig::getDefaultForCashinPix($userPreferredGateway);
        }
        
        // Log após buscar o gateway
        Log::info('ReceiveController: Gateway encontrado', [
            'gateway_found' => $gatewayConfig ? true : false,
            'gateway_name' => $gatewayConfig ? $gatewayConfig->provider_name : null,
            'gateway_id' => $gatewayConfig ? $gatewayConfig->id : null,
            'has_client_id' => $gatewayConfig ? !empty($gatewayConfig->client_id) : false,
            'has_client_secret' => $gatewayConfig ? !empty($gatewayConfig->client_secret) : false,
            'is_active_for_pix' => $gatewayConfig ? $gatewayConfig->is_active_for_pix : false,
        ]);
        
        if (!$gatewayConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum gateway PIX configurado. Por favor, configure um gateway no painel administrativo.',
            ], 400);
        }

        if (empty($gatewayConfig->client_secret) || (!in_array($gatewayConfig->provider_name, ['hypercash', 'zoompag']) && empty($gatewayConfig->client_id))) {
            return response()->json([
                'success' => false,
                'message' => 'Keys de pagamentos não configuradas. As credenciais do gateway não estão configuradas. Por favor, configure as credenciais no painel administrativo.',
            ], 400);
        }

        try {
            $gateway = GatewayFactory::make(
                $gatewayConfig->provider_name,
                $gatewayConfig->client_id,
                $gatewayConfig->client_secret
            );

            // Determina o postback URL baseado no gateway
            $postbackRoute = 'api.webhooks.bspay'; // Default
            if ($gatewayConfig->provider_name === 'venit') {
                $postbackRoute = 'api.webhooks.venit';
            } elseif ($gatewayConfig->provider_name === 'asaas') {
                $postbackRoute = 'api.webhooks.asaas';
            } elseif ($gatewayConfig->provider_name === 'pluggou') {
                $postbackRoute = 'api.webhooks.pluggou';
            } elseif ($gatewayConfig->provider_name === 'zoompag') {
                $postbackRoute = 'api.webhooks.zoompag';
            }

            $payerData = [
                'name' => $user->name,
                'email' => $user->email,
                'cpf' => $user->cpf_cnpj,
                'external_id' => $transactionId->toString(),
                'postback_url' => \App\Helpers\WebhookUrlHelper::generateUrl($postbackRoute), // URL absoluta com domínio do .env
                'description' => 'Depósito via PIX',
            ];

            Log::info('ReceiveController: Chamando gateway createPix', [
                'gateway' => $gatewayConfig->provider_name,
                'amount' => $amountGross,
                'payer_data' => $payerData,
            ]);

            $response = $gateway->createPix($amountGross, $payerData);
            
            Log::info('ReceiveController: Resposta do gateway', [
                'response_keys' => array_keys($response),
                'has_qr_code' => isset($response['qr_code']),
                'has_raw_response' => isset($response['raw_response']),
                'response' => $response,
            ]);

            // Obtém expires_at da resposta do gateway ou usa o padrão (5 minutos)
            $gatewayExpiresAt = isset($response['expires_at']) 
                ? \Carbon\Carbon::parse($response['expires_at']) 
                : $expiresAt;

            // Cria transação de depósito pendente com expires_at
            $transaction = Transaction::create([
                'uuid' => $transactionId,
                'user_id' => $user->id,
                'amount_gross' => $amountGross,
                'amount_net' => $amountNet,
                'fee' => $fee,
                'type' => 'pix',
                'status' => 'pending',
                'external_id' => $response['external_id'] ?? $transactionId->toString(),
                'gateway_provider' => $gatewayConfig->provider_name,
                'expires_at' => $gatewayExpiresAt,
            ]);

            // Obtém o QR Code e a chave PIX da resposta
            // Primeiro tenta pegar diretamente da resposta
            $qrCodeString = $response['qr_code'] ?? '';
            
            // Se não veio, tenta usar raw_response
            if (empty($qrCodeString) && isset($response['raw_response'])) {
                $rawResponse = $response['raw_response'];
                $qrCodeString = $rawResponse['qrCode'] 
                    ?? $rawResponse['qrcode'] 
                    ?? $rawResponse['qr_code']
                    ?? $rawResponse['pixCopyPaste']
                    ?? $rawResponse['pix_copy_paste']
                    ?? $rawResponse['copyPaste']
                    ?? $rawResponse['emv']
                    ?? '';
            }
            
            // Se ainda não encontrou, tenta buscar diretamente na resposta (pode estar em outro formato)
            if (empty($qrCodeString)) {
                // Tenta todas as possíveis chaves diretamente na resposta
                $qrCodeString = $response['qrCode'] 
                    ?? $response['qrcode'] 
                    ?? $response['qr_code']
                    ?? $response['pixCopyPaste']
                    ?? $response['pix_copy_paste']
                    ?? $response['copyPaste']
                    ?? $response['emv']
                    ?? $response['pix_key']
                    ?? $response['pixKey']
                    ?? '';
            }
            
            // Log para debug
            Log::info('ReceiveController: QR Code gerado', [
                'qr_code_string_length' => strlen($qrCodeString),
                'has_qr_code' => !empty($qrCodeString),
                'response_keys' => array_keys($response),
            ]);

            // Gera imagem do QR Code
            if (!empty($qrCodeString)) {
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrCodeString);
                $qrCodeImage = '<img src="' . $qrCodeUrl . '" alt="QR Code PIX" class="w-full max-w-xs mx-auto" />';
            } else {
                $qrCodeImage = '<p class="text-red-500">QR Code não disponível</p>';
            }

            return response()->json([
                'success' => true,
                'qr_code' => $qrCodeImage,
                'qr_code_string' => $qrCodeString,
                'pix_key' => $qrCodeString, // Chave PIX copia e cola
                'transaction_id' => $transactionId->toString(),
                'external_id' => $response['external_id'] ?? null,
                'amount_gross' => $amountGross,
                'amount_net' => $amountNet,
                'fee' => $fee,
                'fee_percentage' => $cashinPixPercentual,
                'expires_at' => $gatewayExpiresAt->toIso8601String(),
                'expires_in_seconds' => max(0, $gatewayExpiresAt->diffInSeconds(now())),
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar QR Code via gateway', [
                'error' => $e->getMessage(),
                'gateway' => $gatewayConfig->provider_name,
                'trace' => $e->getTraceAsString(),
            ]);

            // Retorna erro específico
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar QR Code: ' . $e->getMessage(),
            ], 500);
        }
    }
}
