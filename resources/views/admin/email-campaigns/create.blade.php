@extends('layouts.admin')

@section('title', 'Nova Campanha de Email')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
            <div class="p-2 bg-blue-50 rounded-xl">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            Nova Campanha de Email
        </h1>
        <p class="text-slate-500 mt-2 ml-14">Crie uma campanha para enviar emails promocionais ou informativos</p>
    </div>

    <form action="{{ route('admin.email-campaigns.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulário Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informações Básicas -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-6">Informações da Campanha</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-slate-700 font-bold mb-2">Nome da Campanha</label>
                            <input type="text" name="name" required placeholder="Ex: Promoção de Verão 2026" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 font-bold mb-2">Assunto do Email</label>
                            <input type="text" name="subject" required placeholder="Ex: Confira nossas ofertas especiais!" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                            @error('subject')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 font-bold mb-2">Data/Hora de Agendamento (Opcional)</label>
                            <input type="datetime-local" name="scheduled_at" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                            <p class="text-slate-400 text-xs mt-1">Deixe em branco para enviar imediatamente ou agende para depois</p>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo do Email -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-6">Conteúdo do Email</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-700 font-bold">Corpo do Email (HTML)</label>
                            <div class="flex gap-2">
                                <button type="button" onclick="insertVariable('user_name')" class="px-3 py-1 text-xs bg-slate-50 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors font-medium">
                                    &#123;&#123;user_name&#125;&#125;
                                </button>
                                <button type="button" onclick="insertVariable('app_name')" class="px-3 py-1 text-xs bg-slate-50 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors font-medium">
                                    &#123;&#123;app_name&#125;&#125;
                                </button>
                                <button type="button" onclick="insertVariable('app_url')" class="px-3 py-1 text-xs bg-slate-50 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors font-medium">
                                    &#123;&#123;app_url&#125;&#125;
                                </button>
                            </div>
                        </div>
                        
                        <textarea name="body_html" id="body_html" required rows="20" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-mono text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all resize-y" placeholder="Digite o HTML do email aqui... Você pode usar variáveis como {user_name}, {app_name}, {app_url}"></textarea>
                        @error('body_html')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                            <p class="text-blue-700 text-sm"><strong>Variáveis disponíveis:</strong></p>
                            <ul class="text-blue-600 text-xs mt-2 space-y-1 list-disc list-inside">
                                <li><code class="bg-white px-2 py-0.5 rounded border border-blue-100">&#123;&#123;user_name&#125;&#125;</code> - Nome do usuário</li>
                                <li><code class="bg-white px-2 py-0.5 rounded border border-blue-100">&#123;&#123;user_email&#125;&#125;</code> - Email do usuário</li>
                                <li><code class="bg-white px-2 py-0.5 rounded border border-blue-100">&#123;&#123;app_name&#125;&#125;</code> - Nome da aplicação</li>
                                <li><code class="bg-white px-2 py-0.5 rounded border border-blue-100">&#123;&#123;app_url&#125;&#125;</code> - URL da aplicação</li>
                            </ul>
                        </div>

                        <div>
                            <label class="block text-slate-700 font-bold mb-2">Versão Texto (Opcional)</label>
                            <textarea name="body_text" rows="8" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-mono text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all resize-y" placeholder="Versão texto simples do email (opcional, será gerada automaticamente se deixar vazio)"></textarea>
                            <p class="text-slate-400 text-xs mt-1">Versão texto para clientes de email que não suportam HTML</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview e Ações -->
            <div class="space-y-6">
                <!-- Preview -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-8 sticky top-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Preview</h2>
                    <div class="bg-slate-50 rounded-xl p-4 max-h-96 overflow-auto border border-slate-200">
                        <div id="preview-content" class="text-sm prose prose-sm max-w-none">
                            <p class="text-slate-500">Digite o conteúdo para ver o preview</p>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Ações</h2>
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30">
                            Salvar Campanha
                        </button>
                        <a href="{{ route('admin.email-campaigns.index') }}" class="block w-full text-center px-6 py-3 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-all">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function insertVariable(variable) {
    const textarea = document.getElementById('body_html');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    
    textarea.value = textBefore + '{{' + variable + '}}' + textAfter;
    textarea.focus();
    textarea.setSelectionRange(cursorPos + variable.length + 4, cursorPos + variable.length + 4);
    updatePreview();
}

function updatePreview() {
    const content = document.getElementById('body_html').value;
    const preview = document.getElementById('preview-content');
    
    // Substitui variáveis por exemplos
    const appName = @json(config('app.name'));
    let previewContent = content
        .replace(/\{\{user_name\}\}/g, 'João Silva')
        .replace(/\{\{user_email\}\}/g, 'joao@exemplo.com')
        .replace(/\{\{app_name\}\}/g, appName)
        .replace(/\{\{app_url\}\}/g, window.location.origin);
    
    preview.innerHTML = previewContent || '<p class="text-gray-500">Digite o conteúdo para ver o preview</p>';
}

document.getElementById('body_html').addEventListener('input', updatePreview);
updatePreview();
</script>
@endsection

