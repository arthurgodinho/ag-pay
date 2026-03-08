@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-4 sm:py-6 md:py-8 px-3 sm:px-4 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Barra de Progresso -->
        <div class="mb-4 sm:mb-6 md:mb-8" x-data="kycWizard()">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="flex-1 flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full text-white font-bold text-sm sm:text-base" :class="step >= 1 ? 'bg-blue-600' : 'bg-slate-200 text-slate-500'">
                        <span x-show="step >= 1">✓</span>
                        <span x-show="step < 1">1</span>
                    </div>
                    <div class="flex-1 h-1 mx-1 sm:mx-2" :class="step >= 2 ? 'bg-blue-600' : 'bg-slate-200'"></div>
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full text-white font-bold text-sm sm:text-base" :class="step >= 2 ? 'bg-blue-600' : 'bg-slate-200 text-slate-500'">
                        <span x-show="step >= 2">✓</span>
                        <span x-show="step < 2">2</span>
                    </div>
                    <template x-if="facialBiometricsEnabled">
                        <div class="flex-1 flex items-center">
                            <div class="flex-1 h-1 mx-1 sm:mx-2" :class="step >= 3 ? 'bg-blue-600' : 'bg-slate-200'"></div>
                            <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full text-white font-bold text-sm sm:text-base" :class="step >= 3 ? 'bg-blue-600' : 'bg-slate-200 text-slate-500'">
                                <span x-show="step >= 3">✓</span>
                                <span x-show="step < 3">3</span>
                            </div>
                        </div>
                    </template>
                    <div class="flex-1 h-1 mx-1 sm:mx-2" :class="step >= 4 ? 'bg-blue-600' : 'bg-slate-200'"></div>
                    <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full text-white font-bold text-sm sm:text-base" :class="step >= 4 ? 'bg-blue-600' : 'bg-slate-200 text-slate-500'">
                        <span x-show="step >= 4">✓</span>
                        <span x-show="step < 4" x-text="facialBiometricsEnabled ? '4' : '3'"></span>
                    </div>
                </div>
            </div>
            <div class="flex justify-between text-xs sm:text-sm text-slate-500 px-1">
                <span class="text-center">Endereço</span>
                <span class="text-center">Documentos</span>
                <span class="text-center" x-show="facialBiometricsEnabled">Biometria</span>
                <span class="text-center">Status</span>
            </div>
        </div>

        <!-- Card Principal -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6 md:p-8" 
             x-data="kycWizard()" 
             x-init="init()">
            
            <!-- ETAPA 1: DADOS RESIDENCIAIS -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="mb-4 sm:mb-6">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900 mb-1 sm:mb-2">📍 Dados Residenciais</h2>
                    <p class="text-sm sm:text-base text-slate-500">Preencha seu endereço completo para continuar</p>
                </div>

                <form @submit.prevent="saveAddress" class="space-y-4 sm:space-y-5 md:space-y-6">
                    <!-- CEP -->
                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                            CEP <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input 
                                type="text" 
                                id="zip_code" 
                                x-model="address.zip_code"
                                @input="formatCep"
                                @blur="searchCep"
                                maxlength="10"
                                placeholder="00000-000"
                                required
                                class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-base transition-colors"
                            >
                            <button 
                                type="button"
                                @click="searchCep"
                                :disabled="loadingCep"
                                class="w-full sm:w-auto px-5 sm:px-6 py-2.5 sm:py-3 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base"
                            >
                                <span x-show="!loadingCep" class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Buscar
                                </span>
                                <span x-show="loadingCep" class="flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Rua -->
                    <div>
                        <label for="street" class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                            Rua <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="street" 
                            x-model="address.street"
                            required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-base transition-colors"
                        >
                    </div>

                    <!-- Número e Bairro -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
                        <div>
                            <label for="number" class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                                Número <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="number" 
                                x-model="address.number"
                                required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-base transition-colors"
                            >
                        </div>
                        <div>
                            <label for="neighborhood" class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                                Bairro <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="neighborhood" 
                                x-model="address.neighborhood"
                                required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-base transition-colors"
                            >
                        </div>
                    </div>

                    <!-- Cidade e Estado -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                                Cidade <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="city" 
                                x-model="address.city"
                                required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-base transition-colors"
                            >
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                                Estado (UF) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="state" 
                                x-model="address.state"
                                maxlength="2"
                                required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white uppercase text-base transition-colors"
                            >
                        </div>
                    </div>

                    <!-- Botão Próximo -->
                    <div class="flex justify-end pt-3 sm:pt-4">
                        <button 
                            type="submit"
                            :disabled="submitting"
                            class="w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold rounded-lg shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                        >
                            <span x-show="!submitting">Próximo</span>
                            <span x-show="submitting" class="flex items-center">
                                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Salvando...
                            </span>
                            <svg x-show="!submitting" class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ETAPA 2: DOCUMENTAÇÃO -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="mb-4 sm:mb-6">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900 mb-1 sm:mb-2">📄 Documentação</h2>
                    <p class="text-sm sm:text-base text-slate-500">Envie fotos claras e legíveis dos seus documentos</p>
                </div>

                <!-- Tipo de Pessoa Detectado -->
                <div class="mb-4 sm:mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">
                                <span x-show="personType === 'PF'">Pessoa Física (CPF) detectado</span>
                                <span x-show="personType === 'PJ'">Pessoa Jurídica (CNPJ) detectado</span>
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span x-show="personType === 'PF'">Documentos de pessoa física serão solicitados</span>
                                <span x-show="personType === 'PJ'">Documentos de pessoa jurídica serão solicitados</span>
                            </p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="saveDocs" class="space-y-4 sm:space-y-5 md:space-y-6">

                    <!-- Frente do Documento -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                            <span x-show="personType === 'PF'">Frente do RG ou CNH</span>
                            <span x-show="personType === 'PJ'">Frente do Documento da Empresa</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="file" 
                                id="document_front"
                                @change="handleFileSelect($event, 'document_front')"
                                accept="image/jpeg,image/png,image/jpg"
                                required
                                class="hidden"
                            >
                            <label for="document_front" class="flex flex-col items-center justify-center w-full h-40 sm:h-48 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-slate-50 transition-colors bg-white p-4">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-xs sm:text-sm text-slate-500 text-center px-2" x-text="files.document_front ? files.document_front.name : 'Clique para fazer upload'"></p>
                                <p class="text-xs text-slate-400 mt-1 text-center">JPG, PNG (máx. 5MB)</p>
                            </label>
                        </div>
                    </div>

                    <!-- Verso do Documento -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                            <span x-show="personType === 'PF'">Verso do RG ou CNH</span>
                            <span x-show="personType === 'PJ'">Verso do Documento da Empresa</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="file" 
                                id="document_back"
                                @change="handleFileSelect($event, 'document_back')"
                                accept="image/jpeg,image/png,image/jpg"
                                required
                                class="hidden"
                            >
                            <label for="document_back" class="flex flex-col items-center justify-center w-full h-40 sm:h-48 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-slate-50 transition-colors bg-white p-4">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-xs sm:text-sm text-slate-500 text-center px-2" x-text="files.document_back ? files.document_back.name : 'Clique para fazer upload'"></p>
                                <p class="text-xs text-slate-400 mt-1 text-center">JPG, PNG (máx. 5MB)</p>
                            </label>
                        </div>
                    </div>

                    <!-- Selfie segurando documento (sempre obrigatória) -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                            Selfie segurando o documento <span class="text-red-500">*</span>
                        </label>
                        <div class="mb-2 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                            <p class="text-xs text-blue-700">
                                <strong>Importante:</strong> Tire uma foto segurando seu documento (RG, CNH ou CNPJ) próximo ao seu rosto. 
                                O documento deve estar visível e legível, e seu rosto deve estar claramente visível.
                            </p>
                        </div>
                        <div class="relative">
                            <input 
                                type="file" 
                                id="selfie_with_doc"
                                @change="handleFileSelect($event, 'selfie_with_doc')"
                                accept="image/jpeg,image/png,image/jpg"
                                required
                                class="hidden"
                            >
                            <label for="selfie_with_doc" class="flex flex-col items-center justify-center w-full h-40 sm:h-48 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-slate-50 transition-colors bg-white p-4">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-xs sm:text-sm text-slate-500 text-center px-2" x-text="files.selfie_with_doc ? files.selfie_with_doc.name : 'Clique para fazer upload'"></p>
                                <p class="text-xs text-slate-400 mt-1 text-center">JPG, PNG (máx. 5MB)</p>
                            </label>
                        </div>
                    </div>

                    <!-- Comprovante CNPJ (condicional) -->
                    <div x-show="personType === 'PJ'">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                            Comprovante de Cartão CNPJ <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="file" 
                                id="cnpj_proof"
                                @change="handleFileSelect($event, 'cnpj_proof')"
                                accept="image/jpeg,image/png,image/jpg,application/pdf"
                                :required="personType === 'PJ'"
                                class="hidden"
                            >
                            <label for="cnpj_proof" class="flex flex-col items-center justify-center w-full h-40 sm:h-48 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-slate-50 transition-colors bg-white p-4">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-xs sm:text-sm text-slate-500 text-center px-2" x-text="files.cnpj_proof ? files.cnpj_proof.name : 'Clique para fazer upload'"></p>
                                <p class="text-xs text-slate-400 mt-1 text-center">JPG, PNG, PDF (máx. 5MB)</p>
                            </label>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 sm:gap-0 pt-3 sm:pt-4">
                        <button 
                            type="button"
                            @click="step = 1"
                            class="w-full sm:w-auto px-5 sm:px-6 py-2.5 sm:py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition-all duration-200 flex items-center justify-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Voltar
                        </button>
                        <button 
                            type="submit"
                            :disabled="submitting"
                            class="w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold rounded-lg shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                        >
                            <span x-show="!submitting">Próximo</span>
                            <span x-show="submitting" class="flex items-center">
                                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enviando...
                            </span>
                            <svg x-show="!submitting" class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ETAPA 3: BIOMETRIA FACIAL OTIMIZADA (apenas se biometria estiver ativada) -->
            <div x-show="step === 3 && facialBiometricsEnabled" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="mb-4 sm:mb-6">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900 mb-1 sm:mb-2">📸 Biometria Facial</h2>
                    <p class="text-sm sm:text-base text-slate-500">Posicione seu rosto na câmera e capture uma foto clara</p>
                </div>

                <div class="space-y-4 sm:space-y-5 md:space-y-6">
                    <!-- Instruções -->
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 sm:p-4">
                        <div class="flex items-start gap-2 sm:gap-3">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-xs sm:text-sm text-slate-600">
                                <p class="font-semibold text-slate-900 mb-1">Dicas para uma boa foto:</p>
                                <ul class="list-disc list-inside space-y-0.5 sm:space-y-1 text-slate-500">
                                    <li>Mantenha o rosto centralizado e bem iluminado</li>
                                    <li>Remova óculos, máscaras ou acessórios</li>
                                    <li>Olhe diretamente para a câmera</li>
                                    <li>Mantenha uma expressão neutra</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Área da Câmera Otimizada -->
                    <div class="flex justify-center">
                        <div class="relative w-full max-w-[320px] sm:max-w-[400px]">
                            <!-- Overlay de guia -->
                            <div x-show="cameraActive && !capturedImage" 
                                 class="absolute inset-0 pointer-events-none z-10 flex items-center justify-center">
                                <div class="w-40 h-40 sm:w-48 sm:h-48 rounded-full border-4 border-blue-400/50 border-dashed"></div>
                            </div>
                            
                            <!-- Video da câmera -->
                            <video 
                                x-ref="video"
                                autoplay
                                playsinline
                                class="hidden w-full aspect-square rounded-xl sm:rounded-2xl object-cover border-4 border-blue-500 shadow-lg"
                            ></video>
                            
                            <canvas x-ref="canvas" class="hidden"></canvas>
                            
                            <!-- Placeholder inicial -->
                            <div x-show="!cameraActive && !capturedImage" 
                                 class="w-full aspect-square rounded-xl sm:rounded-2xl bg-slate-50 border-4 border-slate-200 flex flex-col items-center justify-center shadow-inner">
                                <svg class="w-24 h-24 sm:w-32 sm:h-32 text-slate-300 mb-3 sm:mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-slate-400 text-xs sm:text-sm">Câmera não ativada</p>
                            </div>
                            
                            <!-- Preview da foto capturada -->
                            <div x-show="capturedImage" 
                                 class="relative w-full aspect-square rounded-xl sm:rounded-2xl overflow-hidden border-4 border-green-500 shadow-lg">
                                <img 
                                    :src="capturedImage"
                                    class="w-full h-full object-cover"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end justify-center pb-3 sm:pb-4">
                                    <div class="flex items-center gap-2 bg-blue-500/20 backdrop-blur-sm px-3 sm:px-4 py-1.5 sm:py-2 rounded-full border border-green-500/50">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-blue-400 font-semibold text-xs sm:text-sm">Foto capturada</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Controles da Câmera Otimizados -->
                    <div class="flex flex-col items-center gap-3 sm:gap-4">
                        <!-- Botão Ativar Câmera -->
                        <button 
                            x-show="!cameraActive && !capturedImage"
                            @click="startCamera"
                            class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold rounded-xl shadow-sm transition-all duration-200 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>Ativar Câmera</span>
                        </button>

                        <!-- Botão Capturar Foto -->
                        <button 
                            x-show="cameraActive && !capturedImage"
                            @click="capturePhoto"
                            class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-3.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold rounded-xl shadow-sm transition-all duration-200 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Capturar Foto</span>
                        </button>

                        <!-- Botões após captura -->
                        <div x-show="capturedImage" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button 
                                @click="retakePhoto"
                                class="w-full sm:w-auto px-5 sm:px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>Tirar Outra</span>
                            </button>

                            <button 
                                @click="saveBiometrics"
                                :disabled="submitting"
                                class="w-full sm:w-auto px-6 sm:px-8 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold rounded-xl shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <span x-show="!submitting">Enviar para Análise</span>
                                <span x-show="submitting" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Enviando...</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ETAPA 4: STATUS -->
            <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <!-- Em Análise -->
                <div x-show="status === 'pending'" class="text-center py-8 sm:py-10 md:py-12 px-4">
                    <div class="mb-4 sm:mb-6">
                        <svg class="animate-pulse w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mx-auto text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900 mb-3 sm:mb-4">Análise em Andamento</h2>
                    <p class="text-sm sm:text-base md:text-lg text-slate-500 mb-2">
                        Recebemos seus dados e nossa equipe está processando sua identidade.
                    </p>
                    <p class="text-xs sm:text-sm md:text-base text-slate-400">
                        O prazo médio é de 24 horas. Você receberá uma notificação quando sua verificação for aprovada.
                    </p>
                </div>

                <!-- Aprovado -->
                <div x-show="status === 'approved'" class="text-center py-8 sm:py-10 md:py-12 px-4">
                    <div class="mb-4 sm:mb-6">
                        <svg class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mx-auto text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900 mb-3 sm:mb-4">Conta Liberada!</h2>
                    <p class="text-sm sm:text-base md:text-lg text-slate-500 mb-5 sm:mb-6">
                        Sua verificação foi aprovada com sucesso. Você já pode usar todas as funcionalidades da plataforma.
                    </p>
                    <a href="{{ route('dashboard.index') }}" class="inline-block w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold rounded-lg shadow-sm transition-all duration-200">
                        Ir para Dashboard
                    </a>
                </div>

                <!-- Rejeitado -->
                <div x-show="status === 'rejected'" class="text-center py-8 sm:py-10 md:py-12 px-4">
                    <div class="mb-4 sm:mb-6">
                        <svg class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mx-auto text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900 mb-3 sm:mb-4 px-2">Não foi possível aprovar sua conta</h2>
                    <p class="text-sm sm:text-base md:text-lg text-slate-500 mb-2">
                        Analisamos seus dados e identificamos inconsistências.
                    </p>
                    <p class="text-sm sm:text-base text-red-500 font-semibold mb-5 sm:mb-6 px-2" x-text="rejectionReason ? 'Motivo: ' + rejectionReason : ''"></p>
                    <a href="{{ route('dashboard.manager-contact.index') }}" class="inline-block w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center mx-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Falar com meu Gerente
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function kycWizard() {
    return {
        step: {{ $user->kyc_step ?? 1 }},
        personType: '{{ \App\Helpers\DocumentHelper::isPessoaJuridica($user->cpf_cnpj ?? "") ? "PJ" : "PF" }}',
        status: '{{ $user->kyc_status ?? "pending" }}',
        rejectionReason: '{{ $user->rejection_reason ?? "" }}',
        facialBiometricsEnabled: {{ ($facialBiometricsEnabled ?? true) ? 'true' : 'false' }},
        submitting: false,
        loadingCep: false,
        cameraActive: false,
        capturedImage: null,
        stream: null,
        address: {
            zip_code: '{{ $user->zip_code ?? old("zip_code", "") }}',
            street: '{{ $user->street ?? old("street", "") }}',
            number: '{{ $user->number ?? old("number", "") }}',
            neighborhood: '{{ $user->neighborhood ?? old("neighborhood", "") }}',
            city: '{{ $user->city ?? old("city", "") }}',
            state: '{{ $user->state ?? old("state", "") }}',
        },
        files: {
            document_front: null,
            document_back: null,
            selfie_with_doc: null,
            cnpj_proof: null,
        },

        init() {
            // Se já está aprovado ou rejeitado, vai direto para status
            if (this.status === 'approved' || this.status === 'rejected') {
                this.step = 4;
            } else if (this.status === 'pending' && this.step === 4) {
                // Já enviou tudo, mostra status
                this.step = 4;
            }
        },

        formatCep() {
            let cep = this.address.zip_code.replace(/\D/g, '');
            if (cep.length > 5) {
                cep = cep.substring(0, 5) + '-' + cep.substring(5, 8);
            }
            this.address.zip_code = cep;
        },

        async searchCep() {
            const cep = this.address.zip_code.replace(/\D/g, '');
            if (cep.length !== 8) {
                alert('CEP inválido. Digite um CEP com 8 dígitos.');
                return;
            }

            this.loadingCep = true;
            try {
                const response = await fetch(`{{ url('/kyc/search-cep') }}/${cep}`);
                const data = await response.json();
                
                if (data.success) {
                    this.address.street = data.data.street || '';
                    this.address.neighborhood = data.data.neighborhood || '';
                    this.address.city = data.data.city || '';
                    this.address.state = data.data.state || '';
                } else {
                    alert(data.message || 'CEP não encontrado.');
                }
            } catch (error) {
                console.error('Erro ao buscar CEP:', error);
                alert('Erro ao buscar CEP. Tente novamente.');
            } finally {
                this.loadingCep = false;
            }
        },

        async saveAddress() {
            this.submitting = true;
            try {
                const response = await fetch('{{ route("kyc.storeAddress") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.address),
                });

                const data = await response.json();
                
                if (data.success) {
                    this.step = data.next_step;
                } else {
                    alert(data.message || 'Erro ao salvar endereço.');
                }
            } catch (error) {
                console.error('Erro ao salvar endereço:', error);
                alert('Erro ao salvar endereço. Tente novamente.');
            } finally {
                this.submitting = false;
            }
        },

        handleFileSelect(event, field) {
            const file = event.target.files[0];
            if (file) {
                // Valida tamanho (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert(`O arquivo ${file.name} excede o tamanho máximo de 5MB.`);
                    event.target.value = ''; // Limpa o input
                    this.files[field] = null;
                    return;
                }
                
                // Valida tipo de arquivo
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (field === 'cnpj_proof') {
                    allowedTypes.push('application/pdf');
                }
                
                if (!allowedTypes.includes(file.type)) {
                    alert(`Tipo de arquivo inválido. Use JPG, PNG${field === 'cnpj_proof' ? ' ou PDF' : ''}.`);
                    event.target.value = '';
                    this.files[field] = null;
                    return;
                }
                
                this.files[field] = file;
                console.log(`Arquivo ${field} selecionado:`, file.name, file.size, 'bytes');
            } else {
                this.files[field] = null;
            }
        },

        async saveDocs() {
            // Valida se todos os arquivos foram selecionados
            if (!this.files.document_front) {
                alert('Por favor, selecione a foto da frente do documento.');
                return;
            }
            
            if (!this.files.document_back) {
                alert('Por favor, selecione a foto do verso do documento.');
                return;
            }
            
            // Valida selfie segurando documento (sempre obrigatória)
            if (!this.files.selfie_with_doc) {
                alert('Por favor, selecione a foto da selfie segurando o documento.');
                return;
            }
            
            // Valida comprovante CNPJ apenas se for pessoa jurídica
            if (this.personType === 'PJ' && !this.files.cnpj_proof) {
                alert('Por favor, selecione o comprovante do CNPJ.');
                return;
            }

            this.submitting = true;
            try {
                const formData = new FormData();
                formData.append('person_type', this.personType);
                formData.append('document_front', this.files.document_front);
                formData.append('document_back', this.files.document_back);
                formData.append('selfie_with_doc', this.files.selfie_with_doc); // Sempre obrigatória
                
                // Adiciona comprovante CNPJ apenas se for pessoa jurídica
                if (this.personType === 'PJ' && this.files.cnpj_proof) {
                    formData.append('cnpj_proof', this.files.cnpj_proof);
                }

                // Debug: verifica se os arquivos estão no FormData
                console.log('Enviando documentos:', {
                    person_type: this.personType,
                    document_front: this.files.document_front?.name,
                    document_back: this.files.document_back?.name,
                    selfie_with_doc: this.files.selfie_with_doc?.name,
                    cnpj_proof: this.personType === 'PJ' ? this.files.cnpj_proof?.name : 'N/A (não é PJ)',
                    facial_biometrics_enabled: this.facialBiometricsEnabled,
                });

                const response = await fetch('{{ route("kyc.storeDocs") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();
                
                if (data.success) {
                    this.step = data.next_step;
                } else {
                    console.error('Erro ao enviar documentos:', data);
                    const errorMessage = data.errors 
                        ? Object.values(data.errors).flat().join(', ')
                        : data.message || 'Erro ao enviar documentos.';
                    alert(errorMessage);
                }
            } catch (error) {
                console.error('Erro ao enviar documentos:', error);
                alert('Erro ao enviar documentos. Tente novamente.');
            } finally {
                this.submitting = false;
            }
        },

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 640 }
                    } 
                });
                this.$refs.video.srcObject = this.stream;
                this.$refs.video.classList.remove('hidden');
                this.cameraActive = true;
                
                // Aguarda um frame para garantir que o vídeo está rodando
                await new Promise(resolve => {
                    this.$refs.video.onloadedmetadata = () => {
                        resolve();
                    };
                });
            } catch (error) {
                console.error('Erro ao acessar câmera:', error);
                let errorMessage = 'Erro ao acessar a câmera. ';
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    errorMessage += 'Por favor, permita o acesso à câmera nas configurações do navegador.';
                } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                    errorMessage += 'Nenhuma câmera foi encontrada no dispositivo.';
                } else {
                    errorMessage += 'Verifique as permissões do navegador e tente novamente.';
                }
                alert(errorMessage);
            }
        },

        capturePhoto() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            const context = canvas.getContext('2d');

            // Garante que o vídeo está pronto
            if (video.readyState !== video.HAVE_ENOUGH_DATA) {
                alert('Aguarde a câmera carregar completamente antes de capturar.');
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Captura com qualidade otimizada
            this.capturedImage = canvas.toDataURL('image/jpeg', 0.92);
            
            // Para o stream
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.cameraActive = false;
            video.classList.add('hidden');
            
            // Feedback visual
            setTimeout(() => {
                // Scroll suave para a foto capturada
                this.$refs.video?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        },

        retakePhoto() {
            this.capturedImage = null;
            this.startCamera();
        },

        async saveBiometrics() {
            if (!this.capturedImage) {
                alert('Por favor, capture uma foto primeiro.');
                return;
            }

            this.submitting = true;
            try {
                const response = await fetch('{{ route("kyc.storeBiometrics") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        biometric_image: this.capturedImage,
                    }),
                });

                const data = await response.json();
                
                if (data.success) {
                    this.step = 4;
                    this.status = 'pending';
                } else {
                    alert(data.message || 'Erro ao enviar biometria.');
                }
            } catch (error) {
                alert('Erro ao enviar biometria. Tente novamente.');
            } finally {
                this.submitting = false;
            }
        },
    }
}
</script>
@endsection
