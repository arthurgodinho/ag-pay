<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\Setting;
use App\Models\SystemGatewayConfig;
use App\Services\Gateways\GatewayFactory;
use App\Helpers\WebhookUrlHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(): View
    {
        $products = Product::where('user_id', Auth::id())->latest()->get();

        // Calculate stats for checkout products
        $checkoutTransactions = Transaction::where('user_id', Auth::id())
            ->whereNotNull('product_id')
            ->get();
            
        $stats = [
            'total_received' => $checkoutTransactions->whereIn('status', ['paid', 'completed'])->sum('amount_net'),
            'confirmed_sales' => $checkoutTransactions->whereIn('status', ['paid', 'completed'])->count(),
            'pix_generated' => $checkoutTransactions->where('type', 'pix')->count(),
            'pending_payments' => $checkoutTransactions->where('status', 'pending')->count(),
        ];

        return view('dashboard.checkout.index', compact('products', 'stats'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('dashboard.checkout.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'enable_pix' => 'required_without:enable_credit_card',
            'enable_credit_card' => 'required_without:enable_pix',
        ]);

        $data = $request->except(['_token', 'product_image_url']);
        $data['user_id'] = Auth::id();
        $data['uuid'] = Str::uuid();

        // Handle boolean fields
        $data['is_active'] = $request->has('is_active');
        $data['show_product_image'] = $request->has('show_product_image');
        $data['has_timer'] = $request->has('has_timer');
        $data['show_security_badges'] = $request->has('show_security_badges');
        $data['show_guarantee'] = $request->has('show_guarantee');
        $data['enable_pix'] = $request->has('enable_pix');
        $data['enable_credit_card'] = $request->has('enable_credit_card');
        
        // Handle file uploads
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('products/banners', 'public');
        }
        
        // Handle Product Image (URL or File)
        if ($request->filled('product_image_url')) {
            $data['product_image'] = $request->input('product_image_url');
        } elseif ($request->hasFile('product_image')) {
            $data['product_image'] = $request->file('product_image')->store('products/images', 'public');
        }

        if ($request->hasFile('checkout_logo')) {
            $data['checkout_logo'] = $request->file('checkout_logo')->store('products/logos', 'public');
        }

        Product::create($data);

        return redirect()->route('dashboard.checkout.index')->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id): View
    {
        $product = Product::where('uuid', $id)->where('user_id', Auth::id())->firstOrFail();
        return view('dashboard.checkout.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('uuid', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'enable_pix' => 'required_without:enable_credit_card',
            'enable_credit_card' => 'required_without:enable_pix',
        ]);

        $data = $request->except(['_token', '_method', 'product_image_url']);

        // Handle boolean fields
        $data['is_active'] = $request->has('is_active');
        $data['show_product_image'] = $request->has('show_product_image');
        $data['has_timer'] = $request->has('has_timer');
        $data['show_security_badges'] = $request->has('show_security_badges');
        $data['show_guarantee'] = $request->has('show_guarantee');

        // Handle file uploads with cleanup
        if ($request->hasFile('banner_image')) {
            if ($product->banner_image) {
                Storage::disk('public')->delete($product->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('products/banners', 'public');
        }

        // Handle Product Image (URL or File)
        if ($request->filled('product_image_url')) {
            // If replacing a file with a URL, delete the old file
            if ($product->product_image && !Str::startsWith($product->product_image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($product->product_image);
            }
            $data['product_image'] = $request->input('product_image_url');
        } elseif ($request->hasFile('product_image')) {
            // If replacing with a new file, delete the old file (if it was a file)
            if ($product->product_image && !Str::startsWith($product->product_image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($product->product_image);
            }
            $data['product_image'] = $request->file('product_image')->store('products/images', 'public');
        }

        if ($request->hasFile('checkout_logo')) {
            if ($product->checkout_logo) {
                Storage::disk('public')->delete($product->checkout_logo);
            }
            $data['checkout_logo'] = $request->file('checkout_logo')->store('products/logos', 'public');
        }
        
        // Handle boolean fields that might be missing from request if unchecked
        $data['is_active'] = $request->has('is_active');
        $data['show_product_image'] = $request->has('show_product_image');
        $data['show_security_badges'] = $request->has('show_security_badges');
        $data['show_guarantee'] = $request->has('show_guarantee');
        $data['has_timer'] = $request->has('has_timer');
        $data['has_social_proof'] = $request->has('has_social_proof');
        $data['order_bump_active'] = $request->has('order_bump_active');
        $data['use_default_thankyou_page'] = $request->has('use_default_thankyou_page');
        $data['enable_pix'] = $request->has('enable_pix');
        $data['enable_credit_card'] = $request->has('enable_credit_card');

        $product->update($data);

        return redirect()->route('dashboard.checkout.index')->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::where('uuid', $id)->where('user_id', Auth::id())->firstOrFail();
        
        // Delete associated files
        if ($product->banner_image) {
            Storage::disk('public')->delete($product->banner_image);
        }
        if ($product->product_image && !Str::startsWith($product->product_image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($product->product_image);
        }
        if ($product->checkout_logo) {
            Storage::disk('public')->delete($product->checkout_logo);
        }

        $product->delete();
        return redirect()->route('dashboard.checkout.index')->with('success', 'Produto removido com sucesso!');
    }

    /**
     * Show the public checkout page.
     */
    public function showPublicCheckout($uuid)
    {
        $product = Product::where('uuid', $uuid)->where('is_active', true)->firstOrFail();
        return view('checkout.payment', compact('product'));
    }

    /**
     * Process the payment for the public checkout.
     */
    public function processPayment(Request $request, $uuid): JsonResponse
    {
        $product = Product::where('uuid', $uuid)->where('is_active', true)->firstOrFail();
        $user = $product->user; // The merchant

        $request->validate([
            'payment_method' => 'required|in:pix,credit_card',
            'name' => 'required|string',
            'email' => 'required|email',
            'cpf' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($request->payment_method === 'credit_card') {
            $request->validate([
                'card_number' => 'required',
                'card_expiration' => 'required',
                'card_cvv' => 'required',
                'card_holder' => 'required',
            ]);
        }

        $amountGross = floatval($product->price);

        // Fee Calculation
        if ($request->payment_method === 'pix') {
            $fixedFee = $user->getCashinPixFixo();
            $percentFee = $user->getCashinPixPercentual();
            $minFee = floatval(Setting::get('cashin_pix_minima', '0.00'));
        } else {
            $fixedFee = $user->getCashinCardFixo();
            $percentFee = $user->getCashinCardPercentual();
            $minFee = floatval(Setting::get('cashin_card_minima', '0.00'));
        }

        $feePercentual = ($amountGross * $percentFee) / 100;
        $fee = max($feePercentual, $minFee) + $fixedFee;
        $amountNet = $amountGross - $fee;

        // Rounding
        $amountGross = round($amountGross, 2);
        $fee = round($fee, 2);
        $amountNet = round($amountNet, 2);

        $transactionId = Str::uuid();

        // Gateway Selection
        $userPreferredGateway = $user->preferred_gateway;
        if ($request->payment_method === 'pix') {
            $gatewayConfig = SystemGatewayConfig::getDefaultForCheckoutPix();
            
            \Log::info('CheckoutController: Buscando gateway para checkout PIX', [
                'found' => $gatewayConfig ? true : false,
                'provider' => $gatewayConfig ? $gatewayConfig->provider_name : null,
                'setting_checkout' => Setting::get('default_gateway_for_checkout_pix'),
                'setting_pix' => Setting::get('default_gateway_for_pix'),
            ]);

            // Fallback to general PIX default if specific checkout one is not set
            if (!$gatewayConfig) {
                $gatewayConfig = SystemGatewayConfig::getDefaultForPix();
                \Log::info('CheckoutController: Fallback para gateway padrão PIX', [
                    'found' => $gatewayConfig ? true : false,
                    'provider' => $gatewayConfig ? $gatewayConfig->provider_name : null,
                ]);
            }
        } else {
            $gatewayConfig = SystemGatewayConfig::getDefaultForCheckoutCard();
        }

        if (!$gatewayConfig) {
            \Log::error('CheckoutController: Nenhum gateway configurado', [
                'payment_method' => $request->payment_method,
                'settings' => [
                    'checkout_pix' => Setting::get('default_gateway_for_checkout_pix'),
                    'default_pix' => Setting::get('default_gateway_for_pix'),
                ]
            ]);
            return response()->json(['success' => false, 'message' => 'Gateway não configurado.'], 400);
        }

        // Create Gateway Instance
        try {
            $gateway = GatewayFactory::make(
                $gatewayConfig->provider_name,
                $gatewayConfig->client_id,
                $gatewayConfig->client_secret
            );

            $externalIdForGateway = $transactionId->toString();
            
            // Determine postback URL based on gateway
            $postbackRoute = 'api.webhooks.bspay'; // Default
            if ($gatewayConfig->provider_name === 'venit') {
                $postbackRoute = 'api.webhooks.venit';
            } elseif ($gatewayConfig->provider_name === 'podpay') {
                $postbackRoute = 'api.webhooks.podpay';
            } elseif ($gatewayConfig->provider_name === 'hypercash') {
                $postbackRoute = 'api.webhooks.hypercash';
            } elseif ($gatewayConfig->provider_name === 'paguemax') {
                $postbackRoute = 'api.webhooks.paguemax';
            } elseif ($gatewayConfig->provider_name === 'asaas') {
                $postbackRoute = 'api.webhooks.asaas';
            } elseif ($gatewayConfig->provider_name === 'pluggou') {
                $postbackRoute = 'api.webhooks.pluggou';
            } elseif ($gatewayConfig->provider_name === 'zoompag') {
                $postbackRoute = 'api.webhooks.zoompag';
            } elseif ($gatewayConfig->provider_name === 'pagarme') {
                $postbackRoute = 'api.webhooks.pagarme';
            }

            $postbackUrl = WebhookUrlHelper::generateUrl($postbackRoute);
            
            \Log::info('CheckoutController: URL de postback gerada', [
                'route' => $postbackRoute,
                'url' => $postbackUrl,
                'transaction_uuid' => $transactionId->toString()
            ]);

            $payerData = [
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'phone' => $request->phone,
                'external_id' => $externalIdForGateway,
                'postback_url' => $postbackUrl,
                'description' => 'Pagamento Checkout - ' . $product->name,
            ];

            if ($request->payment_method === 'pix') {
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

                    Log::error('CheckoutController: Erro ao chamar gateway->createPix()', [
                        'error' => $errorMsg,
                        'gateway' => $gatewayConfig->provider_name,
                    ]);

                    throw new \Exception('Erro no gateway: ' . $errorMsg);
                }
                
                // Helper function to ensure string value
                $ensureStringValue = function($value) {
                    if (is_string($value) && !empty(trim($value))) {
                        return trim($value);
                    } elseif (is_array($value)) {
                        if (isset($value['qrcode']) && is_string($value['qrcode'])) {
                            return trim($value['qrcode']);
                        } elseif (isset($value[0]) && is_string($value[0])) {
                            return trim($value[0]);
                        }
                        return '';
                    } elseif (is_object($value)) {
                        return json_encode($value, JSON_UNESCAPED_UNICODE);
                    } elseif (is_scalar($value)) {
                        return trim((string) $value);
                    }
                    return '';
                };

                // Extract QR Code (Robust logic from FinancialController)
                $qrCodeString = '';
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
                        
                        // Nested pix.qrcode check
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
                        $qrCodeString = trim($rawResponse);
                    }
                }

                if (empty($qrCodeString)) {
                    Log::error('CheckoutController: QR Code not found in gateway response', ['response' => $response]);
                    throw new \Exception('QR Code não retornado pelo gateway.');
                }

                $responseData = [
                    'qr_code' => $qrCodeString,
                    'qr_code_image_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrCodeString),
                    'expires_at' => now()->addMinutes(30)->toIso8601String(),
                ];
            } else {
                // Credit Card implementation
                $expirationParts = explode('/', $request->card_expiration);
                $expMonth = trim($expirationParts[0] ?? '');
                $expYear = trim($expirationParts[1] ?? '');
                
                // Ensure 4-digit year
                if (strlen($expYear) === 2) {
                    $expYear = '20' . $expYear;
                }

                $cardData = [
                    'number' => preg_replace('/\D/', '', $request->card_number),
                    'holder_name' => $request->card_holder,
                    'expiration_month' => $expMonth,
                    'expiration_year' => $expYear,
                    'cvv' => $request->card_cvv,
                    'installments' => $request->installments ?? 1,
                ];

                try {
                    $response = $gateway->createCreditCard($amountGross, $cardData, $payerData);
                    
                    $responseData = $response;
                } catch (\Exception $e) {
                     Log::error('CheckoutController: Credit Card Error', ['error' => $e->getMessage()]);
                     throw new \Exception('Erro ao processar cartão: ' . $e->getMessage());
                }
            }

            // Transaction Creation
            $initialStatus = 'pending';
            if (isset($response['status']) && in_array($response['status'], ['paid', 'completed', 'approved'])) {
                $initialStatus = 'paid'; // Or 'completed' depending on your system enum
            }

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'uuid' => $transactionId,
                'type' => $request->payment_method,
                'amount_gross' => $amountGross,
                'amount_net' => $amountNet,
                'fee' => $fee,
                'status' => $initialStatus, 
                'gateway_provider' => $gatewayConfig->provider_name,
                'external_id' => $response['external_id'] ?? $externalIdForGateway,
                'payer_name' => $request->name,
                'payer_email' => $request->email,
                'payer_cpf' => $request->cpf,
            ]);

            Log::info('CheckoutController: PIX Data Returned', ['pixData' => $responseData, 'transaction_id' => $transaction->id]);

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'data' => $responseData,
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout Payment Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Erro ao processar pagamento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check transaction status (public route for checkout polling).
     */
    public function checkPaymentStatus($uuid): JsonResponse
    {
        $transaction = Transaction::where('uuid', $uuid)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transação não encontrada.'
            ], 404);
        }

        // Mapear status aceitos como "pago/completado"
        $isCompleted = in_array($transaction->status, ['completed', 'paid', 'approved']);

        return response()->json([
            'success' => true,
            'status' => $transaction->status,
            'completed' => $isCompleted,
            'amount_gross' => $transaction->amount_gross,
            'type' => $transaction->type,
        ]);
    }
}

