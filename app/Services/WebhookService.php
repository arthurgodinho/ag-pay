<?php

namespace App\Services;

use App\Jobs\SendWebhookJob;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    /**
     * Dispara um evento de webhook para o usuário
     *
     * @param string $event Nome do evento (ex: transaction.completed)
     * @param array $data Dados do evento
     * @param int $userId ID do usuário dono da transação
     * @return void
     */
    public function dispatch(string $event, array $data, int $userId): void
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return;
            }

            // Busca todos os tokens ativos com webhook_url configurada
            $tokens = $user->apiTokens()
                ->where('is_active', true)
                ->whereNotNull('webhook_url')
                ->where('webhook_url', '!=', '')
                ->get();

            if ($tokens->isEmpty()) {
                return;
            }

            $payload = [
                'event' => $event,
                'data' => $data,
                'created_at' => now()->toIso8601String(),
            ];

            foreach ($tokens as $token) {
                // Dispara job para envio assíncrono
                SendWebhookJob::dispatch($token->webhook_url, $payload, $token->token);
                
                Log::info('Webhook disparado', [
                    'event' => $event,
                    'user_id' => $userId,
                    'url' => $token->webhook_url
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao disparar webhook service', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'event' => $event
            ]);
        }
    }
}
