<?php

namespace App\Services;

use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as LaravelLog;
use Throwable;

class ErrorLogService
{
    /**
     * Registra um erro no sistema
     *
     * @param string $title
     * @param string $message
     * @param string|null $type
     * @param string $level
     * @param array|null $context
     * @param Throwable|null $exception
     * @return ErrorLog
     */
    public static function log(
        string $title,
        string $message,
        ?string $type = null,
        string $level = 'error',
        ?array $context = null,
        ?Throwable $exception = null
    ): ErrorLog {
        // Para webhooks, o user_id pode ser null (requisições externas)
        $userId = Auth::id();
        // Se não houver usuário autenticado (webhook), tenta pegar do contexto
        if (!$userId && isset($context['user_id'])) {
            $userId = $context['user_id'];
        }

        // Para webhooks, pode não haver request (requisições de fora)
        $ipAddress = null;
        $userAgent = null;
        try {
            $ipAddress = request()->ip();
            $userAgent = request()->userAgent();
        } catch (\Exception $e) {
            // Se não houver request, usa do contexto
            $ipAddress = $context['ip_address'] ?? null;
            $userAgent = $context['user_agent'] ?? null;
        }

        $data = [
            'level' => $level,
            'type' => $type ?? 'system',
            'title' => $title,
            'message' => $message,
            'context' => $context,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ];

        // Se houver uma exceção, captura informações adicionais
        if ($exception) {
            $data['file'] = $exception->getFile();
            $data['line'] = $exception->getLine();
            $data['trace'] = $exception->getTraceAsString();
            $data['message'] = $message . ' | Exception: ' . $exception->getMessage();
        }

        // Adiciona transaction_id se estiver no contexto
        if (isset($context['transaction_id'])) {
            $data['transaction_id'] = $context['transaction_id'];
        }

        $errorLog = ErrorLog::create($data);

        // Também registra no log padrão do Laravel
        LaravelLog::{$level}($title . ': ' . $message, $context ?? []);

        return $errorLog;
    }

    /**
     * Registra erro de pagamento
     */
    public static function logPaymentError(
        string $message,
        ?string $transactionId = null,
        ?array $context = null,
        ?Throwable $exception = null
    ): ErrorLog {
        return self::log(
            'Erro no Processamento de Pagamento',
            $message,
            'payment',
            'error',
            array_merge($context ?? [], ['transaction_id' => $transactionId]),
            $exception
        );
    }

    /**
     * Registra erro de saque
     */
    public static function logWithdrawalError(
        string $message,
        ?string $transactionId = null,
        ?array $context = null,
        ?Throwable $exception = null
    ): ErrorLog {
        return self::log(
            'Erro no Processamento de Saque',
            $message,
            'withdrawal',
            'error',
            array_merge($context ?? [], ['transaction_id' => $transactionId]),
            $exception
        );
    }

    /**
     * Registra erro de API
     */
    public static function logApiError(
        string $message,
        ?string $provider = null,
        ?array $context = null,
        ?Throwable $exception = null
    ): ErrorLog {
        return self::log(
            'Erro na Integração com API' . ($provider ? ' - ' . $provider : ''),
            $message,
            'api',
            'error',
            array_merge($context ?? [], ['provider' => $provider]),
            $exception
        );
    }

    /**
     * Registra erro de produto
     */
    public static function logProductError(
        string $message,
        ?int $userId = null,
        ?array $context = null,
        ?Throwable $exception = null
    ): ErrorLog {
        return self::log(
            'Erro ao Criar/Processar Produto',
            $message,
            'product',
            'error',
            $context,
            $exception
        );
    }

    /**
     * Registra erro crítico do sistema
     */
    public static function logCritical(
        string $title,
        string $message,
        ?array $context = null,
        ?Throwable $exception = null
    ): ErrorLog {
        return self::log(
            $title,
            $message,
            'system',
            'critical',
            $context,
            $exception
        );
    }

    /**
     * Registra erro de webhook
     */
    public static function logWebhookError(
        string $message,
        string $webhookType = 'webhook',
        ?array $payload = null,
        ?array $context = null,
        ?Throwable $exception = null
    ): ErrorLog {
        $contextData = array_merge($context ?? [], [
            'webhook_type' => $webhookType,
            'payload' => $payload,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'headers' => request()->headers->all(),
        ]);

        return self::log(
            'Erro no Processamento de Webhook - ' . ucfirst($webhookType),
            $message,
            'api',
            'error',
            $contextData,
            $exception
        );
    }

    /**
     * Registra webhook não identificado (requisição não reconhecida)
     */
    public static function logUnidentifiedWebhook(
        string $provider,
        ?array $payload = null,
        ?string $reason = null
    ): ErrorLog {
        return self::log(
            'Webhook Não Identificado - ' . ucfirst($provider),
            $reason ?? 'Requisição recebida mas não foi possível identificar ou processar a transação.',
            'api',
            'warning',
            [
                'provider' => $provider,
                'payload' => $payload,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'headers' => request()->headers->all(),
                'url' => request()->fullUrl(),
            ]
        );
    }
}



