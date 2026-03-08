<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IntegrationsController extends Controller
{
    /**
     * Exibe a página de integrações
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();
        
        $shopifyIntegration = Integration::where('user_id', $user->id)
            ->where('platform', 'shopify')
            ->first();
            
        $woocommerceIntegration = Integration::where('user_id', $user->id)
            ->where('platform', 'woocommerce')
            ->first();

        return view('dashboard.integrations.index', compact('shopifyIntegration', 'woocommerceIntegration'));
    }
}








