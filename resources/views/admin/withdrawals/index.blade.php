@extends('layouts.admin')

@section('title', 'Gestão de Saques')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Gestão de Saques
            </h1>
            <p class="text-slate-500 mt-2">Aprove, rejeite ou pague automaticamente os saques</p>
        </div>
        
        <!-- Filtros -->
        <div class="flex gap-3">
            <a 
                href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}"
                class="px-6 py-2 rounded-2xl transition-colors font-medium text-sm {{ request('status') == 'pending' || !request('status') ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:border-blue-300' }}"
            >
                Pendentes
            </a>
            <a 
                href="{{ route('admin.withdrawals.index', ['status' => 'paid']) }}"
                class="px-6 py-2 rounded-2xl transition-colors font-medium text-sm {{ request('status') == 'paid' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200 hover:border-blue-300' }}"
            >
                Pagos
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="px-6 py-4 rounded-2xl bg-blue-50 border border-emerald-200 text-blue-600">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-6 py-4 rounded-2xl bg-red-50 border border-red-200 text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Usuário</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Valor</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Chave Pix</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($withdrawals as $withdrawal)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-600">#{{ $withdrawal->id }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900 font-medium">{{ $withdrawal->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900 font-semibold">
                                R$ {{ number_format($withdrawal->amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $withdrawal->pix_key }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full
                                    @if($withdrawal->status === 'paid')
                                        bg-blue-50 text-blue-600
                                    @elseif($withdrawal->status === 'cancelled')
                                        bg-slate-100 text-slate-500
                                    @else
                                        bg-yellow-50 text-yellow-600
                                    @endif">
                                    {{ ucfirst($withdrawal->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($withdrawal->status === 'pending')
                                    <div class="flex flex-wrap items-center gap-2">
                                        <!-- Pagar -->
                                        <form method="POST" action="{{ route('admin.withdrawals.pay', $withdrawal->id) }}" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-2xl transition-colors shadow-sm"
                                                onclick="return confirm('Confirmar pagamento deste saque?')"
                                            >
                                                Pagar
                                            </button>
                                        </form>
                                        
                                        <!-- Reembolsar -->
                                        <form method="POST" action="{{ route('admin.withdrawals.refund', $withdrawal->id) }}" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-2xl transition-colors shadow-sm"
                                                onclick="return confirm('Reembolsar este saque e estornar o valor para o usuário?')"
                                            >
                                                Reembolsar
                                            </button>
                                        </form>
                                        
                                        <!-- Cancelar -->
                                        <form method="POST" action="{{ route('admin.withdrawals.cancel', $withdrawal->id) }}" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-2xl transition-colors shadow-sm"
                                                onclick="return confirm('Cancelar este saque? O valor permanecerá bloqueado.')"
                                            >
                                                Cancelar
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-slate-500 text-lg">Nenhum saque encontrado</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
