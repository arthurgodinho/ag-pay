@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-4 px-2 sm:px-0">

    <!-- Alerta de Bloqueio -->
    @if(Auth::user()->is_blocked)
        <div class="rounded-xl p-4 bg-red-50 border border-red-200 text-center">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h2 class="text-lg font-bold text-red-600 mb-1">{{ __('dashboard.you_were_blocked') }}</h2>
                    <p class="text-sm text-red-500 mb-3">{{ __('dashboard.contact_manager_info') }}</p>
                    <a href="{{ route('manager.contact') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-semibold rounded-lg transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        {{ __('dashboard.talk_to_manager') }}
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Alerta KYC -->
        @if(Auth::user()->kyc_status === null || Auth::user()->kyc_status === 'rejected')
            <div class="rounded-xl p-4 bg-slate-800 border border-slate-700 shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-blue-100">
                            @if(Auth::user()->kyc_status === 'rejected')
                                {{ __('dashboard.kyc_rejected') }} <a href="{{ route('kyc.index') }}" class="underline hover:text-white font-medium ml-1">{{ __('dashboard.resubmit') }}</a>
                            @else
                                <a href="{{ route('kyc.index') }}" class="underline hover:text-white font-medium">{{ __('dashboard.complete_kyc') }}</a>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    @if(!Auth::user()->is_blocked)
    <!-- Cards Principais (Top Row) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Saldo Disponível -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-slate-200 transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wide">{{ __('dashboard.available_balance') }}</p>
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-lg sm:text-xl font-bold text-slate-800 mb-0.5">
                <span x-show="showBalances">R$ {{ number_format($availableBalance, 2, ',', '.') }}</span>
                <span x-show="!showBalances" x-cloak>R$ •••••</span>
            </p>
            <p class="text-[10px] sm:text-xs text-slate-400">{{ __('dashboard.available_balance_desc') }}</p>
        </div>

        <!-- Recebido Hoje -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-slate-200 transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wide">{{ __('dashboard.received_today') }}</p>
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
            <p class="text-lg sm:text-xl font-bold text-slate-800 mb-0.5">
                <span x-show="showBalances">R$ {{ number_format($receivedToday, 2, ',', '.') }}</span>
                <span x-show="!showBalances" x-cloak>R$ •••••</span>
            </p>
            <p class="text-[10px] sm:text-xs text-slate-400">{{ __('dashboard.received_today_desc') }}</p>
        </div>

        <!-- Bloqueio Cautelar -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-slate-200 transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wide">{{ __('dashboard.precautionary_lock') }}</p>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-amber-50 flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-lg sm:text-xl font-bold text-slate-800 mb-0.5">
                <span x-show="showBalances">R$ {{ number_format($cautionaryBlock, 2, ',', '.') }}</span>
                <span x-show="!showBalances" x-cloak>R$ •••••</span>
            </p>
            <p class="text-[10px] sm:text-xs text-slate-400">{{ __('dashboard.precautionary_lock_desc') }}</p>
        </div>

        <!-- Faturamento Total -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-slate-200 transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wide">{{ __('dashboard.total_billing') }}</p>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-purple-50 flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-lg sm:text-xl font-bold text-slate-800 mb-0.5">
                <span x-show="showBalances">R$ {{ number_format($totalBilling, 2, ',', '.') }}</span>
                <span x-show="!showBalances" x-cloak>R$ •••••</span>
            </p>
            <p class="text-[10px] sm:text-xs text-slate-400">{{ __('dashboard.total_billing_desc') }}</p>
        </div>
    </div>

    <!-- Cards de Ação -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Gerar PIX -->
        <a href="{{ route('dashboard.financial.index') }}" class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-blue-500/50 transition-all duration-300 hover:shadow-md hover:shadow-blue-500/10 cursor-pointer group">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 group-hover:border-blue-500/30 flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-slate-700 font-semibold text-sm truncate group-hover:text-blue-600 transition-colors">{{ __('financial.generate_pix') }}</p>
                        <p class="text-[10px] text-slate-500 hidden sm:block">{{ __('financial.qr_code_to_receive') }}</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <!-- Solicitar Saque -->
        <a href="{{ route('dashboard.financial.index') }}" class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-blue-500/50 transition-all duration-300 hover:shadow-md hover:shadow-blue-500/10 cursor-pointer group">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-blue-50 border border-blue-100 group-hover:border-blue-200 transition-colors">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-slate-700 font-semibold text-sm truncate group-hover:text-blue-600 transition-colors">{{ __('financial.request_withdrawal') }}</p>
                        <p class="text-[10px] text-slate-500 hidden sm:block">{{ __('financial.transfer_via_pix') }}</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <!-- Transações -->
        <a href="{{ route('dashboard.reports.index') }}" class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-blue-500/50 transition-all duration-300 hover:shadow-md hover:shadow-blue-500/10 cursor-pointer group">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 group-hover:border-blue-500/30 flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-slate-700 font-semibold text-sm truncate group-hover:text-blue-600 transition-colors">{{ __('dashboard.transactions') }}</p>
                        <p class="text-[10px] text-slate-500 hidden sm:block">{{ __('dashboard.view_all') }}</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <!-- Credenciais API -->
        <a href="{{ route('dashboard.api.index') }}" class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-blue-500/50 transition-all duration-300 hover:shadow-md hover:shadow-blue-500/10 cursor-pointer group">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 group-hover:border-blue-500/30 flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-slate-700 font-semibold text-sm truncate group-hover:text-blue-600 transition-colors">Credenciais API</p>
                        <p class="text-[10px] text-slate-500 hidden sm:block">Gerencie suas chaves</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>
    </div>

    <!-- Gráfico de Faturamento -->
    <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
        <div class="mb-4">
            <h2 class="text-base font-semibold text-slate-800 mb-0.5">Faturamento</h2>
            <p class="text-xs text-slate-500">Fluxo de caixa (30 dias)</p>
        </div>
        <div id="billingChart" class="h-64 sm:h-72"></div>
    </div>

    <!-- Seção de Conversão e Métricas Adicionais -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Conversão -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <h2 class="text-base font-semibold text-slate-800 mb-0.5">Conversão</h2>
            <p class="text-xs text-slate-500 mb-5">Pagamentos gerados vs. concluídos</p>
            
            <div class="space-y-4">
                <!-- Conversão Geral -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-xs font-medium text-slate-600">Geral</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800">
                            <span x-show="showBalances">{{ number_format($generalConversion, 2, ',', '.') }}%</span>
                            <span x-show="!showBalances" x-cloak>•••%</span>
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: {{ min($generalConversion, 100) }}%"></div>
                    </div>
                </div>

                <!-- Pix -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs font-medium text-slate-600">Pix</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800">
                            <span x-show="showBalances">{{ number_format($pixStats['conversion_rate'], 2, ',', '.') }}%</span>
                            <span x-show="!showBalances" x-cloak>•••%</span>
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: {{ min($pixStats['conversion_rate'], 100) }}%"></div>
                    </div>
                </div>

                <!-- Cartão de Crédito -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="text-xs font-medium text-slate-600">Cartão de Crédito</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800">
                            <span x-show="showBalances">{{ number_format($creditStats['conversion_rate'], 2, ',', '.') }}%</span>
                            <span x-show="!showBalances" x-cloak>•••%</span>
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: {{ min($creditStats['conversion_rate'], 100) }}%"></div>
                    </div>
                </div>

                <!-- Taxa de estorno (Chargebacks) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-xs font-medium text-slate-600">Taxa de estorno</span>
                        </div>
                        <span class="text-xs font-bold text-slate-800">
                            <span x-show="showBalances">{{ number_format($chargebackRate, 2, ',', '.') }}%</span>
                            <span x-show="!showBalances" x-cloak>•••%</span>
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-rose-500 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: {{ min($chargebackRate, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas Adicionais -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            <!-- Saldo a Liberar -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-slate-200 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-xs font-medium">Saldo a Liberar</p>
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-base font-bold text-slate-800 mb-0.5">
                    <span x-show="showBalances">R$ {{ number_format($balanceToRelease ?? 0, 2, ',', '.') }}</span>
                    <span x-show="!showBalances" x-cloak>R$ •••••</span>
                </p>
                <p class="text-[10px] text-slate-400">Aguardando liberação</p>
            </div>

            <!-- Ticket Médio -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-slate-200 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-xs font-medium">Ticket Médio</p>
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-base font-bold text-slate-800 mb-0.5">
                    <span x-show="showBalances">R$ {{ number_format($averageTicket, 2, ',', '.') }}</span>
                    <span x-show="!showBalances" x-cloak>R$ •••••</span>
                </p>
                <p class="text-[10px] text-slate-400">Média por venda</p>
            </div>

            <!-- Média diária -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-slate-200 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-xs font-medium">Média diária</p>
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-base font-bold text-slate-800 mb-0.5">
                    <span x-show="showBalances">R$ {{ number_format($dailyAverage, 2, ',', '.') }}</span>
                    <span x-show="!showBalances" x-cloak>R$ •••••</span>
                </p>
                <p class="text-[10px] text-slate-400">Média de faturamento</p>
            </div>

            <!-- Quantidade de Transações -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:border-slate-200 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-xs font-medium">Vendas Aprovadas</p>
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-base font-bold text-slate-800 mb-0.5">
                    {{ number_format($salesQuantity, 0, ',', '.') }}
                </p>
                <p class="text-[10px] text-slate-400">Total de vendas</p>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    // Gráfico de Faturamento (Entradas e Saídas)
    document.addEventListener('DOMContentLoaded', function() {
        const chartElement = document.querySelector("#billingChart");
        if (!chartElement) return;

        function initChart() {
            if (typeof ApexCharts === 'undefined') {
                setTimeout(initChart, 100); // Tenta novamente se a lib não carregou
                return;
            }

            const chartData = @json($chartData);
            
            const options = {
                series: [{
                    name: 'Entradas',
                    data: chartData.entries
                }, {
                    name: 'Saídas',
                    data: chartData.exits
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                    background: 'transparent'
                },
                colors: ['#2563EB', '#EF4444'], // Blue-600, Red-500
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: chartData.dates,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px' },
                        formatter: (value) => {
                            return new Intl.NumberFormat('pt-BR', {
                                style: 'currency',
                                currency: 'BRL',
                                notation: 'compact'
                            }).format(value);
                        }
                    }
                },
                grid: {
                    borderColor: '#e2e8f0',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } },
                    xaxis: { lines: { show: false } },
                    padding: { top: 0, right: 0, bottom: 0, left: 10 }
                },
                tooltip: {
                    theme: 'light',
                    style: { fontSize: '12px' },
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('pt-BR', {
                                style: 'currency',
                                currency: 'BRL'
                            }).format(val);
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px',
                    labels: { colors: '#475569' }
                }
            };

            const chart = new ApexCharts(chartElement, options);
            chart.render();
        }

        initChart();
    });
</script>
    
    <!-- Script para lazy loading de imagens -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                });
                
                lazyImages.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback para navegadores antigos
                lazyImages.forEach(img => img.classList.add('loaded'));
            }
        });
    </script>
@endsection
