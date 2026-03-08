@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="space-y-4 sm:space-y-6 px-3 sm:px-0" x-data="{ activeTab: 'info' }">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">{{ __('profile.title') }}</h1>
        <p class="text-sm sm:text-base text-slate-500 mt-1">{{ __('profile.subtitle') }}</p>
    </div>

    @if(session('success'))
        <div class="bg-blue-50 border border-emerald-200 text-blue-600 px-4 py-3 rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-slate-200 overflow-x-auto">
        <div class="flex space-x-2 sm:space-x-4 md:space-x-8 min-w-max">
            <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-3 sm:px-4 py-2.5 sm:py-3 font-semibold transition-colors text-sm sm:text-base whitespace-nowrap active:scale-95">
                {{ __('profile.information') }}
            </button>
            <button @click="activeTab = 'fees'" :class="activeTab === 'fees' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-3 sm:px-4 py-2.5 sm:py-3 font-semibold transition-colors text-sm sm:text-base whitespace-nowrap active:scale-95">
                {{ __('profile.fees') }}
            </button>
            <button @click="activeTab = 'password'" :class="activeTab === 'password' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-3 sm:px-4 py-2.5 sm:py-3 font-semibold transition-colors text-sm sm:text-base whitespace-nowrap active:scale-95">
                {{ __('profile.change_password') }}
            </button>
            <button @click="activeTab = 'stats'" :class="activeTab === 'stats' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-3 sm:px-4 py-2.5 sm:py-3 font-semibold transition-colors text-sm sm:text-base whitespace-nowrap active:scale-95">
                {{ __('profile.statistics') }}
            </button>
        </div>
    </div>

    <!-- Tab: Informações -->
    <div x-show="activeTab === 'info'" class="space-y-4 sm:space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5 md:p-6 border border-slate-200">
            <h2 class="text-lg sm:text-xl font-semibold text-slate-800 mb-4 sm:mb-5 md:mb-6">Informações Pessoais</h2>
            
            <form method="POST" action="{{ route('dashboard.profile.update') }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5 sm:mb-2">
                            Nome Completo
                            <span class="text-xs text-slate-400 ml-1 sm:ml-2">(Não editável)</span>
                        </label>
                        <input 
                            type="text" 
                            value="{{ $user->name }}"
                            readonly
                            disabled
                            class="w-full px-3 sm:px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed text-base"
                        >
                        <p class="text-xs text-slate-400 mt-1">Para alterar seu nome, entre em contato com o suporte</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5 sm:mb-2">E-mail</label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ $user->email }}"
                            required
                            class="w-full px-3 sm:px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">
                            CPF/CNPJ
                            <span class="text-xs text-slate-400 ml-2">(Não editável)</span>
                        </label>
                        <input 
                            type="text" 
                            value="{{ $user->cpf_cnpj ?? 'Não informado' }}"
                            readonly
                            disabled
                            class="w-full px-3 sm:px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed text-base"
                        >
                        <p class="text-xs text-slate-400 mt-1">Para alterar seu CPF/CNPJ, entre em contato com o suporte</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5 sm:mb-2">
                            Data de Nascimento
                            <span class="text-xs text-slate-400 ml-1 sm:ml-2">(Não editável)</span>
                        </label>
                        <input 
                            type="text" 
                            value="{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d/m/Y') : 'Não informado' }}"
                            readonly
                            disabled
                            class="w-full px-3 sm:px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed text-base"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5 sm:mb-2">Status KYC</label>
                        <div class="px-3 sm:px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg">
                            <span class="px-2 py-1 text-xs rounded-full font-medium {{ $user->kyc_status === 'approved' ? 'bg-blue-100 text-blue-700' : ($user->kyc_status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($user->kyc_status ?? 'Não iniciado') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 sm:mt-6">
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold px-5 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all shadow-sm hover:shadow-md">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab: Taxas -->
    <div x-show="activeTab === 'fees'" class="space-y-4 sm:space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
            <!-- Taxas Cash-In -->
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5 md:p-6 border border-slate-200">
                <h3 class="text-base sm:text-lg font-semibold text-slate-800 mb-3 sm:mb-4">Taxas de Cash-In (Entrada)</h3>
                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <p class="text-sm text-slate-500 mb-2">Taxa Fixa</p>
                        <p class="text-2xl font-bold text-blue-600">
                            R$ {{ number_format($cashinPixFixo, 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">Taxa padrão do sistema</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 mb-2">Taxa Percentual</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ number_format($cashinPixPercentual, 2, ',', '.') }}%
                        </p>
                        <p class="text-xs text-slate-400 mt-1">Taxa padrão do sistema</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-sm text-slate-500 mb-2">Exemplo para R$ 100,00:</p>
                        <p class="text-xl font-bold text-slate-800">
                            R$ {{ number_format($cashinPixFixo + (100 * $cashinPixPercentual / 100), 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Taxas Cash-Out -->
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5 md:p-6 border border-slate-200">
                <h3 class="text-base sm:text-lg font-semibold text-slate-800 mb-3 sm:mb-4">Taxas de Cash-Out (Saída)</h3>
                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <p class="text-sm text-slate-500 mb-2">Taxa Fixa</p>
                        <p class="text-2xl font-bold text-blue-600">
                            R$ {{ number_format($cashoutPixFixo, 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">Taxa padrão do sistema</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 mb-2">Taxa Percentual</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ number_format($cashoutPixPercentual, 2, ',', '.') }}%
                        </p>
                        <p class="text-xs text-slate-400 mt-1">Taxa padrão do sistema</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-sm text-slate-500 mb-2">Exemplo para R$ 100,00:</p>
                        <p class="text-xl font-bold text-slate-800">
                            R$ {{ number_format($cashoutPixFixo + (100 * $cashoutPixPercentual / 100), 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Alterar Senha -->
    <div x-show="activeTab === 'password'" class="space-y-4 sm:space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5 md:p-6 border border-slate-200">
            <h2 class="text-lg sm:text-xl font-semibold text-slate-800 mb-4 sm:mb-5 md:mb-6">Alterar Senha</h2>
            
            <form method="POST" action="{{ route('dashboard.profile.password') }}" x-data="{ showCurrentPassword: false, showNewPassword: false, showConfirmPassword: false }">
                @csrf
                
                <div class="space-y-4 sm:space-y-5 md:space-y-6 max-w-md">
                    <!-- Senha Atual -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-slate-600 mb-1.5 sm:mb-2">
                            Senha Atual <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="current_password" 
                                name="current_password" 
                                :type="showCurrentPassword ? 'text' : 'password'"
                                required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-500 @enderror text-base"
                                placeholder="Digite sua senha atual"
                            >
                            <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg x-show="!showCurrentPassword" class="h-5 w-5 text-slate-400 hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showCurrentPassword" class="h-5 w-5 text-slate-400 hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nova Senha -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-600 mb-1.5 sm:mb-2">
                            Nova Senha <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                :type="showNewPassword ? 'text' : 'password'"
                                required
                                minlength="8"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror text-base"
                                placeholder="Mínimo 8 caracteres"
                            >
                            <button type="button" @click="showNewPassword = !showNewPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg x-show="!showNewPassword" class="h-5 w-5 text-slate-400 hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showNewPassword" class="h-5 w-5 text-slate-400 hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Nova Senha -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-600 mb-2">
                            Confirmar Nova Senha <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                :type="showConfirmPassword ? 'text' : 'password'"
                                required
                                minlength="8"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                                placeholder="Digite a nova senha novamente"
                            >
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg x-show="!showConfirmPassword" class="h-5 w-5 text-slate-400 hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showConfirmPassword" class="h-5 w-5 text-slate-400 hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- PIN -->
                    <div>
                        <label for="pin" class="block text-sm font-medium text-slate-600 mb-1.5 sm:mb-2">
                            Confirmar PIN (6 dígitos) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="pin" 
                            name="pin" 
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-xl sm:text-2xl tracking-widest font-mono @error('pin') border-red-500 @enderror text-base"
                            placeholder="000000"
                            @input="this.value = this.value.replace(/[^0-9]/g, '')"
                        >
                        <p class="mt-2 text-xs text-slate-500">Digite seu PIN de 6 dígitos para confirmar a alteração</p>
                        @error('pin')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-600">
                            <svg class="inline-block w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Para alterar sua senha, você precisa confirmar com seu PIN de 6 dígitos.
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        Alterar Senha
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab: Estatísticas -->
    <div x-show="activeTab === 'stats'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
                <p class="text-slate-500 text-sm mb-2">Total de Transações</p>
                <p class="text-3xl font-bold text-slate-800">{{ number_format($totalTransactions, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
                <p class="text-slate-500 text-sm mb-2">Volume Total</p>
                <p class="text-3xl font-bold text-blue-600">R$ {{ number_format($totalVolume, 2, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
                <p class="text-slate-500 text-sm mb-2">Total de Saques</p>
                <p class="text-3xl font-bold text-blue-600">{{ number_format($totalWithdrawals, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
