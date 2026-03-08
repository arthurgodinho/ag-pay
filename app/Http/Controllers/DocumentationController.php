<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocumentationController extends Controller
{
    /**
     * Exibe a página de documentação
     */
    public function index(): View
    {
        $user = Auth::user();
        $apiToken = $user->apiTokens()->where('is_active', true)->first();
        
        return view('dashboard.documentation.index', [
            'apiToken' => $apiToken,
            'baseUrl' => config('app.url') . '/api/v1',
        ]);
    }
}
