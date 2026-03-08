@extends('layouts.app')

@section('title', 'Configuração de Adquirentes')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white">Configuração de Adquirentes</h1>
        <p class="text-gray-400 mt-1">Gerencie as credenciais dos adquirentes de pagamento</p>
    </div>

    @if(session('success'))
        <div class="bg-blue-500/20 border border-emerald-500 text-blue-400 px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulário de Adicionar/Editar -->
    <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10">
        <h2 class="text-xl font-semibold text-white mb-6">Adicionar/Editar Adquirente</h2>
        <form method="POST" action="{{ route('admin.gateway-configs.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Provedor</label>
                    <select name="provider_name" required class="w-full px-4 py-3 bg-[#0B0E14] border border-white/10 rounded-2xl text-white">
                        <option value="">Selecione...</option>
                        <option value="bspay">BSPay</option>
                        <option value="venit">Venit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Prioridade (0-100)</label>
                    <input type="number" name="priority" value="0" min="0" max="100" required class="w-full px-4 py-3 bg-[#0B0E14] border border-white/10 rounded-2xl text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Client ID</label>
                    <input type="text" name="client_id" class="w-full px-4 py-3 bg-[#0B0E14] border border-white/10 rounded-2xl text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Client Secret</label>
                    <input type="password" name="client_secret" class="w-full px-4 py-3 bg-[#0B0E14] border border-white/10 rounded-2xl text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Wallet ID (opcional)</label>
                    <input type="text" name="wallet_id" class="w-full px-4 py-3 bg-[#0B0E14] border border-white/10 rounded-2xl text-white">
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active_for_pix" class="w-4 h-4 text-blue-600 bg-[#0B0E14] border-white/10 rounded">
                        <span class="ml-2 text-gray-400">Ativo para PIX</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active_for_card" class="w-4 h-4 text-blue-600 bg-[#0B0E14] border-white/10 rounded">
                        <span class="ml-2 text-gray-400">Ativo para Cartão</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="mt-6 w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-2xl">
                Salvar Configuração
            </button>
        </form>
    </div>

    <!-- Lista de Configurações -->
    <div class="bg-[#151A23] rounded-3xl shadow-lg border border-white/10 overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <h2 class="text-xl font-semibold text-white">Configurações Existentes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#0B0E14]/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Provedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Prioridade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">PIX</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Cartão</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($configs as $config)
                        <tr class="hover:bg-[#0B0E14]/30">
                            <td class="px-6 py-4 text-sm text-white font-semibold">{{ ucfirst($config->provider_name) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $config->priority }}</td>
                            <td class="px-6 py-4">
                                @if($config->is_active_for_pix)
                                    <span class="text-blue-400">?</span>
                                @else
                                    <span class="text-gray-500">?</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($config->is_active_for_card)
                                    <span class="text-blue-400">?</span>
                                @else
                                    <span class="text-gray-500">?</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.gateway-configs.delete', $config->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Nenhuma configuração encontrada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


