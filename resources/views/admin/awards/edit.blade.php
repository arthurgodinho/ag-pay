@use('Illuminate\Support\Facades\Storage')
@extends('layouts.admin')

@section('title', 'Editar Prêmio')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.awards.index') }}" class="p-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg transition-colors text-slate-500 hover:text-slate-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Editar Prêmio</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.awards.update', $award->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Título -->
                <div class="space-y-2">
                    <label for="title" class="text-sm font-bold text-slate-700">Título do Prêmio</label>
                    <input type="text" name="title" id="title" required value="{{ $award->title }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <!-- Meta de Saldo -->
                <div class="space-y-2">
                    <label for="goal_amount" class="text-sm font-bold text-slate-700">Meta de Saldo (R$)</label>
                    <input type="number" name="goal_amount" id="goal_amount" required step="0.01" min="0" value="{{ $award->goal_amount }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
            </div>

            <!-- Descrição -->
            <div class="space-y-2">
                <label for="description" class="text-sm font-bold text-slate-700">Descrição Curta</label>
                <textarea name="description" id="description" required rows="3"
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">{{ $award->description }}</textarea>
            </div>

            <!-- Imagem -->
            <div class="space-y-2">
                <label for="image" class="text-sm font-bold text-slate-700">Imagem do Prêmio</label>
                
                @error('image')
                    <div class="p-3 bg-red-50 text-red-600 rounded-lg text-sm mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:bg-slate-50 transition-colors cursor-pointer relative @error('image') border-red-300 bg-red-50 @enderror">
                    <input type="file" name="image" id="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                    
                    <div id="image-preview" class="{{ $award->image_url ? '' : 'hidden' }} mb-4">
                        <img src="{{ $award->image_url ? url('storage/app/public/' . $award->image_url) : '' }}" alt="Preview" class="mx-auto h-32 object-contain rounded-lg shadow-sm">
                    </div>
                    
                    <div id="image-placeholder" class="{{ $award->image_url ? 'hidden' : '' }}">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-1 text-sm text-slate-500">Clique para alterar a imagem</p>
                        <p class="mt-1 text-xs text-slate-400">PNG, JPG, GIF, WEBP até 10MB</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-all transform active:scale-95">
                    Atualizar Prêmio
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        const img = preview.querySelector('img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
