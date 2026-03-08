<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        use App\Helpers\ThemeHelper;
        use App\Helpers\LogoHelper;
        $themeColors = ThemeHelper::getThemeColors();
        $logoUrl = LogoHelper::getLogoUrl();
        $faviconUrl = LogoHelper::getFaviconUrl();
        $gatewayName = LogoHelper::getSystemName();
    @endphp
    <title>{{ $settings['hero_title'] ?? $gatewayName . ' - Gateway de Pagamentos' }}</title>
    @if($faviconUrl)
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    @endif
    
    <!-- CSS CRÍTICO INLINE - DEVE SER O PRIMEIRO PARA PREVENIR FOUC -->
    <style>
        /* CRÍTICO: Esconde TUDO que pode aparecer grande antes do Tailwind */
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #0B0E14;
            color: #fff;
            opacity: 0; /* Começa invisível para evitar flash */
        }
        body.loaded {
            opacity: 1;
            transition: opacity 0.3s ease-in;
        }
        /* Esconde elementos grandes até Tailwind carregar */
        svg:not([width]):not([height]),
        img:not([width]):not([height]) {
            width: 0 !important;
            height: 0 !important;
            visibility: hidden !important;
            display: none !important;
        }
        /* Garante que o logo/favicon não exploda */
        link[rel="icon"], link[rel="apple-touch-icon"] {
            display: none !important;
        }
        /* Previne o erro da estrela gigante */
        .star-icon, [class*="star"], svg {
            max-width: 100px !important;
            max-height: 100px !important;
        }
    </style>
    
    <script>
        // Trigger de carregamento
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
        });
    </script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        {!! ThemeHelper::generateThemeCSS() !!}
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B0E14;
        }
        
        /* CORREÇÃO CRÍTICA: Esconde favicon/imagens grandes antes do CSS carregar */
        link[rel="icon"],
        link[rel="apple-touch-icon"] {
            display: none !important;
        }
        
        /* CORREÇÃO CRÍTICA: Garante que nenhuma imagem apareça gigante antes do CSS carregar */
        img:not([width]):not([height]):not([style*="width"]):not([style*="height"]) {
            max-width: 100% !important;
            height: auto !important;
        }
        
        /* CORREÇÃO CRÍTICA: Esconde qualquer elemento de loading/spinner que possa aparecer */
        [class*="loading"],
        [class*="spinner"],
        [class*="loader"] {
            display: none !important;
        }

        /* CORREÇÃO CRÍTICA: Restringe SVGs para evitar que fiquem gigantes antes do CSS carregar */
        svg:not([width]):not([height]) {
            max-width: 100% !important;
            height: auto !important;
            display: block;
        }
        
        /* CORREÇÃO CRÍTICA: Esconde TODOS os elementos grandes antes do Tailwind carregar */
        div[class*="w-"],
        div[class*="h-"],
        div[class*="w-["],
        div[class*="h-["],
        div[class*="blur-"],
        div[class*="rounded-full"],
        section > div[class*="absolute"]:not([id]),
        section > div[class*="absolute"]:not(.max-w),
        div.absolute:not([id]),
        div[style*="background"]:not([id]) {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
            max-width: 0 !important;
            max-height: 0 !important;
            overflow: hidden !important;
        }
        
        /* CORREÇÃO: Garante que elementos com IDs específicos também sejam escondidos */
        #hero-bg-gradient,
        #hero-gradient-1,
        #hero-gradient-2 {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
            max-width: 0 !important;
            max-height: 0 !important;
            overflow: hidden !important;
        }
        
        /* CORREÇÃO: Mostra elementos apenas após carregar */
        body.loaded div[class*="w-"],
        body.loaded div[class*="h-"],
        body.loaded div[class*="w-["],
        body.loaded div[class*="h-["],
        body.loaded div[class*="blur-"],
        body.loaded div[class*="rounded-full"],
        body.loaded section > div[class*="absolute"],
        body.loaded div.absolute,
        body.loaded div[style*="background"] {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: auto !important;
            height: auto !important;
            max-width: none !important;
            max-height: none !important;
            overflow: visible !important;
        }
        
        body.loaded #hero-bg-gradient,
        body.loaded #hero-gradient-1,
        body.loaded #hero-gradient-2 {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: auto !important;
            height: auto !important;
            max-width: none !important;
            max-height: none !important;
            overflow: visible !important;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            background-size: 1000px 100%;
            animation: shimmer 3s infinite;
        }
        
        .bg-gradient-animated {
            background: linear-gradient(-45deg, #0B0E14, #151A23, #0B0E14, #1F2937);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        .glass {
            background: rgba(21, 26, 35, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .grid-pattern {
            background-image: 
                linear-gradient(rgba(0, 178, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 178, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
        }
        
        /* Otimizações Mobile */
        @media (max-width: 640px) {
            /* Ajustes de espaçamento geral */
            section {
                padding-top: 3rem !important;
                padding-bottom: 3rem !important;
            }
            
            /* Hero section mobile */
            .hero-title {
                font-size: 2rem !important;
                line-height: 1.2 !important;
                margin-bottom: 1rem !important;
            }
            
            .hero-subtitle {
                font-size: 1rem !important;
                line-height: 1.5 !important;
                margin-bottom: 1.5rem !important;
            }
            
            /* Stats mobile */
            .stats-grid {
                gap: 1rem !important;
                padding-top: 1rem !important;
            }
            
            .stats-grid > div {
                text-align: center;
            }
            
            .stats-number {
                font-size: 1.5rem !important;
            }
            
            .stats-label {
                font-size: 0.7rem !important;
            }
            
            /* Features cards mobile */
            .feature-card {
                padding: 1.5rem !important;
                margin-bottom: 1rem;
            }
            
            /* Pricing cards mobile */
            .pricing-card {
                margin-bottom: 1.5rem !important;
            }
            
            /* Footer mobile */
            .footer-grid {
                grid-template-columns: 1fr !important;
                gap: 2rem !important;
            }
            
            /* Melhorias de toque */
            button, a {
                -webkit-tap-highlight-color: rgba(0, 178, 255, 0.2);
            }
            
            /* Prevenir zoom em inputs */
            input, select, textarea {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased selection:bg-blue-100 selection:text-blue-900" x-data="{ mobileMenuOpen: false }">
    <!-- Navigation -->
    <nav 
        class="fixed top-0 w-full z-50 transition-all duration-300"
        :class="{ 'bg-white/80 backdrop-blur-md shadow-sm': window.scrollY > 20 }"
        @scroll.window="window.scrollY > 20 ? document.querySelector('nav').classList.add('scrolled') : document.querySelector('nav').classList.remove('scrolled')"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0">
                    @if($logoUrl)
                        <a href="/">
                            <img src="{{ $logoUrl }}" alt="Logo" class="h-10 sm:h-12 object-contain">
                        </a>
                    @else
                        <a href="/" class="text-2xl font-bold font-display tracking-tight text-slate-900">{{ $gatewayName }}</a>
                    @endif
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Soluções</a>
                    <a href="#pricing" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Taxas</a>
                    <a href="#about" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Sobre</a>
                    <a href="#faq" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">FAQ</a>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Entrar</a>
                        <a href="{{ route('auth.register') }}" class="group relative px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-lg overflow-hidden transition-all hover:scale-105 shadow-lg shadow-blue-500/20 hover:bg-blue-700">
                            <span class="relative z-10">Criar Conta</span>
                        </a>
                    </div>
                    <button @click="mobileMenuOpen = true" class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" aria-label="Abrir menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div 
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-full"
        class="fixed inset-0 z-50 bg-white md:hidden"
        @click.away="mobileMenuOpen = false"
        style="display: none;"
    >
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <span class="text-xl font-bold text-slate-900">{{ $gatewayName }}</span>
                <button @click="mobileMenuOpen = false" class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg" aria-label="Fechar menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto py-6 px-4 space-y-6">
                <nav class="flex flex-col space-y-4">
                    <a href="#features" @click="mobileMenuOpen = false" class="text-lg font-medium text-slate-900 hover:text-blue-600">Soluções</a>
                    <a href="#pricing" @click="mobileMenuOpen = false" class="text-lg font-medium text-slate-900 hover:text-blue-600">Taxas</a>
                    <a href="#about" @click="mobileMenuOpen = false" class="text-lg font-medium text-slate-900 hover:text-blue-600">Sobre</a>
                    <a href="#faq" @click="mobileMenuOpen = false" class="text-lg font-medium text-slate-900 hover:text-blue-600">FAQ</a>
                </nav>
                <div class="border-t border-slate-100 pt-6 flex flex-col space-y-3">
                    <a href="{{ route('login') }}" class="w-full py-3 px-4 bg-slate-50 text-slate-700 font-semibold rounded-xl text-center">Entrar</a>
                    <a href="{{ route('auth.register') }}" class="w-full py-3 px-4 bg-blue-600 text-white font-bold rounded-xl text-center shadow-lg shadow-blue-500/20">Criar Conta</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden min-h-screen flex items-center bg-subtle-blue">
        <!-- Background Effects -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMzAsIDU4LCAxMzgsIDAuMDUpIi8+PC9zdmc+')] [mask-image:linear-gradient(to_bottom,white,transparent)]"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Text Content -->
                <div class="max-w-3xl text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 mb-8 animate-fade-in-up">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                        </span>
                        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">🚀 {{ $settings['hero_badge'] ?? 'A Nova Era dos Pagamentos' }}</span>
                    </div>
                    
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold font-display text-slate-900 leading-[1.1] mb-8 tracking-tight animate-fade-in-up" style="animation-delay: 0.2s;">
                        {{ $settings['hero_title'] ?? 'Gateway de Pagamentos Inteligente' }}
                    </h1>
                    
                    <p class="text-lg sm:text-xl text-slate-600 mb-10 leading-relaxed max-w-lg mx-auto lg:mx-0 animate-fade-in-up" style="animation-delay: 0.4s;">
                        {{ $settings['hero_subtitle'] ?? 'Transforme cada transação em uma oportunidade de crescimento. Infraestrutura robusta, segura e escalável para o seu negócio digital.' }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-5 justify-center lg:justify-start animate-fade-in-up" style="animation-delay: 0.6s;">
                        <a href="{{ route('auth.register') }}" class="inline-flex justify-center items-center px-8 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-1 transition-all duration-300">
                            {{ $settings['hero_cta_text'] ?? 'Começar Agora' }}
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        <a href="#features" class="inline-flex justify-center items-center px-8 py-4 bg-white text-slate-700 font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all">
                            Conhecer Soluções
                        </a>
                    </div>
                </div>

                <!-- Visual Content (Dashboard Mockup) -->
                <div class="relative lg:ml-auto hidden lg:block perspective-1000 animate-float">
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-200 to-cyan-200 rounded-2xl blur opacity-30 animate-pulse"></div>
                    <div class="relative rounded-2xl bg-white shadow-2xl border border-slate-100 p-2 transform rotate-y-12 hover:rotate-y-0 transition-transform duration-700 ease-out preserve-3d">
                        @if(!empty($settings['hero_image']))
                            <img src="{{ asset(strpos($settings['hero_image'], 'IMG/') === 0 ? $settings['hero_image'] : ('storage/' . $settings['hero_image'])) }}" alt="Dashboard" class="rounded-xl w-full h-auto object-cover shadow-inner">
                        @else
                        <!-- Dashboard CSS Mockup Light -->
                        <div class="bg-slate-50 rounded-xl overflow-hidden shadow-xl">
                            <!-- Header -->
                            <div class="h-10 bg-white border-b border-slate-200 flex items-center px-4 gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                <div class="ml-4 h-5 w-64 bg-slate-100 rounded-md"></div>
                            </div>
                            <!-- Content -->
                            <div class="p-6 bg-white">
                                <div class="flex justify-between items-end mb-8">
                                    <div>
                                        <p class="text-sm text-slate-500 mb-1">Receita Mensal</p>
                                        <p class="text-4xl font-bold text-slate-800 tracking-tight">R$ 1.240.592,00</p>
                                    </div>
                                    <div class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 text-xs font-bold rounded-full flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        +12.5%
                                    </div>
                                </div>
                                <!-- Chart Bars -->
                                <div class="flex items-end gap-3 h-40 mb-8">
                                    <div class="flex-1 bg-blue-50 rounded-t-sm h-[40%] relative group"><div class="absolute bottom-0 w-full bg-blue-500 h-0 group-hover:h-full transition-all duration-500 rounded-t-sm"></div></div>
                                    <div class="flex-1 bg-blue-50 rounded-t-sm h-[60%] relative group"><div class="absolute bottom-0 w-full bg-blue-500 h-0 group-hover:h-full transition-all duration-500 delay-75 rounded-t-sm"></div></div>
                                    <div class="flex-1 bg-blue-50 rounded-t-sm h-[45%] relative group"><div class="absolute bottom-0 w-full bg-blue-500 h-0 group-hover:h-full transition-all duration-500 delay-100 rounded-t-sm"></div></div>
                                    <div class="flex-1 bg-blue-50 rounded-t-sm h-[80%] relative group"><div class="absolute bottom-0 w-full bg-blue-500 h-0 group-hover:h-full transition-all duration-500 delay-150 rounded-t-sm"></div></div>
                                    <div class="flex-1 bg-blue-50 rounded-t-sm h-[55%] relative group"><div class="absolute bottom-0 w-full bg-blue-500 h-0 group-hover:h-full transition-all duration-500 delay-200 rounded-t-sm"></div></div>
                                    <div class="flex-1 bg-blue-50 rounded-t-sm h-[90%] relative group"><div class="absolute bottom-0 w-full bg-blue-500 h-0 group-hover:h-full transition-all duration-500 delay-300 rounded-t-sm"></div></div>
                                    <div class="flex-1 bg-blue-50 rounded-t-sm h-[70%] relative group"><div class="absolute bottom-0 w-full bg-blue-500 h-0 group-hover:h-full transition-all duration-500 delay-400 rounded-t-sm"></div></div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mb-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">Vendas Hoje</p>
                                        <p class="text-xs text-slate-500">1.234 transações</p>
                                    </div>
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                        <div class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center mb-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">Aprovação</p>
                                        <p class="text-xs text-slate-500">98.5% conversão</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Confiado por empresas inovadoras</p>
            </div>
            <div class="flex flex-wrap justify-center items-center gap-x-8 gap-y-4 md:gap-x-16 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                <span class="text-xl font-bold text-slate-400">TechPay</span>
                <span class="text-xl font-bold text-slate-400">GlobalBank</span>
                <span class="text-xl font-bold text-slate-400">FutureFinance</span>
                <span class="text-xl font-bold text-slate-400">SecureCheckout</span>
                <span class="text-xl font-bold text-slate-400">FastMoney</span>
            </div>
            <div class="mt-20 pt-12 border-t border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <p class="text-4xl lg:text-5xl font-bold text-slate-900">+10k</p>
                    <p class="text-sm text-slate-500 mt-2 uppercase tracking-wide font-medium">Clientes Satisfeitos</p>
                </div>
                <div>
                    <p class="text-4xl lg:text-5xl font-bold text-slate-900">R$ 50M+</p>
                    <p class="text-sm text-slate-500 mt-2 uppercase tracking-wide font-medium">Transacionado com Sucesso</p>
                </div>
                <div>
                    <p class="text-4xl lg:text-5xl font-bold text-slate-900">99.99%</p>
                    <p class="text-sm text-slate-500 mt-2 uppercase tracking-wide font-medium">Uptime Garantido</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold font-display text-slate-900 mb-4">{{ $settings['features_title'] ?? 'Soluções Completas para seu Negócio' }}</h2>
                <p class="text-lg text-slate-600">{{ $settings['features_subtitle'] ?? 'Tudo o que você precisa para escalar suas vendas online com segurança e estabilidade.' }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="modern-card p-8 rounded-2xl group">
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform border border-blue-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Alta Conversão</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Checkout otimizado para máxima performance. Recuperação de carrinho e one-click buy nativos.</p>
                </div>

                <!-- Feature 2 -->
                <div class="modern-card p-8 rounded-2xl group">
                    <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mb-6 group-hover:scale-110 transition-transform border border-purple-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Segurança Total</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Anti-fraude integrado com inteligência artificial. Proteção contra chargebacks e monitoramento 24/7.</p>
                </div>

                <!-- Feature 3 -->
                <div class="modern-card p-8 rounded-2xl group">
                    <div class="w-14 h-14 bg-cyan-50 rounded-xl flex items-center justify-center text-cyan-600 mb-6 group-hover:scale-110 transition-transform border border-cyan-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Pix Instantâneo</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Recebimento e conciliação automática via Pix. QR Code dinâmico e suporte a Pix Copia e Cola.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-12 sm:py-16 md:py-20 px-3 sm:px-4 lg:px-8 bg-[#0B0E14] relative">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8 sm:mb-12 md:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4 text-white px-2">
                    {{ $settings['pricing_title'] ?? __('landing.plans_pricing') }}
                </h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-300 max-w-2xl mx-auto px-2">
                    {{ $settings['pricing_subtitle'] ?? __('landing.choose_plan') }}
                </p>
            </div>

            <!-- Taxas Detalhadas -->
            <div class="mb-8 sm:mb-12 md:mb-16 glass rounded-2xl sm:rounded-3xl p-5 sm:p-6 md:p-8 lg:p-12 border border-white/10">
                <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-center mb-5 sm:mb-6 md:mb-8 text-white">{{ __('landing.our_fees') }}</h3>
                <div class="grid sm:grid-cols-2 gap-4 sm:gap-6 md:gap-8 max-w-4xl mx-auto">
                    <!-- Cash-In -->
                    <div class="bg-[#0B0E14] rounded-2xl sm:rounded-3xl p-5 sm:p-6 md:p-8 border border-white/10">
                        <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-5 md:mb-6">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }});">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <h4 class="text-base sm:text-lg md:text-xl font-bold text-white">Cash-In (Entrada)</h4>
                        </div>
                        <div class="space-y-2 sm:space-y-3">
                            <div class="flex justify-between items-center flex-wrap gap-1">
                                <span class="text-sm sm:text-base text-gray-300">{{ __('landing.fixed_fee') }}</span>
                                <span class="text-base sm:text-lg font-bold text-[#00B2FF]">R$ {{ number_format($cashinFixo, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center flex-wrap gap-1">
                                <span class="text-sm sm:text-base text-gray-300">{{ __('landing.percentual_fee') }}</span>
                                <span class="text-base sm:text-lg font-bold text-[#00B2FF]">{{ number_format($cashinPercentual, 2, ',', '.') }}%</span>
                            </div>
                            <div class="pt-3 sm:pt-4 border-t border-white/10">
                                <p class="text-xs sm:text-sm text-gray-400 mb-1 sm:mb-2">{{ __('landing.example_for') }}</p>
                                <p class="text-xl sm:text-2xl font-bold text-white">
                                    R$ {{ number_format($cashinFixo + (100 * $cashinPercentual / 100), 2, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('landing.total_fee') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cash-Out -->
                    <div class="bg-[#0B0E14] rounded-2xl sm:rounded-3xl p-5 sm:p-6 md:p-8 border border-white/10">
                        <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-5 md:mb-6">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }});">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                            <h4 class="text-base sm:text-lg md:text-xl font-bold text-white">Cash-Out (Saída)</h4>
                        </div>
                        <div class="space-y-2 sm:space-y-3">
                            <div class="flex justify-between items-center flex-wrap gap-1">
                                <span class="text-sm sm:text-base text-gray-300">{{ __('landing.fixed_fee') }}</span>
                                <span class="text-base sm:text-lg font-bold text-[#00D9AC]">R$ {{ number_format($cashoutFixo, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center flex-wrap gap-1">
                                <span class="text-sm sm:text-base text-gray-300">{{ __('landing.percentual_fee') }}</span>
                                <span class="text-base sm:text-lg font-bold text-[#00D9AC]">{{ number_format($cashoutPercentual, 2, ',', '.') }}%</span>
                            </div>
                            <div class="pt-3 sm:pt-4 border-t border-white/10">
                                <p class="text-xs sm:text-sm text-gray-400 mb-1 sm:mb-2">{{ __('landing.example_for') }}</p>
                                <p class="text-xl sm:text-2xl font-bold text-white">
                                    R$ {{ number_format($cashoutFixo + (100 * $cashoutPercentual / 100), 2, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('landing.total_fee') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-xs sm:text-sm text-gray-300 mt-5 sm:mt-6 md:mt-8 px-2">
                    <span class="font-semibold text-[#00B2FF]">{{ __('landing.total_transparency') }}</span> {{ __('landing.transparency_desc') }}
                </p>
            </div>
            
            <!-- Planos -->
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                <!-- Starter -->
                <div class="pricing-card bg-[#151A23] rounded-2xl sm:rounded-3xl p-5 sm:p-6 md:p-8 border border-white/10 hover:border-[#00B2FF]/50 transition-all active:scale-95">
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-white">{{ __('landing.starter') }}</h3>
                    <div class="mb-4 sm:mb-5 md:mb-6">
                        <span class="text-3xl sm:text-4xl md:text-5xl font-bold text-[#00B2FF]">2.99%</span>
                        <span class="text-sm sm:text-base text-gray-300"> {{ __('landing.per_transaction') }}</span>
                    </div>
                    <ul class="space-y-2 sm:space-y-3 mb-6 sm:mb-8">
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.up_to') }} R$ 10.000{{ __('landing.per_month') }}</span>
                        </li>
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.pix_and_card') }}</span>
                        </li>
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.email_support') }}</span>
                        </li>
                    </ul>
                    <a href="{{ route('auth.register') }}" class="block w-full text-center bg-[#0B0E14] border border-white/10 text-white px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold hover:border-[#00B2FF]/50 active:scale-95 transition-all text-sm sm:text-base">
                        {{ __('landing.start') }}
                    </a>
                </div>
                
                <!-- Professional -->
                <div class="pricing-card bg-[#0B0E14] rounded-2xl sm:rounded-3xl p-5 sm:p-6 md:p-8 border-2 border-[#00B2FF] relative sm:transform sm:scale-105 shadow-xl shadow-[#00B2FF]/20">
                    <div class="absolute -top-3 sm:-top-4 left-1/2 transform -translate-x-1/2 px-3 sm:px-4 py-1 rounded-full text-xs sm:text-sm font-semibold text-white" style="background: linear-gradient(to right, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }});">{{ __('landing.most_popular') }}</div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-white">{{ __('landing.professional') }}</h3>
                    <div class="mb-4 sm:mb-5 md:mb-6">
                        <span class="text-3xl sm:text-4xl md:text-5xl font-bold text-[#00B2FF]">1.99%</span>
                        <span class="text-sm sm:text-base text-gray-300"> {{ __('landing.per_transaction') }}</span>
                    </div>
                    <ul class="space-y-2 sm:space-y-3 mb-6 sm:mb-8">
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.up_to') }} R$ 100.000{{ __('landing.per_month') }}</span>
                        </li>
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.pix_and_card') }}</span>
                        </li>
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.priority_support') }}</span>
                        </li>
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.advanced_dashboard') }}</span>
                        </li>
                    </ul>
                    <a href="{{ route('auth.register') }}" class="block w-full text-center text-white px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold transition-all active:scale-95 hover:shadow-lg hover:shadow-[#00B2FF]/50 text-sm sm:text-base" style="background: linear-gradient(to right, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }});">
                        {{ __('landing.start') }}
                    </a>
                </div>
                
                <!-- Enterprise -->
                <div class="pricing-card bg-[#151A23] rounded-2xl sm:rounded-3xl p-5 sm:p-6 md:p-8 border border-white/10 hover:border-[#00B2FF]/50 transition-all active:scale-95 sm:col-span-2 md:col-span-1">
                    <h3 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-white">{{ __('landing.enterprise') }}</h3>
                    <div class="mb-4 sm:mb-5 md:mb-6">
                        <span class="text-3xl sm:text-4xl md:text-5xl font-bold text-[#00B2FF]">Custom</span>
                    </div>
                    <ul class="space-y-2 sm:space-y-3 mb-6 sm:mb-8">
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.unlimited_volume') }}</span>
                        </li>
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.all_methods') }}</span>
                        </li>
                        <li class="flex items-start sm:items-center text-sm sm:text-base text-gray-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#00B2FF] mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('landing.dedicated_manager') }}</span>
                        </li>
                    </ul>
                    <a href="{{ route('auth.register') }}" class="block w-full text-center bg-[#0B0E14] border border-white/10 text-white px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold hover:border-[#00B2FF]/50 active:scale-95 transition-all text-sm sm:text-base">
                        {{ __('landing.talk_to_sales') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-12 sm:py-16 md:py-20 px-3 sm:px-4 lg:px-8 bg-[#151A23] relative">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8 sm:mb-12 md:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4 text-white px-2">
                    {{ $settings['about_title'] ?? __('landing.about_us') }}
                </h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed px-2">
                    {{ $settings['about_text'] ?? __('landing.about_text') }}
                </p>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-12 sm:py-16 md:py-20 px-3 sm:px-4 lg:px-8 bg-[#0B0E14]">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8 sm:mb-12 md:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4 text-white px-2">{{ __('landing.frequently_asked') }}</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-300 px-2">{{ __('landing.clear_doubts') }}</p>
            </div>
            
            <div class="space-y-3 sm:space-y-4" x-data="{ openFaq: null }">
                <!-- FAQ 1 -->
                <div class="bg-[#151A23] rounded-2xl sm:rounded-3xl border border-white/10 overflow-hidden">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-4 sm:px-6 py-4 sm:py-5 flex items-center justify-between text-left hover:bg-[#0B0E14]/50 active:bg-[#0B0E14]/70 transition-colors">
                        <span class="font-semibold text-sm sm:text-base text-white pr-2">{{ __('landing.how_integration_works') }}</span>
                        <svg class="w-5 h-5 text-[#00B2FF] transition-transform flex-shrink-0" :class="openFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-4 sm:px-6 pb-4 sm:pb-5 text-sm sm:text-base text-gray-300">
                        {{ __('landing.integration_answer') }}
                    </div>
                </div>
                
                <!-- FAQ 2 -->
                <div class="bg-[#151A23] rounded-2xl sm:rounded-3xl border border-white/10 overflow-hidden">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-4 sm:px-6 py-4 sm:py-5 flex items-center justify-between text-left hover:bg-[#0B0E14]/50 active:bg-[#0B0E14]/70 transition-colors">
                        <span class="font-semibold text-sm sm:text-base text-white pr-2">{{ __('landing.payment_methods_accepted') }}</span>
                        <svg class="w-5 h-5 text-[#00B2FF] transition-transform flex-shrink-0" :class="openFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-4 sm:px-6 pb-4 sm:pb-5 text-sm sm:text-base text-gray-300">
                        {{ __('landing.methods_answer') }}
                    </div>
                </div>
                
                <!-- FAQ 3 -->
                <div class="bg-[#151A23] rounded-2xl sm:rounded-3xl border border-white/10 overflow-hidden">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-4 sm:px-6 py-4 sm:py-5 flex items-center justify-between text-left hover:bg-[#0B0E14]/50 active:bg-[#0B0E14]/70 transition-colors">
                        <span class="font-semibold text-sm sm:text-base text-white pr-2">{{ __('landing.how_fees_calculated') }}</span>
                        <svg class="w-5 h-5 text-[#00B2FF] transition-transform flex-shrink-0" :class="openFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-4 sm:px-6 pb-4 sm:pb-5 text-sm sm:text-base text-gray-300">
                        {{ __('landing.fees_answer') }}
                    </div>
                </div>
                
                <!-- FAQ 4 -->
                <div class="bg-[#151A23] rounded-2xl sm:rounded-3xl border border-white/10 overflow-hidden">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full px-4 sm:px-6 py-4 sm:py-5 flex items-center justify-between text-left hover:bg-[#0B0E14]/50 active:bg-[#0B0E14]/70 transition-colors">
                        <span class="font-semibold text-sm sm:text-base text-white pr-2">{{ __('landing.when_receive_money') }}</span>
                        <svg class="w-5 h-5 text-[#00B2FF] transition-transform flex-shrink-0" :class="openFaq === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="px-4 sm:px-6 pb-4 sm:pb-5 text-sm sm:text-base text-gray-300">
                        {{ __('landing.money_answer') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-2 md:col-span-1">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $gatewayName }}" class="h-10 w-auto object-contain mb-6">
                    @else
                        <span class="text-2xl font-bold text-slate-900 font-display mb-6 block">{{ $gatewayName }}</span>
                    @endif
                    <p class="text-slate-500 text-sm leading-relaxed">Tecnologia financeira de ponta para impulsionar a nova economia digital com segurança e eficiência.</p>
                </div>
                
                <div>
                    <h4 class="font-bold text-slate-900 mb-6">Produto</h4>
                    <ul class="space-y-4 text-sm text-slate-500">
                        <li><a href="#features" class="hover:text-blue-600 transition-colors">Soluções</a></li>
                        <li><a href="#pricing" class="hover:text-blue-600 transition-colors">Taxas</a></li>
                        <li><a href="#faq" class="hover:text-blue-600 transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-6">Legal</h4>
                    <ul class="space-y-4 text-sm text-slate-500">
                        <li><a href="{{ route('static.show', 'termos-de-uso') }}" class="hover:text-blue-600 transition-colors">Termos de Uso</a></li>
                        <li><a href="{{ route('static.show', 'politica-de-privacidade') }}" class="hover:text-blue-600 transition-colors">Privacidade</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-6">Contato</h4>
                    <ul class="space-y-4 text-sm text-slate-500">
                        <li><a href="{{ route('dashboard.support.index') }}" class="hover:text-blue-600 transition-colors">Suporte</a></li>
                        @if(!empty($settings['whatsapp_number']))
                        <li><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) }}" target="_blank" class="hover:text-blue-600 transition-colors">WhatsApp</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-slate-500">
                    {{ $settings['footer_text'] ?? '© ' . date('Y') . ' ' . $gatewayName . '. Todos os direitos reservados.' }}
                </div>
                <div class="flex space-x-6">
                    <!-- Social Icons -->
                    <a href="#" class="text-slate-400 hover:text-blue-600 transition-colors">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 014.18 3.388c.636-.247 1.363-.416 2.427-.465C7.902 2.013 8.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353-.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Script para otimizar carregamento e esconder elementos grandes -->
    <script>
        (function() {
            'use strict';
            
            // CORREÇÃO: Esconde body até o Tailwind carregar
            document.body.style.opacity = '1';
            document.body.style.visibility = 'visible';
            
            // Função para mostrar elementos de fundo após Tailwind carregar
            function showBackgroundElements() {
                document.body.classList.add('loaded');
                
                const heroBgGradient = document.getElementById('hero-bg-gradient');
                const heroGradient1 = document.getElementById('hero-gradient-1');
                const heroGradient2 = document.getElementById('hero-gradient-2');
                
                if (heroBgGradient) {
                    heroBgGradient.style.display = 'block';
                    heroBgGradient.style.width = '';
                    heroBgGradient.style.height = '';
                    heroBgGradient.style.maxWidth = '';
                    heroBgGradient.style.maxHeight = '';
                    heroBgGradient.style.overflow = '';
                }
                
                if (heroGradient1) {
                    heroGradient1.style.display = 'block';
                    heroGradient1.style.width = '';
                    heroGradient1.style.height = '';
                    heroGradient1.style.maxWidth = '';
                    heroGradient1.style.maxHeight = '';
                    heroGradient1.style.overflow = '';
                }
                
                if (heroGradient2) {
                    heroGradient2.style.display = 'block';
                    heroGradient2.style.width = '';
                    heroGradient2.style.height = '';
                    heroGradient2.style.maxWidth = '';
                    heroGradient2.style.maxHeight = '';
                    heroGradient2.style.overflow = '';
                }
                
                // Restaura todos os elementos com classes Tailwind
                const allElements = document.querySelectorAll('div[class*="w-"], div[class*="h-"], div[class*="blur-"], div[class*="rounded-full"]');
                allElements.forEach(el => {
                    if (el.id !== 'hero-bg-gradient' && el.id !== 'hero-gradient-1' && el.id !== 'hero-gradient-2') {
                        el.style.width = '';
                        el.style.height = '';
                        el.style.maxWidth = '';
                        el.style.maxHeight = '';
                        el.style.overflow = '';
                        el.style.display = '';
                        el.style.visibility = '';
                        el.style.opacity = '';
                    }
                });
            }
            
            // Espera o Tailwind CSS carregar antes de mostrar elementos
            function waitForTailwind() {
                // Verifica se Tailwind já processou as classes
                const testEl = document.createElement('div');
                testEl.className = 'w-[300px]';
                testEl.style.position = 'absolute';
                testEl.style.top = '-9999px';
                document.body.appendChild(testEl);
                const computed = window.getComputedStyle(testEl);
                
                if (computed.width && computed.width !== 'auto' && computed.width !== '0px') {
                    document.body.removeChild(testEl);
                    setTimeout(showBackgroundElements, 100);
                } else {
                    document.body.removeChild(testEl);
                    setTimeout(waitForTailwind, 100);
                }
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(waitForTailwind, 500);
                });
            } else {
                setTimeout(waitForTailwind, 500);
            }
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('loaded');
        });
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
        });
    </script>
</body>
</html>
