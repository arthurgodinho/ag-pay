@extends('layouts.admin')

@section('title', 'Editor da Landing Page')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    use Illuminate\Support\Facades\Storage;
    $themeColors = ThemeHelper::getThemeColors();
@endphp

<div class="max-w-7xl mx-auto space-y-8" x-data="{ activeTab: 'hero' }">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
            <div class="p-2 bg-blue-50 rounded-xl">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                </svg>
            </div>
            Editor da Landing Page
        </h1>
        <p class="text-slate-500 mt-2 ml-14">Personalize todos os aspectos da sua página inicial</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.landing.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Tabs Navigation -->
        <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-2">
            <button type="button" @click="activeTab = 'hero'" :class="activeTab === 'hero' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-lg font-medium transition-all">
                Hero & Stats
            </button>
            <button type="button" @click="activeTab = 'features'" :class="activeTab === 'features' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-lg font-medium transition-all">
                Soluções
            </button>
            <button type="button" @click="activeTab = 'pricing'" :class="activeTab === 'pricing' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-lg font-medium transition-all">
                Taxas
            </button>
            <button type="button" @click="activeTab = 'whitelabel'" :class="activeTab === 'whitelabel' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-lg font-medium transition-all">
                Whitelabel
            </button>
            <button type="button" @click="activeTab = 'integrations'" :class="activeTab === 'integrations' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-lg font-medium transition-all">
                Integrações
            </button>
            <button type="button" @click="activeTab = 'extra'" :class="activeTab === 'extra' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-lg font-medium transition-all">
                API & Passos
            </button>
            <button type="button" @click="activeTab = 'app'" :class="activeTab === 'app' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-lg font-medium transition-all">
                Página do APP
            </button>
            <button type="button" @click="activeTab = 'faq'" :class="activeTab === 'faq' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-lg font-medium transition-all">
                FAQ
            </button>
            <button type="button" @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'" class="px-4 py-2 rounded-lg font-medium transition-all">
                Configurações Gerais
            </button>
        </div>

        <!-- Hero Tab -->
        <div x-show="activeTab === 'hero'" class="space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Seção Hero</h2>
                <div class="grid gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Badge</label>
                        <input type="text" name="hero_badge" value="{{ $settings['hero_badge'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Nova Geração de Pagamentos">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Título Principal</label>
                        <input type="text" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Gateway de Pagamentos...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo</label>
                        <textarea name="hero_subtitle" rows="3" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Texto CTA</label>
                        <input type="text" name="hero_cta_text" value="{{ $settings['hero_cta_text'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Começar Agora">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Imagem Hero</label>
                        @if(!empty($settings['hero_image']))
                            <div class="p-2 bg-slate-50 border border-slate-200 rounded-xl w-fit mb-3">
                                <img src="{{ asset($settings['hero_image']) }}" class="h-32 rounded-lg object-cover">
                            </div>
                        @endif
                        <input type="file" name="hero_image" class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Estatísticas</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Valor {{ $i }}</label>
                        <input type="text" name="hero_stats{{ $i }}_value" value="{{ $settings['hero_stats'.$i.'_value'] ?? '' }}" class="w-full bg-transparent text-slate-900 border-b border-slate-300 focus:border-blue-500 outline-none pb-1 mb-3">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Label {{ $i }}</label>
                        <input type="text" name="hero_stats{{ $i }}_label" value="{{ $settings['hero_stats'.$i.'_label'] ?? '' }}" class="w-full bg-transparent text-slate-900 border-b border-slate-300 focus:border-blue-500 outline-none pb-1">
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Features Tab -->
        <div x-show="activeTab === 'features'" class="space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl">
                <div class="grid gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Título da Seção</label>
                        <input type="text" name="features_title" value="{{ $settings['features_title'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo</label>
                        <textarea name="features_subtitle" rows="2" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['features_subtitle'] ?? '' }}</textarea>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <h3 class="text-slate-900 font-bold mb-4">Card {{ $i }}</h3>
                        <div class="space-y-4">
                            <input type="text" name="feature{{ $i }}_title" value="{{ $settings['feature'.$i.'_title'] ?? '' }}" placeholder="Título" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                            <textarea name="feature{{ $i }}_text" rows="3" placeholder="Descrição" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['feature'.$i.'_text'] ?? '' }}</textarea>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Pricing Tab -->
        <div x-show="activeTab === 'pricing'" class="space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl">
                <div class="grid gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Título</label>
                        <input type="text" name="pricing_title" value="{{ $settings['pricing_title'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo</label>
                        <textarea name="pricing_subtitle" rows="2" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['pricing_subtitle'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nota de Rodapé</label>
                        <input type="text" name="pricing_note" value="{{ $settings['pricing_note'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- Whitelabel Tab -->
        <div x-show="activeTab === 'whitelabel'" class="space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Título</label>
                            <input type="text" name="whitelabel_title" value="{{ $settings['whitelabel_title'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Texto Principal</label>
                            <textarea name="whitelabel_text" rows="3" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['whitelabel_text'] ?? '' }}</textarea>
                        </div>
                        
                        <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-blue-900 font-bold flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                                        Usar Imagem do Hero
                                    </h3>
                                    <p class="text-blue-700/70 text-sm mt-1">Ative para ignorar o upload abaixo e usar a imagem da aba Hero.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="whitelabel_use_hero_image" value="0">
                                    <input type="checkbox" name="whitelabel_use_hero_image" value="1" {{ ($settings['whitelabel_use_hero_image'] ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-slate-700">Imagem Personalizada (Whitelabel)</label>
                        <div class="relative group" x-data="{ preview: null }">
                            <div class="w-full h-64 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-all overflow-hidden relative">
                                <!-- Preview Image -->
                                <template x-if="preview">
                                    <img :src="preview" class="absolute inset-0 w-full h-full object-cover z-10">
                                </template>
                                
                                <!-- Existing Image -->
                                @if(!empty($settings['whitelabel_image']))
                                    <img src="{{ asset($settings['whitelabel_image']) }}" class="absolute inset-0 w-full h-full object-cover" x-show="!preview">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-20">
                                        <span class="text-white font-bold">Alterar Imagem</span>
                                    </div>
                                @else
                                    <div x-show="!preview" class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                                        <span class="text-slate-400 font-medium">Clique para fazer upload</span>
                                    </div>
                                @endif
                                
                                <input type="file" name="whitelabel_image" 
                                    @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { preview = e.target.result }; reader.readAsDataURL(file); }"
                                    class="absolute inset-0 opacity-0 cursor-pointer z-30">
                            </div>
                            <p class="text-xs text-slate-400 mt-2 text-center italic">Esta imagem substituirá o mockup visual na seção Whitelabel.</p>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    @for($i = 1; $i <= 4; $i++)
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <h3 class="text-slate-900 font-bold mb-4">Item {{ $i }}</h3>
                        <div class="space-y-4">
                            <input type="text" name="whitelabel_item{{ $i }}_title" value="{{ $settings['whitelabel_item'.$i.'_title'] ?? '' }}" placeholder="Título" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                            <textarea name="whitelabel_item{{ $i }}_text" rows="2" placeholder="Descrição" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['whitelabel_item'.$i.'_text'] ?? '' }}</textarea>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Integrations Tab -->
        <div x-show="activeTab === 'integrations'" class="space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl">
                <div class="grid md:grid-cols-3 gap-6">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <h3 class="text-slate-900 font-bold mb-4">Integração {{ $i }}</h3>
                        <div class="space-y-4">
                            <input type="text" name="integration{{ $i }}_title" value="{{ $settings['integration'.$i.'_title'] ?? '' }}" placeholder="Título" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                            <textarea name="integration{{ $i }}_text" rows="3" placeholder="Descrição" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['integration'.$i.'_text'] ?? '' }}</textarea>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Extra Sections Tab (API & Steps) -->
        <div x-show="activeTab === 'extra'" class="space-y-6">
            <!-- API Section -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Seção API / Desenvolvedores</h2>
                <div class="grid gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Título</label>
                        <input type="text" name="api_title" value="{{ $settings['api_title'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="API Robusta para Desenvolvedores">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo</label>
                        <textarea name="api_subtitle" rows="2" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['api_subtitle'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Texto/Descrição Técnica</label>
                        <textarea name="api_text" rows="3" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['api_text'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Steps Section -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Seção Passo a Passo (Como Funciona)</h2>
                <div class="grid gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Título da Seção</label>
                        <input type="text" name="steps_title" value="{{ $settings['steps_title'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Comece em 3 Passos">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo</label>
                        <textarea name="steps_subtitle" rows="2" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['steps_subtitle'] ?? '' }}</textarea>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <h3 class="text-slate-900 font-bold mb-4">Passo {{ $i }}</h3>
                        <div class="space-y-4">
                            <input type="text" name="step{{ $i }}_title" value="{{ $settings['step'.$i.'_title'] ?? '' }}" placeholder="Título (Ex: Crie sua conta)" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                            <textarea name="step{{ $i }}_text" rows="3" placeholder="Descrição" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['step'.$i.'_text'] ?? '' }}</textarea>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- App Page Tab -->
        <div x-show="activeTab === 'app'" class="space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Configurações da Página do APP</h2>
                <div class="grid gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Título Principal</label>
                        <input type="text" name="app_title" value="{{ $settings['app_title'] ?? 'Seu Banco na palma da sua mão.' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo / Descrição</label>
                        <textarea name="app_subtitle" rows="4" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['app_subtitle'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Link Google PlayStore</label>
                        <input type="text" name="app_playstore_url" value="{{ $settings['app_playstore_url'] ?? '#' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Tab -->
        <div x-show="activeTab === 'faq'" class="space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-md">
                <div class="grid gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Título</label>
                        <input type="text" name="faq_title" value="{{ $settings['faq_title'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo</label>
                        <input type="text" name="faq_subtitle" value="{{ $settings['faq_subtitle'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    @for($i = 1; $i <= 6; $i++)
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <h3 class="text-slate-900 font-bold mb-4">Pergunta {{ $i }}</h3>
                        <div class="space-y-4">
                            <input type="text" name="faq{{ $i }}_question" value="{{ $settings['faq'.$i.'_question'] ?? '' }}" placeholder="Pergunta" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                            <textarea name="faq{{ $i }}_answer" rows="3" placeholder="Resposta" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $settings['faq'.$i.'_answer'] ?? '' }}</textarea>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Settings Tab (General) -->
        <div x-show="activeTab === 'settings'" class="space-y-6">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Marca & SEO</h2>
                <div class="grid gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Logo</label>
                        @if(!empty($settings['logo']))
                            <div class="mb-2 p-2 bg-slate-50 border border-slate-200 rounded-lg w-fit">
                                <img src="{{ asset($settings['logo']) }}" class="h-12 object-contain">
                            </div>
                        @endif
                        <input type="file" name="logo" class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg hover:shadow-blue-500/30">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection
