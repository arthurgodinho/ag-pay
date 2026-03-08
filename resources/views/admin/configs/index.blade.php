@extends('layouts.admin')

@section('title', 'Ajustes Gerais')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Ajustes Gerais
            </h1>
            <p class="text-sm text-slate-500 mt-1">Configure taxas, limites, contato do gerente e personalizações.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.configs.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Tabs Navigation -->
        <div class="border-b border-slate-200">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button type="button" onclick="switchTab('geral')" id="tab-geral" class="tab-btn border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
                    Geral
                </button>
                <button type="button" onclick="switchTab('manager')" id="tab-manager" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700">
                    Contato do Gerente
                </button>
                <button type="button" onclick="switchTab('cashin')" id="tab-cashin" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700">
                    Taxas Cash-In
                </button>
                <button type="button" onclick="switchTab('cashout')" id="tab-cashout" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700">
                    Taxas Cash-Out
                </button>
                <button type="button" onclick="switchTab('limits')" id="tab-limits" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700">
                    Limites
                </button>
                <button type="button" onclick="switchTab('affiliates')" id="tab-affiliates" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700">
                    Taxas de Afiliado
                </button>
                <button type="button" onclick="switchTab('branding')" id="tab-branding" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700">
                    Personalização
                </button>
            </nav>
        </div>

        <!-- Tab Contents -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            
            <!-- Geral Tab -->
            <div id="content-geral" class="tab-content space-y-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Configurações Gerais</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="gateway_name" class="block text-sm font-medium text-slate-600 mb-1">Nome do Sistema</label>
                        <input type="text" id="gateway_name" name="gateway_name" value="{{ old('gateway_name', $gateway_name ?? 'PagueMax') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="default_language" class="block text-sm font-medium text-slate-600 mb-1">Idioma Padrão</label>
                        <select id="default_language" name="default_language" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pt" {{ (old('default_language', $default_language ?? 'pt') == 'pt') ? 'selected' : '' }}>Português</option>
                            <option value="en" {{ (old('default_language', $default_language ?? 'pt') == 'en') ? 'selected' : '' }}>Inglês</option>
                            <option value="es" {{ (old('default_language', $default_language ?? 'pt') == 'es') ? 'selected' : '' }}>Espanhol</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <input type="checkbox" id="kyc_facial_biometrics_enabled" name="kyc_facial_biometrics_enabled" value="1" {{ (old('kyc_facial_biometrics_enabled', $kyc_facial_biometrics_enabled) ? 'checked' : '') }} class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <label for="kyc_facial_biometrics_enabled" class="text-sm font-medium text-slate-700">Habilitar Biometria Facial (KYC)</label>
                </div>
            </div>

            <!-- Manager Contact Tab -->
            <div id="content-manager" class="tab-content hidden space-y-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Contato do Gerente</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <label for="default_manager_name" class="block text-sm font-medium text-slate-600 mb-1">Nome do Gerente</label>
                            <input type="text" id="default_manager_name" name="default_manager_name" value="{{ old('default_manager_name', $default_manager_name ?? '') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="default_manager_email" class="block text-sm font-medium text-slate-600 mb-1">Email de Contato</label>
                            <input type="email" id="default_manager_email" name="default_manager_email" value="{{ old('default_manager_email', $default_manager_email ?? '') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="default_whatsapp" class="block text-sm font-medium text-slate-600 mb-1">WhatsApp / Telefone</label>
                            <input type="text" id="default_whatsapp" name="default_whatsapp" value="{{ old('default_whatsapp', $default_whatsapp ?? '') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-slate-500 mt-1">Inclua o código do país e DDD (ex: 5511999999999)</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Foto de Perfil</label>
                        <div class="flex items-start gap-4">
                            <div class="w-24 h-24 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                                @if(!empty($default_manager_photo))
                                    <img src="{{ Storage::url($default_manager_photo) }}" alt="Gerente" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="default_manager_photo" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                                <p class="text-xs text-slate-500 mt-2">Recomendado: 500x500px. Formatos: JPG, PNG.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash-In Tab -->
            <div id="content-cashin" class="tab-content hidden space-y-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Taxas de Entrada (Cash-In)</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="cashin_pix_fixo" class="block text-sm font-medium text-slate-600 mb-1">Taxa Fixa PIX (R$)</label>
                        <input type="number" id="cashin_pix_fixo" name="cashin_pix_fixo" value="{{ old('cashin_pix_fixo', $cashin_pix_fixo ?? '1.00') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="cashin_pix_percentual" class="block text-sm font-medium text-slate-600 mb-1">Taxa Percentual PIX (%)</label>
                        <input type="number" id="cashin_pix_percentual" name="cashin_pix_percentual" value="{{ old('cashin_pix_percentual', $cashin_pix_percentual ?? '3.00') }}" step="0.01" min="0" max="100" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="cashin_pix_minima" class="block text-sm font-medium text-slate-600 mb-1">Taxa Mínima PIX (R$)</label>
                        <input type="number" id="cashin_pix_minima" name="cashin_pix_minima" value="{{ old('cashin_pix_minima', $cashin_pix_minima ?? '0.00') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="deposit_min_value" class="block text-sm font-medium text-slate-600 mb-1">Depósito Mínimo (R$)</label>
                        <input type="number" id="deposit_min_value" name="deposit_min_value" value="{{ old('deposit_min_value', $deposit_min_value ?? '10.00') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Cash-Out Tab -->
            <div id="content-cashout" class="tab-content hidden space-y-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Taxas de Saque (Cash-Out)</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="cashout_pix_percentual" class="block text-sm font-medium text-slate-600 mb-1">Percentual PIX (%)</label>
                        <input type="number" id="cashout_pix_percentual" name="cashout_pix_percentual" value="{{ old('cashout_pix_percentual', $cashout_pix_percentual ?? '2.00') }}" step="0.01" min="0" max="100" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="cashout_pix_minima" class="block text-sm font-medium text-slate-600 mb-1">Mínima PIX (R$)</label>
                        <input type="number" id="cashout_pix_minima" name="cashout_pix_minima" value="{{ old('cashout_pix_minima', $cashout_pix_minima ?? '0.80') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="cashout_pix_fixo" class="block text-sm font-medium text-slate-600 mb-1">Fixa PIX (R$)</label>
                        <input type="number" id="cashout_pix_fixo" name="cashout_pix_fixo" value="{{ old('cashout_pix_fixo', $cashout_pix_fixo ?? '1.00') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="cashout_api_percentual" class="block text-sm font-medium text-slate-600 mb-1">Taxa API (%)</label>
                        <input type="number" id="cashout_api_percentual" name="cashout_api_percentual" value="{{ old('cashout_api_percentual', $cashout_api_percentual ?? '5.00') }}" step="0.01" min="0" max="100" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="withdrawal_min_value" class="block text-sm font-medium text-slate-600 mb-1">Saque Mínimo (R$)</label>
                        <input type="number" id="withdrawal_min_value" name="withdrawal_min_value" value="{{ old('withdrawal_min_value', $withdrawal_min_value ?? '10.00') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Limits Tab -->
            <div id="content-limits" class="tab-content hidden space-y-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Limites Operacionais</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- PF -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-700 mb-3 border-b border-slate-200 pb-2">Pessoa Física (CPF)</h3>
                        <div class="space-y-3">
                            <div>
                                <label for="limit_pf_daily" class="block text-xs font-medium text-slate-600 mb-1">Limite Diário (R$)</label>
                                <input type="number" id="limit_pf_daily" name="limit_pf_daily" value="{{ old('limit_pf_daily', $limit_pf_daily ?? '10000.00') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="limit_pf_withdrawal" class="block text-xs font-medium text-slate-600 mb-1">Limite por Saque (R$)</label>
                                <input type="number" id="limit_pf_withdrawal" name="limit_pf_withdrawal" value="{{ old('limit_pf_withdrawal', $limit_pf_withdrawal ?? '10000.00') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="withdrawals_per_day_pf" class="block text-xs font-medium text-slate-600 mb-1">Saques por Dia</label>
                                <input type="number" id="withdrawals_per_day_pf" name="withdrawals_per_day_pf" value="{{ old('withdrawals_per_day_pf', $withdrawals_per_day_pf ?? '3') }}" min="1" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- PJ -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-700 mb-3 border-b border-slate-200 pb-2">Pessoa Jurídica (CNPJ)</h3>
                        <div class="space-y-3">
                            <div>
                                <label for="limit_pj_daily" class="block text-xs font-medium text-slate-600 mb-1">Limite Diário (R$)</label>
                                <input type="number" id="limit_pj_daily" name="limit_pj_daily" value="{{ old('limit_pj_daily', $limit_pj_daily ?? '50000.00') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="limit_pj_withdrawal" class="block text-xs font-medium text-slate-600 mb-1">Limite por Saque (R$)</label>
                                <input type="number" id="limit_pj_withdrawal" name="limit_pj_withdrawal" value="{{ old('limit_pj_withdrawal', $limit_pj_withdrawal ?? '50000.00') }}" step="0.01" min="0" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="withdrawals_per_day_pj" class="block text-xs font-medium text-slate-600 mb-1">Saques por Dia</label>
                                <input type="number" id="withdrawals_per_day_pj" name="withdrawals_per_day_pj" value="{{ old('withdrawals_per_day_pj', $withdrawals_per_day_pj ?? '3') }}" min="1" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Affiliate Tab -->
            <div id="content-affiliates" class="tab-content hidden space-y-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Taxas de Afiliado</h2>
                
                <div class="p-6 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="affiliate_commission_type" class="block text-sm font-medium text-slate-600 mb-1">Tipo de Comissão</label>
                            <select id="affiliate_commission_type" name="affiliate_commission_type" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="percentage" {{ (old('affiliate_commission_type', $affiliate_commission_type ?? 'percentage') == 'percentage') ? 'selected' : '' }}>Porcentagem (%)</option>
                                <option value="fixed" {{ (old('affiliate_commission_type', $affiliate_commission_type ?? 'percentage') == 'fixed') ? 'selected' : '' }}>Valor Fixo (R$)</option>
                            </select>
                        </div>
                        <div>
                            <label for="affiliate_commission_percentage" class="block text-sm font-medium text-slate-600 mb-1">Comissão Percentual (%)</label>
                            <input type="number" id="affiliate_commission_percentage" name="affiliate_commission_percentage" value="{{ old('affiliate_commission_percentage', $affiliate_commission_percentage ?? '5.00') }}" step="0.01" min="0" max="100" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="affiliate_commission_fixed" class="block text-sm font-medium text-slate-600 mb-1">Comissão Fixa (R$)</label>
                            <input type="number" id="affiliate_commission_fixed" name="affiliate_commission_fixed" value="{{ old('affiliate_commission_fixed', $affiliate_commission_fixed ?? '0.00') }}" step="0.01" min="0" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branding Tab -->
            <div id="content-branding" class="tab-content hidden space-y-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Personalização Visual (Branding)</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Logos Section -->
                    <div class="space-y-6">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Identidade Visual
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-2">Logotipo Principal</label>
                                    <div class="flex items-center gap-4">
                                        <div class="w-32 h-16 bg-white border border-slate-200 rounded flex items-center justify-center p-2">
                                            @if($logo)
                                                <img src="{{ asset($logo) }}" alt="Logo" class="max-h-full max-w-full">
                                            @else
                                                <span class="text-[10px] text-slate-400">Sem logo</span>
                                            @endif
                                        </div>
                                        <input type="file" name="logo" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-2">Favicon</label>
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-white border border-slate-200 rounded flex items-center justify-center p-1">
                                            @if($favicon)
                                                <img src="{{ asset($favicon) }}" alt="Favicon" class="max-h-full max-w-full">
                                            @else
                                                <span class="text-[10px] text-slate-400">Sem icon</span>
                                            @endif
                                        </div>
                                        <input type="file" name="favicon" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Colors Section -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                            Esquema de Cores
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="theme_primary_color" class="block text-xs font-medium text-slate-600 mb-1">Cor Primária</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="theme_primary_color_picker" value="{{ $theme_primary_color }}" oninput="document.getElementById('theme_primary_color').value = this.value" class="w-8 h-8 rounded border border-slate-200 cursor-pointer">
                                    <input type="text" id="theme_primary_color" name="theme_primary_color" value="{{ old('theme_primary_color', $theme_primary_color) }}" class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs uppercase focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label for="theme_secondary_color" class="block text-xs font-medium text-slate-600 mb-1">Cor Secundária</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="theme_secondary_color_picker" value="{{ $theme_secondary_color }}" oninput="document.getElementById('theme_secondary_color').value = this.value" class="w-8 h-8 rounded border border-slate-200 cursor-pointer">
                                    <input type="text" id="theme_secondary_color" name="theme_secondary_color" value="{{ old('theme_secondary_color', $theme_secondary_color) }}" class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs uppercase focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label for="theme_accent_color" class="block text-xs font-medium text-slate-600 mb-1">Cor de Destaque</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="theme_accent_color_picker" value="{{ $theme_accent_color }}" oninput="document.getElementById('theme_accent_color').value = this.value" class="w-8 h-8 rounded border border-slate-200 cursor-pointer">
                                    <input type="text" id="theme_accent_color" name="theme_accent_color" value="{{ old('theme_accent_color', $theme_accent_color) }}" class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs uppercase focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label for="theme_text_color" class="block text-xs font-medium text-slate-600 mb-1">Cor do Texto</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="theme_text_color_picker" value="{{ $theme_text_color }}" oninput="document.getElementById('theme_text_color').value = this.value" class="w-8 h-8 rounded border border-slate-200 cursor-pointer">
                                    <input type="text" id="theme_text_color" name="theme_text_color" value="{{ old('theme_text_color', $theme_text_color) }}" class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs uppercase focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label for="theme_background_color" class="block text-xs font-medium text-slate-600 mb-1">Cor de Fundo</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="theme_background_color_picker" value="{{ $theme_background_color }}" oninput="document.getElementById('theme_background_color').value = this.value" class="w-8 h-8 rounded border border-slate-200 cursor-pointer">
                                    <input type="text" id="theme_background_color" name="theme_background_color" value="{{ old('theme_background_color', $theme_background_color) }}" class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs uppercase focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label for="theme_sidebar_bg" class="block text-xs font-medium text-slate-600 mb-1">Fundo Sidebar</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="theme_sidebar_bg_picker" value="{{ $theme_sidebar_bg }}" oninput="document.getElementById('theme_sidebar_bg').value = this.value" class="w-8 h-8 rounded border border-slate-200 cursor-pointer">
                                    <input type="text" id="theme_sidebar_bg" name="theme_sidebar_bg" value="{{ old('theme_sidebar_bg', $theme_sidebar_bg) }}" class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs uppercase focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-xl shadow-sm transition-colors text-sm">
                Salvar Configurações
            </button>
        </div>
    </form>
</div>

<script>
    function switchTab(tabId) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Show selected content
        document.getElementById('content-' + tabId).classList.remove('hidden');
        
        // Reset all buttons
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('border-blue-500', 'text-blue-600');
            el.classList.add('border-transparent', 'text-slate-500');
        });
        
        // Highlight selected button
        const btn = document.getElementById('tab-' + tabId);
        btn.classList.remove('border-transparent', 'text-slate-500');
        btn.classList.add('border-blue-500', 'text-blue-600');
    }
</script>
@endsection
