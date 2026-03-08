@extends('layouts.app')

@section('title', 'Recebimento Manual')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 sm:space-y-6 px-4 sm:px-0">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Recebimento Manual</h1>
        <p class="text-sm sm:text-base text-slate-500 mt-1">Gere um QR Code Pix para receber pagamentos</p>
    </div>

    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm p-4 sm:p-6 md:p-8">
        <form id="qrCodeForm" class="space-y-6">
            @csrf
            <div>
                <label for="amount" class="block text-sm font-medium text-slate-600 mb-2">
                    Valor a Receber (R$)
                </label>
                <input
                    type="number"
                    id="amount"
                    name="amount"
                    step="0.01"
                    min="0.01"
                    required
                    style="font-size: 16px;"
                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl sm:rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 transition-all"
                    placeholder="0.00"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl sm:rounded-2xl shadow-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-95"
            >
                Gerar QR Code
            </button>
        </form>

        <!-- Área do QR Code -->
        <div id="qrCodeContainer" class="mt-4 sm:mt-6 hidden">
            <div class="bg-slate-50 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-center space-y-3 sm:space-y-4 border border-slate-200">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">QR Code PIX</h3>
                    
                    <!-- Temporizador -->
                    <div id="timerContainer" class="mb-4">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-100 rounded-lg">
                            <svg class="w-5 h-5 text-red-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-red-700 font-semibold">
                                Tempo restante: <span id="timerDisplay">05:00</span>
                            </span>
                        </div>
                    </div>
                    
                    <div id="qrCodeDisplay" class="flex justify-center mb-4"></div>
                    <p class="text-slate-600 text-sm">Escaneie o QR Code com seu app de pagamento</p>
                    <p id="expiredMessage" class="text-red-600 font-semibold hidden mt-2">QR Code expirado! Gere um novo código.</p>
                </div>
                
                <div class="border-t border-slate-200 pt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Chave PIX (Copia e Cola)</label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            id="pixKeyInput"
                            readonly
                            style="font-size: 16px;"
                            class="flex-1 px-3 sm:px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-800 text-xs sm:text-sm font-mono"
                        >
                        <button
                            type="button"
                            onclick="copyPixKey()"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-semibold"
                        >
                            Copiar
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Cole este código no app do seu banco para pagar</p>
                </div>

                <div class="border-t border-slate-200 pt-3 sm:pt-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 text-xs sm:text-sm">
                        <div>
                            <p class="text-slate-500">Valor Bruto</p>
                            <p id="amountGross" class="text-slate-900 font-semibold"></p>
                        </div>
                        <div>
                            <p class="text-slate-500">Taxa</p>
                            <p id="feeAmount" class="text-slate-900 font-semibold"></p>
                        </div>
                        <div>
                            <p class="text-slate-500">Valor Líquido</p>
                            <p id="amountNet" class="text-slate-900 font-semibold"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('qrCodeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = document.getElementById('amount').value;
        const container = document.getElementById('qrCodeContainer');
        const display = document.getElementById('qrCodeDisplay');
        const pixKeyInput = document.getElementById('pixKeyInput');
        
        // Validação
        if (!amount || parseFloat(amount) < 0.01) {
            alert('Por favor, informe um valor válido (mínimo R$ 0,01)');
            return;
        }
        
        // Mostra loading
        display.innerHTML = '<div class="animate-spin text-[#00B2FF] text-lg">Carregando...</div>';
        pixKeyInput.value = '';
        container.classList.remove('hidden');
        
        fetch('{{ route("dashboard.receive.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Exibe QR Code
                display.innerHTML = data.qr_code || '<p class="text-red-500">QR Code não disponível</p>';
                
                // Exibe chave PIX copia e cola
                pixKeyInput.value = data.pix_key || data.qr_code_string || '';
                
                // Exibe valores
                document.getElementById('amountGross').textContent = 'R$ ' + parseFloat(data.amount_gross || amount).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('feeAmount').textContent = 'R$ ' + parseFloat(data.fee || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('amountNet').textContent = 'R$ ' + parseFloat(data.amount_net || amount).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                
                // Inicia temporizador de 5 minutos
                if (data.expires_in_seconds) {
                    startTimer(data.expires_in_seconds);
                } else {
                    startTimer(300); // 5 minutos padrão
                }
                
                // Scroll para o QR Code
                container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                display.innerHTML = '<p class="text-red-500">' + (data.message || 'Erro ao gerar QR Code') + '</p>';
                pixKeyInput.value = '';
                alert(data.message || 'Erro ao gerar QR Code');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            display.innerHTML = '<p class="text-red-500">Erro ao gerar QR Code. Por favor, tente novamente.</p>';
            pixKeyInput.value = '';
            alert('Erro ao gerar QR Code. Por favor, tente novamente.');
        });
    });
    
    function copyPixKey() {
        const pixKeyInput = document.getElementById('pixKeyInput');
        if (pixKeyInput.value) {
            pixKeyInput.select();
            pixKeyInput.setSelectionRange(0, 99999); // Para mobile
            
            try {
                document.execCommand('copy');
                
                // Feedback visual
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copiado!';
                button.classList.add('bg-blue-600');
                button.classList.remove('bg-[#00B2FF]');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-blue-600');
                    button.classList.add('bg-[#00B2FF]');
                }, 2000);
            } catch (err) {
                // Fallback para navegadores modernos
                navigator.clipboard.writeText(pixKeyInput.value).then(() => {
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = 'Copiado!';
                    button.classList.add('bg-blue-600');
                    button.classList.remove('bg-[#00B2FF]');
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-blue-600');
                        button.classList.add('bg-[#00B2FF]');
                    }, 2000);
                }).catch(() => {
                    alert('Não foi possível copiar. Por favor, copie manualmente.');
                });
            }
        }
    }
    
    let timerInterval = null;
    
    function startTimer(seconds) {
        // Limpa timer anterior se existir
        if (timerInterval) {
            clearInterval(timerInterval);
        }
        
        let timeLeft = seconds;
        const timerDisplay = document.getElementById('timerDisplay');
        const expiredMessage = document.getElementById('expiredMessage');
        const timerContainer = document.getElementById('timerContainer');
        const qrCodeDisplay = document.getElementById('qrCodeDisplay');
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerDisplay.textContent = '00:00';
                expiredMessage.classList.remove('hidden');
                timerContainer.classList.add('hidden');
                qrCodeDisplay.innerHTML = '<p class="text-red-500 font-semibold">QR Code expirado! Gere um novo código.</p>';
                document.getElementById('pixKeyInput').value = '';
            }
            
            timeLeft--;
        }
        
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    }
</script>
@endsection
