@extends('layouts.admin')

@section('title', 'Editar Página Estática')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Editar: {{ $page->title }}</h1>
            <p class="text-gray-400 mt-1">Atualize o conteúdo da página</p>
        </div>
        <a 
            href="{{ route('static.show', $page->slug) }}"
            target="_blank"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl transition-colors"
        >
            Ver Página
        </a>
    </div>

    @if(session('success'))
        <div class="bg-blue-500/20 border border-emerald-500 text-blue-400 px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulário -->
    <form action="{{ route('admin.static.update', $page->slug) }}" method="POST" class="bg-[#151A23] rounded-3xl shadow-lg border border-white/10 p-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Título -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Título</label>
                <input 
                    type="text" 
                    name="title" 
                    value="{{ old('title', $page->title) }}"
                    required
                    class="w-full px-4 py-2 bg-[#0B0E14] border border-white/10 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-[#00B2FF]"
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Conteúdo -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Conteúdo</label>
                <textarea 
                    name="content" 
                    rows="20"
                    required
                    class="w-full px-4 py-2 bg-[#0B0E14] border border-white/10 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-[#00B2FF] font-mono text-sm"
                >{{ old('content', $page->content) }}</textarea>
                <p class="mt-1 text-xs text-gray-400">Use quebras de linha para parágrafos. O texto será formatado automaticamente.</p>
                @error('content')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', $page->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 bg-[#0B0E14] border-white/10 rounded text-red-600 focus:ring-[#00B2FF]"
                    >
                    <span class="ml-2 text-sm text-gray-400">Página ativa (visível publicamente)</span>
                </label>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end space-x-4">
                <a 
                    href="{{ route('admin.static.index') }}"
                    class="px-4 py-2 bg-[#0B0E14] hover:bg-[#0B0E14] text-white rounded-2xl transition-colors"
                >
                    Cancelar
                </a>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-[#00B2FF] hover:bg-[#00B2FF]/90 text-white rounded-2xl transition-colors"
                >
                    Salvar Alterações
                </button>
            </div>
        </div>
    </form>
</div>
@endsection









