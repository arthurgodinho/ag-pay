@extends('layouts.app')

@section('title', 'Editar Split de Pagamento')

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
            <h1 class="text-3xl font-bold text-slate-900">Editar Split de Pagamento</h1>
            <p class="text-slate-500 mt-1">Atualize a configuração do split</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <form method="POST" action="{{ route('dashboard.split.update', $split->id) }}" x-data="{ splitType: '{{ $split->split_type }}' }">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Recebedor -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Recebedor <span class="text-red-500">*</span>
                    </label>
                    <select name="recipient_user_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors">
                        <option value="">Selecione um usuário</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $split->recipient_user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
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
                        value="{{ $split->split_value }}"
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
                        value="{{ $split->priority }}"
                        min="0"
                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                    >
                    <p class="mt-1 text-xs text-slate-500">
                        Splits com maior prioridade são processados primeiro (padrão: 0)
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center gap-3">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ $split->is_active ? 'checked' : '' }}
                            class="w-5 h-5 rounded bg-slate-50 border-slate-300 text-blue-600 focus:ring-blue-500"
                        >
                        <span class="text-slate-700">Split ativo</span>
                    </label>
                    <p class="mt-1 text-xs text-slate-500">
                        Splits inativos não serão processados automaticamente
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
                        value="{{ $split->description }}"
                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                        placeholder="Ex: Comissão do marketplace"
                    >
                </div>

                <!-- Botões -->
                <div class="flex items-center gap-4">
                    <button type="submit" class="px-6 py-3 rounded-lg text-white font-semibold transition-colors shadow-sm" style="background-color: {{ $themeColors['primary'] }};">
                        Salvar Alterações
                    </button>
                    <a href="{{ route('dashboard.split.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition-colors">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection









