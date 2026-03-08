@extends('layouts.admin')

@section('title', 'Detalhes do Log')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.logs.index') }}" class="text-red-400 hover:text-red-300 mb-2 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Voltar
            </a>
            <h1 class="text-3xl font-bold text-white">Detalhes do Log</h1>
        </div>
        <div class="flex gap-2">
            @if(!$log->resolved)
                <form action="{{ route('admin.logs.resolve', $log->id) }}" method="POST">
                    @csrf
                    <button 
                        type="button"
                        onclick="showResolveModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-2xl transition-colors"
                    >
                        Marcar como Resolvido
                    </button>
                </form>
            @else
                <form action="{{ route('admin.logs.unresolve', $log->id) }}" method="POST">
                    @csrf
                    <button 
                        type="submit"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-4 py-2 rounded-2xl transition-colors"
                    >
                        Marcar como Não Resolvido
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Informações Principais -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Detalhes do Erro -->
            <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10">
                <h2 class="text-xl font-semibold text-white mb-4">Informações do Erro</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-400">Título</label>
                        <p class="text-white font-semibold mt-1">{{ $log->title }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-400">Mensagem</label>
                        <p class="text-white mt-1 whitespace-pre-wrap">{{ $log->message }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-400">Tipo</label>
                            <p class="text-white mt-1">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">
                                    {{ ucfirst($log->type ?? 'Sistema') }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-400">Nível</label>
                            <p class="text-white mt-1">
                                <span class="px-2 py-1 text-xs rounded-full {{ $log->level === 'critical' ? 'bg-red-500/20 text-red-400' : 'bg-orange-500/20 text-orange-400' }}">
                                    {{ ucfirst($log->level) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($log->file)
                    <div>
                        <label class="text-sm text-gray-400">Arquivo</label>
                        <p class="text-white font-mono text-sm mt-1">{{ $log->file }}</p>
                        @if($log->line)
                            <p class="text-gray-400 text-sm mt-1">Linha: {{ $log->line }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stack Trace -->
            @if($log->trace)
            <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10">
                <h2 class="text-xl font-semibold text-white mb-4">Stack Trace</h2>
                <pre class="bg-[#0B0E14] rounded-2xl p-4 text-xs text-gray-400 overflow-x-auto max-h-96 overflow-y-auto"><code>{{ $log->trace }}</code></pre>
            </div>
            @endif

            <!-- Contexto -->
            @if($log->context)
            <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10">
                <h2 class="text-xl font-semibold text-white mb-4">Contexto Adicional</h2>
                <pre class="bg-[#0B0E14] rounded-2xl p-4 text-xs text-gray-400 overflow-x-auto"><code>{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10">
                <h3 class="text-lg font-semibold text-white mb-4">Status</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-400">Estado</label>
                        <p class="mt-1">
                            @if($log->resolved)
                                <span class="px-3 py-1 text-sm rounded-full bg-blue-500/20 text-blue-400">Resolvido</span>
                            @else
                                <span class="px-3 py-1 text-sm rounded-full bg-red-500/20 text-red-400">Pendente</span>
                            @endif
                        </p>
                    </div>
                    @if($log->resolved && $log->resolver)
                    <div>
                        <label class="text-sm text-gray-400">Resolvido por</label>
                        <p class="text-white mt-1">{{ $log->resolver->name }}</p>
                        <p class="text-gray-400 text-sm mt-1">{{ $log->resolved_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    @if($log->resolution_notes)
                    <div>
                        <label class="text-sm text-gray-400">Notas de Resolução</label>
                        <p class="text-white text-sm mt-1 whitespace-pre-wrap">{{ $log->resolution_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10">
                <h3 class="text-lg font-semibold text-white mb-4">Informações</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <label class="text-gray-400">Data/Hora</label>
                        <p class="text-white mt-1">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    @if($log->user)
                    <div>
                        <label class="text-gray-400">Usuário</label>
                        <p class="text-white mt-1">{{ $log->user->name }}</p>
                        <p class="text-gray-400 text-xs mt-1">{{ $log->user->email }}</p>
                    </div>
                    @endif
                    @if($log->transaction_id)
                    <div>
                        <label class="text-gray-400">ID da Transação</label>
                        <p class="text-white font-mono text-xs mt-1">{{ $log->transaction_id }}</p>
                    </div>
                    @endif
                    @if($log->ip_address)
                    <div>
                        <label class="text-gray-400">IP</label>
                        <p class="text-white font-mono text-xs mt-1">{{ $log->ip_address }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resolução -->
<div id="resolveModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10 max-w-md w-full mx-4">
        <h3 class="text-xl font-semibold text-white mb-4">Marcar como Resolvido</h3>
        <form action="{{ route('admin.logs.resolve', $log->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">Notas de Resolução (opcional)</label>
                <textarea 
                    name="resolution_notes" 
                    rows="4"
                    class="w-full bg-[#0B0E14] border border-white/10 rounded-2xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Descreva como o problema foi resolvido..."
                ></textarea>
            </div>
            <div class="flex gap-3">
                <button 
                    type="button"
                    onclick="hideResolveModal()"
                    class="flex-1 bg-[#0B0E14] hover:bg-[#0B0E14] text-white font-semibold px-4 py-2 rounded-2xl transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-2xl transition-colors"
                >
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showResolveModal() {
    document.getElementById('resolveModal').style.display = 'flex';
}

function hideResolveModal() {
    document.getElementById('resolveModal').style.display = 'none';
}
</script>
@endsection








