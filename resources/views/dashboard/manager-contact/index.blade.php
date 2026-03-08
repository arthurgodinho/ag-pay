@extends('layouts.app')

@section('title', 'Falar com Gerente')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-500/10 to-blue-500/10 border border-emerald-500/30 rounded-2xl p-6 backdrop-blur-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Falar com Gerente</h1>
                <p class="text-slate-500">Entre em contato com seu gerente pelo WhatsApp</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-blue-500 flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Card do Gerente -->
    <div class="bg-white rounded-2xl shadow-sm p-8 border border-slate-200">
        <div class="flex flex-col items-center text-center space-y-6">
            <!-- Logo do Site no lugar da Foto -->
            <div class="relative">
                <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center p-4 shadow-xl border-4 border-slate-50 ring-1 ring-slate-100 overflow-hidden group hover:scale-105 transition-transform duration-300">
                    @php
                        $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
                        $systemName = \App\Helpers\LogoHelper::getSystemName();
                    @endphp
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $systemName }}" class="w-full h-full object-contain">
                    @else
                        <span class="text-2xl font-black text-blue-600">{{ substr($systemName, 0, 2) }}</span>
                    @endif
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center border-4 border-white shadow-sm animate-bounce">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                </div>
            </div>

            <!-- Informações do Gerente -->
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">{{ $managerName }}</h2>
                <p class="text-slate-500">{{ $managerEmail }}</p>
                @if($isDefault)
                    <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium border border-blue-100">
                        Suporte do Sistema
                    </span>
                @else
                    <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium border border-emerald-100">
                        Seu Gerente
                    </span>
                @endif
            </div>

            <!-- Botão WhatsApp -->
            @if($whatsapp)
                <a 
                    @php
                        $systemName = \App\Helpers\LogoHelper::getSystemName();
                    @endphp
                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}?text=Olá! Preciso de ajuda com minha conta no {{ urlencode($systemName) }}." 
                    target="_blank"
                    class="w-full max-w-md bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center space-x-3"
                >
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <span>Fale com seu Gerente pelo WhatsApp</span>
                </a>
            @else
                <div class="w-full max-w-md bg-slate-50 border border-slate-200 rounded-xl p-4 text-center">
                    <p class="text-slate-500 text-sm">WhatsApp não configurado. Entre em contato pelo suporte.</p>
                </div>
            @endif

@endsection



