<!-- Dados Pessoais e Conta -->
<div class="space-y-6">
    <!-- Card Principal: Foto e Info Básica -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <!-- Foto de Perfil com Upload -->
            <div class="relative shrink-0" x-data="{ showUpload: false }">
                <div class="relative group">
                    @if($user->profile_photo)
                        <img src="{{ asset(strpos($user->profile_photo, 'IMG/') === 0 ? $user->profile_photo : ('storage/' . $user->profile_photo)) }}" 
                             alt="Perfil" 
                             class="w-20 h-20 md:w-24 md:h-24 rounded-full object-cover border-4 border-slate-50 shadow-sm transition-transform group-hover:scale-105">
                    @else
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-slate-100 flex items-center justify-center border-4 border-slate-50 shadow-sm group-hover:bg-slate-200 transition-colors">
                            <svg class="w-8 h-8 md:w-10 md:h-10 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    @endif
                    
                    <!-- Botão de Upload Flutuante -->
                    <button @click="showUpload = !showUpload" 
                            class="absolute -bottom-1 -right-1 bg-slate-900 hover:bg-slate-800 text-white w-8 h-8 flex items-center justify-center rounded-full shadow-lg border-2 border-white transition-all hover:scale-110 z-10"
                            title="Alterar foto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal de Upload (Dropdown) -->
                <div x-show="showUpload" 
                     @click.away="showUpload = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute top-full left-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-slate-100 p-4 z-20"
                     style="display: none;">
                    <form method="POST" action="{{ route('dashboard.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        <p class="text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Nova Foto</p>
                        
                        <label class="block mb-3 cursor-pointer group">
                            <div class="border-2 border-dashed border-slate-200 rounded-lg p-4 group-hover:bg-slate-50 group-hover:border-blue-400 transition-all text-center">
                                <span class="text-xs text-slate-500 font-medium group-hover:text-blue-600 transition-colors">Clique para selecionar</span>
                                <input type="file" name="profile_photo" class="hidden" onchange="this.form.submit()">
                            </div>
                        </label>
                        
                        <button type="button" @click="showUpload = false" class="w-full py-1.5 text-xs text-slate-500 hover:text-slate-800 font-medium transition-colors">
                            Cancelar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Informações Principais -->
            <div class="text-center md:text-left flex-1 min-w-0">
                <div class="flex flex-col md:flex-row items-center gap-3 mb-1">
                    <h2 class="text-xl font-bold text-slate-900 truncate max-w-full">{{ $user->name }}</h2>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">
                            Ativa
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wide font-mono">
                            #{{ $user->id }}
                        </span>
                    </div>
                </div>
                <p class="text-sm text-slate-500 font-medium flex items-center justify-center md:justify-start gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    {{ $user->email }}
                </p>
            </div>
        </div>
    </div>

    <!-- Grid de Detalhes -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Dados da Conta -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-100 flex items-center gap-2.5">
                <div class="p-1.5 bg-white rounded-md border border-slate-200 shadow-sm text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .883-.393 1.627-1 2.138-.607.51-1.407.862-2.5.862-1.093 0-1.893-.352-2.5-.862C5.393 7.627 5 6.883 5 6m4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Dados Cadastrais</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="space-y-1">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Usuário</p>
                        <p class="text-sm font-semibold text-slate-700 truncate select-all" title="{{ $user->name }}">
                            {{ strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $user->name)) }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Documento</p>
                        <p class="text-sm font-semibold text-slate-700 font-mono select-all">
                            {{ $user->cpf_cnpj ?? '---' }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Telefone</p>
                        <p class="text-sm font-semibold text-slate-700 select-all">
                            {{ $user->phone ?? '---' }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Desde</p>
                        <p class="text-sm font-semibold text-slate-700">
                            {{ $user->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taxas da Plataforma -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-100 flex items-center gap-2.5">
                <div class="p-1.5 bg-white rounded-md border border-slate-200 shadow-sm text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Taxas da Plataforma</h3>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                <!-- Entrada PIX -->
                <div class="p-6 hover:bg-slate-50 transition-colors group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-12 h-12 text-blue-50 transform rotate-12 translate-x-4 -translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Entrada PIX</p>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-bold text-slate-800 tracking-tight">{{ number_format($cashinPixPercentual, 2, ',', '.') }}%</span>
                            <span class="text-xs font-medium text-slate-500">+ R$ {{ number_format($cashinPixFixo, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Entrada Cartão -->
                <div class="p-6 hover:bg-slate-50 transition-colors group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-12 h-12 text-purple-50 transform rotate-12 translate-x-4 -translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Entrada Cartão</p>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-bold text-slate-800 tracking-tight">{{ number_format($cashinCardPercentual, 2, ',', '.') }}%</span>
                            <span class="text-xs font-medium text-slate-500">+ R$ {{ number_format($cashinCardFixo, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Saque -->
                <div class="p-6 hover:bg-slate-50 transition-colors group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-12 h-12 text-red-50 transform rotate-12 translate-x-4 -translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Saque PIX</p>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-bold text-slate-800 tracking-tight">{{ number_format($cashoutPixPercentual, 2, ',', '.') }}%</span>
                            <span class="text-xs font-medium text-slate-500">+ R$ {{ number_format($cashoutPixFixo, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Saque Cripto -->
                <div class="p-6 hover:bg-slate-50 transition-colors group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-12 h-12 text-yellow-50 transform rotate-12 translate-x-4 -translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Saque Cripto</p>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-bold text-slate-800 tracking-tight">{{ number_format($cashoutCryptoPercentual, 2, ',', '.') }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>