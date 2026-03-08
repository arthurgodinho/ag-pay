@extends('layouts.app')

@section('title', 'Configurações')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                <div class="p-2 bg-blue-600 rounded-lg shadow-sm text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                {{ __('settings.title') }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">Gerencie suas informações pessoais e preferências de conta</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-1.5 inline-flex flex-wrap gap-1">
        <a href="{{ route('dashboard.settings.index', ['tab' => 'personal']) }}" 
           class="px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-all {{ $tab === 'personal' ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            {{ __('settings.personal') }}
        </a>
        <a href="{{ route('dashboard.settings.index', ['tab' => 'security']) }}" 
           class="px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-all {{ $tab === 'security' ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
            Segurança
        </a>
        <a href="{{ route('dashboard.settings.index', ['tab' => 'limits']) }}" 
           class="px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-all {{ $tab === 'limits' ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            {{ __('settings.limits') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium text-sm">{{ session('error') }}</span>
        </div>
    @endif

    @if(session('new_token') && session('new_client_id'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-4 rounded-xl shadow-sm">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 mt-0.5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-bold text-lg mb-2 text-blue-900">Credenciais criadas com sucesso!</p>
                    <p class="text-sm mb-4 text-blue-800">Copie e guarde estas credenciais em um local seguro. Você não poderá ver o token novamente!</p>
                    
                    <div class="space-y-3">
                        <div class="bg-white rounded-lg border border-blue-200 p-3">
                            <p class="text-xs text-slate-500 mb-1 font-semibold uppercase">Client ID</p>
                            <div class="flex items-center justify-between gap-2">
                                <code class="text-slate-700 font-mono text-sm break-all">{{ session('new_client_id') }}</code>
                                <button onclick="copyToClipboard('{{ session('new_client_id') }}')" class="p-1 hover:bg-slate-100 rounded text-slate-400 hover:text-blue-600 transition-colors" title="Copiar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg border border-blue-200 p-3">
                            <p class="text-xs text-slate-500 mb-1 font-semibold uppercase">Token</p>
                            <div class="flex items-center justify-between gap-2">
                                <code class="text-slate-700 font-mono text-sm break-all">{{ session('new_token') }}</code>
                                <button onclick="copyToClipboard('{{ session('new_token') }}')" class="p-1 hover:bg-slate-100 rounded text-slate-400 hover:text-blue-600 transition-colors" title="Copiar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab Content -->
    <div class="mt-4">
        @if($tab === 'personal')
            @include('dashboard.settings.personal')
        @elseif($tab === 'security')
            @include('dashboard.settings.security')
        @elseif($tab === 'limits')
            @include('dashboard.settings.limits')
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Custom toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 z-50 flex items-center gap-2 font-medium';
        toast.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Copiado com sucesso!</span>
        `;
        document.body.appendChild(toast);
        
        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });
        
        setTimeout(() => {
            toast.style.transform = 'translateY(100%)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }, function(err) {
        console.error('Erro ao copiar:', err);
    });
}
</script>
@endsection
