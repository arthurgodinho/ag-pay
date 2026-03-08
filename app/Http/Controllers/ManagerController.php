<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ManagerController extends Controller
{
    /**
     * Exibe informações do gerente da conta
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();
        $manager = $user->manager;

        return view('dashboard.manager.index', compact('manager'));
    }
}
