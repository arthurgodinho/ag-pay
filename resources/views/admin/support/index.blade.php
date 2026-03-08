@extends('layouts.admin')

@section('title', 'Suporte - Admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">Central de Suporte</h1>
        <p class="text-slate-500 mt-1">Gerencie todos os tickets de suporte</p>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-200">
            <p class="text-slate-500 text-sm">Total</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-blue-50 border-blue-200 rounded-3xl shadow-sm p-6 border">
            <p class="text-blue-600 text-sm">Abertos</p>
            <p class="text-3xl font-bold text-blue-700 mt-2">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-amber-50 border-amber-200 rounded-3xl shadow-sm p-6 border">
            <p class="text-amber-600 text-sm">Em Andamento</p>
            <p class="text-3xl font-bold text-amber-700 mt-2">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-blue-50 border-emerald-200 rounded-3xl shadow-sm p-6 border">
            <p class="text-blue-600 text-sm">Resolvidos</p>
            <p class="text-3xl font-bold text-blue-700 mt-2">{{ $stats['resolved'] }}</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-200">
        <form method="GET" action="{{ route('admin.support.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm text-slate-600 mb-2">Status</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Aberto</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em Andamento</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolvido</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fechado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-2">Atribuição</label>
                <select name="assigned_to" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="me" {{ request('assigned_to') === 'me' ? 'selected' : '' }}>Atribuídos a mim</option>
                    <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Não atribuídos</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-2xl transition-colors shadow-sm hover:shadow-md">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Tickets -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Usuário</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Assunto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Atribuído</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Última Mensagem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-500">#{{ $ticket->id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-800 font-medium">{{ $ticket->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $ticket->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $ticket->subject }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full font-medium {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : ($ticket->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Não atribuído' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $ticket->last_message_at ? $ticket->last_message_at->diffForHumans() : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.support.show', $ticket->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">Nenhum ticket encontrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection
