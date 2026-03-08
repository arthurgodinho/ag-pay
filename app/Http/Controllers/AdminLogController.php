<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminLogController extends Controller
{
    /**
     * Exibe a lista de logs de erros
     */
    public function index(Request $request): View
    {
        $query = ErrorLog::with(['user', 'resolver'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('resolved')) {
            $query->where('resolved', $request->resolved === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        // Estatísticas
        $stats = [
            'total' => ErrorLog::count(),
            'unresolved' => ErrorLog::where('resolved', false)->count(),
            'critical' => ErrorLog::where('level', 'critical')->where('resolved', false)->count(),
            'today' => ErrorLog::whereDate('created_at', today())->count(),
        ];

        return view('admin.logs.index', compact('logs', 'stats'));
    }

    /**
     * Exibe detalhes de um log específico
     */
    public function show(int $id): View
    {
        $log = ErrorLog::with(['user', 'resolver'])->findOrFail($id);
        
        return view('admin.logs.show', compact('log'));
    }

    /**
     * Marca um erro como resolvido
     */
    public function resolve(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $log = ErrorLog::findOrFail($id);
        
        $log->update([
            'resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
            'resolution_notes' => $request->resolution_notes,
        ]);

        return redirect()->route('admin.logs.index')
            ->with('success', 'Erro marcado como resolvido!');
    }

    /**
     * Marca um erro como não resolvido
     */
    public function unresolve(int $id): RedirectResponse
    {
        $log = ErrorLog::findOrFail($id);
        
        $log->update([
            'resolved' => false,
            'resolved_at' => null,
            'resolved_by' => null,
            'resolution_notes' => null,
        ]);

        return redirect()->route('admin.logs.index')
            ->with('success', 'Erro marcado como não resolvido!');
    }

    /**
     * Deleta um log
     */
    public function destroy(int $id): RedirectResponse
    {
        $log = ErrorLog::findOrFail($id);
        $log->delete();

        return redirect()->route('admin.logs.index')
            ->with('success', 'Log deletado com sucesso!');
    }
}








