@extends('layouts.admin')

@section('title', 'Editar Usuário')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'info' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-700 mb-2 inline-flex items-center transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Voltar
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Editar Usuário</h1>
            <p class="text-slate-500 mt-1">{{ $user->name }} ({{ $user->email }})</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <!-- Informações do Usuário -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Card de Informações -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tabs -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-200 flex overflow-x-auto">
                    <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'bg-slate-50 text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="px-6 py-3 font-medium transition-colors whitespace-nowrap">
                        Informações
                    </button>
                    <button @click="activeTab = 'balance'" :class="activeTab === 'balance' ? 'bg-slate-50 text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="px-6 py-3 font-medium transition-colors whitespace-nowrap">
                        Saldo
                    </button>
                    <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-slate-50 text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="px-6 py-3 font-medium transition-colors whitespace-nowrap">
                        Segurança
                    </button>
                    <button @click="activeTab = 'kyc'" :class="activeTab === 'kyc' ? 'bg-slate-50 text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="px-6 py-3 font-medium transition-colors whitespace-nowrap">
                        KYC
                    </button>
                </div>

                <!-- Tab: Informações -->
                <div x-show="activeTab === 'info'" class="p-6">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-8">
                            <!-- Informações Básicas -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Informações Básicas
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Nome</label>
                                        <input
                                            type="text"
                                            name="name"
                                            value="{{ $user->name }}"
                                            required
                                            class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                                        <input
                                            type="email"
                                            name="email"
                                            value="{{ $user->email }}"
                                            required
                                            class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- Split de Pagamento -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Split de Pagamento
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Split Fixo (R$)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-slate-500 sm:text-sm">R$</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="split_fixed"
                                                value="{{ $user->split_fixed ?? 0.00 }}"
                                                step="0.01"
                                                min="0"
                                                class="w-full pl-10 px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                                            >
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500">Valor fixo descontado por transação</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Split Variável (%)</label>
                                        <div class="relative">
                                            <input
                                                type="number"
                                                name="split_variable"
                                                value="{{ $user->split_variable ?? 0.00 }}"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                                            >
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-slate-500 sm:text-sm">%</span>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500">Porcentagem descontada por transação</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- Taxas -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Taxas Personalizadas
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Taxa de Entrada (%)</label>
                                        <div class="relative">
                                            <input
                                                type="number"
                                                name="taxa_entrada"
                                                value="{{ $user->taxa_entrada ?? 2.99 }}"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                                            >
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-slate-500 sm:text-sm">%</span>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500">Taxa aplicada em transações de entrada</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Taxa de Saída (%)</label>
                                        <div class="relative">
                                            <input
                                                type="number"
                                                name="taxa_saida"
                                                value="{{ $user->taxa_saida ?? 1.00 }}"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                                            >
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-slate-500 sm:text-sm">%</span>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500">Taxa aplicada em saques</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- Configurações -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Configurações da Conta
                                </h3>
                                <div class="space-y-4">
                                    <!-- Aprovado -->
                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div>
                                            <label class="text-sm font-semibold text-slate-900">Status da Conta</label>
                                            <p class="text-xs text-slate-500">Define se o usuário pode acessar o sistema</p>
                                        </div>
                                        <div class="flex gap-2">
                                            @if($user->is_approved)
                                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-lg border border-emerald-200">Aprovado</span>
                                                <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-lg transition-colors">Reprovar</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">Aprovar</button>
                                                </form>
                                                <span class="px-3 py-1.5 bg-slate-200 text-slate-600 text-sm font-semibold rounded-lg">Reprovado</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Bloqueado -->
                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div>
                                            <label class="text-sm font-semibold text-slate-900">Bloqueio de Acesso</label>
                                            <p class="text-xs text-slate-500">Impede login no sistema</p>
                                        </div>
                                        <div class="flex gap-2">
                                            @if($user->is_blocked)
                                                <span class="px-3 py-1.5 bg-red-100 text-red-700 text-sm font-semibold rounded-lg border border-red-200">Bloqueado</span>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1.5 bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 text-sm font-semibold rounded-lg transition-colors">Desbloquear</button>
                                                    </form>
                                                @endif
                                            @else
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.block', $user->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-lg transition-colors">Bloquear</button>
                                                    </form>
                                                @endif
                                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-lg border border-emerald-200">Ativo</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Bloquear Saque -->
                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div>
                                            <label class="text-sm font-semibold text-slate-900">Bloqueio de Saque</label>
                                            <p class="text-xs text-slate-500">Impede solicitações de saque</p>
                                        </div>
                                        <div class="flex gap-2">
                                            @if($user->bloquear_saque)
                                                <span class="px-3 py-1.5 bg-red-100 text-red-700 text-sm font-semibold rounded-lg border border-red-200">Bloqueado</span>
                                                <form action="{{ route('admin.users.unblock-withdrawal', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 text-sm font-semibold rounded-lg transition-colors">Liberar</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.block-withdrawal', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-lg transition-colors">Bloquear</button>
                                                </form>
                                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-lg border border-emerald-200">Liberado</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Modo de Saque -->
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div class="mb-3">
                                            <label class="block text-sm font-semibold text-slate-900 mb-1">Modo de Saque Individual</label>
                                            <p class="text-xs text-slate-500">Substitui a configuração global para este usuário</p>
                                        </div>
                                        <select name="withdrawal_mode" class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
                                            <option value="" {{ $user->withdrawal_mode === null ? 'selected' : '' }}>Usar padrão global</option>
                                            <option value="auto" {{ $user->withdrawal_mode === 'auto' ? 'selected' : '' }}>Automático</option>
                                            <option value="manual" {{ $user->withdrawal_mode === 'manual' ? 'selected' : '' }}>Manual</option>
                                        </select>
                                        <p class="text-xs text-slate-500 mt-2 bg-white p-2 rounded border border-slate-200 inline-block">
                                            @php
                                                $globalMode = \App\Models\Setting::get('withdrawal_mode', 'manual');
                                            @endphp
                                            Padrão global atual: <strong>{{ $globalMode === 'auto' ? 'Automático' : 'Manual' }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-100"></div>

                            <!-- Funções e Permissões -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                    Funções e Permissões
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div>
                                            <label class="text-sm font-semibold text-slate-900">Administrador</label>
                                            <p class="text-xs text-slate-500">Acesso total ao painel</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="hidden" name="is_admin" value="0">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_admin" value="1" class="sr-only peer" id="is_admin_{{ $user->id }}" {{ $user->is_admin ? 'checked' : '' }}>
                                                <div class="px-3 py-1.5 bg-white border border-slate-300 text-slate-600 text-sm font-semibold rounded-lg peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600 transition-all">
                                                    Administrador
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div>
                                            <label class="text-sm font-semibold text-slate-900">Gerente</label>
                                            <p class="text-xs text-slate-500">Pode gerenciar subordinados</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="hidden" name="is_manager" value="0">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_manager" value="1" class="sr-only peer" id="is_manager_{{ $user->id }}" {{ $user->is_manager ? 'checked' : '' }}>
                                                <div class="px-3 py-1.5 bg-white border border-slate-300 text-slate-600 text-sm font-semibold rounded-lg peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition-all">
                                                    Gerente
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gerente e Gateway -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Gerente da Conta</label>
                                    <select name="manager_id" class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
                                        <option value="">Sem gerente</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ $user->manager_id == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Adquirente Preferido</label>
                                    <select name="preferred_gateway" class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
                                        <option value="">Padrão do sistema</option>
                                        @foreach($gateways as $gateway)
                                            <option value="{{ $gateway->provider_name }}" {{ $user->preferred_gateway == $gateway->provider_name ? 'selected' : '' }}>
                                                {{ ucfirst($gateway->provider_name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl shadow-sm transition-all transform hover:scale-[1.02]">
                                    Salvar Todas Alterações
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab: Saldo -->
                <div x-show="activeTab === 'balance'" class="p-6 space-y-6">
                    <!-- Saldo Atual -->
                    <div class="bg-gradient-to-r from-slate-50 to-white rounded-xl p-6 border border-slate-200">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Saldo Atual</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="bg-white p-4 rounded-lg border border-slate-100 shadow-sm">
                                <p class="text-sm text-slate-500 mb-1">Disponível</p>
                                <p class="text-2xl font-bold text-emerald-600">R$ {{ number_format($user->wallet->balance ?? 0.00, 2, ',', '.') }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-slate-100 shadow-sm">
                                <p class="text-sm text-slate-500 mb-1">Congelado</p>
                                <p class="text-2xl font-bold text-amber-500">R$ {{ number_format($user->wallet->frozen_balance ?? 0.00, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Adicionar Saldo -->
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
                            <h3 class="text-lg font-bold text-slate-900 mb-4 text-emerald-700 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Adicionar Saldo
                            </h3>
                            <form method="POST" action="{{ route('admin.users.balance.add', $user->id) }}">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Valor</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-slate-500">R$</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="amount"
                                                step="0.01"
                                                min="0.01"
                                                required
                                                placeholder="0.00"
                                                class="w-full pl-10 px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all shadow-sm"
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Descrição</label>
                                        <input
                                            type="text"
                                            name="description"
                                            placeholder="Motivo do crédito..."
                                            class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all shadow-sm"
                                        >
                                    </div>
                                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                                        Creditiar Valor
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Remover Saldo -->
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
                            <h3 class="text-lg font-bold text-slate-900 mb-4 text-red-700 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                Remover Saldo
                            </h3>
                            <form method="POST" action="{{ route('admin.users.balance.remove', $user->id) }}">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Valor</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-slate-500">R$</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="amount"
                                                step="0.01"
                                                min="0.01"
                                                max="{{ $user->wallet->balance ?? 0 }}"
                                                required
                                                placeholder="0.00"
                                                class="w-full pl-10 px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all shadow-sm"
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Descrição</label>
                                        <input
                                            type="text"
                                            name="description"
                                            placeholder="Motivo do débito..."
                                            class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all shadow-sm"
                                        >
                                    </div>
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                                        Debitar Valor
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="border-t border-slate-200"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Congelar Saldo -->
                        <div class="bg-amber-50 rounded-xl p-6 border border-amber-200">
                            <h3 class="text-lg font-bold text-amber-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Congelar Saldo
                            </h3>
                            <form method="POST" action="{{ route('admin.users.balance.freeze', $user->id) }}" onsubmit="return confirm('Deseja congelar este valor? O saldo será movido para bloqueio cautelar.');">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-amber-900 mb-2">Valor</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-amber-700">R$</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="amount"
                                                step="0.01"
                                                min="0.01"
                                                max="{{ $user->wallet->balance ?? 0 }}"
                                                required
                                                placeholder="0.00"
                                                class="w-full pl-10 px-4 py-2 bg-white border border-amber-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all shadow-sm"
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-amber-900 mb-2">Motivo</label>
                                        <input
                                            type="text"
                                            name="description"
                                            placeholder="Motivo do congelamento..."
                                            class="w-full px-4 py-2 bg-white border border-amber-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all shadow-sm"
                                        >
                                    </div>
                                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                                        Congelar Valor
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Descongelar Saldo -->
                        <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                            <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                Descongelar Saldo
                            </h3>
                            <form method="POST" action="{{ route('admin.users.balance.unfreeze', $user->id) }}" onsubmit="return confirm('Deseja descongelar este valor? O saldo será movido de volta para disponível.');">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-blue-900 mb-2">Valor</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-blue-700">R$</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="amount"
                                                step="0.01"
                                                min="0.01"
                                                max="{{ $user->wallet->frozen_balance ?? 0 }}"
                                                required
                                                placeholder="0.00"
                                                class="w-full pl-10 px-4 py-2 bg-white border border-blue-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all shadow-sm"
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-blue-900 mb-2">Motivo</label>
                                        <input
                                            type="text"
                                            name="description"
                                            placeholder="Motivo do descongelamento..."
                                            class="w-full px-4 py-2 bg-white border border-blue-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all shadow-sm"
                                        >
                                    </div>
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                                        Descongelar Valor
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab: Segurança -->
                <div x-show="activeTab === 'security'" class="p-6">
                    <div class="max-w-xl mx-auto">
                        <div class="text-center mb-6">
                            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900">Alterar Senha de Acesso</h3>
                            <p class="text-slate-500 text-sm">Defina uma nova senha para este usuário</p>
                        </div>

                        <form method="POST" action="{{ route('admin.users.password', $user->id) }}" class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Nova Senha</label>
                                    <input
                                        type="password"
                                        name="password"
                                        required
                                        minlength="8"
                                        class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Confirmar Senha</label>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        required
                                        minlength="8"
                                        class="w-full px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm"
                                    >
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-sm">
                                    Atualizar Senha
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab: KYC -->
                <div x-show="activeTab === 'kyc'" class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-slate-900">Documentos KYC</h3>
                        <div class="text-sm">
                            <span class="text-slate-500 mr-2">Status Geral:</span>
                            @if($user->kyc_status === 'approved')
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 font-bold rounded-lg border border-emerald-200">Aprovado</span>
                            @elseif($user->kyc_status === 'rejected')
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 font-bold rounded-lg border border-red-200">Rejeitado</span>
                            @elseif($user->kyc_status === 'pending')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 font-bold rounded-lg border border-amber-200">Pendente</span>
                            @else
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-500 font-bold rounded-lg border border-slate-200">Não Enviado</span>
                            @endif
                        </div>
                    </div>
                    
                    @if(empty($user->doc_front) && empty($user->doc_back) && empty($user->selfie_with_doc) && empty($user->facial_biometrics))
                        <div class="text-center py-16 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                            <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-slate-500 font-medium">Nenhum documento KYC enviado ainda.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Documentos -->
                            @foreach(['doc_front' => 'Frente do Documento', 'doc_back' => 'Verso do Documento', 'selfie_with_doc' => 'Selfie com Documento', 'facial_biometrics' => 'Biometria Facial', 'cnpj_card' => 'Comprovante CNPJ'] as $field => $label)
                                @if($user->$field)
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 shadow-sm">
                                        <h4 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $label }}
                                        </h4>
                                        <div class="relative group">
                                            <a href="{{ asset('storage/' . $user->$field) }}" target="_blank" class="block overflow-hidden rounded-lg">
                                                @if(pathinfo($user->$field, PATHINFO_EXTENSION) === 'pdf')
                                                    <div class="w-full h-56 bg-white border border-slate-200 flex flex-col items-center justify-center hover:bg-slate-50 transition-colors">
                                                        <svg class="w-12 h-12 text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <p class="text-slate-900 font-medium">Documento PDF</p>
                                                        <p class="text-slate-500 text-xs mt-1">Clique para visualizar</p>
                                                    </div>
                                                @else
                                                    <img src="{{ asset('storage/' . $user->$field) }}" alt="{{ $label }}" class="w-full h-56 object-cover transform group-hover:scale-105 transition-transform duration-300">
                                                @endif
                                            </a>
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                                <div class="bg-white/90 p-2 rounded-full pointer-events-auto">
                                                    <a href="{{ asset('storage/' . $user->$field) }}" target="_blank" class="text-slate-900">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Ações do KYC -->
                        <div class="mt-8 bg-slate-50 rounded-xl p-6 border border-slate-200">
                            <h4 class="text-base font-bold text-slate-900 mb-4">Gerenciar Verificação</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <h5 class="text-sm font-semibold text-slate-700 mb-2">Dados do Usuário</h5>
                                    <ul class="space-y-2 text-sm">
                                        <li class="flex justify-between">
                                            <span class="text-slate-500">Tipo:</span>
                                            <span class="text-slate-900 font-medium">{{ $user->person_type === 'PJ' ? 'Pessoa Jurídica' : 'Pessoa Física' }}</span>
                                        </li>
                                        <li class="flex justify-between">
                                            <span class="text-slate-500">Documento:</span>
                                            <span class="text-slate-900 font-medium">{{ $user->cpf_cnpj }}</span>
                                        </li>
                                        @if($user->rejection_reason)
                                            <li class="mt-2 p-2 bg-red-50 border border-red-100 rounded text-red-700 text-xs">
                                                <strong>Motivo Rejeição Anterior:</strong> {{ $user->rejection_reason }}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                
                                <div class="flex flex-col gap-3 justify-center">
                                    @if($user->kyc_status !== 'approved')
                                        <form action="{{ route('admin.users.kyc.approve', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Aprovar Documentação
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($user->kyc_status !== 'rejected')
                                        <form action="{{ route('admin.users.kyc.reject', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold py-3 px-4 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Reprovar Documentação
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Resumo -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 sticky top-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-2xl font-bold text-slate-600 border border-slate-200">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $user->name }}</h3>
                        <p class="text-slate-500 text-sm">Cliente desde {{ $user->created_at->format('M Y') }}</p>
                    </div>
                </div>

                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-slate-500">ID do Usuário</span>
                        <span class="text-slate-900 font-mono font-medium">#{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-slate-500">Cadastrado em</span>
                        <span class="text-slate-900 font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-slate-500">Status KYC</span>
                        @if($user->kyc_status === 'approved')
                            <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700 font-bold">Aprovado</span>
                        @elseif($user->kyc_status === 'rejected')
                            <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 font-bold">Rejeitado</span>
                        @elseif($user->kyc_status === 'pending')
                            <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700 font-bold">Pendente</span>
                        @else
                            <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 text-slate-500 font-bold">N/A</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-500">Total Transações</span>
                        <span class="text-slate-900 font-bold bg-slate-100 px-2 py-0.5 rounded">{{ $user->transactions()->count() }}</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                        <p class="text-indigo-800 font-semibold text-sm mb-1">Saldo Total</p>
                        <p class="text-2xl font-bold text-indigo-600">R$ {{ number_format(($user->wallet->balance ?? 0) + ($user->wallet->frozen_balance ?? 0), 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection