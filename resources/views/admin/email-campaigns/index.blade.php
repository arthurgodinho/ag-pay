@extends('layouts.admin')

@section('title', 'Campanhas de Email')

@section('content')
<div class="space-y-6">
    <!-- Header with improved mobile responsiveness -->
    {{-- Header removed as per request --}}

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm text-sm" role="alert">
            <div class="p-1.5 bg-emerald-100 rounded-full shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Modern Table Layout -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nome</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Assunto</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Progresso</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Criado em</th>
                        <th scope="col" class="px-6 py-3 text-right text-[10px] font-bold text-slate-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($campaigns as $campaign)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="text-slate-900 font-semibold text-xs">{{ $campaign->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <p class="text-slate-600 text-xs truncate max-w-[200px]" title="{{ $campaign->subject }}">{{ $campaign->subject }}</p>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
                            @php
                                $statusConfig = [
                                    'draft' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'label' => 'Rascunho', 'dot' => 'bg-slate-400'],
                                    'scheduled' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'Agendada', 'dot' => 'bg-blue-500'],
                                    'sending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'Enviando', 'dot' => 'bg-amber-500'],
                                    'sent' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'label' => 'Enviada', 'dot' => 'bg-emerald-500'],
                                    'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => 'Cancelada', 'dot' => 'bg-red-500'],
                                ];
                                $config = $statusConfig[$campaign->status] ?? $statusConfig['draft'];
                            @endphp
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-transparent {{ $config['bg'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
                                <span class="text-[10px] font-bold uppercase tracking-wide {{ $config['text'] }}">{{ $config['label'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex flex-col gap-1 w-32">
                                <div class="flex items-center justify-between text-[10px] text-slate-500">
                                    <span>{{ $campaign->sent_count }} / {{ $campaign->total_recipients }}</span>
                                    @if($campaign->total_recipients > 0)
                                        <span class="font-medium">{{ round(($campaign->sent_count / $campaign->total_recipients) * 100) }}%</span>
                                    @endif
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    @php
                                        $percentage = $campaign->total_recipients > 0 ? ($campaign->sent_count / $campaign->total_recipients) * 100 : 0;
                                    @endphp
                                    <div class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                </div>
                                @if($campaign->failed_count > 0)
                                    <span class="text-[10px] text-red-500 font-medium flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $campaign->failed_count }} falhas
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-slate-700 font-medium text-xs">{{ $campaign->created_at->format('d/m/Y') }}</span>
                                <span class="text-slate-400 text-[10px]">{{ $campaign->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.email-campaigns.show', $campaign->id) }}" 
                                   class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all group/icon" 
                                   title="Ver detalhes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if($campaign->status === 'draft')
                                <form action="{{ route('admin.email-campaigns.send', $campaign->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja enviar esta campanha?');">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Enviar agora">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center bg-slate-50/50">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-4 border border-slate-100 shadow-sm">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 mb-1">Nenhuma campanha encontrada</h3>
                                <p class="text-xs text-slate-500 mb-6 text-center">Comece a engajar seus usuários criando sua primeira campanha de email marketing hoje mesmo.</p>
                                <a href="{{ route('admin.email-campaigns.create') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Criar Campanha
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($campaigns->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $campaigns->links() }}
        </div>
        @endif
    </div>
</div>
@endsection