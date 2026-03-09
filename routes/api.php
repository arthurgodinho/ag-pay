<?php

use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\TransactionApiController;
// use App\Http\Controllers\Api\WebhookApiController;
use App\Http\Controllers\Api\CashinApiController;
use App\Http\Controllers\Api\CashoutApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas de API protegidas por token
Route::middleware('api.token')->prefix('v1')->group(function () {
    
    // Pagamentos
    Route::post('/payments/pix', [PaymentApiController::class, 'createPix'])->name('api.payments.pix');
    Route::post('/payments/credit-card', [PaymentApiController::class, 'createCreditCard'])->name('api.payments.credit-card');
    Route::get('/payments/{uuid}', [PaymentApiController::class, 'getPayment'])->name('api.payments.get');
    
    // Cashin (Depósitos)
    Route::post('/cashin/pix', [\App\Http\Controllers\Api\CashinApiController::class, 'createPix'])->name('api.cashin.pix');
    
    // Cashout (Saques)
    Route::post('/cashout/pix', [\App\Http\Controllers\Api\CashoutApiController::class, 'createPix'])->name('api.cashout.pix');
    
    // Transações
    Route::get('/transactions', [TransactionApiController::class, 'index'])->name('api.transactions.index');
    Route::get('/transactions/{uuid}', [TransactionApiController::class, 'show'])->name('api.transactions.show');
    
    // Webhooks (para configurar) - REMOVIDO
    // Route::get('/webhooks', [WebhookApiController::class, 'index'])->name('api.webhooks.index');
    // Route::post('/webhooks', [WebhookApiController::class, 'store'])->name('api.webhooks.store');
    // Route::delete('/webhooks/{id}', [WebhookApiController::class, 'destroy'])->name('api.webhooks.destroy');
});

// Webhook público (recebe notificações dos gateways)
// Route::post('/webhooks/gateway', [WebhookApiController::class, 'receive'])->name('api.webhooks.receive');

// Webhook público para receber MED/Chargeback do adquirente
Route::post('/webhooks/chargeback', [\App\Http\Controllers\Api\WebhookController::class, 'chargeback'])->name('api.webhooks.chargeback');

// Webhook público para receber notificações da BsPay
Route::post('/webhooks/bspay', [\App\Http\Controllers\Api\WebhookController::class, 'bspay'])->name('api.webhooks.bspay');

// Webhook público para receber notificações da Venit
Route::post('/webhooks/venit', [\App\Http\Controllers\Api\WebhookController::class, 'venit'])->name('api.webhooks.venit');

// Webhook público para receber notificações da PodPay
Route::post('/webhooks/podpay', [\App\Http\Controllers\Api\WebhookController::class, 'podpay'])->name('api.webhooks.podpay');

// Webhook público para receber notificações da HyperCash
Route::post('/webhooks/hypercash', [\App\Http\Controllers\Api\WebhookController::class, 'hypercash'])->name('api.webhooks.hypercash');

// Webhook público para receber notificações da Efi Bank
Route::post('/webhooks/efi', [\App\Http\Controllers\Api\WebhookController::class, 'efi'])->name('api.webhooks.efi');

// Webhook público para receber notificações da PagueMax
Route::post('/webhooks/paguemax', [\App\Http\Controllers\Api\WebhookController::class, 'paguemax'])->name('api.webhooks.paguemax');

// Webhook público para receber notificações da Pluggou
Route::post('/webhooks/pluggou', [\App\Http\Controllers\Api\WebhookController::class, 'pluggou'])->name('api.webhooks.pluggou');

// Webhook público para receber notificações da Asaas
Route::post('/webhooks/asaas', [\App\Http\Controllers\Api\WebhookController::class, 'asaas'])->name('api.webhooks.asaas');

// Webhook público para receber notificações da ZoomPag
Route::post('/webhooks/zoompag', [\App\Http\Controllers\Api\WebhookController::class, 'zoompag'])->name('api.webhooks.zoompag');

// Webhook público para receber notificações da Pagar.me
Route::post('/webhooks/pagarme', [\App\Http\Controllers\Api\WebhookController::class, 'pagarme'])->name('api.webhooks.pagarme');



