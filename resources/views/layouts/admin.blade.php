<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        use App\Helpers\LogoHelper;
        $logoUrl = LogoHelper::getLogoUrl();
        $faviconUrl = LogoHelper::getFaviconUrl();
        $systemName = LogoHelper::getSystemName();
    @endphp
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Admin') - {{ $systemName }}</title>
    <meta name="description" content="@yield('description', 'Painel administrativo da plataforma ' . $systemName)">
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
        
        /* Scrollbar Moderna - White Theme */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; transition: background 0.3s ease; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
        .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #CBD5E1 transparent; }
    </style>
    
    <script>
        // Trigger de carregamento imediato para evitar tela branca por muito tempo
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
    
    <!-- CSS de Otimizações Globais -->
    <link rel="stylesheet" href="{{ asset('optimizations.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('optimizations.css') }}"></noscript>
    
    <!-- Tratamento global de erros JavaScript -->
    <script>
        window.addEventListener('error', function(e) {
            console.error('Erro JavaScript capturado:', e.error);
            e.preventDefault();
            return true;
        });
        
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Promise rejeitada não tratada:', e.reason);
            e.preventDefault();
        });
    </script>
    
    <!-- Scripts otimizados -->
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
<body class="bg-slate-50 text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Admin -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:static inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 ease-in-out lg:translate-x-0 sidebar-transition"
            role="navigation"
            aria-label="Menu administrativo"
        >
            <div class="flex flex-col h-full bg-white rounded-r-lg border-r border-slate-200 shadow-2xl">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $systemName }} Logo" class="h-8 object-contain" loading="eager" width="160" height="40">
                            <span class="text-xs text-blue-600 font-medium">Admin</span>
                        @else
                            <h1 class="text-2xl font-bold text-slate-800">{{ $systemName }} <span class="text-xs text-blue-600 font-medium">Admin</span></h1>
                        @endif
                    </div>
                    <button 
                        @click="sidebarOpen = false"
                        class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1 transition-all"
                        aria-label="Fechar menu"
                        type="button"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Menu Admin -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
                    <a 
                        href="{{ route('admin.dashboard') }}" 
                        class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-500' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Visão Geral
                    </a>

                    <a 
                        href="{{ route('admin.gateways.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.gateways.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Adquirentes
                    </a>

                    <a 
                        href="{{ route('admin.kyc.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.kyc.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        KYC Pendentes
                        @php
                            try {
                                $pendingKyc = \App\Models\User::where('kyc_status', 'pending')->count();
                            } catch (\Exception $e) {
                                $pendingKyc = 0;
                            }
                        @endphp
                        @if($pendingKyc > 0)
                            <span class="ml-auto text-white text-xs font-bold rounded-full min-w-[20px] h-5 px-1.5 flex items-center justify-center bg-blue-600">{{ $pendingKyc }}</span>
                        @endif
                    </a>

                    <a 
                        href="{{ route('admin.users.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Usuários
                    </a>

                    <a 
                        href="{{ route('admin.transactions.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.transactions.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Transações
                    </a>

                    <a 
                        href="{{ route('admin.withdrawals.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.withdrawals.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Saques
                        @php
                            try {
                                $pendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();
                            } catch (\Exception $e) {
                                $pendingWithdrawals = 0;
                            }
                        @endphp
                        @if($pendingWithdrawals > 0)
                            <span class="ml-auto text-white text-xs font-bold rounded-full min-w-[20px] h-5 px-1.5 flex items-center justify-center bg-blue-600">{{ $pendingWithdrawals }}</span>
                        @endif
                    </a>

                    <a 
                        href="{{ route('admin.chargebacks.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.chargebacks.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Chargebacks / MED
                        @php
                            try {
                                $pendingChargebacks = \App\Models\Chargeback::where('status', 'pending')->count();
                            } catch (\Exception $e) {
                                $pendingChargebacks = 0;
                            }
                        @endphp
                        @if($pendingChargebacks > 0)
                            <span class="ml-auto text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center bg-blue-600">{{ $pendingChargebacks }}</span>
                        @endif
                    </a>

                    <a 
                        href="{{ route('admin.support.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.support.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        Suporte
                    </a>


                    <a 
                        href="{{ route('admin.configs.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.configs.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Configurações
                    </a>

                    <a 
                        href="{{ route('admin.smtp.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.smtp.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        SMTP / Email
                    </a>

                    <a 
                        href="{{ route('admin.email-campaigns.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.email-campaigns.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        Campanhas de Email
                    </a>

                    <a 
                        href="{{ route('admin.awards.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.awards.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prêmios
                    </a>

                    <a 
                        href="{{ route('admin.landing.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.landing.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Landing Page
                    </a>

                    <a 
                        href="{{ route('admin.static.index') }}" 
                        class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.static.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Páginas Estáticas
                    </a>
                </nav>

                <!-- Footer Sidebar -->
                <div class="p-4 border-t border-slate-100">
                    <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-3 text-slate-500 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar ao Dashboard
                    </a>
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
                    class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1 transition-all"
                    aria-label="Abrir menu"
                    type="button"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="flex items-center space-x-4 ml-auto" x-data="{ profileMenuOpen: false }">
                    <div class="relative">
                        <button 
                            @click="profileMenuOpen = !profileMenuOpen"
                            class="flex items-center space-x-3 focus:outline-none"
                        >
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-blue-600 font-medium">Administrador</p>
                            </div>
                            @if(Auth::user()->profile_photo)
                                <img src="{{ asset(Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover shadow-md border-2 border-white">
                            @else
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold bg-blue-600 shadow-md border-2 border-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div 
                            x-show="profileMenuOpen"
                            @click.away="profileMenuOpen = false"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-100 py-1 z-50"
                        >
                            <a 
                                href="{{ route('dashboard.profile.index') }}"
                                class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                            >
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Perfil
                                </div>
                            </a>
                            <a 
                                href="{{ route('dashboard.settings.index') }}"
                                class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                            >
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Configurações
                                </div>
                            </a>
                            <hr class="my-1 border-slate-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button 
                                    type="submit"
                                    class="w-full text-left block px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors"
                                >
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Sair
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">
                @if(session('success'))
                    <div class="mb-6 px-4 py-3 rounded-lg bg-blue-500/20 border border-emerald-500/50 text-blue-400">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 px-4 py-3 rounded-lg bg-red-500/20 border border-red-500/50 text-red-400">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
