@extends('layouts.app')

@section('title', 'Criar Produto')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-20 md:pb-0">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-12 sm:mb-8 relative z-20">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Criar Produto</h1>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto mt-2 sm:mt-0">
            <a href="{{ route('dashboard.checkout.index') }}" class="flex-1 sm:flex-none justify-center px-4 py-2.5 text-slate-600 hover:text-slate-800 font-medium transition-colors bg-white border border-slate-300 rounded-lg text-sm flex items-center gap-2 relative z-40">
                Cancelar
            </a>
            <button type="submit" form="product-form" class="flex-1 sm:flex-none justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all text-sm flex items-center gap-2 shadow-sm hover:shadow-md relative z-40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Criar Produto
            </button>
        </div>
    </div>

    <!-- Main Form -->
    <form id="product-form" action="{{ route('dashboard.checkout.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Tabs Navigation (Alpine.js) -->
        <div x-data="{ tab: 'geral' }" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Mobile Scrollable Tabs -->
            <div class="border-b border-slate-200 overflow-x-auto scrollbar-hide">
                <nav class="flex min-w-full">
                    <button type="button" @click="tab = 'geral'" 
                        :class="{'border-blue-500 text-blue-600 bg-blue-50/50': tab === 'geral', 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50': tab !== 'geral'}" 
                        class="flex-1 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Geral
                    </button>
                    <button type="button" @click="tab = 'config'" 
                        :class="{'border-blue-500 text-blue-600 bg-blue-50/50': tab === 'config', 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50': tab !== 'config'}" 
                        class="flex-1 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Configurações
                    </button>
                    <button type="button" @click="tab = 'rastreamento'" 
                        :class="{'border-blue-500 text-blue-600 bg-blue-50/50': tab === 'rastreamento', 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50': tab !== 'rastreamento'}" 
                        class="flex-1 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Rastreamento
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Tab: Geral -->
                <div x-show="tab === 'geral'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    
                    <!-- Status Card -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between transition-shadow hover:shadow-md">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800">Status do Produto</h3>
                            <p class="text-sm text-slate-500">Defina se o produto está disponível para vendas</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" class="sr-only peer" checked>
                            <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 transition-colors"></div>
                        </label>
                    </div>

                    <!-- Basic Info Card -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm transition-shadow hover:shadow-md">
                        <h3 class="text-base font-semibold text-slate-800 mb-6 flex items-center gap-2">
                            <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            Informações Básicas
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Nome do Produto</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-shadow" placeholder="Ex: E-book Premium" required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Preço</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-slate-500 sm:text-sm font-bold">R$</span>
                                    </div>
                                    <input type="number" name="price" step="0.01" value="{{ old('price') }}" class="block w-full rounded-lg border-slate-300 pl-10 focus:border-blue-500 focus:ring-blue-500 transition-shadow" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Descrição</label>
                                <textarea name="description" rows="4" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-shadow resize-none" placeholder="Descreva seu produto para o cliente...">{{ old('description') }}</textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Link de Download <span class="text-slate-400 font-normal">(Produto Digital)</span></label>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </span>
                                    <input type="url" name="download_url" value="{{ old('download_url') }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-slate-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-shadow" placeholder="https://exemplo.com/download/arquivo.zip">
                                </div>
                                <p class="text-xs text-slate-500 mt-2">Link para o cliente baixar o arquivo após o pagamento.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Media Card -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm transition-shadow hover:shadow-md">
                        <h3 class="text-base font-semibold text-slate-800 mb-6 flex items-center gap-2">
                            <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            Mídia do Produto
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8" x-data="{ imageUrl: '{{ old('product_image_url') }}', imageError: false }">
                            <div class="md:col-span-2 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">URL da Imagem</label>
                                    <div class="flex rounded-md shadow-sm">
                                        <input type="url" name="product_image_url" 
                                            x-model="imageUrl"
                                            @input="imageError = false"
                                            placeholder="https://exemplo.com/imagem.jpg" 
                                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border-slate-300 focus:border-purple-500 focus:ring-purple-500 sm:text-sm transition-shadow">
                                    </div>
                                    <p class="text-xs text-slate-500 mt-2">Cole o link direto da imagem do seu produto para exibição no checkout.</p>
                                </div>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Pré-visualização</label>
                                <div class="aspect-square w-full rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden relative group hover:border-purple-400 transition-colors">
                                    <div x-show="imageUrl && !imageError" class="w-full h-full">
                                        <img :src="imageUrl" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" x-on:error="imageError = true">
                                    </div>
                                    <div x-show="!imageUrl || imageError" class="text-center p-4">
                                        <svg class="mx-auto h-12 w-12 text-slate-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-1 text-sm text-slate-400" x-text="imageError ? 'Erro ao carregar' : 'Sem imagem'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Configurações -->
                <div x-show="tab === 'config'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="space-y-8">
                    
                    <!-- Payment Methods -->
                    <div>
                        <h3 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            Formas de Pagamento
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative flex items-center justify-between p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-blue-300 hover:bg-blue-50/30 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-green-100 rounded-lg text-green-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold text-slate-800">Pix</span>
                                        <span class="block text-xs text-slate-500">Aprovação imediata</span>
                                    </div>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_pix" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                            </label>

                            <label class="relative flex items-center justify-between p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-blue-300 hover:bg-blue-50/30 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold text-slate-800">Cartão de Crédito</span>
                                        <span class="block text-xs text-slate-500">Parcelamento</span>
                                    </div>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_credit_card" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6"></div>

                    <!-- Display Options -->
                    <div>
                        <h3 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Exibição
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Show Image -->
                            <label class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100 cursor-pointer">
                                <div>
                                    <h4 class="font-medium text-slate-800 text-sm">Imagem do Produto</h4>
                                    <p class="text-xs text-slate-500">Exibir no checkout</p>
                                </div>
                                <input type="checkbox" name="show_product_image" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5" checked>
                            </label>

                            <!-- Timer -->
                            <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="font-medium text-slate-800 text-sm">Timer de Escassez</h4>
                                        <p class="text-xs text-slate-500">Urgência para compra</p>
                                    </div>
                                    <input type="checkbox" name="has_timer" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5" x-model="hasTimer">
                                </div>
                                <div x-show="hasTimer" x-transition class="mt-2">
                                    <input type="number" name="timer_minutes" value="15" class="w-full rounded-md border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Minutos">
                                </div>
                            </div>

                            <!-- Security Badges -->
                            <label class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100 cursor-pointer">
                                <div>
                                    <h4 class="font-medium text-slate-800 text-sm">Selos de Segurança</h4>
                                    <p class="text-xs text-slate-500">Site Seguro, SSL, etc</p>
                                </div>
                                <input type="checkbox" name="show_security_badges" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5" checked>
                            </label>

                            <!-- Guarantee -->
                            <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="font-medium text-slate-800 text-sm">Garantia</h4>
                                        <p class="text-xs text-slate-500">Dias de garantia</p>
                                    </div>
                                    <input type="checkbox" name="show_guarantee" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5" x-model="hasGuarantee" checked>
                                </div>
                                <div x-show="hasGuarantee" x-transition class="mt-2">
                                    <input type="number" name="warranty_days" value="7" class="w-full rounded-md border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Dias">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Rastreamento -->
                <div x-show="tab === 'rastreamento'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="space-y-6">
                    
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm transition-shadow hover:shadow-md">
                        <h3 class="text-base font-semibold text-slate-800 mb-6 flex items-center gap-2">
                            <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            Pixels de Rastreamento
                        </h3>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Pixel do Facebook</label>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">ID</span>
                                    <input type="text" name="pixel_facebook" value="{{ old('pixel_facebook') }}" placeholder="Ex: 123456789012345" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-slate-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-shadow">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Google Ads ID</label>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">AW-</span>
                                    <input type="text" name="pixel_google" value="{{ old('pixel_google') }}" placeholder="Ex: 123456789" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-slate-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-shadow">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">TikTok Pixel</label>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">ID</span>
                                    <input type="text" name="pixel_tiktok" value="{{ old('pixel_tiktok') }}" placeholder="Ex: C1234567890123456789" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-slate-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-shadow">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
