<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Helpers\WebhookUrlHelper;

class EfiGateway implements PaymentGatewayInterface
{
    private string $baseUrl = 'https://pix.api.efipay.com.br'; // Produção
    // private string $baseUrl = 'https://pix-h.api.efipay.com.br'; // Homologação (pode ser configurável)
    
    private ?string $clientId = null;
    private ?string $clientSecret = null;
    private ?string $certificatePath = null;
    private ?string $pixKey = null;

    public function __construct(?string $clientId = null, ?string $clientSecret = null, array $config = [])
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->certificatePath = $config['certificate_path'] ?? null;
        $this->pixKey = $config['pix_key'] ?? null;
        
        // Verifica se é homologação (opcional, pode ser via config)
        // Por padrão usa produção
    }

    private function getAccessToken(): ?string
    {
        if (!$this->clientId || !$this->clientSecret || !$this->certificatePath) {
            throw new \Exception('Efi: Credenciais ou Certificado não configurados');
        }

        if (!file_exists($this->certificatePath)) {
            throw new \Exception("Efi: Certificado não encontrado no caminho: {$this->certificatePath}");
        }

        $cacheKey = "efi_token_{$this->clientId}";
        $cachedToken = Cache::get($cacheKey);
        if ($cachedToken) return $cachedToken;

        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");
        
        try {
            $response = Http::withOptions([
                'cert' => $this->certificatePath,
            ])->withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/oauth/token", [
                'grant_type' => 'client_credentials'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                $expiresIn = $data['expires_in'] ?? 3600;
                
                if ($token) {
                    // Cacheia por um pouco menos que o tempo de expiração
                    Cache::put($cacheKey, $token, now()->addSeconds($expiresIn - 600));
                    return $token;
                }
            }
            
            Log::error('Efi: Erro ao obter token', ['body' => $response->body(), 'status' => $response->status()]);
            throw new \Exception('Efi: Falha ao obter token: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Efi: Exceção ao obter token', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->getAccessToken();
        
        $options = [
            'cert' => $this->certificatePath,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ]
        ];

        // Se for GET e tiver dados, passar como query params? 
        // Geralmente API Pix usa query params para filtros no GET, mas aqui assumo POST/PUT com body
        
        $http = Http::withOptions(['cert' => $this->certificatePath])
            ->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ]);
            
        if (strtolower($method) === 'get') {
            $response = $http->get("{$this->baseUrl}{$endpoint}", $data);
        } elseif (strtolower($method) === 'post') {
            $response = $http->post("{$this->baseUrl}{$endpoint}", $data);
        } elseif (strtolower($method) === 'put') {
            $response = $http->put("{$this->baseUrl}{$endpoint}", $data);
        } else {
            throw new \Exception("Método {$method} não suportado");
        }

        if (!$response->successful()) {
            Log::error("Efi: Erro na requisição {$endpoint}", ['body' => $response->body()]);
            // Tenta extrair mensagem de erro amigável
            $errorData = $response->json();
            $msg = $errorData['mensagem'] ?? $errorData['error_description'] ?? $response->body();
            throw new \Exception("Efi Error: {$msg}");
        }

        return $response->json();
    }

    public function getBalance(): array
    {
        try {
            $response = $this->makeRequest('GET', '/v2/gn/saldo');
            return [
                'balance' => isset($response['saldo']) ? floatval($response['saldo']) : 0.00
            ];
        } catch (\Exception $e) {
            Log::error('Efi: Erro ao consultar saldo', ['error' => $e->getMessage()]);
            // Retorna 0 em caso de erro para evitar quebras, mas loga o erro
            return ['balance' => 0.00];
        }
    }

    public function createPix(float $amount, array $payerData): array
    {
        // 1. Criar cobrança imediata (Cob)
        // POST /v2/cob
        
        $txid = $payerData['external_id'] ?? Str::uuid()->toString();
        // TxID no Pix tem regras (letras e números, 26 a 35 chars). UUID tem hífens e 36 chars.
        // Vamos gerar um TxID válido para API Pix se não fornecido ou limpar o UUID
        $txidClean = preg_replace('/[^a-zA-Z0-9]/', '', $txid);
        if (strlen($txidClean) > 35) $txidClean = substr($txidClean, 0, 35);
        if (strlen($txidClean) < 26) $txidClean = str_pad($txidClean, 26, '0', STR_PAD_LEFT);
        
        $payload = [
            'calendario' => [
                'expiracao' => 3600
            ],
            'valor' => [
                'original' => number_format($amount, 2, '.', '')
            ],
            'chave' => $this->pixKey, // Chave Pix é OBRIGATÓRIA para criar cobrança
            'solicitacaoPagador' => $payerData['description'] ?? 'Pagamento via PIX',
        ];
        
        if (!empty($payerData['cpf'])) {
            $payload['devedor'] = [
                'cpf' => preg_replace('/\D/', '', $payerData['cpf']),
                'nome' => $payerData['name'] ?? 'Cliente'
            ];
        }

        // Se tiver chave Pix configurada, usa ela. Se não, erro.
        if (empty($this->pixKey)) {
            throw new \Exception('Efi: Chave Pix não configurada no painel admin.');
        }

        // Criar Cobrança
        $response = $this->makeRequest('POST', '/v2/cob', $payload);
        
        // A resposta contém 'loc' (location) que usamos para gerar o QR Code
        $locId = $response['loc']['id'] ?? null;
        $txidRetornado = $response['txid'];
        
        if (!$locId) {
            throw new \Exception('Efi: Location ID não retornado na criação da cobrança');
        }
        
        // 2. Obter QR Code
        // GET /v2/loc/:id/qrcode
        $qrCodeResponse = $this->makeRequest('GET', "/v2/loc/{$locId}/qrcode");
        
        return [
            'status' => 'pending',
            'qr_code' => $qrCodeResponse['qrcode'] ?? '',
            'qr_code_image_url' => $qrCodeResponse['imagemQrcode'] ?? null,
            'transaction_id' => $txidRetornado,
            'external_id' => $txid, // Mantém referência original
            'raw_response' => array_merge($response, $qrCodeResponse)
        ];
    }

    public function createCreditCard(float $amount, array $cardData, array $payerData): array
    {
        throw new \Exception('Efi: Pagamento via cartão não implementado nesta integração.');
    }

    public function createBoleto(float $amount, array $payerData): array
    {
        throw new \Exception('Efi: Boleto não implementado.');
    }

    /**
     * Realiza um saque via PIX (Envio de Pix)
     */
    public function createPixPayment(float $amount, array $recipientData, ?string $externalId = null, ?string $description = null): array
    {
        // Envio de Pix
        // PUT /v3/gn/pix/:idEnvio (Recomendado para idempotência)
        
        $externalId = $externalId ?? Str::uuid()->toString();
        
        $payload = [
            'valor' => number_format($amount, 2, '.', ''),
            'pagador' => [
                'chave' => $this->pixKey
            ],
            'favorecido' => [
                'chave' => $recipientData['pix_key'] ?? $recipientData['key'] ?? ''
            ]
        ];
        
        try {
            // Usando v3 para idempotência
            $response = $this->makeRequest('PUT', "/v3/gn/pix/{$externalId}", $payload);
            
            return [
                'status' => 'pending', // Webhook confirmará
                'transaction_id' => $response['e2eId'] ?? null,
                'external_id' => $externalId,
                'raw_response' => $response,
            ];
        } catch (\Exception $e) {
            // Se falhar com v3, tenta v2 (apenas fallback, embora v3 seja o padrão agora)
            if (strpos($e->getMessage(), '404') !== false) {
                 try {
                     $response = $this->makeRequest('POST', '/v2/gn/pix/envio', $payload);
                     return [
                        'status' => 'pending',
                        'transaction_id' => $response['e2eId'] ?? null,
                        'external_id' => $externalId,
                        'raw_response' => $response,
                    ];
                 } catch (\Exception $ex) {
                     throw $e; // Lança o erro original
                 }
            }
            throw $e;
        }
    }

    public function consultTransaction(string $transactionId): array
    {
        // GET /v2/pix/:e2eid
        // Ou consultar cobrança: GET /v2/cob/:txid
        
        try {
            // Assume que transactionId é txid (cobrança)
            $response = $this->makeRequest('GET', "/v2/cob/{$transactionId}");
            
            $status = $response['status'] ?? 'unknown';
            // Mapeia status: ATIVA, CONCLUIDA, REMOVIDA_PELO_USUARIO_RECEBEDOR, REMOVIDA_PELO_PSP
            
            $mappedStatus = match($status) {
                'CONCLUIDA' => 'paid',
                'ATIVA' => 'pending',
                default => 'cancelled'
            };

            return [
                'status' => $mappedStatus,
                'raw_response' => $response
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
