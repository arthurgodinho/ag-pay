@extends('layouts.app')

@section('title', 'Financeiro')

@section('content')
<div class="space-y-4 px-2 sm:px-0" x-data="{ showBalances: localStorage.getItem('showBalances') !== 'false' }">
    <!-- Header Otimizado -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                Dashboard Financeiro
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Gerencie seus saldos, depósitos e saques com total controle.
            </p>
        </div>
        
        <button 
            @click="showBalances = !showBalances; localStorage.setItem('showBalances', showBalances)" 
            class="p-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-400 hover:text-slate-600 transition-all shadow-sm"
            title="{{ __('dashboard.hide_show_balances') }}"
        >
            <!-- Ícone de olho aberto -->
            <svg x-show="showBalances" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <!-- Ícone de olho fechado -->
            <svg x-show="!showBalances" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.532 13.532l-3.29-3.29M3 3l18 18"></path>
            </svg>
        </button>
    </div>

    <!-- Alertas Otimizados -->
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-blue-50 border border-emerald-200 text-blue-600 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if(!$canWithdraw)
        <div class="px-4 py-3 rounded-xl bg-yellow-50 border border-yellow-200 text-yellow-600 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            Seus saques estão temporariamente bloqueados. Entre em contato com o suporte.
        </div>
    @endif

    <!-- Cards de Saldo Otimizados -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
        <!-- Disponível para Saque -->
        <div class="bg-white rounded-xl p-4 border border-slate-100 hover:border-slate-200 transition-all shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex-1">
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wide mb-0.5">{{ __('financial.available_for_withdrawal') }}</p>
                    <p class="text-lg sm:text-xl font-bold text-slate-800 mb-0.5" x-show="showBalances">
                        R$ {{ number_format($availableBalance, 2, ',', '.') }}
                    </p>
                    <p class="text-lg sm:text-xl font-bold mb-0.5 text-slate-600" x-show="!showBalances" x-cloak>••••••</p>
                    <p class="text-[10px] sm:text-xs text-blue-600">{{ __('dashboard.available_balance_desc') }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 border border-blue-100 flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Aguardando Aprovação (apenas saques manuais pendentes) -->
        @if($pendingBalance > 0)
        <div class="bg-white rounded-xl p-4 border border-slate-100 hover:border-slate-200 transition-all shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex-1">
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wide mb-0.5">Saques Pendentes</p>
                    <p class="text-lg sm:text-xl font-bold text-yellow-600 mb-0.5" x-show="showBalances">
                        R$ {{ number_format($pendingBalance, 2, ',', '.') }}
                    </p>
                    <p class="text-lg sm:text-xl font-bold mb-0.5 text-slate-600" x-show="!showBalances" x-cloak>••••••</p>
                    <p class="text-[10px] sm:text-xs text-slate-500">Aguardando aprovação</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-yellow-50 border border-yellow-100 flex-shrink-0">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Cards de Ação Otimizados -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Card Depósito Otimizado -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-100 hover:border-slate-200 transition-all shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">{{ __('financial.deposit') }}</h3>
                    <p class="text-xs text-slate-500">{{ __('financial.deposit_pix') }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
            </div>
            
            <form id="depositForm" class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">{{ __('financial.full_name') }}</label>
                    <input
                        type="text"
                        id="payer_name"
                        name="payer_name"
                        required
                        placeholder="Seu nome completo"
                        style="font-size: 14px;"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    >
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">{{ __('financial.deposit_amount') }}</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">R$</span>
                        <input
                            type="number"
                            id="deposit_amount"
                            name="amount"
                            step="0.01"
                            min="{{ $depositMinValue }}"
                            required
                            placeholder="0,00"
                            style="font-size: 14px;"
                            class="w-full pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">{{ __('financial.minimum_value') }} R$ {{ number_format($depositMinValue, 2, ',', '.') }}</p>
                </div>
                
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transform hover:scale-[1.02] active:scale-95 text-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>{{ __('financial.generate_qr_code_pix') }}</span>
                </button>
            </form>
        </div>

        <!-- Card Saque Otimizado -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-100 hover:border-slate-200 transition-all shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">{{ __('financial.withdrawal') }}</h3>
                    <p class="text-xs text-slate-500">{{ __('financial.transfer_via_pix') }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-blue-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            
            @if($canWithdraw)
                <form id="withdrawalForm" method="POST" action="{{ route('dashboard.financial.withdrawal') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">{{ __('financial.amount') }} *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">R$</span>
                            <input
                                type="number"
                                id="withdrawal_amount"
                                name="amount"
                                step="0.01"
                                min="{{ $withdrawalMinValue ?? 10.00 }}"
                                required
                                placeholder="0,00"
                                oninput="updateWithdrawalCalculations(this.value)"
                                style="font-size: 14px;"
                                class="w-full pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            >
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1">{{ __('financial.inform_net_value') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">{{ __('financial.pix_key') }} *</label>
                        <input
                            type="text"
                            id="pix_key"
                            name="pix_key"
                            required
                            placeholder="CPF, E-mail, Telefone ou Chave Aleatória"
                            style="font-size: 14px;"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        >
                    </div>
                    
                    <!-- Informações de cálculo otimizadas -->
                    <div id="withdrawalCalculation" class="bg-slate-50 rounded-lg p-3 space-y-1.5 border border-slate-100" style="display: none;">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">{{ __('financial.value_blocked') }}</span>
                            <span class="font-semibold text-slate-800" id="withdrawalAmountDisplay">R$ 0,00</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">{{ __('common.fees') }}:</span>
                            <span class="font-semibold text-yellow-600" id="withdrawalFeeDisplay">R$ 0,00</span>
                        </div>
                        <div class="border-t border-slate-200 pt-1.5 mt-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-700 font-medium text-xs">{{ __('financial.you_will_receive') }}</span>
                                <span class="text-base font-bold text-blue-600" id="withdrawalNetDisplay">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aviso de saldo insuficiente -->
                    <div id="withdrawalWarning" class="hidden"></div>
                    
                    <button
                        type="button"
                        id="withdrawalSubmitBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transform hover:scale-[1.02] active:scale-95 text-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>{{ __('financial.request_withdrawal_btn') }}</span>
                    </button>
                </form>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                    <svg class="w-10 h-10 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-red-600 font-medium text-sm">{{ __('financial.withdrawals_blocked') }}</p>
                    <p class="text-red-500 text-xs mt-0.5">Entre em contato com o suporte</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Card Antecipação de Saldo (se houver saldo a liberar) -->
    @if($balanceToRelease > 0)
    <div class="bg-slate-900 rounded-xl p-4 sm:p-5 border border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-white mb-0.5">Antecipação de Recebíveis</h3>
                <p class="text-xs text-slate-400">Antecipe seus pagamentos de cartão instantaneamente</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-slate-800 rounded-lg p-3 mb-3 border border-slate-700">
            <div class="flex justify-between items-center mb-1.5">
                <span class="text-slate-400 text-xs">Saldo a Liberar:</span>
                <span class="text-base font-bold text-white">R$ {{ number_format($balanceToRelease, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center mb-1.5">
                <span class="text-slate-400 text-xs">Taxa ({{ number_format($advanceFeePercentage, 2, ',', '.') }}%):</span>
                <span class="text-sm font-semibold text-yellow-400">- R$ {{ number_format(($balanceToRelease * $advanceFeePercentage) / 100, 2, ',', '.') }}</span>
            </div>
            <div class="border-t border-slate-700 pt-1.5 mt-1.5 flex justify-between items-center">
                <span class="text-slate-300 font-medium text-xs">Líquido a Receber:</span>
                <span class="text-lg font-bold text-blue-400">R$ {{ number_format($balanceToRelease - (($balanceToRelease * $advanceFeePercentage) / 100), 2, ',', '.') }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('dashboard.financial.advance') }}" onsubmit="return confirm('Ao antecipar o saldo, você aceita pagar uma taxa de {{ number_format($advanceFeePercentage, 2, ',', '.') }}% sobre o valor total. Deseja continuar?');">
            @csrf
            <div class="mb-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="accept_terms" required class="w-4 h-4 text-purple-600 bg-slate-800 border-slate-700 rounded focus:ring-purple-500">
                    <span class="text-xs text-gray-300">
                        Aceito os termos de antecipação e a taxa de <strong>{{ number_format($advanceFeePercentage, 2, ',', '.') }}%</strong>.
                    </span>
                </label>
            </div>
            <button
                type="submit"
                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-purple-900/20 hover:shadow-purple-900/30 transform hover:scale-[1.02] text-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span>Solicitar Antecipação</span>
            </button>
        </form>
    </div>
    @endif

    <!-- Modal PIN para Saque -->
    <div x-data="pinModal()" 
         x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.away="closeModal()"
         class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="bg-slate-900 rounded-lg p-6 sm:p-8 border border-slate-700 shadow-2xl max-w-md w-full relative"
             @click.stop>
            <!-- Botão Fechar -->
            <button @click="closeModal()" 
                    class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-14 h-14 mx-auto mb-3 bg-blue-500/10 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-1">Confirmação de Segurança</h3>
                <p class="text-slate-400 text-xs">Digite seu PIN de 6 dígitos para confirmar o saque</p>
            </div>

            <!-- Input PIN -->
            <div class="mb-6">
                <div class="flex gap-2 justify-center" id="pinInputs">
                    <input type="text" 
                           maxlength="1" 
                           pattern="[0-9]"
                           inputmode="numeric"
                           x-ref="pin0"
                           @input="handlePinInput(0, $event)"
                           @keydown="handlePinKeydown(0, $event)"
                           class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl font-bold bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                           autocomplete="off">
                    <input type="text" 
                           maxlength="1" 
                           pattern="[0-9]"
                           inputmode="numeric"
                           x-ref="pin1"
                           @input="handlePinInput(1, $event)"
                           @keydown="handlePinKeydown(1, $event)"
                           class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl font-bold bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                           autocomplete="off">
                    <input type="text" 
                           maxlength="1" 
                           pattern="[0-9]"
                           inputmode="numeric"
                           x-ref="pin2"
                           @input="handlePinInput(2, $event)"
                           @keydown="handlePinKeydown(2, $event)"
                           class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl font-bold bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                           autocomplete="off">
                    <input type="text" 
                           maxlength="1" 
                           pattern="[0-9]"
                           inputmode="numeric"
                           x-ref="pin3"
                           @input="handlePinInput(3, $event)"
                           @keydown="handlePinKeydown(3, $event)"
                           class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl font-bold bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                           autocomplete="off">
                    <input type="text" 
                           maxlength="1" 
                           pattern="[0-9]"
                           inputmode="numeric"
                           x-ref="pin4"
                           @input="handlePinInput(4, $event)"
                           @keydown="handlePinKeydown(4, $event)"
                           class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl font-bold bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                           autocomplete="off">
                    <input type="text" 
                           maxlength="1" 
                           pattern="[0-9]"
                           inputmode="numeric"
                           x-ref="pin5"
                           @input="handlePinInput(5, $event)"
                           @keydown="handlePinKeydown(5, $event)"
                           class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl font-bold bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                           autocomplete="off">
                </div>
                <input type="hidden" id="pinValue" name="pin" value="">
            </div>

            <!-- Mensagem de Erro -->
            <div x-show="errorMessage" 
                 x-transition
                 class="mb-4 bg-red-500/10 border border-red-500/30 rounded-lg p-2.5 text-center">
                <p class="text-red-400 text-xs sm:text-sm" x-text="errorMessage"></p>
            </div>

            <!-- Botões -->
            <div class="flex gap-3">
                <button @click="closeModal()" 
                        class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg transition-colors font-medium text-sm">
                    Cancelar
                </button>
                <button @click="confirmWithdrawal()" 
                        :disabled="isProcessing || !isPinComplete"
                        :class="isProcessing || !isPinComplete ? 'opacity-50 cursor-not-allowed' : ''"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all font-medium text-sm">
                    <span x-show="!isProcessing">Confirmar</span>
                    <span x-show="isProcessing" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processando...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal QR Code PIX Otimizado -->
    <div x-data="qrCodeModal()" 
         x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.away="closeModal()"
         class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="bg-gradient-to-br from-[#151A23] to-[#0f1419] rounded-2xl p-6 sm:p-8 border border-white/10 shadow-2xl max-w-md w-full relative"
             @click.stop>
            <!-- Botão Fechar -->
            <button @click="closeModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Header -->
            <div class="text-center mb-5">
                <h3 class="text-xl font-bold text-white mb-1">{{ __('financial.pay_via_pix') }}</h3>
                <p class="text-gray-400 text-xs">{{ __('financial.scan_qr_code') }}</p>
            </div>

            <!-- Timer -->
            <div x-show="!isExpired" class="mb-5">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-500/20 border border-red-500/30 rounded-lg mx-auto block w-fit">
                    <svg class="w-4 h-4 text-red-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-red-400 font-semibold text-xs">
                        {{ __('financial.remaining_time') }} <span x-text="formatTimer()"></span>
                    </span>
                </div>
            </div>

            <!-- QR Code -->
            <div class="bg-white rounded-xl p-4 mb-5 flex justify-center" x-show="!isExpired">
                <div id="qrCodeContainerModal" class="flex justify-center"></div>
            </div>

            <!-- Mensagem de Expirado -->
            <div x-show="isExpired" class="mb-5 text-center">
                <div class="bg-red-500/20 border border-red-500/30 rounded-xl p-5">
                    <svg class="w-12 h-12 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-400 font-semibold text-base">{{ __('financial.qr_code_expired') }}</p>
                    <p class="text-gray-400 text-xs mt-1">{{ __('financial.payment_time_expired') }}</p>
                </div>
            </div>

            <!-- Chave PIX Copia e Cola -->
            <div x-show="!isExpired && qrCodeData?.pix_key" class="mb-5">
                <label class="block text-xs font-medium text-gray-300 mb-1.5">{{ __('financial.pix_key_copy_paste') }}</label>
                <div class="flex gap-2">
                    <input type="text" 
                           :value="qrCodeData?.pix_key || ''" 
                           readonly
                           id="pixKeyInputModal"
                           class="flex-1 px-3 py-2 bg-[#0B0E14] border border-white/5 rounded-lg text-white text-xs font-mono">
                    <button @click="copyPixKey()" 
                            class="px-3 py-2 bg-[#00B2FF] hover:bg-[#0099CC] text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Valor -->
            <div class="border-t border-white/10 pt-4 mb-5" x-show="!isExpired && qrCodeData?.amount_gross">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Valor:</span>
                    <span class="text-xl font-bold text-[#00D9AC]" x-text="'R$ ' + formatMoney(qrCodeData?.amount_gross || 0)"></span>
                </div>
            </div>

            <!-- Instruções -->
            <div class="bg-[#00B2FF]/10 border border-[#00B2FF]/30 rounded-xl p-3 mb-5" x-show="!isExpired && !isPaymentConfirmed">
                <p class="text-xs text-gray-300 leading-relaxed">
                    <strong class="text-[#00B2FF]">Instruções:</strong><br>
                    1. Abra o app do seu banco<br>
                    2. Escaneie o QR Code ou cole a chave PIX<br>
                    3. Confirme o pagamento e aguarde
                </p>
            </div>
            
            <!-- Mensagem de Pagamento Confirmado -->
            <div x-show="isPaymentConfirmed" 
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-gradient-to-r from-[#00D9AC]/20 to-[#00B2FF]/20 border-2 border-[#00D9AC] rounded-xl p-5 mb-5 text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-[#00D9AC]/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#00D9AC]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-[#00D9AC] mb-1">Pagamento Confirmado!</h3>
                <p class="text-gray-300 text-sm mb-2">Depósito processado com sucesso.</p>
                <p class="text-xs text-gray-400">Redirecionando...</p>
            </div>

            <!-- Botão Fechar -->
            <button @click="closeModal()" 
                    class="w-full py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg transition-colors font-medium text-sm">
                Fechar
            </button>
        </div>
    </div>
</div>

<script>
    // Função para gerar QR Code de depósito
    document.getElementById('depositForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const payerName = document.getElementById('payer_name').value.trim();
        if (!payerName) {
            alert('Por favor, informe o nome completo');
            return;
        }
        
        const amount = parseFloat(document.getElementById('deposit_amount').value);
        const minValue = {{ $depositMinValue }};
        if (!amount || amount < minValue) {
            alert('Por favor, informe um valor válido (mínimo R$ ' + minValue.toFixed(2).replace('.', ',') + ')');
            return;
        }

        const formData = new FormData();
        formData.append('payer_name', payerName);
        formData.append('amount', amount);
        formData.append('_token', '{{ csrf_token() }}');

        const submitButton = document.querySelector('#depositForm button[type="submit"]');
        const originalButtonText = submitButton ? submitButton.innerHTML : '';
        
        try {
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Gerando...';
            }

            console.log('Iniciando requisição para gerar QR Code...');
            
            // Cria um AbortController para timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 segundos de timeout

            let response;
            try {
                response = await fetch('{{ route("dashboard.financial.deposit.qr") }}', {
                    method: 'POST',
                    body: formData,
                    signal: controller.signal,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                clearTimeout(timeoutId);
            } catch (fetchError) {
                clearTimeout(timeoutId);
                if (fetchError.name === 'AbortError') {
                    throw new Error('A requisição demorou muito tempo. Por favor, tente novamente.');
                }
                throw fetchError;
            }

            console.log('Resposta recebida:', response.status, response.statusText);

            // Verifica se a resposta é JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const textResponse = await response.text();
                console.error('Resposta não é JSON:', textResponse.substring(0, 500));
                
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
                
                let errorMsg = 'Erro ao gerar QR Code. ';
                if (response.status === 403) {
                    errorMsg += 'Acesso negado. Verifique as credenciais do gateway.';
                } else if (response.status === 500) {
                    errorMsg += 'Erro interno do servidor. Verifique os logs.';
                } else {
                    errorMsg += `Status: ${response.status}`;
                }
                
                alert(errorMsg);
                return;
            }

            let data;
            try {
                data = await response.json();
            } catch (jsonError) {
                console.error('Erro ao fazer parse do JSON:', jsonError);
                const textResponse = await response.text();
                console.error('Resposta recebida:', textResponse.substring(0, 500));
                
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
                
                alert('Erro ao processar resposta do servidor. Por favor, tente novamente.');
                return;
            }

            console.log('Dados recebidos:', data);

            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }

            // Verifica status HTTP antes de verificar data.success
            if (!response.ok) {
                const errorMessage = data.message || `Erro HTTP ${response.status}: ${response.statusText}`;
                alert(errorMessage);
                console.error('Erro HTTP:', response.status, data);
                return;
            }

            if (data.success) {
                if (!data.qr_code_string && !data.qr_code && !data.pix_key) {
                    alert('Erro: QR Code não foi gerado. Por favor, verifique as credenciais do gateway no painel administrativo.');
                    return;
                }

                console.log('QR Code gerado com sucesso!');

                if (window.Alpine) {
                    const modal = document.querySelector('[x-data*="qrCodeModal"]');
                    if (modal) {
                        const component = Alpine.$data(modal);
                        if (component && component.openModal) {
                            component.openModal({
                                qr_code: data.qr_code || '',
                                pix_key: data.qr_code_string || data.pix_key || '',
                                amount_gross: data.amount_gross || 0,
                                expires_in_seconds: data.expires_in_seconds || 300,
                                transaction_id: data.transaction_id || data.transaction?.uuid || null
                            });
                        } else {
                            console.error('Component Alpine não encontrado ou método openModal não existe');
                            alert('Erro ao abrir modal. Recarregue a página e tente novamente.');
                        }
                    } else {
                        console.error('Modal não encontrado no DOM');
                        alert('Erro ao abrir modal. Recarregue a página e tente novamente.');
                    }
                } else {
                    console.warn('Alpine.js não está carregado, aguardando...');
                    document.addEventListener('alpine:init', () => {
                        const modal = document.querySelector('[x-data*="qrCodeModal"]');
                        if (modal) {
                            const component = Alpine.$data(modal);
                            if (component && component.openModal) {
                                component.openModal({
                                    qr_code: data.qr_code || '',
                                    pix_key: data.qr_code_string || data.pix_key || '',
                                    amount_gross: data.amount_gross || 0,
                                    expires_in_seconds: data.expires_in_seconds || 300,
                                    transaction_id: data.transaction_id || data.transaction?.uuid || null
                                });
                            }
                        }
                    });
                }
            } else {
                const errorMessage = data.message || 'Erro desconhecido ao gerar QR Code';
                alert(errorMessage);
                console.error('Erro ao gerar QR Code:', data);
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
            
            const errorMessage = error.message || 'Erro ao gerar QR Code. Por favor, verifique sua conexão e tente novamente.';
            alert(errorMessage);
        }
    });

    // Função para calcular taxas de saque otimizada
    function updateWithdrawalCalculations(desiredNetAmount) {
        const cashoutFixo = {{ $cashoutFixo }};
        const cashoutPercentual = {{ $cashoutPercentual }};
        const cashoutMinima = {{ $cashoutPixMinima ?? 0.80 }};
        const availableBalance = {{ $availableBalance }};
        const value = parseFloat(desiredNetAmount) || 0;
        const minValue = {{ $withdrawalMinValue ?? 10.00 }};
        
        const calculationDiv = document.getElementById('withdrawalCalculation');
        
        if (value >= minValue) {
            if (calculationDiv) calculationDiv.style.display = 'block';
            
            // Calcula taxa percentual
            let amountGross = value;
            let fee = 0;
            let net = 0;
            const maxIterations = 10;
            const tolerance = 0.01;
            
            for (let i = 0; i < maxIterations; i++) {
                const feePercentual = (amountGross * cashoutPercentual) / 100;
                fee = Math.max(feePercentual, cashoutMinima) + cashoutFixo;
                net = amountGross - fee;
                
                if (Math.abs(net - value) <= tolerance) break;
                
                const difference = value - net;
                amountGross += difference;
            }
            
            const amountGrossRounded = Math.round(amountGross * 100) / 100;
            const feeRounded = Math.round(fee * 100) / 100;
            const netRounded = Math.round(net * 100) / 100;
            
            const hasEnoughBalance = availableBalance >= amountGrossRounded;
            
            const amountDisplay = document.getElementById('withdrawalAmountDisplay');
            const feeDisplay = document.getElementById('withdrawalFeeDisplay');
            const netDisplay = document.getElementById('withdrawalNetDisplay');
            const warningDisplay = document.getElementById('withdrawalWarning');
            
            if (amountDisplay) amountDisplay.textContent = 'R$ ' + amountGrossRounded.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (feeDisplay) {
                feeDisplay.textContent = 'R$ ' + feeRounded.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                feeDisplay.className = hasEnoughBalance ? 'font-semibold text-yellow-400' : 'font-semibold text-red-400';
            }
            if (netDisplay) {
                netDisplay.textContent = 'R$ ' + netRounded.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                netDisplay.className = hasEnoughBalance ? 'text-base font-bold text-[#00D9AC]' : 'text-base font-bold text-red-400';
            }
            
            if (warningDisplay) {
                if (!hasEnoughBalance) {
                    warningDisplay.innerHTML = `<div class="bg-red-500/10 border border-red-500/30 rounded-lg p-2.5 mt-2">
                        <p class="text-red-300 text-xs">
                            <strong>⚠️ Saldo insuficiente!</strong> Você precisa de R$ ${amountGrossRounded.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}.
                        </p>
                    </div>`;
                    warningDisplay.classList.remove('hidden');
                } else {
                    warningDisplay.classList.add('hidden');
                }
            }
        } else {
            if (calculationDiv) calculationDiv.style.display = 'none';
            
            const amountDisplay = document.getElementById('withdrawalAmountDisplay');
            const feeDisplay = document.getElementById('withdrawalFeeDisplay');
            const netDisplay = document.getElementById('withdrawalNetDisplay');
            const warningDisplay = document.getElementById('withdrawalWarning');
            
            if (amountDisplay) amountDisplay.textContent = 'R$ 0,00';
            if (feeDisplay) feeDisplay.textContent = 'R$ 0,00';
            if (netDisplay) netDisplay.textContent = 'R$ 0,00';
            if (warningDisplay) warningDisplay.classList.add('hidden');
        }
    }

    // Função Alpine.js para o modal de PIN
    function pinModal() {
        return {
            isOpen: false,
            isProcessing: false,
            errorMessage: '',
            pin: ['', '', '', '', '', ''],
            withdrawalForm: null,
            
            openModal(form) {
                this.withdrawalForm = form;
                this.isOpen = true;
                this.errorMessage = '';
                this.pin = ['', '', '', '', '', ''];
                this.isProcessing = false;
                
                // Foca no primeiro input após um pequeno delay
                this.$nextTick(() => {
                    if (this.$refs.pin0) {
                        this.$refs.pin0.focus();
                    }
                });
            },
            
            closeModal() {
                this.isOpen = false;
                this.errorMessage = '';
                this.pin = ['', '', '', '', '', ''];
                this.isProcessing = false;
                this.withdrawalForm = null;
            },
            
            handlePinInput(index, event) {
                const value = event.target.value.replace(/[^0-9]/g, '');
                if (value) {
                    this.pin[index] = value;
                    // Move para o próximo input
                    if (index < 5 && this.$refs['pin' + (index + 1)]) {
                        this.$refs['pin' + (index + 1)].focus();
                    }
                } else {
                    this.pin[index] = '';
                }
            },
            
            handlePinKeydown(index, event) {
                // Backspace: volta para o input anterior se estiver vazio
                if (event.key === 'Backspace' && !this.pin[index] && index > 0) {
                    if (this.$refs['pin' + (index - 1)]) {
                        this.$refs['pin' + (index - 1)].focus();
                    }
                }
                // Seta esquerda: move para o input anterior
                if (event.key === 'ArrowLeft' && index > 0) {
                    if (this.$refs['pin' + (index - 1)]) {
                        this.$refs['pin' + (index - 1)].focus();
                    }
                }
                // Seta direita: move para o próximo input
                if (event.key === 'ArrowRight' && index < 5) {
                    if (this.$refs['pin' + (index + 1)]) {
                        this.$refs['pin' + (index + 1)].focus();
                    }
                }
            },
            
            get isPinComplete() {
                return this.pin.every(digit => digit !== '') && this.pin.length === 6;
            },
            
            async confirmWithdrawal() {
                if (!this.isPinComplete) {
                    this.errorMessage = 'Por favor, digite o PIN completo de 6 dígitos';
                    return;
                }
                
                const pinValue = this.pin.join('');
                if (pinValue.length !== 6) {
                    this.errorMessage = 'O PIN deve ter exatamente 6 dígitos';
                    return;
                }
                
                this.isProcessing = true;
                this.errorMessage = '';
                
                try {
                    // Adiciona o PIN ao formulário
                    if (this.withdrawalForm) {
                        // Cria um input hidden para o PIN
                        let pinInput = this.withdrawalForm.querySelector('input[name="pin"]');
                        if (!pinInput) {
                            pinInput = document.createElement('input');
                            pinInput.type = 'hidden';
                            pinInput.name = 'pin';
                            this.withdrawalForm.appendChild(pinInput);
                        }
                        pinInput.value = pinValue;
                        
                        // Submete o formulário
                        this.withdrawalForm.submit();
                    } else {
                        this.errorMessage = 'Erro ao processar. Por favor, tente novamente.';
                        this.isProcessing = false;
                    }
                } catch (error) {
                    console.error('Erro ao confirmar saque:', error);
                    this.errorMessage = 'Erro ao processar saque. Por favor, tente novamente.';
                    this.isProcessing = false;
                }
            }
        }
    }

    // Função Alpine.js para o modal de QR Code
    function qrCodeModal() {
        return {
            isOpen: false,
            isExpired: false,
            qrCodeData: null,
            timer: 0,
            timerInterval: null,
            paymentCheckInterval: null,
            isPaymentConfirmed: false,
            
            openModal(data) {
                this.qrCodeData = data;
                this.isOpen = true;
                this.isExpired = false;
                this.isPaymentConfirmed = false;
                
                this.timer = data.expires_in_seconds || 300;
                this.startTimer();
                
                // Inicia verificação de pagamento se tiver transaction_id
                if (data.transaction_id) {
                    this.startPaymentCheck(data.transaction_id);
                }
                
                const container = document.getElementById('qrCodeContainerModal');
                if (container && data.qr_code) {
                    container.innerHTML = data.qr_code;
                }
            },
            
            startPaymentCheck(transactionId) {
                // Limpa intervalo anterior se existir
                if (this.paymentCheckInterval) {
                    clearInterval(this.paymentCheckInterval);
                }
                
                let checkCount = 0;
                const maxChecks = 300; // Máximo de 5 minutos
                
                this.paymentCheckInterval = setInterval(async () => {
                    if (checkCount >= maxChecks || this.isPaymentConfirmed) {
                        if (this.paymentCheckInterval) {
                            clearInterval(this.paymentCheckInterval);
                            this.paymentCheckInterval = null;
                        }
                        return;
                    }
                    
                    checkCount++;
                    
                    try {
                        const response = await fetch(`{{ url('/financial/transaction/check') }}/${transactionId}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            cache: 'no-cache',
                        });
                        
                        if (!response.ok) {
                            return;
                        }
                        
                        const data = await response.json();
                        
                        // Se o pagamento foi confirmado
                        if (data.completed && !this.isPaymentConfirmed) {
                            this.isPaymentConfirmed = true;
                            
                            if (this.paymentCheckInterval) {
                                clearInterval(this.paymentCheckInterval);
                                this.paymentCheckInterval = null;
                            }
                            
                            // Para o timer
                            if (this.timerInterval) {
                                clearInterval(this.timerInterval);
                                this.timerInterval = null;
                            }
                            
                            // Mostra mensagem de sucesso
                            this.showPaymentSuccessMessage(data.message || 'Pagamento realizado com sucesso!');
                            
                            // Redireciona após 2 segundos
                            setTimeout(() => {
                                if (data.redirect_url) {
                                    window.location.href = data.redirect_url;
                                } else {
                                    window.location.href = '{{ route("dashboard.financial.index") }}';
                                }
                            }, 2000);
                        }
                    } catch (error) {
                        console.error('Erro ao verificar status do pagamento:', error);
                    }
                }, 1000); // Verifica a cada 1 segundo
            },
            
            showPaymentSuccessMessage(message) {
                // Remove mensagens anteriores
                const existingMessage = document.getElementById('payment-success-message-modal');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Cria elemento de mensagem
                const messageDiv = document.createElement('div');
                messageDiv.id = 'payment-success-message-modal';
                messageDiv.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-[60] animate-slide-down';
                messageDiv.innerHTML = `
                    <div class="bg-gradient-to-r from-[#00D9AC] to-[#00B2FF] text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[300px] max-w-md">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-lg">${message}</p>
                            <p class="text-sm opacity-90">Redirecionando para o dashboard...</p>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(messageDiv);
                
                // Adiciona animação CSS se não existir
                if (!document.getElementById('payment-success-styles-financial')) {
                    const style = document.createElement('style');
                    style.id = 'payment-success-styles-financial';
                    style.textContent = `
                        @keyframes slide-down {
                            from {
                                transform: translateX(-50%) translateY(-100px);
                                opacity: 0;
                            }
                            to {
                                transform: translateX(-50%) translateY(0);
                                opacity: 1;
                            }
                        }
                        .animate-slide-down {
                            animation: slide-down 0.5s ease-out;
                        }
                    `;
                    document.head.appendChild(style);
                }
            },
            
            closeModal() {
                this.isOpen = false;
                this.isExpired = false;
                this.qrCodeData = null;
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                }
                if (this.paymentCheckInterval) {
                    clearInterval(this.paymentCheckInterval);
                    this.paymentCheckInterval = null;
                }
            },
            
            startTimer() {
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                }
                
                this.timerInterval = setInterval(() => {
                    if (this.timer > 0) {
                        this.timer--;
                    } else {
                        this.isExpired = true;
                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                            this.timerInterval = null;
                        }
                    }
                }, 1000);
            },
            
            formatTimer() {
                const minutes = Math.floor(this.timer / 60);
                const secs = this.timer % 60;
                return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            },
            
            formatMoney(value) {
                return parseFloat(value).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            },
            
            copyPixKey() {
                const input = document.getElementById('pixKeyInputModal');
                if (input && this.qrCodeData?.pix_key) {
                    input.select();
                    input.setSelectionRange(0, 99999);
                    
                    try {
                        document.execCommand('copy');
                        const button = event.target.closest('button');
                        if (button) {
                            const originalHTML = button.innerHTML;
                            button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                            button.classList.add('bg-[#00D9AC]');
                            button.classList.remove('bg-[#00B2FF]');
                            
                            setTimeout(() => {
                                button.innerHTML = originalHTML;
                                button.classList.remove('bg-[#00D9AC]');
                                button.classList.add('bg-[#00B2FF]');
                            }, 2000);
                        }
                    } catch (err) {
                        navigator.clipboard.writeText(this.qrCodeData.pix_key).then(() => {
                            const button = event.target.closest('button');
                            if (button) {
                                const originalHTML = button.innerHTML;
                                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                                button.classList.add('bg-[#00D9AC]');
                                button.classList.remove('bg-[#00B2FF]');
                                
                                setTimeout(() => {
                                    button.innerHTML = originalHTML;
                                    button.classList.remove('bg-[#00D9AC]');
                                    button.classList.add('bg-[#00B2FF]');
                                }, 2000);
                            }
                        }).catch(() => {
                            alert('Não foi possível copiar. Por favor, copie manualmente.');
                        });
                    }
                }
            }
        }
    }

    // Intercepta o submit do formulário de saque
    document.addEventListener('DOMContentLoaded', function() {
        const withdrawalForm = document.getElementById('withdrawalForm');
        const withdrawalSubmitBtn = document.getElementById('withdrawalSubmitBtn');
        
        if (withdrawalSubmitBtn && withdrawalForm) {
            withdrawalSubmitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Valida o formulário
                if (!withdrawalForm.checkValidity()) {
                    withdrawalForm.reportValidity();
                    return;
                }
                
                // Abre o modal de PIN
                if (window.Alpine) {
                    const modal = document.querySelector('[x-data*="pinModal"]');
                    if (modal) {
                        const component = Alpine.$data(modal);
                        if (component && component.openModal) {
                            component.openModal(withdrawalForm);
                        }
                    }
                }
            });
        }
        
        // Inicializa cálculos de saque
        const withdrawalInput = document.getElementById('withdrawal_amount');
        if (withdrawalInput) {
            withdrawalInput.addEventListener('input', function() {
                updateWithdrawalCalculations(this.value);
            });
        }
    });
</script>
@endsection
