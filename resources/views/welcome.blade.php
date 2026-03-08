<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    @php
        use App\Helpers\LogoHelper;
        use App\Helpers\ThemeHelper;
        $systemName = LogoHelper::getSystemName();
        $themeColors = ThemeHelper::getThemeColors();
        $logoUrl = LogoHelper::getLogoUrl();
        $faviconUrl = LogoHelper::getFaviconUrl();
        $primaryColor = $themeColors['primary'] ?? '#00B2FF';
        $secondaryColor = $themeColors['secondary'] ?? '#00D9AC';
        $metaTitle = $landingSettings['meta_title'] ?? $systemName . ' - Gateway de Pagamentos e Plataforma Whitelabel';
        $metaDescription = $landingSettings['meta_description'] ?? 'Plataforma completa de pagamentos para usuários e gateways. Processe pagamentos PIX e Cartão com taxas competitivas.';
    @endphp
    
    <!-- SEO Meta Tags -->
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="gateway de pagamentos, pagamentos online, PIX, whitelabel, API de pagamentos, {{ $systemName }}">
    <meta name="author" content="{{ $systemName }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url('/') }}">
    @if($logoUrl)
    <meta property="og:image" content="{{ $logoUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @endif
    <meta property="og:site_name" content="{{ $systemName }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'pt' ? 'pt_BR' : (app()->getLocale() === 'es' ? 'es_ES' : 'en_US') }}">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if($logoUrl)
    <meta name="twitter:image" content="{{ $logoUrl }}">
    @endif
    
    <!-- Favicon -->
    @if($faviconUrl)
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @endif
    
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --primary: {{ $primaryColor }};
            --secondary: {{ $secondaryColor }};
            --primary-rgb: {{ hexToRgb($primaryColor) }};
        }

        @php
            function hexToRgb($hex) {
                $hex = str_replace("#", "", $hex);
                if(strlen($hex) == 3) {
                    $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                    $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                    $b = hexdec(substr($hex,2,1).substr($hex,2,1));
                } else {
                    $r = hexdec(substr($hex,0,2));
                    $g = hexdec(substr($hex,2,2));
                    $b = hexdec(substr($hex,4,2));
                }
                return "$r, $g, $b";
            }
        @endphp

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            overflow-x: hidden;
        }
        
        .font-display { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        
        /* Fundo com gradiente sutil e partículas */
        .bg-subtle-blue {
            background: radial-gradient(circle at top right, rgba(var(--primary-rgb), 0.05), transparent),
                        radial-gradient(circle at bottom left, rgba(var(--primary-rgb), 0.02), transparent);
        }

        .text-gradient-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Cards Modernos Light com Glassmorphism sutil */
        .modern-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(var(--primary-rgb), 0.1);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modern-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
            border-color: rgba(var(--primary-rgb), 0.3);
        }
        
        /* Efeito de brilho no hover */
        .glow-hover:hover {
            box-shadow: 0 0 20px rgba(var(--primary-rgb), 0.4);
        }

        /* Blobs de Fundo Animados */
        .blob {
            position: absolute;
            filter: blur(100px);
            z-index: -1;
            opacity: 0.4;
            animation: float-blob 20s infinite alternate cubic-bezier(0.45, 0, 0.55, 1);
        }
        .blob-1 { background: var(--primary); width: 800px; height: 800px; border-radius: 50%; top: -10%; right: -5%; }
        .blob-2 { background: var(--secondary); width: 600px; height: 600px; border-radius: 50%; bottom: -10%; left: -5%; animation-delay: -5s; }
        .blob-3 { background: #818cf8; width: 400px; height: 400px; border-radius: 50%; top: 40%; left: 30%; opacity: 0.2; animation-duration: 15s; }
        
        @keyframes float-blob {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            33% { transform: translate(100px, 50px) rotate(120deg) scale(1.1); }
            66% { transform: translate(-50px, 150px) rotate(240deg) scale(0.9); }
            100% { transform: translate(0, 0) rotate(360deg) scale(1); }
        }

        /* Floating elements animation */
        .animate-float-slow { animation: float-slow 6s infinite ease-in-out; }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Reveal on scroll */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Scrollbar customizada */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 3px solid #f1f5f9; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        /* Loader refinado */
        #page-loader {
            position: fixed; inset: 0; background: #ffffff; z-index: 9999;
            display: flex; justify-content: center; align-items: center;
            transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .loader-ring {
            width: 80px; height: 80px; border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary); border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="antialiased selection:bg-blue-100 selection:text-blue-900 overflow-x-hidden">

    <!-- Loader -->
    <div id="page-loader">
        <div class="flex flex-col items-center gap-4">
            <div class="loader-ring"></div>
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="h-10 w-auto animate-pulse">
            @else
                <span class="text-xl font-bold text-slate-900 animate-pulse">{{ $systemName }}</span>
            @endif
        </div>
    </div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 border-b border-transparent" id="navbar">
        <div class="absolute inset-0 bg-white/70 backdrop-blur-xl -z-10 shadow-sm opacity-0 transition-opacity duration-500" id="navbar-bg"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $systemName }}" class="h-10 w-auto object-contain transition-transform hover:scale-110 duration-300">
                    @else
                        <span class="text-2xl font-bold font-display tracking-tight text-slate-900">{{ $systemName }}</span>
                    @endif
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-10">
                    <a href="#solucoes" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all hover:-translate-y-0.5">Soluções</a>
                    <a href="#taxas" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all hover:-translate-y-0.5">Taxas</a>
                    <a href="#api" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all hover:-translate-y-0.5">API</a>
                    <a href="{{ route('landing.app') }}" class="text-sm font-bold text-blue-600 hover:text-blue-700 transition-all hover:-translate-y-0.5">Baixe o APP</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-6">
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('login') }}" class="text-sm font-bold text-slate-700 hover:text-blue-600 transition-colors">Entrar</a>
                        <a href="{{ route('auth.register') }}" class="px-6 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/25 hover:bg-blue-700 hover:shadow-blue-500/40 transition-all hover:-translate-y-1 active:scale-95">
                            Criar Conta
                        </a>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="fixed inset-0 z-[60] bg-white translate-x-full transition-transform duration-500 ease-in-out md:hidden overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-12">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $systemName }}" class="h-8 w-auto">
                @else
                    <span class="text-xl font-bold text-slate-900">{{ $systemName }}</span>
                @endif
                <button id="close-mobile-menu" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <nav class="space-y-6">
                <a href="#solucoes" class="mobile-link block text-2xl font-bold text-slate-800 hover:text-blue-600 transition-colors">Soluções</a>
                <a href="#taxas" class="mobile-link block text-2xl font-bold text-slate-800 hover:text-blue-600 transition-colors">Taxas</a>
                <a href="#api" class="mobile-link block text-2xl font-bold text-slate-800 hover:text-blue-600 transition-colors">API</a>
                <a href="{{ route('landing.app') }}" class="mobile-link block text-2xl font-bold text-blue-600 hover:text-blue-700 transition-colors">Baixe o APP</a>
                
                <div class="pt-10 border-t border-slate-100 space-y-4">
                    <a href="{{ route('login') }}" class="block w-full py-4 text-center font-bold text-slate-700 bg-slate-50 rounded-2xl">Entrar</a>
                    <a href="{{ route('auth.register') }}" class="block w-full py-4 text-center font-bold text-white bg-blue-600 rounded-2xl shadow-lg shadow-blue-500/25">Criar Conta</a>
                </div>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-56 lg:pb-40 overflow-hidden min-h-screen flex items-center bg-subtle-blue">
        <!-- Background Effects -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMzAsIDU4LCAxMzgsIDAuMDUpIi8+PC9zdmc+')] [mask-image:linear-gradient(to_bottom,white,transparent)]"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <!-- Text Content -->
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 border border-blue-100 mb-10 animate-float-slow">
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600"></span>
                        </span>
                        <span class="text-xs font-black text-blue-700 uppercase tracking-widest">{{ $landingSettings['hero_badge'] ?? '🚀 A Nova Era dos Pagamentos' }}</span>
                    </div>
                    
                    <h1 class="text-6xl sm:text-7xl lg:text-8xl font-black font-display text-slate-900 leading-[1.05] mb-10 tracking-tight">
                        {{ $landingSettings['hero_title'] ?? 'O Futuro dos Pagamentos é Agora' }}
                    </h1>
                    
                    <p class="text-xl sm:text-2xl text-slate-600 mb-12 leading-relaxed max-w-xl border-l-4 border-blue-500 pl-8">
                        {{ $landingSettings['hero_subtitle'] ?? 'Infraestrutura completa para você processar pagamentos PIX e Cartão com segurança, escala e as melhores taxas do mercado.' }}
                    </p>
                    
                    <!-- Floating Emojis -->
                    <div class="absolute -top-10 right-1/4 text-4xl animate-float-slow opacity-50 select-none">💸</div>
                    <div class="absolute top-1/2 -left-20 text-4xl animate-float-slow opacity-30 select-none" style="animation-delay: -2s">🛡️</div>
                    <div class="absolute bottom-0 right-10 text-4xl animate-float-slow opacity-40 select-none" style="animation-delay: -4s">⚡</div>

                    <div class="flex flex-col sm:flex-row gap-6">
                        <a href="{{ route('auth.register') }}" class="inline-flex justify-center items-center px-10 py-5 bg-blue-600 text-white font-black text-lg rounded-2xl hover:bg-blue-700 hover:shadow-2xl hover:shadow-blue-500/40 hover:-translate-y-1.5 transition-all duration-300 group">
                            {{ $landingSettings['hero_cta_text'] ?? 'Começar Gratuitamente' }}
                            <svg class="w-6 h-6 ml-3 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        <a href="#solucoes" class="inline-flex justify-center items-center px-10 py-5 bg-white text-slate-800 font-bold text-lg rounded-2xl border-2 border-slate-100 hover:bg-slate-50 hover:border-blue-200 hover:shadow-xl transition-all">
                            ✨ Ver Soluções
                        </a>
                    </div>

                    <div class="mt-20 pt-10 border-t border-slate-200/60 grid grid-cols-3 gap-10">
                        <div class="group cursor-default">
                            <p class="text-4xl font-black text-slate-900 group-hover:text-blue-600 transition-colors">{{ $landingSettings['hero_stats1_value'] ?? '+10k' }}</p>
                            <p class="text-xs text-slate-500 mt-2 uppercase tracking-widest font-black">{{ $landingSettings['hero_stats1_label'] ?? 'Empresas' }}</p>
                        </div>
                        <div class="group cursor-default">
                            <p class="text-4xl font-black text-slate-900 group-hover:text-blue-600 transition-colors">{{ $landingSettings['hero_stats2_value'] ?? 'R$ 50M+' }}</p>
                            <p class="text-xs text-slate-500 mt-2 uppercase tracking-widest font-black">{{ $landingSettings['hero_stats2_label'] ?? 'Transacionado' }}</p>
                        </div>
                        <div class="group cursor-default">
                            <p class="text-4xl font-black text-slate-900 group-hover:text-blue-600 transition-colors">{{ $landingSettings['hero_stats3_value'] ?? '99.99%' }}</p>
                            <p class="text-xs text-slate-500 mt-2 uppercase tracking-widest font-black">{{ $landingSettings['hero_stats3_label'] ?? 'Uptime' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Visual Content (Interactive Dashboard Mockup) -->
                <div class="relative lg:ml-auto hidden lg:block perspective-2000 group">
                    <div class="absolute -inset-10 bg-gradient-to-tr from-blue-500/20 to-purple-500/20 rounded-[3rem] blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
                    
                    <!-- Floating Badge -->
                    <div class="absolute -top-10 -left-10 bg-white p-6 rounded-3xl shadow-2xl border border-slate-100 z-20 animate-float-slow transition-transform group-hover:scale-110">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">PIX Recebido</p>
                                <p class="text-xl font-black text-slate-900">R$ 1.450,00</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative rounded-[2.5rem] bg-slate-900/5 shadow-2xl p-3 transform rotate-y-12 rotate-x-6 hover:rotate-y-0 hover:rotate-x-0 transition-all duration-1000 ease-out preserve-3d">
                        <div class="bg-white rounded-[2rem] overflow-hidden shadow-inner border border-white">
                            <!-- Dashboard CSS Mockup Light -->
                            <div class="bg-slate-50 rounded-xl overflow-hidden">
                                <!-- Header -->
                                <div class="h-14 bg-white border-b border-slate-100 flex items-center px-6 gap-3">
                                    <div class="flex gap-1.5">
                                        <div class="w-3.5 h-3.5 rounded-full bg-red-400"></div>
                                        <div class="w-3.5 h-3.5 rounded-full bg-yellow-400"></div>
                                        <div class="w-3.5 h-3.5 rounded-full bg-green-400"></div>
                                    </div>
                                    <div class="ml-6 h-6 w-full max-w-md bg-slate-100 rounded-full"></div>
                                </div>
                                <!-- Content -->
                                <div class="p-10 bg-white">
                                    <div class="flex justify-between items-end mb-12">
                                        <div>
                                            <p class="text-sm font-bold text-slate-400 mb-2 uppercase tracking-widest">Receita Acumulada</p>
                                            <p class="text-5xl font-black text-slate-900 tracking-tighter">R$ 1.240.592,00</p>
                                        </div>
                                        <div class="px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-100 text-sm font-black rounded-2xl flex items-center gap-2 animate-pulse">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                            +12.5%
                                        </div>
                                    </div>
                                    <!-- Chart Bars -->
                                    <div class="flex items-end gap-4 h-56 mb-12">
                                        @foreach([40, 65, 45, 85, 55, 95, 75] as $h)
                                            <div class="flex-1 bg-blue-50 rounded-2xl h-[{{ $h }}%] relative group/bar overflow-hidden">
                                                <div class="absolute bottom-0 w-full bg-blue-600 h-0 group-hover/bar:h-full transition-all duration-700 ease-out"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="grid grid-cols-2 gap-6">
                                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 hover:border-blue-200 transition-colors">
                                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center mb-4 shadow-lg shadow-blue-500/30">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            </div>
                                            <p class="text-lg font-black text-slate-800">Vendas Hoje</p>
                                            <p class="text-sm font-bold text-slate-500">1.234 transações</p>
                                        </div>
                                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 hover:border-cyan-200 transition-colors">
                                            <div class="w-12 h-12 rounded-2xl bg-cyan-500 text-white flex items-center justify-center mb-4 shadow-lg shadow-cyan-500/30">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <p class="text-lg font-black text-slate-800">Taxa de Aprovação</p>
                                            <p class="text-sm font-bold text-slate-500">98.5% conversão</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By / Social Proof -->
    <section class="py-12 border-y border-slate-100 bg-white reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm font-bold text-slate-400 uppercase tracking-widest mb-10">Confiado pelas maiores empresas do mercado</p>
            <div class="flex flex-wrap justify-center items-center gap-12 md:gap-20 opacity-40 grayscale hover:grayscale-0 transition-all duration-700">
                <!-- Logos reais e confiáveis via CDN SimpleIcons -->
                <img src="https://cdn.simpleicons.org/mercadopago/009EE3" alt="Mercado Pago" class="h-8 md:h-10 w-auto object-contain">
                <img src="https://cdn.simpleicons.org/nubank/8A05BE" alt="Nubank" class="h-8 md:h-10 w-auto object-contain">
                <img src="https://cdn.simpleicons.org/pagseguro/1B3A5A" alt="PagSeguro" class="h-8 md:h-10 w-auto object-contain">
                <img src="https://cdn.simpleicons.org/visa/1A1F71" alt="Visa" class="h-8 md:h-10 w-auto object-contain">
                <img src="https://cdn.simpleicons.org/mastercard/EB001B" alt="Mastercard" class="h-8 md:h-10 w-auto object-contain">
                <img src="https://cdn.simpleicons.org/americanexpress/007BC1" alt="Amex" class="h-8 md:h-10 w-auto object-contain">
                <img src="https://cdn.simpleicons.org/pix/32BCAD" alt="PIX" class="h-8 md:h-10 w-auto object-contain">
            </div>
        </div>
    </section>

    <!-- Steps Section -->
    <section class="py-24 relative overflow-hidden bg-white reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-3xl md:text-4xl font-bold font-display text-slate-900 mb-6">{{ $landingSettings['steps_title'] ?? 'Comece em 3 Passos Simples' }}</h2>
                <p class="text-lg text-slate-600">{{ $landingSettings['steps_subtitle'] ?? 'Descomplicamos o processo para você começar a faturar hoje mesmo.' }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                <!-- Linha conectora (Desktop) -->
                <div class="hidden md:block absolute top-12 left-0 w-full h-0.5 bg-gradient-to-r from-blue-100 via-blue-200 to-blue-100"></div>

                <!-- Step 1 -->
                <div class="relative z-10 text-center group">
                    <div class="w-24 h-24 mx-auto bg-white rounded-full border-4 border-slate-100 flex items-center justify-center mb-6 group-hover:border-blue-500 transition-colors duration-300 shadow-xl shadow-blue-900/5">
                        <span class="text-3xl font-bold text-slate-800 group-hover:text-blue-600 transition-colors">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $landingSettings['step1_title'] ?? 'Crie sua Conta' }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed px-4">{{ $landingSettings['step1_text'] ?? 'Cadastro rápido e gratuito. Aprovação automática de documentos em minutos.' }}</p>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 text-center group">
                    <div class="w-24 h-24 mx-auto bg-white rounded-full border-4 border-slate-100 flex items-center justify-center mb-6 group-hover:border-blue-500 transition-colors duration-300 shadow-xl shadow-blue-900/5">
                        <span class="text-3xl font-bold text-slate-800 group-hover:text-blue-600 transition-colors">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $landingSettings['step2_title'] ?? 'Configure' }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed px-4">{{ $landingSettings['step2_text'] ?? 'Personalize seu checkout, integre via API ou use nossos plugins prontos.' }}</p>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 text-center group">
                    <div class="w-24 h-24 mx-auto bg-white rounded-full border-4 border-slate-100 flex items-center justify-center mb-6 group-hover:border-blue-500 transition-colors duration-300 shadow-xl shadow-blue-900/5">
                        <span class="text-3xl font-bold text-slate-800 group-hover:text-blue-600 transition-colors">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $landingSettings['step3_title'] ?? 'Comece a Vender' }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed px-4">{{ $landingSettings['step3_text'] ?? 'Receba pagamentos via PIX e Cartão com as melhores taxas do mercado.' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Segments Section -->
    <section class="py-24 bg-white reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="text-blue-600 font-bold text-sm uppercase tracking-widest mb-4 block">Flexibilidade Total</span>
                <h2 class="text-4xl md:text-5xl font-black font-display text-slate-900 mb-6">Soluções para cada tipo de negócio</h2>
                <p class="text-xl text-slate-600">Não importa o tamanho da sua empresa, o {{ $systemName }} tem a tecnologia certa para você.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- E-commerce -->
                <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 hover:bg-white hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-8 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">E-commerce</h3>
                    <p class="text-slate-500 leading-relaxed mb-6">Venda em qualquer lugar com checkouts otimizados e alta taxa de aprovação.</p>
                    <ul class="space-y-3 text-sm font-bold text-slate-700">
                        <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div> Checkout Transparente</li>
                        <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div> Recuperação de Vendas</li>
                    </ul>
                </div>

                <!-- SaaS & Assinaturas -->
                <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 hover:bg-white hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-8 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">SaaS</h3>
                    <p class="text-slate-500 leading-relaxed mb-6">Gestão completa de recorrência e assinaturas com cobrança automática.</p>
                    <ul class="space-y-3 text-sm font-bold text-slate-700">
                        <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-purple-500 rounded-full"></div> Cobrança Recorrente</li>
                        <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-purple-500 rounded-full"></div> Gestão de Planos</li>
                    </ul>
                </div>

                <!-- Marketplaces -->
                <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 hover:bg-white hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">Marketplaces</h3>
                    <p class="text-slate-500 leading-relaxed mb-6">Divisão de pagamentos (split) complexa e gestão de múltiplos vendedores.</p>
                    <ul class="space-y-3 text-sm font-bold text-slate-700">
                        <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></div> Split de Pagamento</li>
                        <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></div> KYC Automatizado</li>
                    </ul>
                </div>

                <!-- Infoprodutos -->
                <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 hover:bg-white hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 mb-8 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">Infoprodutos</h3>
                    <p class="text-slate-500 leading-relaxed mb-6">Ideal para cursos, mentorias e comunidades com entrega imediata.</p>
                    <ul class="space-y-3 text-sm font-bold text-slate-700">
                        <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-amber-500 rounded-full"></div> Webhooks de Entrega</li>
                        <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-amber-500 rounded-full"></div> Área de Membros</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="solucoes" class="py-24 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold font-display text-slate-900 mb-4">{{ $landingSettings['features_title'] ?? 'Soluções Completas' }}</h2>
                <p class="text-lg text-slate-600">{{ $landingSettings['features_subtitle'] ?? 'Tudo o que você precisa para escalar suas vendas online com segurança e estabilidade.' }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="modern-card p-8 rounded-2xl group">
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform border border-blue-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $landingSettings['feature1_title'] ?? 'Alta Conversão' }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">{{ $landingSettings['feature1_text'] ?? 'Checkout otimizado para máxima performance. Recuperação de carrinho e one-click buy nativos.' }}</p>
                </div>

                <!-- Card 2 -->
                <div class="modern-card p-8 rounded-2xl group">
                    <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mb-6 group-hover:scale-110 transition-transform border border-purple-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $landingSettings['feature2_title'] ?? 'Segurança Total' }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">{{ $landingSettings['feature2_text'] ?? 'Anti-fraude integrado com inteligência artificial. Proteção contra chargebacks e monitoramento 24/7.' }}</p>
                </div>

                <!-- Card 3 -->
                <div class="modern-card p-8 rounded-2xl group">
                    <div class="w-14 h-14 bg-cyan-50 rounded-xl flex items-center justify-center text-cyan-600 mb-6 group-hover:scale-110 transition-transform border border-cyan-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $landingSettings['feature3_title'] ?? 'Pix Instantâneo' }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">{{ $landingSettings['feature3_text'] ?? 'Recebimento e conciliação automática via Pix. QR Code dinâmico e suporte a Pix Copia e Cola.' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- API / Developers Section -->
    <section id="api" class="py-24 bg-white relative overflow-hidden reveal">
        <div class="absolute inset-0 bg-slate-50 skew-y-3 transform origin-bottom-right -z-10 h-1/2 bottom-0"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 border border-cyan-100 mb-6">
                        <span class="text-xs font-bold text-cyan-700 uppercase tracking-wide">Developer Friendly</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold font-display text-slate-900 mb-6">{{ $landingSettings['api_title'] ?? 'API Robusta para Desenvolvedores' }}</h2>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        {{ $landingSettings['api_text'] ?? 'Nossa API RESTful foi construída pensando na experiência do desenvolvedor. Documentação clara, webhooks em tempo real e SDKs nas principais linguagens.' }}
                    </p>
                    
                    <ul class="space-y-4 mb-10">
                        <li class="flex items-center text-slate-700">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Integração simples e bem documentada
                        </li>
                        <li class="flex items-center text-slate-700">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Ambiente de Sandbox para testes
                        </li>
                        <li class="flex items-center text-slate-700">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Webhooks para notificações instantâneas
                        </li>
                    </ul>

                    <a href="{{ route('landing.documentation') }}" class="text-blue-600 font-semibold hover:text-blue-800 flex items-center gap-2 group">
                        Ler Documentação da API
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>

                <!-- Code Block (Dark Mode preserved for contrast) -->
                <div class="bg-[#0f172a] rounded-xl border border-slate-800 shadow-2xl overflow-hidden font-mono text-sm relative group">
                    <div class="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="text-xs text-gray-400 hover:text-white bg-white/5 px-2 py-1 rounded">Copy</button>
                    </div>
                    <div class="flex items-center px-4 py-3 bg-[#1e293b] border-b border-white/5 gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="ml-2 text-xs text-gray-500">POST /v1/transactions</span>
                    </div>
                    <div class="p-6 text-gray-300 overflow-x-auto">
<pre><code><span class="text-purple-400">curl</span> -X POST https://api.{{ strtolower(str_replace(' ', '', $systemName)) }}.com/v1/transactions \
  -H <span class="text-green-400">"Authorization: Bearer sk_test_..."</span> \
  -H <span class="text-green-400">"Content-Type: application/json"</span> \
  -d '{
    <span class="text-blue-400">"amount"</span>: 1000,
    <span class="text-blue-400">"payment_method"</span>: "pix",
    <span class="text-blue-400">"customer"</span>: {
      <span class="text-blue-400">"name"</span>: "João Silva",
      <span class="text-blue-400">"email"</span>: "joao@email.com"
    }
  }'</code></pre>
                    </div>
                </div>
            </div>

            <!-- Ecosystem / Integrations -->
            <div class="mt-32 pt-20 border-t border-slate-100 reveal">
                <div class="text-center mb-12">
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Integrações Nativas</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center opacity-40 grayscale hover:grayscale-0 transition-all duration-700">
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="w-16 h-16 bg-white shadow-xl rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform p-3">
                            <img src="https://cdn.simpleicons.org/woocommerce/96588A" alt="WooCommerce" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-900 transition-colors">WooCommerce</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="w-16 h-16 bg-white shadow-xl rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform p-3">
                            <img src="https://cdn.simpleicons.org/shopify/96BF48" alt="Shopify" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-900 transition-colors">Shopify</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="w-16 h-16 bg-white shadow-xl rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform p-3">
                            <img src="https://www.citypng.com/public/uploads/preview/magento-logo-icon-hd-png-701751694968127smfdayuwdf.png" alt="Magento" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-900 transition-colors">Magento</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="w-16 h-16 bg-white shadow-xl rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform p-3">
                            <img src="https://cdn.simpleicons.org/prestashop/DF0067" alt="PrestaShop" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-900 transition-colors">PrestaShop</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="w-16 h-16 bg-white shadow-xl rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform p-3">
                            <img src="https://www.epotentia.com/wp-content/uploads/2015/11/whmcs-logo.png" alt="WHMCS" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-900 transition-colors">WHMCS</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="w-16 h-16 bg-white shadow-xl rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform p-3">
                            <img src="https://cdn.simpleicons.org/vuedotjs/4FC08D" alt="API" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-900 transition-colors">API & SDKs</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security & Infrastructure -->
    <section class="py-24 bg-white reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="absolute inset-0 bg-blue-600 rounded-[3rem] rotate-3 opacity-5"></div>
                    <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&q=80&w=800" alt="Security" class="relative rounded-[3rem] shadow-2xl border border-slate-100 grayscale hover:grayscale-0 transition-all duration-700">
                    <!-- Floating Shield Badge -->
                    <div class="absolute -bottom-10 -right-10 bg-white p-6 rounded-3xl shadow-2xl border border-slate-100 animate-float-slow">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900">Segurança Nível Bancário</p>
                                <p class="text-xs text-slate-500 font-bold uppercase">PCI-DSS Compliant</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-blue-600 font-bold text-sm uppercase tracking-widest mb-4 block">Segurança Inabalável</span>
                    <h2 class="text-4xl md:text-5xl font-black font-display text-slate-900 mb-8 leading-tight">Infraestrutura robusta para sua paz de espírito</h2>
                    
                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div class="w-12 h-12 flex-shrink-0 bg-slate-50 rounded-2xl flex items-center justify-center text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-slate-900 mb-2">Criptografia de Ponta a Ponta</h4>
                                <p class="text-slate-500 leading-relaxed">Todos os dados sensíveis são criptografados com os mais altos padrões de segurança do mercado financeiro.</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="w-12 h-12 flex-shrink-0 bg-slate-50 rounded-2xl flex items-center justify-center text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-slate-900 mb-2">Monitoramento 24/7</h4>
                                <p class="text-slate-500 leading-relaxed">Nossa equipe de segurança e sistemas de IA monitoram transações em tempo real para prevenir fraudes.</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="w-12 h-12 flex-shrink-0 bg-slate-50 rounded-2xl flex items-center justify-center text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-slate-900 mb-2">Alta Disponibilidade</h4>
                                <p class="text-slate-500 leading-relaxed">Infraestrutura em nuvem com 99.9% de uptime garantido por contrato (SLA).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="taxas" class="py-24 bg-slate-50 reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold font-display text-slate-900 mb-4">{{ $landingSettings['pricing_title'] ?? 'Taxas Transparentes' }}</h2>
                <p class="text-lg text-slate-600">{{ $landingSettings['pricing_subtitle'] ?? 'Sem mensalidades ou letras miúdas. Você só paga quando vende.' }}</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Plan Card 1 -->
                <div class="modern-card rounded-3xl p-8 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-1 bg-blue-500 group-hover:h-2 transition-all"></div>
                    <h3 class="text-lg font-semibold text-slate-500 mb-2 uppercase tracking-wide">Recebimento</h3>
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-4xl font-bold text-slate-900">{{ number_format($cashinPercentual, 2, ',', '.') }}%</span>
                        <span class="text-slate-500">+ R$ {{ number_format($cashinFixo, 2, ',', '.') }}</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center text-slate-600">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Pix e Cartão de Crédito
                        </li>
                        <li class="flex items-center text-slate-600">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Sem taxa de adesão
                        </li>
                    </ul>
                    <a href="{{ route('auth.register') }}" class="block w-full py-3 px-4 bg-white border-2 border-slate-200 text-slate-700 font-semibold rounded-xl text-center hover:border-blue-500 hover:text-blue-600 transition-all">
                        Começar Agora
                    </a>
                </div>

                <!-- Plan Card 2 -->
                <div class="modern-card rounded-3xl p-8 relative overflow-hidden group border-blue-200 shadow-blue-100">
                    <div class="absolute top-0 left-0 w-full h-1 bg-cyan-500 group-hover:h-2 transition-all"></div>
                    <h3 class="text-lg font-semibold text-slate-500 mb-2 uppercase tracking-wide">Saque</h3>
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-4xl font-bold text-slate-900">R$ {{ number_format($cashoutFixo, 2, ',', '.') }}</span>
                        <span class="text-slate-500">fixo</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center text-slate-600">
                            <svg class="w-5 h-5 text-cyan-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Disponível 24/7 via Pix
                        </li>
                        <li class="flex items-center text-slate-600">
                            <svg class="w-5 h-5 text-cyan-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Processamento Automático
                        </li>
                    </ul>
                    <a href="{{ route('auth.register') }}" class="block w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:shadow-lg hover:shadow-cyan-500/20 text-white font-semibold rounded-xl text-center transition-all">
                        Criar Conta Grátis
                    </a>
                </div>
            </div>
            
            @if(!empty($landingSettings['pricing_note']))
            <div class="text-center mt-8">
                <p class="text-sm text-slate-500">{{ $landingSettings['pricing_note'] }}</p>
            </div>
            @endif

            <!-- Calculadora de Taxas -->
            <div class="mt-24 max-w-2xl mx-auto p-10 bg-white rounded-[3rem] border border-slate-100 shadow-2xl reveal" x-data="{ 
                amount: 100, 
                percent: {{ $cashinPercentual }}, 
                fixed: {{ $cashinFixo }},
                get totalFee() { return (this.amount * (this.percent / 100)) + this.fixed },
                get netAmount() { return this.amount - this.totalFee }
            }">
                <div class="text-center mb-10">
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Simule seus recebimentos</h3>
                    <p class="text-slate-500 font-bold">Veja quanto você recebe por cada venda</p>
                </div>

                <div class="space-y-8">
                    <div>
                        <label class="block text-sm font-black text-slate-700 uppercase tracking-widest mb-4">Valor da Venda (R$)</label>
                        <input type="number" x-model="amount" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-2xl font-black text-slate-900 focus:border-blue-500 focus:outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Você paga</p>
                            <p class="text-2xl font-black text-red-500">R$ <span x-text="totalFee.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></p>
                        </div>
                        <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100">
                            <p class="text-xs font-black text-blue-400 uppercase tracking-widest mb-2">Você recebe</p>
                            <p class="text-2xl font-black text-blue-600">R$ <span x-text="netAmount.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Whitelabel Section -->
    <section id="whitelabel" class="py-24 bg-white overflow-hidden reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1 relative">
                    @php
                        $displayImage = null;
                        if (($landingSettings['whitelabel_use_hero_image'] ?? '0') == '1' && !empty($landingSettings['hero_image'])) {
                            $displayImage = asset($landingSettings['hero_image']);
                        } elseif (!empty($landingSettings['whitelabel_image'])) {
                            $displayImage = asset($landingSettings['whitelabel_image']);
                        }
                    @endphp

                    @if($displayImage)
                        <div class="absolute inset-0 bg-blue-600 rounded-[3rem] rotate-3 opacity-5"></div>
                        <img src="{{ $displayImage }}" alt="Fintech" class="relative rounded-[3rem] shadow-2xl border border-slate-100 hover:scale-105 transition-transform duration-700 w-full object-cover">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-tr from-blue-100 to-cyan-100 rounded-3xl transform -rotate-3 blur-lg opacity-60"></div>
                        <div class="relative modern-card rounded-2xl p-8 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                            <!-- Mockup Interface -->
                            <div class="space-y-6">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                                            <span class="text-white font-bold">L</span>
                                        </div>
                                        <div>
                                            <div class="h-2 w-24 bg-slate-200 rounded mb-1"></div>
                                            <div class="h-2 w-16 bg-slate-100 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="h-24 bg-slate-50 rounded-xl border border-slate-100"></div>
                                    <div class="h-24 bg-slate-50 rounded-xl border border-slate-100"></div>
                                    <div class="h-24 bg-slate-50 rounded-xl border border-slate-100"></div>
                                </div>
                                <div class="h-40 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center">
                                    <p class="text-slate-400 text-sm font-medium">Sua Marca Aqui</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="order-1 lg:order-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 mb-6">
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wide">Plataforma Whitelabel</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold font-display text-slate-900 mb-6">{{ $landingSettings['whitelabel_title'] ?? 'Sua Fintech, Sua Marca' }}</h2>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        {{ $landingSettings['whitelabel_text'] ?? 'Tenha sua própria plataforma de pagamentos sem precisar desenvolver uma linha de código. Personalize cores, logo, domínio e taxas. Foque no seu negócio enquanto cuidamos da infraestrutura.' }}
                    </p>
                    
                    <ul class="space-y-4">
                        @for($i = 1; $i <= 4; $i++)
                            @if(!empty($landingSettings['whitelabel_item'.$i.'_title']))
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mt-1 mr-4 border border-blue-200">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-800">{{ $landingSettings['whitelabel_item'.$i.'_title'] }}</h4>
                                    <p class="text-sm text-slate-500 mt-1">{{ $landingSettings['whitelabel_item'.$i.'_text'] ?? '' }}</p>
                                </div>
                            </li>
                            @endif
                        @endfor
                    </ul>
                    
                    <div class="mt-10">
                        <a href="{{ route('auth.register') }}" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800 transition-colors">
                            Começar meu Banco Digital
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-24 bg-slate-50 reveal">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold font-display text-slate-900 mb-4">{{ $landingSettings['faq_title'] ?? 'Perguntas Frequentes' }}</h2>
                <p class="text-lg text-slate-600">{{ $landingSettings['faq_subtitle'] ?? 'Tire suas dúvidas sobre nossa plataforma.' }}</p>
            </div>
            
            <div class="space-y-4">
                @for($i = 1; $i <= 6; $i++)
                    @if(!empty($landingSettings['faq'.$i.'_question']) && !empty($landingSettings['faq'.$i.'_answer']))
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
                        <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-slate-50 transition-colors focus:outline-none" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180')">
                            <span class="font-semibold text-slate-800">{{ $landingSettings['faq'.$i.'_question'] }}</span>
                            <svg class="w-5 h-5 text-slate-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="hidden px-6 py-4 text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                            {{ $landingSettings['faq'.$i.'_answer'] }}
                        </div>
                    </div>
                    @endif
                @endfor
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xKSIvPjwvc3ZnPg==')] opacity-20"></div>
        
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-8 tracking-tight">{{ $landingSettings['cta_title'] ?? 'Pronto para revolucionar seus pagamentos?' }}</h2>
            <p class="text-xl text-blue-100 mb-12 max-w-2xl mx-auto">{{ $landingSettings['cta_text'] ?? 'Junte-se a milhares de empreendedores que já utilizam nossa tecnologia para vender mais.' }}</p>
            <div class="flex flex-col sm:flex-row gap-5 justify-center">
                <a href="{{ route('auth.register') }}" class="px-10 py-5 bg-white text-blue-900 font-bold rounded-xl shadow-xl hover:bg-gray-50 hover:scale-105 transition-all duration-300">
                    Criar Conta Grátis
                </a>
                <a href="#solucoes" class="px-10 py-5 bg-transparent border-2 border-white/20 text-white font-bold rounded-xl hover:bg-white/10 transition-all">
                    Falar com Consultor
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-2 md:col-span-1">
                    <!-- Logo no Footer -->
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $systemName }}" class="h-10 w-auto object-contain mb-6">
                    @else
                        <span class="text-2xl font-bold text-slate-900 font-display mb-6 block">{{ $systemName }}</span>
                    @endif
                    <p class="text-slate-500 text-sm leading-relaxed">Tecnologia financeira de ponta para impulsionar a nova economia digital com segurança e eficiência.</p>
                </div>
                
                <div>
                    <h4 class="font-bold text-slate-900 mb-6">Produto</h4>
                    <ul class="space-y-4 text-sm text-slate-500">
                        <li><a href="#solucoes" class="hover:text-blue-600 transition-colors">Soluções</a></li>
                        <li><a href="#taxas" class="hover:text-blue-600 transition-colors">Taxas</a></li>
                        <li><a href="#api" class="hover:text-blue-600 transition-colors">API</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-6">Legal</h4>
                    <ul class="space-y-4 text-sm text-slate-500">
                        <li><a href="{{ route('static.termos') }}" class="hover:text-blue-600 transition-colors">Termos de Uso</a></li>
                        <li><a href="{{ route('static.privacidade') }}" class="hover:text-blue-600 transition-colors">Privacidade</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-6">Contato</h4>
                    <ul class="space-y-4 text-sm text-slate-500">
                        <li><a href="{{ route('dashboard.support.index') }}" class="hover:text-blue-600 transition-colors">Suporte</a></li>
                        @if(!empty($landingSettings['whatsapp_number']))
                        <li><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingSettings['whatsapp_number']) }}" class="hover:text-blue-600 transition-colors">WhatsApp</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-slate-500">
                    {{ $landingSettings['footer_text'] ?? '© ' . date('Y') . ' ' . $systemName . '. Todos os direitos reservados.' }}
                </div>
                <div class="flex space-x-6">
                    <!-- Social Icons (Placeholder) -->
                    <a href="#" class="text-slate-400 hover:text-blue-600 transition-colors">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 014.18 3.388c.636-.247 1.363-.416 2.427-.465C7.902 2.013 8.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        window.addEventListener('load', () => {
            document.getElementById('page-loader').style.opacity = '0';
            setTimeout(() => {
                document.getElementById('page-loader').style.display = 'none';
            }, 500);
        });

        // Mobile Menu Logic
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const closeMobileMenuBtn = document.getElementById('close-mobile-menu');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        function toggleMenu() {
            mobileMenu.classList.toggle('translate-x-full');
            if (!mobileMenu.classList.contains('translate-x-full')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', toggleMenu);
        if (closeMobileMenuBtn) closeMobileMenuBtn.addEventListener('click', toggleMenu);
        
        mobileLinks.forEach(link => {
            link.addEventListener('click', toggleMenu);
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbarBg = document.getElementById('navbar-bg');
            if (window.scrollY > 20) {
                navbarBg.classList.remove('opacity-0');
            } else {
                navbarBg.classList.add('opacity-0');
            }
        });

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Reveal on Scroll Observer
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
    </script>
</body>
</html>
