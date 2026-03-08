@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-20 md:pb-0">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-12 sm:mb-8 relative z-20">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Editar</h1>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto mt-2 sm:mt-0">
            <a href="{{ route('dashboard.checkout.index') }}" class="flex-1 sm:flex-none justify-center px-4 py-2.5 text-slate-600 hover:text-slate-800 font-medium transition-colors bg-white border border-slate-300 rounded-lg text-sm flex items-center gap-2 relative z-40">
                Cancelar
            </a>
            <button type="submit" form="product-form" class="flex-1 sm:flex-none justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all text-sm flex items-center gap-2 shadow-sm hover:shadow-md relative z-40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Salvar
            </button>
        </div>
    </div>

    <!-- Main Form -->
    <form id="product-form" action="{{ route('dashboard.checkout.update', $product->uuid) }}" method="POST" enctype="multipart/form-data" class="space-y-6 relative z-10">
        @csrf
        @method('PUT')

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
                            <input type="checkbox" name="is_active" class="sr-only peer" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
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
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-shadow" placeholder="Ex: E-book Premium" required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Preço</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-slate-500 sm:text-sm font-bold">R$</span>
                                    </div>
                                    <input type="number" name="price" step="0.01" value="{{ old('price', $product->price) }}" class="block w-full rounded-lg border-slate-300 pl-10 focus:border-blue-500 focus:ring-blue-500 transition-shadow" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Descrição</label>
                                <textarea name="description" rows="4" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-shadow resize-none" placeholder="Descreva seu produto para o cliente...">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Link de Download <span class="text-slate-400 font-normal">(Produto Digital)</span></label>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </span>
                                    <input type="url" name="download_url" value="{{ old('download_url', $product->download_url) }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-slate-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-shadow" placeholder="https://exemplo.com/download/arquivo.zip">
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

                        @php
                            $imageUrl = \Illuminate\Support\Str::startsWith($product->product_image, ['http://', 'https://']) ? $product->product_image : '';
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8" x-data='{ imageUrl: {{ json_encode(old("product_image_url", $imageUrl), JSON_HEX_APOS) }}, imageError: false }' x-init="$watch('imageUrl', () => imageError = false)">
                            <div class="md:col-span-2 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">URL da Imagem</label>
                                    <div class="flex rounded-md shadow-sm">
                                        <input type="url" name="product_image_url" 
                                            x-model="imageUrl"
                                            placeholder="https://exemplo.com/imagem.jpg" 
                                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border-slate-300 focus:border-purple-500 focus:ring-purple-500 sm:text-sm transition-shadow">
                                    </div>
                                    <p class="text-xs text-slate-500 mt-2">Cole o link direto da imagem do seu produto para exibição no checkout.</p>
                                </div>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Pré-visualização</label>
                                <div class="aspect-square w-full rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden relative group hover:border-purple-400 transition-colors" style="min-height: 200px;">
                                    <div x-show="imageUrl && !imageError" class="w-full h-full relative">
                                        <img :src="imageUrl" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" x-on:error="imageError = true" x-on:load="imageError = false">
                                    </div>
                                    <div x-show="!imageUrl || imageError" class="w-full h-full flex flex-col items-center justify-center text-center p-4">
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
                                    <input type="checkbox" name="enable_pix" class="sr-only peer" {{ old('enable_pix', $product->enable_pix) ? 'checked' : '' }}>
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
                                    <input type="checkbox" name="enable_credit_card" class="sr-only peer" {{ old('enable_credit_card', $product->enable_credit_card) ? 'checked' : '' }}>
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
                                <input type="checkbox" name="show_product_image" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5" {{ old('show_product_image', $product->show_product_image) ? 'checked' : '' }}>
                            </label>

                            <!-- Timer -->
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100">
                                <div>
                                    <h4 class="font-medium text-slate-800 text-sm">Timer de Escassez</h4>
                                    <p class="text-xs text-slate-500">Contador regressivo</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="number" name="timer_minutes" value="{{ old('timer_minutes', $product->timer_minutes) }}" class="w-16 rounded-lg border-slate-300 text-sm p-1.5 text-center" placeholder="Min">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="has_timer" class="sr-only peer" {{ old('has_timer', $product->has_timer) ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>

                            <!-- Security Badges -->
                            <label class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100 cursor-pointer">
                                <div>
                                    <h4 class="font-medium text-slate-800 text-sm">Selos de Segurança</h4>
                                    <p class="text-xs text-slate-500">Site seguro e criptografado</p>
                                </div>
                                <input type="checkbox" name="show_security_badges" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5" {{ old('show_security_badges', $product->show_security_badges) ? 'checked' : '' }}>
                            </label>

                            <!-- Warranty -->
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100">
                                <div>
                                    <h4 class="font-medium text-slate-800 text-sm">Garantia</h4>
                                    <p class="text-xs text-slate-500">Dias de garantia</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="number" name="warranty_days" value="{{ old('warranty_days', $product->warranty_days) }}" class="w-16 rounded-lg border-slate-300 text-sm p-1.5 text-center" placeholder="Dias">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="show_guarantee" class="sr-only peer" {{ old('show_guarantee', $product->show_guarantee) ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Rastreamento -->
                <div x-show="tab === 'rastreamento'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="space-y-6">
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Dica de Rastreamento</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Adicione os IDs dos seus pixels para acompanhar as conversões (InitiateCheckout e Purchase) automaticamente.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Pixel do Facebook</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </div>
                                <input type="text" name="pixel_facebook" value="{{ old('pixel_facebook', $product->pixel_facebook) }}" placeholder="Ex: 123456789012345" class="w-full rounded-lg border-slate-300 pl-10 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Google Ads ID</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.003 0C5.376 0 .003 5.373.003 12S5.376 24 12.003 24s12-5.373 12-12S18.63 0 12.003 0zm-.005 3.636c2.25 0 4.17.818 5.618 2.182l-2.727 2.727c-.682-.654-1.636-1.036-2.89-1.036-2.482 0-4.5 1.664-5.25 3.9h-2.946V8.182c1.473-2.918 4.473-4.546 8.195-4.546zm-5.25 7.91c-.19-.574-.3-1.182-.3-1.818s.109-1.245.3-1.818V4.636H3.807c-.6 1.182-.927 2.51-.927 3.91s.327 2.727.927 3.909l2.946-3.273zm5.25 8.182c-2.482 0-4.5-1.664-5.25-3.91l-2.946 2.273c1.473 2.918 4.473 4.545 8.196 4.545 2.25 0 4.145-.736 5.564-2.045l-2.727-2.127c-.736.518-1.745.873-2.837.873v.001zm8.7-3.982h-1.09V12h-7.61v3.273h4.39c-.19 1.091-.818 2.045-1.745 2.673l2.727 2.127c1.6-1.473 2.51-3.655 2.51-6.182 0-.6-.055-1.182-.164-1.727z"/></svg>
                                </div>
                                <input type="text" name="pixel_google" value="{{ old('pixel_google', $product->pixel_google) }}" placeholder="Ex: AW-123456789" class="w-full rounded-lg border-slate-300 pl-10 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">TikTok Pixel</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.65-1.55-1.09-.01 5.05.02 10.09-.01 15.14-.05 1.61-.6 3.22-1.78 4.42-1.52 1.56-3.79 2.13-5.88 1.67-1.92-.42-3.66-1.72-4.66-3.41-1.39-2.35-1.04-5.38.8-7.39 1.34-1.46 3.4-2.06 5.34-1.63v4.22c-1.62-.27-3.25.96-3.63 2.57-.36 1.54.54 3.09 2.05 3.55 1.5.46 3.08-.27 3.73-1.69.45-.98.42-2.13.43-3.21.02-6.52.01-13.04.01-19.56.02-.33-.3-.6-.61-.59-1.31.02-2.61.01-3.91.02h-.03z"/></svg>
                                </div>
                                <input type="text" name="pixel_tiktok" value="{{ old('pixel_tiktok', $product->pixel_tiktok) }}" placeholder="Ex: C1234567890" class="w-full rounded-lg border-slate-300 pl-10 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
