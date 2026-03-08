@extends('layouts.admin')

@section('title', 'Detalhes da Campanha')

@section('content')
@php
    use App\Helpers\ThemeHelper;
    $themeColors = ThemeHelper::getThemeColors();
@endphp
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <svg class="w-8 h-8 text-[#00B2FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                {{ $campaign->name }}
            </h1>
            <p class="text-gray-400 mt-2">Detalhes e estatísticas da campanha</p>
        </div>
        <a href="{{ route('admin.email-campaigns.index') }}" class="px-6 py-3 bg-[#0B0E14] border border-white/10 text-white font-semibold rounded-xl hover:bg-[#151A23] transition-all">
            Voltar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações Principais -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Estatísticas -->
            <div class="bg-[#151A23] rounded-3xl border border-white/10 p-8">
                <h2 class="text-xl font-bold text-white mb-6">Estatísticas</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-[#0B0E14] rounded-xl">
                        <p class="text-3xl font-bold text-[#00B2FF]">{{ $campaign->total_recipients }}</p>
                        <p class="text-gray-400 text-sm mt-1">Destinatários</p>
                    </div>
                    <div class="text-center p-4 bg-[#0B0E14] rounded-xl">
                        <p class="text-3xl font-bold text-blue-400">{{ $campaign->sent_count }}</p>
                        <p class="text-gray-400 text-sm mt-1">Enviados</p>
                    </div>
                    <div class="text-center p-4 bg-[#0B0E14] rounded-xl">
                        <p class="text-3xl font-bold text-red-400">{{ $campaign->failed_count }}</p>
                        <p class="text-gray-400 text-sm mt-1">Falharam</p>
                    </div>
                </div>
            </div>

            <!-- Preview do Email -->
            <div class="bg-[#151A23] rounded-3xl border border-white/10 p-8">
                <h2 class="text-xl font-bold text-white mb-6">Preview do Email</h2>
                <div class="bg-white rounded-xl p-6 max-h-96 overflow-auto">
                    <div class="text-sm">
                        {!! $campaign->body_html !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações -->
        <div class="space-y-6">
            <div class="bg-[#151A23] rounded-3xl border border-white/10 p-8">
                <h2 class="text-xl font-bold text-white mb-6">Informações</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-400 text-sm">Assunto</p>
                        <p class="text-white font-medium">{{ $campaign->subject }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Status</p>
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-500/20 text-gray-300 border-gray-500/30',
                                'scheduled' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                'sending' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                'sent' => 'bg-blue-500/20 text-blue-300 border-green-500/30',
                                'cancelled' => 'bg-red-500/20 text-red-300 border-red-500/30',
                            ];
                            $statusLabels = [
                                'draft' => 'Rascunho',
                                'scheduled' => 'Agendada',
                                'sending' => 'Enviando',
                                'sent' => 'Enviada',
                                'cancelled' => 'Cancelada',
                            ];
                        @endphp
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$campaign->status] ?? 'bg-gray-500/20' }}">
                            {{ $statusLabels[$campaign->status] ?? $campaign->status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Criada em</p>
                        <p class="text-white font-medium">{{ $campaign->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($campaign->sent_at)
                    <div>
                        <p class="text-gray-400 text-sm">Enviada em</p>
                        <p class="text-white font-medium">{{ $campaign->sent_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    @if($campaign->status === 'draft')
                    <form action="{{ route('admin.email-campaigns.send', $campaign->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja enviar esta campanha para todos os usuários?');">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-[#00B2FF] text-white font-semibold rounded-xl hover:opacity-90 transition-all">
                            Enviar Campanha
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

