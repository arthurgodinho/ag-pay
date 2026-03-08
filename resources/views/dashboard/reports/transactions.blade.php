@extends('layouts.app')

@section('title', 'Relatórios de Transações')

@section('content')
<div class="space-y-4 sm:space-y-6 px-3 sm:px-0" x-data="{ 
    modalOpen: false, 
    transaction: {}, 
    openModal(data) { 
        this.transaction = data; 
        this.modalOpen = true; 
        document.body.style.overflow = 'hidden';
    }, 
    closeModal() { 
        this.modalOpen = false; 
        document.body.style.overflow = 'auto';
    } 
}">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Relatórios de Transações</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Visualize todas as suas transações</p>
    </div>

    <!-- Tabela para Desktop/Tablet -->
    <div class="hidden sm:block bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Histórico Completo</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Origem</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Valor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Método</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-slate-50 transition-colors cursor-pointer" 
                            @click="openModal({ 
                                uuid: '{{ $transaction->uuid }}', 
                                payer_name: '{{ $transaction->payer_name ?? $transaction->user->name ?? 'N/A' }}', 
                                amount_gross: '{{ $transaction->amount_gross }}', 
                                status: '{{ $transaction->status }}', 
                                status_label: '{{ $transaction->status === 'completed' ? 'Aprovado' : ($transaction->status === 'pending' ? 'Pendente' : 'Falhou') }}',
                                type: '{{ $transaction->type }}', 
                                date: '{{ $transaction->created_at->format('d/m/Y H:i') }}' 
                            })">
                            <td class="px-4 py-3 text-xs text-slate-600 font-mono">{{ substr($transaction->uuid, 0, 8) }}...</td>
                            <td class="px-4 py-3">
                                @if($transaction->product_id)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        Checkout
                                    </span>
                                @elseif($transaction->type === 'deposit')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Depósito
                                    </span>
                                @elseif($transaction->type === 'withdrawal')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                        Saque
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        Outro
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-900 font-medium">{{ $transaction->payer_name ?? $transaction->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-xs font-bold text-slate-900">R$ {{ number_format($transaction->amount_gross, 2, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 text-xs rounded-full font-bold border {{ $transaction->status === 'completed' ? 'bg-blue-50 text-blue-700 border-emerald-100' : ($transaction->status === 'pending' ? 'bg-yellow-50 text-yellow-700 border-yellow-100' : 'bg-red-50 text-red-700 border-red-100') }}">
                                    {{ $transaction->status === 'completed' ? 'Aprovado' : ($transaction->status === 'pending' ? 'Pendente' : 'Falhou') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs rounded-full font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    @if($transaction->type === 'pix')
                                        <svg class="w-3 h-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    @endif
                                    {{ strtoupper($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="font-medium">Nenhuma transação encontrada</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lista em Cards para Mobile -->
    <div class="sm:hidden space-y-3">
        @forelse($transactions as $transaction)
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm active:scale-[0.99] transition-transform cursor-pointer"
                 @click="openModal({ 
                    uuid: '{{ $transaction->uuid }}', 
                    payer_name: '{{ $transaction->payer_name ?? $transaction->user->name ?? 'N/A' }}', 
                    amount_gross: '{{ $transaction->amount_gross }}', 
                    status: '{{ $transaction->status }}', 
                    status_label: '{{ $transaction->status === 'completed' ? 'Aprovado' : ($transaction->status === 'pending' ? 'Pendente' : 'Falhou') }}',
                    type: '{{ $transaction->type }}', 
                    date: '{{ $transaction->created_at->format('d/m/Y H:i') }}' 
                })">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transaction->type === 'pix' ? 'bg-blue-50 text-blue-600' : 'bg-blue-50 text-blue-600' }}">
                            @if($transaction->type === 'pix')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-mono">#{{ substr($transaction->uuid, 0, 8) }}</p>
                            <h3 class="text-sm font-bold text-slate-900 line-clamp-1">{{ $transaction->payer_name ?? $transaction->user->name ?? 'Cliente Desconhecido' }}</h3>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] rounded-full font-bold border {{ $transaction->status === 'completed' ? 'bg-blue-50 text-blue-700 border-emerald-100' : ($transaction->status === 'pending' ? 'bg-yellow-50 text-yellow-700 border-yellow-100' : 'bg-red-50 text-red-700 border-red-100') }}">
                        {{ $transaction->status === 'completed' ? 'APROVADO' : ($transaction->status === 'pending' ? 'PENDENTE' : 'FALHOU') }}
                    </span>
                </div>
                
                <div class="flex justify-between items-end pt-2 border-t border-slate-100">
                    <div>
                        <p class="text-[10px] text-slate-400 mb-0.5">Data</p>
                        <p class="text-xs font-medium text-slate-600">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 mb-0.5">Valor</p>
                        <p class="text-lg font-bold text-slate-900">R$ {{ number_format($transaction->amount_gross, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center shadow-sm">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-slate-900 font-medium mb-1">Nada por aqui</h3>
                <p class="text-slate-500 text-sm">Nenhuma transação encontrada.</p>
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $transactions->links() }}
    </div>

    <!-- Transaction Details Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/75 transition-opacity" 
                 @click="closeModal()" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10 mb-4 sm:mb-0"
                             :class="{
                                 'bg-blue-100': transaction.status === 'completed',
                                 'bg-yellow-100': transaction.status === 'pending',
                                 'bg-red-100': ['failed', 'cancelled'].includes(transaction.status)
                             }">
                            <template x-if="transaction.status === 'completed'">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="transaction.status === 'pending'">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                             <template x-if="['failed', 'cancelled'].includes(transaction.status)">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </template>
                        </div>
                        <div class="text-center sm:text-left w-full sm:ml-4">
                            <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                Detalhes da Transação
                            </h3>
                            <div class="mt-4 space-y-3">
                                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                                    <span class="text-sm text-slate-500">ID da Transação</span>
                                    <span class="text-sm font-mono font-medium text-slate-900 bg-slate-50 px-2 py-1 rounded" x-text="transaction.uuid"></span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                                    <span class="text-sm text-slate-500">Valor Total</span>
                                    <span class="text-lg font-bold text-slate-900" x-text="'R$ ' + parseFloat(transaction.amount_gross).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                                    <span class="text-sm text-slate-500">Status Atual</span>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full"
                                          :class="{
                                              'bg-blue-100 text-blue-800': transaction.status === 'completed',
                                              'bg-yellow-100 text-yellow-800': transaction.status === 'pending',
                                              'bg-red-100 text-red-800': ['failed', 'cancelled'].includes(transaction.status)
                                          }" x-text="transaction.status_label">
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                                    <span class="text-sm text-slate-500">Método de Pagamento</span>
                                    <span class="text-sm font-bold text-slate-900 uppercase bg-slate-100 px-2 py-1 rounded" x-text="transaction.type"></span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                                    <span class="text-sm text-slate-500">Pagador</span>
                                    <span class="text-sm font-medium text-slate-900 text-right max-w-[150px] truncate" x-text="transaction.payer_name || 'N/A'"></span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                                    <span class="text-sm text-slate-500">Data da Criação</span>
                                    <span class="text-sm font-medium text-slate-900" x-text="transaction.date"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" 
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-3 bg-slate-900 text-base font-medium text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors" 
                            @click="closeModal()">
                        Fechar Detalhes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
