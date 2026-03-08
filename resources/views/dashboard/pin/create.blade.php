<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        $systemName = \App\Helpers\LogoHelper::getSystemName();
    @endphp
    <title>Configurar PIN - {{ $systemName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @php
        $logoUrl = \App\Models\LandingPageSetting::get('logo_url', '');
    @endphp
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-100 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-100 rounded-full blur-3xl opacity-50"></div>
    </div>
    
    <div class="w-full max-w-md relative z-10">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            @if($logoUrl)
                <div class="flex justify-center mb-4">
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-16 w-auto object-contain">
                </div>
            @else
                <h1 class="text-5xl font-bold text-blue-600 mb-2">{{ $systemName }}</h1>
            @endif
            <p class="text-slate-600 text-lg">{{ $user->pin_configured ? 'Alterar seu PIN de segurança' : 'Configure seu PIN de segurança' }}</p>
        </div>

        <!-- Card de Configuração de PIN -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200" x-data="{ pin: '', pinConfirmation: '', currentPin: '', showPin: false, showPinConfirmation: false }">
            <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                <p class="text-sm text-blue-700">
                    <svg class="inline-block w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    O PIN é usado para alterar sua senha e realizar saques. Mantenha-o seguro!
                </p>
            </div>

            <form method="POST" action="{{ route('pin.store') }}">
                @csrf

                @if($user->pin_configured)
                    <!-- PIN Atual -->
                    <div class="mb-6">
                        <label for="current_pin" class="block text-sm font-medium text-slate-700 mb-2">
                            PIN Atual (6 dígitos) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="current_pin" 
                                name="current_pin" 
                                x-model="currentPin"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                required
                                autofocus
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-center text-2xl tracking-widest font-mono @error('current_pin') border-red-500 @enderror"
                                placeholder="000000"
                                @input="currentPin = currentPin.replace(/[^0-9]/g, '')"
                            >
                        </div>
                        @error('current_pin')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Novo PIN -->
                <div class="mb-6">
                    <label for="pin" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ $user->pin_configured ? 'Novo PIN' : 'PIN' }} (6 dígitos) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="pin" 
                            name="pin" 
                            x-model="pin"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            autofocus
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-center text-2xl tracking-widest font-mono @error('pin') border-red-500 @enderror"
                            placeholder="000000"
                            @input="pin = pin.replace(/[^0-9]/g, '')"
                        >
                    </div>
                    @error('pin')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-slate-500">Digite apenas números (6 dígitos)</p>
                </div>

                <!-- Confirmar PIN -->
                <div class="mb-6">
                    <label for="pin_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                        Confirmar PIN <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="pin_confirmation" 
                            name="pin_confirmation" 
                            x-model="pinConfirmation"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-center text-2xl tracking-widest font-mono @error('pin_confirmation') border-red-500 @enderror"
                            placeholder="000000"
                            @input="pinConfirmation = pinConfirmation.replace(/[^0-9]/g, '')"
                        >
                    </div>
                    @error('pin_confirmation')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-red-500" x-show="pin && pinConfirmation && pin !== pinConfirmation" x-transition>
                        Os PINs não coincidem
                    </p>
                </div>

                <!-- Botão Submit -->
                <button 
                    type="submit" 
                    :disabled="!pin || !pinConfirmation || pin.length !== 6 || pinConfirmation.length !== 6 || pin !== pinConfirmation{{ $user->pin_configured ? ' || !currentPin || currentPin.length !== 6' : '' }}"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-lg shadow-sm transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    {{ $user->pin_configured ? 'Alterar PIN' : 'Configurar PIN' }}
                </button>
            </form>
        </div>
    </div>
</body>
</html>
