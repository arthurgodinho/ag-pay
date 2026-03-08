@extends('layouts.app')

@section('title', 'Gest�o de Usu�rios')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white">Gest�o de Usu�rios</h1>
        <p class="text-gray-400 mt-1">Lista de todos os usu�rios do sistema</p>
    </div>

    @if(session('success'))
        <div class="bg-blue-500/20 border border-emerald-500 text-blue-400 px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-[#151A23] rounded-3xl shadow-lg border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#0B0E14]/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Saldo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">KYC Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">A��es</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#0B0E14]/30">
                            <td class="px-6 py-4 text-sm text-gray-400">#{{ $user->id }}</td>
                            <td class="px-6 py-4 text-sm text-white">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-white">
                                R$ {{ number_format($user->wallet->balance ?? 0.00, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $user->kyc_status === 'verified' ? 'bg-blue-500/20 text-blue-400' : 
                                       ($user->kyc_status === 'rejected' ? 'bg-red-500/20 text-red-400' : 'bg-yellow-500/20 text-yellow-400') }}">
                                    {{ ucfirst($user->kyc_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->kyc_status === 'pending')
                                    <form method="POST" action="{{ route('admin.users.kyc', $user->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" name="action" value="approve" class="text-blue-400 hover:text-blue-300 mr-2">Aprovar</button>
                                        <button type="submit" name="action" value="reject" class="text-red-400 hover:text-red-300">Rejeitar</button>
                                    </form>
                                @else
                                    <span class="text-gray-500 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">Nenhum usu�rio encontrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-white/10">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection


