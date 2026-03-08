<?php

namespace App\Http\Controllers;

use App\Models\SmtpSetting;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminSmtpController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Exibe página de configuração SMTP
     */
    public function index(): View
    {
        $smtp = SmtpSetting::first() ?? new SmtpSetting();
        return view('admin.smtp.index', compact('smtp'));
    }

    /**
     * Salva/Atualiza configurações SMTP
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mailer' => 'required|in:smtp,sendmail,mailgun,ses,postmark',
            'host' => 'required_if:mailer,smtp|nullable|string|max:255',
            'port' => 'required_if:mailer,smtp|nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'encryption' => 'nullable|in:tls,ssl,null',
            'from_address' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Se password está vazio, mantém o anterior
        $smtp = SmtpSetting::first();
        if (empty($validated['password']) && $smtp) {
            $validated['password'] = $smtp->password;
        } else {
            $validated['password'] = encrypt($validated['password'] ?? '');
        }

        if ($validated['encryption'] === 'null') {
            $validated['encryption'] = null;
        }

        $smtp = SmtpSetting::updateOrCreate(
            ['id' => $smtp->id ?? null],
            $validated
        );

        Log::info('Configurações SMTP atualizadas', ['smtp_id' => $smtp->id]);

        return back()->with('success', 'Configurações SMTP salvas com sucesso!');
    }

    /**
     * Testa conexão SMTP
     */
    public function test(Request $request)
    {
        try {
            // Cria settings temporárias para teste
            $settings = new SmtpSetting();
            $settings->mailer = $request->mailer ?? $request->input('mailer');
            $settings->host = $request->host ?? $request->input('host');
            $settings->port = $request->port ?? $request->input('port');
            $settings->username = $request->username ?? $request->input('username');
            // Se a senha não foi fornecida no request, busca da configuração atual
            if (empty($request->password) && empty($request->input('password'))) {
                $currentSmtp = SmtpSetting::first();
                if ($currentSmtp) {
                    $settings->password = $currentSmtp->decrypted_password;
                } else {
                    $settings->password = '';
                }
            } else {
                $settings->password = $request->password ?? $request->input('password');
            }
            $encryption = $request->encryption ?? $request->input('encryption');
            $settings->encryption = ($encryption === 'null' || empty($encryption)) ? null : $encryption;
            $settings->from_address = $request->from_address ?? $request->input('from_address');
            $settings->from_name = $request->from_name ?? $request->input('from_name');
            $settings->is_active = true;

            $result = $this->emailService->testSmtpConnection($settings);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($result);
            }

            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao testar SMTP: ' . $e->getMessage();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }
            
            return back()->with('error', $errorMessage);
        }
    }
}

