@extends('layouts.admin')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="flex flex-col h-[calc(100vh-110px)] -m-4 sm:-m-8" x-data="chatApp()" x-init="loadMessages(); setInterval(() => loadMessages(), 5000);">
    <!-- Chat Header -->
    <div class="bg-white border-b border-slate-200 px-4 py-3 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0 z-10">
        <div class="flex items-center gap-3 w-full sm:w-auto overflow-hidden">
            <a href="{{ route('admin.support.index') }}" class="p-2 -ml-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            
            <div class="flex flex-col min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-bold text-slate-900 truncate">{{ $ticket->subject }}</h1>
                    <span class="px-2 py-0.5 text-xs font-bold rounded-full border hidden sm:inline-flex items-center"
                        :class="{
                            'bg-blue-50 text-blue-700 border-blue-100': ticketStatus === 'open',
                            'bg-amber-50 text-amber-700 border-amber-100': ticketStatus === 'in_progress',
                            'bg-emerald-50 text-emerald-700 border-emerald-100': ticketStatus === 'resolved',
                            'bg-slate-50 text-slate-600 border-slate-200': ticketStatus === 'closed'
                        }">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                            :class="{
                                'bg-blue-500': ticketStatus === 'open',
                                'bg-amber-500': ticketStatus === 'in_progress',
                                'bg-emerald-500': ticketStatus === 'resolved',
                                'bg-slate-500': ticketStatus === 'closed'
                            }"></span>
                        <span x-text="formatStatus(ticketStatus)"></span>
                    </span>
                </div>
                <p class="text-xs text-slate-500 flex items-center gap-1.5">
                    <span class="font-medium text-slate-700">{{ $ticket->user->name }}</span>
                    <span>•</span>
                    <span>Ticket #{{ $ticket->id }}</span>
                    <span>•</span>
                    <span>{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                </p>
            </div>
        </div>

        <!-- Admin Controls -->
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
            @if(!$ticket->assigned_to || $ticket->assigned_to !== auth()->id())
                <form action="{{ route('admin.support.assign', $ticket->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-medium px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Atribuir a Mim
                    </button>
                </form>
            @endif

            <div class="relative">
                <select 
                    x-model="ticketStatus" 
                    @change="updateStatus($event.target.value)"
                    class="appearance-none bg-white border border-slate-200 text-slate-700 text-xs font-medium rounded-lg pl-3 pr-8 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer hover:bg-slate-50 transition-colors"
                >
                    <option value="open">Aberto</option>
                    <option value="in_progress">Em Andamento</option>
                    <option value="waiting">Aguardando</option>
                    <option value="resolved">Resolvido</option>
                    <option value="closed">Fechado</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Messages -->
    <div class="flex-1 bg-slate-50 relative overflow-hidden flex flex-col">
        <!-- Messages Container -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 custom-scrollbar scroll-smooth" id="messagesContainer">
            <!-- Loading State -->
            <div x-show="isLoading" class="flex flex-col items-center justify-center h-full space-y-3">
                <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-200 border-t-blue-600"></div>
                <p class="text-sm text-slate-400 font-medium">Carregando mensagens...</p>
            </div>
        </div>
        
        <!-- Scroll to bottom button -->
        <button x-show="showScrollButton" 
                @click="scrollToBottom()"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="absolute bottom-24 right-6 bg-white shadow-lg border border-slate-100 text-slate-600 hover:text-blue-600 p-2 rounded-full z-20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </button>
    </div>

    <!-- Input Area -->
    <div class="bg-white border-t border-slate-200 p-4 shrink-0 z-20">
        <form @submit.prevent="sendMessage()" enctype="multipart/form-data" class="max-w-4xl mx-auto w-full">
            <!-- File Preview -->
            <div x-show="selectedFile" x-transition class="mb-3 flex items-center gap-3 p-2 bg-blue-50 border border-blue-100 rounded-lg w-fit">
                <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                </div>
                <div class="flex flex-col min-w-0 max-w-[200px]">
                    <span x-text="selectedFile?.name" class="text-sm font-medium text-slate-700 truncate"></span>
                    <span x-text="(selectedFile?.size / 1024).toFixed(1) + ' KB'" class="text-[10px] text-slate-500"></span>
                </div>
                <button type="button" @click="clearFile()" class="p-1 hover:bg-white rounded-full text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex items-end gap-2 sm:gap-3 bg-slate-50 p-2 rounded-2xl border border-slate-200 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 transition-all">
                <!-- Attachment Button -->
                <label class="p-2.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl cursor-pointer transition-colors shrink-0" title="Anexar arquivo">
                    <input type="file" @change="handleFileSelect($event)" accept="image/*,.pdf,.doc,.docx" class="hidden" id="attachmentInput">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                </label>

                <textarea 
                    x-model="message"
                    placeholder="Digite sua resposta..."
                    rows="1"
                    @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                    @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'"
                    class="flex-1 bg-transparent border-none text-slate-900 placeholder-slate-400 focus:ring-0 py-3 px-0 resize-none max-h-[120px] min-h-[24px] leading-relaxed"
                ></textarea>

                <button 
                    type="submit"
                    :disabled="isSending || (!message.trim() && !selectedFile)"
                    class="p-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl transition-all shadow-md hover:shadow-lg hover:shadow-blue-600/20 shrink-0 mb-0.5"
                >
                    <svg x-show="!isSending" class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <svg x-show="isSending" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
            <p class="text-[10px] text-slate-400 mt-2 text-center sm:text-right px-1">
                Pressione Enter para enviar • Shift + Enter para quebra de linha
            </p>
        </form>
    </div>
</div>

<script>
function chatApp() {
    return {
        message: '',
        selectedFile: null,
        ticketId: {{ $ticket->id }},
        ticketStatus: '{{ $ticket->status }}',
        isLoading: true,
        isSending: false,
        showScrollButton: false,
        lastMessageCount: 0,
        
        init() {
            const container = document.getElementById('messagesContainer');
            container.addEventListener('scroll', () => {
                const distanceToBottom = container.scrollHeight - container.scrollTop - container.clientHeight;
                this.showScrollButton = distanceToBottom > 100;
            });
        },
        
        formatStatus(status) {
            const map = {
                'open': 'Aberto',
                'in_progress': 'Em Andamento',
                'waiting': 'Aguardando',
                'resolved': 'Resolvido',
                'closed': 'Fechado'
            };
            return map[status] || status;
        },

        handleFileSelect(event) {
            if (event.target.files.length > 0) {
                this.selectedFile = event.target.files[0];
            }
        },

        clearFile() {
            this.selectedFile = null;
            document.getElementById('attachmentInput').value = '';
        },
        
        scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            container.scrollTop = container.scrollHeight;
        },

        async updateStatus(status) {
            const formData = new FormData();
            formData.append('status', status);
            formData.append('_token', '{{ csrf_token() }}');
            
            try {
                const response = await fetch('{{ route("admin.support.status", $ticket->id) }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                
                const data = await response.json();
                if (data.success) {
                    this.ticketStatus = status;
                    // Optional toast
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('Erro ao atualizar status');
            }
        },

        async sendMessage() {
            if (!this.message.trim() && !this.selectedFile) return;
            
            this.isSending = true;
            
            const formData = new FormData();
            if (this.message.trim()) formData.append('message', this.message);
            if (this.selectedFile) formData.append('attachment', this.selectedFile);
            formData.append('_token', '{{ csrf_token() }}');
            
            try {
                const url = '{{ route("admin.support.message", ["id" => 999]) }}'.replace('999', this.ticketId);
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                
                const data = await response.json();
                
                if (!response.ok) throw new Error(data.message || 'Erro ao enviar mensagem');
                
                if (data.success) {
                    this.message = '';
                    this.clearFile();
                    await this.loadMessages(true);
                    
                    // Reset textarea height
                    const textarea = document.querySelector('textarea');
                    if (textarea) textarea.style.height = 'auto';
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Erro ao enviar mensagem');
            } finally {
                this.isSending = false;
            }
        },

        async loadMessages(forceScroll = false) {
            try {
                const url = '{{ route("admin.support.messages", ["id" => 999]) }}'.replace('999', this.ticketId);
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                if (!response.ok) return;
                
                const data = await response.json();
                
                const container = document.getElementById('messagesContainer');
                const currentUserId = {{ Auth::id() }};
                const wasAtBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 50;
                
                if (!this.isLoading && data.messages.length === this.lastMessageCount && !forceScroll) {
                    return;
                }
                this.lastMessageCount = data.messages.length;

                let lastDate = null;
                
                const html = data.messages.map((msg, index) => {
                    const isCurrentUser = msg.user_id === currentUserId;
                    const isAdmin = msg.user.is_admin || msg.user.is_manager;
                    const msgDate = new Date(msg.created_at);
                    const dateStr = msgDate.toLocaleDateString();
                    
                    let dateDivider = '';
                    if (dateStr !== lastDate) {
                        dateDivider = `
                            <div class="flex items-center justify-center my-6">
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-full uppercase tracking-wide">
                                    ${dateStr}
                                </span>
                            </div>
                        `;
                        lastDate = dateStr;
                    }
                    
                    // Avatar logic for ADMIN view
                    // For admin view, we want to see the USER'S avatar on the left (if they are the sender)
                    // And NO avatar for the current ADMIN (right)
                    const initials = msg.user.name
                        .split(' ')
                        .map(n => n[0])
                        .slice(0, 2)
                        .join('')
                        .toUpperCase();
                        
                    const avatar = isCurrentUser 
                        ? '' 
                        : `
                            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-[10px] font-bold mr-3 ${isAdmin ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-200 text-slate-600'}">
                                ${initials}
                            </div>
                        `;

                    // Bubble Styles
                    const bubbleClass = isCurrentUser 
                        ? 'bg-blue-600 text-white rounded-2xl rounded-tr-none shadow-sm' 
                        : 'bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-tl-none shadow-sm';
                        
                    const justifyClass = isCurrentUser ? 'justify-end' : 'justify-start';
                    
                    let attachmentHtml = '';
                    if (msg.attachment) {
                        const attachBg = isCurrentUser 
                            ? 'bg-white/10 hover:bg-white/20 text-white border-white/20' 
                            : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200';
                        
                        const iconColor = isCurrentUser ? 'text-white' : 'text-slate-400';
                            
                        attachmentHtml = `
                            <a href="/storage/${msg.attachment}" target="_blank" class="mt-2 flex items-center gap-3 p-2.5 rounded-xl border ${attachBg} transition-all group">
                                <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                                    <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">${msg.attachment_name || 'Anexo'}</p>
                                    <p class="text-[10px] opacity-70">Clique para baixar</p>
                                </div>
                            </a>
                        `;
                    }
                    
                    return `
                        ${dateDivider}
                        <div class="flex ${justifyClass} mb-4 group animate-fadeIn">
                            ${!isCurrentUser ? avatar : ''}
                            <div class="flex flex-col ${isCurrentUser ? 'items-end' : 'items-start'} max-w-[85%] sm:max-w-[70%]">
                                <div class="flex items-center gap-2 mb-1 px-1">
                                    <span class="text-xs font-bold ${isCurrentUser ? 'text-slate-500' : 'text-slate-900'}">
                                        ${msg.user.name}
                                    </span>
                                    ${isAdmin ? '<span class="text-[10px] px-1.5 py-0.5 rounded-md bg-indigo-50 text-indigo-600 font-bold uppercase tracking-wider">Staff</span>' : ''}
                                </div>

                                <div class="${bubbleClass} px-4 py-3 text-[15px] leading-relaxed break-words w-full relative">
                                    ${msg.message ? `<p class="whitespace-pre-wrap">${msg.message}</p>` : ''}
                                    ${attachmentHtml}
                                    
                                    <div class="flex items-center justify-end gap-1 mt-1 select-none">
                                        <span class="text-[10px] ${isCurrentUser ? 'text-blue-100' : 'text-slate-400'}">
                                            ${msgDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                        </span>
                                        ${isCurrentUser ? `
                                            <svg class="w-3 h-3 ${msg.is_read ? 'text-blue-200' : 'text-blue-300/50'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                if (container.innerHTML !== html) {
                    container.innerHTML = html;
                    if (forceScroll || wasAtBottom || this.isLoading) {
                        setTimeout(() => this.scrollToBottom(), 50);
                    }
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            } finally {
                this.isLoading = false;
            }
        }
    }
}
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out forwards;
}
</style>
@endsection
