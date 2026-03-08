@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
                <div class="p-2 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </div>
                Dashboard Admin
            </h1>
            <p class="text-slate-500 mt-2 ml-14">Visão geral e estatísticas em tempo real</p>
        </div>
    </div>

    <!-- Cards Principais de Estatísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Lucro Total -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wide">Lucro Total</p>
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-1">
                R$ {{ number_format($totalProfit, 2, ',', '.') }}
            </p>
            <p class="text-xs text-slate-400">Total de taxas coletadas</p>
        </div>

        <!-- Usuários Ativos -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wide">Usuários Ativos</p>
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-1">{{ $totalUsers }}</p>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1">
                Ver todos
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <!-- KYC Pendentes -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wide">KYC Pendentes</p>
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-1">{{ $pendingKyc }}</p>
            <a href="{{ route('admin.kyc.index') }}" class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1">
                Revisar documentos
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <!-- Saques Pendentes -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wide">Saques Pendentes</p>
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-1">{{ $pendingWithdrawals }}</p>
            <a href="{{ route('admin.withdrawals.index') }}" class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1">
                Processar saques
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Segunda Linha de Estatísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Saldo Total dos Usuários -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wide">Saldo em Custódia</p>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-1">
                R$ {{ number_format($totalUserBalance, 2, ',', '.') }}
            </p>
            <p class="text-xs text-slate-400">Soma de todas as carteiras</p>
        </div>

        <!-- Transações Hoje -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wide">Transações Hoje</p>
                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-1">{{ $transactionsToday }}</p>
            <p class="text-xs text-slate-400">Total de transações hoje</p>
        </div>

        <!-- Transações Aprovadas -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wide">Aprovadas</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-1">{{ $transactionsCompleted }}</p>
            <p class="text-xs text-slate-400">De {{ $totalTransactions }} total</p>
        </div>

        <!-- Chargebacks Pendentes -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wide">Chargebacks</p>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-1">{{ $totalChargebacks ?? 0 }}</p>
            <a href="{{ route('admin.chargebacks.index') }}" class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1">
                Ver MEDs
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Gráfico de Volume e Lucro -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
            Volume de Transações (7 Dias)
        </h2>
        <div id="adminChart" class="h-72 w-full"></div>
    </div>

    <!-- Ações Rápidas -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Ações Rápidas
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.gateways.index') }}" class="bg-slate-50 rounded-xl border border-slate-200 p-4 hover:border-blue-500/50 transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition-colors">Adquirentes</h3>
                        <p class="text-xs text-slate-500">Gerenciar gateways</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.transactions.index') }}" class="bg-slate-50 rounded-xl border border-slate-200 p-4 hover:border-blue-500/50 transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition-colors">Transações</h3>
                        <p class="text-xs text-slate-500">Ver todas as transações</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.kyc.index') }}" class="bg-slate-50 rounded-xl border border-slate-200 p-4 hover:border-blue-500/50 transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center group-hover:bg-yellow-100 transition-colors">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition-colors">KYC</h3>
                        <p class="text-xs text-slate-500">Aprovar documentos</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.configs.index') }}" class="bg-slate-50 rounded-xl border border-slate-200 p-4 hover:border-blue-500/50 transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition-colors">Configurações</h3>
                        <p class="text-xs text-slate-500">Ajustes do sistema</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartElement = document.querySelector("#adminChart");
        if (!chartElement) return;

        function initAdminChart() {
            if (typeof ApexCharts === 'undefined') {
                setTimeout(initAdminChart, 100);
                return;
            }

            const chartData = @json($chartData ?? ['dates' => [], 'volumes' => [], 'profits' => []]);

            const options = {
                series: [
                    {
                        name: 'Volume Total (R$)',
                        type: 'column',
                        data: chartData.volumes
                    },
                    {
                        name: 'Lucro (R$)',
                        type: 'line',
                        data: chartData.profits
                    }
                ],
                chart: {
                    height: 320,
                    type: 'line',
                    toolbar: {
                        show: false
                    },
                    background: 'transparent'
                },
                stroke: {
                    width: [0, 4],
                    curve: 'smooth'
                },
                title: {
                    text: undefined
                },
                dataLabels: {
                    enabled: false
                },
                labels: chartData.dates,
                xaxis: {
                    type: 'category',
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px',
                            fontFamily: 'Inter, sans-serif'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: [
                    {
                        title: {
                            text: 'Volume Total',
                            style: { color: '#2563EB', fontFamily: 'Inter, sans-serif' }
                        },
                        labels: {
                            style: { colors: '#2563EB', fontFamily: 'Inter, sans-serif' },
                            formatter: function (val) {
                                return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                            }
                        }
                    },
                    {
                        opposite: true,
                        title: {
                            text: 'Lucro',
                            style: { color: '#10b981', fontFamily: 'Inter, sans-serif' }
                        },
                        labels: {
                            style: { colors: '#10b981', fontFamily: 'Inter, sans-serif' },
                            formatter: function (val) {
                                return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                ],
                colors: ['#2563EB', '#10b981'],
                grid: {
                    borderColor: '#e2e8f0',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function (val) {
                            return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif'
                    }
                },
                legend: {
                    labels: {
                        colors: '#475569'
                    }
                }
            };

            const chart = new ApexCharts(chartElement, options);
            chart.render();
        }

        initAdminChart();
    });
</script>
@endpush
