<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        use App\Helpers\ThemeHelper;
        use App\Helpers\LogoHelper;
        $themeColors = ThemeHelper::getThemeColors();
        $systemName = LogoHelper::getSystemName();
        $logoUrl = LogoHelper::getLogoUrl();
        $faviconUrl = LogoHelper::getFaviconUrl();
    @endphp
    <title>{{ $page->title }} - {{ $systemName }}</title>
    @if($faviconUrl)
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        :root {
            --primary-color: {{ $themeColors['primary'] ?? '#00B2FF' }};
            --secondary-color: {{ $themeColors['secondary'] ?? '#00D9AC' }};
        }
        
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
        .prose {
            color: #e5e7eb;
        }
        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            color: #ffffff;
        }
        .prose p {
            color: #d1d5db;
            margin-bottom: 1rem;
        }
        .prose strong {
            color: #ffffff;
        }
        .prose a {
            color: var(--primary-color);
        }
        .prose a:hover {
            color: var(--secondary-color);
        }
        .prose ul, .prose ol {
            color: #d1d5db;
        }
        .prose li {
            margin-bottom: 0.5rem;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #151A23;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #1F2937;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #374151;
        }
        [x-cloak] { 
            display: none !important; 
        }
    </style>
</head>
<body class="bg-[#0B0E14] text-white min-h-screen" x-data="{ mobileMenuOpen: false }">
    <!-- Header -->
    <header class="bg-[#151A23]/95 backdrop-blur-lg border-b border-[#1F2937] sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center">
                    <a href="{{ route('landing.index') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $systemName }}" class="h-10 object-contain" width="160" height="40">
                        @else
                            <span class="text-2xl font-bold" style="color: {{ $themeColors['primary'] }};">{{ $systemName }}</span>
                        @endif
                    </a>
                </div>
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('landing.index') }}" class="text-gray-300 hover:text-white transition-colors">Início</a>
                    @php
                        $currentSlug = $page->slug;
                    @endphp
                    <a href="{{ route('static.termos') }}" class="transition-colors {{ $currentSlug === 'termos-uso' ? 'text-white font-medium' : 'text-gray-300 hover:text-white' }}" style="{{ $currentSlug === 'termos-uso' ? 'color: ' . $themeColors['primary'] . ';' : '' }}">Termos de Uso</a>
                    <a href="{{ route('static.privacidade') }}" class="transition-colors {{ $currentSlug === 'privacidade' ? 'text-white font-medium' : 'text-gray-300 hover:text-white' }}" style="{{ $currentSlug === 'privacidade' ? 'color: ' . $themeColors['primary'] . ';' : '' }}">Privacidade</a>
                    <a href="{{ route('static.pld') }}" class="transition-colors {{ $currentSlug === 'pld' ? 'text-white font-medium' : 'text-gray-300 hover:text-white' }}" style="{{ $currentSlug === 'pld' ? 'color: ' . $themeColors['primary'] . ';' : '' }}">PLD</a>
                    <a href="{{ route('static.manual-kyc') }}" class="transition-colors {{ $currentSlug === 'manual-kyc' ? 'text-white font-medium' : 'text-gray-300 hover:text-white' }}" style="{{ $currentSlug === 'manual-kyc' ? 'color: ' . $themeColors['primary'] . ';' : '' }}">Manual KYC</a>
                </nav>
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-300 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-1" x-cloak class="md:hidden pb-4 border-t border-[#1F2937] mt-4 pt-4">
                <nav class="flex flex-col space-y-3">
                    <a href="{{ route('landing.index') }}" class="text-gray-300 hover:text-white transition-colors py-2">Início</a>
                    @php
                        $currentSlug = $page->slug;
                    @endphp
                    <a href="{{ route('static.termos') }}" class="transition-colors py-2 {{ $currentSlug === 'termos-uso' ? 'text-white font-medium' : 'text-gray-300 hover:text-white' }}" style="{{ $currentSlug === 'termos-uso' ? 'color: ' . $themeColors['primary'] . ';' : '' }}">Termos de Uso</a>
                    <a href="{{ route('static.privacidade') }}" class="transition-colors py-2 {{ $currentSlug === 'privacidade' ? 'text-white font-medium' : 'text-gray-300 hover:text-white' }}" style="{{ $currentSlug === 'privacidade' ? 'color: ' . $themeColors['primary'] . ';' : '' }}">Privacidade</a>
                    <a href="{{ route('static.pld') }}" class="transition-colors py-2 {{ $currentSlug === 'pld' ? 'text-white font-medium' : 'text-gray-300 hover:text-white' }}" style="{{ $currentSlug === 'pld' ? 'color: ' . $themeColors['primary'] . ';' : '' }}">PLD</a>
                    <a href="{{ route('static.manual-kyc') }}" class="transition-colors py-2 {{ $currentSlug === 'manual-kyc' ? 'text-white font-medium' : 'text-gray-300 hover:text-white' }}" style="{{ $currentSlug === 'manual-kyc' ? 'color: ' . $themeColors['primary'] . ';' : '' }}">Manual KYC</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-[#151A23] rounded-3xl shadow-xl border border-[#1F2937] p-8 md:p-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white">{{ $page->title }}</h1>
            <div class="prose prose-lg max-w-none custom-scrollbar overflow-y-auto" style="max-height: calc(100vh - 400px);">
                {!! nl2br(e($page->content)) !!}
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#151A23] border-t border-[#1F2937] mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} {{ $systemName }}. Todos os direitos reservados.</p>
                <div class="mt-4 flex justify-center space-x-6">
                    <a href="{{ route('static.termos') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Termos de Uso</a>
                    <a href="{{ route('static.privacidade') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Privacidade</a>
                    <a href="{{ route('static.pld') }}" class="text-gray-400 hover:text-white transition-colors text-sm">PLD</a>
                    <a href="{{ route('static.manual-kyc') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Manual KYC</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>




