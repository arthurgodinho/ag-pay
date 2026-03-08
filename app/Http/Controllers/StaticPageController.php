<?php

namespace App\Http\Controllers;

use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    /**
     * Exibe uma página estática pública
     */
    public function show(string $slug): View
    {
        $page = StaticPage::findBySlug($slug);
        
        if (!$page) {
            abort(404, 'Página não encontrada');
        }

        return view('static.show', compact('page'));
    }

    /**
     * Exibe Termos de Uso
     */
    public function termos(): View
    {
        return $this->show('termos-uso');
    }

    /**
     * Exibe Política de Privacidade
     */
    public function privacidade(): View
    {
        return $this->show('privacidade');
    }

    /**
     * Exibe PLD (Prevenção à Lavagem de Dinheiro)
     */
    public function pld(): View
    {
        return $this->show('pld');
    }

    /**
     * Exibe Manual KYC
     */
    public function manualKyc(): View
    {
        return $this->show('manual-kyc');
    }
}
