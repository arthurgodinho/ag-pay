<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        $systemName = \App\Helpers\LogoHelper::getSystemName();
    @endphp
    <title>@yield('title', $systemName) - Gateway de Pagamentos</title>
    @php
        use App\Helpers\LogoHelper;
        $faviconUrl = LogoHelper::getFaviconUrl();
    @endphp
    @if($faviconUrl)
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        
        [x-cloak] { display: none !important; }
        
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

        /* CORREÇÃO: Restringe SVGs para evitar que fiquem gigantes antes do CSS carregar */
        svg:not([width]):not([height]) {
            max-width: 100% !important;
            height: auto !important;
            display: block;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes glow {
            0% { box-shadow: 0 0 5px #0097c9, 0 0 10px #0097c9, 0 0 15px #0097c9; }
            100% { box-shadow: 0 0 10px #0097c9, 0 0 20px #0097c9, 0 0 30px #0097c9; }
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-glow {
            animation: glow 2s ease-in-out infinite alternate;
        }
        
        .bg-gradient-animated {
            background: linear-gradient(-45deg, #0f172a, #1e293b, #0f172a, #1e1b4b);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .glass-strong {
            background: rgba(21, 26, 35, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Scrollbar Customizada */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        /* Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
        }
        
        .grid-pattern {
            background-image: 
                linear-gradient(rgba(0, 151, 201, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 151, 201, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
        }
    </style>
    @php
        use App\Helpers\ThemeHelper;
        $themeColors = ThemeHelper::getThemeColors();
    @endphp
    <style>
        {!! ThemeHelper::generateThemeCSS() !!}
        
        .bg-gradient-animated {
            background: linear-gradient(-45deg, {{ $themeColors['background'] }}, {{ $themeColors['dashboard_bg'] }}, {{ $themeColors['background'] }}, {{ $themeColors['secondary'] }});
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        .grid-pattern {
            background-image: 
                linear-gradient(rgba(0, 151, 201, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 151, 201, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
        }
    </style>
    @stack('styles')
    <script>
        // Aplica cores do tema dinamicamente em todos os inputs
        document.addEventListener('DOMContentLoaded', function() {
            const primaryColor = '{{ $themeColors['primary'] }}';
            const inputs = document.querySelectorAll('input, textarea, select');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.borderColor = primaryColor;
                    this.style.boxShadow = `0 0 0 2px ${primaryColor}50`;
                });
                
                input.addEventListener('blur', function() {
                    this.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
</head>
<body class="antialiased" style="background-color: {{ $themeColors['background'] }}; color: {{ $themeColors['text'] }};" onload="document.body.classList.add('loaded')">
    @yield('content')
    @stack('scripts')
</body>
</html>

