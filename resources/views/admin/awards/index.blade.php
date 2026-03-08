@use('Illuminate\Support\Facades\Storage')
@extends('layouts.admin')

@section('title', 'Prêmios')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
                <div class="p-2 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
                Gerenciar Prêmios
            </h1>
            <p class="text-slate-500 mt-2 ml-14">Configure as recompensas disponíveis para seus usuários</p>
        </div>
        <a href="{{ route('admin.awards.create') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all font-bold shadow-lg hover:shadow-blue-500/30 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Novo Prêmio
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down" role="alert">
            <div class="p-1.5 bg-emerald-100 rounded-full shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Imagem</th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Título</th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Descrição</th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Meta</th>
                        <th class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($awards as $award)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-8 py-5">
                                @if($award->image_url)
                                    <img src="/storage/app/public/{{ $award->image_url }}" alt="{{ $award->title }}" class="w-12 h-12 object-cover rounded-lg border-2 border-white shadow-md">
                                @else
                                    <div class="w-16 h-16 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 border border-slate-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <span class="font-bold text-slate-700">{{ $award->title }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm text-slate-600 max-w-xs truncate">{{ $award->description }}</p>
                            </td>
                            <td class="px-8 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    R$ {{ number_format($award->goal_amount, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.awards.edit', $award->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.awards.destroy', $award->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este prêmio?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Excluir">
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
                            <td colspan="5" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6 border border-slate-100">
                                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900 mb-2">Nenhum prêmio cadastrado</h3>
                                    <p class="text-slate-500 max-w-sm mx-auto mb-6">Comece criando o primeiro prêmio para engajar seus usuários.</p>
                                    <a href="{{ route('admin.awards.create') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all font-bold shadow-lg hover:shadow-blue-500/30">
                                        Criar Primeiro Prêmio
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection