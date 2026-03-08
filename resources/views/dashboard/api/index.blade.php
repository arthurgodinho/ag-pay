@extends('layouts.app')

@section('title', 'API')

@section('content')
<div class="space-y-8" x-data="apiManager()">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Credenciais de API</h1>
            <p class="text-slate-500 mt-1">Gerencie suas chaves de acesso para integração segura com nossa plataforma.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('dashboard.documentation.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Documentação
            </a>
            <button @click="toggleCreateForm()"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-white font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Credencial
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 p-4 border border-emerald-200 shadow-sm animate-fade-in-down">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg bg-red-50 p-4 border border-red-200 shadow-sm animate-fade-in-down">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Create Form Modal/Card -->
    <div x-show="showCreateForm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-4"
         class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden relative">
        
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-800">Nova Credencial</h3>
            <button @click="showCreateForm = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('dashboard.api.store') }}" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Nome de Identificação <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Ex: E-commerce Principal" 
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <p class="text-xs text-slate-500">Um nome para você identificar esta chave.</p>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Projeto (Opcional)</label>
                    <input type="text" name="project" placeholder="Ex: Integração V1" 
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Modo de Saque</label>
                    <div class="relative">
                        <select name="withdrawal_mode" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm appearance-none">
                            <option value="manual">Manual (Requer aprovação)</option>
                            <option value="automatic">Automático (Processamento imediato)</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700">Expiração (Opcional)</label>
                    <input type="datetime-local" name="expires_at" 
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">URL de Webhook (Opcional)</label>
                    <input type="url" name="webhook_url" placeholder="https://seu-site.com/callback" 
                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <p class="text-xs text-slate-500">Receba notificações POST quando o status de uma transação mudar.</p>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="showCreateForm = false" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 font-medium rounded-lg text-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm hover:shadow-md">
                    Gerar Chave API
                </button>
            </div>
        </form>
    </div>

    <!-- Credentials List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Suas Credenciais</h2>
            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium border border-slate-200">
                {{ $tokens->count() }} ativa(s)
            </span>
        </div>

        @if($tokens->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Nenhuma credencial encontrada</h3>
                <p class="text-slate-500 mt-2 mb-8 max-w-md mx-auto">Crie sua primeira chave de API para começar a integrar seus sistemas com nossa plataforma de pagamentos.</p>
                <button @click="showCreateForm = true" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition-all shadow-sm hover:shadow-md inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Criar Primeira Credencial
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/5">Identificação</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/4">Client ID</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/3">Token (Secret)</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/12 text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/12 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tokens as $token)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-800">{{ $token->name }}</span>
                                        @if($token->project)
                                            <span class="inline-flex mt-1.5 self-start items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $token->project }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-slate-400 mt-2">Criado em {{ $token->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="flex items-center gap-2 group/copy relative max-w-[200px]">
                                        <code class="block text-xs font-mono bg-slate-100 text-slate-700 px-2 py-1.5 rounded border border-slate-200 truncate select-all flex-1">
                                            {{ $token->client_id }}
                                        </code>
                                        <button @click="copyToClipboard('{{ $token->client_id }}')" 
                                                class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-all opacity-100 sm:opacity-0 sm:group-hover/copy:opacity-100 flex-shrink-0" 
                                                title="Copiar Client ID">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="flex items-start gap-2 group/copy relative">
                                        <div class="relative w-full">
                                            <code class="block text-xs font-mono bg-slate-100 text-slate-700 px-2 py-1.5 rounded border border-slate-200 truncate select-all leading-relaxed">
                                                {{ $token->token }}
                                            </code>
                                        </div>
                                        <button @click="copyToClipboard('{{ $token->token }}')" 
                                                class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-all opacity-100 sm:opacity-0 sm:group-hover/copy:opacity-100 flex-shrink-0 mt-0.5" 
                                                title="Copiar Token">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top text-center">
                                    @if($token->isExpired())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">Expirado</span>
                                    @elseif($token->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                                            Ativo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">Revogado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-top text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <!-- Botão IPs -->
                                        <button @click="openIpModal({{ $token->toJson() }})" 
                                                class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors group/btn" title="Gerenciar IPs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </button>

                                        @if($token->is_active)
                                            <form method="POST" action="{{ route('dashboard.api.revoke', $token->id) }}">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Tem certeza que deseja revogar esta credencial? A integração irá parar de funcionar.')" 
                                                        class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors group/btn" title="Revogar Acesso">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                </button>
                                            </form>
                                        @elseif(!$token->isExpired())
                                            <form method="POST" action="{{ route('dashboard.api.reactivate', $token->id) }}">
                                                @csrf
                                                <button type="submit" 
                                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Reativar Acesso">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('dashboard.api.destroy', $token->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Excluir permanentemente? Esta ação não pode ser desfeita.')" 
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Excluir Permanentemente">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- IP Management Modal -->
    <div x-show="showIpModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden relative" @click.away="showIpModal = false">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Gerenciar IPs</h3>
                    <p class="text-xs text-slate-500" x-text="currentIpToken ? currentIpToken.name : ''"></p>
                </div>
                <button @click="showIpModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Add IP Form -->
                <form method="POST" action="{{ route('dashboard.api.allowed-ip.store') }}" class="mb-6">
                    @csrf
                    <input type="hidden" name="api_token_id" x-bind:value="currentIpToken ? currentIpToken.id : ''">
                    <div class="flex gap-2">
                        <input type="text" name="ip_address" required placeholder="Ex: 192.168.1.1" 
                               class="flex-1 px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition-colors shadow-sm">
                            Adicionar
                        </button>
                    </div>
                </form>

                <!-- List of IPs -->
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <template x-if="currentAllowedIps.length === 0">
                        <div class="text-center py-6 border-2 border-dashed border-slate-100 rounded-lg">
                            <p class="text-sm text-slate-500">Nenhum IP restrito cadastrado.</p>
                            <p class="text-xs text-slate-400 mt-1">Qualquer IP pode acessar com este token.</p>
                        </div>
                    </template>
                    
                    <template x-for="ip in currentAllowedIps" :key="ip.id">
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                <span class="text-slate-800 font-mono text-sm" x-text="ip.ip_address"></span>
                            </div>
                            <!-- Delete Form (Constructed dynamically) -->
                            <form method="POST" :action="'{{ url('dashboard/api/allowed-ip') }}/' + ip.id" @submit.prevent="$el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Tem certeza que deseja remover este IP?')" 
                                        class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Integration Guide -->
    <div class="bg-slate-900 rounded-xl shadow-lg border border-slate-800 overflow-hidden text-white">
        <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/50">
            <h3 class="font-bold text-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
                Guia Rápido de Integração
            </h3>
        </div>
        
        <div class="p-6 space-y-8">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Auth Info -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wider">1. Autenticação</h4>
                    <p class="text-slate-400 text-sm">Envie suas credenciais no header de todas as requisições para autenticar.</p>
                    
                    <div class="bg-slate-950 rounded-lg border border-slate-800 p-4 font-mono text-xs text-slate-300 shadow-inner">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-purple-400 font-bold min-w-[100px]">X-Client-ID:</span> 
                            <span class="text-slate-500 italic">{seu_client_id}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-purple-400 font-bold min-w-[100px]">Authorization:</span> 
                            <span class="text-slate-500 italic">Bearer {seu_token}</span>
                        </div>
                    </div>
                </div>

                <!-- Endpoint Info -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wider">2. Endpoint Base</h4>
                    <p class="text-slate-400 text-sm">Utilize esta URL base para todas as chamadas da API.</p>
                    
                    <div class="bg-slate-950 rounded-lg border border-slate-800 p-4 font-mono text-xs text-slate-300 shadow-inner flex items-center justify-between group">
                        <span class="text-emerald-400">{{ config('app.url') }}/api/v1</span>
                        <button @click="copyToClipboard('{{ config('app.url') }}/api/v1')" 
                                class="text-slate-500 hover:text-white transition-colors p-1 rounded hover:bg-slate-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- cURL Example -->
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wider">Exemplo de Requisição (cURL)</h4>
                <div class="bg-slate-950 rounded-lg border border-slate-800 p-5 font-mono text-xs text-slate-300 shadow-inner overflow-x-auto relative group">
                    <button @click="copyToClipboard('curl -X GET \'{{ config('app.url') }}/api/v1/transactions\' \\\n  -H \'X-Client-ID: seu_client_id\' \\\n  -H \'Authorization: Bearer seu_token\' \\\n  -H \'Accept: application/json\'')" 
                            class="absolute top-3 right-3 p-2 text-slate-500 hover:text-white bg-slate-900/50 hover:bg-slate-800 rounded-lg border border-slate-800 transition-all opacity-0 group-hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    <div class="leading-relaxed">
                        <span class="text-pink-400">curl</span> <span class="text-cyan-400">-X</span> GET <span class="text-green-400">"{{ config('app.url') }}/api/v1/transactions"</span> \ <br>
                        &nbsp;&nbsp;<span class="text-cyan-400">-H</span> <span class="text-yellow-200">"X-Client-ID: <span class="text-yellow-400">seu_client_id</span>"</span> \ <br>
                        &nbsp;&nbsp;<span class="text-cyan-400">-H</span> <span class="text-yellow-200">"Authorization: Bearer <span class="text-blue-400">seu_token</span>"</span> \ <br>
                        &nbsp;&nbsp;<span class="text-cyan-400">-H</span> <span class="text-yellow-200">"Accept: application/json"</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('apiManager', () => ({
            showCreateForm: false,
            showEditForm: false,
            showIpModal: false,
            currentIpToken: null,
            editingToken: null,
            currentAllowedIps: [],
            
            toggleCreateForm() {
                this.showCreateForm = !this.showCreateForm;
            },

            openEditModal(token) {
                this.editingToken = token;
                this.showEditForm = true;
            },

            openIpModal(token) {
                this.currentIpToken = token;
                this.currentAllowedIps = token.allowed_ips || [];
                this.showIpModal = true;
            },

            copyToClipboard(text) {
                if (!navigator.clipboard) {
                    const textArea = document.createElement("textarea");
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        this.showToast();
                    } catch (err) {
                        console.error('Unable to copy', err);
                    }
                    document.body.removeChild(textArea);
                    return;
                }
                
                navigator.clipboard.writeText(text).then(() => {
                    this.showToast();
                }, (err) => {
                    console.error('Could not copy text: ', err);
                });
            },

            showToast() {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-6 right-6 bg-slate-900 text-white px-6 py-3 rounded-lg shadow-xl z-50 text-sm font-medium flex items-center gap-3 animate-fade-in-up transform transition-all duration-300';
                toast.innerHTML = `
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Copiado para a área de transferência!</span>
                `;
                
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(10px)';
                    setTimeout(() => toast.remove(), 300);
                }, 2500);
            }
        }))
    })
</script>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 20px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out;
    }
</style>
@endsection
