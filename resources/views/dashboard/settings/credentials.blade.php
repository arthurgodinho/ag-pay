<!-- Gerenciar Credenciais -->
@if($apiToken)
    <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Credenciais de API</h2>
                <p class="text-xs text-slate-500 mt-0.5">Gerencie suas chaves de acesso para integração.</p>
            </div>
        </div>
        
        <div class="space-y-3">
            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider mb-1">Client ID</p>
                    <div class="flex items-center gap-2">
                        <code class="text-slate-800 font-mono text-xs sm:text-sm bg-white border border-slate-200 px-2 py-1 rounded truncate">{{ $apiToken->client_id }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ $apiToken->client_id }}')" class="text-blue-600 hover:text-blue-700 transition-colors" title="Copiar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider mb-1">Token</p>
                    <div class="flex items-center gap-2">
                        <code class="text-slate-800 font-mono text-xs sm:text-sm bg-white border border-slate-200 px-2 py-1 rounded truncate">{{ Str::limit($apiToken->token, 20) }}...</code>
                        <span class="text-[10px] text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Oculto</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider mb-0.5">Projeto</p>
                        <p class="text-slate-800 font-medium text-xs sm:text-sm">{{ $apiToken->project ?? 'Não definido' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider mb-0.5">Criado em</p>
                        <p class="text-slate-800 font-medium text-xs sm:text-sm">{{ $apiToken->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-slate-200">
                <a href="{{ route('dashboard.documentation.index') }}" class="px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg transition-colors text-xs font-medium flex items-center gap-2 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Ver Documentação
                </a>
                <form method="POST" action="{{ route('dashboard.api.destroy', $apiToken->id) }}" onsubmit="return confirm('Tem certeza que deseja redefinir as credenciais? Esta ação não pode ser desfeita.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors text-xs font-medium border border-red-200">
                        Redefinir Credenciais
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- IP's Permitidos -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-50 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.2-2.858.59-4.18"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">IPs Permitidos</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Lista de IPs autorizados a realizar requisições.</p>
                </div>
            </div>
            <button onclick="document.getElementById('addIpModal').classList.remove('hidden')" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-sm flex items-center gap-2 text-xs font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Adicionar IP
            </button>
        </div>

        <div class="space-y-2">
            @forelse($apiToken->allowedIps as $ip)
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                        <span class="text-slate-800 font-mono text-xs sm:text-sm">{{ $ip->ip_address }}</span>
                    </div>
                    <form method="POST" action="{{ route('dashboard.api.allowed-ip.destroy', $ip->id) }}" onsubmit="return confirm('Tem certeza que deseja remover este IP?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-all" title="Remover IP">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            @empty
                <div class="text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                    <div class="w-10 h-10 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <p class="text-slate-500 text-xs">Nenhum IP restrito cadastrado</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Isso significa que qualquer IP com o token pode acessar.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Adicionar IP -->
    <div id="addIpModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-all">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full transform transition-all scale-100">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-xl font-bold text-slate-800">Adicionar IP Permitido</h3>
                <p class="text-sm text-slate-500 mt-1">Digite o endereço IP que deseja autorizar.</p>
            </div>
            
            <form method="POST" action="{{ route('dashboard.api.allowed-ip.store') }}" class="p-6">
                @csrf
                <input type="hidden" name="api_token_id" value="{{ $apiToken->id }}">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Endereço IP</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                        <input type="text" name="ip_address" required placeholder="Ex: 192.168.1.1" 
                               class="w-full pl-10 pr-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('addIpModal').classList.add('hidden')" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-xl transition-colors font-medium">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors shadow-sm font-medium">
                        Adicionar IP
                    </button>
                </div>
            </form>
        </div>
    </div>
@else
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-sm">
        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Sem Credenciais</h3>
        <p class="text-slate-500 mb-8 max-w-md mx-auto">Você ainda não possui credenciais de API geradas. Crie agora para começar a integrar seu sistema.</p>
        <a href="{{ route('dashboard.api.index') }}" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all shadow-sm font-semibold inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Gerar Credenciais
        </a>
    </div>
@endif
