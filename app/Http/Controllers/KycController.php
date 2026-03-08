<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class KycController extends Controller
{
    /**
     * Exibe a página de KYC com wizard progressivo
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        
        // Administradores e gerentes não precisam de KYC
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->is_manager) {
            return redirect()->route('dashboard.index');
        }
        
        // Se já está aprovado, redireciona para o dashboard
        if ($user->is_approved && $user->kyc_status === 'approved') {
            return redirect()->route('dashboard.index')
                ->with('success', 'Cadastro aprovado! Bem-vindo ao sistema.');
        }
        
        // Se foi reprovado, mostra mensagem de erro
        if ($user->kyc_status === 'rejected') {
            return view('kyc.index', compact('user'))->with('error', 'Seu cadastro foi reprovado. Entre em contato com o suporte para mais informações.');
        }
        
        // Verifica se biometria facial está ativada
        $facialBiometricsEnabled = \App\Models\Setting::get('kyc_facial_biometrics_enabled', '1') === '1';
        
        return view('kyc.index', compact('user', 'facialBiometricsEnabled'));
    }

    /**
     * Salva o endereço (Etapa 1)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeAddress(Request $request): JsonResponse
    {
        $request->validate([
            'zip_code' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
        ]);

        $user = Auth::user();

        try {
            $updateData = [
                'zip_code' => $request->zip_code,
                'street' => $request->street,
                'number' => $request->number,
                'neighborhood' => $request->neighborhood,
                'city' => $request->city,
                'state' => strtoupper($request->state),
                // Mantém compatibilidade com campos antigos
                'cep' => $request->zip_code,
                'address' => $request->street,
                'address_number' => $request->number,
                'kyc_step' => 2, // Avança para próxima etapa
            ];
            
            Log::info('Atualizando endereço do usuário', [
                'user_id' => $user->id,
                'data' => $updateData
            ]);
            
            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Endereço salvo com sucesso!',
                'next_step' => 2,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação ao salvar endereço KYC', [
                'errors' => $e->errors(),
                'user_id' => $user->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . implode(', ', $e->errors()['zip_code'] ?? []),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar endereço KYC', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar endereço: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Salva os documentos (Etapa 2)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeDocs(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Verifica se o usuário tem endereço preenchido
        if (empty($user->zip_code) || empty($user->street) || empty($user->city) || empty($user->state)) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor, preencha seu endereço completo antes de enviar os documentos.',
            ], 400);
        }

        // Detecta automaticamente o tipo de pessoa pelo CPF/CNPJ
        $isPessoaJuridica = \App\Helpers\DocumentHelper::isPessoaJuridica($user->cpf_cnpj ?? '');
        $personType = $isPessoaJuridica ? 'PJ' : 'PF';
        
        // Verifica se biometria facial está ativada
        $facialBiometricsEnabled = \App\Models\Setting::get('kyc_facial_biometrics_enabled', '1') === '1';
        
        $validationRules = [
            'document_front' => 'required|file|image|mimes:jpeg,png,jpg|max:5120',
            'document_back' => 'required|file|image|mimes:jpeg,png,jpg|max:5120',
            'selfie_with_doc' => 'required|file|image|mimes:jpeg,png,jpg|max:5120', // Sempre obrigatória
        ];

        // Se for CNPJ, adiciona validação para comprovante
        if ($personType === 'PJ') {
            $validationRules['cnpj_proof'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:5120';
        }

        try {
            $validated = $request->validate($validationRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação ao salvar documentos KYC', [
                'errors' => $e->errors(),
                'user_id' => $user->id,
                'person_type' => $personType,
                'has_files' => [
                    'document_front' => $request->hasFile('document_front'),
                    'document_back' => $request->hasFile('document_back'),
                    'selfie_with_doc' => $request->hasFile('selfie_with_doc'),
                    'cnpj_proof' => $request->hasFile('cnpj_proof'),
                ]
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro na validação dos arquivos. Verifique se todos os arquivos foram selecionados e são válidos.',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            Log::info('KYC: Iniciando upload de documentos', [
                'user_id' => $user->id,
                'person_type' => $personType,
                'has_files' => [
                    'document_front' => $request->hasFile('document_front'),
                    'document_back' => $request->hasFile('document_back'),
                    'selfie_with_doc' => $request->hasFile('selfie_with_doc'),
                    'cnpj_proof' => $request->hasFile('cnpj_proof'),
                ]
            ]);

            // Verifica se os arquivos foram enviados
            if (!$request->hasFile('document_front')) {
                Log::error('KYC: document_front não foi enviado');
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo da frente do documento não foi enviado.',
                ], 400);
            }
            
            if (!$request->hasFile('document_back')) {
                Log::error('KYC: document_back não foi enviado');
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo do verso do documento não foi enviado.',
                ], 400);
            }
            
            // Verifica se selfie segurando documento foi enviada (sempre obrigatória)
            if (!$request->hasFile('selfie_with_doc')) {
                Log::error('KYC: selfie_with_doc não foi enviado');
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo da selfie segurando o documento não foi enviado.',
                ], 400);
            }
            
            if ($personType === 'PJ' && !$request->hasFile('cnpj_proof')) {
                Log::error('KYC: cnpj_proof não foi enviado para PJ');
                return response()->json([
                    'success' => false,
                    'message' => 'Comprovante do CNPJ não foi enviado.',
                ], 400);
            }

            // Cria o diretório na pasta IMG (public/IMG/kyc/user_id)
            $kycBasePath = public_path('IMG/kyc/' . $user->id);
            if (!is_dir($kycBasePath)) {
                if (!mkdir($kycBasePath, 0755, true)) {
                    Log::error('KYC: Erro ao criar diretório', ['path' => $kycBasePath]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao criar diretório para salvar os documentos.',
                    ], 500);
                }
                Log::info('KYC: Diretório criado', ['path' => $kycBasePath]);
            }

            // Salva a frente do documento
            $documentFrontFile = $request->file('document_front');
            if (!$documentFrontFile || !$documentFrontFile->isValid()) {
                $error = $documentFrontFile ? $documentFrontFile->getError() : 'Arquivo não recebido';
                Log::error('KYC: document_front inválido', ['error' => $error]);
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo da frente do documento é inválido. Erro: ' . $error,
                ], 400);
            }

            // Verifica tamanho do arquivo (5MB = 5 * 1024 * 1024 bytes)
            if ($documentFrontFile->getSize() > 5242880) {
                Log::error('KYC: document_front muito grande', ['size' => $documentFrontFile->getSize()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo da frente do documento excede 5MB.',
                ], 400);
            }

            $documentFrontExtension = strtolower($documentFrontFile->getClientOriginalExtension() ?: $documentFrontFile->guessExtension() ?: 'jpg');
            $documentFrontFileName = 'document_front_' . time() . '_' . uniqid() . '.' . $documentFrontExtension;
            $documentFrontTargetPath = $kycBasePath . DIRECTORY_SEPARATOR . $documentFrontFileName;
            
            if (!move_uploaded_file($documentFrontFile->getPathname(), $documentFrontTargetPath)) {
                if (!copy($documentFrontFile->getPathname(), $documentFrontTargetPath)) {
                    Log::error('KYC: Erro ao salvar document_front', ['target' => $documentFrontTargetPath]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao salvar arquivo da frente do documento.',
                    ], 500);
                }
            }

            $documentFrontPath = 'IMG/kyc/' . $user->id . '/' . $documentFrontFileName;
            Log::info('KYC: document_front salvo', ['path' => $documentFrontPath]);
            
            // Salva o verso do documento
            $documentBackFile = $request->file('document_back');
            if (!$documentBackFile || !$documentBackFile->isValid()) {
                $error = $documentBackFile ? $documentBackFile->getError() : 'Arquivo não recebido';
                Log::error('KYC: document_back inválido', ['error' => $error]);
                // Remove arquivo já salvo
                @unlink($documentFrontTargetPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo do verso do documento é inválido. Erro: ' . $error,
                ], 400);
            }

            if ($documentBackFile->getSize() > 5242880) {
                @unlink($documentFrontTargetPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo do verso do documento excede 5MB.',
                ], 400);
            }

            $documentBackExtension = strtolower($documentBackFile->getClientOriginalExtension() ?: $documentBackFile->guessExtension() ?: 'jpg');
            $documentBackFileName = 'document_back_' . time() . '_' . uniqid() . '.' . $documentBackExtension;
            $documentBackTargetPath = $kycBasePath . DIRECTORY_SEPARATOR . $documentBackFileName;
            
            if (!move_uploaded_file($documentBackFile->getPathname(), $documentBackTargetPath)) {
                if (!copy($documentBackFile->getPathname(), $documentBackTargetPath)) {
                    @unlink($documentFrontTargetPath);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao salvar arquivo do verso do documento.',
                    ], 500);
                }
            }

            $documentBackPath = 'IMG/kyc/' . $user->id . '/' . $documentBackFileName;
            Log::info('KYC: document_back salvo', ['path' => $documentBackPath]);
            
            // Salva a selfie segurando documento (sempre obrigatória)
            $selfieFile = $request->file('selfie_with_doc');
            if (!$selfieFile || !$selfieFile->isValid()) {
                $error = $selfieFile ? $selfieFile->getError() : 'Arquivo não recebido';
                Log::error('KYC: selfie_with_doc inválido', ['error' => $error]);
                @unlink($documentFrontTargetPath);
                @unlink($documentBackTargetPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo da selfie segurando o documento é inválido. Erro: ' . $error,
                ], 400);
            }

            if ($selfieFile->getSize() > 5242880) {
                @unlink($documentFrontTargetPath);
                @unlink($documentBackTargetPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo da selfie segurando o documento excede 5MB.',
                ], 400);
            }

            $selfieExtension = strtolower($selfieFile->getClientOriginalExtension() ?: $selfieFile->guessExtension() ?: 'jpg');
            $selfieFileName = 'selfie_with_doc_' . time() . '_' . uniqid() . '.' . $selfieExtension;
            $selfieTargetPath = $kycBasePath . DIRECTORY_SEPARATOR . $selfieFileName;
            
            if (!move_uploaded_file($selfieFile->getPathname(), $selfieTargetPath)) {
                if (!copy($selfieFile->getPathname(), $selfieTargetPath)) {
                    @unlink($documentFrontTargetPath);
                    @unlink($documentBackTargetPath);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao salvar arquivo da selfie segurando o documento.',
                    ], 500);
                }
            }

            $selfiePath = 'IMG/kyc/' . $user->id . '/' . $selfieFileName;
            Log::info('KYC: selfie_with_doc salvo', ['path' => $selfiePath]);
            
            // Salva o comprovante do CNPJ se for CNPJ
            $cnpjProofPath = null;
            if ($personType === 'PJ' && $request->hasFile('cnpj_proof')) {
                $cnpjProofFile = $request->file('cnpj_proof');
                if ($cnpjProofFile && $cnpjProofFile->isValid()) {
                    if ($cnpjProofFile->getSize() > 5242880) {
                        @unlink($documentFrontTargetPath);
                        @unlink($documentBackTargetPath);
                        @unlink($selfieTargetPath);
                        return response()->json([
                            'success' => false,
                            'message' => 'Comprovante do CNPJ excede 5MB.',
                        ], 400);
                    }

                    $cnpjExtension = strtolower($cnpjProofFile->getClientOriginalExtension() ?: $cnpjProofFile->guessExtension() ?: 'pdf');
                    $cnpjFileName = 'cnpj_proof_' . time() . '_' . uniqid() . '.' . $cnpjExtension;
                    $cnpjTargetPath = $kycBasePath . DIRECTORY_SEPARATOR . $cnpjFileName;
                    
                    if (!move_uploaded_file($cnpjProofFile->getPathname(), $cnpjTargetPath)) {
                        if (!copy($cnpjProofFile->getPathname(), $cnpjTargetPath)) {
                            @unlink($documentFrontTargetPath);
                            @unlink($documentBackTargetPath);
                            @unlink($selfieTargetPath);
                            return response()->json([
                                'success' => false,
                                'message' => 'Erro ao salvar comprovante do CNPJ.',
                            ], 500);
                        }
                    }

                    $cnpjProofPath = 'IMG/kyc/' . $user->id . '/' . $cnpjFileName;
                    Log::info('KYC: cnpj_proof salvo', ['path' => $cnpjProofPath]);
                }
            }

            // Remove arquivos antigos se existirem
            if ($user->doc_front) {
                $oldPath = public_path($user->doc_front);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            if ($user->doc_back) {
                $oldPath = public_path($user->doc_back);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            if ($user->selfie_with_doc) {
                $oldPath = public_path($user->selfie_with_doc);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Atualiza o usuário
            $updateData = [
                'person_type' => $personType,
                'doc_front' => $documentFrontPath,
                'doc_back' => $documentBackPath,
                'selfie_with_doc' => $selfiePath, // Sempre salva a selfie segurando documento
            ];

            // Verifica se biometria facial está ativada para determinar próximo passo
            $facialBiometricsEnabled = \App\Models\Setting::get('kyc_facial_biometrics_enabled', '1') === '1';
            
            // Se biometria estiver desativada, vai direto para status (step 4)
            // Se estiver ativada, vai para biometria (step 3)
            if ($facialBiometricsEnabled) {
                $updateData['kyc_step'] = 3; // Avança para biometria facial
            } else {
                // Sem biometria, finaliza o KYC e marca como pending
                $updateData['kyc_status'] = 'pending';
                $updateData['documents_sent'] = true;
                $updateData['kyc_step'] = 4; // Finalizado
            }

            if ($cnpjProofPath) {
                if ($user->cnpj_card) {
                    Storage::disk('public')->delete($user->cnpj_card);
                }
                $updateData['cnpj_card'] = $cnpjProofPath;
            }

            $user->update($updateData);

            Log::info('KYC: Documentos salvos com sucesso', [
                'user_id' => $user->id,
                'paths' => $updateData,
                'facial_biometrics_enabled' => $facialBiometricsEnabled
            ]);

            return response()->json([
                'success' => true,
                'message' => $facialBiometricsEnabled 
                    ? 'Documentos enviados com sucesso!' 
                    : 'Documentos enviados com sucesso! Seus dados foram enviados para análise.',
                'next_step' => $facialBiometricsEnabled ? 3 : 4,
            ]);
        } catch (\Exception $e) {
            Log::error('KYC: Erro ao salvar documentos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $user->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar documentos. Tente novamente ou entre em contato com o suporte se o problema persistir.',
                'error_details' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Salva a biometria facial (Etapa 3)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeBiometrics(Request $request): JsonResponse
    {
        $request->validate([
            'biometric_image' => 'required|string', // Base64
        ]);

        $user = Auth::user();
        
        // Verifica se os documentos foram enviados (incluindo selfie segurando documento)
        if (empty($user->doc_front) || empty($user->doc_back) || empty($user->selfie_with_doc)) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor, envie os documentos e a selfie segurando o documento antes de capturar a biometria.',
            ], 400);
        }

        try {
            // Decodifica a imagem Base64
            $imageData = $request->input('biometric_image');
            
            // Remove o prefixo data:image/jpeg;base64, se existir
            if (strpos($imageData, ',') !== false) {
                $imageData = explode(',', $imageData)[1];
            }
            
            $decodedImage = base64_decode($imageData);
            
            if ($decodedImage === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao processar a imagem da biometria.',
                ], 400);
            }

            // Cria o diretório na pasta IMG (public/IMG/kyc/user_id)
            $kycBasePath = public_path('IMG/kyc/' . $user->id);
            if (!is_dir($kycBasePath)) {
                if (!mkdir($kycBasePath, 0755, true)) {
                    Log::error('KYC: Erro ao criar diretório para biometria', ['path' => $kycBasePath]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao criar diretório para salvar a biometria.',
                    ], 500);
                }
            }

            // Salva a imagem da biometria
            $biometricFileName = 'facial_biometrics_' . time() . '_' . uniqid() . '.jpg';
            $biometricTargetPath = $kycBasePath . DIRECTORY_SEPARATOR . $biometricFileName;
            
            if (file_put_contents($biometricTargetPath, $decodedImage) === false) {
                Log::error('KYC: Erro ao salvar biometria', ['target' => $biometricTargetPath]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao salvar imagem da biometria.',
                ], 500);
            }

            // Remove arquivo antigo se existir
            if ($user->facial_biometrics) {
                $oldPath = public_path($user->facial_biometrics);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $biometricPath = 'IMG/kyc/' . $user->id . '/' . $biometricFileName;

            // Atualiza status do KYC para pending
            $user->update([
                'facial_biometrics' => $biometricPath,
                'kyc_status' => 'pending',
                'documents_sent' => true,
                'kyc_step' => 4, // Finalizado
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biometria capturada com sucesso! Seus dados foram enviados para análise.',
                'next_step' => 4,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar biometria KYC', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar biometria: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Método de compatibilidade para atualização de endereço (rota antiga)
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'cep' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'address_number' => 'required|string|max:20',
            'address_complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
        ]);

        $user = Auth::user();

        try {
            $user->update([
                'cep' => $request->cep,
                'address' => $request->address,
                'address_number' => $request->address_number,
                'address_complement' => $request->address_complement,
                'neighborhood' => $request->neighborhood,
                'city' => $request->city,
                'state' => strtoupper($request->state),
                'zip_code' => $request->cep,
                'street' => $request->address,
                'number' => $request->address_number,
            ]);

            return redirect()->route('kyc.index')
                ->with('success', 'Endereço salvo com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('kyc.index')
                ->with('error', 'Erro ao salvar endereço: ' . $e->getMessage());
        }
    }

    /**
     * Busca endereço via CEP (ViaCEP)
     *
     * @param string $cep
     * @return JsonResponse
     */
    public function searchCep(string $cep): JsonResponse
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        if (strlen($cep) !== 8) {
            return response()->json([
                'success' => false,
                'message' => 'CEP inválido. Digite um CEP com 8 dígitos.',
            ], 400);
        }

        try {
            $url = "https://viacep.com.br/ws/{$cep}/json/";
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'method' => 'GET',
                ]
            ]);
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                throw new \Exception('Não foi possível conectar ao serviço ViaCEP.');
            }
            
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Erro ao processar resposta do ViaCEP.');
            }

            if (isset($data['erro']) || empty($data['logradouro'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP não encontrado.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'street' => $data['logradouro'] ?? '',
                    'neighborhood' => $data['bairro'] ?? '',
                    'city' => $data['localidade'] ?? '',
                    'state' => $data['uf'] ?? '',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar CEP', [
                'cep' => $cep,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar CEP: ' . $e->getMessage(),
            ], 500);
        }
    }
}
