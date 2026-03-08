@extends('layouts.app')

@section('title', 'Relatórios Financeiros')

@section('content')
<div class="space-y-4 sm:space-y-6 px-3 sm:px-0">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Relatórios Financeiros</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Visualize seus depósitos e saques</p>
    </div>

    <!-- Resumo Financeiro -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-slate-500 text-xs font-medium">Saldo Disponível</p>
            <p class="text-2xl font-bold text-slate-900 mt-2">R$ {{ number_format($walletBalance ?? 0, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-slate-500 text-xs font-medium">Total Depositado</p>
            <p class="text-2xl font-bold text-slate-900 mt-2">R$ {{ number_format($totalDeposited, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-slate-500 text-xs font-medium">Total Sacado</p>
            <p class="text-2xl font-bold text-slate-900 mt-2">R$ {{ number_format($totalWithdrawn, 2, ',', '.') }}</p>
        </div>
    </div>

    <!-- Depósitos, Vendas Checkout e Saques -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Depósitos -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-3 sm:p-5 border-b border-slate-200">
                <h2 class="text-base sm:text-lg font-semibold text-slate-900">Depósitos Recentes</h2>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 sticky top-0">
                        <tr>
                            <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Valor</th>
                            <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentDeposits as $deposit)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold text-blue-600">+ R$ {{ number_format($deposit->amount_gross, 2, ',', '.') }}</td>
                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-xs text-slate-500">{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-slate-500 text-xs">Nenhum depósito encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Vendas Checkout -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-3 sm:p-5 border-b border-slate-200">
                <h2 class="text-base sm:text-lg font-semibold text-slate-900">Vendas Checkout</h2>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 sticky top-0">
                        <tr>
                            <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Valor</th>
                            <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentCheckoutSales as $sale)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold text-green-600">+ R$ {{ number_format($sale->amount_gross, 2, ',', '.') }}</td>
                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-xs text-slate-500">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-slate-500 text-xs">Nenhuma venda encontrada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Saques -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-3 sm:p-5 border-b border-slate-200">
                <h2 class="text-base sm:text-lg font-semibold text-slate-900">Saques Recentes</h2>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 sticky top-0">
                        <tr>
                            <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Valor</th>
                            <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentWithdrawals as $withdrawal)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold text-red-600">- R$ {{ number_format($withdrawal->amount_gross, 2, ',', '.') }}</td>
                                <td class="px-3 sm:px-4 py-2 sm:py-3">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $withdrawal->status === 'approved' ? 'bg-blue-50 text-blue-700' : ($withdrawal->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') }}">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-xs text-slate-500">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-xs">Nenhum saque encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
