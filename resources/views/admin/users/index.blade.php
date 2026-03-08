@extends('layouts.admin')

@section('title', 'Usuários')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Usuários</h1>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-2xl" style="background-color: {{ $themeColors['primary'] }}20; border: 1px solid {{ $themeColors['primary'] }}; color: {{ $themeColors['primary'] }};">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-2xl" style="background-color: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.2); color: #DC2626;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm mb-1 font-medium">Total de Cadastros</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background-color: {{ $themeColors['primary'] }}15;">
                    <svg class="w-6 h-6" style="color: {{ $themeColors['primary'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm mb-1 font-medium">Cadastros no Mês</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['month'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background-color: {{ $themeColors['primary'] }}15;">
                    <svg class="w-6 h-6" style="color: {{ $themeColors['primary'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm mb-1 font-medium">Cadastros Pendentes</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background-color: {{ $themeColors['primary'] }}15;">
                    <svg class="w-6 h-6" style="color: {{ $themeColors['primary'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm mb-1 font-medium">Usuários Banidos</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['blocked'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background-color: {{ $themeColors['primary'] }}15;">
                    <svg class="w-6 h-6" style="color: {{ $themeColors['primary'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Busca -->
    <div class="bg-white rounded-2xl shadow-sm p-4 border border-slate-200">
        <div class="flex flex-col md:flex-row gap-4 items-center">
            <div class="flex-1 w-full">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Buscar"
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 rounded-2xl transition-all"
                    style="focus:ring-color: {{ $themeColors['primary'] }};"
                    onkeyup="if(event.key === 'Enter') { window.location.href = '{{ route('admin.users.index') }}?search=' + this.value + '&status={{ request('status', 'todos') }}'; }"
                >
            </div>
            <div>
                <select 
                    name="status"
                    onchange="window.location.href = '{{ route('admin.users.index') }}?status=' + this.value + '&search={{ request('search') }}'"
                    class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 rounded-2xl transition-all"
                    style="focus:ring-color: {{ $themeColors['primary'] }};"
                >
                    <option value="todos" {{ request('status', 'todos') === 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovados</option>
                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Bloqueados</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendentes</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabela de Usuários -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Usuário</th>
                        <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">CPF/CNPJ</th>
                        <th class="hidden lg:table-cell px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Permissão</th>
                        <th class="hidden xl:table-cell px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Adquirente</th>
                        <th class="hidden lg:table-cell px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vendas 7d</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="hidden xl:table-cell px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Doc</th>
                        <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Criado</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center mr-3 border border-slate-200">
                                        <span class="text-xs font-bold text-slate-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="hidden md:table-cell px-4 py-3 text-sm text-slate-500 font-medium">
                                {{ $user->cpf_cnpj ?: '---' }}
                            </td>
                            <td class="hidden lg:table-cell px-4 py-3">
                                @if($user->is_admin)
                                    <span class="px-3 py-1 text-xs font-bold rounded-full" style="background-color: {{ $themeColors['primary'] }}15; color: {{ $themeColors['primary'] }};">ADMIN</span>
                                @elseif($user->is_manager)
                                    <span class="px-3 py-1 text-xs font-bold rounded-full" style="background-color: {{ $themeColors['primary'] }}15; color: {{ $themeColors['primary'] }};">GERENTE</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-500">CLIENTE</span>
                                @endif
                            </td>
                            <td class="hidden xl:table-cell px-4 py-3 text-sm text-slate-500 font-medium">
                                {{ $user->preferred_gateway ? ucfirst($user->preferred_gateway) : 'Padrão' }}
                            </td>
                            <td class="hidden lg:table-cell px-4 py-3 text-sm text-slate-600 font-medium">
                                R$ {{ number_format($user->sales_7d ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_blocked)
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-600">BLOQUEADO</span>
                                @elseif(!$user->is_approved)
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700">PENDENTE</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-700">ATIVO</span>
                                @endif
                            </td>
                            <td class="hidden xl:table-cell px-4 py-3">
                                @if($user->kyc_status === 'approved' || $user->kyc_status === 'verified')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-700">OK</span>
                                @elseif($user->kyc_status === 'pending')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700">ANÁLISE</span>
                                @elseif($user->kyc_status === 'rejected')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-600">REJEITADO</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-400">---</span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell px-4 py-3 text-sm text-slate-500 font-medium">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-1">
                                    <!-- Editar -->
                                    <a 
                                        href="{{ route('admin.users.edit', $user->id) }}"
                                        class="p-1 transition hover:bg-slate-100 rounded text-slate-900"
                                        title="Editar"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <!-- Usuário -->
                                    <button 
                                        type="button"
                                        onclick="alert('Funcionalidade de gerenciar usuário')"
                                        class="p-1 text-slate-900 hover:text-slate-600 transition hover:bg-slate-100 rounded"
                                        title="Gerenciar"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </button>
                                    <!-- Aprovar/Clock -->
                                    @if(!$user->is_approved)
                                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button 
                                                type="submit"
                                                class="p-1 transition hover:bg-slate-100 rounded text-slate-800"
                                                title="Aprovar"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button 
                                                type="submit"
                                                class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded transition-colors shadow-sm"
                                                title="Reprovar"
                                                onclick="return confirm('Tem certeza que deseja reprovar este usuário?')"
                                            >
                                                Reprovar
                                            </button>
                                        </form>
                                    @endif
                                    <!-- Bloquear/Desbloquear -->
                                    @if($user->id !== auth()->id())
                                        @if($user->is_blocked)
                                            <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button 
                                                    type="submit"
                                                    class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded transition-colors shadow-sm"
                                                    title="Desbloquear"
                                                >
                                                    Desbloquear
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.block', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button 
                                                    type="submit"
                                                    class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded transition-colors shadow-sm"
                                                    title="Bloquear"
                                                    onclick="return confirm('Tem certeza que deseja bloquear este usuário?')"
                                                >
                                                    Bloquear
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                    <!-- Excluir -->
                                    @if($user->id !== auth()->id() && !$user->is_admin)
                                        <button 
                                            type="button"
                                            onclick="if(confirm('Tem certeza que deseja excluir este usuário?')) { document.getElementById('delete-form-{{ $user->id }}').submit(); }"
                                            class="p-1 text-slate-800 hover:text-red-600 transition hover:bg-red-50 rounded"
                                            title="Excluir"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @else
                                        <span class="p-1 text-slate-300 cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </span>
                                    @endif
                                    <!-- Saldo/Dólar -->
                                    <a 
                                        href="{{ route('admin.users.edit', $user->id) }}#balance"
                                        class="p-1 text-slate-800 hover:text-blue-600 transition hover:bg-blue-50 rounded"
                                        title="Gerenciar Saldo"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </a>
                                    <!-- Usuário 2 -->
                                    <button 
                                        type="button"
                                        onclick="alert('Funcionalidade adicional')"
                                        class="p-1 text-slate-800 hover:text-slate-600 transition hover:bg-slate-100 rounded"
                                        title="Mais opções"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                Nenhum usuário encontrado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
