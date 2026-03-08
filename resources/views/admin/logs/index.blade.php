@extends('layouts.admin')

@section('title', 'Logs de Erros')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Logs de Erros</h1>
            <p class="text-gray-400 mt-1">Monitore e gerencie erros do sistema</p>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10">
            <p class="text-gray-400 text-sm">Total de Erros</p>
            <p class="text-3xl font-bold text-white mt-2">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-red-900/30 border-red-800 rounded-3xl shadow-lg p-6 border border-red-800">
            <p class="text-red-300 text-sm">Não Resolvidos</p>
            <p class="text-3xl font-bold text-red-400 mt-2">{{ $stats['unresolved'] }}</p>
        </div>
        <div class="bg-orange-900/30 border-orange-800 rounded-3xl shadow-lg p-6 border border-orange-800">
            <p class="text-orange-300 text-sm">Críticos</p>
            <p class="text-3xl font-bold text-orange-400 mt-2">{{ $stats['critical'] }}</p>
        </div>
        <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10">
            <p class="text-gray-400 text-sm">Hoje</p>
            <p class="text-3xl font-bold text-white mt-2">{{ $stats['today'] }}</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-[#151A23] rounded-3xl shadow-lg p-6 border border-white/10">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-2">Buscar</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Título ou mensagem..."
                    class="w-full bg-[#0B0E14] border border-white/10 rounded-2xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#00B2FF]"
                >
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Tipo</label>
                <select 
                    name="type"
                    class="w-full bg-[#0B0E14] border border-white/10 rounded-2xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#00B2FF]"
                >
                    <option value="">Todos</option>
                    <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Pagamento</option>
                    <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Saque</option>
                    <option value="api" {{ request('type') === 'api' ? 'selected' : '' }}>API</option>
                    <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Produto</option>
                    <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>Sistema</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Nível</label>
                <select 
                    name="level"
                    class="w-full bg-[#0B0E14] border border-white/10 rounded-2xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#00B2FF]"
                >
                    <option value="">Todos</option>
                    <option value="critical" {{ request('level') === 'critical' ? 'selected' : '' }}>Crítico</option>
                    <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>Erro</option>
                    <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Aviso</option>
                    <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Status</label>
                <select 
                    name="resolved"
                    class="w-full bg-[#0B0E14] border border-white/10 rounded-2xl px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#00B2FF]"
                >
                    <option value="">Todos</option>
                    <option value="0" {{ request('resolved') === '0' ? 'selected' : '' }}>Não Resolvidos</option>
                    <option value="1" {{ request('resolved') === '1' ? 'selected' : '' }}>Resolvidos</option>
                </select>
            </div>
            <div class="flex items-end">
                <button 
                    type="submit"
                    class="w-full bg-[#00B2FF] hover:bg-[#00B2FF]/90 text-white font-semibold px-4 py-2 rounded-2xl transition-colors"
                >
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabela de Logs -->
    <div class="bg-[#151A23] rounded-3xl shadow-lg border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#0B0E14]/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Data/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Título</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Nível</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Usuário</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($logs as $log)
                        <tr class="hover:bg-[#0B0E14]/30 {{ !$log->resolved && $log->level === 'critical' ? 'bg-red-900/20' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-400">
                                    {{ $log->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $log->created_at->format('H:i:s') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-white">{{ $log->title }}</div>
                                <div class="text-xs text-gray-400 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($log->message, 80) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $log->type === 'payment' ? 'bg-blue-500/20 text-blue-400' : 
                                       ($log->type === 'withdrawal' ? 'bg-purple-500/20 text-purple-400' : 
                                       ($log->type === 'api' ? 'bg-yellow-500/20 text-yellow-400' : 
                                       ($log->type === 'product' ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-500/20 text-gray-400'))) }}">
                                    {{ ucfirst($log->type ?? 'Sistema') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $log->level === 'critical' ? 'bg-red-500/20 text-red-400' : 
                                       ($log->level === 'error' ? 'bg-orange-500/20 text-orange-400' : 
                                       ($log->level === 'warning' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-blue-500/20 text-blue-400')) }}">
                                    {{ ucfirst($log->level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-400">
                                    {{ $log->user ? $log->user->name : 'Sistema' }}
                                </div>
                                @if($log->user_id)
                                    <div class="text-xs text-gray-500">ID: {{ $log->user_id }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->resolved)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-400">
                                        Resolvido
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500/20 text-red-400">
                                        Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                    <a 
                                        href="{{ route('admin.logs.show', $log->id) }}"
                                        class="text-blue-400 hover:text-blue-300"
                                        title="Ver detalhes"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if(!$log->resolved)
                                        <form action="{{ route('admin.logs.resolve', $log->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button 
                                                type="submit"
                                                class="text-blue-400 hover:text-blue-300"
                                                title="Marcar como resolvido"
                                                onclick="return confirm('Deseja marcar este erro como resolvido?')"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.logs.unresolve', $log->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button 
                                                type="submit"
                                                class="text-yellow-400 hover:text-yellow-300"
                                                title="Marcar como não resolvido"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.logs.destroy', $log->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit"
                                            class="text-red-400 hover:text-red-300"
                                            title="Deletar"
                                            onclick="return confirm('Tem certeza que deseja deletar este log?')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                Nenhum log encontrado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-white/10">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
