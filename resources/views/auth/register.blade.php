@extends('layouts.base-cyberpunk')

@php
    use App\Helpers\ThemeHelper;
    use App\Helpers\LogoHelper;
    $themeColors = ThemeHelper::getThemeColors();
    $logoUrl = LogoHelper::getLogoUrl();
    $gatewayName = LogoHelper::getSystemName();
@endphp
@section('title', 'Cadastro - ' . $gatewayName)

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
                    <h1 class="text-5xl font-black text-slate-900 mb-4 tracking-tight">Junte-se a Nós</h1>
                @endif
                <p class="text-xl text-slate-600 leading-relaxed mb-8 font-medium">
                    Crie sua conta em minutos e comece a receber pagamentos hoje mesmo. Sem burocracia, sem complicação.
                </p>
                <div class="grid grid-cols-2 gap-6 pt-8 border-t border-slate-200">
                    <div>
                        <div class="text-2xl font-bold text-blue-600 mb-1">R$ 0</div>
                        <div class="text-sm text-slate-500 font-medium">Taxa de Setup</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600 mb-1">5min</div>
                        <div class="text-sm text-slate-500 font-medium">Para Começar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lado Direito: Formulário -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-slate-50 relative overflow-y-auto custom-scrollbar">
        
        <div class="w-full max-w-md relative z-10 my-8" x-data="{
            currentStep: 1,
            totalSteps: 4,
            formData: {
                name: '{{ old('name') }}',
                cpf_cnpj: '{{ old('cpf_cnpj') }}',
                birth_date: '{{ old('birth_date') }}',
                phone: '{{ old('phone') }}',
                email: '{{ old('email') }}',
                monthly_billing: '{{ old('monthly_billing') }}',
                password: '',
                password_confirmation: ''
            },
            errors: {},
            validateStep(step) {
                this.errors = {};
                if (step === 1) {
                    if (!this.formData.name || this.formData.name.length < 3) {
                        this.errors.name = 'Nome deve ter pelo menos 3 caracteres';
                        return false;
                    }
                    if (!this.formData.cpf_cnpj || this.formData.cpf_cnpj.length < 11) {
                        this.errors.cpf_cnpj = 'CPF/CNPJ inválido';
                        return false;
                    }
                    if (!this.formData.birth_date) {
                        this.errors.birth_date = 'Data de nascimento é obrigatória';
                        return false;
                    }
                }
                if (step === 2) {
                    if (!this.formData.phone || this.formData.phone.length < 10) {
                        this.errors.phone = 'Telefone inválido';
                        return false;
                    }
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!this.formData.email || !emailRegex.test(this.formData.email)) {
                        this.errors.email = 'Email inválido';
                        return false;
                    }
                }
                if (step === 3) {
                    if (!this.formData.monthly_billing || parseFloat(this.formData.monthly_billing) <= 0) {
                        this.errors.monthly_billing = 'Informe um valor válido';
                        return false;
                    }
                }
                if (step === 4) {
                    if (!this.formData.password || this.formData.password.length < 8) {
                        this.errors.password = 'Senha deve ter pelo menos 8 caracteres';
                        return false;
                    }
                    if (this.formData.password !== this.formData.password_confirmation) {
                        this.errors.password_confirmation = 'As senhas não coincidem';
                        return false;
                    }
                }
                return true;
            },
            nextStep() {
                if (this.validateStep(this.currentStep)) {
                    if (this.currentStep < this.totalSteps) {
                        this.currentStep++;
                    }
                }
            },
            previousStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                }
            },
            getProgress() {
                return (this.currentStep / this.totalSteps) * 100;
            },
            getStepTitle(step) {
                const titles = {
                    1: 'Dados Pessoais',
                    2: 'Informações de Contato',
                    3: 'Sobre Seu Negócio',
                    4: 'Segurança'
                };
                return titles[step] || '';
            }
        }">
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
                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight" x-text="getStepTitle(currentStep)"></h2>
                        <span class="text-sm text-slate-500 font-medium" x-text="`${currentStep} de ${totalSteps}`"></span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div 
                            class="h-2 rounded-full transition-all duration-500 ease-out" 
                            style="background: linear-gradient(to right, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }}); width: 0%;"
                            :style="`width: ${getProgress()}%`"
                        ></div>
                    </div>
                </div>

                <!-- Step Indicators -->
                <div class="flex justify-between mb-8">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex-1 flex items-center">
                            <div 
                                class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all"
                                :class="currentStep >= step ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-slate-100 border border-slate-200 text-slate-400'"
                                x-text="step"
                            ></div>
                            <div 
                                class="flex-1 h-0.5 mx-2 transition-all"
                                :class="currentStep > step ? 'bg-blue-600' : 'bg-slate-100'"
                                x-show="step < totalSteps"
                            ></div>
                        </div>
                    </template>
                </div>

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        <ul class="text-sm space-y-1 font-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.register') }}" id="registerForm">
                    @csrf
                    
                    <!-- Hidden inputs to preserve data -->
                    <input type="hidden" name="name" :value="formData.name">
                    <input type="hidden" name="cpf_cnpj" :value="formData.cpf_cnpj">
                    <input type="hidden" name="birth_date" :value="formData.birth_date">
                    <input type="hidden" name="phone" :value="formData.phone">
                    <input type="hidden" name="email" :value="formData.email">
                    <input type="hidden" name="monthly_billing" :value="formData.monthly_billing">
                    <input type="hidden" name="password" :value="formData.password">
                    <input type="hidden" name="password_confirmation" :value="formData.password_confirmation">

                    <!-- Step 1: Dados Pessoais -->
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700 mb-2">
                                Nome Completo <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                x-model="formData.name"
                                required
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                :class="errors.name ? 'border-red-500' : 'border-slate-200'"
                                placeholder="João Silva"
                            >
                            <p x-show="errors.name" class="mt-1 text-sm text-red-500 font-medium" x-text="errors.name"></p>
                        </div>

                        <div>
                            <label for="cpf_cnpj" class="block text-sm font-bold text-slate-700 mb-2">
                                CPF ou CNPJ <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="cpf_cnpj" 
                                x-model="formData.cpf_cnpj"
                                required
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                :class="errors.cpf_cnpj ? 'border-red-500' : 'border-slate-200'"
                                placeholder="000.000.000-00"
                            >
                            <p x-show="errors.cpf_cnpj" class="mt-1 text-sm text-red-500 font-medium" x-text="errors.cpf_cnpj"></p>
                        </div>

                        <div>
                            <label for="birth_date" class="block text-sm font-bold text-slate-700 mb-2">
                                Data de Nascimento <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="birth_date" 
                                x-model="formData.birth_date"
                                required
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                :class="errors.birth_date ? 'border-red-500' : 'border-slate-200'"
                            >
                            <p x-show="errors.birth_date" class="mt-1 text-sm text-red-500 font-medium" x-text="errors.birth_date"></p>
                        </div>
                    </div>

                    <!-- Step 2: Contato -->
                    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="space-y-5">
                        <div>
                            <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">
                                Celular <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                id="phone" 
                                x-model="formData.phone"
                                required
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                :class="errors.phone ? 'border-red-500' : 'border-slate-200'"
                                placeholder="(00) 00000-0000"
                            >
                            <p x-show="errors.phone" class="mt-1 text-sm text-red-500 font-medium" x-text="errors.phone"></p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                                E-mail <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                x-model="formData.email"
                                required
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                :class="errors.email ? 'border-red-500' : 'border-slate-200'"
                                placeholder="seu@email.com"
                            >
                            <p x-show="errors.email" class="mt-1 text-sm text-red-500 font-medium" x-text="errors.email"></p>
                        </div>
                    </div>

                    <!-- Step 3: Negócio -->
                    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="space-y-5">
                        <div>
                            <label for="monthly_billing" class="block text-sm font-bold text-slate-700 mb-2">
                                Faturamento Mensal Estimado (R$) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="monthly_billing" 
                                x-model="formData.monthly_billing"
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                :class="errors.monthly_billing ? 'border-red-500' : 'border-slate-200'"
                                placeholder="10000.00"
                            >
                            <p x-show="errors.monthly_billing" class="mt-1 text-sm text-red-500 font-medium" x-text="errors.monthly_billing"></p>
                            <p class="mt-2 text-xs text-slate-500 font-medium">Este valor nos ajuda a configurar o melhor plano para você</p>
                        </div>
                    </div>

                    <!-- Step 4: Segurança -->
                    <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="space-y-5">
                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-2">
                                Senha <span class="text-red-500">*</span>
                            </label>
                            <div class="relative" x-data="{ showPassword: false }">
                                <input 
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password" 
                                    x-model="formData.password"
                                    required
                                    class="w-full px-4 pr-12 py-3 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                    :class="errors.password ? 'border-red-500' : 'border-slate-200'"
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
                            <p x-show="errors.password" class="mt-1 text-sm text-red-500 font-medium" x-text="errors.password"></p>
                            <p class="mt-2 text-xs text-slate-500 font-medium">A senha deve ter pelo menos 8 caracteres</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">
                                Confirmar Senha <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                x-model="formData.password_confirmation"
                                required
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium"
                                :class="errors.password_confirmation ? 'border-red-500' : 'border-slate-200'"
                                placeholder="••••••••"
                            >
                            <p x-show="errors.password_confirmation" class="mt-1 text-sm text-red-500 font-medium" x-text="errors.password_confirmation"></p>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-sm text-slate-600 font-medium">
                                <span class="font-bold text-blue-600">⚠️ Importante:</span> Após o cadastro, você precisará enviar documentos para verificação (KYC) antes de poder utilizar todas as funcionalidades do sistema.
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-200">
                        <button 
                            type="button"
                            @click="previousStep()"
                            x-show="currentStep > 1"
                            class="px-6 py-3 rounded-xl font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-all"
                        >
                            ← Anterior
                        </button>
                        <div x-show="currentStep === 1"></div>
                        
                        <button 
                            type="button"
                            @click="nextStep()"
                            x-show="currentStep < totalSteps"
                            class="px-6 py-3 rounded-xl font-bold text-white transition-all hover:shadow-lg hover:shadow-blue-500/30 ml-auto"
                            style="background: linear-gradient(to right, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }});"
                        >
                            Próximo →
                        </button>
                        
                        <button 
                            type="submit"
                            x-show="currentStep === totalSteps"
                            class="w-full py-3 rounded-xl text-white font-bold text-lg hover:shadow-lg hover:shadow-blue-500/30 transition-all transform hover:scale-[1.02]"
                            style="background: linear-gradient(to right, {{ $themeColors['primary'] }}, {{ $themeColors['accent'] }});"
                        >
                            Criar Conta
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-slate-500 font-medium">
                        Já tem uma conta? 
                        <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-700 transition-colors">Fazer login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
