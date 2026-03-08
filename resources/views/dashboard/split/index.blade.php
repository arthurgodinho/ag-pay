@extends('layouts.app')

@section('title', 'Split de Pagamento')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Split de Pagamento</h1>
            <p class="text-sm text-slate-500 mt-1">Divisão automática de valores entre recebedores.</p>
        </div>
        <a href="{{ route('dashboard.split.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Novo Split
        </a>
    </div>

    @if(session('success'))
        <div class="bg-blue-50 border border-emerald-200 text-blue-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm text-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($splits->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-8 sm:p-12 text-center shadow-sm">
            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Nenhum split configurado</h3>
            <p class="text-sm text-slate-500 mb-6 max-w-md mx-auto">Configure regras de divisão automática para distribuir seus recebimentos entre múltiplos recebedores.</p>
            <a href="{{ route('dashboard.split.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                Criar Primeiro Split
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Recebedor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Valor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Prioridade</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($splits as $split)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-slate-900">{{ $split->recipient->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $split->recipient->email }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $split->split_type === 'percentage' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-purple-50 text-purple-700 border border-purple-100' }}">
                                        {{ $split->split_type === 'percentage' ? 'Percentual' : 'Fixo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-semibold text-slate-700">
                                        @if($split->split_type === 'percentage')
                                            {{ number_format($split->split_value, 2, ',', '.') }}%
                                        @else
                                            R$ {{ number_format($split->split_value, 2, ',', '.') }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-slate-600">{{ $split->priority }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $split->is_active ? 'bg-blue-50 text-blue-700 border border-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $split->is_active ? 'bg-blue-500' : 'bg-slate-400' }} mr-1.5"></span>
                                        {{ $split->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('dashboard.split.edit', $split->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('dashboard.split.destroy', $split->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este split?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Excluir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Informações -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wide">Como funciona?</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 font-bold text-sm">1</span>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-800">Configuração Flexível</h4>
                    <p class="text-xs text-slate-500 mt-1">Defina splits por porcentagem (%) ou valor fixo (R$) para cada recebedor.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 font-bold text-sm">2</span>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-800">Processamento Automático</h4>
                    <p class="text-xs text-slate-500 mt-1">Os valores são divididos instantaneamente no momento do recebimento.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 font-bold text-sm">3</span>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-800">Prioridade de Execução</h4>
                    <p class="text-xs text-slate-500 mt-1">Controle a ordem dos descontos usando o campo de prioridade.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 font-bold text-sm">4</span>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-800">Saldo Remanescente</h4>
                    <p class="text-xs text-slate-500 mt-1">O valor restante após todos os splits permanece na sua conta principal.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
