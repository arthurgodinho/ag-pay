@use('Illuminate\Support\Facades\Storage')
@extends('layouts.app')

@section('title', 'Meus Prêmios')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="p-2 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
                Meus Prêmios
            </h1>
            <p class="text-slate-500 mt-2 ml-14 text-lg">Acompanhe seu progresso e desbloqueie recompensas exclusivas</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white px-5 py-2.5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <span class="text-sm font-medium text-slate-500 uppercase tracking-wide">Saldo Atual</span>
                <span class="text-xl font-black text-slate-900">R$ {{ number_format($currentBalance, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Hero Status Section -->
    <div class="relative bg-white rounded-3xl p-8 md:p-12 overflow-hidden shadow-xl border border-slate-200 group">
        <!-- Background Effects -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50 -mr-20 -mt-20 group-hover:opacity-70 transition-opacity duration-700"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-indigo-50 rounded-full blur-3xl opacity-50 -ml-20 -mb-20 group-hover:opacity-70 transition-opacity duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
            <div class="max-w-2xl text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-bold uppercase tracking-wider mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    Programa de Recompensas
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4 tracking-tight leading-tight">
                    Conquiste <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Prêmios Incríveis</span>
                </h2>
                <p class="text-slate-500 text-lg leading-relaxed">
                    A cada meta atingida, uma nova recompensa é desbloqueada automaticamente. Continue vendendo para subir de nível e ganhar prêmios exclusivos.
                </p>
            </div>
            
            <!-- Progress Circle or Highlight -->
            <div class="bg-white rounded-2xl p-6 min-w-[280px] text-center shadow-lg border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wide mb-2">Próxima Meta</p>
                @php
                    $nextAward = $awards->where('goal_amount', '>', $currentBalance)->sortBy('goal_amount')->first();
                @endphp
                @if($nextAward)
                    <div class="text-3xl font-black text-slate-900 mb-1 tracking-tight">
                        R$ {{ number_format($nextAward->goal_amount, 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-blue-600 font-medium mb-3">Faltam R$ {{ number_format($nextAward->goal_amount - $currentBalance, 2, ',', '.') }}</p>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ min(100, ($currentBalance / $nextAward->goal_amount) * 100) }}%"></div>
                    </div>
                @else
                    <div class="text-2xl font-black text-emerald-600 mb-1 tracking-tight">
                        Todas as Metas!
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Você é incrível!</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Grid de Prêmios -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($awards as $award)
            @php
                $percentage = min(100, max(0, ($currentBalance / $award->goal_amount) * 100));
                $isUnlocked = $currentBalance >= $award->goal_amount;
            @endphp
            
            <div class="group relative bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 h-full flex flex-col {{ $isUnlocked ? 'ring-2 ring-emerald-400 ring-offset-2' : '' }}">
                
                <!-- Status Badge Absolute -->
                <div class="absolute top-4 right-4 z-20">
                    @if($isUnlocked)
                        <div class="flex items-center gap-1.5 bg-emerald-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg shadow-emerald-500/30 animate-bounce-subtle">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            CONQUISTADO
                        </div>
                    @else
                        <div class="flex items-center gap-1.5 bg-slate-900/60 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full border border-white/20">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            BLOQUEADO
                        </div>
                    @endif
                </div>

                <!-- Image Section -->
                <div class="relative h-48 overflow-hidden bg-slate-100">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent z-10"></div>
                    
                    @if($award->image_url)
                        <img 
                            src="{{ url('storage/app/public/' . $award->image_url) }}" 
                            alt="{{ $award->title }}" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 {{ !$isUnlocked ? 'grayscale opacity-60' : '' }}"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-300">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                    @endif

                    <!-- Goal Overlay -->
                    <div class="absolute bottom-4 left-4 z-20 text-white">
                        <p class="text-xs font-medium opacity-80 uppercase tracking-wider mb-0.5">Meta a atingir</p>
                        <p class="text-xl font-bold text-white drop-shadow-md">R$ {{ number_format($award->goal_amount, 2, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors">{{ $award->title }}</h3>
                    <p class="text-sm text-slate-500 mb-6 line-clamp-3 leading-relaxed flex-1">{{ $award->description }}</p>

                    <!-- Progress Bar -->
                    <div class="mt-auto">
                        <div class="flex items-center justify-between text-xs font-bold mb-2">
                            <span class="text-slate-600 uppercase tracking-wide">Progresso</span>
                            <span class="{{ $isUnlocked ? 'text-emerald-600' : 'text-blue-600' }}">{{ number_format($percentage, 1) }}%</span>
                        </div>
                        
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden shadow-inner border border-slate-100 relative">
                            <!-- Background pattern -->
                            <div class="absolute inset-0 opacity-20" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(0,0,0,0.1) 5px, rgba(0,0,0,0.1) 10px);"></div>
                            
                            <div 
                                class="h-full rounded-full transition-all duration-1000 ease-out relative overflow-hidden {{ $isUnlocked ? 'bg-gradient-to-r from-emerald-400 to-teal-500' : 'bg-gradient-to-r from-blue-500 to-indigo-600' }}" 
                                style="width: {{ $percentage }}%"
                            >
                                <div class="absolute inset-0 bg-white/30 w-full animate-[shimmer_2s_infinite]"></div>
                            </div>
                        </div>
                        
                        @if(!$isUnlocked)
                            <div class="mt-3 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-400 bg-slate-50 py-2 rounded-lg border border-slate-100">
                                <span>Faltam</span>
                                <span class="text-slate-700 font-bold">R$ {{ number_format($award->goal_amount - $currentBalance, 2, ',', '.') }}</span>
                            </div>
                        @else
                            <button class="mt-3 w-full py-2 bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wide rounded-lg border border-emerald-100 flex items-center justify-center gap-2 hover:bg-emerald-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Recompensa Ativa
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border border-slate-200 shadow-xl text-center">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 animate-pulse">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Nenhum prêmio disponível</h3>
                    <p class="text-slate-500 max-w-md mx-auto">Em breve novos prêmios e metas estarão disponíveis para você conquistar.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
</style>
@endsection