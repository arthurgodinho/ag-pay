<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PaymentSplit;
use App\Models\User;
use App\Services\PaymentSplitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SplitController extends Controller
{
    /**
     * Lista todos os splits do usuário
     */
    public function index(): View
    {
        $splits = PaymentSplit::where('user_id', Auth::id())
            ->with('recipient')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.split.index', compact('splits'));
    }

    /**
     * Exibe formulário de criação
     */
    public function create(): View
    {
        return view('dashboard.split.create');
    }

    /**
     * Busca usuário por email para split
     */
    public function searchUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->where('id', '!=', Auth::id())
            ->where('is_approved', true)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado ou não aprovado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Salva um novo split
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_user_id' => 'required|exists:users,id',
            'split_type' => 'required|in:percentage,fixed',
            'split_value' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|min:0',
        ]);

        // Validação adicional
        if ($request->split_type === 'percentage' && $request->split_value > 100) {
            return back()->withErrors(['split_value' => 'O percentual não pode ser maior que 100%'])->withInput();
        }

        PaymentSplit::create([
            'user_id' => Auth::id(),
            'recipient_user_id' => $request->recipient_user_id,
            'split_type' => $request->split_type,
            'split_value' => $request->split_value,
            'description' => $request->description,
            'priority' => $request->priority ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('dashboard.split.index')
            ->with('success', 'Split configurado com sucesso!');
    }

    /**
     * Exibe formulário de edição
     */
    public function edit(int $id): View
    {
        $split = PaymentSplit::where('user_id', Auth::id())
            ->with('recipient')
            ->findOrFail($id);

        return view('dashboard.split.edit', compact('split'));
    }

    /**
     * Atualiza um split
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $split = PaymentSplit::where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'recipient_user_id' => 'required|exists:users,id',
            'split_type' => 'required|in:percentage,fixed',
            'split_value' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->split_type === 'percentage' && $request->split_value > 100) {
            return back()->withErrors(['split_value' => 'O percentual não pode ser maior que 100%'])->withInput();
        }

        $split->update([
            'recipient_user_id' => $request->recipient_user_id,
            'split_type' => $request->split_type,
            'split_value' => $request->split_value,
            'description' => $request->description,
            'priority' => $request->priority ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('dashboard.split.index')
            ->with('success', 'Split atualizado com sucesso!');
    }

    /**
     * Remove um split
     */
    public function destroy(int $id): RedirectResponse
    {
        $split = PaymentSplit::where('user_id', Auth::id())
            ->findOrFail($id);

        $split->delete();

        return redirect()->route('dashboard.split.index')
            ->with('success', 'Split removido com sucesso!');
    }

    /**
     * Calcula preview de splits
     */
    public function preview(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $splitService = new PaymentSplitService();
        $result = $splitService->calculateSplits(Auth::id(), $request->amount);

        return response()->json($result);
    }
}
