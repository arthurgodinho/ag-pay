@extends('layouts.app')

@php
    $systemName = \App\Helpers\LogoHelper::getSystemName();
@endphp
@section('title', 'Configurar 2FA - ' . $systemName)

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="max-w-4xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('dashboard.settings.index', ['tab' => 'security']) }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Autenticação de Dois Fatores (2FA)
            </h1>
            <p class="text-sm text-slate-500 mt-1">Configure a autenticação de dois fatores para maior segurança</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($user->google2fa_enabled)
        <!-- Desativar 2FA -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="shrink-0">
                        <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600 ring-4 ring-red-50/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-slate-800 mb-2">Desativar 2FA</h2>
                        <p class="text-sm text-slate-500 leading-relaxed mb-6">
                            Para sua segurança, é necessário confirmar a desativação com um código do seu aplicativo autenticador.
                        </p>
                        
                        <form method="POST" action="{{ route('2fa.disable') }}" class="max-w-sm">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Código de Verificação</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="code" 
                                            maxlength="6"
                                            pattern="[0-9]{6}"
                                            inputmode="numeric"
                                            required
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                                            placeholder="000000"
                                            autocomplete="off"
                                        >
                                    </div>
                                    <p class="mt-1.5 text-xs text-slate-400">Digite o código de 6 dígitos do seu app</p>
                                </div>
                                <button 
                                    type="submit"
                                    class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wide rounded-lg transition-all shadow-md hover:shadow-lg hover:shadow-red-600/20"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                    Confirmar Desativação
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Passo 1 -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">1</div>
                        <h2 class="text-lg font-bold text-slate-800">Escanear QR Code</h2>
                    </div>
                    
                    <p class="text-sm text-slate-500 mb-6">
                        Abra seu aplicativo autenticador (Google Authenticator, Authy, etc) e escaneie o código abaixo:
                    </p>
                    
                    <div class="flex justify-center mb-8">
                        <div class="bg-white p-3 rounded-xl border-2 border-slate-100 shadow-inner w-48 h-48 flex items-center justify-center overflow-hidden">
                            <div class="w-full h-full [&>svg]:w-full [&>svg]:h-full">
                                {!! $qrCodeUrl !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Chave de Configuração Manual</p>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded text-slate-600 font-mono text-xs break-all select-all">{{ $secret }}</code>
                            <button 
                                onclick="navigator.clipboard.writeText('{{ $secret }}');"
                                class="p-2 bg-white border border-slate-200 hover:bg-slate-50 hover:text-blue-600 rounded text-slate-400 transition-colors"
                                title="Copiar Chave"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passo 2 -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">2</div>
                        <h2 class="text-lg font-bold text-slate-800">Verificar Código</h2>
                    </div>

                    <p class="text-sm text-slate-500 mb-6">
                        Após escanear, digite o código de 6 dígitos gerado pelo aplicativo para confirmar a ativação:
                    </p>
                    
                    <form method="POST" action="{{ route('2fa.enable') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Código de Verificação</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </span>
                                    <input 
                                        type="text" 
                                        name="code" 
                                        maxlength="6"
                                        pattern="[0-9]{6}"
                                        inputmode="numeric"
                                        required
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                        placeholder="000000"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <button 
                                type="submit"
                                class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wide rounded-lg transition-all shadow-md hover:shadow-lg hover:shadow-blue-600/20"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Ativar Autenticação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

