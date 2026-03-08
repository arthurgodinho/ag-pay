@extends('layouts.app')

@section('title', 'Gest�o de Saques')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white">Saques Pendentes</h1>
        <p class="text-gray-400 mt-1">Gerencie os saques solicitados pelos usu�rios</p>
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

    <div class="bg-[#151A23] rounded-3xl shadow-lg border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#0B0E14]/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Usu�rio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Chave PIX</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">A��es</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($withdrawals as $withdrawal)
                        <tr class="hover:bg-[#0B0E14]/30">
                            <td class="px-6 py-4 text-sm text-gray-400">#{{ $withdrawal->id }}</td>
                            <td class="px-6 py-4 text-sm text-white">{{ $withdrawal->user->name }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-white">
                                R$ {{ number_format($withdrawal->amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">{{ $withdrawal->pix_key }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($withdrawal->amount <= $autoPayMaxAmount)
                                    <form method="POST" action="{{ route('admin.withdrawals.auto-pay', $withdrawal->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                            Pagar Agora
                                        </button>
                                    </form>
                                    <span class="text-gray-500 mx-2">|</span>
                                @endif
                                <button onclick="showProcessModal({{ $withdrawal->id }})" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                    Processar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">Nenhum saque pendente</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-white/10">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>

<!-- Modal de Processamento -->
<div id="processModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-[#151A23] rounded-3xl p-6 border border-white/10 max-w-md w-full mx-4">
        <h3 class="text-xl font-semibold text-white mb-4">Processar Saque</h3>
        <form id="processForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-400 mb-2">A��o</label>
                <select name="action" required class="w-full px-4 py-2 bg-[#0B0E14] border border-white/10 rounded-2xl text-white">
                    <option value="approve">Aprovar e Pagar</option>
                    <option value="reject">Rejeitar</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-400 mb-2">Observa��o (opcional)</label>
                <textarea name="admin_note" rows="3" class="w-full px-4 py-2 bg-[#0B0E14] border border-white/10 rounded-2xl text-white"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-2xl">
                    Confirmar
                </button>
                <button type="button" onclick="hideProcessModal()" class="flex-1 bg-[#0B0E14] hover:bg-[#0B0E14] text-white font-semibold py-2 px-4 rounded-2xl">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showProcessModal(withdrawalId) {
        document.getElementById('processForm').action = `/admin/withdrawals/${withdrawalId}/process`;
        document.getElementById('processModal').classList.remove('hidden');
    }

    function hideProcessModal() {
        document.getElementById('processModal').classList.add('hidden');
    }
</script>
@endsection


