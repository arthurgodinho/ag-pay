@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-12 sm:mb-8 relative z-30">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Checkout</h1>
        </div>
        <a href="{{ route('dashboard.checkout.create') }}" class="group relative inline-flex items-center justify-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-blue-500/20 transition-all duration-200 hover:-translate-y-0.5 overflow-hidden whitespace-nowrap w-full sm:w-auto min-w-[160px] z-40">
            <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer"></span>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Criar Produto
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Saldo Recebido -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-200">
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-full -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-green-50 rounded-xl text-green-600 group-hover:bg-green-100 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="hidden sm:flex items-center text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Receita
                    </span>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-slate-500 mb-1">Saldo Recebido</h3>
                <div class="flex items-baseline">
                    <span class="text-lg md:text-2xl font-bold text-slate-900">R$ {{ number_format($stats['total_received'] ?? 0, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Vendas Confirmadas -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-200">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="hidden sm:flex items-center text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Vendas
                    </span>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-slate-500 mb-1">Vendas Confirmadas</h3>
                <div class="flex items-baseline">
                    <span class="text-lg md:text-2xl font-bold text-slate-900">{{ $stats['confirmed_sales'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Pix Gerados -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-200">
            <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-full -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-purple-50 rounded-xl text-purple-600 group-hover:bg-purple-100 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="hidden sm:flex items-center text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Leads
                    </span>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-slate-500 mb-1">Pix Gerados</h3>
                <div class="flex items-baseline">
                    <span class="text-lg md:text-2xl font-bold text-slate-900">{{ $stats['pix_generated'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Pagamentos Pendentes -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-200">
            <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-full -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-orange-50 rounded-xl text-orange-600 group-hover:bg-orange-100 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="hidden sm:flex items-center text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pendente
                    </span>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-slate-500 mb-1">Pagamentos Pendentes</h3>
                <div class="flex items-baseline">
                    <span class="text-lg md:text-2xl font-bold text-slate-900">{{ $stats['pending_payments'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    @if($products->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6 ring-8 ring-blue-50/50">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Comece a vender agora!</h3>
            <p class="text-slate-500 max-w-md mx-auto mb-8 text-base">Crie seu primeiro checkout personalizado em minutos e comece a aceitar pagamentos via PIX e Cartão de Crédito.</p>
            <a href="{{ route('dashboard.checkout.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/30 hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Criar Primeiro Checkout
            </a>
        </div>
    @else
        <!-- Products List (Desktop) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hidden md:block">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                <h3 class="font-semibold text-slate-800">Seus Produtos</h3>
                <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">{{ $products->count() }} produtos</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Produto</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Preço</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vendas</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($products as $product)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 relative">
                                        @if($product->product_image)
                                            @if(\Illuminate\Support\Str::startsWith($product->product_image, ['http://', 'https://']))
                                                <img class="h-12 w-12 rounded-xl object-cover shadow-sm ring-2 ring-white" src="{{ $product->product_image }}" alt="">
                                            @else
                                                <img class="h-12 w-12 rounded-xl object-cover shadow-sm ring-2 ring-white" src="{{ \Illuminate\Support\Facades\Storage::url($product->product_image) }}" alt="">
                                            @endif
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 shadow-sm ring-2 ring-white">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $product->name }}</div>
                                        <div class="text-xs text-slate-500 max-w-xs truncate">{{ \Illuminate\Support\Str::limit($product->description, 40) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900 bg-slate-50 px-3 py-1 rounded-lg inline-block border border-slate-100">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $product->is_active ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-green-500' : 'bg-red-500' }} mr-1.5 my-auto"></span>
                                    {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-slate-600">
                                    <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    {{ $product->sales_count }} <span class="text-xs text-slate-400 ml-1">vendas</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2 transition-opacity">
                                    <button onclick="copyToClipboard('{{ route('checkout.public', $product->uuid) }}', this)" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all relative group/btn" title="Copiar Link">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-slate-800 text-white text-xs rounded shadow-lg opacity-0 group-hover/btn:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Copiar Link</div>
                                    </button>
                                    
                                    <a href="{{ route('checkout.public', $product->uuid) }}" target="_blank" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all relative group/btn" title="Visualizar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-slate-800 text-white text-xs rounded shadow-lg opacity-0 group-hover/btn:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Visualizar</div>
                                    </a>

                                    <a href="{{ route('dashboard.checkout.edit', $product->uuid) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all relative group/btn" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-slate-800 text-white text-xs rounded shadow-lg opacity-0 group-hover/btn:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Editar</div>
                                    </a>

                                    <form action="{{ route('dashboard.checkout.destroy', $product->uuid) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all relative group/btn" title="Excluir">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-slate-800 text-white text-xs rounded shadow-lg opacity-0 group-hover/btn:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Excluir</div>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Products List (Mobile) -->
        <div class="md:hidden space-y-4">
            @foreach($products as $product)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-5">
                    <div class="flex gap-4">
                        <!-- Image -->
                        <div class="h-20 w-20 flex-shrink-0 relative">
                            @if($product->product_image)
                                @if(\Illuminate\Support\Str::startsWith($product->product_image, ['http://', 'https://']))
                                    <img class="h-full w-full rounded-xl object-cover shadow-sm ring-1 ring-slate-100" src="{{ $product->product_image }}" alt="">
                                @else
                                    <img class="h-full w-full rounded-xl object-cover shadow-sm ring-1 ring-slate-100" src="{{ \Illuminate\Support\Facades\Storage::url($product->product_image) }}" alt="">
                                @endif
                            @else
                                <div class="h-full w-full rounded-xl bg-slate-50 flex items-center justify-center text-slate-300 ring-1 ring-slate-100">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start gap-2">
                                    <h3 class="text-base font-bold text-slate-900 leading-snug line-clamp-2">{{ $product->name }}</h3>
                                    <!-- Status Badge -->
                                    <span class="flex-shrink-0 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full border {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                                        {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $product->description }}</p>
                            </div>
                            
                            <div class="flex items-center justify-between mt-3">
                                <div class="text-lg font-bold text-slate-900">R$ {{ number_format($product->price, 2, ',', '.') }}</div>
                                <div class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded-md flex items-center">
                                    <svg class="w-3 h-3 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    {{ $product->sales_count }} vendas
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar (Modern Grid) -->
                <div class="grid grid-cols-4 divide-x divide-slate-100 border-t border-slate-100 bg-slate-50/50">
                    <!-- Copy Link -->
                    <button onclick="copyToClipboard('{{ route('checkout.public', $product->uuid) }}', this)" class="flex flex-col items-center justify-center py-3 px-2 hover:bg-white transition-colors group" title="Copiar Link">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600 transition-colors mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                        </svg>
                        <span class="text-[10px] font-medium text-slate-500 group-hover:text-blue-600">Copiar</span>
                    </button>
                    
                    <!-- View -->
                    <a href="{{ route('checkout.public', $product->uuid) }}" target="_blank" class="flex flex-col items-center justify-center py-3 px-2 hover:bg-white transition-colors group">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-slate-900 transition-colors mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="text-[10px] font-medium text-slate-500 group-hover:text-slate-900">Visualizar</span>
                    </a>
                    
                    <!-- Edit -->
                    <a href="{{ route('dashboard.checkout.edit', $product->uuid) }}" class="flex flex-col items-center justify-center py-3 px-2 hover:bg-white transition-colors group">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-indigo-600 transition-colors mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="text-[10px] font-medium text-slate-500 group-hover:text-indigo-600">Editar</span>
                    </a>
                    
                    <!-- Delete -->
                    <form action="{{ route('dashboard.checkout.destroy', $product->uuid) }}" method="POST" class="contents" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex flex-col items-center justify-center py-3 px-2 hover:bg-white transition-colors group w-full">
                            <svg class="w-5 h-5 text-slate-500 group-hover:text-red-600 transition-colors mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span class="text-[10px] font-medium text-slate-500 group-hover:text-red-600">Excluir</span>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-5 right-5 transform translate-y-20 opacity-0 transition-all duration-300 z-50">
    <div class="bg-slate-800 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-medium text-sm">Link copiado com sucesso!</span>
    </div>
</div>

<script>
    function copyToClipboard(text, buttonElement) {
        navigator.clipboard.writeText(text).then(function() {
            // Visual feedback on button
            if (buttonElement) {
                const originalHtml = buttonElement.innerHTML;
                const icon = buttonElement.querySelector('svg');
                if (icon) icon.classList.add('text-green-500');
                
                setTimeout(() => {
                    if (icon) icon.classList.remove('text-green-500');
                }, 2000);
            }

            // Show toast
            const toast = document.getElementById('toast');
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }, function(err) {
            console.error('Erro ao copiar: ', err);
            alert('Erro ao copiar link. Por favor, copie manualmente.');
        });
    }
</script>
<style>
    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }
    .animate-shimmer {
        animation: shimmer 2s infinite;
    }
</style>
@endsection
