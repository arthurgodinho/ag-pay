<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class SupportController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $tickets = SupportTicket::where('user_id', $user->id)
            ->with(['assignedTo', 'messages'])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.support.index', compact('tickets'));
    }

    public function show(int $id): View
    {
        $ticket = SupportTicket::with(['messages.user', 'assignedTo'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Marca mensagens como lidas
        $ticket->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('dashboard.support.show', compact('ticket'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        if ($request->description) {
            $message = SupportMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $request->description,
            ]);

            // Cria notificações para todos os administradores
            $admins = \App\Models\User::where('is_admin', true)
                ->orWhere('is_manager', true)
                ->get();
            
            foreach ($admins as $admin) {
                \App\Models\SupportNotification::create([
                    'ticket_id' => $ticket->id,
                    'message_id' => $message->id,
                    'user_id' => $admin->id,
                    'is_read' => false,
                ]);
            }
        }

        return response()->json(['success' => true, 'ticket' => $ticket]);
    }

    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240', // 10MB
        ]);

        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Verifica se o ticket está fechado
        if ($ticket->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Este chat foi fechado pelo administrador. Você não pode mais enviar mensagens.',
            ], 403);
        }

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
            'status' => $ticket->status === 'resolved' ? 'open' : $ticket->status,
            'last_message_at' => now(),
        ]);

        // Cria notificações para todos os administradores
        $admins = \App\Models\User::where('is_admin', true)
            ->orWhere('is_manager', true)
            ->get();
        
        foreach ($admins as $admin) {
            \App\Models\SupportNotification::create([
                'ticket_id' => $ticket->id,
                'message_id' => $message->id,
                'user_id' => $admin->id,
                'is_read' => false,
            ]);
        }

        // Recarrega o ticket para obter o status atualizado
        $ticket->refresh();

        return response()->json([
            'success' => true,
            'message' => $message->load('user'),
            'ticket_status' => $ticket->status,
        ]);
    }

    public function getMessages(int $id): JsonResponse
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $messages = $ticket->messages()->with('user')->orderBy('created_at', 'asc')->get();

        // Marca como lidas
        $ticket->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Recarrega o ticket para obter o status atualizado
        $ticket->refresh();

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
            'ticket_status' => $ticket->status,
        ]);
    }
}