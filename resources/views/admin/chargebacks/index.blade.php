@extends('layouts.admin')

@section('title', 'Chargebacks / MED')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Chargebacks / MED
        </h1>
        <p class="text-slate-500 mt-1">Gerencie os chargebacks e MED recebidos do adquirente</p>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-2xl bg-blue-50 border border-emerald-200 text-blue-600">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-2xl bg-red-50 border border-red-200 text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.chargebacks.index') }}" class="flex gap-4">
            <select name="status" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos os Status</option>
                <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Pendente</option>
                <option value="approved" {{ ($statusFilter ?? '') === 'approved' ? 'selected' : '' }}>MED EFETUADO</option>
                <option value="cancelled" {{ ($statusFilter ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
            </select>
            <button type="submit" class="px-6 py-2 rounded-2xl text-white font-semibold bg-blue-600 hover:bg-blue-700 transition-colors">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Tabela de Chargebacks -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-500">ID</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-500">Usuário</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-500">Transação</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-500">Valor</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-500">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-500">Ações</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-500">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-600">
                                #{{ $item['type'] === 'transaction' ? $item['transaction']->id : $item['chargeback']->id }}
                                @if($item['type'] === 'transaction')
                                    <span class="ml-2 text-xs text-slate-400">(Transação)</span>
                                @else
                                    <span class="ml-2 text-xs text-slate-400">(Chargeback)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-800">
                                <div>
                                    <div class="font-semibold">{{ $item['user']->name }}</div>
                                    <div class="text-slate-500 text-xs">{{ $item['user']->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-800">
                                <div>
                                    <div class="font-semibold">#{{ $item['transaction']->id }}</div>
                                    <div class="text-slate-500 text-xs">{{ $item['transaction']->external_id ?? 'N/A' }}</div>
                                    @if($item['type'] === 'transaction')
                                        <div class="text-slate-500 text-xs mt-1">
                                            Status: {{ $item['transaction']->status === 'mediation' ? 'Mediação' : 'Chargeback' }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-800 font-semibold">
                                R$ {{ number_format($item['amount'], 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item['status'] === 'pending')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-200">
                                        Pendente
                                    </span>
                                @elseif($item['status'] === 'approved')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-emerald-200">
                                        MED EFETUADO
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-200">
                                        Cancelado
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @if($item['status'] === 'pending')
                                        @if($item['type'] === 'transaction')
                                            <!-- Ações para transação direta -->
                                            <form method="POST" action="{{ route('admin.chargebacks.approve-transaction', $item['transaction']->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-2xl transition-colors">
                                                    Aprovar Transação
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.chargebacks.approve-med', 't_' . $item['transaction']->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 text-xs bg-orange-600 hover:bg-orange-700 text-white rounded-2xl transition-colors">
                                                    Aprovar MED
                                                </button>
                                            </form>
                                        @else
                                            <!-- Ações para chargeback da tabela -->
                                            <form method="POST" action="{{ route('admin.chargebacks.approve-med', 'c_' . $item['chargeback']->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 text-xs bg-orange-600 hover:bg-orange-700 text-white rounded-2xl transition-colors">
                                                    Aprovar MED
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.chargebacks.cancel', $item['chargeback']->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-2xl transition-colors">
                                                    Cancelar MED
                                                </button>
                                            </form>
                                            @if(!($item['chargeback']->withdrawal_blocked ?? false))
                                                <form method="POST" action="{{ route('admin.chargebacks.block-withdrawal', $item['chargeback']->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 text-xs bg-amber-600 hover:bg-amber-700 text-white rounded-2xl transition-colors">
                                                        Bloquear Saque
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.chargebacks.unblock-withdrawal', $item['chargeback']->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-2xl transition-colors">
                                                        Desbloquear Saque
                                                    </button>
                                                </form>
                                            @endif
                                            @if(!($item['chargeback']->balance_debited ?? false) && !($item['chargeback']->account_negativated ?? false))
                                                <form method="POST" action="{{ route('admin.chargebacks.debit-balance', $item['chargeback']->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 text-xs bg-orange-600 hover:bg-orange-700 text-white rounded-2xl transition-colors">
                                                        Debitar Saldo
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                                @if(isset($item['chargeback']) && ($item['chargeback']->balance_debited || $item['chargeback']->account_negativated))
                                    <div class="mt-2 text-xs text-slate-500">
                                        @if($item['chargeback']->balance_debited)
                                            <span class="text-blue-600">✓ Saldo debitado</span>
                                        @endif
                                        @if($item['chargeback']->account_negativated)
                                            <span class="text-red-600">⚠ Conta negativada: R$ {{ number_format($item['chargeback']->negative_balance, 2, ',', '.') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $item['created_at']->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                Nenhum chargeback encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
