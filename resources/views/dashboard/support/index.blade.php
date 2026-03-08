@extends('layouts.app')

@section('title', 'Suporte')

@section('content')
<div class="space-y-8" x-data="{ 
    showNewTicket: false, 
    isLoading: false, 
    errorMessage: '',
    errors: {},
    async createTicket() {
        this.isLoading = true;
        this.errorMessage = '';
        this.errors = {};
        
        const form = document.getElementById('newTicketForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('{{ route("dashboard.support.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                if (response.status === 422) {
                    this.errors = data.errors || {};
                    this.errorMessage = 'Por favor, corrija os erros abaixo.';
                } else {
                    throw new Error(data.message || 'Ocorreu um erro ao criar o ticket.');
                }
                return;
            }
            
            if (data.success) {
                // Fecha o modal e recarrega a página para atualizar a lista
                this.showNewTicket = false;
                window.location.reload();
            } else {
                throw new Error(data.message || 'Erro desconhecido.');
            }
        } catch (error) {
            console.error('Error:', error);
            this.errorMessage = error.message || 'Erro de conexão. Tente novamente.';
        } finally {
            this.isLoading = false;
        }
    }
}">
    <!-- Header Hero -->
    <div class="relative bg-white rounded-3xl p-6 sm:p-10 border border-slate-100 shadow-sm overflow-hidden group">
        <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-50/50 rounded-full blur-3xl -mr-40 -mt-40 transition-transform duration-700 group-hover:scale-110"></div>
        <div class="absolute bottom-0 left-0 w-60 h-60 bg-blue-50/50 rounded-full blur-2xl -ml-32 -mb-32 transition-transform duration-700 group-hover:scale-110"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 text-xs font-medium mb-4">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    Central de Ajuda
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-4 tracking-tight">
                    Como podemos ajudar?
                </h1>
                <p class="text-slate-500 text-lg leading-relaxed">
                    Abra um ticket e nossa equipe especializada responderá o mais breve possível. Estamos aqui para resolver seus problemas.
                </p>
            </div>
            
            <div class="hidden md:block">
                <button 
                    @click="showNewTicket = true; setTimeout(() => document.getElementById('subjectInput').focus(), 100)"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-semibold transition-all hover:shadow-xl hover:-translate-y-1 group/btn"
                >
                    <div class="bg-white/10 p-1.5 rounded-lg group-hover/btn:bg-white/20 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <span>Novo Ticket</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Action Button (Mobile Only) -->
    <button 
        @click="showNewTicket = true"
        class="md:hidden fixed bottom-24 right-6 z-40 bg-slate-900 text-white p-4 rounded-full shadow-xl shadow-slate-900/30 transition-transform hover:scale-110 active:scale-95 flex items-center justify-center"
        aria-label="Novo Ticket"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
    </button>

    <!-- Modal Novo Ticket -->
    <div 
        x-show="showNewTicket"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-4"
        style="display: none;"
        @click.self="showNewTicket = false"
        x-cloak
    >
        <div 
            x-show="showNewTicket"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 scale-95"
            class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8 max-w-lg w-full relative overflow-hidden border border-slate-100"
        >
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Novo Ticket</h2>
                    <p class="text-slate-500 text-sm mt-1">Descreva seu problema detalhadamente</p>
                </div>
                <button @click="showNewTicket = false" class="text-slate-400 hover:text-slate-600 transition-colors p-2 hover:bg-slate-50 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="createTicket()" id="newTicketForm" class="space-y-6">
                <!-- Global Error Message -->
                <div x-show="errorMessage" x-transition class="p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-text="errorMessage"></span>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Assunto</label>
                    <input 
                        id="subjectInput"
                        type="text" 
                        name="subject" 
                        required
                        placeholder="Ex: Problema com saque PIX"
                        class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:bg-white transition-all font-medium placeholder-slate-400"
                        :class="{'border-red-300 bg-red-50': errors.subject}"
                    >
                    <template x-if="errors.subject">
                        <p class="text-red-500 text-xs mt-1" x-text="errors.subject[0]"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Mensagem</label>
                    <textarea 
                        name="description" 
                        rows="5"
                        required
                        placeholder="Descreva o que aconteceu..."
                        class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:bg-white transition-all font-medium placeholder-slate-400 resize-none"
                        :class="{'border-red-300 bg-red-50': errors.description}"
                    ></textarea>
                    <template x-if="errors.description">
                        <p class="text-red-500 text-xs mt-1" x-text="errors.description[0]"></p>
                    </template>
                </div>

                <div class="flex gap-3 pt-2">
                    <button 
                        type="button"
                        @click="showNewTicket = false"
                        class="flex-1 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold py-3.5 px-4 rounded-xl transition-all"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit"
                        :disabled="isLoading"
                        class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold py-3.5 px-4 rounded-xl transition-all shadow-lg hover:shadow-slate-900/20 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Criando...' : 'Criar Ticket'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Tickets -->
    <div class="grid gap-4">
        @forelse($tickets as $ticket)
            <a 
                href="{{ route('dashboard.support.show', $ticket->id) }}"
                class="group bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all duration-300 relative overflow-hidden"
            >
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <!-- Left: Status Icon + Info -->
                    <div class="flex items-start gap-5">
                        <!-- Status Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-colors
                                {{ $ticket->status === 'open' ? 'bg-blue-50 text-blue-600' : 
                                   ($ticket->status === 'in_progress' ? 'bg-amber-50 text-amber-600' : 
                                   ($ticket->status === 'resolved' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500')) }}">
                                @if($ticket->status === 'resolved')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif($ticket->status === 'in_progress')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif($ticket->status === 'open')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                @else
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                @endif
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full uppercase tracking-wider border
                                    {{ $ticket->status === 'open' ? 'bg-blue-50 text-blue-700 border-blue-100' : 
                                       ($ticket->status === 'in_progress' ? 'bg-amber-50 text-amber-700 border-amber-100' : 
                                       ($ticket->status === 'resolved' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-600 border-slate-200')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">#{{ $ticket->id }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="text-xs text-slate-500 font-medium">{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-slate-900 truncate pr-4 group-hover:text-indigo-600 transition-colors">
                                {{ $ticket->subject }}
                            </h3>
                            <p class="text-slate-500 text-sm line-clamp-1 mt-1 font-medium">
                                {{ $ticket->description ?? 'Sem descrição' }}
                            </p>
                        </div>
                    </div>

                    <!-- Right: Actions & Meta -->
                    <div class="flex items-center justify-between md:flex-col md:items-end md:justify-center gap-4 pl-19 md:pl-0 border-t md:border-t-0 border-slate-100 pt-4 md:pt-0">
                        @if($ticket->unreadMessagesCount() > 0)
                            <span class="inline-flex items-center px-3 py-1 bg-red-50 text-red-600 border border-red-100 text-xs font-bold rounded-full animate-pulse">
                                {{ $ticket->unreadMessagesCount() }} nova(s) msg
                            </span>
                        @else
                            <div class="h-6"></div> <!-- Spacer to keep alignment -->
                        @endif
                        
                        <div class="flex items-center text-slate-400 group-hover:text-indigo-600 transition-colors text-sm font-bold">
                            Ver conversa
                            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-3xl shadow-sm p-12 border border-slate-200 text-center flex flex-col items-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 ring-8 ring-slate-50/50">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Nenhum ticket encontrado</h3>
                <p class="text-slate-500 max-w-md mx-auto mb-8">Você ainda não abriu nenhum chamado de suporte. Se precisar de ajuda, estamos aqui para resolver.</p>
                <button 
                    @click="showNewTicket = true"
                    class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-8 py-3 rounded-xl transition-all shadow-lg hover:shadow-slate-900/20"
                >
                    Abrir Primeiro Ticket
                </button>
            </div>
        @endforelse
    </div>
</div>
@endsection
