@extends('layouts.admin')

@section('title', 'Adquirentes')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    use App\Models\Setting;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div>
        <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
            <div class="p-2 bg-blue-50 rounded-xl">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <span>Adquirentes</span>
        </h1>
        <p class="text-slate-500 mt-2 md:ml-14">Configure as credenciais e preferências dos adquirentes de pagamento.</p>
    </div>

    <!-- Layout Principal -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6" x-data="{ activeTab: '{{ array_key_first($providers) }}' }">
        <!-- Menu Lateral (Abas) -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <nav class="flex flex-col" role="tablist">
                    @foreach($providers as $key => $name)
                        <button
                            @click="activeTab = '{{ $key }}'"
                            :class="activeTab === '{{ $key }}' ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800'"
                            class="w-full text-left px-4 py-3 text-sm font-semibold transition-colors focus:outline-none"
                            role="tab"
                        >
                            {{ $name }}
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="md:col-span-3">
            <form method="POST" action="{{ route('admin.gateways.update') }}" class="space-y-6">
                @csrf
                
                @foreach($providers as $key => $name)
                    @php
                        $config = $configs->get($key) ?? null;
                        $gatewayLabels = [
                            'bspay' => ['credential1' => ['label' => 'Client ID', 'placeholder' => 'Digite o Client ID'], 'credential2' => ['label' => 'Client Secret', 'placeholder' => 'Digite o Client Secret']],
                            'venit' => ['credential1' => ['label' => 'Secret Key', 'placeholder' => 'Digite a Secret Key (username)'], 'credential2' => ['label' => 'Company ID', 'placeholder' => 'Digite o Company ID (password)']],
                            'podpay' => ['credential1' => ['label' => 'Public Key', 'placeholder' => 'Digite a Public Key'], 'credential2' => ['label' => 'Secret Key', 'placeholder' => 'Digite a Secret Key']],
                            'hypercash' => ['credential1' => ['label' => 'API Token', 'placeholder' => 'Digite o Token da API']],
                            'efi' => ['credential1' => ['label' => 'Client ID', 'placeholder' => 'Digite o Client ID'], 'credential2' => ['label' => 'Client Secret', 'placeholder' => 'Digite o Client Secret']],
                            'paguemax' => ['credential1' => ['label' => 'Client ID', 'placeholder' => 'Digite o Client ID'], 'credential2' => ['label' => 'Token Secret', 'placeholder' => 'Digite o Token Secret']],
                            'zoompag' => ['credential1' => ['label' => 'Ignorar (Client ID)', 'placeholder' => 'Pode deixar em branco'], 'credential2' => ['label' => 'Chave Secreta (API Key)', 'placeholder' => 'Digite a Chave Secreta']],
                            'pluggou' => ['credential1' => ['label' => 'Public Key', 'placeholder' => 'Digite a Public Key'], 'credential2' => ['label' => 'Secret Key', 'placeholder' => 'Digite a Secret Key']],
                        ];
                        $labels = $gatewayLabels[$key] ?? ['credential1' => ['label' => 'Client ID', 'placeholder' => 'Digite o Client ID'], 'credential2' => ['label' => 'Client Secret', 'placeholder' => 'Digite o Client Secret']];
                    @endphp
                    
                    <div x-show="activeTab === '{{ $key }}'" x-transition.opacity class="space-y-6">
                        <!-- Card de Credenciais -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                            <h3 class="text-lg font-bold text-slate-900 mb-4">{{ $name }} - Credenciais</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if(isset($labels['credential1']))
                                <div class="{{ $key === 'zoompag' ? 'hidden' : '' }}">
                                    <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">{{ $labels['credential1']['label'] }}</label>
                                    <input type="text" name="gateways[{{ $key }}][client_id]" value="{{ $config->client_id ?? '' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="{{ $labels['credential1']['placeholder'] }}">
                                </div>
                                @endif
                                @if(isset($labels['credential2']))
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">{{ $labels['credential2']['label'] }}</label>
                                    <input type="password" name="gateways[{{ $key }}][client_secret]" value="{{ $config->client_secret ?? '' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="{{ $labels['credential2']['placeholder'] }}">
                                </div>
                                @endif
                            </div>
                            @if($key === 'efi')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">Chave PIX</label>
                                    <input type="text" name="gateways[{{ $key }}][pix_key]" value="{{ $config->pix_key ?? '' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Digite a Chave PIX">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">Caminho do Certificado (.p12)</label>
                                    <input type="text" name="gateways[{{ $key }}][certificate_path]" value="{{ $config->certificate_path ?? '' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: /caminho/certificado.p12">
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Card de Ativação -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                            <h3 class="text-lg font-bold text-slate-900 mb-4">Ativação do Gateway</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                                    <div>
                                        <label class="text-sm font-bold text-slate-900">Ativo para PIX</label>
                                        <p class="text-xs text-slate-500 font-medium">Permitir pagamentos via PIX</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="gateways[{{ $key }}][is_active_for_pix]" value="1" {{ ($config->is_active_for_pix ?? false) ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                @if(in_array($key, ['hypercash', 'paguemax']))
                                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                                    <div>
                                        <label class="text-sm font-bold text-slate-900">Ativo para Cartão</label>
                                        <p class="text-xs text-slate-500 font-medium">Permitir pagamentos via Cartão</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="gateways[{{ $key }}][is_active_for_card]" value="1" {{ ($config->is_active_for_card ?? false) ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" name="gateways[{{ $key }}][provider_name]" value="{{ $key }}">
                    </div>
                @endforeach

                <!-- Card de Configurações Globais -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900">Configurações Globais</h3>
                    
                    <!-- Gateways Padrão -->
                    <div class="space-y-4">
                        <h4 class="text-md font-bold text-slate-800">Gateways Padrão por Operação</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">Recebimento PIX</label>
                                <select name="default_gateway_for_pix" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Nenhum (preferência do usuário)</option>
                                    @foreach($providers as $key => $name)
                                        @if($configs->get($key)->is_active_for_pix ?? false)
                                        <option value="{{ $key }}" {{ (Setting::get('default_gateway_for_pix') === $key) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">Saque PIX</label>
                                <select name="default_gateway_for_withdrawals" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Nenhum (preferência do usuário)</option>
                                    @foreach($providers as $key => $name)
                                        @if($configs->get($key)->is_active_for_pix ?? false)
                                        <option value="{{ $key }}" {{ (Setting::get('default_gateway_for_withdrawals') === $key) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">Checkout PIX</label>
                                <select name="default_gateway_for_checkout_pix" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Nenhum (usar padrão de recebimento)</option>
                                    @foreach($providers as $key => $name)
                                        @if($configs->get($key)->is_active_for_pix ?? false)
                                        <option value="{{ $key }}" {{ (Setting::get('default_gateway_for_checkout_pix') === $key) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">Checkout Cartão</label>
                                <select name="default_gateway_for_checkout_card" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Nenhum (indisponível)</option>
                                    @foreach($providers as $key => $name)
                                        @if($configs->get($key)->is_active_for_card ?? false)
                                        <option value="{{ $key }}" {{ (Setting::get('default_gateway_for_checkout_card') === $key) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-200">

                    <!-- Taxas de Venda -->
                    <div class="space-y-4">
                        <h4 class="text-md font-bold text-slate-800">Taxas de Venda (Cartão de Crédito)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">Taxa Percentual (%)</label>
                                <div class="relative">
                                    <input type="number" step="0.01" min="0" name="credit_card_transaction_fee_percent" value="{{ Setting::get('credit_card_transaction_fee_percent', 0) }}" class="w-full pl-3 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 font-bold">%</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">Taxa Fixa (R$)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 font-bold">R$</span>
                                    <input type="number" step="0.01" min="0" name="credit_card_transaction_fee_fixed" value="{{ Setting::get('credit_card_transaction_fee_fixed', 0) }}" class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-200">

                    <!-- Modo de Saque -->
                    <div class="space-y-2">
                        <h4 class="text-md font-bold text-slate-800">Modo de Saque Global</h4>
                        <select name="withdrawal_mode" class="w-full md:w-1/2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="manual" {{ Setting::get('withdrawal_mode', 'manual') === 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="auto" {{ Setting::get('withdrawal_mode', 'manual') === 'auto' ? 'selected' : '' }}>Automático</option>
                        </select>
                        <p class="text-xs text-slate-500 font-medium">Define como os saques serão processados: manualmente pelo admin ou automaticamente pelo adquirente.</p>
                    </div>
                </div>

                <!-- Botão Salvar -->
                <div class="flex justify-end">
                    <button type="submit" id="btn-save-gateways" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors flex items-center gap-2 shadow-lg hover:shadow-blue-500/30 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Salvar Configurações</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="gateways"]');
    const btnSave = document.getElementById('btn-save-gateways');
    
    if (form && btnSave) {
        form.addEventListener('submit', function(e) {
            btnSave.disabled = true;
            btnSave.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Salvando...</span>`;
        });
    }
});
</script>
@endpush
