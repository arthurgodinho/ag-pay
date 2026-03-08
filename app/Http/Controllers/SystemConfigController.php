<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class SystemConfigController extends Controller
{
    /**
     * Exibe formulário de configurações
     *
     * @return View
     */
    public function index(): View
    {
        // Personalização (Branding)
        $theme_primary_color = Setting::get('theme_primary_color', '#00F3FF');
        $theme_secondary_color = Setting::get('theme_secondary_color', '#BF00FF');
        $theme_accent_color = Setting::get('theme_accent_color', '#0070FF');
        $theme_background_color = Setting::get('theme_background_color', '#0F111A');
        $theme_sidebar_bg = Setting::get('theme_sidebar_bg', '#0B0D15');
        $theme_card_bg = Setting::get('theme_card_bg', '#161926');
        $theme_text_color = Setting::get('theme_text_color', '#FFFFFF');
        
        $logo = \App\Models\LandingPageSetting::get('logo', '');
        $favicon = \App\Models\LandingPageSetting::get('favicon', '');

        return view('admin.configs.index', compact(
            'default_manager_name',
            'default_manager_email',
            'default_whatsapp',
            'default_manager_photo',
            'limit_pf_daily',
            'limit_pf_withdrawal',
            'withdrawals_per_day_pf',
            'limit_pj_daily',
            'limit_pj_withdrawal',
            'withdrawals_per_day_pj',
            'cashin_pix_fixo',
            'cashin_pix_percentual',
            'cashin_card_fixo',
            'cashin_card_percentual',
            'deposit_min_value',
            'cashin_pix_minima',
            'cashin_card_minima',
            'cashout_pix_percentual',
            'cashout_pix_minima',
            'cashout_pix_fixo',
            'cashout_api_percentual',
            'cashout_crypto_percentual',
            'withdrawal_min_value',
            'checkout_pix_fixo',
            'checkout_pix_percentual',
            'checkout_card_fixo',
            'checkout_card_percentual',
            'checkout_boleto_fixo',
            'checkout_boleto_percentual',
            'gateway_name',
            'default_language',
            'affiliate_commission_percentage',
            'affiliate_commission_fixed',
            'affiliate_commission_type',
            'kyc_facial_biometrics_enabled',
            'theme_primary_color',
            'theme_secondary_color',
            'theme_accent_color',
            'theme_background_color',
            'theme_sidebar_bg',
            'theme_card_bg',
            'theme_text_color',
            'logo',
            'favicon'
        ));
    }

    /**
     * Salva configurações
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Taxas Cash-In
            'cashin_pix_fixo' => 'required|numeric|min:0',
            'cashin_pix_percentual' => 'required|numeric|min:0|max:100',
            'cashin_pix_minima' => 'required|numeric|min:0',
            'deposit_min_value' => 'required|numeric|min:0',
            // Taxas Cash-Out
            'cashout_pix_percentual' => 'required|numeric|min:0|max:100',
            'cashout_pix_minima' => 'required|numeric|min:0',
            'cashout_pix_fixo' => 'required|numeric|min:0',
            'cashout_api_percentual' => 'required|numeric|min:0|max:100',
            'withdrawal_min_value' => 'required|numeric|min:0',
            // Limites Pessoa Física
            'withdrawals_per_day_pf' => 'required|integer|min:1|max:100',
            'limit_pf_daily' => 'required|numeric|min:0',
            'limit_pf_withdrawal' => 'required|numeric|min:0',
            // Limites Pessoa Jurídica
            'withdrawals_per_day_pj' => 'required|integer|min:1|max:100',
            'limit_pj_daily' => 'required|numeric|min:0',
            'limit_pj_withdrawal' => 'required|numeric|min:0',
            // Contato do Gerente
            'default_manager_name' => 'nullable|string|max:255',
            'default_manager_email' => 'nullable|email|max:255',
            'default_whatsapp' => 'nullable|string|max:20',
            'default_manager_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Taxas de Afiliados
            'affiliate_commission_type' => 'nullable|string|in:percentage,fixed',
            'affiliate_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'affiliate_commission_fixed' => 'nullable|numeric|min:0',
            // Cores e Logos
            'theme_primary_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'theme_secondary_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'theme_accent_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'theme_background_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'theme_sidebar_bg' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'theme_card_bg' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'theme_text_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,ico,png,webp|max:1024',
        ]);

        try {
            // Contato do Gerente
            if ($request->has('default_manager_name')) {
                Setting::set('default_manager_name', $request->default_manager_name);
            }
            if ($request->has('default_manager_email')) {
                Setting::set('default_manager_email', $request->default_manager_email);
            }
            if ($request->has('default_whatsapp')) {
                Setting::set('default_whatsapp', $request->default_whatsapp);
            }
            
            // Upload da foto do gerente
            if ($request->hasFile('default_manager_photo')) {
                // Remove foto antiga se existir
                $oldPhoto = Setting::get('default_manager_photo');
                if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
                
                // Salva nova foto
                $path = $request->file('default_manager_photo')->store('settings', 'public');
                Setting::set('default_manager_photo', $path);
            }

            // Taxas Cash-In PIX
            Setting::set('cashin_pix_fixo', $request->cashin_pix_fixo);
            Setting::set('cashin_pix_percentual', $request->cashin_pix_percentual);
            Setting::set('cashin_pix_minima', $request->cashin_pix_minima);
            Setting::set('deposit_min_value', $request->deposit_min_value);
            
            // Taxas Checkout (específicas para vendas de produtos)
            // REMOVIDO
            
            // Compatibilidade: manter taxas antigas baseadas nas novas
            Setting::set('cashin_fixo', $request->cashin_pix_fixo);
            Setting::set('cashin_percentual', $request->cashin_pix_percentual);

            // Taxas Cash-Out
            Setting::set('cashout_pix_percentual', $request->cashout_pix_percentual);
            Setting::set('cashout_pix_minima', $request->cashout_pix_minima);
            Setting::set('cashout_pix_fixo', $request->cashout_pix_fixo);
            Setting::set('cashout_api_percentual', $request->cashout_api_percentual);
            Setting::set('withdrawal_min_value', $request->withdrawal_min_value);
            
            // Compatibilidade: manter taxas antigas baseadas nas novas
            Setting::set('cashout_fixo', $request->cashout_pix_fixo);
            Setting::set('cashout_percentual', $request->cashout_pix_percentual);

            // Limites Pessoa Física (CPF)
            Setting::set('withdrawals_per_day_pf', $request->withdrawals_per_day_pf);
            Setting::set('limit_pf_daily', $request->limit_pf_daily);
            Setting::set('limit_pf_withdrawal', $request->limit_pf_withdrawal);

            // Limites Pessoa Jurídica (CNPJ)
            Setting::set('withdrawals_per_day_pj', $request->withdrawals_per_day_pj);
            Setting::set('limit_pj_daily', $request->limit_pj_daily);
            Setting::set('limit_pj_withdrawal', $request->limit_pj_withdrawal);

            // Taxas de Afiliados
            if ($request->has('affiliate_commission_type')) {
                Setting::set('affiliate_commission_type', $request->affiliate_commission_type);
            }
            if ($request->has('affiliate_commission_percentage')) {
                Setting::set('affiliate_commission_percentage', $request->affiliate_commission_percentage);
            }
            if ($request->has('affiliate_commission_fixed')) {
                Setting::set('affiliate_commission_fixed', $request->affiliate_commission_fixed);
            }

            // Nome da empresa/gateway
            if ($request->has('gateway_name')) {
                Setting::set('gateway_name', $request->gateway_name);
            }
            
            // Idioma padrão do sistema
            if ($request->has('default_language')) {
                $availableLocales = ['pt', 'es', 'en'];
                if (in_array($request->default_language, $availableLocales)) {
                    Setting::set('default_language', $request->default_language);
                }
            }

            // Configuração de Biometria Facial KYC
            // Se o checkbox não foi enviado, significa que está desativado
            Setting::set('kyc_facial_biometrics_enabled', $request->has('kyc_facial_biometrics_enabled') && $request->boolean('kyc_facial_biometrics_enabled') ? '1' : '0');

            // Personalização (Branding)
            if ($request->has('theme_primary_color')) Setting::set('theme_primary_color', $request->theme_primary_color);
            if ($request->has('theme_secondary_color')) Setting::set('theme_secondary_color', $request->theme_secondary_color);
            if ($request->has('theme_accent_color')) Setting::set('theme_accent_color', $request->theme_accent_color);
            if ($request->has('theme_background_color')) Setting::set('theme_background_color', $request->theme_background_color);
            if ($request->has('theme_sidebar_bg')) Setting::set('theme_sidebar_bg', $request->theme_sidebar_bg);
            if ($request->has('theme_card_bg')) Setting::set('theme_card_bg', $request->theme_card_bg);
            if ($request->has('theme_text_color')) Setting::set('theme_text_color', $request->theme_text_color);

            // Upload de Logo e Favicon
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->move(public_path('IMG'), 'logo_' . time() . '.' . $request->file('logo')->getClientOriginalExtension());
                \App\Models\LandingPageSetting::set('logo', 'IMG/' . basename($logoPath));
            }
            if ($request->hasFile('favicon')) {
                $faviconPath = $request->file('favicon')->move(public_path('IMG'), 'favicon_' . time() . '.' . $request->file('favicon')->getClientOriginalExtension());
                \App\Models\LandingPageSetting::set('favicon', 'IMG/' . basename($faviconPath));
            }

            // ATUALIZA TODOS OS USUÁRIOS COM AS NOVAS TAXAS DO PAINEL
            $this->syncAllUsersFees(
                $request->cashin_pix_percentual,
                $request->cashin_pix_fixo,
                $request->cashout_pix_percentual,
                $request->cashout_pix_fixo
            );

            Log::info('Configurações salvas com sucesso e taxas sincronizadas com todos os usuários', [
                'cashin_pix_fixo' => $request->cashin_pix_fixo,
                'cashin_pix_percentual' => $request->cashin_pix_percentual,
                'cashout_pix_percentual' => $request->cashout_pix_percentual,
            ]);

            return back()->with('success', 'Configurações salvas com sucesso! Todas as taxas dos usuários foram atualizadas automaticamente.');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Erro ao salvar configurações: ' . $e->getMessage());
        }
    }

    /**
     * Sincroniza as taxas de todos os usuários com as taxas do painel
     */
    private function syncAllUsersFees($cashinPixPercentual, $cashinPixFixo, $cashoutPixPercentual, $cashoutPixFixo): void
    {
        try {
            // Atualiza todos os usuários (exceto admins e managers, se necessário)
            $usersUpdated = User::where('is_admin', false)
                ->where('is_manager', false)
                ->update([
                    'taxa_entrada' => $cashinPixPercentual,
                    'taxa_entrada_fixo' => $cashinPixFixo,
                    'taxa_saida' => $cashoutPixPercentual,
                    'taxa_saida_fixo' => $cashoutPixFixo,
                ]);

            Log::info('Taxas sincronizadas com todos os usuários', [
                'users_updated' => $usersUpdated,
                'cashin_pix_percentual' => $cashinPixPercentual,
                'cashin_pix_fixo' => $cashinPixFixo,
                'cashout_pix_percentual' => $cashoutPixPercentual,
                'cashout_pix_fixo' => $cashoutPixFixo,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar taxas dos usuários', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Não lança exceção para não impedir o salvamento das configurações
        }
    }
}
