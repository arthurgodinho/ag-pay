<?php

namespace App\Services;

use App\Models\SmtpSetting;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Config;

class EmailService
{
    protected $smtpSettings;

    public function __construct()
    {
        $this->loadSmtpSettings();
    }

    /**
     * Carrega configurações SMTP do banco
     */
    protected function loadSmtpSettings()
    {
        $settings = SmtpSetting::getActive();
        if ($settings) {
            $settings->applyToConfig();
            $this->smtpSettings = $settings;
        }
    }

    /**
     * Envia email usando template
     */
    public function sendTemplateEmail($type, $user, array $variables = [])
    {
        try {
            $template = EmailTemplate::getByType($type);
            
            if (!$template) {
                Log::warning("Email template não encontrado: {$type}");
                return false;
            }

            $rendered = $template->render(array_merge([
                'user_name' => $user->name ?? 'Usuário',
                'user_email' => $user->email,
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
            ], $variables));

            return $this->sendEmail(
                $user->email,
                $rendered['subject'],
                $rendered['html'],
                $rendered['text'],
                $user,
                $type
            );

        } catch (\Exception $e) {
            Log::error("Erro ao enviar email template {$type}", [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envia email personalizado
     */
    public function sendEmail($toEmail, $subject, $htmlBody, $textBody = null, $user = null, $type = null)
    {
        try {
            // Cria log de email
            $log = EmailLog::create([
                'type' => $type,
                'user_id' => $user->id ?? null,
                'to_email' => $toEmail,
                'subject' => $subject,
                'body_html' => $htmlBody,
                'status' => 'pending',
            ]);

            // Recarrega configurações SMTP antes de enviar (garante que está atualizado)
            $this->loadSmtpSettings();
            
            // Se não tiver configuração SMTP ativa, retorna false
            if (!$this->smtpSettings || !$this->smtpSettings->is_active) {
                $log->markAsFailed('SMTP não configurado');
                Log::warning('Tentativa de enviar email sem SMTP configurado');
                return false;
            }
            
            // Aplica configurações SMTP antes de enviar
            $this->smtpSettings->applyToConfig();
            
            // Obtém remetente das configurações SMTP
            $fromAddress = $this->smtpSettings->from_address ?? config('mail.from.address');
            $fromName = $this->smtpSettings->from_name ?? config('mail.from.name');

            // Envia email
            Mail::send([], [], function (Message $message) use ($toEmail, $subject, $htmlBody, $textBody, $fromAddress, $fromName) {
                $message->from($fromAddress, $fromName)
                    ->to($toEmail)
                    ->subject($subject)
                    ->html($htmlBody);
                
                if ($textBody) {
                    $message->text($textBody);
                }
            });

            // Marca como enviado
            $log->markAsSent();

            // Atualiza último email enviado do usuário
            if ($user) {
                $user->update(['last_email_sent_at' => now()]);
            }

            return true;

        } catch (\Exception $e) {
            if (isset($log)) {
                $log->markAsFailed($e->getMessage());
            }
            
            Log::error('Erro ao enviar email', [
                'to' => $toEmail,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Envia email de boas-vindas para novo usuário
     */
    public function sendUserRegisteredEmail(User $user)
    {
        return $this->sendTemplateEmail('user_registered', $user, [
            'register_date' => $user->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Envia email de cadastro em análise
     */
    public function sendRegistrationPendingEmail(User $user)
    {
        return $this->sendTemplateEmail('user_registration_pending', $user);
    }

    /**
     * Envia email de conta aprovada
     */
    public function sendAccountApprovedEmail(User $user)
    {
        return $this->sendTemplateEmail('user_approved', $user, [
            'approval_date' => now()->format('d/m/Y H:i'),
            'login_url' => route('login'),
        ]);
    }

    /**
     * Envia email de cadastro incompleto
     */
    public function sendIncompleteRegistrationEmail(User $user)
    {
        return $this->sendTemplateEmail('user_incomplete_registration', $user, [
            'complete_registration_url' => route('auth.register'),
        ]);
    }

    /**
     * Envia email de pagamento recebido
     */
    public function sendPaymentReceivedEmail(User $user, $transaction)
    {
        return $this->sendTemplateEmail('payment_received', $user, [
            'transaction_id' => $transaction->uuid ?? $transaction->id,
            'amount' => 'R$ ' . number_format($transaction->amount_gross ?? $transaction->amount ?? 0, 2, ',', '.'),
            'payment_method' => $transaction->type ?? 'PIX',
            'transaction_date' => ($transaction->created_at ?? now())->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Envia email de pagamento enviado
     */
    public function sendPaymentSentEmail(User $user, $transaction)
    {
        return $this->sendTemplateEmail('payment_sent', $user, [
            'transaction_id' => $transaction->uuid ?? $transaction->id,
            'amount' => 'R$ ' . number_format($transaction->amount_gross ?? $transaction->amount ?? 0, 2, ',', '.'),
            'payment_method' => $transaction->type ?? 'PIX',
            'transaction_date' => ($transaction->created_at ?? now())->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Envia email de pagamento pendente
     */
    public function sendPaymentPendingEmail(User $user, $transaction)
    {
        return $this->sendTemplateEmail('payment_pending', $user, [
            'transaction_id' => $transaction->uuid ?? $transaction->id,
            'amount' => 'R$ ' . number_format($transaction->amount_gross ?? $transaction->amount ?? 0, 2, ',', '.'),
            'payment_method' => $transaction->type ?? 'PIX',
            'payment_url' => $transaction->payment_url ?? route('dashboard'),
        ]);
    }

    /**
     * Envia email de pagamento falhou
     */
    public function sendPaymentFailedEmail(User $user, $transaction)
    {
        return $this->sendTemplateEmail('payment_failed', $user, [
            'transaction_id' => $transaction->uuid ?? $transaction->id,
            'amount' => 'R$ ' . number_format($transaction->amount_gross ?? $transaction->amount ?? 0, 2, ',', '.'),
            'error_message' => $transaction->error_message ?? 'Erro desconhecido',
        ]);
    }

    /**
     * Testa configuração SMTP
     */
    public function testSmtpConnection($settings = null)
    {
        try {
            if ($settings) {
                $settings->applyToConfig();
            } else {
                $this->loadSmtpSettings();
            }

            Mail::raw('Este é um email de teste do sistema.', function (Message $message) {
                $fromAddress = config('mail.from.address');
                $fromName = config('mail.from.name');
                
                $message->to($fromAddress)
                    ->subject('Teste de SMTP - ' . config('app.name'));
            });

            return ['success' => true, 'message' => 'Email de teste enviado com sucesso!'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro ao enviar email de teste: ' . $e->getMessage()];
        }
    }
}

