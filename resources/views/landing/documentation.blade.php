<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação da API - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Scrollbar Moderna */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.5); border-radius: 10px; transition: background 0.3s ease; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.8); }
        .custom-scrollbar { scrollbar-width: thin; scrollbar-color: rgba(148, 163, 184, 0.5) transparent; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    @php
        use App\Helpers\LogoHelper;
        $baseUrl = config('app.url') . '/api/v1';
        $systemName = LogoHelper::getSystemName();
        $logoUrl = LogoHelper::getLogoUrl();
    @endphp

    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    @if(!empty($landingSettings['logo']))
                        <img src="{{ $landingSettings['logo'] }}" alt="Logo" class="h-8 w-auto">
                    @elseif($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $systemName }}" class="h-8 w-auto">
                    @else
                        <a href="{{ route('landing.index') }}" class="text-xl font-bold text-slate-900">{{ $systemName }}</a>
                    @endif
                    <span class="ml-4 px-2 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full border border-blue-100">API Docs v1.0</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('landing.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-900">Voltar ao Site</a>
                    <a href="{{ route('auth.register') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Criar Conta</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="space-y-4 sm:space-y-6 px-3 sm:px-0" x-data="{ activeSection: 'intro' }">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Documentação da API</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Integre o {{ $systemName }} ao seu sistema de forma simples e rápida</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Sidebar de Navegação -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-200 sticky top-24">
                        <h3 class="text-sm font-semibold text-slate-800 mb-3">Navegação</h3>
                        <nav class="space-y-1 custom-scrollbar max-h-[calc(100vh-200px)] overflow-y-auto">
                            <a @click="activeSection = 'intro'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'intro' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">Introdução</a>
                            <a @click="activeSection = 'credentials'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'credentials' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">Credenciais</a>
                            <a @click="activeSection = 'pix'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'pix' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">Integração PIX</a>
                            <a @click="activeSection = 'cashout'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'cashout' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">Cashout (Saques)</a>
                            <a @click="activeSection = 'consult'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'consult' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">Consultar Transações</a>
                            <a @click="activeSection = 'webhooks'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'webhooks' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">Webhooks (Notificações)</a>
                            <div class="pt-3 border-t border-slate-100 mt-3">
                                <p class="px-3 text-[10px] font-semibold text-slate-400 uppercase mb-2">Exemplos</p>
                                <a @click="activeSection = 'php'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'php' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">PHP</a>
                                <a @click="activeSection = 'python'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'python' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">Python</a>
                                <a @click="activeSection = 'javascript'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'javascript' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">JavaScript</a>
                                <a @click="activeSection = 'curl'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'curl' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer text-xs sm:text-sm">cURL</a>
                            </div>
                            <a @click="activeSection = 'errors'; window.scrollTo({top: 0, behavior: 'smooth'})" :class="activeSection === 'errors' ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-50'" class="block px-3 py-2 rounded-lg transition-colors cursor-pointer mt-3 border-t border-slate-100 pt-3 text-xs sm:text-sm">Códigos de Erro</a>
                        </nav>
                    </div>
                </div>

                <!-- Conteúdo Principal -->
                <div class="lg:col-span-3 space-y-4 sm:space-y-6">
                    <!-- Base URL -->
                    <div class="bg-white border border-blue-200 rounded-xl p-4 sm:p-5 shadow-sm">
                        <p class="text-xs sm:text-sm text-slate-600 flex items-center flex-wrap gap-2">
                            <span class="font-semibold text-slate-800">Base URL:</span> 
                            <code class="bg-slate-100 px-2 py-1 rounded text-blue-600 border border-slate-200 font-mono text-xs">{{ $baseUrl }}</code>
                        </p>
                    </div>

                    <!-- Seção: Introdução -->
                    <div x-show="activeSection === 'intro'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Introdução</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5 leading-relaxed">
                                A API do {{ $systemName }} permite que você integre pagamentos PIX e Cartão de Crédito ao seu sistema de forma simples e segura.
                            </p>
                            <div class="space-y-3">
                                <div class="bg-slate-50 rounded-xl p-4 sm:p-5 border border-slate-100">
                                    <h3 class="text-sm font-semibold text-slate-800 mb-3">Características</h3>
                                    <ul class="space-y-2 text-slate-600 text-xs sm:text-sm">
                                        <li class="flex items-center gap-2">
                                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <span>API RESTful completa</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <span>Autenticação via Token Bearer</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <span>Webhooks em tempo real</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <span>Respostas em JSON</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Credenciais -->
                    <div x-show="activeSection === 'credentials'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Obter Credenciais de API</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5">
                                Para usar a API, você precisa gerar uma chave de API no painel do sistema.
                            </p>
                            
                            <div class="space-y-4">
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-5">
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Passo 1: Acesse a página de Chaves de API</h3>
                                    <p class="text-slate-600 text-xs sm:text-sm mb-4">
                                        No painel do sistema, acesse a seção <strong>"Chave API"</strong> no menu lateral.
                                    </p>
                                    <a href="{{ route('dashboard.api.index') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg transition-all shadow-sm hover:shadow-md text-xs sm:text-sm">
                                        Ir para Chaves de API →
                                    </a>
                                </div>

                                <div class="bg-slate-50 rounded-xl p-4 sm:p-5 border border-slate-100">
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Passo 2: Gere uma nova chave</h3>
                                    <p class="text-slate-600 text-xs sm:text-sm mb-3">
                                        Clique em "Gerar Nova Chave" e dê um nome descritivo (ex: "Site Principal", "App Mobile").
                                    </p>
                                    <p class="text-amber-600 text-xs font-medium bg-amber-50 px-3 py-2 rounded-lg inline-block border border-amber-100">
                                        ⚠️ Importante: Copie e guarde o token imediatamente, pois ele só será exibido uma vez!
                                    </p>
                                </div>

                                <div class="bg-slate-50 rounded-xl p-4 sm:p-5 border border-slate-100">
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Passo 3: Configure no seu sistema</h3>
                                    <p class="text-slate-600 text-xs sm:text-sm mb-3">
                                        Adicione o token gerado nas configurações do seu site/aplicação. O token terá o formato:
                                    </p>
                                    <div class="bg-slate-100 border border-slate-200 rounded-lg p-3 mt-2">
                                        <code class="text-xs sm:text-sm text-blue-600 font-mono break-all">nxp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Integração PIX -->
                    <div x-show="activeSection === 'pix'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Integração PIX</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5">
                                Crie pagamentos PIX e receba notificações em tempo real quando o pagamento for confirmado.
                            </p>

                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Endpoint</h3>
                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                                        <code class="text-xs sm:text-sm text-blue-600 font-mono break-all">POST {{ $baseUrl }}/payments/pix</code>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Parâmetros</h3>
                                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                                        <table class="w-full text-xs sm:text-sm">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Campo</th>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Tipo</th>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Obrigatório</th>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Descrição</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 bg-white">
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">amount</td>
                                                    <td class="px-4 py-3 text-slate-500">float</td>
                                                    <td class="px-4 py-3 text-slate-500">Sim</td>
                                                    <td class="px-4 py-3 text-slate-500">Valor do pagamento (mínimo: R$ 0,01)</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">payer_name</td>
                                                    <td class="px-4 py-3 text-slate-500">string</td>
                                                    <td class="px-4 py-3 text-slate-500">Sim</td>
                                                    <td class="px-4 py-3 text-slate-500">Nome completo do pagador</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">payer_email</td>
                                                    <td class="px-4 py-3 text-slate-500">string</td>
                                                    <td class="px-4 py-3 text-slate-500">Sim</td>
                                                    <td class="px-4 py-3 text-slate-500">Email do pagador</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">payer_cpf</td>
                                                    <td class="px-4 py-3 text-slate-500">string</td>
                                                    <td class="px-4 py-3 text-slate-500">Sim</td>
                                                    <td class="px-4 py-3 text-slate-500">CPF do pagador (apenas números)</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">description</td>
                                                    <td class="px-4 py-3 text-slate-500">string</td>
                                                    <td class="px-4 py-3 text-slate-500">Não</td>
                                                    <td class="px-4 py-3 text-slate-500">Descrição do pagamento</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Resposta de Sucesso (201)</h3>
                                    <div class="bg-slate-900 rounded-xl p-4 overflow-hidden">
                                        <pre class="text-xs text-slate-300 overflow-x-auto font-mono"><code>{
  "success": true,
  "data": {
    "transaction_uuid": "550e8400-e29b-41d4-a716-446655440000",
    "amount": 100.00,
    "fee": 4.00,
    "amount_net": 96.00,
    "status": "pending",
    "qr_code": "00020126580014BR.GOV.BCB.PIX...",
    "pix_code": "00020126580014BR.GOV.BCB.PIX...",
    "pix_key": "00020126580014BR.GOV.BCB.PIX...",
    "expires_at": "2024-11-26T12:30:00Z",
    "expires_in_seconds": 300
  }
}</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Cashout (Saques) -->
                    <div x-show="activeSection === 'cashout'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Cashout - Saques via PIX</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5">
                                Crie saques via PIX para transferir saldo da conta do usuário para uma chave PIX.
                            </p>

                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                                <p class="text-amber-800 text-xs sm:text-sm">
                                    <strong class="block mb-2">Importante: Para saques automáticos funcionarem, você precisa:</strong>
                                    <span class="block ml-2">1. Configurar o modo de saque como "Automático" ao criar a credencial de API</span>
                                    <span class="block ml-2">2. Adicionar o IP do seu servidor nas configurações da API</span>
                                    <span class="block ml-2">3. O usuário precisa estar aprovado (KYC) e com saques desbloqueados</span>
                                </p>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Endpoint</h3>
                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                                        <code class="text-xs sm:text-sm text-blue-600 font-mono break-all">POST {{ $baseUrl }}/cashout/pix</code>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Parâmetros</h3>
                                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                                        <table class="w-full text-xs sm:text-sm">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Campo</th>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Tipo</th>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Obrigatório</th>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Descrição</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 bg-white">
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">amount</td>
                                                    <td class="px-4 py-3 text-slate-500">float</td>
                                                    <td class="px-4 py-3 text-slate-500">Sim</td>
                                                    <td class="px-4 py-3 text-slate-500">Valor líquido desejado (mínimo: R$ 10,00). O sistema calcula automaticamente o valor bruto incluindo taxas.</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">pix_key</td>
                                                    <td class="px-4 py-3 text-slate-500">string</td>
                                                    <td class="px-4 py-3 text-slate-500">Sim</td>
                                                    <td class="px-4 py-3 text-slate-500">Chave PIX de destino (CPF, Email, Telefone ou Chave Aleatória)</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Resposta de Sucesso</h3>
                                    <div class="bg-slate-900 rounded-xl p-4 overflow-hidden">
                                        <pre class="text-xs text-slate-300 overflow-x-auto font-mono"><code>{
  "success": true,
  "message": "Saque criado e processado automaticamente.",
  "withdrawal": {
    "id": 123,
    "amount": 95.00,
    "amount_gross": 100.00,
    "fee": 5.00,
    "status": "processing",
    "pix_key": "12345678900"
  }
}</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Consultar Transações -->
                    <div x-show="activeSection === 'consult'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Consultar Transações</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5">
                                Liste e filtre as transações da sua conta ou busque uma transação específica.
                            </p>

                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Listar Transações</h3>
                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                                        <code class="text-xs sm:text-sm text-blue-600 font-mono break-all">GET {{ $baseUrl }}/transactions</code>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Parâmetros (Query String)</h3>
                                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                                        <table class="w-full text-xs sm:text-sm">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Campo</th>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Tipo</th>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Descrição</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 bg-white">
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">status</td>
                                                    <td class="px-4 py-3 text-slate-500">string</td>
                                                    <td class="px-4 py-3 text-slate-500">Filtrar por status (paid, pending, failed, etc)</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">page</td>
                                                    <td class="px-4 py-3 text-slate-500">integer</td>
                                                    <td class="px-4 py-3 text-slate-500">Número da página</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">limit</td>
                                                    <td class="px-4 py-3 text-slate-500">integer</td>
                                                    <td class="px-4 py-3 text-slate-500">Itens por página (padrão: 15)</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Webhooks -->
                    <div x-show="activeSection === 'webhooks'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Webhooks (Notificações)</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5">
                                Receba notificações automáticas em seu sistema sempre que o status de uma transação mudar.
                            </p>

                            <div class="space-y-6">
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-5">
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Configuração</h3>
                                    <p class="text-slate-600 text-xs sm:text-sm">
                                        Configure a URL de callback (Webhook) nas configurações da sua conta ou envie o parâmetro <code>callback_url</code> na criação da transação.
                                    </p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Eventos Disparados</h3>
                                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                                        <table class="w-full text-xs sm:text-sm">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Evento</th>
                                                    <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Descrição</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 bg-white">
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">payment.created</td>
                                                    <td class="px-4 py-3 text-slate-500">Disparado quando um pagamento é criado.</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">payment.paid</td>
                                                    <td class="px-4 py-3 text-slate-500">Disparado quando o pagamento é confirmado (pago).</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-4 py-3 font-mono text-slate-700">payment.failed</td>
                                                    <td class="px-4 py-3 text-slate-500">Disparado quando o pagamento falha ou expira.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Exemplo de Payload (JSON)</h3>
                                    <p class="text-slate-600 text-xs sm:text-sm mb-3">O sistema enviará uma requisição <strong>POST</strong> para sua URL com o seguinte corpo:</p>
                                    <div class="bg-slate-900 rounded-xl p-4 overflow-hidden">
                                        <pre class="text-xs text-slate-300 overflow-x-auto font-mono"><code>{
  "event": "payment.paid",
  "created_at": "2024-01-20T10:30:00Z",
  "data": {
    "transaction_uuid": "550e8400-e29b-41d4-a716-446655440000",
    "external_reference": "PEDIDO_123",
    "status": "paid",
    "amount": 150.00,
    "fee": 4.50,
    "amount_net": 145.50,
    "payer": {
      "name": "João Silva",
      "document": "123.456.789-00",
      "email": "joao@email.com"
    },
    "payment_method": "pix",
    "paid_at": "2024-01-20T10:35:00Z"
  }
}</code></pre>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 mb-2">Resposta Esperada</h3>
                                    <p class="text-slate-600 text-xs sm:text-sm">
                                        Seu servidor deve responder com status HTTP <strong>200 OK</strong> para confirmar o recebimento.
                                        Caso contrário, o sistema tentará reenviar a notificação.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exemplos de Código -->
                    
                    <!-- PHP -->
                    <div x-show="activeSection === 'php'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Exemplo em PHP</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5">Exemplo de criação de pagamento PIX usando Guzzle.</p>
                            
                            <div class="bg-slate-900 rounded-xl p-4 relative group border border-slate-800">
                                <button onclick="copyToClipboard(this)" class="absolute top-4 right-4 text-xs text-blue-400 hover:text-blue-400 mb-2 opacity-0 group-hover:opacity-100 transition-opacity font-medium">Copiar</button>
                                <pre class="text-xs text-slate-300 overflow-x-auto font-mono"><code>&lt;?php

$client = new \GuzzleHttp\Client();

$response = $client->post('{{ $baseUrl }}/payments/pix', [
    'headers' => [
        'Authorization' => 'Bearer SEU_TOKEN',
        'X-Client-ID' => 'SEU_CLIENT_ID',
        'Accept' => 'application/json',
    ],
    'json' => [
        'amount' => 100.00,
        'payer_name' => 'João Silva',
        'payer_email' => 'joao@email.com',
        'payer_cpf' => '12345678900',
        'description' => 'Pagamento de Teste'
    ]
]);

$body = json_decode($response->getBody(), true);
print_r($body);</code></pre>
                            </div>
                        </div>
                    </div>

                    <!-- Python -->
                    <div x-show="activeSection === 'python'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Exemplo em Python</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5">Exemplo usando a biblioteca requests.</p>
                            
                            <div class="bg-slate-900 rounded-xl p-4 relative group border border-slate-800">
                                <button onclick="copyToClipboard(this)" class="absolute top-4 right-4 text-xs text-blue-400 hover:text-blue-400 mb-2 opacity-0 group-hover:opacity-100 transition-opacity font-medium">Copiar</button>
                                <pre class="text-xs text-slate-300 overflow-x-auto font-mono"><code>import requests

url = "{{ $baseUrl }}/payments/pix"

payload = {
    "amount": 100.00,
    "payer_name": "João Silva",
    "payer_email": "joao@email.com",
    "payer_cpf": "12345678900",
    "description": "Pagamento de Teste"
}

headers = {
    "Authorization": "Bearer SEU_TOKEN",
    "X-Client-ID": "SEU_CLIENT_ID",
    "Content-Type": "application/json"
}

response = requests.post(url, json=payload, headers=headers)

print(response.json())</code></pre>
                            </div>
                        </div>
                    </div>

                    <!-- JavaScript -->
                    <div x-show="activeSection === 'javascript'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Exemplo em JavaScript</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5">Exemplo usando fetch API.</p>
                            
                            <div class="bg-slate-900 rounded-xl p-4 relative group border border-slate-800">
                                <button onclick="copyToClipboard(this)" class="absolute top-4 right-4 text-xs text-blue-400 hover:text-blue-400 mb-2 opacity-0 group-hover:opacity-100 transition-opacity font-medium">Copiar</button>
                                <pre class="text-xs text-slate-300 overflow-x-auto font-mono"><code>const url = "{{ $baseUrl }}/payments/pix";

const payload = {
    amount: 100.00,
    payer_name: "João Silva",
    payer_email: "joao@email.com",
    payer_cpf: "12345678900",
    description: "Pagamento de Teste"
};

const headers = {
    "Authorization": "Bearer SEU_TOKEN",
    "X-Client-ID": "SEU_CLIENT_ID",
    "Content-Type": "application/json"
};

fetch(url, {
    method: "POST",
    headers: headers,
    body: JSON.stringify(payload)
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error("Error:", error));</code></pre>
                            </div>
                        </div>
                    </div>

                    <!-- cURL -->
                    <div x-show="activeSection === 'curl'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Exemplo cURL</h2>
                            
                            <div class="bg-slate-900 rounded-xl p-4 relative group border border-slate-800">
                                <button onclick="copyToClipboard(this)" class="absolute top-4 right-4 text-xs text-blue-400 hover:text-blue-400 mb-2 opacity-0 group-hover:opacity-100 transition-opacity font-medium">Copiar</button>
                                <pre class="text-xs text-slate-300 overflow-x-auto font-mono"><code>curl -X POST "{{ $baseUrl }}/payments/pix" \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "X-Client-ID: SEU_CLIENT_ID" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100.00,
    "payer_name": "João Silva",
    "payer_email": "joao@email.com",
    "payer_cpf": "12345678900",
    "description": "Pagamento de Teste"
  }'</code></pre>
                            </div>
                        </div>
                    </div>

                    <!-- Códigos de Erro -->
                    <div x-show="activeSection === 'errors'" x-transition class="space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-3">Códigos de Erro</h2>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5">
                                Lista de possíveis códigos de status HTTP retornados pela API.
                            </p>

                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="w-full text-xs sm:text-sm">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Código</th>
                                            <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Significado</th>
                                            <th class="px-4 py-2 text-left text-slate-500 font-semibold uppercase tracking-wider text-xs">Descrição</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        <tr>
                                            <td class="px-4 py-3 text-blue-600 font-bold">200</td>
                                            <td class="px-4 py-3 text-slate-800 font-medium">OK</td>
                                            <td class="px-4 py-3 text-slate-600">Requisição processada com sucesso.</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 text-blue-600 font-bold">201</td>
                                            <td class="px-4 py-3 text-slate-800 font-medium">Created</td>
                                            <td class="px-4 py-3 text-slate-600">Recurso criado com sucesso (ex: novo pagamento).</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 text-amber-500 font-bold">400</td>
                                            <td class="px-4 py-3 text-slate-800 font-medium">Bad Request</td>
                                            <td class="px-4 py-3 text-slate-600">Dados inválidos enviados na requisição. Verifique os campos.</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 text-amber-500 font-bold">401</td>
                                            <td class="px-4 py-3 text-slate-800 font-medium">Unauthorized</td>
                                            <td class="px-4 py-3 text-slate-600">Token inválido ou não fornecido.</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 text-amber-500 font-bold">403</td>
                                            <td class="px-4 py-3 text-slate-800 font-medium">Forbidden</td>
                                            <td class="px-4 py-3 text-slate-600">Acesso negado ao recurso solicitado.</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 text-amber-500 font-bold">404</td>
                                            <td class="px-4 py-3 text-slate-800 font-medium">Not Found</td>
                                            <td class="px-4 py-3 text-slate-600">Recurso não encontrado (ex: transação inexistente).</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 text-red-500 font-bold">422</td>
                                            <td class="px-4 py-3 text-slate-800 font-medium">Unprocessable Entity</td>
                                            <td class="px-4 py-3 text-slate-600">Erro de validação (ex: email inválido, saldo insuficiente).</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 text-red-500 font-bold">500</td>
                                            <td class="px-4 py-3 text-slate-800 font-medium">Internal Server Error</td>
                                            <td class="px-4 py-3 text-slate-600">Erro interno do servidor. Tente novamente mais tarde.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <footer class="bg-white border-t border-slate-200 py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-slate-500">© {{ date('Y') }} {{ $systemName }}. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
    function copyToClipboard(button) {
        const codeBlock = button.parentElement.querySelector('code');
        const text = codeBlock.innerText;
        
        navigator.clipboard.writeText(text).then(() => {
            const originalText = button.innerText;
            button.innerText = 'Copiado!';
            button.classList.add('text-blue-400');
            button.classList.remove('text-blue-400');
            
            setTimeout(() => {
                button.innerText = originalText;
                button.classList.remove('text-blue-400');
                button.classList.add('text-blue-400');
            }, 2000);
        });
    }
    </script>
</body>
</html>