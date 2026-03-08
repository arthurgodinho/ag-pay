<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Altera o idioma do sistema
     */
    public function changeLanguage(Request $request, string $locale)
    {
        $availableLocales = ['pt', 'es', 'en'];
        
        if (!in_array($locale, $availableLocales)) {
            return back()->with('error', __('common.language') . ' ' . __('common.error'));
        }
        
        // Salva na sessão ANTES de definir no app
        Session::put('locale', $locale);
        
        // Define o locale imediatamente
        app()->setLocale($locale);
        
        // Se o usuário estiver autenticado, salva no perfil
        if (Auth::check()) {
            try {
                Auth::user()->update(['language' => $locale]);
            } catch (\Exception $e) {
                \Log::error('Erro ao salvar idioma do usuário', ['error' => $e->getMessage()]);
            }
        }
        
        return redirect()->back()->with('success', __('common.language_changed'));
    }
}
