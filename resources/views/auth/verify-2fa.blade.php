@extends('layouts.base-cyberpunk')

@php
    $systemName = \App\Helpers\LogoHelper::getSystemName();
@endphp
@section('title', 'Verificar 2FA - ' . $systemName)

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="glass rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: {{ $themeColors['primary'] }}20;">
                    <svg class="w-10 h-10" style="color: {{ $themeColors['primary'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Verificação 2FA</h1>
                <p class="text-slate-400">Digite o código de 6 dígitos do seu aplicativo de autenticação</p>
            </div>

            @if(session('info'))
                <div class="mb-6 px-4 py-3 rounded-lg" style="background-color: {{ $themeColors['primary'] }}20; border: 1px solid {{ $themeColors['primary'] }}; color: {{ $themeColors['primary'] }};">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 px-4 py-3 rounded-lg" style="background-color: rgba(220, 38, 38, 0.2); border: 1px solid rgba(220, 38, 38, 0.5); color: rgba(220, 38, 38, 0.9);">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Código de Verificação</label>
                    <input 
                        type="text" 
                        name="code" 
                        maxlength="6"
                        pattern="[0-9]{6}"
                        required
                        autofocus
                        class="w-full px-4 py-3 bg-slate-800/50 border border-white/10 rounded-lg text-white text-center text-2xl tracking-widest placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-400"
                        placeholder="000000"
                        autocomplete="off"
                    >
                    <p class="mt-2 text-xs text-slate-500 text-center">Digite o código de 6 dígitos do Google Authenticator, Authy ou outro aplicativo</p>
                </div>

                <button 
                    type="submit"
                    class="w-full py-3 px-4 rounded-lg font-semibold text-white transition-all hover:shadow-lg hover:shadow-cyan-500/50"
                    style="background: linear-gradient(to right, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }});"
                >
                    Verificar e Continuar
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('logout') }}" class="text-sm text-slate-400 hover:text-white transition-colors">
                    Não é você? Fazer logout
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.querySelector('input[name="code"]');
        if (codeInput) {
            // Permite apenas números
            codeInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>
@endsection

