@extends('layouts.base-cyberpunk')

@php
    use App\Helpers\ThemeHelper;
    use App\Helpers\LogoHelper;
    $themeColors = ThemeHelper::getThemeColors();
    $logoUrl = LogoHelper::getLogoUrl();
    $gatewayName = LogoHelper::getSystemName();
@endphp
@section('title', 'Login - ' . $gatewayName)

@section('content')
<div class="min-h-screen flex bg-slate-50 font-sans">
    <!-- Lado Esquerdo: Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-white border-r border-slate-200">
        <div class="absolute inset-0 bg-slate-50 opacity-50"></div>
        <div class="absolute top-20 left-20 w-96 h-96 rounded-full blur-3xl opacity-10" style="background-color: {{ $themeColors['primary'] }};"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 rounded-full blur-3xl opacity-10" style="background-color: {{ $themeColors['accent'] }}; animation-delay: 2s;"></div>
        
        <div class="relative z-10 flex flex-col justify-center items-center p-12 text-center w-full">
            <div class="mb-8 max-w-md">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-16 mx-auto mb-8 object-contain">
                @else
                    <div class="w-24 h-24 rounded-3xl flex items-center justify-center mb-6 shadow-xl mx-auto animate-float bg-white border border-slate-100">
                        <span class="font-black text-4xl bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">$</span>
                    </div>
                    <h1 class="text-5xl font-black text-slate-900 mb-4 tracking-tight">{{ $gatewayName }}</h1>
                @endif
                <p class="text-xl text-slate-600 leading-relaxed font-medium">
                    Acesse sua conta e transforme a forma como você recebe pagamentos
                </p>
                <div class="mt-12 grid grid-cols-3 gap-6 pt-8 border-t border-slate-200">
                    <div>
                        <div class="text-2xl font-bold text-blue-600 mb-1">99.9%</div>
                        <div class="text-sm text-slate-500 font-medium">Uptime</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600 mb-1">24/7</div>
                        <div class="text-sm text-slate-500 font-medium">Suporte</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600 mb-1">100%</div>
                        <div class="text-sm text-slate-500 font-medium">Seguro</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lado Direito: Formulário -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-slate-50 relative">
        
        <div class="w-full max-w-md relative z-10">
            <div class="lg:hidden text-center mb-8">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-12 mx-auto mb-4 object-contain">
                @else
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-white shadow-lg border border-slate-100">
                        <span class="font-black text-3xl bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">$</span>
                    </div>
                    <h1 class="text-3xl font-black text-slate-900 mb-2 tracking-tight">{{ $gatewayName }}</h1>
                @endif
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-200">
                <h2 class="text-3xl font-bold text-slate-900 mb-2 tracking-tight">Bem-vindo de volta</h2>
                <p class="text-slate-500 mb-8 font-medium">Entre com suas credenciais para continuar</p>

                @if(session('info'))
                    <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-medium">{{ session('info') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        <ul class="text-sm space-y-1 font-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                            E-mail
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                placeholder="seu@email.com"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-2">
                            Senha
                        </label>
                        <div class="relative" x-data="{ showPassword: false }">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input 
                                :type="showPassword ? 'text' : 'password'"
                                id="password" 
                                name="password" 
                                required
                                class="w-full pl-12 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                placeholder="••••••••"
                            >
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                            >
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 bg-white border-slate-300 rounded text-blue-600 focus:ring-blue-500 transition-colors">
                            <span class="ml-2 text-sm text-slate-500 font-medium select-none">Lembrar-me</span>
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-semibold transition-colors">Esqueci minha senha</a>
                    </div>

                    <button 
                        type="submit"
                        id="loginBtn"
                        class="w-full py-3 rounded-xl text-white font-bold text-lg hover:shadow-lg hover:shadow-blue-500/30 transition-all transform hover:scale-[1.02]"
                        style="background: linear-gradient(to right, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }});"
                    >
                        Entrar
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-slate-500 font-medium">
                        Não tem uma conta? 
                        <a href="{{ route('auth.register') }}" class="font-bold text-blue-600 hover:text-blue-700 transition-colors">Cadastre-se</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const rememberCheckbox = document.getElementById('remember');
        const loginForm = emailInput.closest('form');

        // Carregar email salvo
        const savedEmail = localStorage.getItem('remember_email');
        if (savedEmail) {
            emailInput.value = savedEmail;
            rememberCheckbox.checked = true;
        }

        // Salvar email ao enviar, se checkbox estiver marcado
        loginForm.addEventListener('submit', function() {
            if (rememberCheckbox.checked) {
                localStorage.setItem('remember_email', emailInput.value);
            } else {
                localStorage.removeItem('remember_email');
            }
        });
    });
</script>
@endsection
