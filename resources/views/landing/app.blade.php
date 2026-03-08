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
        $metaTitle = $landingSettings['meta_title'] ?? 'Baixe nosso APP - ' . $systemName;
    @endphp
    
    <title>{{ $metaTitle }}</title>
    
    @if($faviconUrl)
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    @endif
    
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.tailwindcss.com"></script>
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
        
        .bg-subtle-blue {
            background: radial-gradient(circle at top right, rgba(var(--primary-rgb), 0.05), transparent),
                        radial-gradient(circle at bottom left, rgba(var(--primary-rgb), 0.02), transparent);
        }

        .blob {
            position: absolute;
            filter: blur(100px);
            z-index: -1;
            opacity: 0.4;
            animation: float-blob 20s infinite alternate cubic-bezier(0.45, 0, 0.55, 1);
        }
        .blob-1 { background: var(--primary); width: 800px; height: 800px; border-radius: 50%; top: -10%; right: -5%; }
        
        @keyframes float-blob {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            100% { transform: translate(50px, 100px) rotate(180deg) scale(1.1); }
        }

        .animate-float-slow { animation: float-slow 6s infinite ease-in-out; }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

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
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 border-b border-transparent bg-white/70 backdrop-blur-xl shadow-sm" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <a href="{{ url('/') }}">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $systemName }}" class="h-10 w-auto object-contain transition-transform hover:scale-110 duration-300">
                        @else
                            <span class="text-2xl font-bold font-display tracking-tight text-slate-900">{{ $systemName }}</span>
                        @endif
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-10">
                    <a href="{{ url('/') }}#solucoes" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">Soluções</a>
                    <a href="{{ url('/') }}#taxas" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">Taxas</a>
                    <a href="{{ url('/') }}#api" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">API</a>
                    <a href="{{ route('landing.app') }}" class="text-sm font-bold text-blue-600">Baixe o APP</a>
                </div>

                <div class="flex items-center space-x-6">
                    <a href="{{ route('login') }}" class="hidden md:block text-sm font-bold text-slate-700 hover:text-blue-600 transition-colors">Entrar</a>
                    <a href="{{ route('auth.register') }}" class="px-6 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/25 hover:bg-blue-700 transition-all">
                        Criar Conta
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- App Content -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-40 overflow-hidden min-h-screen flex items-center bg-subtle-blue">
        <div class="blob blob-1"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div class="max-w-2xl reveal active">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 border border-blue-100 mb-8">
                        <span class="flex h-2 w-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-black text-blue-700 uppercase tracking-widest">Disponível para Android</span>
                    </span>
                    
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black font-display text-slate-900 leading-[1.1] mb-8">
                        {{ $landingSettings['app_title'] ?? 'Seu Banco na palma da sua mão. 📱' }}
                    </h1>
                    
                    <p class="text-xl text-slate-600 mb-12 leading-relaxed">
                        {{ $landingSettings['app_subtitle'] ?? 'Gerencie suas vendas, acompanhe seu saldo em tempo real e realize saques instantâneos onde quer que você esteja. O app do ' . $systemName . ' é completo, seguro e ultra-rápido.' }}
                    </p>
                    
                    <div class="flex flex-wrap gap-6 mb-16">
                        <!-- Google Play -->
                        <a href="{{ $landingSettings['app_playstore_url'] ?? '#' }}" target="_blank" class="flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-2xl hover:bg-black transition-all hover:-translate-y-1 shadow-xl">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M3,20.5V3.5C3,2.91,3.34,2.39,3.84,2.15L13.69,12L3.84,21.85C3.34,21.61,3,21.09,3,20.5Z M16.81,15.12L4.94,22.01C4.77,22.09,4.59,22.13,4.41,22.13C4.08,22.13,3.76,22.01,3.53,21.79L14.34,10.97L16.81,15.12Z M19.03,13.67L17.49,14.56L14.97,10.34L17.49,6.12L19.03,7.01C19.66,7.37,20,8.03,20,8.75V11.93C20,12.65,19.66,13.31,19.03,13.67Z M14.34,6.53L3.53,15.71C3.76,15.49,4.08,15.37,4.41,15.37C4.59,15.37,4.77,15.41,4.94,15.49L16.81,2.38L14.34,6.53Z"/></svg>
                            <div class="text-left">
                                <p class="text-[10px] uppercase font-bold opacity-60 leading-none">Download no</p>
                                <p class="text-lg font-bold leading-none">Google Play</p>
                            </div>
                        </a>
                    </div>

                    <!-- QR Code Mockup -->
                    <div class="flex items-center gap-6 p-6 bg-white rounded-3xl border border-slate-100 shadow-xl max-w-sm">
                        <div class="w-24 h-24 bg-slate-100 rounded-2xl flex items-center justify-center border-2 border-slate-50">
                            <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 17h.01M9 17h.01M12 17v1m-3-1v1m3-4h-2m2 0v3m0 0h.01m-2-3h.01m-4 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Aponte a câmera</p>
                            <p class="text-sm text-slate-500">Escaneie o QR Code para baixar agora mesmo.</p>
                        </div>
                    </div>
                </div>

                <!-- App Mockup Image -->
                <div class="relative lg:ml-auto reveal active">
                    <div class="absolute -inset-10 bg-blue-500/20 rounded-[3rem] blur-3xl opacity-50"></div>
                    <div class="relative animate-float-slow">
                        <!-- iPhone Mockup CSS -->
                        <div class="w-[300px] h-[600px] bg-slate-900 rounded-[3rem] p-3 shadow-2xl border-[8px] border-slate-800 mx-auto overflow-hidden relative">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-800 rounded-b-2xl z-20"></div>
                            <div class="bg-white h-full w-full rounded-[2.2rem] overflow-hidden relative">
                                <!-- App Header -->
                                <div class="p-6 pt-10 bg-blue-600 text-white">
                                    <p class="text-xs opacity-80 mb-1">Saldo Total</p>
                                    <p class="text-3xl font-black">R$ 12.450,00</p>
                                </div>
                                <!-- App Body -->
                                <div class="p-4 space-y-4">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg mb-2 flex items-center justify-center text-blue-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                            <p class="text-[10px] font-bold text-slate-400">Vender</p>
                                        </div>
                                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                            <div class="w-8 h-8 bg-green-100 rounded-lg mb-2 flex items-center justify-center text-green-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                            </div>
                                            <p class="text-[10px] font-bold text-slate-400">Sacar</p>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                        <p class="text-xs font-bold text-slate-400 mb-3">Últimas Transações</p>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <div class="flex gap-2 items-center">
                                                    <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 text-[10px] font-bold">PIX</div>
                                                    <span class="text-[10px] font-bold">Venda #1234</span>
                                                </div>
                                                <span class="text-[10px] font-bold text-green-600">+R$ 150,00</span>
                                            </div>
                                            <div class="flex justify-between items-center opacity-50">
                                                <div class="flex gap-2 items-center">
                                                    <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 text-[10px] font-bold">CC</div>
                                                    <span class="text-[10px] font-bold">Venda #1233</span>
                                                </div>
                                                <span class="text-[10px] font-bold text-green-600">+R$ 89,90</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Chart Mock -->
                                    <div class="h-24 w-full bg-slate-50 rounded-2xl border border-slate-100 flex items-end p-2 gap-1">
                                        <div class="flex-1 bg-blue-200 h-1/2 rounded-t-sm"></div>
                                        <div class="flex-1 bg-blue-400 h-3/4 rounded-t-sm"></div>
                                        <div class="flex-1 bg-blue-600 h-full rounded-t-sm"></div>
                                        <div class="flex-1 bg-blue-300 h-2/3 rounded-t-sm"></div>
                                        <div class="flex-1 bg-blue-500 h-5/6 rounded-t-sm"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-slate-500 text-sm">© {{ date('Y') }} {{ $systemName }}. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        window.addEventListener('load', () => {
            document.getElementById('page-loader').style.opacity = '0';
            setTimeout(() => {
                document.getElementById('page-loader').style.display = 'none';
            }, 500);
        });

        // Simple reveal on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('active');
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
</body>
</html>