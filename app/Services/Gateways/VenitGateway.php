<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gateway de integração com a Venit API
 * Documentação: https://venit.readme.io/reference/introdução
 */
class VenitGateway implements PaymentGatewayInterface
{
    private string $baseUrl = 'https://api.venitip.com.br/functions/v1';
    
    private ?string $secretKey = null;
    private ?string $companyId = null;

    /**
     * Construtor
     * IMPORTANTE: Secret Key é o username, Company ID é o password no Basic Auth
     * 
     * @param string|null $secretKey Secret Key da Venit (username)
     * @param string|null $companyId Company ID da Venit (password)
     */
    public function __construct(?string $secretKey = null, ?string $companyId = null)
    {
        $this->secretKey = !empty($secretKey) ? trim($secretKey) : null;
        $this->companyId = !empty($companyId) ? trim($companyId) : null;
    }

    /**
     * Gera o cabeçalho de autenticação Basic Auth
     * Formato: Basic base64(SecretKey:CompanyId)
     * 
     * @return string
     */
    private function getAuthHeader(): string
    {
        if (empty($this->secretKey) || empty($this->companyId)) {
            throw new \Exception('Venit: Secret Key e Company ID são obrigatórios. Verifique as credenciais no painel administrativo.');
        }

        // Formato correto: SecretKey:CompanyId (conforme documentação)
        // Garante que ambos são strings antes de concatenar
        $secretKeyStr = is_array($this->secretKey) ? json_encode($this->secretKey) : (string) $this->secretKey;
        $companyIdStr = is_array($this->companyId) ? json_encode($this->companyId) : (string) $this->companyId;
        
        $credentials = trim($secretKeyStr) . ':' . trim($companyIdStr);
        return 'Basic ' . base64_encode($credentials);
    }

    /**
     * Faz uma requisição autenticada para a API Venit
     * 
     * @param string $method Método HTTP (GET, POST, etc)
     * @param string $endpoint Endpoint da API (ex: /transactions/pix)
     * @param array $data Dados do payload (para POST/PUT)
     * @param array $headers Headers adicionais
     * @return array
     */
    private function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $authHeader = $this->getAuthHeader();

        $requestHeaders = array_merge([
            'Authorization' => $authHeader,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], $headers);

        try {
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
            
            Log::info('VenitGateway: Enviando requisição', [
                'method' => $method,
                'endpoint' => $endpoint,
                'url' => $url,
                'has_data' => !empty($data),
            ]);

            // Valida que $data é array antes de enviar
            if (!is_array($data)) {
                Log::error('VenitGateway: Dados para requisição não são array', [
                    'data_type' => gettype($data),
                    'method' => $method,
                    'endpoint' => $endpoint,
                ]);
                throw new \Exception('Dados inválidos para enviar à API Venit. Esperado array.');
            }
            
            // Envia requisição com tratamento de erro para Array to string conversion
            try {
                $response = Http::withHeaders($requestHeaders)
                    ->timeout(30)
                    ->{strtolower($method)}($url, $data);
            } catch (\TypeError $typeError) {
                // Captura erro "Array to string conversion"
                Log::error('VenitGateway: TypeError ao enviar requisição HTTP', [
                    'error' => $typeError->getMessage(),
                    'file' => $typeError->getFile(),
                    'line' => $typeError->getLine(),
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'data_keys' => is_array($data) ? array_keys($data) : 'not array',
                ]);
                throw new \Exception('Erro ao enviar dados para a API Venit. Verifique se todos os dados estão no formato correto.');
            }

            Log::info('VenitGateway: Resposta recebida', [
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            // Tratamento de erro 401
            if ($response->status() === 401) {
                $errorBody = $response->body();
                $errorJson = $response->json();
                
                Log::error("Venit: Erro 401 - Credenciais inválidas", [
                    'status' => 401,
                    'response_body' => $errorBody,
                    'error_json' => $errorJson,
                ]);
                
                $errorMessage = 'Credenciais inválidas';
                if (is_array($errorJson)) {
                    $msg = $errorJson['message'] ?? $errorJson['error'] ?? null;
                    if (is_array($msg)) {
                        $errorMessage = json_encode($msg, JSON_UNESCAPED_UNICODE);
                    } elseif (is_string($msg)) {
                        $errorMessage = $msg;
                    } elseif (is_scalar($msg)) {
                        $errorMessage = (string) $msg;
                    }
                } elseif (is_string($errorBody)) {
                    $errorMessage = $errorBody;
                }
                
                // Garante que errorMessage é sempre string
                $errorMessage = is_string($errorMessage) ? $errorMessage : (is_scalar($errorMessage) ? (string) $errorMessage : 'Erro desconhecido');
                
                throw new \Exception("Venit: Credenciais inválidas (401). Verifique se a Secret Key e o Company ID estão corretos no painel administrativo. Erro: {$errorMessage}");
            }

            if (!$response->successful()) {
                $errorBody = $response->body();
                $errorJson = $response->json();
                $errorMessage = 'Erro desconhecido';
                
                // Garante que errorMessage é sempre string
                if (is_array($errorJson)) {
                    $msg = $errorJson['message'] ?? $errorJson['error'] ?? $errorJson['error_description'] ?? null;
                    if (is_array($msg)) {
                        $errorMessage = json_encode($msg, JSON_UNESCAPED_UNICODE);
                    } elseif (is_string($msg)) {
                        $errorMessage = $msg;
                    } elseif (is_scalar($msg)) {
                        $errorMessage = (string) $msg;
                    }
                } elseif (is_string($errorBody)) {
                    $errorMessage = $errorBody;
                } elseif (is_scalar($errorBody)) {
                    $errorMessage = (string) $errorBody;
                }
                
                // Garante que errorMessage é sempre string antes de usar
                $errorMessage = is_string($errorMessage) ? $errorMessage : (is_scalar($errorMessage) ? (string) $errorMessage : 'Erro desconhecido');
                
                Log::error("Venit: Erro na requisição", [
                    'status' => $response->status(),
                    'endpoint' => $endpoint,
                    'error_message' => $errorMessage,
                    'response_body' => is_string($errorBody) ? $errorBody : json_encode($errorBody, JSON_UNESCAPED_UNICODE),
                ]);
                
                throw new \Exception("Venit: Erro na requisição - Status {$response->status()} - {$errorMessage}");
            }

            $jsonResponse = $response->json();
            
            if (!is_array($jsonResponse)) {
                Log::error('VenitGateway: Resposta JSON inválida', [
                    'response_type' => gettype($jsonResponse),
                    'response' => $jsonResponse,
                ]);
                throw new \Exception('Venit: Resposta inválida do servidor. Formato esperado: JSON array.');
            }
            
            return $jsonResponse;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Venit: Erro de conexão", [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint,
            ]);
            throw new \Exception("Venit: Erro de conexão com o gateway. Verifique sua conexão com a internet.");
        } catch (\TypeError $typeError) {
            // Captura erros de tipo (Array to string conversion)
            $errorMsg = 'Erro ao processar dados: tipo de dado inválido';
            Log::error('VenitGateway: TypeError no makeRequest', [
                'error' => $typeError->getMessage(),
                'file' => $typeError->getFile(),
                'line' => $typeError->getLine(),
                'method' => $method,
                'endpoint' => $endpoint,
            ]);
            throw new \Exception($errorMsg);
        } catch (\Exception $e) {
            // Re-lança exceções já tratadas, mas garante que a mensagem é string
            $exceptionMsg = $e->getMessage();
            if (!is_string($exceptionMsg)) {
                if (is_array($exceptionMsg)) {
                    $exceptionMsg = json_encode($exceptionMsg, JSON_UNESCAPED_UNICODE);
                } elseif (is_scalar($exceptionMsg)) {
                    $exceptionMsg = (string) $exceptionMsg;
                } else {
                    $exceptionMsg = 'Erro desconhecido ao processar requisição';
                }
                throw new \Exception($exceptionMsg);
            }
            throw $e;
        }
    }

    /**
     * Cria um pagamento PIX (recebimento - QR Code)
     * Documentação: https://venit.readme.io/reference/post_transactions-pix
     * 
     * @param float $amount Valor em reais
     * @param array $payerData Dados do pagador
     * @return array
     */
    public function createPix(float $amount, array $payerData): array
    {
        // Wrapper global para capturar TypeError (Array to string conversion)
        try {
            return $this->executeCreatePix($amount, $payerData);
        } catch (\TypeError $typeError) {
            Log::error('VenitGateway: TypeError (Array to string conversion) capturado', [
                'error' => $typeError->getMessage(),
                'file' => $typeError->getFile(),
                'line' => $typeError->getLine(),
                'trace' => $typeError->getTraceAsString(),
            ]);
            throw new \Exception('Erro ao processar dados da API Venit. Verifique se todos os campos estão preenchidos corretamente.');
        }
    }
    
    /**
     * Método interno para criar PIX (separado para melhor tratamento de erros)
     */
    private function executeCreatePix(float $amount, array $payerData): array
    {
        try {
            // Valida credenciais
            if (empty($this->secretKey) || empty($this->companyId)) {
                throw new \Exception('Venit: Secret Key e Company ID são obrigatórios. Verifique as credenciais no painel administrativo.');
            }

            // 1. Prepara dados do cliente - garante que todos são strings
            $customerName = '';
            $nameInput = $payerData['name'] ?? '';
            if (is_string($nameInput)) {
                $customerName = trim($nameInput);
            } elseif (is_scalar($nameInput)) {
                $customerName = trim((string) $nameInput);
            }
            
            if (empty($customerName)) {
                $customerName = 'Cliente';
            }
            
            $customerEmail = '';
            $emailInput = $payerData['email'] ?? '';
            if (is_string($emailInput)) {
                $customerEmail = trim($emailInput);
            } elseif (is_scalar($emailInput)) {
                $customerEmail = trim((string) $emailInput);
            }
            
            if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                $customerEmail = 'cliente@email.com';
            }
            
            // CPF/CNPJ: Remove tudo que não é número - garante que é string primeiro
            $docInput = $payerData['cpf'] ?? $payerData['document'] ?? '';
            $docString = '';
            if (is_string($docInput)) {
                $docString = $docInput;
            } elseif (is_scalar($docInput)) {
                $docString = (string) $docInput;
            }
            
            $customerDoc = preg_replace('/\D/', '', $docString);
            // Fallback para documento se vazio ou inválido (evita erro 422 em alguns gateways)
            if (empty($customerDoc) || !in_array(strlen($customerDoc), [11, 14])) {
                $customerDoc = '19100000000'; // CPF de teste VÁLIDO
            }

            // Telefone: Remove tudo que não é número
            $rawPhone = preg_replace('/\D/', '', $payerData['phone'] ?? '');
            
            // Se não tiver telefone, usa um padrão
            if (empty($rawPhone)) {
                $rawPhone = '5511999999999'; 
            }
            
            // Garante formato correto (sem + no início)
            $rawPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            if (!str_starts_with($rawPhone, '55') && strlen($rawPhone) >= 10) {
                $rawPhone = '55' . $rawPhone;
            }
            $customerPhone = $rawPhone;

            // 2. Converte valor para centavos
            $amountInCents = (int) round($amount * 100);
            
            // Valida valor mínimo (R$ 0,01 = 1 centavo)
            if ($amountInCents < 1) {
                throw new \Exception('Venit: Valor mínimo é R$ 0,01');
            }

            // 3. Prepara o payload conforme documentação - garante que todos os valores são escalares
            $externalId = $payerData['external_id'] ?? \Illuminate\Support\Str::uuid()->toString();
            
            // Garante que externalId é string
            if (!is_string($externalId)) {
                $externalId = is_scalar($externalId) ? (string) $externalId : \Illuminate\Support\Str::uuid()->toString();
            }
            
            $postbackUrl = $payerData['postback_url'] ?? \App\Helpers\WebhookUrlHelper::generateUrl('api.webhooks.venit');
            // Garante que postbackUrl é string
            if (!is_string($postbackUrl)) {
                $postbackUrl = is_scalar($postbackUrl) ? (string) $postbackUrl : \App\Helpers\WebhookUrlHelper::generateUrl('api.webhooks.venit');
            }
            
            $descriptionInput = $payerData['description'] ?? 'Pagamento via PIX';
            $description = is_string($descriptionInput) ? trim($descriptionInput) : (is_scalar($descriptionInput) ? trim((string) $descriptionInput) : 'Pagamento via PIX');
            
            // Garante que amountInCents é integer
            $amountInCents = is_int($amountInCents) ? $amountInCents : (int) round($amountInCents);

            // Determina o tipo de documento (CPF ou CNPJ)
            $documentType = strlen($customerDoc) === 11 ? 'CPF' : 'CNPJ';
            
            // Garante que todos os valores do payload são escalares válidos
            // IMPORTANTE: customer.document DEVE ser um objeto conforme documentação Venit
            // Formato: { "type": "CPF" ou "CNPJ", "number": "12345678901" }
            $payload = [
                'paymentMethod' => 'PIX',
                'amount' => $amountInCents,
                'customer' => [
                    'name' => (string) $customerName,
                    'email' => (string) $customerEmail,
                    'phone' => (string) $customerPhone,
                    'document' => [
                        'type' => $documentType,
                        'number' => (string) $customerDoc,
                    ],
                ],
                'items' => [
                    [
                        'title' => (string) $description,
                        'unitPrice' => (int) $amountInCents,
                        'quantity' => 1,
                    ],
                ],
                'description' => (string) $description,
                'externalRef' => (string) $externalId,
                'postbackUrl' => (string) $postbackUrl,
            ];
            
            // Log do payload antes de enviar (sem dados sensíveis)
            Log::info('VenitGateway: Payload preparado', [
                'amount_in_cents' => $amountInCents,
                'has_customer_name' => !empty($customerName),
                'has_customer_email' => !empty($customerEmail),
                'has_customer_doc' => !empty($customerDoc),
                'has_phone' => !empty($customerPhone),
                'external_ref' => $externalId,
            ]);

            // Adiciona shipping se fornecido
            if (isset($payerData['shipping']) && is_array($payerData['shipping'])) {
                $payload['shipping'] = $payerData['shipping'];
            }

            // Adiciona metadata se fornecido
            if (isset($payerData['metadata']) && is_array($payerData['metadata'])) {
                $payload['metadata'] = $payerData['metadata'];
            }

            Log::info('VenitGateway: Criando PIX', [
                'amount_in_cents' => $amountInCents,
                'external_id' => $externalId,
                'has_customer_doc' => !empty($customerDoc),
            ]);

            // 4. Envia requisição - com tratamento de erro específico
            try {
                $response = $this->makeRequest('POST', '/transactions/pix', $payload);
            } catch (\TypeError $typeError) {
                // Captura erros de tipo (Array to string conversion é TypeError)
                $errorMsg = 'Erro ao processar dados da API. Verifique os logs para mais detalhes.';
                Log::error('VenitGateway: TypeError ao fazer requisição', [
                    'error' => $typeError->getMessage(),
                    'file' => $typeError->getFile(),
                    'line' => $typeError->getLine(),
                    'trace' => $typeError->getTraceAsString(),
                ]);
                throw new \Exception($errorMsg);
            } catch (\Exception $requestException) {
                // Outros erros
                $errorMsg = $requestException->getMessage();
                if (!is_string($errorMsg)) {
                    $errorMsg = 'Erro desconhecido ao comunicar com a API Venit';
                }
                throw new \Exception($errorMsg);
            }
            
            // 5. Extrai dados da resposta
            // A resposta pode vir em 'data' ou diretamente na raiz
            $data = $response['data'] ?? $response;
            
            // Função helper para garantir que o valor é string
            $ensureString = function($value) {
                if (is_string($value)) {
                    return $value;
                } elseif (is_array($value)) {
                    // Se for array, tenta pegar o primeiro valor string ou converte para JSON
                    if (isset($value['qrcode']) && is_string($value['qrcode'])) {
                        return $value['qrcode'];
                    } elseif (isset($value[0]) && is_string($value[0])) {
                        return $value[0];
                    }
                    return json_encode($value, JSON_UNESCAPED_UNICODE);
                } elseif (is_object($value)) {
                    return json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                return (string) $value;
            };
            
            // Extrai QR Code (pode estar em diferentes lugares)
            $qrCode = '';
            $possibleQrCodePaths = [
                ['pix', 'qrcode'],
                ['pix', 'qrCode'],
                ['pix', 'qr_code'],
                ['qrcode'],
                ['qrCode'],
                ['qr_code'],
                ['qrcodeString'],
                ['qr_code_string'],
                ['copyPaste'],
                ['copy_paste'],
                ['emv'],
            ];
            
            foreach ($possibleQrCodePaths as $path) {
                $value = $data;
                $found = true;
                
                foreach ($path as $key) {
                    if (is_array($value) && isset($value[$key])) {
                        $value = $value[$key];
                    } else {
                        $found = false;
                        break;
                    }
                }
                
                if ($found && !empty($value)) {
                    $qrCode = $ensureString($value);
                    if (!empty($qrCode) && is_string($qrCode)) {
                        break;
                    }
                }
            }
            
            // Extrai transaction ID (garante que é string ou null)
            $txId = null;
            if (isset($data['id'])) {
                $txId = is_string($data['id']) ? $data['id'] : (is_scalar($data['id']) ? (string) $data['id'] : null);
            } elseif (isset($response['id'])) {
                $txId = is_string($response['id']) ? $response['id'] : (is_scalar($response['id']) ? (string) $response['id'] : null);
            }
            
            // Extrai expiration date (garante que é string ou null)
            $expirationDate = null;
            if (isset($data['pix']['expirationDate']) && is_string($data['pix']['expirationDate'])) {
                $expirationDate = $data['pix']['expirationDate'];
            } elseif (isset($data['expirationDate']) && is_string($data['expirationDate'])) {
                $expirationDate = $data['expirationDate'];
            }

            if (empty($qrCode) || !is_string($qrCode)) {
                Log::warning('Venit: QR Code não encontrado na resposta', [
                    'response_keys' => is_array($data) ? array_keys($data) : null,
                    'response_structure' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                    'qrCode_type' => gettype($qrCode),
                    'qrCode_value' => $qrCode,
                ]);
                throw new \Exception('Venit: QR Code não foi retornado pela API. Verifique os logs para mais detalhes.');
            }
            
            // Garante que qrCode é uma string válida (não vazia após trim)
            $qrCode = trim($qrCode);
            if (empty($qrCode)) {
                throw new \Exception('Venit: QR Code retornado está vazio. Verifique os logs para mais detalhes.');
            }

            // Converte expirationDate para Carbon
            $expiresAt = now()->addMinutes(30); // Default 30 minutos
            if ($expirationDate && is_string($expirationDate)) {
                try {
                    $parsedDate = \Carbon\Carbon::parse($expirationDate);
                    if ($parsedDate->isFuture()) {
                        $expiresAt = $parsedDate;
                    }
                } catch (\Exception $e) {
                    Log::warning('Venit: Erro ao parsear expirationDate', [
                        'expirationDate' => $expirationDate,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('VenitGateway: PIX criado com sucesso', [
                'transaction_id' => $txId,
                'has_qrcode' => !empty($qrCode),
            ]);

            return [
                'status' => 'pending',
                'qr_code' => $qrCode,
                'external_id' => $externalId,
                'transaction_id' => $txId,
                'expires_at' => $expiresAt->toIso8601String(),
                'raw_response' => $response,
            ];
            
        } catch (\TypeError $typeError) {
            // Captura especificamente TypeError (Array to string conversion)
            $errorMsg = 'Erro ao processar dados. Verifique se todos os campos estão preenchidos corretamente.';
            Log::error('VenitGateway: TypeError ao criar PIX', [
                'error' => $typeError->getMessage(),
                'file' => $typeError->getFile(),
                'line' => $typeError->getLine(),
                'amount' => $amount,
                'trace' => $typeError->getTraceAsString(),
            ]);
            throw new \Exception($errorMsg);
        } catch (\Exception $e) {
            // Garante que a mensagem de erro é sempre string
            $errorMsg = $e->getMessage();
            if (!is_string($errorMsg)) {
                if (is_array($errorMsg)) {
                    $errorMsg = json_encode($errorMsg, JSON_UNESCAPED_UNICODE);
                } elseif (is_scalar($errorMsg)) {
                    $errorMsg = (string) $errorMsg;
                } else {
                    $errorMsg = 'Erro desconhecido ao criar PIX';
                }
            }
            
            Log::error('VenitGateway: Erro ao criar PIX', [
                'error' => $errorMsg,
                'error_type' => gettype($e->getMessage()),
                'amount' => $amount,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Cria um saque PIX (transferência - PIX OUT)
     * Documentação: https://venit.readme.io/reference/createwithdrawal
     * 
     * @param float $amount Valor em reais
     * @param array $recipientData Dados do destinatário (chave PIX, etc)
     * @param string|null $externalId ID externo único
     * @param string|null $description Descrição do saque
     * @return array
     */
    public function createPixPayment(float $amount, array $recipientData, ?string $externalId = null, ?string $description = null): array
    {
        try {
            // Converte valor para centavos
            $amountInCents = (int) round($amount * 100);
            
            // Valida valor mínimo
            if ($amountInCents < 1) {
                throw new \Exception('Venit: Valor mínimo para saque é R$ 0,01');
            }

            // Determina tipo de chave PIX
            $pixKey = $recipientData['pix_key'] ?? '';
            $pixKeyType = $this->determinePixKeyType($pixKey);

            // Gera ID único para idempotência
            $idempotencyKey = $externalId ?? \Illuminate\Support\Str::uuid()->toString();
            $externalRef = $externalId ?? \Illuminate\Support\Str::uuid()->toString();
            
            // Postback URL
            $postbackUrl = $recipientData['postback_url'] ?? \App\Helpers\WebhookUrlHelper::generateUrl('api.webhooks.venit');

            $payload = [
                'pixkeytype' => $pixKeyType,
                'pixkey' => $pixKey,
                'requestedamount' => $amountInCents,
                'description' => $description ?? 'Saque via PIX',
                'isPix' => true,
                'postbackUrl' => $postbackUrl,
                'externalRef' => $externalRef,
            ];

            // Headers com Idempotency-Key (obrigatório conforme documentação)
            $headers = [
                'Idempotency-Key' => $idempotencyKey,
            ];

            Log::info('VenitGateway: Criando saque PIX', [
                'amount_in_cents' => $amountInCents,
                'pix_key_type' => $pixKeyType,
                'external_ref' => $externalRef,
            ]);

            $response = $this->makeRequest('POST', '/withdrawals/cashout', $payload, $headers);
            
            // A resposta pode vir em 'withdrawal' ou diretamente
            $withdrawal = $response['withdrawal'] ?? $response;

            return [
                'status' => $this->mapWithdrawalStatus($withdrawal['status'] ?? 'pending'),
                'external_id' => $externalRef,
                'transaction_id' => $withdrawal['id'] ?? null,
                'withdrawal_id' => $withdrawal['id'] ?? null,
                'raw_response' => $response,
            ];
            
        } catch (\Exception $e) {
            Log::error('VenitGateway: Erro ao criar saque PIX', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Consulta uma transação PIX específica
     * Documentação: https://venit.readme.io/reference/get_transactions-pix-id
     * 
     * @param string $transactionId ID da transação
     * @return array
     */
    public function consultTransaction(string $transactionId): array
    {
        try {
            $response = $this->makeRequest('GET', "/transactions/pix/{$transactionId}");
            
            // Retorna os dados da transação
            return $response['data'] ?? $response;
            
        } catch (\Exception $e) {
            Log::error('VenitGateway: Erro ao consultar transação', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);
            throw $e;
        }
    }

    /**
     * Determina o tipo de chave PIX baseado no formato
     * 
     * @param string $pixKey Chave PIX
     * @return string Tipo (CPF, CNPJ, EMAIL, PHONE, EVP)
     */
    private function determinePixKeyType(string $pixKey): string
    {
        $pixKey = trim($pixKey);

        // CPF (11 dígitos numéricos)
        if (preg_match('/^[0-9]{11}$/', $pixKey)) {
            return 'CPF';
        }

        // CNPJ (14 dígitos numéricos)
        if (preg_match('/^[0-9]{14}$/', $pixKey)) {
            return 'CNPJ';
        }

        // EMAIL
        if (filter_var($pixKey, FILTER_VALIDATE_EMAIL)) {
            return 'EMAIL';
        }

        // PHONE (formato +5511999999999 ou 5511999999999)
        $cleanPhone = preg_replace('/[^0-9]/', '', $pixKey);
        if (preg_match('/^55\d{10,11}$/', $cleanPhone)) {
            return 'PHONE';
        }

        // EVP (UUID ou chave aleatória)
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $pixKey)) {
            return 'EVP';
        }

        // Default: EVP (chave aleatória)
        return 'EVP';
    }

    /**
     * Mapeia status de saque da Venit para status do sistema
     * 
     * @param string $venitStatus Status retornado pela Venit
     * @return string Status do sistema
     */
    private function mapWithdrawalStatus(string $venitStatus): string
    {
        return match (strtolower($venitStatus)) {
            'pending' => 'pending',
            'approved' => 'processing',
            'processing' => 'processing',
            'done', 'done_manual' => 'paid',
            'failed', 'refused', 'cancelled' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Cria um pagamento com cartão de crédito
     * (Venit não suporta cartão de crédito, apenas PIX)
     * 
     * @param float $amount
     * @param array $cardData
     * @param array $payerData
     * @return array
     */
    public function createCreditCard(float $amount, array $cardData, array $payerData): array
    {
        throw new \Exception('Venit não suporta pagamentos com cartão de crédito. Use PIX.');
    }

    /**
     * Cria um pagamento via Boleto
     * (Venit não suporta boleto, apenas PIX)
     * 
     * @param float $amount
     * @param array $payerData
     * @return array
     */
    public function createBoleto(float $amount, array $payerData): array
    {
        throw new \Exception('Venit não suporta pagamentos via boleto. Use PIX.');
    }
}