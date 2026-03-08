@extends('layouts.app')

@section('title', 'Baixe nosso APP')

@section('content')
<div class="min-h-[calc(100-64px)] p-4 sm:p-6 lg:p-10 flex items-center justify-center">
    <div class="w-full max-w-6xl">
        <!-- Card Principal com Design Ultra Moderno -->
        <div class="bg-white rounded-[2rem] sm:rounded-[3rem] p-8 sm:p-12 lg:p-16 border border-slate-100 shadow-[0_20px_50px_rgba(0,0,0,0.05)] relative overflow-hidden">
            
            <!-- Elementos Decorativos de Fundo -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/5 rounded-full blur-[100px] -mr-48 -mt-48"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-400/5 rounded-full blur-[100px] -ml-48 -mb-48"></div>
            
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-20 items-center relative z-10">
                
                <!-- Coluna de Texto e Ações -->
                <div class="lg:col-span-7 space-y-8 text-center lg:text-left order-2 lg:order-1">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 border border-blue-100 shadow-sm">
                        <span class="flex h-2 w-2 rounded-full bg-blue-600 animate-ping"></span>
                        <span class="text-[10px] font-black text-blue-700 uppercase tracking-[0.2em]">App Oficial Android</span>
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-[1.1] tracking-tight">
                        {{ $landingSettings['app_title'] ?? 'Sua conta na palma da sua mão.' }}
                    </h1>
                    
                    <p class="text-lg sm:text-xl text-slate-500 leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                        {{ $landingSettings['app_subtitle'] ?? 'Gerencie suas vendas, acompanhe seu saldo e realize saques instantâneos onde quer que você esteja.' }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-8 pt-10">
                        <!-- Google Play Button -->
                        <a href="{{ $landingSettings['app_playstore_url'] ?? '#' }}" target="_blank" class="w-full sm:w-auto flex items-center justify-center gap-5 px-12 py-6 bg-slate-900 text-white rounded-[2rem] hover:bg-blue-600 transition-all duration-300 hover:-translate-y-2 shadow-[0_20px_40px_rgba(0,0,0,0.2)] group">
                            <svg class="w-10 h-10 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M3,20.5V3.5C3,2.91,3.34,2.39,3.84,2.15L13.69,12L3.84,21.85C3.34,21.61,3,21.09,3,20.5Z M16.81,15.12L4.94,22.01C4.77,22.09,4.59,22.13,4.41,22.13C4.08,22.13,3.76,22.01,3.53,21.79L14.34,10.97L16.81,15.12Z M19.03,13.67L17.49,14.56L14.97,10.34L17.49,6.12L19.03,7.01C19.66,7.37,20,8.03,20,8.75V11.93C20,12.65,19.66,13.31,19.03,13.67Z M14.34,6.53L3.53,15.71C3.76,15.49,4.08,15.37,4.41,15.37C4.59,15.37,4.77,15.41,4.94,15.49L16.81,2.38L14.34,6.53Z"/></svg>
                            <div class="text-left">
                                <p class="text-[10px] uppercase font-bold opacity-60 leading-none mb-1.5 tracking-wider">Baixar na</p>
                                <p class="text-2xl font-black leading-none tracking-tight">Google Play</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Coluna do Mockup -->
                <div class="lg:col-span-5 relative order-1 lg:order-2">
                    <div class="absolute -inset-10 bg-blue-500/15 rounded-full blur-[80px] animate-pulse"></div>
                    
                    <!-- iPhone Frame -->
                    <div class="w-[280px] h-[580px] bg-slate-900 rounded-[3rem] p-3 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] border-[8px] border-slate-800 relative z-10 mx-auto">
                        <!-- Dynamic Notch -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-800 rounded-b-2xl z-20 flex items-center justify-center">
                            <div class="w-10 h-1 bg-slate-700 rounded-full"></div>
                        </div>
                        
                        <div class="bg-white h-full w-full rounded-[2.3rem] overflow-hidden relative flex flex-col">
                            <!-- App Header -->
                            <div class="p-6 pt-10 bg-blue-600 text-white shadow-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[10px] font-bold opacity-80 uppercase tracking-widest">Saldo Disponível</p>
                                    <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <p class="text-3xl font-black">R$ {{ number_format(Auth::user()->balance ?? 0, 2, ',', '.') }}</p>
                            </div>
                            
                            <!-- App Body -->
                            <div class="flex-1 p-5 space-y-6 bg-slate-50/50">
                                <!-- Quick Actions -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="p-4 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center gap-2 group cursor-pointer hover:border-blue-200 transition-colors">
                                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </div>
                                        <p class="text-[10px] font-black text-slate-500 uppercase">Receber</p>
                                    </div>
                                    <div class="p-4 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center gap-2 group cursor-pointer hover:border-green-200 transition-colors">
                                        <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                        </div>
                                        <p class="text-[10px] font-black text-slate-500 uppercase">Sacar</p>
                                    </div>
                                </div>
                                
                                <!-- Recent Activity -->
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Atividade</p>
                                        <span class="text-[9px] font-bold text-blue-600">Ver tudo</span>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center p-3 bg-white rounded-2xl shadow-sm border border-slate-100">
                                            <div class="flex gap-3 items-center">
                                                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white text-[9px] font-black">PIX</div>
                                                <div class="text-left">
                                                    <p class="text-[10px] font-black text-slate-900">Venda #8829</p>
                                                    <p class="text-[8px] text-slate-400">Há 5 minutos</p>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-black text-green-600">+R$ 150,00</span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-white rounded-2xl shadow-sm border border-slate-100">
                                            <div class="flex gap-3 items-center">
                                                <div class="w-9 h-9 bg-slate-900 rounded-xl flex items-center justify-center text-white text-[9px] font-black">CC</div>
                                                <div class="text-left">
                                                    <p class="text-[10px] font-black text-slate-900">Venda #8828</p>
                                                    <p class="text-[8px] text-slate-400">Há 12 minutos</p>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-black text-green-600">+R$ 89,90</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endsection
