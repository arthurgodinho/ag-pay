<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AdminSupportController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportTicket::with(['user', 'assignedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'me') {
                $query->where('assigned_to', Auth::id());
            } elseif ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            }
        }

        $tickets = $query->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'total' => SupportTicket::count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }

    public function show(int $id): View
    {
        $ticket = SupportTicket::with(['messages.user', 'user', 'assignedTo'])
            ->findOrFail($id);

        // Marca mensagens como lidas
        $ticket->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Marca notificações deste ticket como lidas
        \App\Models\SupportNotification::where('ticket_id', $ticket->id)
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('admin.support.show', compact('ticket'));
    }

    public function assign(Request $request, int $id): RedirectResponse
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update([
            'assigned_to' => Auth::id(),
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'Ticket atribuído a você com sucesso!');
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,waiting,resolved,closed',
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'status' => $ticket->status,
        ]);
    }

    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240', // 10MB
        ]);

        $ticket = SupportTicket::findOrFail($id);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('support/attachments', 'public');
        }

        if (empty($request->message) && !$attachmentPath) {
            return response()->json([
                'success' => false,
                'message' => 'Você deve enviar uma mensagem ou um anexo.',
            ], 422);
        }

        $message = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message ?? '',
            'attachment' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $ticket->update([
            'status' => $ticket->status === 'closed' ? 'open' : ($ticket->status === 'resolved' ? 'in_progress' : $ticket->status),
            'last_message_at' => now(),
        ]);

        // Se a mensagem foi enviada por um admin, marca as notificações deste ticket como lidas para o admin
        // (não cria nova notificação, pois o admin está respondendo)
        if (Auth::user()->is_admin || Auth::user()->is_manager) {
            \App\Models\SupportNotification::where('ticket_id', $ticket->id)
                ->where('user_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => $message->load('user'),
        ]);
    }

    public function getMessages(int $id): JsonResponse
    {
        $ticket = SupportTicket::findOrFail($id);
        $messages = $ticket->messages()->with('user')->orderBy('created_at', 'asc')->get();

        // Marca como lidas
        $ticket->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'ticket_id' => $msg->ticket_id,
                    'user_id' => $msg->user_id,
                    'message' => $msg->message,
                    'attachment' => $msg->attachment,
                    'attachment_name' => $msg->attachment_name,
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at->toISOString(),
                    'user' => [
                        'id' => $msg->user->id,
                        'name' => $msg->user->name,
                        'is_admin' => $msg->user->is_admin ?? false,
                        'is_manager' => $msg->user->is_manager ?? false,
                    ],
                ];
            }),
        ]);
    }

    public function getNotificationsCount(): JsonResponse
    {
        $count = \App\Models\SupportNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getUnreadNotifications(): JsonResponse
    {
        $notifications = \App\Models\SupportNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->with(['ticket.user', 'message'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'ticket_id' => $notif->ticket_id,
                    'ticket_subject' => $notif->ticket->subject ?? '',
                    'user_name' => $notif->ticket->user->name ?? '',
                    'message_preview' => $notif->message ? substr($notif->message->message ?? '', 0, 100) : '',
                    'created_at' => $notif->created_at->toISOString(),
                ];
            }),
        ]);
    }
}