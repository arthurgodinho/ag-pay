<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $product->checkout_title ?? 'Checkout - ' . $product->name }}</title>

    <!-- Favicon -->
    @if($faviconUrl = \App\Helpers\LogoHelper::getFaviconUrl())
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-slate-800" x-data="{ 
    step: 1, 
    paymentMethod: '{{ $product->enable_pix ? 'pix' : 'credit_card' }}',
    loading: false,
    pixData: null,
    paymentSuccess: false,
    checkInterval: null,
    timer: 300,
    timerInterval: null,
    isExpired: false,
    
    startPaymentCheck() {
        if (this.checkInterval) clearInterval(this.checkInterval);
        this.checkInterval = setInterval(async () => {
            if (this.pixData && this.pixData.transaction_id) {
                try {
                    const response = await fetch(`/pay/check/${this.pixData.transaction_id}`);
                    const data = await response.json();
                    if (data.success && (data.status === 'paid' || data.status === 'completed')) {
                        clearInterval(this.checkInterval);
                        this.paymentSuccess = true;
                        this.step = 4; // New Thank You Step
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                } catch (e) {
                    console.error('Erro ao verificar status:', e);
                }
            }
        }, 3000);
    },

    validateStep1() {
        const form = document.getElementById('checkout-form');
        const name = form.elements['name'].value;
        const email = form.elements['email'].value;
        const cpf = form.elements['cpf'].value;
        const phone = form.elements['phone'].value;
        
        if (!name || !email || !cpf || !phone) {
            alert('Por favor, preencha todos os campos de identificação.');
            return false;
        }
        return true;
    },
    
    goToPayment() {
        if (this.validateStep1()) {
            this.step = 2;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    },

    startTimer() {
        if (this.timerInterval) clearInterval(this.timerInterval);
        this.timer = 300;
        this.isExpired = false;
        this.timerInterval = setInterval(() => {
            if (this.timer > 0) {
                this.timer--;
            } else {
                this.isExpired = true;
                clearInterval(this.timerInterval);
            }
        }, 1000);
    },

    formatTimer() {
        const minutes = Math.floor(this.timer / 60);
        const secs = this.timer % 60;
        return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    },

    copyPixKey() {
        const input = document.getElementById('pix-key-input');
        if (input) {
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(() => {
                const btn = document.getElementById('copy-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class=\'text-green-600 font-bold\'>Copiado!</span>';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            }).catch(() => {
                alert('Erro ao copiar. Tente selecionar e copiar manualmente.');
            });
        }
    },

    async submitPayment() {
        this.loading = true;
        const form = document.getElementById('checkout-form');
        const formData = new FormData(form);

        try {
            console.log('Enviando pagamento...', Object.fromEntries(formData));
            const response = await fetch('{{ route('checkout.process', $product->uuid) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            console.log('Resposta do pagamento:', data);

            if (data.success) {
                if (this.paymentMethod === 'pix') {
                    if (data.data && (data.data.qr_code || data.data.qr_code_image_url)) {
                        this.pixData = data.data;
                        this.pixData.transaction_id = data.transaction.uuid; // Link the transaction UUID
                        this.step = 3;
                        this.startTimer();
                        this.startPaymentCheck(); // Start checking for payment status
                        console.log('Mudando para step 3 (PIX)');
                    } else {
                        console.error('Dados do PIX incompletos:', data.data);
                        alert('Erro: Dados do PIX não retornados corretamente.');
                    }
                } else {
                    this.paymentSuccess = true;
                    this.step = 4; // Go straight to Thank You for card
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                alert(data.message || 'Erro ao processar pagamento.');
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            alert('Erro de conexão. Tente novamente.');
        } finally {
            this.loading = false;
        }
    }
}">

    <!-- Top Bar -->
    <div class="bg-white border-b border-gray-200 py-4 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex-shrink-0">
                {!! \App\Helpers\LogoHelper::renderLogo('h-10 w-auto', 'Logo') !!}
            </div>
            
            <!-- Security Badge -->
            <div class="flex items-center text-slate-500 text-sm font-medium">
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                PAGAMENTO 100% SEGURO
            </div>
        </div>
    </div>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Column: Checkout Steps -->
            <div class="flex-1 order-2 lg:order-1">
                
                <!-- Info Banner -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6 flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-blue-800 text-sm font-medium">
                        Produto Digital: Entregue automaticamente via E-mail e Painel após a confirmação do pagamento!
                    </p>
                </div>

                <form id="checkout-form" action="{{ route('checkout.process', $product->uuid) }}" method="POST" @submit.prevent="submitPayment()" class="space-y-6">
                    @csrf
                    
                    <!-- Step 1: Identification -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden" x-show="step < 4">
                        <div class="p-6">
                            <div class="flex items-center mb-6">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-white mr-3"
                                     :class="step >= 1 ? 'bg-slate-800' : 'bg-slate-300'">
                                    1
                                </div>
                                <h2 class="text-lg font-bold text-slate-800" :class="step >= 1 ? 'opacity-100' : 'opacity-50'">Identificação</h2>
                                
                                <div class="ml-auto" x-show="step > 1" x-cloak>
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>

                            <div x-show="step === 1" x-transition>
                                <div class="space-y-4">
                                    <p class="text-sm text-slate-500 mb-4">Utilizaremos seu e-mail para: identificar seu perfil, histórico de compra, notificação de pedidos e carrinho de compras.</p>
                                    
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nome completo</label>
                                        <input type="text" name="name" id="name" required
                                            class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                            placeholder="Digite seu nome completo">
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">E-mail</label>
                                        <input type="email" name="email" id="email" required
                                            class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                            placeholder="seu@email.com">
                                    </div>

                                    <div>
                                        <label for="cpf" class="block text-sm font-medium text-slate-700 mb-1">CPF</label>
                                        <input type="text" name="cpf" id="cpf" required
                                            class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                            placeholder="000.000.000-00">
                                    </div>

                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Celular</label>
                                        <div class="flex">
                                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">
                                                +55
                                            </span>
                                            <input type="tel" name="phone" id="phone" required
                                                class="flex-1 rounded-r-lg border-slate-300 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                                placeholder="(00) 00000-0000">
                                        </div>
                                    </div>

                                    <div class="pt-4">
                                        <button type="button" @click="goToPayment()"
                                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-lg shadow transition-colors text-lg uppercase tracking-wide">
                                            Ir para Pagamento
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Payment -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden" :class="step === 2 ? 'opacity-100' : 'opacity-60'" x-show="step < 4">
                        <div class="p-6">
                            <div class="flex items-center mb-6">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-white mr-3"
                                     :class="step >= 2 ? 'bg-slate-800' : 'bg-slate-300'">
                                    2
                                </div>
                                <h2 class="text-lg font-bold text-slate-800">Pagamento</h2>
                            </div>

                            <div x-show="step === 2" x-transition x-cloak>
                                <h3 class="text-base font-semibold text-slate-800 mb-4">Escolha a forma de pagamento</h3>

                                <div class="space-y-3">
                                    @if($product->enable_pix)
                                    <!-- Pix Option -->
                                    <label class="relative flex items-center p-4 border rounded-lg cursor-pointer transition-all"
                                           :class="paymentMethod === 'pix' ? 'border-green-500 bg-green-50/10 ring-1 ring-green-500' : 'border-slate-200 hover:border-slate-300'">
                                        <input type="radio" name="payment_method" value="pix" class="sr-only" x-model="paymentMethod">
                                        <div class="flex items-center justify-between w-full">
                                            <div class="flex items-center">
                                                <div class="mr-3 text-green-600">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <span class="block text-sm font-bold text-slate-800">Pix</span>
                                                    <span class="block text-xs text-green-600 font-medium">Aprovação imediata</span>
                                                </div>
                                            </div>
                                            <div x-show="paymentMethod === 'pix'" class="text-green-500">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                    @endif

                                    @if($product->enable_credit_card)
                                    <!-- Credit Card Option -->
                                    <label class="relative flex items-center p-4 border rounded-lg cursor-pointer transition-all"
                                           :class="paymentMethod === 'credit_card' ? 'border-green-500 bg-green-50/10 ring-1 ring-green-500' : 'border-slate-200 hover:border-slate-300'">
                                        <input type="radio" name="payment_method" value="credit_card" class="sr-only" x-model="paymentMethod">
                                        <div class="flex items-center justify-between w-full">
                                            <div class="flex items-center">
                                                <div class="mr-3 text-slate-600">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <span class="block text-sm font-bold text-slate-800">Cartão de Crédito</span>
                                                    <span class="block text-xs text-slate-500">Até 12x no cartão</span>
                                                    <span class="block text-[10px] text-green-600 mt-0.5 flex items-center">
                                                        <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                        Processado via MercadoPago
                                                    </span>
                                                </div>
                                            </div>
                                            <div x-show="paymentMethod === 'credit_card'" class="text-green-500">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                    @endif
                                </div>

                                @if($product->enable_credit_card)
                                <!-- Credit Card Form -->
                                <div x-show="paymentMethod === 'credit_card'" x-transition class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center justify-center gap-2 mb-4 text-green-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        <p class="text-xs font-black uppercase tracking-widest">PAGAMENTO 100% SEGURO</p>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Número do Cartão</label>
                                            <input type="text" name="card_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="0000 0000 0000 0000">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700">Validade</label>
                                                <input type="text" name="card_expiration" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="MM/AA">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700">CVV</label>
                                                <input type="text" name="card_cvv" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="123">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Nome no Cartão</label>
                                            <input type="text" name="card_holder" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="NOME COMO NO CARTAO">
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="pt-6">
                                    <button type="submit" 
                                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-lg shadow transition-colors text-lg uppercase tracking-wide">
                                        Finalizar Pagamento
                                    </button>
                                    
                                    <div class="mt-4 text-center">
                                        <button type="button" @click="step = 1" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
                                            Voltar para identificação
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Confirmation (PIX ONLY) -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden" x-show="step === 3" x-cloak x-transition>
                        <div class="p-6 text-center">
                            <!-- PIX Success State Placeholder (Behind Modal) -->
                            <div x-show="paymentMethod === 'pix' && pixData">
                                <div class="mb-6">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 17h.01M9 17h.01M12 17v1m-3-1v1m3-4h-2m2 0v3m0 0h.01m-2-3h.01m-4 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-slate-800 mb-2">Aguardando Pagamento...</h2>
                                    <p class="text-slate-500 mb-6">O QR Code para pagamento foi gerado na tela.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Step 4: Thank You Page (Integrated) -->
            <div class="flex-1 order-2 lg:order-1" x-show="step === 4" x-cloak x-transition>
                <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 p-8 sm:p-12 text-center relative overflow-hidden">
                    <!-- Modern Success Background -->
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-64 bg-green-50 rounded-full blur-3xl -mt-32 opacity-60"></div>
                    
                    <div class="relative z-10">
                        <!-- Modern Animated Icon -->
                        <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl shadow-green-500/20 animate-bounce">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>

                        <h2 class="text-4xl font-black text-slate-900 mb-4 leading-tight">Pagamento Confirmado! 🎉</h2>
                        <p class="text-lg text-slate-500 mb-8 max-w-md mx-auto">
                            Obrigado pela sua compra! Seu pedido foi processado com sucesso e os detalhes já foram enviados para o seu e-mail.
                        </p>

                        <!-- Order Info Card -->
                        <div class="bg-slate-50 rounded-2xl p-6 mb-8 max-w-sm mx-auto border border-slate-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Produto</span>
                                <span class="text-sm font-bold text-slate-900">{{ $product->name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Valor</span>
                                <span class="text-lg font-black text-green-600">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-4 max-w-sm mx-auto">
                            @if(isset($product) && $product->download_url)
                            <a href="{{ $product->download_url }}" target="_blank" class="flex items-center justify-center gap-3 w-full py-5 bg-green-500 text-white rounded-2xl font-black text-lg shadow-xl shadow-green-500/20 hover:bg-green-600 transition-all hover:-translate-y-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                ACESSAR MEU PRODUTO
                            </a>
                            @endif
                            
                            <a href="{{ url('/') }}" class="block text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">
                                Voltar para o site
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary -->
            <div class="lg:w-96 order-1 lg:order-2">
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 lg:sticky lg:top-8">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Resumo</h3>
                    
                    <!-- Coupon -->
                    <div class="mb-6">
                        <label class="block text-sm text-slate-500 mb-1">Tem um cupom?</label>
                        <div class="flex gap-2">
                            <input type="text" class="flex-1 rounded-md border-slate-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Código do cupom">
                            <button type="button" class="text-blue-600 font-bold text-sm hover:text-blue-700 px-2">
                                Adicionar
                            </button>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="space-y-3 pb-6 border-b border-slate-100">
                        <div class="flex justify-between text-sm text-slate-600">
                        <span>{{ $product->name }}</span>
                        <span>R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                    </div>
                    </div>

                    <div class="flex justify-between items-center py-4">
                        <span class="text-base font-bold text-green-500">Total</span>
                        <span class="text-xl font-bold text-green-500">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                    </div>

                    <!-- Product Card -->
                    <div class="mt-4 bg-slate-50 rounded-lg p-3 flex gap-3 items-start">
                        @if($product->product_image)
                            @if(\Illuminate\Support\Str::startsWith($product->product_image, ['http://', 'https://']))
                                <img src="{{ $product->product_image }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-md border border-slate-200">
                            @else
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($product->product_image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-md border border-slate-200">
                            @endif
                        @else
                            <div class="w-16 h-16 bg-white rounded-md border border-slate-200 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-slate-800 leading-tight mb-1">{{ $product->name }}</h4>
                            <p class="text-xs text-slate-500 mb-1">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</p>
                            <p class="text-sm font-bold text-slate-800">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Info -->
                <div class="mt-6 flex justify-center space-x-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Mastercard_2019_logo.svg" class="h-6" alt="Mastercard">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Visa_Inc._logo_%281992%E2%80%931999%29.svg/960px-Visa_Inc._logo_%281992%E2%80%931999%29.svg.png" class="h-6" alt="Visa">
                    <img src="https://img.icons8.com/color/512/pix.png" class="h-6" alt="Pix">
                </div>
            </div>
        </div>
    </main>
    <!-- PIX Modal -->
    <div x-show="step === 3 && paymentMethod === 'pix'" 
         class="relative z-[9999]" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         x-cloak>
        
        <!-- Backdrop -->
        <div x-show="step === 3 && paymentMethod === 'pix'"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <!-- Modal Panel -->
                <div x-show="step === 3 && paymentMethod === 'pix'"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200">
                     
                     <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="text-center mb-5">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 17h.01M9 17h.01M12 17v1m-3-1v1m3-4h-2m2 0v3m0 0h.01m-2-3h.01m-4 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            
                            <h3 class="text-xl leading-6 font-bold text-slate-900 mb-1">
                                Pagamento via Pix
                            </h3>
                            <p class="text-slate-500 text-xs">Escaneie o QR Code com o app do seu banco</p>
                        </div>
                            
                        <!-- Timer -->
                        <div x-show="!isExpired" class="mb-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-100 rounded-lg mx-auto block w-fit">
                                <svg class="w-4 h-4 text-red-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-red-500 font-semibold text-xs">
                                    Tempo restante: <span x-text="formatTimer()"></span>
                                </span>
                            </div>
                        </div>

                        <!-- PIX Content -->
                        <div x-show="!isExpired" class="mt-2">
                            <!-- QR Code -->
                            <div class="flex justify-center mb-6">
                                <template x-if="pixData && pixData.qr_code_image_url">
                                    <div class="p-2 border border-slate-100 rounded-xl bg-white shadow-sm">
                                        <img :src="pixData.qr_code_image_url" alt="QR Code PIX" class="w-56 h-56 rounded-lg">
                                    </div>
                                </template>
                            </div>

                            <!-- Copy Paste -->
                            <div class="relative mb-6 text-left">
                                <label class="block text-xs font-medium text-slate-500 mb-2 uppercase tracking-wider">Chave PIX (Copia e Cola)</label>
                                <div class="flex shadow-sm rounded-lg">
                                    <input type="text" id="pix-key-input" readonly :value="pixData ? pixData.qr_code : ''" 
                                           class="flex-1 min-w-0 block w-full px-3 py-3 rounded-l-lg border border-slate-200 bg-slate-50 text-slate-600 text-xs focus:ring-green-500 focus:border-green-500 font-mono truncate">
                                    <button type="button" id="copy-btn" @click="copyPixKey()" 
                                            class="inline-flex items-center px-4 py-3 border border-l-0 border-slate-200 rounded-r-lg bg-green-600 hover:bg-green-700 text-xs font-bold text-white focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors whitespace-nowrap">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Copiar
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center mb-6 px-1">
                                 <span class="text-sm font-medium text-slate-500">Valor:</span>
                                 <span class="text-lg font-bold text-green-600">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            </div>

                            <!-- Instructions -->
                            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-4 text-left">
                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    <strong class="text-slate-800 block mb-1 uppercase text-[10px] tracking-wider">Instruções:</strong>
                                    1. Abra o app do seu banco<br>
                                    2. Escaneie o QR Code ou cole a chave PIX<br>
                                    3. Confirme o pagamento e aguarde
                                </p>
                            </div>
                        </div>

                        <!-- Expired State -->
                        <div x-show="isExpired" class="mt-4 p-6 bg-red-50 rounded-xl border border-red-100">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Cobrança Expirada</h3>
                            <p class="text-sm text-gray-500 mb-4">O tempo para pagamento expirou. Gere uma nova cobrança para continuar.</p>
                            <button @click="window.location.reload()" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                                Gerar Novo PIX
                            </button>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="step = 1; window.location.reload()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal (Credit Card) -->
    <div x-show="step === 3 && paymentMethod !== 'pix'" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
             <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
             <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
             <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Pagamento Aprovado!</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Seu pagamento foi processado com sucesso. Você receberá os detalhes por e-mail.
                                </p>
                                
                                @if($product->download_url)
                                <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-100">
                                    <p class="text-sm font-medium text-green-800 mb-2">Seu produto está pronto para download!</p>
                                    <a href="{{ $product->download_url }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Baixar Produto
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50"
         style="display: none;"
         x-cloak>
        <div class="bg-white p-6 rounded-2xl shadow-xl flex flex-col items-center transform transition-all scale-100">
            <!-- Modern Spinner -->
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                <div class="absolute inset-0 rounded-full border-4 border-green-500 border-t-transparent animate-spin"></div>
            </div>
            <p class="text-slate-800 font-bold text-lg">Processando...</p>
            <p class="text-slate-500 text-sm">Aguarde um momento</p>
        </div>
    </div>
</body>
</html>