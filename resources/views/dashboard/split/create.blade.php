@extends('layouts.app')

@section('title', 'Criar Split de Pagamento')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('dashboard.split.index') }}" class="text-blue-600 hover:text-blue-700 mb-2 inline-flex items-center font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Voltar
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Criar Split de Pagamento</h1>
            <p class="text-slate-500 mt-1">Configure a divisão automática de valores entre recebedores</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <form method="POST" action="{{ route('dashboard.split.store') }}" x-data="splitForm()">
            @csrf
            <input type="hidden" name="recipient_user_id" x-model="recipientUser?.id" required>

            <div class="space-y-6">
                <!-- Recebedor - Busca por Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Recebedor <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <div class="flex gap-2">
                            <input
                                type="email"
                                id="recipient_email"
                                x-model="recipientEmail"
                                placeholder="Digite o email do usuário"
                                class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                            >
                            <button
                                type="button"
                                @click="searchUser()"
                                :disabled="searching || !recipientEmail"
                                class="px-6 py-2 rounded-lg text-white font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                style="background-color: {{ $themeColors['primary'] }};"
                            >
                                <span x-show="!searching">Buscar</span>
                                <span x-show="searching">Buscando...</span>
                            </button>
                        </div>
                        
                        <!-- Resultado da busca -->
                        <div x-show="recipientUser" class="p-4 bg-blue-50 border border-green-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-slate-900 font-semibold" x-text="recipientUser?.name"></p>
                                    <p class="text-sm text-blue-700" x-text="recipientUser?.email"></p>
                                </div>
                                <button
                                    type="button"
                                    @click="recipientUser = null; recipientEmail = '';"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Erro na busca -->
                        <div x-show="searchError" class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-600 text-sm" x-text="searchError"></p>
                        </div>
                    </div>
                    @error('recipient_user_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Split -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Tipo de Split <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center p-4 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-100 transition-colors" :class="splitType === 'percentage' ? 'border-blue-500 bg-blue-50' : ''">
                            <input type="radio" name="split_type" value="percentage" x-model="splitType" class="sr-only">
                            <div class="flex-1">
                                <p class="text-slate-900 font-semibold">Percentual</p>
                                <p class="text-sm text-slate-500">% do valor recebido</p>
                            </div>
                        </label>
                        <label class="flex items-center p-4 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-100 transition-colors" :class="splitType === 'fixed' ? 'border-blue-500 bg-blue-50' : ''">
                            <input type="radio" name="split_type" value="fixed" x-model="splitType" class="sr-only">
                            <div class="flex-1">
                                <p class="text-slate-900 font-semibold">Valor Fixo</p>
                                <p class="text-sm text-slate-500">R$ fixo por transação</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Valor -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <span x-show="splitType === 'percentage'">Percentual (%)</span>
                        <span x-show="splitType === 'fixed'">Valor Fixo (R$)</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="split_value"
                        step="0.01"
                        min="0.01"
                        :max="splitType === 'percentage' ? '100' : ''"
                        required
                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                        placeholder="0.00"
                    >
                    <p class="mt-1 text-xs text-slate-500" x-show="splitType === 'percentage'">
                        Informe um valor entre 0.01% e 100%
                    </p>
                    <p class="mt-1 text-xs text-slate-500" x-show="splitType === 'fixed'">
                        Valor fixo que será transferido por transação
                    </p>
                    @error('split_value')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prioridade -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Prioridade
                    </label>
                    <input
                        type="number"
                        name="priority"
                        value="0"
                        min="0"
                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                    >
                    <p class="mt-1 text-xs text-slate-500">
                        Splits com maior prioridade são processados primeiro (padrão: 0)
                    </p>
                </div>

                <!-- Descrição -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Descrição (opcional)
                    </label>
                    <input
                        type="text"
                        name="description"
                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                        placeholder="Ex: Comissão do marketplace"
                    >
                </div>

                <!-- Botões -->
                <div class="flex items-center gap-4">
                    <button 
                        type="submit" 
                        :disabled="!recipientUser"
                        class="px-6 py-3 rounded-lg text-white font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        style="background-color: {{ $themeColors['primary'] }};"
                    >
                        Criar Split
                    </button>
                    <a href="{{ route('dashboard.split.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition-colors">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function splitForm() {
        return {
            splitType: 'percentage',
            recipientEmail: '',
            recipientUser: null,
            searching: false,
            searchError: null,
            
            async searchUser() {
                if (!this.recipientEmail) {
                    this.searchError = 'Por favor, informe um email.';
                    return;
                }

                this.searching = true;
                this.searchError = null;
                this.recipientUser = null;

                try {
                    const response = await fetch('{{ route("dashboard.split.search") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ email: this.recipientEmail })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.recipientUser = data.user;
                        this.searchError = null;
                    } else {
                        this.recipientUser = null;
                        this.searchError = data.message || 'Usuário não encontrado ou não aprovado.';
                    }
                } catch (error) {
                    this.recipientUser = null;
                    this.searchError = 'Erro ao buscar usuário. Tente novamente.';
                } finally {
                    this.searching = false;
                }
            }
        }
    }
</script>
@endsection
