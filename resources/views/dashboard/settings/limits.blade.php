<!-- Gerenciamento de Limites -->
<div class="space-y-6">
    @php
        $documentType = \App\Helpers\DocumentHelper::getDocumentType($user->cpf_cnpj ?? '');
        $isPJ = $documentType === 'cnpj';
    @endphp

    <!-- Card Principal de Informações da Conta -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="p-6 flex items-center gap-4">
            <!-- Ícone -->
            <div class="shrink-0">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 ring-4 ring-blue-50/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>

            <!-- Conteúdo -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                <h3 class="text-lg font-bold text-slate-800 whitespace-nowrap">
                    @if($isPJ)
                        Conta Pessoa Jurídica (CNPJ)
                    @else
                        Conta Pessoa Física (CPF)
                    @endif
                </h3>
                <span class="inline-flex items-center self-start sm:self-auto px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 uppercase tracking-wide shrink-0">
                    {{ $isPJ ? 'EMPRESARIAL' : 'PESSOAL' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Grid de Limites -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Limite Diário -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-all duration-300 hover:border-blue-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Diário</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 tracking-tight">R$ {{ number_format($dailyLimit, 2, ',', '.') }}</p>
                <p class="text-xs text-slate-500 mt-1 font-medium">Limite total por dia</p>
            </div>
        </div>

        <!-- Limite por Saque -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-all duration-300 hover:border-blue-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 rounded-lg text-purple-600 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Por Saque</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 tracking-tight">R$ {{ number_format($withdrawalLimit, 2, ',', '.') }}</p>
                <p class="text-xs text-slate-500 mt-1 font-medium">Máximo por operação</p>
            </div>
        </div>

        <!-- Limite por Documento -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-all duration-300 hover:border-blue-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-orange-50 rounded-lg text-orange-600 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Por {{ $isPJ ? 'CNPJ' : 'CPF' }}</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 tracking-tight">R$ {{ number_format($cpfLimit, 2, ',', '.') }}</p>
                <p class="text-xs text-slate-500 mt-1 font-medium">Limite por destinatário</p>
            </div>
        </div>

        <!-- Saques por Dia -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-all duration-300 hover:border-blue-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Frequência</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 tracking-tight">{{ $withdrawalsPerDay ?? '3' }}</p>
                <p class="text-xs text-slate-500 mt-1 font-medium">Saques diários permitidos</p>
            </div>
        </div>
    </div>
</div>
