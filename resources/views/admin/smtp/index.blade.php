@extends('layouts.admin')

@section('title', 'Configurações SMTP')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
            <div class="p-2 bg-blue-50 rounded-xl">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            Configurações SMTP
        </h1>
        <p class="text-slate-500 mt-2 ml-14">Configure o servidor de email para envio automático de emails</p>
    </div>

    <!-- Formulário de Configuração -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8">
        <form action="{{ route('admin.smtp.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Status Ativo -->
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <div>
                    <h3 class="text-slate-900 font-semibold">Status do SMTP</h3>
                    <p class="text-slate-500 text-sm">Ativar ou desativar o envio de emails</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ (isset($smtp) && $smtp->is_active) ? 'checked' : '' }}>
                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mailer -->
                <div>
                    <label class="block text-slate-700 font-medium mb-2">Tipo de Mailer</label>
                    <select name="mailer" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                        <option value="smtp" {{ ($smtp->mailer ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ ($smtp->mailer ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="mailgun" {{ ($smtp->mailer ?? '') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        <option value="ses" {{ ($smtp->mailer ?? '') === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                        <option value="postmark" {{ ($smtp->mailer ?? '') === 'postmark' ? 'selected' : '' }}>Postmark</option>
                    </select>
                </div>

                <!-- Host -->
                <div>
                    <label class="block text-slate-700 font-medium mb-2">Servidor SMTP (Host)</label>
                    <input type="text" name="host" value="{{ $smtp->host ?? 'smtp.gmail.com' }}" placeholder="smtp.gmail.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    @error('host')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Porta -->
                <div>
                    <label class="block text-slate-700 font-medium mb-2">Porta</label>
                    <input type="number" name="port" value="{{ $smtp->port ?? 587 }}" placeholder="587" min="1" max="65535" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    @error('port')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Encryption -->
                <div>
                    <label class="block text-slate-700 font-medium mb-2">Criptografia</label>
                    <select name="encryption" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                        <option value="tls" {{ ($smtp->encryption ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($smtp->encryption ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="null" {{ empty($smtp->encryption ?? null) ? 'selected' : '' }}>Nenhuma</option>
                    </select>
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-slate-700 font-medium mb-2">Usuário (Email)</label>
                    <input type="email" name="username" value="{{ $smtp->username ?? '' }}" placeholder="seu-email@gmail.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    @error('username')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-slate-700 font-medium mb-2">Senha</label>
                    <input type="password" name="password" placeholder="Deixe vazio para manter a atual" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    <p class="text-slate-500 text-xs mt-1">Deixe em branco para manter a senha atual</p>
                    @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- From Address -->
                <div>
                    <label class="block text-slate-700 font-medium mb-2">Email Remetente</label>
                    <input type="email" name="from_address" value="{{ $smtp->from_address ?? '' }}" placeholder="noreply@seudominio.com" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    @error('from_address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- From Name -->
                <div>
                    <label class="block text-slate-700 font-medium mb-2">Nome do Remetente</label>
                    <input type="text" name="from_name" value="{{ $smtp->from_name ?? config('app.name') }}" placeholder="{{ config('app.name') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    @error('from_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Informações de Ajuda -->
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                <h3 class="text-blue-900 font-semibold mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informações de Configuração
                </h3>
                <div class="space-y-3 text-sm text-slate-600">
                    <div>
                        <p class="font-semibold text-slate-800 mb-1">📧 Hostinger (Recomendado):</p>
                        <p>Host: <code class="bg-white px-2 py-1 rounded border border-slate-200 text-slate-800">smtp.hostinger.com</code> | Porta: <code class="bg-white px-2 py-1 rounded border border-slate-200 text-slate-800">465</code> | SSL | Use seu email e senha da Hostinger</p>
                        <p class="text-xs text-slate-500 mt-1">Exemplo: seu-email@seudominio.com | Use a senha do seu email na Hostinger</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 mb-1">Gmail:</p>
                        <p>Host: <code class="bg-white px-2 py-1 rounded border border-slate-200 text-slate-800">smtp.gmail.com</code> | Porta: <code class="bg-white px-2 py-1 rounded border border-slate-200 text-slate-800">587</code> | TLS | Use senha de app</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 mb-1">Outlook/Hotmail:</p>
                        <p>Host: <code class="bg-white px-2 py-1 rounded border border-slate-200 text-slate-800">smtp-mail.outlook.com</code> | Porta: <code class="bg-white px-2 py-1 rounded border border-slate-200 text-slate-800">587</code> | TLS</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 mb-1">SendGrid:</p>
                        <p>Host: <code class="bg-white px-2 py-1 rounded border border-slate-200 text-slate-800">smtp.sendgrid.net</code> | Porta: <code class="bg-white px-2 py-1 rounded border border-slate-200 text-slate-800">587</code> | TLS</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 mb-1">Mailgun:</p>
                        <p>Use o tipo de mailer "mailgun" e configure no .env</p>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" class="flex-1 sm:flex-none px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-200">
                    Salvar Configurações
                </button>
                <button type="button" onclick="testSmtp()" class="flex-1 sm:flex-none px-8 py-3 bg-white border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition-all">
                    Testar Conexão
                </button>
            </div>
        </form>
    </div>

    <!-- Resultado do Teste -->
    <div id="test-result" class="hidden"></div>
</div>

<script>
function testSmtp() {
    const form = event.target.closest('form');
    const formData = new FormData(form);
    const testResult = document.getElementById('test-result');
    
    testResult.innerHTML = '<div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm"><p class="text-slate-700">Testando conexão SMTP...</p></div>';
    testResult.classList.remove('hidden');
    
    fetch('{{ route("admin.smtp.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            testResult.innerHTML = `
                <div class="bg-emerald-50 border border-emerald-200 rounded-3xl p-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-emerald-700 font-semibold">${data.message || 'Teste realizado com sucesso! Verifique sua caixa de entrada.'}</p>
                    </div>
                </div>
            `;
        } else {
            testResult.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-3xl p-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <p class="text-red-700 font-semibold">${data.message || 'Erro ao testar conexão SMTP. Verifique as configurações.'}</p>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        testResult.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-3xl p-6">
                <p class="text-red-700 font-semibold">Erro: ${error.message}</p>
            </div>
        `;
    });
}

// Mensagens de sucesso/erro
@if(session('success'))
    setTimeout(() => {
        alert('{{ session("success") }}');
    }, 100);
@endif

@if(session('error'))
    setTimeout(() => {
        alert('{{ session("error") }}');
    }, 100);
@endif
</script>
@endsection

