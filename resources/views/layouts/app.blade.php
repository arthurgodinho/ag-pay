<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        use App\Helpers\ThemeHelper;
        use App\Helpers\LogoHelper;
        $themeColors = ThemeHelper::getThemeColors();
        $logoUrl = LogoHelper::getLogoUrl();
        $faviconUrl = LogoHelper::getFaviconUrl();
        $systemName = LogoHelper::getSystemName();
    @endphp
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Dashboard') - {{ $systemName }}</title>
    <meta name="description" content="@yield('description', 'Dashboard de gerenciamento da plataforma ' . $systemName)">
    <meta name="keywords" content="@yield('keywords', 'dashboard, pagamentos, gateway, ' . $systemName)">
    <meta name="author" content="{{ $systemName }}">
    <meta name="robots" content="noindex, nofollow">

    <!-- CSS CRÍTICO INLINE - DEVE SER O PRIMEIRO PARA PREVENIR FOUC -->
    <style>
        /* CRÍTICO: Esconde TUDO que pode aparecer grande antes do Tailwind */
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            background-color: #F8FAFC;
            color: #0F172A;
            opacity: 0;
            transition: opacity 0.3s ease-in;
        }
        
        body.loaded {
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        /* CORREÇÃO CRÍTICA: Previne ícones gigantes (SVG) antes do Tailwind carregar */
        svg:not(.apexcharts-svg) {
            width: 1em;
            height: 1em;
            max-width: 100%;
            display: inline-block;
        }
        
        /* Garante que o gráfico renderize corretamente */
        .apexcharts-svg {
            width: 100% !important;
            height: auto !important;
        }

        /* Esconde elementos grandes até Tailwind carregar, mas permite SVGs com classes */
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
        .star-icon, [class*="star"] {
            max-width: 20px !important;
            max-height: 20px !important;
        }
        
        [x-cloak] { display: none !important; }
        .overlay-mobile { display: none !important; }

        /* Scrollbar Moderna */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; transition: background 0.3s ease; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
        .custom-scrollbar { scrollbar-width: thin; scrollbar-color: rgba(255, 255, 255, 0.1) transparent; }
    </style>
    
    <script>
        // Trigger de carregamento imediato
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('loaded');
        });
        // Fallback
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
        });
        // Fallback de segurança
        setTimeout(function() { document.body.classList.add('loaded'); }, 1000);
    </script>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Dashboard') - {{ $systemName }}">
    <meta property="og:description" content="@yield('description', 'Dashboard de gerenciamento')">
    @if($logoUrl)
    <meta property="og:image" content="{{ $logoUrl }}">
    @endif
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Dashboard') - {{ $systemName }}">
    <meta name="twitter:description" content="@yield('description', 'Dashboard de gerenciamento')">
    
    <!-- Favicon -->
    @if($faviconUrl)
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @endif
    
    <!-- Preconnect para melhor performance -->
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    
    <!-- Critical CSS inline -->
    <style>
        {!! ThemeHelper::generateThemeCSS() !!}
        [x-cloak] { display: none !important; }
        .overlay-mobile { display: none !important; }
        body:not([x-data]) .overlay-mobile,
        body:not([x-data]) [x-show] { display: none !important; }
        
        /* CORREÇÃO: Esconde favicon/imagens grandes antes do CSS carregar */
        link[rel="icon"],
        link[rel="apple-touch-icon"] {
            display: none !important;
        }
        
        /* CORREÇÃO: Garante que nenhuma imagem apareça gigante antes do CSS carregar */
        img:not([width]):not([height]):not([style*="width"]):not([style*="height"]) {
            max-width: 100% !important;
            height: auto !important;
        }
        
        /* CORREÇÃO: Esconde qualquer elemento de loading/spinner que possa aparecer */
        [class*="loading"],
        [class*="spinner"],
        [class*="loader"] {
            display: none !important;
        }
        
        /* Scrollbar Moderna e Customizada - White Theme */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 transparent;
        }
        
        /* Performance: will-change para animações */
        .sidebar-transition {
            will-change: transform;
        }
        
        /* Melhorias de acessibilidade */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Otimização mobile */
        @media (max-width: 640px) {
            body {
                -webkit-tap-highlight-color: transparent;
            }
        }
    </style>
    
    <!-- CSS de Otimizações Globais -->
    <link rel="stylesheet" href="{{ asset('optimizations.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('optimizations.css') }}"></noscript>
    
    <!-- Scripts otimizados com defer/async e tratamento de erros -->
    <script>
        // Tratamento global de erros JavaScript
        window.addEventListener('error', function(e) {
            console.error('Erro JavaScript capturado:', e.error);
            // Previne que o erro trave a página
            e.preventDefault();
            return true;
        });
        
        // Tratamento de erros não capturados em Promises
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Promise rejeitada não tratada:', e.reason);
            e.preventDefault();
        });
    </script>
    
    <script src="https://cdn.tailwindcss.com" defer onerror="console.error('Erro ao carregar Tailwind CSS')"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" onerror="console.error('Erro ao carregar Alpine.js')"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer onerror="console.error('Erro ao carregar ApexCharts')"></script>
    
    <!-- Script de Otimizações Globais -->
    <script src="{{ asset('optimizations.js') }}" defer onerror="console.error('Erro ao carregar optimizations.js')"></script>
    
    <!-- Garantia de que o conteúdo sempre aparece -->
    <script>
        // FORÇA a exibição do conteúdo imediatamente se travar
        (function() {
            setTimeout(function() {
                if (document.body && getComputedStyle(document.body).opacity === '0') {
                    document.body.classList.add('loaded');
                    document.body.style.opacity = '1';
                }
            }, 2000);
        })();
    </script>
    <style>
        {!! ThemeHelper::generateThemeCSS() !!}
        .sidebar-transition { will-change: transform; }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        @media (max-width: 640px) { body { -webkit-tap-highlight-color: transparent; } }
    </style>
</head>
<body class="bg-slate-50 text-slate-800" style="background-color: #F8FAFC;" x-data="{ 
    sidebarOpen: false,
    reportsOpen: false,
    adminFinancialOpen: false,
    adminTransactionsOpen: false,
    adminSettingsOpen: false,
    showBalances: true
}" x-init="sidebarOpen = false; showBalances = localStorage.getItem('showBalances') !== 'false'; $watch('sidebarOpen', value => { if (value && window.innerWidth >= 1024) sidebarOpen = false; }); $watch('showBalances', value => { localStorage.setItem('showBalances', value); }); window.addEventListener('resize', () => { if (window.innerWidth >= 1024) sidebarOpen = false; }); window.addEventListener('balanceVisibilityChanged', (e) => { showBalances = e.detail.showBalances; });">
    <div class="flex h-screen overflow-hidden bg-slate-50">
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:static inset-y-0 left-0 z-50 w-72 transform transition-transform duration-300 ease-in-out lg:translate-x-0 sidebar-transition"
            role="navigation"
            aria-label="Menu principal"
        >
            <div class="flex flex-col h-full bg-white border-r border-slate-200 shadow-xl">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-slate-100 bg-white">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $systemName }} Logo" class="h-7 sm:h-8 object-contain max-w-full flex-shrink-0" style="max-width: 160px;" loading="eager" width="160" height="40">
                        @else
                            <h1 class="text-lg sm:text-xl md:text-2xl font-bold bg-clip-text text-transparent whitespace-nowrap truncate" style="background: linear-gradient(to right, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }}); -webkit-background-clip: text;">{{ $systemName }}</h1>
                        @endif
                    </div>
                    <button 
                        @click="sidebarOpen = false"
                        class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary rounded-lg p-1 transition-all"
                        aria-label="Fechar menu"
                        type="button"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @php
                    // Calcula o Saldo Acumulado baseado no Faturamento Total (mesma lógica do Dashboard)
                    $accumulatedBalance = \App\Models\Transaction::where('user_id', Auth::id())
                        ->where('status', 'completed')
                        ->where(function($query) {
                            $query->where('type', '!=', 'debit')
                                ->orWhere(function($q) {
                                    $q->where('type', 'debit')
                                      ->where('gateway_provider', '!=', 'admin');
                                });
                        })
                        ->sum('amount_gross');
                    
                    // Busca o próximo prêmio a ser alcançado
                    $nextAward = \App\Models\Award::where('goal_amount', '>', $accumulatedBalance)
                        ->orderBy('goal_amount', 'asc')
                        ->first();
                        
                    // Se não houver próximo prêmio (zerou o game), pega o último para mostrar 100% ou define um estado "Max"
                    if (!$nextAward) {
                        $lastAward = \App\Models\Award::orderBy('goal_amount', 'desc')->first();
                        $limitAmount = $lastAward ? $lastAward->goal_amount : 10000.00;
                        $progressPercentage = 100;
                        $goalTitle = "Nível Máximo";
                    } else {
                        $limitAmount = $nextAward->goal_amount;
                        $progressPercentage = min(($accumulatedBalance / $limitAmount) * 100, 100);
                        $goalTitle = "Próxima Meta: " . $nextAward->title;
                    }
                @endphp

                <!-- Barra de Progresso -->
                <div class="px-6 py-4 border-b border-slate-100">
                    <div class="mb-2">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">SALDO ACUMULADO</span>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                <span x-show="showBalances">{{ number_format($progressPercentage, 1, ',', '.') }}%</span>
                                <span x-show="!showBalances" x-cloak>•••%</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-2 font-medium">
                            <span class="text-slate-800">
                                <span x-show="showBalances">R$ {{ number_format($accumulatedBalance, 2, ',', '.') }}</span>
                                <span x-show="!showBalances" x-cloak>R$ •••••</span>
                            </span>
                            <span class="text-slate-400 text-[10px] uppercase">{{ isset($nextAward) ? 'Meta' : 'Concluído' }}</span>
                            <span class="text-slate-800">
                                <span x-show="showBalances">R$ {{ number_format($limitAmount, 0, ',', '.') }}</span>
                                <span x-show="!showBalances" x-cloak>R$ •••••</span>
                            </span>
                        </div>
                        <div class="relative w-full h-3 bg-slate-100 rounded-full overflow-hidden shadow-inner border border-slate-200/60">
                            <!-- Background Pattern (Opcional) -->
                            <div class="absolute inset-0 opacity-30" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(0,0,0,0.05) 5px, rgba(0,0,0,0.05) 10px);"></div>
                            
                            <!-- Barra de Progresso com Gradiente Moderno -->
                            <div class="h-full rounded-full transition-all duration-1000 ease-out relative shadow-sm" 
                                 style="width: {{ $progressPercentage }}%; background: linear-gradient(90deg, #3B82F6 0%, #2563EB 50%, #1D4ED8 100%); box-shadow: 0 0 10px rgba(37, 99, 235, 0.3);">
                                
                                <!-- Efeito de Brilho Animado -->
                                <div class="absolute top-0 left-0 bottom-0 right-0 bg-gradient-to-r from-transparent via-white/30 to-transparent w-full -translate-x-full animate-[shimmer_2s_infinite]"></div>
                            </div>
                        </div>
                        @if(isset($nextAward))
                        <p class="text-[10px] text-center mt-2 text-slate-400 font-medium">
                            Faltam <span class="text-blue-600 font-bold" x-show="showBalances">R$ {{ number_format($limitAmount - $accumulatedBalance, 2, ',', '.') }}</span><span x-show="!showBalances" x-cloak>R$ •••</span> para <span class="text-slate-600">{{ $nextAward->title }}</span>
                        </p>
                        @else
                        <p class="text-[10px] text-center mt-2 text-blue-600 font-bold uppercase tracking-wide">
                            🏆 Todas as metas alcançadas!
                        </p>
                        @endif
                    </div>
                </div>

                <!-- Menu Principal -->
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto custom-scrollbar">
                    <!-- Dashboard -->
                    <a 
                        href="{{ route('dashboard.index') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-colors {{ request()->routeIs('dashboard.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Relatórios -->
                    <div>
                        <button 
                            @click="reportsOpen = !reportsOpen"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard.reports.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard.reports.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-900 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>{{ __('nav.reports') }}</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="reportsOpen ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div x-show="reportsOpen" x-collapse class="ml-4 mt-1 space-y-1 border-l border-slate-200 pl-2">
                            <a href="{{ route('dashboard.reports.sales') }}" class="flex items-center px-4 py-2 text-sm text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                {{ __('nav.sales') }}
                            </a>
                            <a href="{{ route('dashboard.reports.transactions') }}" class="flex items-center px-4 py-2 text-sm text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                {{ __('nav.transactions') }}
                            </a>
                            <a href="{{ route('dashboard.reports.financial') }}" class="flex items-center px-4 py-2 text-sm text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                {{ __('nav.financial') }}
                            </a>
                        </div>
                    </div>

                    <!-- Financeiro -->
                    <a 
                        href="{{ route('dashboard.financial.index') }}" 
                        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard.financial.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard.financial.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-900 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ __('nav.financial') }}
                    </a>

                    <!-- Checkout -->
                    <a 
                        href="{{ route('dashboard.checkout.index') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-colors {{ request()->routeIs('dashboard.checkout.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Checkout
                    </a>

                    <!-- Documentação -->
                    <a 
                        href="{{ route('dashboard.documentation.index') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-colors {{ request()->routeIs('dashboard.documentation.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('nav.documentation') }}
                    </a>

                    <!-- Chave API -->
                    <a 
                        href="{{ route('dashboard.api.index') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-colors {{ request()->routeIs('dashboard.api.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ __('nav.api_keys') }}
                    </a>



                    <!-- Meus Referidos -->
                    <a 
                        href="{{ route('dashboard.affiliates.index') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-colors {{ request()->routeIs('dashboard.affiliates.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        {{ __('nav.affiliates') }}
                    </a>



                    <!-- Split de Pagamento -->
                    <a 
                        href="{{ route('dashboard.split.index') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-colors {{ request()->routeIs('dashboard.split.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        {{ __('nav.split_payment') }}
                    </a>

                    <!-- Suporte -->
                    <a 
                        href="{{ route('dashboard.support.index') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-colors {{ request()->routeIs('dashboard.support.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        {{ __('nav.support') }}
                        @php
                            try {
                                $unreadTickets = \App\Models\SupportTicket::where('user_id', Auth::id())
                                    ->whereHas('messages', function($q) {
                                        $q->where('user_id', '!=', Auth::id())
                                          ->where('is_read', false);
                                    })
                                    ->count();
                            } catch (\Exception $e) {
                                $unreadTickets = 0;
                            }
                        @endphp
                        @if($unreadTickets > 0)
                            <span class="ml-auto bg-blue-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ $unreadTickets }}</span>
                        @endif
                    </a>


                    <!-- Prêmios -->
                    <a 
                        href="{{ route('awards.index') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-colors {{ request()->routeIs('awards.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prêmios
                    </a>

                    <!-- Baixe o APP -->
                    <a 
                        href="{{ route('dashboard.app') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-all duration-200 group {{ request()->routeIs('dashboard.app') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'bg-blue-50/50 border border-blue-100/50 text-blue-600 hover:bg-blue-600 hover:text-white hover:shadow-lg hover:shadow-blue-600/20' }}"
                    >
                        <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-bold">Baixe o APP</span>
                    </a>

                    <!-- Configurações -->
                    <a 
                        href="{{ route('dashboard.settings.index') }}" 
                        class="flex items-center px-4 py-3 rounded-3xl transition-colors {{ request()->routeIs('dashboard.settings.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Configurações
                    </a>

                    <!-- Divisor ADMINISTRAÇÃO -->
                    @if(Auth::user()->is_admin)
                    <div class="pt-4 px-2">
                        <a 
                            href="{{ route('admin.dashboard') }}" 
                            class="flex items-center justify-center w-full px-4 py-3 text-white font-bold bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-lg shadow-blue-600/20 group"
                        >
                            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            PAINEL ADMIN
                        </a>
                    </div>
                    @endif
                </nav>

                <!-- Campo de Busca -->
                <div class="p-4 border-t border-slate-200">
                    <div class="relative">
                        <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input 
                            type="text" 
                            placeholder="Pesquisar" 
                            class="w-full pl-10 pr-4 py-2 bg-slate-100 border border-slate-200 rounded-3xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                </div>
            </div>
        </aside>

        <!-- Overlay Mobile - Só aparece quando sidebar está aberta em mobile -->
        <div 
            x-show="sidebarOpen && window.innerWidth < 1024"
            x-cloak
            @click="sidebarOpen = false"
            @resize.window="if (window.innerWidth >= 1024) sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="overlay-mobile fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
            style="display: none !important;"
        ></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Navbar -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 shadow-sm">
                <button 
                    @click="sidebarOpen = true"
                    class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary rounded-lg p-1 transition-all"
                    aria-label="Abrir menu"
                    type="button"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Welcome Text & Toggle -->
                <div class="flex items-center gap-3 ml-2 lg:ml-0 mr-auto">
                    <div class="hidden sm:block ml-4">
                        <h1 class="text-sm font-bold text-slate-800 flex items-center gap-1">
                            <span>Olá,</span>
                            <span x-show="showBalances" class="truncate max-w-[150px]">{{ Auth::user()->name }}</span>
                            <span x-show="!showBalances" x-cloak>•••••</span>
                        </h1>
                        <p class="text-[10px] text-slate-500 font-medium">Bem-vindo(a)</p>
                    </div>
                    
                    <button 
                        @click="
                            showBalances = !showBalances;
                            localStorage.setItem('showBalances', showBalances);
                            window.dispatchEvent(new CustomEvent('balanceVisibilityChanged', { detail: { showBalances } }));
                        " 
                        class="p-1.5 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer text-slate-400 hover:text-slate-600 ml-2"
                        title="{{ __('dashboard.hide_show_balances') }}"
                    >
                        <svg x-show="showBalances" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg x-show="!showBalances" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex items-center space-x-4 ml-auto" x-data="{ profileMenuOpen: false, notificationsOpen: false }">
                    <a 
                        href="{{ route('dashboard.manager-contact.index') }}" 
                        class="hidden md:flex items-center px-4 py-2 border rounded-3xl transition-all text-sm font-medium bg-slate-50 hover:bg-slate-100"
                        style="border-color: {{ $themeColors['primary'] }}30; color: {{ $themeColors['accent'] }};"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        {{ __('nav.manager_contact') }}
                    </a>
                    
                    <!-- Notificações -->
                    <div class="relative" x-data="{ 
                        notificationsOpen: false, 
                        unreadCount: 0,
                        notifications: [],
                        async fetchNotifications() {
                            try {
                                const response = await fetch('{{ route('dashboard.notifications.unread') }}');
                                const data = await response.json();
                                this.notifications = data.notifications;
                                this.unreadCount = data.unreadCount;
                                
                                // Se houver novas notificações e a permissão de Push for concedida
                                if (this.unreadCount > 0 && Notification.permission === 'granted') {
                                    this.notifications.forEach(n => {
                                        if (!n.is_pushed) {
                                            new Notification(n.title, { body: n.message, icon: '{{ $logoUrl }}' });
                                            // Marcar como 'pushed' para não repetir
                                            fetch(`/dashboard/notifications/${n.id}/mark-pushed`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                                        }
                                    });
                                }
                            } catch (e) { console.error('Erro ao buscar notificações'); }
                        }
                    }" x-init="
                        fetchNotifications(); 
                        setInterval(() => fetchNotifications(), 10000);
                        if (Notification.permission === 'default') Notification.requestPermission();
                    ">
                        <button 
                            @click="notificationsOpen = !notificationsOpen"
                            @click.away="notificationsOpen = false"
                            class="p-2 text-slate-500 hover:text-slate-700 transition-colors rounded-3xl hover:bg-slate-100 relative"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white" x-text="unreadCount"></span>
                            </template>
                        </button>

                        <div 
                            x-show="notificationsOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 top-12 w-80 bg-white rounded-3xl shadow-2xl border border-slate-100 z-50 overflow-hidden"
                            style="display: none;"
                        >
                            <div class="p-4 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                                <h3 class="font-bold text-slate-800 text-sm">Notificações</h3>
                                <button @click="unreadCount = 0" class="text-[10px] text-blue-600 font-bold hover:underline">Marcar todas como lidas</button>
                            </div>
                            <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                <template x-if="notifications.length === 0">
                                    <div class="p-8 text-center">
                                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8"></path></svg>
                                        </div>
                                        <p class="text-xs text-slate-400">Nenhuma notificação nova</p>
                                    </div>
                                </template>
                                <template x-for="n in notifications" :key="n.id">
                                    <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors cursor-pointer relative group">
                                        <div class="flex gap-3">
                                            <div :class="n.type === 'success' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600'" class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="n.type === 'success'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="n.type !== 'success'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-slate-900 mb-0.5" x-text="n.title"></p>
                                                <p class="text-[10px] text-slate-500 leading-relaxed" x-text="n.message"></p>
                                                <p class="text-[8px] text-slate-300 mt-1" x-text="n.created_at_human"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Seletor de Idioma -->
                    <div class="relative" x-data="{ languageMenuOpen: false }">
                        <button 
                            @click="languageMenuOpen = !languageMenuOpen"
                            @click.away="languageMenuOpen = false"
                            class="p-2 text-slate-500 hover:text-slate-700 transition-colors rounded-3xl hover:bg-slate-100"
                            title="{{ __('common.language') }}"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                        </button>
                        <div 
                            x-show="languageMenuOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 top-12 w-48 bg-white rounded-3xl shadow-xl border border-slate-100 py-2 z-50"
                            style="display: none;"
                        >
                            <a 
                                href="{{ route('language.change', 'pt') }}"
                                class="flex items-center px-6 py-3 text-slate-600 hover:bg-slate-50 transition-colors {{ app()->getLocale() === 'pt' ? 'bg-slate-50 font-medium text-blue-600' : '' }}"
                                @click="languageMenuOpen = false"
                            >
                                <span class="mr-2">🇧🇷</span>
                                <span>Português (BR)</span>
                                @if(app()->getLocale() === 'pt')
                                    <svg class="w-4 h-4 ml-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @endif
                            </a>
                            <a 
                                href="{{ route('language.change', 'es') }}"
                                class="flex items-center px-6 py-3 text-slate-600 hover:bg-slate-50 transition-colors {{ app()->getLocale() === 'es' ? 'bg-slate-50 font-medium text-blue-600' : '' }}"
                                @click="languageMenuOpen = false"
                            >
                                <span class="mr-2">🇪🇸</span>
                                <span>Español</span>
                                @if(app()->getLocale() === 'es')
                                    <svg class="w-4 h-4 ml-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @endif
                            </a>
                            <a 
                                href="{{ route('language.change', 'en') }}"
                                class="flex items-center px-6 py-3 text-slate-600 hover:bg-slate-50 transition-colors {{ app()->getLocale() === 'en' ? 'bg-slate-50 font-medium text-blue-600' : '' }}"
                                @click="languageMenuOpen = false"
                            >
                                <span class="mr-2">🇺🇸</span>
                                <span>English</span>
                                @if(app()->getLocale() === 'en')
                                    <svg class="w-4 h-4 ml-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @endif
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 relative">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-slate-700">
                                <span x-show="showBalances">{{ Auth::user()->name }}</span>
                                <span x-show="!showBalances" x-cloak>•••••</span>
                            </p>
                            <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                        </div>
                        <button 
                            @click="profileMenuOpen = !profileMenuOpen"
                            @click.away="profileMenuOpen = false"
                            class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold hover:ring-2 transition-all cursor-pointer overflow-hidden shadow-md"
                            style="background: linear-gradient(to bottom right, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }});"
                            onmouseover="this.style.boxShadow='0 0 0 2px {{ $themeColors['primary'] }}';"
                            onmouseout="this.style.boxShadow='none';"
                        >
                            @if(Auth::user()->profile_photo)
                                <img src="{{ asset(strpos(Auth::user()->profile_photo, 'IMG/') === 0 ? Auth::user()->profile_photo : ('storage/' . Auth::user()->profile_photo)) }}" alt="Foto de perfil de {{ Auth::user()->name }}" class="w-full h-full object-cover" loading="lazy" width="40" height="40">
                            @else
                                <span x-show="showBalances">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                <span x-show="!showBalances" x-cloak>•</span>
                            @endif
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div 
                            x-show="profileMenuOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 top-12 w-56 bg-white rounded-3xl shadow-xl border border-slate-100 py-2 z-50 p-2"
                            style="display: none;"
                        >
                            <a 
                                href="{{ route('dashboard.profile.index') }}" 
                                class="flex items-center px-6 py-3 text-slate-600 hover:bg-slate-50 transition-colors rounded-xl"
                                @click="profileMenuOpen = false"
                            >
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Perfil
                            </a>
                            <a 
                                href="{{ route('dashboard.settings.index') }}" 
                                class="flex items-center px-6 py-3 text-slate-600 hover:bg-slate-50 transition-colors rounded-xl"
                                @click="profileMenuOpen = false"
                            >
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Configurações
                            </a>
                            <div class="border-t border-slate-100 my-2"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button 
                                    type="submit"
                                    class="w-full flex items-center px-4 py-3 text-red-500 hover:bg-red-50 transition-colors rounded-xl"
                                    @click="profileMenuOpen = false"
                                >
                                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-slate-50">
                @yield('content')
            </main>
        </div>
    </div>

    </body>
</html>
