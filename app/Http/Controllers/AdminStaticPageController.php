<?php

namespace App\Http\Controllers;

use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminStaticPageController extends Controller
{
    /**
     * Lista todas as páginas estáticas
     */
    public function index(): View
    {
        $pages = StaticPage::orderBy('title')->get();
        return view('admin.static.index', compact('pages'));
    }

    /**
     * Exibe formulário de edição
     */
    public function edit(string $slug): View
    {
        $page = StaticPage::where('slug', $slug)->firstOrFail();
        return view('admin.static.edit', compact('page'));
    }

    /**
     * Atualiza uma página estática
     */
    public function update(Request $request, string $slug): RedirectResponse
    {
        $page = StaticPage::where('slug', $slug)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $page->update([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.static.index')
            ->with('success', 'Página atualizada com sucesso!');
    }
}
