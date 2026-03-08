<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $url;
    public $payload;
    public $secret;

    /**
     * Create a new job instance.
     */
    public function __construct(string $url, array $payload, string $secret)
    {
        $this->url = $url;
        $this->payload = $payload;
        $this->secret = $secret;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Calcula assinatura HMAC SHA256 para segurança
            $signature = hash_hmac('sha256', json_encode($this->payload), $this->secret);

            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Webhook-Signature' => $signature,
                    'User-Agent' => config('app.name', 'PagueMax') . '-Webhook/1.0',
                ])
                ->post($this->url, $this->payload);

            if (!$response->successful()) {
                Log::warning('Falha na entrega do Webhook', [
                    'url' => $this->url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $this->payload
                ]);
                
                // Se falhar (ex: 500, 502), tenta novamente em 60 segundos (até 3 vezes por padrão do Laravel)
                if ($response->serverError()) {
                    $this->release(60);
                }
            } else {
                Log::info('Webhook entregue com sucesso', [
                    'url' => $this->url,
                    'status' => $response->status()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro de conexão no Webhook', [
                'url' => $this->url,
                'error' => $e->getMessage(),
            ]);
            
            // Tenta novamente em 60 segundos
            $this->release(60);
        }
    }
}
