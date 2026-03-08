@extends('layouts.admin')

@section('title', 'Resumo Financeiro')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Resumo Financeiro</h1>
        <p class="text-slate-500 mt-1 font-medium">Visão geral das finanças do sistema</p>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Receita Líquida -->
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm mb-1 font-medium">Receita Líquida</p>
                    <p class="text-3xl font-bold text-blue-600">R$ {{ number_format($totalProfit, 2, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Saldo em Custódia -->
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm mb-1 font-medium">Saldo em Custódia</p>
                    <p class="text-3xl font-bold text-blue-600">R$ {{ number_format($totalUserBalance, 2, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total de Depósitos -->
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm mb-1 font-medium">Total de Depósitos</p>
                    <p class="text-3xl font-bold text-blue-600">R$ {{ number_format($totalDeposits, 2, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Saques Pendentes -->
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm mb-1 font-medium">Saques Pendentes</p>
                    <p class="text-3xl font-bold text-yellow-600">R$ {{ number_format($pendingWithdrawals, 2, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Informação Adicional -->
    <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-200">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Informações</h2>
        <p class="text-slate-500 font-medium">
            Esta página exibe um resumo financeiro do sistema. Para mais detalhes, acesse as páginas específicas de saques e transações.
        </p>
    </div>
</div>
@endsection
