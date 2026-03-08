<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminLandingPageController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminStaticPageController;
use App\Http\Controllers\AdminSupportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminWithdrawalController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\IntegrationsController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiveController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SystemConfigController;
use App\Http\Controllers\WebhooksController;
use Illuminate\Support\Facades\Route;

// Landing Page Pública
Route::get('/', function() {
    // Buscar taxas detalhadas do sistema (novas configurações)
    $cashinPixFixo = \App\Models\Setting::get('cashin_pix_fixo', '1.00');
    $cashinPixPercentual = \App\Models\Setting::get('cashin_pix_percentual', '3.00');
    $cashinCardFixo = \App\Models\Setting::get('cashin_card_fixo', '1.00');
    $cashinCardPercentual = \App\Models\Setting::get('cashin_card_percentual', '4.00');
    
    $cashoutPixFixo = \App\Models\Setting::get('cashout_pix_fixo', '0.00');
    $cashoutPixPercentual = \App\Models\Setting::get('cashout_pix_percentual', '5.00');
    $cashoutPixMinima = \App\Models\Setting::get('cashout_pix_minima', '0.80');
    
    // Para exibição na landing page, vamos usar as taxas PIX como padrão
    // (ou podemos mostrar ambas, mas por enquanto vamos usar PIX)
    $cashinFixo = $cashinPixFixo;
    $cashinPercentual = $cashinPixPercentual;
    $cashoutFixo = $cashoutPixFixo;
    $cashoutPercentual = $cashoutPixPercentual;
    
    // Buscar configurações de efeitos
    $effectMode = \App\Models\LandingPageSetting::get('landing_effect_mode', 'default'); // 'default' ou 'christmas'
    
    // Buscar configurações da landing page
    $landingSettings = [
        'logo' => \App\Models\LandingPageSetting::get('logo', ''),
        'favicon' => \App\Models\LandingPageSetting::get('favicon', ''),
        'meta_title' => \App\Models\LandingPageSetting::get('meta_title', 'PagueMax - Gateway de Pagamentos e Plataforma Whitelabel'),
        'meta_description' => \App\Models\LandingPageSetting::get('meta_description', 'Plataforma completa de pagamentos para usuários e gateways. Processe pagamentos PIX e Cartão com taxas competitivas.'),
        'hero_badge' => \App\Models\LandingPageSetting::get('hero_badge', 'Plataforma de Pagamentos Completa'),
        'hero_title' => \App\Models\LandingPageSetting::get('hero_title', 'Gateway de Pagamentos para Usuários e Processadores'),
        'hero_subtitle' => \App\Models\LandingPageSetting::get('hero_subtitle', 'Ofereça pagamentos PIX e Cartão aos seus clientes ou utilize nossa infraestrutura como gateway whitelabel para processar pagamentos em larga escala. Taxas competitivas, API robusta e suporte dedicado.'),
        'hero_cta_text' => \App\Models\LandingPageSetting::get('hero_cta_text', 'Começar Agora'),
        'hero_image' => \App\Models\LandingPageSetting::get('hero_image', ''),
        'hero_stats1_value' => \App\Models\LandingPageSetting::get('hero_stats1_value', '+500'),
        'hero_stats1_label' => \App\Models\LandingPageSetting::get('hero_stats1_label', 'Empresas ativas'),
        'hero_stats2_value' => \App\Models\LandingPageSetting::get('hero_stats2_value', 'R$ 10 milhões+'),
        'hero_stats2_label' => \App\Models\LandingPageSetting::get('hero_stats2_label', 'Processado'),
        'hero_stats3_value' => \App\Models\LandingPageSetting::get('hero_stats3_value', '99,9%'),
        'hero_stats3_label' => \App\Models\LandingPageSetting::get('hero_stats3_label', 'Tempo de atividade'),
        'solutions_title' => \App\Models\LandingPageSetting::get('solutions_title', 'Soluções Completas de Pagamentos'),
        'solutions_subtitle' => \App\Models\LandingPageSetting::get('solutions_subtitle', 'Para usuários finais, marketplaces ou outros gateways que precisam processar pagamentos em escala'),
        'solution1_title' => \App\Models\LandingPageSetting::get('solution1_title', 'Para Usuários e Negócios'),
        'solution1_text' => \App\Models\LandingPageSetting::get('solution1_text', 'Receba pagamentos PIX e Cartão de forma simples e segura. Crie checkouts personalizados, gerencie produtos, configure taxas e tenha controle total sobre suas transações. Ideal para e-commerces, infoprodutores e negócios digitais.'),
        'solution2_title' => \App\Models\LandingPageSetting::get('solution2_title', 'Gateway Whitelabel'),
        'solution2_text' => \App\Models\LandingPageSetting::get('solution2_text', 'Utilize nossa infraestrutura como gateway próprio. Processe pagamentos em nome de seus clientes, configure taxas personalizadas e ofereça uma solução de pagamentos com sua marca. Ideal para fintechs, PSPs e grandes marketplaces.'),
        'pricing_title' => \App\Models\LandingPageSetting::get('pricing_title', 'Taxas Transparentes e Competitivas'),
        'pricing_subtitle' => \App\Models\LandingPageSetting::get('pricing_subtitle', 'Sem taxas escondidas, sem surpresas. Custo-benefício que faz sentido para o seu negócio'),
        'pricing_note' => \App\Models\LandingPageSetting::get('pricing_note', 'Taxas podem variar conforme volume e negociação comercial. Entre em contato para condições especiais.'),
        'features_title' => \App\Models\LandingPageSetting::get('features_title', 'Por que escolher a PagueMax?'),
        'features_subtitle' => \App\Models\LandingPageSetting::get('features_subtitle', 'Tecnologia de ponta, segurança máxima e suporte dedicado para o seu negócio crescer'),
        'feature1_title' => \App\Models\LandingPageSetting::get('feature1_title', 'Processamento Rápido'),
        'feature1_text' => \App\Models\LandingPageSetting::get('feature1_text', 'Pagamentos PIX aprovados instantaneamente. Cartões processados em segundos. Tecnologia que não faz seu cliente esperar.'),
        'feature2_title' => \App\Models\LandingPageSetting::get('feature2_title', 'Segurança Máxima'),
        'feature2_text' => \App\Models\LandingPageSetting::get('feature2_text', 'Criptografia de ponta a ponta, compliance PCI-DSS, proteção contra fraudes e chargebacks. Seus dados e transações protegidos 24/7.'),
        'feature3_title' => \App\Models\LandingPageSetting::get('feature3_title', 'API Robusta'),
        'feature3_text' => \App\Models\LandingPageSetting::get('feature3_text', 'API REST moderna e bem documentada. SDKs em múltiplas linguagens. Integração rápida e sem complicações. Webhooks em tempo real.'),
        'feature4_title' => \App\Models\LandingPageSetting::get('feature4_title', 'Suporte Dedicado'),
        'feature4_text' => \App\Models\LandingPageSetting::get('feature4_text', 'Equipe técnica especializada disponível 24/7. Onboarding personalizado, documentação detalhada e suporte técnico quando você precisar.'),
        'feature5_title' => \App\Models\LandingPageSetting::get('feature5_title', 'Gestão Financeira'),
        'feature5_text' => \App\Models\LandingPageSetting::get('feature5_text', 'Dashboard completo, relatórios detalhados, split de pagamentos automático, controle de saldo em tempo real e muito mais.'),
        'feature6_title' => \App\Models\LandingPageSetting::get('feature6_title', 'Checkout Personalizado'),
        'feature6_text' => \App\Models\LandingPageSetting::get('feature6_text', 'Crie checkouts únicos com sua marca, cores e textos. Order bumps, upsells, cupons de desconto. Tudo configurável para máxima conversão.'),
        'whitelabel_title' => \App\Models\LandingPageSetting::get('whitelabel_title', 'Gateway Whitelabel Completo'),
        'whitelabel_text' => \App\Models\LandingPageSetting::get('whitelabel_text', 'Utilize nossa infraestrutura como se fosse sua. Processe pagamentos em larga escala com sua marca, configure taxas personalizadas e ofereça uma solução completa aos seus clientes.'),
        'whitelabel_item1_title' => \App\Models\LandingPageSetting::get('whitelabel_item1_title', 'Infraestrutura Escalável'),
        'whitelabel_item1_text' => \App\Models\LandingPageSetting::get('whitelabel_item1_text', 'Processe milhões de transações com alta disponibilidade e baixa latência'),
        'whitelabel_item2_title' => \App\Models\LandingPageSetting::get('whitelabel_item2_title', 'Marca Própria'),
        'whitelabel_item2_text' => \App\Models\LandingPageSetting::get('whitelabel_item2_text', 'Todo o sistema com sua identidade visual, desde o dashboard até os emails transacionais'),
        'whitelabel_item3_title' => \App\Models\LandingPageSetting::get('whitelabel_item3_title', 'Gestão de Sub-Merchants'),
        'whitelabel_item3_text' => \App\Models\LandingPageSetting::get('whitelabel_item3_text', 'Crie e gerencie contas de clientes, configure taxas individualmente e tenha controle total'),
        'whitelabel_item4_title' => \App\Models\LandingPageSetting::get('whitelabel_item4_title', 'Relatórios e Analytics'),
        'whitelabel_item4_text' => \App\Models\LandingPageSetting::get('whitelabel_item4_text', 'Dashboards completos com métricas em tempo real e relatórios exportáveis'),
        'numbers_title' => \App\Models\LandingPageSetting::get('numbers_title', 'Números que Impressionam'),
        'numbers_subtitle' => \App\Models\LandingPageSetting::get('numbers_subtitle', 'Confiado por milhares de negócios em todo o Brasil'),
        'number1_value' => \App\Models\LandingPageSetting::get('number1_value', '+50K'),
        'number1_label' => \App\Models\LandingPageSetting::get('number1_label', 'Usuários Ativos'),
        'number2_value' => \App\Models\LandingPageSetting::get('number2_value', 'R$ 500Mi+'),
        'number2_label' => \App\Models\LandingPageSetting::get('number2_label', 'Processado Mensalmente'),
        'number3_value' => \App\Models\LandingPageSetting::get('number3_value', '5M+'),
        'number3_label' => \App\Models\LandingPageSetting::get('number3_label', 'Transações Processadas'),
        'number4_value' => \App\Models\LandingPageSetting::get('number4_value', '99.9%'),
        'number4_label' => \App\Models\LandingPageSetting::get('number4_label', 'Uptime Garantido'),
        'faq_title' => \App\Models\LandingPageSetting::get('faq_title', 'Perguntas Frequentes'),
        'faq_subtitle' => \App\Models\LandingPageSetting::get('faq_subtitle', 'Tire suas dúvidas sobre nossa plataforma'),
        'faq1_question' => \App\Models\LandingPageSetting::get('faq1_question', 'Como funciona a plataforma PagueMax?'),
        'faq1_answer' => \App\Models\LandingPageSetting::get('faq1_answer', 'A PagueMax é uma plataforma completa de pagamentos que atende tanto usuários finais quanto outros gateways. Você pode receber pagamentos diretamente através de checkouts personalizados ou utilizar nossa infraestrutura como gateway whitelabel para processar pagamentos em larga escala com sua própria marca.'),
        'faq2_question' => \App\Models\LandingPageSetting::get('faq2_question', 'Quais métodos de pagamento são aceitos?'),
        'faq2_answer' => \App\Models\LandingPageSetting::get('faq2_answer', 'Aceitamos pagamentos via PIX (aprovado instantaneamente) e Cartão de Crédito (Visa, Mastercard, Elo e outras bandeiras). Todos os pagamentos são processados com segurança de nível bancário.'),
        'faq3_question' => \App\Models\LandingPageSetting::get('faq3_question', 'Como funciona o gateway whitelabel?'),
        'faq3_answer' => \App\Models\LandingPageSetting::get('faq3_answer', 'Com o gateway whitelabel, você utiliza toda nossa infraestrutura com sua própria marca. Você pode criar sub-merchants (clientes), configurar taxas personalizadas, ter dashboards com sua identidade visual e oferecer uma solução completa de pagamentos como se fosse sua própria plataforma.'),
        'faq4_question' => \App\Models\LandingPageSetting::get('faq4_question', 'Qual o tempo de liberação dos valores?'),
        'faq4_answer' => \App\Models\LandingPageSetting::get('faq4_answer', 'Pagamentos PIX são creditados instantaneamente após a confirmação. Pagamentos com cartão de crédito têm um período de liberação configurável (padrão de 5 dias úteis) para maior segurança contra chargebacks.'),
        'faq5_question' => \App\Models\LandingPageSetting::get('faq5_question', 'Existe taxa de adesão ou mensalidade?'),
        'faq5_answer' => \App\Models\LandingPageSetting::get('faq5_answer', 'Não! Não cobramos taxa de adesão, mensalidade ou custo fixo. Você paga apenas as taxas sobre as transações realizadas. A criação de conta é totalmente gratuita.'),
        'faq6_question' => \App\Models\LandingPageSetting::get('faq6_question', 'Como funciona a integração via API?'),
        'faq6_answer' => \App\Models\LandingPageSetting::get('faq6_answer', 'Nossa API REST é simples e bem documentada. Você recebe um token de autenticação, faz requisições HTTP para criar pagamentos, consultar status e receber webhooks em tempo real. Temos exemplos de código em PHP, Python, JavaScript e outras linguagens na nossa documentação completa.'),
        'cta_title' => \App\Models\LandingPageSetting::get('cta_title', 'Pronto para Começar?'),
        'cta_text' => \App\Models\LandingPageSetting::get('cta_text', 'Crie sua conta gratuitamente e comece a receber pagamentos em minutos. Sem complicação, sem burocracia.'),
        'about_title' => \App\Models\LandingPageSetting::get('about_title', ''),
        'about_text' => \App\Models\LandingPageSetting::get('about_text', ''),
        'about_image' => \App\Models\LandingPageSetting::get('about_image', ''),
        'footer_text' => \App\Models\LandingPageSetting::get('footer_text', '© 2026 PagueMax. Todos os direitos reservados.'),
        'whatsapp_number' => \App\Models\LandingPageSetting::get('whatsapp_number', ''),
    ];
    
    return view('welcome', compact(
        'cashinFixo', 'cashinPercentual', 'cashoutFixo', 'cashoutPercentual',
        'cashinPixFixo', 'cashinPixPercentual', 'cashinCardFixo', 'cashinCardPercentual',
        'cashoutPixFixo', 'cashoutPixPercentual', 'cashoutPixMinima',
        'landingSettings', 'effectMode'
    ));
})->name('landing.index');

// Rota de mudança de idioma (pública)
Route::get('/language/{locale}', [\App\Http\Controllers\LanguageController::class, 'changeLanguage'])->name('language.change');

// Documentação da API (Pública)
Route::get('/documentacao', function () {
    $landingSettings = [
        'logo' => \App\Models\LandingPageSetting::get('logo', ''),
        'favicon' => \App\Models\LandingPageSetting::get('favicon', ''),
    ];
    return view('landing.documentation', compact('landingSettings'));
})->name('landing.documentation');

// Página Baixe Nosso APP
Route::get('/baixe-nosso-app', function() {
    $landingSettings = [
        'logo' => \App\Models\LandingPageSetting::get('logo', ''),
        'favicon' => \App\Models\LandingPageSetting::get('favicon', ''),
        'meta_title' => 'Baixe Nosso APP - ' . \App\Helpers\LogoHelper::getSystemName(),
        'app_title' => \App\Models\LandingPageSetting::get('app_title', 'Seu Banco na palma da sua mão.'),
        'app_subtitle' => \App\Models\LandingPageSetting::get('app_subtitle', 'Gerencie suas vendas, acompanhe seu saldo em tempo real e realize saques instantâneos onde quer que você esteja. O app do ' . \App\Helpers\LogoHelper::getSystemName() . ' é completo, seguro e ultra-rápido.'),
        'app_playstore_url' => \App\Models\LandingPageSetting::get('app_playstore_url', '#'),
    ];
    return view('landing.app', compact('landingSettings'));
})->name('landing.app');

// Rotas de Autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas de Páginas Estáticas (públicas)
Route::get('/termos-uso', [StaticPageController::class, 'termos'])->name('static.termos');
Route::get('/privacidade', [StaticPageController::class, 'privacidade'])->name('static.privacidade');
Route::get('/pld', [StaticPageController::class, 'pld'])->name('static.pld');
Route::get('/manual-kyc', [StaticPageController::class, 'manualKyc'])->name('static.manual-kyc');
Route::get('/pagina/{slug}', [StaticPageController::class, 'show'])->name('static.show');

// Rota Pública de Checkout
Route::get('/pay/{uuid}', [\App\Http\Controllers\CheckoutController::class, 'showPublicCheckout'])->name('checkout.public');
Route::post('/pay/{uuid}/process', [\App\Http\Controllers\CheckoutController::class, 'processPayment'])->name('checkout.process');

// Fallback/Safety route for incorrect POST requests to /pay/{uuid}
Route::post('/pay/{uuid}', [\App\Http\Controllers\CheckoutController::class, 'processPayment']);

// Rotas Protegidas (requerem autenticação)
Route::middleware(['auth', 'session.timeout'])->group(function () {
    // Rotas de KYC (Wizard Progressivo)
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::post('/kyc/address', [KycController::class, 'storeAddress'])->name('kyc.storeAddress');
    Route::post('/kyc/docs', [KycController::class, 'storeDocs'])->name('kyc.storeDocs');
    Route::post('/kyc/biometrics', [KycController::class, 'storeBiometrics'])->name('kyc.storeBiometrics');
    Route::get('/kyc/search-cep/{cep}', [KycController::class, 'searchCep'])->name('kyc.searchCep');
    
    // Rota antiga de KYC (mantida para compatibilidade)
    Route::put('/kyc', [KycController::class, 'update'])->name('kyc.update');
    
    // Rotas de PIN
    Route::get('/pin/create', [\App\Http\Controllers\PinController::class, 'create'])->name('pin.create');
    Route::post('/pin', [\App\Http\Controllers\PinController::class, 'store'])->name('pin.store');
    
    // Rotas Contato com Gerente (acessível mesmo sem aprovação)
    Route::get('/manager-contact', [\App\Http\Controllers\ManagerContactController::class, 'index'])->name('dashboard.manager-contact.index');
    
    // Rotas Configurações (acessível mesmo sem aprovação completa)
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('dashboard.settings.index');
    Route::post('/settings/profile-photo', [\App\Http\Controllers\SettingsController::class, 'update'])->name('dashboard.settings.update');
    
    // Rotas do Dashboard (Cliente) - Requer aprovação e PIN
    Route::middleware('approved')->group(function () {
        // Rotas de Notificações em Tempo Real
        Route::get('/notifications/unread', [\App\Http\Controllers\DashboardController::class, 'getUnreadNotifications'])->name('dashboard.notifications.unread');
        Route::post('/notifications/{id}/mark-pushed', [\App\Http\Controllers\DashboardController::class, 'markNotificationPushed'])->name('dashboard.notifications.mark-pushed');

        // Dashboard Principal
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        
        // Checkout Products
        Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('dashboard.checkout.index');
        Route::get('/checkout/create', [\App\Http\Controllers\CheckoutController::class, 'create'])->name('dashboard.checkout.create');
        Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('dashboard.checkout.store');
        Route::get('/checkout/{id}/edit', [\App\Http\Controllers\CheckoutController::class, 'edit'])->name('dashboard.checkout.edit');
        Route::put('/checkout/{id}', [\App\Http\Controllers\CheckoutController::class, 'update'])->name('dashboard.checkout.update');
        Route::delete('/checkout/{id}', [\App\Http\Controllers\CheckoutController::class, 'destroy'])->name('dashboard.checkout.destroy');
        
        Route::get('/receive', [ReceiveController::class, 'index'])->name('dashboard.receive.index');
    Route::post('/receive/generate', [ReceiveController::class, 'generateQrCode'])->name('dashboard.receive.generate');
    Route::get('/affiliates', [AffiliateController::class, 'index'])->name('dashboard.affiliates.index');
    Route::get('/manager', [ManagerController::class, 'index'])->name('dashboard.manager.index');
    
    // Rotas Prêmios
    Route::get('/awards', [AwardController::class, 'index'])->name('awards.index');

    // Rota Baixe o APP dentro da Dashboard
    Route::get('/baixe-o-app', function() {
        $landingSettings = [
            'app_title' => \App\Models\LandingPageSetting::get('app_title', 'Seu Banco na palma da sua mão.'),
            'app_subtitle' => \App\Models\LandingPageSetting::get('app_subtitle', 'Gerencie suas vendas, acompanhe seu saldo em tempo real e realize saques instantâneos onde quer que você esteja.'),
            'app_playstore_url' => \App\Models\LandingPageSetting::get('app_playstore_url', '#'),
        ];
        return view('dashboard.app.index', compact('landingSettings'));
    })->name('dashboard.app');

    // Rotas Financeiro
    Route::get('/financial', [FinancialController::class, 'index'])->name('dashboard.financial.index');
    Route::post('/financial/deposit/qr', [FinancialController::class, 'generateDepositQr'])->name('dashboard.financial.deposit.qr');
    Route::get('/financial/transaction/check/{transactionUuid}', [FinancialController::class, 'checkTransactionStatus'])->name('dashboard.financial.transaction.check');
    Route::post('/financial/withdrawal', [FinancialController::class, 'requestWithdrawal'])->name('dashboard.financial.withdrawal');
    Route::post('/financial/advance', [FinancialController::class, 'requestAdvance'])->name('dashboard.financial.advance');
    
    // Rotas API
    Route::get('/api', [ApiController::class, 'index'])->name('dashboard.api.index');
    Route::post('/api/tokens', [ApiController::class, 'store'])->name('dashboard.api.store');
    Route::post('/api/tokens/{id}/revoke', [ApiController::class, 'revoke'])->name('dashboard.api.revoke');
    Route::post('/api/tokens/{id}/reactivate', [ApiController::class, 'reactivate'])->name('dashboard.api.reactivate');
    Route::delete('/api/tokens/{id}', [ApiController::class, 'destroy'])->name('dashboard.api.destroy');
    
    // Rotas IPs Permitidos
    Route::post('/api/allowed-ip', [\App\Http\Controllers\ApiTokenAllowedIpController::class, 'store'])->name('dashboard.api.allowed-ip.store');
    Route::delete('/api/allowed-ip/{id}', [\App\Http\Controllers\ApiTokenAllowedIpController::class, 'destroy'])->name('dashboard.api.allowed-ip.destroy');
    
    // Rotas Integrações
    Route::get('/integrations', [IntegrationsController::class, 'index'])->name('dashboard.integrations.index');
    Route::get('/integrations/shopify', [\App\Http\Controllers\ShopifyController::class, 'index'])->name('dashboard.integrations.shopify.index');
    Route::post('/integrations/shopify', [\App\Http\Controllers\ShopifyController::class, 'store'])->name('dashboard.integrations.shopify.store');
    Route::post('/integrations/shopify/sync', [\App\Http\Controllers\ShopifyController::class, 'sync'])->name('dashboard.integrations.shopify.sync');
    Route::delete('/integrations/shopify', [\App\Http\Controllers\ShopifyController::class, 'destroy'])->name('dashboard.integrations.shopify.destroy');
    Route::get('/integrations/woocommerce', [\App\Http\Controllers\WooCommerceController::class, 'index'])->name('dashboard.integrations.woocommerce.index');
    Route::post('/integrations/woocommerce', [\App\Http\Controllers\WooCommerceController::class, 'store'])->name('dashboard.integrations.woocommerce.store');
    Route::post('/integrations/woocommerce/sync', [\App\Http\Controllers\WooCommerceController::class, 'sync'])->name('dashboard.integrations.woocommerce.sync');
    Route::delete('/integrations/woocommerce', [\App\Http\Controllers\WooCommerceController::class, 'destroy'])->name('dashboard.integrations.woocommerce.destroy');
    
    // Rotas Split de Pagamento
    Route::get('/split', [\App\Http\Controllers\Dashboard\SplitController::class, 'index'])->name('dashboard.split.index');
    Route::get('/split/create', [\App\Http\Controllers\Dashboard\SplitController::class, 'create'])->name('dashboard.split.create');
    Route::post('/split/search', [\App\Http\Controllers\Dashboard\SplitController::class, 'searchUser'])->name('dashboard.split.search');
    Route::post('/split', [\App\Http\Controllers\Dashboard\SplitController::class, 'store'])->name('dashboard.split.store');
    Route::get('/split/{id}/edit', [\App\Http\Controllers\Dashboard\SplitController::class, 'edit'])->name('dashboard.split.edit');
    Route::put('/split/{id}', [\App\Http\Controllers\Dashboard\SplitController::class, 'update'])->name('dashboard.split.update');
    Route::delete('/split/{id}', [\App\Http\Controllers\Dashboard\SplitController::class, 'destroy'])->name('dashboard.split.destroy');
    Route::post('/split/preview', [\App\Http\Controllers\Dashboard\SplitController::class, 'preview'])->name('dashboard.split.preview');
    
    // Rotas Relatórios
    Route::get('/reports', [ReportsController::class, 'index'])->name('dashboard.reports.index');
    Route::get('/reports/sales', [ReportsController::class, 'sales'])->name('dashboard.reports.sales');
    Route::get('/reports/transactions', [ReportsController::class, 'transactions'])->name('dashboard.reports.transactions');
    Route::get('/reports/financial', [ReportsController::class, 'financial'])->name('dashboard.reports.financial');
    
    // Rotas Documentação
    Route::get('/documentation', [DocumentationController::class, 'index'])->name('dashboard.documentation.index');
    
    // Rotas Suporte
    Route::get('/support', [SupportController::class, 'index'])->name('dashboard.support.index');
    Route::get('/support/{id}', [SupportController::class, 'show'])->name('dashboard.support.show');
    Route::post('/support', [SupportController::class, 'store'])->name('dashboard.support.store');
    Route::post('/support/{id}/message', [SupportController::class, 'sendMessage'])->name('dashboard.support.message');
    Route::get('/support/{id}/messages', [SupportController::class, 'getMessages'])->name('dashboard.support.messages');
    
    // Rotas Perfil
    Route::get('/profile', [ProfileController::class, 'index'])->name('dashboard.profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('dashboard.profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('dashboard.profile.password');
    
    // Rotas 2FA
    Route::get('/2fa', [\App\Http\Controllers\TwoFactorAuthController::class, 'index'])->name('2fa.index');
    Route::post('/2fa/enable', [\App\Http\Controllers\TwoFactorAuthController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [\App\Http\Controllers\TwoFactorAuthController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/verify', [\App\Http\Controllers\TwoFactorAuthController::class, 'showVerify'])->name('2fa.verify.show');
    Route::post('/2fa/verify', [\App\Http\Controllers\TwoFactorAuthController::class, 'verify'])->name('2fa.verify');
    
    });
    
    // Rotas Admin (protegidas por middleware admin)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Prêmios
        Route::resource('awards', \App\Http\Controllers\Admin\AdminAwardController::class);

        // Usuários
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/password', [AdminUserController::class, 'updatePassword'])->name('users.password');
        Route::post('/users/{id}/balance/add', [AdminUserController::class, 'addBalance'])->name('users.balance.add');
        Route::post('/users/{id}/balance/remove', [AdminUserController::class, 'removeBalance'])->name('users.balance.remove');
        Route::post('/users/{id}/balance/freeze', [AdminUserController::class, 'freezeBalance'])->name('users.balance.freeze');
        Route::post('/users/{id}/balance/unfreeze', [AdminUserController::class, 'unfreezeBalance'])->name('users.balance.unfreeze');
        Route::post('/users/{id}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{id}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
        Route::post('/users/{id}/block', [AdminUserController::class, 'block'])->name('users.block');
        Route::post('/users/{id}/unblock', [AdminUserController::class, 'unblock'])->name('users.unblock');
        Route::post('/users/{id}/block-withdrawal', [AdminUserController::class, 'blockWithdrawal'])->name('users.block-withdrawal');
        Route::post('/users/{id}/unblock-withdrawal', [AdminUserController::class, 'unblockWithdrawal'])->name('users.unblock-withdrawal');
        Route::post('/users/{id}/kyc/approve', [AdminUserController::class, 'approveKyc'])->name('users.kyc.approve');
        Route::post('/users/{id}/kyc/reject', [AdminUserController::class, 'rejectKyc'])->name('users.kyc.reject');
        Route::post('/users/{id}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('users.toggle-block'); // Mantido para compatibilidade
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        
        // Transações (Depósitos e Saques)
        Route::get('/transactions', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{id}/edit', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'editTransaction'])->name('transactions.edit');
        Route::put('/transactions/{id}', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'updateTransaction'])->name('transactions.update');
        Route::delete('/transactions/{id}', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'destroy'])->name('transactions.destroy');
        Route::post('/transactions/{id}/process', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'processPendingTransaction'])->name('transactions.process');
        Route::get('/transactions/withdrawal/{id}/edit', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'editWithdrawal'])->name('transactions.edit-withdrawal');
        Route::put('/transactions/withdrawal/{id}', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'updateWithdrawal'])->name('transactions.update-withdrawal');
        Route::delete('/transactions/withdrawal/{id}', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'destroyWithdrawal'])->name('transactions.destroy-withdrawal');
        Route::get('/users-old', [AdminController::class, 'users'])->name('users'); // Mantido para compatibilidade
        Route::post('/users/{user}/kyc', [AdminController::class, 'updateKyc'])->name('users.kyc');
        
        // KYC
        Route::get('/kyc', [AdminController::class, 'kyc'])->name('kyc.index');
        Route::get('/kyc/{userId}/documents', [AdminController::class, 'getKycDocuments'])->name('kyc.documents');
        Route::get('/kyc/{userId}/document/{type}', [AdminController::class, 'getKycDocument'])->name('kyc.document');
        Route::post('/kyc/{userId}/approve', [AdminController::class, 'approveKyc'])->name('kyc.approve');
        Route::post('/kyc/{userId}/reject', [AdminController::class, 'rejectKyc'])->name('kyc.reject');
        Route::get('/kyc/{userId}/view', [AdminController::class, 'viewKycDocuments'])->name('kyc.view');
        
        // Saques
        Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{id}/pay', [AdminWithdrawalController::class, 'pay'])->name('withdrawals.pay');
        Route::post('/withdrawals/{id}/refund', [AdminWithdrawalController::class, 'refund'])->name('withdrawals.refund');
        Route::post('/withdrawals/{id}/cancel', [AdminWithdrawalController::class, 'cancel'])->name('withdrawals.cancel');
        
        // Chargebacks/MED
        Route::get('/chargebacks', [\App\Http\Controllers\Admin\AdminChargebackController::class, 'index'])->name('chargebacks.index');
        Route::post('/chargebacks/transaction/{transactionId}/approve', [\App\Http\Controllers\Admin\AdminChargebackController::class, 'approveTransaction'])->name('chargebacks.approve-transaction');
        Route::post('/chargebacks/{id}/approve-med', [\App\Http\Controllers\Admin\AdminChargebackController::class, 'approveMed'])->name('chargebacks.approve-med');
        Route::post('/chargebacks/{id}/approve', [\App\Http\Controllers\Admin\AdminChargebackController::class, 'approve'])->name('chargebacks.approve');
        Route::post('/chargebacks/{id}/cancel', [\App\Http\Controllers\Admin\AdminChargebackController::class, 'cancel'])->name('chargebacks.cancel');
        Route::post('/chargebacks/{id}/block-withdrawal', [\App\Http\Controllers\Admin\AdminChargebackController::class, 'blockWithdrawal'])->name('chargebacks.block-withdrawal');
        Route::post('/chargebacks/{id}/unblock-withdrawal', [\App\Http\Controllers\Admin\AdminChargebackController::class, 'unblockWithdrawal'])->name('chargebacks.unblock-withdrawal');
        Route::post('/chargebacks/{id}/debit-balance', [\App\Http\Controllers\Admin\AdminChargebackController::class, 'debitBalance'])->name('chargebacks.debit-balance');
        // Rotas de compatibilidade (mantidas)
        Route::post('/withdrawals/{id}/approve', [AdminWithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{id}/reject', [AdminWithdrawalController::class, 'reject'])->name('withdrawals.reject');
        Route::post('/withdrawals/{id}/auto-pay', [AdminWithdrawalController::class, 'autoPay'])->name('withdrawals.auto-pay');
        Route::get('/withdrawals-old', [AdminController::class, 'withdrawals'])->name('withdrawals'); // Mantido para compatibilidade
        Route::post('/withdrawals/{withdrawal}/process', [AdminController::class, 'processWithdrawal'])->name('withdrawals.process');
        Route::post('/withdrawals/{withdrawal}/auto-pay-old', [AdminController::class, 'autoPayWithdrawal'])->name('withdrawals.auto-pay-old');
        
        // Financeiro Admin
        Route::get('/financial', [AdminController::class, 'financial'])->name('financial.index');
        
        // Transações Admin
        Route::get('/transactions/create', [AdminController::class, 'createTransaction'])->name('transactions.create');
        
        // Configurações do Sistema
        Route::get('/configs', [SystemConfigController::class, 'index'])->name('configs.index');
        Route::post('/configs', [SystemConfigController::class, 'store'])->name('configs.store');
        
        // SMTP Settings
        Route::get('/smtp', [\App\Http\Controllers\AdminSmtpController::class, 'index'])->name('smtp.index');
        Route::post('/smtp', [\App\Http\Controllers\AdminSmtpController::class, 'store'])->name('smtp.store');
        Route::post('/smtp/test', [\App\Http\Controllers\AdminSmtpController::class, 'test'])->name('smtp.test');
        
        // Email Campaigns
        Route::get('/email-campaigns', [\App\Http\Controllers\AdminEmailCampaignController::class, 'index'])->name('email-campaigns.index');
        Route::get('/email-campaigns/create', [\App\Http\Controllers\AdminEmailCampaignController::class, 'create'])->name('email-campaigns.create');
        Route::post('/email-campaigns', [\App\Http\Controllers\AdminEmailCampaignController::class, 'store'])->name('email-campaigns.store');
        Route::get('/email-campaigns/{id}', [\App\Http\Controllers\AdminEmailCampaignController::class, 'show'])->name('email-campaigns.show');
        Route::post('/email-campaigns/{id}/send', [\App\Http\Controllers\AdminEmailCampaignController::class, 'send'])->name('email-campaigns.send');
        
        // Landing Page
        Route::get('/landing', [AdminLandingPageController::class, 'index'])->name('landing.index');
        Route::post('/landing', [AdminLandingPageController::class, 'store'])->name('landing.store');
        
        // Páginas Estáticas
        Route::get('/static', [AdminStaticPageController::class, 'index'])->name('static.index');
        Route::get('/static/{slug}/edit', [AdminStaticPageController::class, 'edit'])->name('static.edit');
        Route::put('/static/{slug}', [AdminStaticPageController::class, 'update'])->name('static.update');
        
        // Gateways
        Route::get('/gateways', [AdminController::class, 'gateways'])->name('gateways.index');
        Route::post('/gateways', [AdminController::class, 'updateGateways'])->name('gateways.update');
        Route::post('/gateway-configs', [AdminController::class, 'storeGatewayConfig'])->name('gateway-configs.store');
        Route::post('/gateway-configs/{config}/toggle', [AdminController::class, 'toggleGatewayStatus'])->name('gateway-configs.toggle');
        Route::delete('/gateway-configs/{config}', [AdminController::class, 'deleteGatewayConfig'])->name('gateway-configs.delete');
        
        // Suporte Admin
        Route::get('/support', [AdminSupportController::class, 'index'])->name('support.index');
        // Rotas de notificações devem vir antes das rotas com {id} para evitar conflito
        // Route::get('/support/notifications/count', [AdminSupportController::class, 'getNotificationsCount'])->name('support.notifications.count');
        // Route::get('/support/notifications/unread', [AdminSupportController::class, 'getUnreadNotifications'])->name('support.notifications.unread');
        Route::get('/support/{id}', [AdminSupportController::class, 'show'])->name('support.show');
        Route::post('/support/{id}/assign', [AdminSupportController::class, 'assign'])->name('support.assign');
        Route::post('/support/{id}/status', [AdminSupportController::class, 'updateStatus'])->name('support.status');
        Route::post('/support/{id}/message', [AdminSupportController::class, 'sendMessage'])->name('support.message');
        Route::get('/support/{id}/messages', [AdminSupportController::class, 'getMessages'])->name('support.messages');
        
        // Notificações Admin
        /*
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/create', [AdminNotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
        Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
        */
    });
    
    // Webhooks de Integrações (públicos, mas autenticados)
    Route::post('/webhooks/shopify/order-create', [\App\Http\Controllers\ShopifyWebhookController::class, 'handleOrderCreate'])->name('webhooks.shopify.order-create');
    Route::post('/webhooks/woocommerce/order-create', [\App\Http\Controllers\WooCommerceWebhookController::class, 'handleOrderCreate'])->name('webhooks.woocommerce.order-create');
});


Route::get('/arrumar-fotos', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return "Atalho criado! Tente atualizar a página das fotos agora.";
});