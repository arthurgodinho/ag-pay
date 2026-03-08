<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminLandingPageController extends Controller
{
    /**
     * Exibe o formulário de edição da landing page
     *
     * @return View
     */
    public function index(): View
    {
        // Busca todas as configurações
        $settings = [
            'logo' => LandingPageSetting::get('logo', ''),
            'favicon' => LandingPageSetting::get('favicon', ''),
            'whitelabel_image' => LandingPageSetting::get('whitelabel_image', ''),
            'meta_title' => LandingPageSetting::get('meta_title', ''),
            'meta_description' => LandingPageSetting::get('meta_description', ''),
            'hero_badge' => LandingPageSetting::get('hero_badge', ''),
            'hero_title' => LandingPageSetting::get('hero_title', ''),
            'hero_subtitle' => LandingPageSetting::get('hero_subtitle', ''),
            'hero_cta_text' => LandingPageSetting::get('hero_cta_text', ''),
            'hero_image' => LandingPageSetting::get('hero_image', ''),
            
            // Features Section (Grid de 3 Cards)
            'features_title' => LandingPageSetting::get('features_title', ''),
            'features_subtitle' => LandingPageSetting::get('features_subtitle', ''),
            'feature1_title' => LandingPageSetting::get('feature1_title', ''),
            'feature1_text' => LandingPageSetting::get('feature1_text', ''),
            'feature2_title' => LandingPageSetting::get('feature2_title', ''),
            'feature2_text' => LandingPageSetting::get('feature2_text', ''),
            'feature3_title' => LandingPageSetting::get('feature3_title', ''),
            'feature3_text' => LandingPageSetting::get('feature3_text', ''),

            // Pricing Section
            'pricing_title' => LandingPageSetting::get('pricing_title', ''),
            'pricing_subtitle' => LandingPageSetting::get('pricing_subtitle', ''),
            'pricing_note' => LandingPageSetting::get('pricing_note', ''),

            // Whitelabel Section
            'whitelabel_title' => LandingPageSetting::get('whitelabel_title', ''),
            'whitelabel_text' => LandingPageSetting::get('whitelabel_text', ''),
            'whitelabel_item1_title' => LandingPageSetting::get('whitelabel_item1_title', ''),
            'whitelabel_item1_text' => LandingPageSetting::get('whitelabel_item1_text', ''),
            'whitelabel_item2_title' => LandingPageSetting::get('whitelabel_item2_title', ''),
            'whitelabel_item2_text' => LandingPageSetting::get('whitelabel_item2_text', ''),
            'whitelabel_item3_title' => LandingPageSetting::get('whitelabel_item3_title', ''),
            'whitelabel_item3_text' => LandingPageSetting::get('whitelabel_item3_text', ''),
            'whitelabel_item4_title' => LandingPageSetting::get('whitelabel_item4_title', ''),
            'whitelabel_item4_text' => LandingPageSetting::get('whitelabel_item4_text', ''),
            'whitelabel_use_hero_image' => LandingPageSetting::get('whitelabel_use_hero_image', '0'),

            // Integrations Section (Novo)
            'integration1_title' => LandingPageSetting::get('integration1_title', ''),
            'integration1_text' => LandingPageSetting::get('integration1_text', ''),
            'integration2_title' => LandingPageSetting::get('integration2_title', ''),
            'integration2_text' => LandingPageSetting::get('integration2_text', ''),
            'integration3_title' => LandingPageSetting::get('integration3_title', ''),
            'integration3_text' => LandingPageSetting::get('integration3_text', ''),

            // FAQ Section
            'faq_subtitle' => LandingPageSetting::get('faq_subtitle', ''),
            'faq1_question' => LandingPageSetting::get('faq1_question', ''),
            'faq1_answer' => LandingPageSetting::get('faq1_answer', ''),
            'faq2_question' => LandingPageSetting::get('faq2_question', ''),
            'faq2_answer' => LandingPageSetting::get('faq2_answer', ''),
            'faq3_question' => LandingPageSetting::get('faq3_question', ''),
            'faq3_answer' => LandingPageSetting::get('faq3_answer', ''),
            'faq4_question' => LandingPageSetting::get('faq4_question', ''),
            'faq4_answer' => LandingPageSetting::get('faq4_answer', ''),
            'faq5_question' => LandingPageSetting::get('faq5_question', ''),
            'faq5_answer' => LandingPageSetting::get('faq5_answer', ''),
            'faq6_question' => LandingPageSetting::get('faq6_question', ''),
            'faq6_answer' => LandingPageSetting::get('faq6_answer', ''),
            'cta_title' => LandingPageSetting::get('cta_title', ''),
            'cta_text' => LandingPageSetting::get('cta_text', ''),
            'footer_text' => LandingPageSetting::get('footer_text', ''),
            'whatsapp_number' => LandingPageSetting::get('whatsapp_number', ''),
            'landing_effect_mode' => LandingPageSetting::get('landing_effect_mode', 'default'),
            'hero_stats1_value' => LandingPageSetting::get('hero_stats1_value', ''),
            'hero_stats1_label' => LandingPageSetting::get('hero_stats1_label', ''),
            'hero_stats2_value' => LandingPageSetting::get('hero_stats2_value', ''),
            'hero_stats2_label' => LandingPageSetting::get('hero_stats2_label', ''),
            'hero_stats3_value' => LandingPageSetting::get('hero_stats3_value', ''),
            'hero_stats3_label' => LandingPageSetting::get('hero_stats3_label', ''),

            // App Section
            'app_title' => LandingPageSetting::get('app_title', ''),
            'app_subtitle' => LandingPageSetting::get('app_subtitle', ''),
            'app_playstore_url' => LandingPageSetting::get('app_playstore_url', '#'),
        ];

        return view('admin.landing.index', compact('settings'));
    }

    /**
     * Salva as configurações da landing page
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Log inicial para debug
        Log::info('=== INÍCIO DO PROCESSAMENTO DE UPLOAD ===');
        Log::info('Arquivos recebidos:', [
            'logo' => $request->hasFile('logo') ? 'SIM' : 'NÃO',
            'favicon' => $request->hasFile('favicon') ? 'SIM' : 'NÃO',
            'hero_image' => $request->hasFile('hero_image') ? 'SIM' : 'NÃO',
        ]);
        
        // Validação dos campos de arquivo (mais permissiva para evitar bloqueios)
        try {
            $request->validate([
                'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:5120', // 5MB max
                'favicon' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:2048', // 2MB max
                'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240', // 10MB max
                'whitelabel_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240', // 10MB max
            ], [
                'logo.mimes' => 'O logo deve ser uma imagem (jpeg, png, jpg, gif, svg, ico, webp)',
                'logo.max' => 'O logo não pode ser maior que 5MB',
                'favicon.mimes' => 'O favicon deve ser uma imagem (jpeg, png, jpg, gif, svg, ico, webp)',
                'favicon.max' => 'O favicon não pode ser maior que 2MB',
                'hero_image.mimes' => 'A imagem hero deve ser uma imagem (jpeg, png, jpg, gif, svg, webp)',
                'hero_image.max' => 'A imagem hero não pode ser maior que 10MB',
                'whitelabel_image.mimes' => 'A imagem whitelabel deve ser uma imagem (jpeg, png, jpg, gif, svg, webp)',
                'whitelabel_image.max' => 'A imagem whitelabel não pode ser maior que 10MB',
            ]);
            Log::info('Validação passou com sucesso');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação:', $e->errors());
            return back()->withErrors($e->errors())->with('error', 'Erro na validação dos arquivos. Verifique os formatos e tamanhos.');
        }

        // Processa uploads de imagens
        // Todas as imagens serão salvas em public/IMG/
        $imageFields = [
            'logo' => 'IMG/landing/logo',
            'favicon' => 'IMG/landing/favicon',
            'hero_image' => 'IMG/landing/hero',
            'whitelabel_image' => 'IMG/landing/whitelabel',
        ];

        $uploadErrors = [];
        $uploadSuccess = [];

        foreach ($imageFields as $field => $path) {
            if ($request->hasFile($field)) {
                Log::info("=== PROCESSANDO {$field} ===");
                $file = $request->file($field);
                
                if (!$file->isValid()) {
                    $uploadErrors[] = "O arquivo {$field} não é válido.";
                    continue;
                }

                try {
                    $imgBaseDir = public_path('IMG');
                    if (!is_dir($imgBaseDir)) @mkdir($imgBaseDir, 0755, true);
                    
                    $fullPath = public_path($path);
                    if (!is_dir($fullPath)) @mkdir($fullPath, 0755, true);
                    
                    // Remove imagem antiga
                    $oldImage = LandingPageSetting::get($field);
                    if ($oldImage && file_exists(public_path($oldImage))) {
                        @unlink(public_path($oldImage));
                    }
                    
                    $extension = $file->getClientOriginalExtension() ?: 'png';
                    $fileName = $field . '_' . time() . '.' . $extension;
                    $targetPath = $fullPath . DIRECTORY_SEPARATOR . $fileName;
                    
                    if ($file->move($fullPath, $fileName)) {
                        $dbPath = $path . '/' . $fileName;
                        LandingPageSetting::set($field, $dbPath);
                        $uploadSuccess[] = ucfirst($field) . " atualizado!";
                    }
                } catch (\Exception $e) {
                    $uploadErrors[] = "Erro em {$field}: " . $e->getMessage();
                }
            }
        }

        // Se houver erros de upload, retorna com aviso mas continua salvando outros campos
        if (!empty($uploadErrors)) {
            $errorMessage = implode(' ', $uploadErrors);
            // Não retorna imediatamente, permite salvar outros campos
        }

        // Salva todas as outras configurações
        $fields = [
            'meta_title', 'meta_description',
            'hero_badge', 'hero_title', 'hero_subtitle', 'hero_cta_text',
            
            // Features
            'features_title', 'features_subtitle',
            'feature1_title', 'feature1_text',
            'feature2_title', 'feature2_text',
            'feature3_title', 'feature3_text',

            // Pricing
            'pricing_title', 'pricing_subtitle', 'pricing_note',
            
            // Whitelabel
            'whitelabel_title', 'whitelabel_text',
            'whitelabel_item1_title', 'whitelabel_item1_text',
            'whitelabel_item2_title', 'whitelabel_item2_text',
            'whitelabel_item3_title', 'whitelabel_item3_text',
            'whitelabel_item4_title', 'whitelabel_item4_text',
            'whitelabel_use_hero_image',

            // Integrations
            'integration1_title', 'integration1_text',
            'integration2_title', 'integration2_text',
            'integration3_title', 'integration3_text',

            // FAQ
            'faq_title', 'faq_subtitle',
            'faq1_question', 'faq1_answer',
            'faq2_question', 'faq2_answer',
            'faq3_question', 'faq3_answer',
            'faq4_question', 'faq4_answer',
            'faq5_question', 'faq5_answer',
            'faq6_question', 'faq6_answer',

            'cta_title', 'cta_text',
            'footer_text', 'whatsapp_number',
            'landing_effect_mode',
            'hero_stats1_value', 'hero_stats1_label',
            'hero_stats2_value', 'hero_stats2_label',
            'hero_stats3_value', 'hero_stats3_label',

            // New Sections
            'api_title', 'api_subtitle', 'api_text',
            'steps_title', 'steps_subtitle',
            'step1_title', 'step1_text',
            'step2_title', 'step2_text',
            'step3_title', 'step3_text',

            // App Settings
            'app_title', 'app_subtitle', 'app_playstore_url',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                LandingPageSetting::set($field, $request->input($field));
            }
        }

        // Log final
        Log::info('=== FIM DO PROCESSAMENTO DE UPLOAD ===');
        Log::info('Sucessos:', ['sucessos' => $uploadSuccess]);
        Log::info('Erros encontrados:', ['erros' => $uploadErrors]);
        
        // Retorna com mensagem apropriada
        if (!empty($uploadErrors) && !empty($uploadSuccess)) {
            $successMessage = implode(' ', $uploadSuccess);
            $errorMessage = implode(' | ', $uploadErrors);
            return back()
                ->with('success', $successMessage)
                ->with('error', 'Alguns problemas: ' . $errorMessage);
        }
        
        if (!empty($uploadErrors)) {
            $errorMessage = 'Erros ao salvar: ' . implode(' | ', $uploadErrors);
            Log::warning($errorMessage);
            return back()
                ->with('error', $errorMessage)
                ->with('warning', 'As outras configurações foram salvas com sucesso.');
        }

        if (!empty($uploadSuccess)) {
            Log::info('✅ Upload completo com sucesso!');
            return back()->with('success', implode(' ', $uploadSuccess) . ' Configurações salvas com sucesso!');
        }

        Log::info('✅ Configurações salvas (sem novos uploads)');
        return back()->with('success', 'Configurações da landing page salvas com sucesso!');
    }
}
