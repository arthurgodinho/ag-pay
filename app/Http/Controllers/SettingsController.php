<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'personal');
        
        // Busca token de API ativo com IPs permitidos
        $apiToken = ApiToken::with('allowedIps')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->first();
        
        // SEMPRE usa as taxas do painel (não mais taxas personalizadas)
        // CashIn PIX
        $cashinPixFixo = $user->getCashinPixFixo();
        $cashinPixPercentual = $user->getCashinPixPercentual();
        
        // CashIn Cartão
        $cashinCardFixo = $user->getCashinCardFixo();
        $cashinCardPercentual = $user->getCashinCardPercentual();
        
        // CashOut PIX
        $cashoutPixFixo = $user->getCashoutPixFixo();
        $cashoutPixPercentual = $user->getCashoutPixPercentual();
        
        // CashOut Cripto
        $cashoutCryptoPercentual = Setting::get('cashout_crypto_percentual', '3.00');
        
        // Taxa mínima
        $cashoutPixMinima = Setting::get('cashout_pix_minima', '0.80');
        
        // Para compatibilidade (usando PIX como padrão)
        $cashinFixo = $cashinPixFixo;
        $cashinPercentual = $cashinPixPercentual;
        $cashoutFixo = $cashoutPixFixo;
        $cashoutPercentual = $cashoutPixPercentual;
        
        // Identifica se é PF ou PJ
        $documentType = \App\Helpers\DocumentHelper::getDocumentType($user->cpf_cnpj ?? '');
        
        // Limites baseados no tipo de documento (PF ou PJ)
        if ($documentType === 'cnpj') {
            // Pessoa Jurídica
            $dailyLimit = Setting::get('limit_pj_daily', '50000.00');
            $withdrawalLimit = Setting::get('limit_pj_withdrawal', '50000.00');
            $cpfLimit = Setting::get('limit_pj_per_cnpj', '25000.00');
            $withdrawalsPerCpf = Setting::get('limit_pj_withdrawals_per_cnpj', '10');
            $withdrawalsPerDay = Setting::get('withdrawals_per_day_pj', '3');
        } else {
            // Pessoa Física (padrão)
            $dailyLimit = Setting::get('limit_pf_daily', '10000.00');
            $withdrawalLimit = Setting::get('limit_pf_withdrawal', '10000.00');
            $cpfLimit = Setting::get('limit_pf_per_cpf', '5000.00');
            $withdrawalsPerCpf = Setting::get('limit_pf_withdrawals_per_cpf', '5');
            $withdrawalsPerDay = Setting::get('withdrawals_per_day_pf', '3');
        }
        
        return view('dashboard.settings.index', compact(
            'user',
            'tab',
            'apiToken',
            'cashinFixo',
            'cashinPercentual',
            'cashoutFixo',
            'cashoutPercentual',
            'cashinPixFixo',
            'cashinPixPercentual',
            'cashinCardFixo',
            'cashinCardPercentual',
            'cashoutPixFixo',
            'cashoutPixPercentual',
            'cashoutCryptoPercentual',
            'cashoutPixMinima',
            'dailyLimit',
            'withdrawalLimit',
            'cpfLimit',
            'withdrawalsPerCpf',
            'withdrawalsPerDay',
            'documentType'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // 2MB
        ]);

        try {
            // Upload da foto de perfil
            if ($request->hasFile('profile_photo')) {
                $profilePhotoFile = $request->file('profile_photo');
                
                // Valida o arquivo
                if (!$profilePhotoFile->isValid()) {
                    return redirect()->route('dashboard.settings.index', ['tab' => 'personal'])
                        ->with('error', 'Erro: Arquivo de foto inválido.');
                }

                // Valida tamanho (2MB)
                if ($profilePhotoFile->getSize() > 2097152) {
                    return redirect()->route('dashboard.settings.index', ['tab' => 'personal'])
                        ->with('error', 'Erro: A foto excede 2MB. Use uma imagem menor.');
                }

                // Cria diretório se não existir (public/IMG/profile)
                $profileBasePath = public_path('IMG/profile');
                if (!is_dir($profileBasePath)) {
                    if (!mkdir($profileBasePath, 0755, true)) {
                        return redirect()->route('dashboard.settings.index', ['tab' => 'personal'])
                            ->with('error', 'Erro ao criar diretório para salvar a foto.');
                    }
                }

                // Remove foto antiga se existir
                if ($user->profile_photo) {
                    $oldPath = public_path($user->profile_photo);
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                // Salva nova foto
                $extension = strtolower($profilePhotoFile->getClientOriginalExtension() ?: $profilePhotoFile->guessExtension() ?: 'jpg');
                $fileName = 'profile_' . $user->id . '_' . time() . '_' . uniqid() . '.' . $extension;
                $targetPath = $profileBasePath . DIRECTORY_SEPARATOR . $fileName;
                
                if (!move_uploaded_file($profilePhotoFile->getPathname(), $targetPath)) {
                    if (!copy($profilePhotoFile->getPathname(), $targetPath)) {
                        return redirect()->route('dashboard.settings.index', ['tab' => 'personal'])
                            ->with('error', 'Erro ao salvar foto. Verifique as permissões do diretório.');
                    }
                }

                // Verifica se o arquivo foi salvo corretamente
                if (!file_exists($targetPath) || filesize($targetPath) == 0) {
                    return redirect()->route('dashboard.settings.index', ['tab' => 'personal'])
                        ->with('error', 'Erro: A foto não foi salva corretamente.');
                }

                $profilePath = 'IMG/profile/' . $fileName;
                $user->update(['profile_photo' => $profilePath]);
            }

            return redirect()->route('dashboard.settings.index', ['tab' => 'personal'])
                ->with('success', 'Foto de perfil atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.settings.index', ['tab' => 'personal'])
                ->with('error', 'Erro ao atualizar foto de perfil: ' . $e->getMessage());
        }
    }
}
