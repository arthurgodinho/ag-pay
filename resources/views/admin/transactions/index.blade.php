@extends('layouts.admin')

@section('title', 'Transações - Depósitos e Saques')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Transações</h1>
            <p class="text-slate-500 mt-1">Gerenciar depósitos e saques de todos os usuários</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-blue-50 border border-emerald-200 text-blue-600 px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl">
            {{ session('error') }}
        </div>
    @endif

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-sm">Total Depósitos</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total_deposits'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-sm">Total Saques</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total_withdrawals'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-sm">Depósitos Pendentes</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($stats['pending_deposits'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-sm">Saques Pendentes</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($stats['pending_withdrawals'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-sm">Total Processado</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">R$ {{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-2">Buscar</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="UUID, ID externo, nome ou email"
                    class="w-full px-4 py-2 bg-white border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-2">Tipo</label>
                <select name="type" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">Todos</option>
                    <option value="pix" {{ request('type') === 'pix' ? 'selected' : '' }}>PIX</option>
                    <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Cartão</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-2">Status Depósito</label>
                <select name="status" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="todos">Todos</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processando</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Pago</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Falhou</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    <option value="mediation" {{ request('status') === 'mediation' ? 'selected' : '' }}>Mediação</option>
                    <option value="chargeback" {{ request('status') === 'chargeback' ? 'selected' : '' }}>Chargeback</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-2">Status Saque</label>
                <select name="withdrawal_status" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="todos">Todos</option>
                    <option value="pending" {{ request('withdrawal_status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="processing" {{ request('withdrawal_status') === 'processing' ? 'selected' : '' }}>Processando</option>
                    <option value="paid" {{ request('withdrawal_status') === 'paid' ? 'selected' : '' }}>Pago</option>
                    <option value="completed" {{ request('withdrawal_status') === 'completed' ? 'selected' : '' }}>Completado</option>
                    <option value="failed" {{ request('withdrawal_status') === 'failed' ? 'selected' : '' }}>Falhou</option>
                    <option value="cancelled" {{ request('withdrawal_status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl transition-colors shadow-sm">
                    Filtrar
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="px-6 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-2xl transition-colors shadow-sm">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabs: Depósitos e Saques -->
    <div x-data="{ activeTab: 'deposits' }" class="space-y-6">
        <div class="border-b border-slate-200 flex gap-4">
            <button @click="activeTab = 'deposits'" :class="activeTab === 'deposits' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-3 font-medium transition-colors">
                Depósitos ({{ $transactions->total() }})
            </button>
            <button @click="activeTab = 'withdrawals'" :class="activeTab === 'withdrawals' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-3 font-medium transition-colors">
                Saques ({{ $withdrawals->total() }})
            </button>
        </div>

        <!-- Tab: Depósitos -->
        <div x-show="activeTab === 'deposits'" class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Valor Bruto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Taxa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Valor Líquido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm text-slate-900 font-mono">#{{ $transaction->id }}</p>
                                    <p class="text-xs text-slate-500">{{ substr($transaction->uuid, 0, 8) }}...</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-900">{{ $transaction->user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $transaction->user->email }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type === 'pix' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                        {{ strtoupper($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-900">
                                    R$ {{ number_format($transaction->amount_gross, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                    R$ {{ number_format($transaction->fee ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-blue-600 font-semibold">
                                    R$ {{ number_format($transaction->amount_net, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($transaction->status === 'completed')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700">Pago</span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-50 text-yellow-700">Pendente</span>
                                    @elseif($transaction->status === 'processing')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700">Processando</span>
                                    @elseif($transaction->status === 'failed')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-50 text-red-700">Falhou</span>
                                    @elseif($transaction->status === 'cancelled')
                                        <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-500">Cancelado</span>
                                    @elseif($transaction->status === 'mediation')
                                        <span class="px-2 py-1 text-xs rounded-full bg-orange-50 text-orange-700">Mediação</span>
                                    @elseif($transaction->status === 'chargeback')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Chargeback</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-500">{{ $transaction->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $transaction->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        @if($transaction->status === 'pending')
                                            <form action="{{ route('admin.transactions.process', $transaction->id) }}" method="POST" onsubmit="return confirm('Deseja processar manualmente esta transação pendente? O saldo será creditado na wallet do usuário.');">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-500 font-medium text-xs" title="Processar manualmente">
                                                    Processar
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.transactions.edit', $transaction->id) }}" class="text-blue-600 hover:text-blue-500 font-medium">
                                            Editar
                                        </a>
                                        <form action="{{ route('admin.transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta transação?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-500 font-medium">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-slate-500">
                                    Nenhuma transação encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        <!-- Tab: Saques -->
        <div x-show="activeTab === 'withdrawals'" class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Valor Bruto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Taxa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Valor Líquido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Chave PIX</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($withdrawals as $withdrawal)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm text-slate-900 font-mono">#{{ $withdrawal->id }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-900">{{ $withdrawal->user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $withdrawal->user->email }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-900">
                                    R$ {{ number_format($withdrawal->amount_gross ?? $withdrawal->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                    R$ {{ number_format($withdrawal->fee ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-600 font-semibold">
                                    R$ {{ number_format($withdrawal->amount ?? ($withdrawal->amount_gross - ($withdrawal->fee ?? 0)), 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-500 font-mono">{{ substr($withdrawal->pix_key ?? 'N/A', 0, 20) }}...</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($withdrawal->status === 'paid' || $withdrawal->status === 'completed')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700">Pago</span>
                                    @elseif($withdrawal->status === 'pending')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-50 text-yellow-700">Pendente</span>
                                    @elseif($withdrawal->status === 'processing')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700">Processando</span>
                                    @elseif($withdrawal->status === 'failed')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-50 text-red-700">Falhou</span>
                                    @elseif($withdrawal->status === 'cancelled')
                                        <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-500">Cancelado</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-500">{{ $withdrawal->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.transactions.edit-withdrawal', $withdrawal->id) }}" class="text-blue-600 hover:text-blue-500 font-medium">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.transactions.destroy-withdrawal', $withdrawal->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este saque?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-500 font-medium">
                                            Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-slate-500">
                                    Nenhum saque encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($withdrawals->hasPages())
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
