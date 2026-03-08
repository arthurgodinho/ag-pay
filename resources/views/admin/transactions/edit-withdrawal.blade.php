@extends('layouts.admin')

@section('title', 'Editar Saque')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.transactions.index') }}" class="text-red-400 hover:text-red-300 mb-2 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Voltar
            </a>
            <h1 class="text-3xl font-bold text-white">Editar Saque</h1>
            <p class="text-gray-400 mt-1">#{{ $withdrawal->id }} - {{ $withdrawal->user->name }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-blue-500/20 border border-emerald-500 text-blue-400 px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-2xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulário -->
        <div class="lg:col-span-2">
            <div class="bg-[#151A23] rounded-3xl p-6 border border-white/10">
                <form method="POST" action="{{ route('admin.transactions.update-withdrawal', $withdrawal->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Informações do Saque -->
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4">Informações do Saque</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Cliente</label>
                                    <input
                                        type="text"
                                        value="{{ $withdrawal->user->name }} ({{ $withdrawal->user->email }})"
                                        readonly
                                        class="w-full px-4 py-2 bg-[#0B0E14]/50 border border-white/10 rounded-2xl text-gray-400"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Data</label>
                                    <input
                                        type="text"
                                        value="{{ $withdrawal->created_at->format('d/m/Y H:i:s') }}"
                                        readonly
                                        class="w-full px-4 py-2 bg-[#0B0E14]/50 border border-white/10 rounded-2xl text-gray-400"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Valores -->
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4">Valores</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Valor Bruto</label>
                                    <input
                                        type="text"
                                        value="R$ {{ number_format($withdrawal->amount_gross ?? $withdrawal->amount, 2, ',', '.') }}"
                                        readonly
                                        class="w-full px-4 py-2 bg-[#0B0E14]/50 border border-white/10 rounded-2xl text-gray-400"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Taxa</label>
                                    <input
                                        type="text"
                                        value="R$ {{ number_format($withdrawal->fee ?? 0, 2, ',', '.') }}"
                                        readonly
                                        class="w-full px-4 py-2 bg-[#0B0E14]/50 border border-white/10 rounded-2xl text-gray-400"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Valor Líquido</label>
                                    <input
                                        type="text"
                                        value="R$ {{ number_format($withdrawal->amount ?? ($withdrawal->amount_gross - ($withdrawal->fee ?? 0)), 2, ',', '.') }}"
                                        readonly
                                        class="w-full px-4 py-2 bg-[#0B0E14]/50 border border-white/10 rounded-2xl text-gray-400"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Chave PIX -->
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Chave PIX</label>
                            <input
                                type="text"
                                value="{{ $withdrawal->pix_key ?? 'N/A' }}"
                                readonly
                                class="w-full px-4 py-2 bg-[#0B0E14]/50 border border-white/10 rounded-2xl text-gray-400 font-mono"
                            >
                        </div>

                        <!-- Status -->
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4">Status</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Status do Saque</label>
                                <select name="status" required class="w-full px-4 py-2 bg-[#0B0E14] border border-white/10 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-[#00B2FF]">
                                    <option value="pending" {{ $withdrawal->status === 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="processing" {{ $withdrawal->status === 'processing' ? 'selected' : '' }}>Processando</option>
                                    <option value="paid" {{ $withdrawal->status === 'paid' ? 'selected' : '' }}>Pago</option>
                                    <option value="completed" {{ $withdrawal->status === 'completed' ? 'selected' : '' }}>Completado</option>
                                    <option value="failed" {{ $withdrawal->status === 'failed' ? 'selected' : '' }}>Falhou</option>
                                    <option value="cancelled" {{ $withdrawal->status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="text-yellow-400">?? Atenção:</span> Ao marcar como "Pago", o valor será debitado do saldo congelado do cliente.
                                </p>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4">Informações Adicionais</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">ID Externo</label>
                                    <input
                                        type="text"
                                        value="{{ $withdrawal->external_id ?? 'N/A' }}"
                                        readonly
                                        class="w-full px-4 py-2 bg-[#0B0E14]/50 border border-white/10 rounded-2xl text-gray-400"
                                    >
                                </div>
                                @if($withdrawal->processed_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Processado em</label>
                                    <input
                                        type="text"
                                        value="{{ $withdrawal->processed_at->format('d/m/Y H:i:s') }}"
                                        readonly
                                        class="w-full px-4 py-2 bg-[#0B0E14]/50 border border-white/10 rounded-2xl text-gray-400"
                                    >
                                </div>
                                @endif
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-[#00B2FF] hover:bg-[#00B2FF]/90 text-white font-semibold py-3 px-6 rounded-2xl transition-colors">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-[#151A23] rounded-3xl p-6 border border-white/10">
                <h3 class="text-lg font-semibold text-white mb-4">Resumo</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-400">Status Atual</p>
                        <p class="text-white mt-1">
                            @if($withdrawal->status === 'paid' || $withdrawal->status === 'completed')
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">Pago</span>
                            @elseif($withdrawal->status === 'pending')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-500/20 text-yellow-400">Pendente</span>
                            @elseif($withdrawal->status === 'processing')
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">Processando</span>
                            @elseif($withdrawal->status === 'failed')
                                <span class="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400">Falhou</span>
                            @elseif($withdrawal->status === 'cancelled')
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-500/20 text-gray-400">Cancelado</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-500/20 text-gray-400">{{ $withdrawal->status }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400">Valor Bruto</p>
                        <p class="text-white font-semibold">R$ {{ number_format($withdrawal->amount_gross ?? $withdrawal->amount, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Taxa</p>
                        <p class="text-white">R$ {{ number_format($withdrawal->fee ?? 0, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Valor Líquido</p>
                        <p class="text-red-400 font-semibold">R$ {{ number_format($withdrawal->amount ?? ($withdrawal->amount_gross - ($withdrawal->fee ?? 0)), 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Criado em</p>
                        <p class="text-white">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
