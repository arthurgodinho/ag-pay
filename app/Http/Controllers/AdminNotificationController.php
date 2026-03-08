<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notification::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create(): View
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'banner_url' => 'nullable|url',
            'type' => 'required|in:info,success,warning,error',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'banner_url' => $request->banner_url,
            'type' => $request->type,
            'is_active' => $request->has('is_active'),
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ]);

        // Cria notificações para todos os usuários
        $users = User::where('is_admin', false)->get();
        foreach ($users as $user) {
            \App\Models\UserNotification::create([
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notificação criada e enviada para todos os usuários!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notificação excluída com sucesso!');
    }
}