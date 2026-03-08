<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminEmailCampaignController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Lista todas as campanhas
     */
    public function index(): View
    {
        $campaigns = EmailCampaign::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.email-campaigns.index', compact('campaigns'));
    }

    /**
     * Mostra formulário de criação
     */
    public function create(): View
    {
        return view('admin.email-campaigns.create');
    }

    /**
     * Salva nova campanha
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign = EmailCampaign::create([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'body_text' => $validated['body_text'] ?? strip_tags($validated['body_html']),
            'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        Log::info('Campanha de email criada', ['campaign_id' => $campaign->id]);

        return redirect()->route('admin.email-campaigns.index')
            ->with('success', 'Campanha criada com sucesso!');
    }

    /**
     * Envia campanha para todos os usuários
     */
    public function send(Request $request, int $id): RedirectResponse
    {
        $campaign = EmailCampaign::findOrFail($id);
        
        if ($campaign->status === 'sent') {
            return back()->with('error', 'Esta campanha já foi enviada!');
        }

        // Busca usuários que aceitam emails de marketing
        $users = User::where('receive_marketing_emails', true)
            ->whereNotNull('email')
            ->where('email_verified', true)
            ->get();

        $campaign->update([
            'status' => 'sending',
            'total_recipients' => $users->count(),
        ]);

        $sentCount = 0;
        $failedCount = 0;

        foreach ($users as $user) {
            try {
                $rendered = $campaign->renderForUser($user);
                
                $success = $this->emailService->sendEmail(
                    $user->email,
                    $rendered['subject'],
                    $rendered['html'],
                    $rendered['text'],
                    $user,
                    'marketing_campaign'
                );

                if ($success) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::error('Erro ao enviar email da campanha', [
                    'campaign_id' => $campaign->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
        ]);

        return back()->with('success', "Campanha enviada! {$sentCount} emails enviados com sucesso.");
    }

    /**
     * Mostra campanha
     */
    public function show(int $id): View
    {
        $campaign = EmailCampaign::with('emailLogs')->findOrFail($id);
        return view('admin.email-campaigns.show', compact('campaign'));
    }
}

