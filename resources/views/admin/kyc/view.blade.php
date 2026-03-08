@extends('layouts.admin')

@section('title', 'Visualizar Documentos KYC')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    use App\Helpers\DocumentHelper;
    $themeColors = ThemeHelper::getThemeColors();
    $isPessoaJuridica = DocumentHelper::isPessoaJuridica($user->cpf_cnpj ?? '');
@endphp

<div class="space-y-4" x-data="{ 
    modalOpen: false, 
    imgSrc: '', 
    imgTitle: '',
    openModal(src, title) {
        this.imgSrc = src;
        this.imgTitle = title;
        this.modalOpen = true;
        document.body.style.overflow = 'hidden';
    },
    closeModal() {
        this.modalOpen = false;
        document.body.style.overflow = 'auto';
    }
}">
    <!-- Header & Info Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-slate-800 truncate">{{ $user->name }}</h1>
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500 mt-0.5">
                        <span class="flex items-center gap-1 truncate">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $user->email }}
                        </span>
                        <span class="text-slate-300">|</span>
                        <span class="flex items-center gap-1 truncate">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.896 1.763-2.25 2M15 11h3m-3 4h2" />
                            </svg>
                            {{ $user->cpf_cnpj ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                @if($user->kyc_status === 'pending')
                    <span class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg border border-amber-200 text-xs font-bold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Aguardando Análise
                    </span>
                @elseif($user->kyc_status === 'approved')
                    <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg border border-blue-200 text-xs font-bold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Aprovado
                    </span>
                @elseif($user->kyc_status === 'rejected')
                    <span class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg border border-red-200 text-xs font-bold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        Rejeitado
                    </span>
                @else
                    <span class="px-3 py-1.5 bg-slate-100 text-slate-500 rounded-lg border border-slate-200 text-xs font-bold">
                        Não Enviado
                    </span>
                @endif
                
                <a href="{{ route('admin.kyc.index') }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Galeria de Documentos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        
        <!-- Frente do RG/CNH -->
        @if(isset($documents['front']))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            @php
                $frontSrc = (strpos($documents['front'], 'IMG/') === 0) ? asset($documents['front']) : route('admin.kyc.document', ['userId' => $user->id, 'type' => 'front']);
            @endphp
            <div class="aspect-[4/3] bg-slate-100 relative cursor-pointer group"
                 @click="openModal('{{ $frontSrc }}', 'Frente do RG/CNH')">
                <img src="{{ $frontSrc }}" 
                     alt="Frente do documento" 
                     class="w-full h-full object-cover"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Erro+ao+carregar'">
                
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                    <span class="sr-only">Ampliar</span>
                </div>
                <div class="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] px-2 py-1 rounded-full backdrop-blur-sm">
                    Toque para ampliar
                </div>
            </div>
            <div class="p-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-700">Frente do Documento</span>
                <a href="{{ $frontSrc }}" target="_blank" class="text-blue-600 p-1 hover:bg-blue-50 rounded">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
        @endif

        <!-- Verso do RG/CNH -->
        @if(isset($documents['back']))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            @php
                $backSrc = (strpos($documents['back'], 'IMG/') === 0) ? asset($documents['back']) : route('admin.kyc.document', ['userId' => $user->id, 'type' => 'back']);
            @endphp
            <div class="aspect-[4/3] bg-slate-100 relative cursor-pointer group"
                 @click="openModal('{{ $backSrc }}', 'Verso do RG/CNH')">
                <img src="{{ $backSrc }}" 
                     alt="Verso do documento" 
                     class="w-full h-full object-cover"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Erro+ao+carregar'">
                
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                    <span class="sr-only">Ampliar</span>
                </div>
                <div class="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] px-2 py-1 rounded-full backdrop-blur-sm">
                    Toque para ampliar
                </div>
            </div>
            <div class="p-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-700">Verso do Documento</span>
                <a href="{{ $backSrc }}" target="_blank" class="text-blue-600 p-1 hover:bg-blue-50 rounded">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
        @endif

        <!-- Selfie -->
        @if(isset($documents['selfie']))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            @php
                $selfieSrc = (strpos($documents['selfie'], 'IMG/') === 0) ? asset($documents['selfie']) : route('admin.kyc.document', ['userId' => $user->id, 'type' => 'selfie']);
            @endphp
            <div class="aspect-[4/3] bg-slate-100 relative cursor-pointer group"
                 @click="openModal('{{ $selfieSrc }}', 'Selfie com Documento')">
                <img src="{{ $selfieSrc }}" 
                     alt="Selfie" 
                     class="w-full h-full object-cover"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Erro+ao+carregar'">
                
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                    <span class="sr-only">Ampliar</span>
                </div>
                <div class="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] px-2 py-1 rounded-full backdrop-blur-sm">
                    Toque para ampliar
                </div>
            </div>
            <div class="p-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-700">Selfie</span>
                <a href="{{ $selfieSrc }}" target="_blank" class="text-blue-600 p-1 hover:bg-blue-50 rounded">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
        @endif

        <!-- Biometria Facial -->
        @if(isset($documents['biometric']) || $user->facial_biometrics)
            @php
                $biometricPath = isset($documents['biometric']) ? $documents['biometric'] : $user->facial_biometrics;
                $biometricUrl = (strpos($biometricPath, 'IMG/') === 0) ? asset($biometricPath) : route('admin.kyc.document', ['userId' => $user->id, 'type' => 'biometric']);
            @endphp
            @if($biometricUrl)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="aspect-[4/3] bg-slate-100 relative cursor-pointer group"
                     @click="openModal('{{ $biometricUrl }}', 'Biometria Facial')">
                    <img src="{{ $biometricUrl }}" 
                         alt="Biometria" 
                         class="w-full h-full object-cover"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Erro+ao+carregar'">
                    
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                        <span class="sr-only">Ampliar</span>
                    </div>
                    <div class="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] px-2 py-1 rounded-full backdrop-blur-sm">
                        Toque para ampliar
                    </div>
                </div>
                <div class="p-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-700">Biometria</span>
                    <a href="{{ $biometricUrl }}" target="_blank" class="text-blue-600 p-1 hover:bg-blue-50 rounded">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
            @endif
        @endif

        <!-- Comprovante CNPJ -->
        @if($isPessoaJuridica && isset($documents['cnpj_proof']))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            @php
                $filePath = $documents['cnpj_proof'];
                $isPdf = strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'pdf';
                $cnpjUrl = (strpos($documents['cnpj_proof'], 'IMG/') === 0) ? asset($documents['cnpj_proof']) : route('admin.kyc.document', ['userId' => $user->id, 'type' => 'cnpj_proof']);
            @endphp
            
            @if($isPdf)
                <div class="aspect-[4/3] bg-slate-50 relative flex flex-col items-center justify-center p-4">
                    <svg class="w-12 h-12 text-red-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <p class="text-xs font-medium text-slate-500">Documento PDF</p>
                    <a href="{{ $cnpjUrl }}" target="_blank" class="mt-3 px-3 py-1.5 bg-white border border-slate-200 rounded text-xs font-medium text-slate-600 hover:text-slate-900 transition-colors">
                        Visualizar
                    </a>
                </div>
            @else
                <div class="aspect-[4/3] bg-slate-100 relative cursor-pointer group"
                     @click="openModal('{{ $cnpjUrl }}', 'Comprovante do CNPJ')">
                    <img src="{{ $cnpjUrl }}" 
                         alt="Comprovante CNPJ" 
                         class="w-full h-full object-cover"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Erro+ao+carregar'">
                    
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                        <span class="sr-only">Ampliar</span>
                    </div>
                    <div class="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] px-2 py-1 rounded-full backdrop-blur-sm">
                        Toque para ampliar
                    </div>
                </div>
            @endif
            
            <div class="p-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-700">CNPJ</span>
                <a href="{{ $cnpjUrl }}" target="_blank" class="text-blue-600 p-1 hover:bg-blue-50 rounded">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Ações Finais (Footer Fixo Mobile) -->
    @if($user->kyc_status === 'pending')
    <div class="fixed bottom-0 left-0 right-0 p-3 bg-white border-t border-slate-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-40">
        <div class="max-w-7xl mx-auto grid grid-cols-2 gap-3">
            <form method="POST" action="{{ route('admin.kyc.reject', $user->id) }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full px-4 py-2.5 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 active:scale-95 transition-all"
                    onclick="return confirm('Tem certeza que deseja rejeitar o KYC deste usuário?')"
                >
                    Rejeitar
                </button>
            </form>
            
            <form method="POST" action="{{ route('admin.kyc.approve', $user->id) }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-lg shadow-md shadow-blue-500/30 active:scale-95 hover:bg-blue-700 transition-all"
                    onclick="return confirm('Tem certeza que deseja aprovar o KYC deste usuário?')"
                >
                    Aprovar
                </button>
            </form>
        </div>
    </div>
    <!-- Espaçador para o footer fixo -->
    <div class="h-16"></div>
    @endif

    <!-- Image Modal / Lightbox Otimizado -->
    <div x-show="modalOpen" 
         class="fixed inset-0 z-[60] overflow-y-auto" 
         style="display: none;"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/95 transition-opacity" @click="closeModal()"></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-center justify-center p-2 text-center">
            <div class="relative w-full max-w-4xl"
                 x-show="modalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <!-- Close Button -->
                <button @click="closeModal()" class="absolute -top-10 right-0 p-2 text-white/80 hover:text-white">
                    <span class="sr-only">Fechar</span>
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Content -->
                <div class="flex flex-col items-center">
                    <img :src="imgSrc" :alt="imgTitle" class="max-h-[80vh] w-auto rounded shadow-2xl object-contain">
                    <h3 class="mt-3 text-sm font-medium text-white/90" x-text="imgTitle"></h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
