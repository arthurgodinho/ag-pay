<?php

namespace App\Http\Controllers;

use App\Models\SystemGatewayConfig;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Events\UserApproved;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminUserController extends Controller
{
    /**
     * Lista todos os usuários
     */
    public function index(Request $request): View
    {
        $query = User::with(['wallet', 'manager']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($request->filled('status') && $request->status !== 'todos') {
            if ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true)->where('is_blocked', false);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'manager') {
                $query->where('is_manager', true);
            } elseif ($request->role === 'user') {
                $query->where('is_admin', false)->where('is_manager', false);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Calcular vendas dos últimos 7 dias para cada usuário
        foreach ($users as $user) {
            $user->sales_7d = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where('type', '!=', 'withdrawal')
                ->where('created_at', '>=', now()->subDays(7))
                ->sum('amount_gross');
        }

        // Estatísticas
        $stats = [
            'total' => User::count(),
            'month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'pending' => User::where('is_approved', false)->count(),
            'blocked' => User::where('is_blocked', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Exibe formulário de edição
     */
    public function edit(int $id): View
    {
        $user = User::with(['wallet', 'manager'])->findOrFail($id);
        $managers = User::where('is_manager', true)
            ->orWhere('is_admin', true)
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();
        
        $gateways = SystemGatewayConfig::where(function($q) {
            $q->where('is_active_for_pix', true)
              ->orWhere('is_active_for_card', true);
        })->orderBy('provider_name')->get();

        return view('admin.users.edit', compact('user', 'managers', 'gateways'));
    }

    /**
     * Atualiza informações básicas do usuário
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'taxa_entrada' => 'nullable|numeric|min:0|max:100',
            'taxa_entrada_fixo' => 'nullable|numeric|min:0',
            'taxa_saida' => 'nullable|numeric|min:0|max:100',
            'taxa_saida_fixo' => 'nullable|numeric|min:0',
            'split_fixed' => 'nullable|numeric|min:0',
            'split_variable' => 'nullable|numeric|min:0|max:100',
            'is_admin' => 'boolean',
            'is_manager' => 'boolean',
            'manager_id' => 'nullable|exists:users,id',
            'preferred_gateway' => 'nullable|string',
            'withdrawal_mode' => 'nullable|in:auto,manual,',
        ]);

        $user = User::findOrFail($id);

        // Não permite remover admin de si mesmo
        if ($user->id === auth()->id() && !$request->boolean('is_admin')) {
            return back()->with('error', 'Você não pode remover seus próprios privilégios de administrador!');
        }

        // Trata withdrawal_mode: se for string vazia, converte para null
        $withdrawalMode = $request->input('withdrawal_mode');
        
        if ($withdrawalMode === '' || $withdrawalMode === null || empty($withdrawalMode)) {
            $withdrawalMode = null;
        } else {
            // Garante que seja 'auto' ou 'manual'
            $withdrawalMode = in_array($withdrawalMode, ['auto', 'manual']) ? $withdrawalMode : null;
        }
        
        try {
            // Atualiza o usuário
            $user->name = $request->name;
            $user->email = $request->email;
            $user->taxa_entrada = $request->taxa_entrada;
            $user->taxa_entrada_fixo = $request->taxa_entrada_fixo;
            $user->taxa_saida = $request->taxa_saida;
            $user->taxa_saida_fixo = $request->taxa_saida_fixo;
            $user->split_fixed = $request->split_fixed;
            $user->split_variable = $request->split_variable;
            
            // PRESERVA STATUS ATUAL - Não altera status de aprovação ou bloqueio na edição simples
            // Esses campos devem ser alterados apenas pelos botões de ação específicos
            // $user->bloquear_saque = ...; 
            // $user->is_blocked = ...;
            // $user->is_approved = ...;
            
            $user->withdrawal_mode = $withdrawalMode;
            
            $user->is_admin = $request->boolean('is_admin');
            $user->is_manager = $request->boolean('is_manager');
            $user->manager_id = $request->manager_id;
            $user->preferred_gateway = $request->preferred_gateway;
            
            $user->save();
            
            // Recarrega o usuário do banco para verificar se foi salvo
            $user->refresh();
            
            \Illuminate\Support\Facades\Log::info('AdminUserController: Usuário atualizado com sucesso', [
                'user_id' => $user->id,
                'withdrawal_mode_salvo_no_banco' => $user->withdrawal_mode,
                'withdrawal_mode_enviado' => $request->input('withdrawal_mode'),
                'withdrawal_mode_processado' => $withdrawalMode,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AdminUserController: Erro ao atualizar usuário', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'withdrawal_mode' => $withdrawalMode,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Erro ao atualizar usuário: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.users.edit', $id)
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Altera a senha do usuário
     */
    public function updatePassword(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.edit', $id)
            ->with('success', 'Senha alterada com sucesso!');
    }

    /**
     * Adiciona saldo à carteira do usuário
     */
    public function addBalance(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request, $id) {
            $user = User::findOrFail($id);
            $wallet = $user->wallet ?? Wallet::create(['user_id' => $id, 'balance' => 0, 'frozen_balance' => 0]);
            
            $wallet->increment('balance', $request->amount);

            // Registra transação com tipo "Crédito Manual"
            Transaction::create([
                'user_id' => $id,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'type' => 'credit_manual', // Tipo específico para crédito manual do admin
                'amount_gross' => $request->amount,
                'amount_net' => $request->amount,
                'fee' => 0,
                'status' => 'completed',
                'payer_name' => 'Administrador',
                'payer_email' => auth()->user()->email,
                'description' => $request->description ?? 'Saldo adicionado pelo administrador',
                'gateway_provider' => 'admin',
            ]);
        });

        return redirect()->route('admin.users.edit', $id)
            ->with('success', 'Saldo adicionado com sucesso!');
    }

    /**
     * Remove saldo da carteira do usuário
     */
    public function removeBalance(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request, $id) {
            $user = User::findOrFail($id);
            $wallet = $user->wallet;

            if (!$wallet || $wallet->balance < $request->amount) {
                throw new \Exception('Saldo insuficiente!');
            }

            $wallet->decrement('balance', $request->amount);

            // Registra transação
            Transaction::create([
                'user_id' => $id,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'type' => 'debit',
                'amount_gross' => $request->amount,
                'amount_net' => -$request->amount,
                'fee' => 0,
                'status' => 'completed',
                'payer_name' => 'Administrador',
                'payer_email' => auth()->user()->email,
                'description' => $request->description ?? 'Saldo removido pelo administrador',
                'gateway_provider' => 'admin',
            ]);
        });

        return redirect()->route('admin.users.edit', $id)
            ->with('success', 'Saldo removido com sucesso!');
    }

    /**
     * Congela saldo da carteira do usuário (move de balance para frozen_balance)
     */
    public function freezeBalance(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function() use ($request, $id) {
                $user = User::findOrFail($id);
                $wallet = $user->wallet;

                if (!$wallet) {
                    throw new \Exception('Carteira não encontrada!');
                }

                $amount = floatval($request->amount);

                if ($wallet->balance < $amount) {
                    throw new \Exception('Saldo disponível insuficiente! Saldo disponível: R$ ' . number_format($wallet->balance, 2, ',', '.'));
                }

                // Move o valor de balance para frozen_balance
                $wallet->decrement('balance', $amount);
                $wallet->increment('frozen_balance', $amount);

                // Registra transação para auditoria (tipo debit administrativo)
                Transaction::create([
                    'user_id' => $id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'type' => 'debit',
                    'amount_gross' => $amount,
                    'amount_net' => -$amount,
                    'fee' => 0,
                    'status' => 'completed',
                    'payer_name' => 'Administrador',
                    'payer_email' => auth()->user()->email,
                    'description' => $request->description ?? 'Saldo congelado pelo administrador',
                    'gateway_provider' => 'admin',
                ]);

                \Illuminate\Support\Facades\Log::info('Saldo congelado pelo admin', [
                    'user_id' => $id,
                    'amount' => $amount,
                    'admin_id' => auth()->id(),
                    'description' => $request->description,
                ]);
            });

            return redirect()->route('admin.users.edit', $id)
                ->with('success', 'Saldo congelado com sucesso! O valor foi movido para bloqueio cautelar.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.edit', $id)
                ->with('error', 'Erro ao congelar saldo: ' . $e->getMessage());
        }
    }

    /**
     * Descongela saldo da carteira do usuário (move de frozen_balance para balance)
     */
    public function unfreezeBalance(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function() use ($request, $id) {
                $user = User::findOrFail($id);
                $wallet = $user->wallet;

                if (!$wallet) {
                    throw new \Exception('Carteira não encontrada!');
                }

                $amount = floatval($request->amount);

                if ($wallet->frozen_balance < $amount) {
                    throw new \Exception('Saldo congelado insuficiente! Saldo congelado: R$ ' . number_format($wallet->frozen_balance, 2, ',', '.'));
                }

                // Move o valor de frozen_balance de volta para balance
                $wallet->decrement('frozen_balance', $amount);
                $wallet->increment('balance', $amount);

                // Registra transação para auditoria (tipo credit administrativo)
                Transaction::create([
                    'user_id' => $id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'type' => 'credit',
                    'amount_gross' => $amount,
                    'amount_net' => $amount,
                    'fee' => 0,
                    'status' => 'completed',
                    'payer_name' => 'Administrador',
                    'payer_email' => auth()->user()->email,
                    'description' => $request->description ?? 'Saldo descongelado pelo administrador',
                    'gateway_provider' => 'admin',
                ]);

                \Illuminate\Support\Facades\Log::info('Saldo descongelado pelo admin', [
                    'user_id' => $id,
                    'amount' => $amount,
                    'admin_id' => auth()->id(),
                    'description' => $request->description,
                ]);
            });

            return redirect()->route('admin.users.edit', $id)
                ->with('success', 'Saldo descongelado com sucesso! O valor foi movido de volta para saldo disponível.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.edit', $id)
                ->with('error', 'Erro ao descongelar saldo: ' . $e->getMessage());
        }
    }

    /**
     * Aprova um usuário
     */
    public function approve(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $wasApproved = $user->is_approved;
        
        $user->update([
            'is_approved' => true,
            'is_blocked' => false,
            'kyc_status' => 'approved',
        ]);

        // Dispara evento se acabou de ser aprovado
        if (!$wasApproved && $user->is_approved) {
            event(new UserApproved($user));
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário aprovado com sucesso!');
    }

    /**
     * Bloqueia um usuário
     */
    public function block(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode bloquear a si mesmo!');
        }

        $user->update(['is_blocked' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário bloqueado com sucesso!');
    }

    /**
     * Desbloqueia um usuário
     */
    public function unblock(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode desbloquear a si mesmo!');
        }

        $user->update(['is_blocked' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário desbloqueado com sucesso!');
    }

    /**
     * Bloqueia/desbloqueia um usuário (mantido para compatibilidade)
     */
    public function toggleBlock(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode bloquear a si mesmo!');
        }

        $user->update(['is_blocked' => !$user->is_blocked]);

        $action = $user->is_blocked ? 'bloqueado' : 'desbloqueado';
        return redirect()->route('admin.users.index')
            ->with('success', "Usuário {$action} com sucesso!");
    }

    /**
     * Rejeita um usuário
     */
    public function reject(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update([
            'is_approved' => false,
            'kyc_status' => 'rejected',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário rejeitado com sucesso!');
    }

    /**
     * Aprova o KYC do usuário e libera o sistema automaticamente
     */
    public function approveKyc(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $wasApproved = $user->is_approved;
        
        $user->update([
            'kyc_status' => 'approved',
            'is_approved' => true, // Libera automaticamente o sistema
        ]);

        // Dispara evento se acabou de ser aprovado
        if (!$wasApproved && $user->is_approved) {
            event(new UserApproved($user));
        }

        return redirect()->route('admin.users.edit', $id)
            ->with('success', 'KYC aprovado com sucesso! O usuário agora pode usar o sistema.');
    }

    /**
     * Rejeita o KYC do usuário
     */
    public function rejectKyc(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'kyc_status' => 'rejected',
            'is_approved' => false, // Bloqueia o acesso ao sistema
        ]);

        return redirect()->route('admin.users.edit', $id)
            ->with('success', 'KYC rejeitado com sucesso!');
    }

    /**
     * Bloqueia saque de um usuário
     */
    public function blockWithdrawal(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['bloquear_saque' => true]);

        return redirect()->route('admin.users.edit', $id)
            ->with('success', 'Saque bloqueado com sucesso!');
    }

    /**
     * Desbloqueia saque de um usuário
     */
    public function unblockWithdrawal(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['bloquear_saque' => false]);

        return redirect()->route('admin.users.edit', $id)
            ->with('success', 'Saque desbloqueado com sucesso!');
    }

    /**
     * Remove um usuário
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir a si mesmo!');
        }

        if ($user->is_admin) {
            return back()->with('error', 'Não é possível excluir um administrador!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}