<div class="space-y-6">
    <!-- Autenticação 2FA -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                <!-- Ícone -->
                <div class="shrink-0">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 ring-4 ring-blue-50/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Conteúdo -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <h3 class="text-lg font-bold text-slate-800">Autenticação 2FA</h3>
                        @if(Auth::user()->google2fa_enabled)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">
                                Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wide">
                                Inativo
                            </span>
                        @endif
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Adicione uma camada extra de segurança à sua conta exigindo um código de verificação do Google Authenticator ao fazer login.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Footer / Ações -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end">
            @if(Auth::user()->google2fa_enabled)
                <a href="{{ route('2fa.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-bold text-xs uppercase tracking-wide hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Gerenciar 2FA
                </a>
            @else
                <a href="{{ route('2fa.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg text-white font-bold text-xs uppercase tracking-wide hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/20 transition-all shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Ativar Agora
                </a>
            @endif
        </div>
    </div>

    <!-- PIN de Transação -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                <!-- Ícone -->
                <div class="shrink-0">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 ring-4 ring-indigo-50/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Conteúdo -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <h3 class="text-lg font-bold text-slate-800">PIN de Transação</h3>
                        @if(Auth::user()->pin)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">
                                Configurado
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 uppercase tracking-wide">
                                Pendente
                            </span>
                        @endif
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        O PIN de 6 dígitos é utilizado para confirmar todas as suas transações financeiras e alterações sensíveis na conta.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Footer / Ações -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end">
            <a href="{{ route('pin.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-bold text-xs uppercase tracking-wide hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                {{ Auth::user()->pin ? 'Alterar PIN' : 'Criar PIN' }}
            </a>
        </div>
    </div>

    <!-- Senha de Acesso -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                <!-- Ícone -->
                <div class="shrink-0">
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 ring-4 ring-orange-50/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Conteúdo -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Alterar Senha de Acesso</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">
                        Atualize sua senha periodicamente para manter sua conta segura. É necessário informar seu PIN atual para confirmar a alteração.
                    </p>

                    <form action="{{ route('dashboard.profile.password') }}" method="POST" class="space-y-4 max-w-xl">
                        @csrf
                        
                        <!-- Senha Atual -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Senha Atual</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </span>
                                <input type="password" name="current_password" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nova Senha -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Nova Senha</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </span>
                                    <input type="password" name="password" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirmar Senha -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Confirmar Nova Senha</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </span>
                                    <input type="password" name="password_confirmation" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- PIN de Confirmação -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">PIN de Segurança (6 dígitos)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </span>
                                <input type="password" name="pin" maxlength="6" pattern="[0-9]*" inputmode="numeric" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Digite seu PIN">
                            </div>
                            @error('pin')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-lg text-white font-bold text-xs uppercase tracking-wide hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/20 transition-all shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Atualizar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
