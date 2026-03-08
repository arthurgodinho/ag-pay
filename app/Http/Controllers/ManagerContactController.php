<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ManagerContactController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        
        // Se o usuário tem um gerente, usa o gerente
        if ($user->manager_id && $user->manager) {
            $manager = $user->manager;
            $whatsapp = $manager->phone ?? Setting::get('default_whatsapp', '');
            $managerName = $manager->name;
            $managerEmail = $manager->email;
            $managerPhoto = null;
            $isDefault = false;
        } else {
            // Caso contrário, usa o contato padrão do sistema
            $manager = null;
            $whatsapp = Setting::get('default_whatsapp', '');
            $systemName = \App\Helpers\LogoHelper::getSystemName();
            $managerName = Setting::get('default_manager_name', 'Suporte ' . $systemName);
            $managerEmail = Setting::get('default_manager_email', 'suporte@paguemax.com');
            $managerPhoto = Setting::get('default_manager_photo', '');
            $isDefault = true;
        }

        return view('dashboard.manager-contact.index', compact('manager', 'whatsapp', 'managerName', 'managerEmail', 'managerPhoto', 'isDefault'));
    }
}
