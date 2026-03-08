<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PinController extends Controller
{
    /**
     * Exibe formulário de criação/alteracao de PIN
     */
    public function create(): View
    {
        $user = Auth::user();
        
        // Se não tem PIN configurado e está tentando acessar via settings, permite
        // Se já tem PIN configurado, permite alterar

        return view('dashboard.pin.create', ['user' => $user]);
    }

    /**
     * Salva o PIN
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pin' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            'pin_confirmation' => 'required|string|same:pin',
        ]);

        $user = Auth::user();
        
        $user->update([
            'pin' => Hash::make($request->pin),
            'pin_configured' => true,
        ]);

        return redirect()->route('dashboard.index')
            ->with('success', 'PIN configurado com sucesso!');
    }
}
