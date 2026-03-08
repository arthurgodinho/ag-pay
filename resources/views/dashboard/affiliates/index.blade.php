@extends('layouts.app')

@section('title', 'Programa de Afiliados')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="p-2 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                Programa de Afiliados
            </h1>
            <p class="text-slate-500 mt-2 ml-14 text-lg">Indique novos usuários e ganhe comissões recorrentes</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm font-bold border border-emerald-200 shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Programa Ativo
            </span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Comissões Pendentes -->
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-slate-500 font-semibold text-sm uppercase tracking-wider">Comissões Pendentes</p>
                </div>
                <h3 class="text-4xl font-black text-slate-900 tracking-tight">R$ {{ number_format($pendingCommissions, 2, ',', '.') }}</h3>
                <div class="mt-4 flex items-center gap-2 text-xs font-medium text-blue-600">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    Atualizado em tempo real
                </div>
            </div>
        </div>

        <!-- Card 2: Total de Indicações -->
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-purple-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-purple-100 text-purple-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <p class="text-slate-500 font-semibold text-sm uppercase tracking-wider">Total de Indicações</p>
                </div>
                <h3 class="text-4xl font-black text-slate-900 tracking-tight">{{ $referrals->count() }}</h3>
                <p class="text-sm text-slate-400 mt-4 font-medium">Usuários cadastrados através do seu link</p>
            </div>
        </div>

        <!-- Card 3: Nível -->
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-yellow-500/10 transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-yellow-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-yellow-100 text-yellow-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <p class="text-slate-500 font-semibold text-sm uppercase tracking-wider">Status da Conta</p>
                </div>
                <div class="flex items-end gap-3">
                    <h3 class="text-4xl font-black text-slate-900 tracking-tight">Nível 1</h3>
                    <span class="mb-1.5 px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full border border-yellow-200">Bronze</span>
                </div>
                <div class="mt-4 w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 20%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-2 font-medium">Continue indicando para subir de nível</p>
            </div>
        </div>
    </div>

    <!-- Link de Indicação Section -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden relative group">
        <!-- Decorative Background -->
        <div class="absolute inset-0 bg-slate-50/50"></div>
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-50 group-hover:scale-110 transition-transform duration-1000"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-50 group-hover:scale-110 transition-transform duration-1000"></div>

        <div class="p-8 md:p-12 relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-bold uppercase tracking-wider mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    Link de Indicação
                </div>
                <h2 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">Compartilhe seu Link Exclusivo</h2>
                <p class="text-slate-500 leading-relaxed text-lg">Envie este link para seus amigos e contatos. Ganhe comissões automáticas sempre que seus indicados realizarem transações na plataforma.</p>
            </div>
            
            <div class="w-full md:w-auto flex-1 max-w-xl">
                <div class="bg-white p-2 rounded-2xl border border-slate-200 flex flex-col sm:flex-row gap-2 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                        <input
                            type="text"
                            id="affiliateLink"
                            value="{{ $affiliateLink }}"
                            readonly
                            class="w-full bg-slate-50 text-slate-600 placeholder-slate-400 pl-12 pr-4 py-4 outline-none font-mono text-sm truncate rounded-xl border border-slate-100 focus:bg-white focus:border-blue-500 transition-all"
                        >
                    </div>
                    <button
                        onclick="copyLink()"
                        id="copyBtn"
                        class="bg-blue-600 text-white hover:bg-blue-700 font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-blue-500/30 active:scale-95 flex items-center justify-center gap-2 min-w-[140px]"
                    >
                        <span id="btnIcon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 012-2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        </span>
                        <span id="btnText">Copiar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Indicações -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-6 bg-slate-50/50">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white border border-slate-200 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Usuários Indicados</h2>
                    <p class="text-sm text-slate-500">Lista dos seus últimos indicados</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-slate-500 bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm">
                    Total: <span class="text-slate-900 font-bold">{{ $referrals->count() }}</span>
                </span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Usuário</th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell">Email</th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Data</th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Comissão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($referrals as $referral)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md ring-4 ring-white">
                                        {{ strtoupper(substr($referral->name, 0, 2)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $referral->name }}</div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">ID: #{{ $referral->id }}</div>
                                        <div class="md:hidden text-xs text-slate-400 mt-1">{{ Str::limit($referral->email, 20) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap hidden md:table-cell">
                                <div class="text-sm text-slate-600 font-medium">{{ $referral->email }}</div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap hidden sm:table-cell">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">{{ $referral->created_at->format('d/m/Y') }}</span>
                                    <span class="text-xs text-slate-400">{{ $referral->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 self-center"></span>
                                    Ativo
                                </span>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-100">
                                    R$ 0,00
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900">Comece a indicar hoje!</h3>
                                    <p class="text-slate-500 mt-2 max-w-md mx-auto text-base">Você ainda não tem indicações. Compartilhe seu link exclusivo e comece a construir sua rede de afiliados.</p>
                                    <button onclick="document.getElementById('affiliateLink').select(); document.execCommand('copy'); showFeedback();" class="mt-8 bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 012-2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                        Copiar Link Agora
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($referrals->count() > 0)
        <div class="bg-slate-50 px-8 py-5 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-slate-500">
                Mostrando <span class="font-bold text-slate-800">{{ $referrals->count() }}</span> resultados
            </div>
            <!-- Placeholder para paginação futura -->
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-400 cursor-not-allowed" disabled>Anterior</button>
                <button class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-400 cursor-not-allowed" disabled>Próxima</button>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function copyLink() {
        const linkInput = document.getElementById('affiliateLink');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const copyBtn = document.getElementById('copyBtn');
        const originalText = btnText.innerText;
        const originalIcon = btnIcon.innerHTML;
        
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(linkInput.value).then(showFeedback).catch(fallbackCopy);
        } else {
            fallbackCopy();
        }
        
        function fallbackCopy() {
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
            showFeedback();
        }
        
        function showFeedback() {
            btnText.innerText = 'Copiado!';
            btnIcon.innerHTML = '<svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            
            copyBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            copyBtn.classList.add('bg-emerald-500', 'hover:bg-emerald-600');
            
            setTimeout(() => {
                btnText.innerText = originalText;
                btnIcon.innerHTML = originalIcon;
                copyBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                copyBtn.classList.remove('bg-emerald-500', 'hover:bg-emerald-600');
            }, 2000);
        }
    }
</script>
@endsection