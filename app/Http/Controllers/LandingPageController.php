<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    /**
     * Exibe a landing page pública
     *
     * @return View
     */
    public function index(): View
    {
        // Busca todas as configurações da landing page
        $settings = [
            'hero_title' => LandingPageSetting::get('hero_title', 'Gateway de Pagamentos Moderno e Seguro'),
            'hero_subtitle' => LandingPageSetting::get('hero_subtitle', 'Integre pagamentos PIX e Cartão de forma simples e rápida'),
            'hero_cta_text' => LandingPageSetting::get('hero_cta_text', 'Começar Agora'),
            'hero_image' => LandingPageSetting::get('hero_image', ''),
            
            'features_title' => LandingPageSetting::get('features_title', 'Por que escolher nosso gateway?'),
            'features_subtitle' => LandingPageSetting::get('features_subtitle', 'Oferecemos a solução completa para seus pagamentos'),
            
            'pricing_title' => LandingPageSetting::get('pricing_title', 'Planos e Preços'),
            'pricing_subtitle' => LandingPageSetting::get('pricing_subtitle', 'Escolha o plano ideal para seu negócio'),
            
            'about_title' => LandingPageSetting::get('about_title', 'Sobre Nós'),
            'about_text' => LandingPageSetting::get('about_text', 'Somos uma plataforma de pagamentos completa e confiável'),
            
            'footer_text' => LandingPageSetting::get('footer_text', '© 2024 PagueMax. Todos os direitos reservados.'),
            'footer_links' => LandingPageSetting::get('footer_links', '[]'),
            
            'logo_url' => LandingPageSetting::get('logo_url', ''),
            'favicon_url' => LandingPageSetting::get('favicon_url', ''),
        ];

        // Converte JSON strings para arrays
        if (is_string($settings['footer_links'])) {
            $settings['footer_links'] = json_decode($settings['footer_links'], true) ?? [];
        }

        // Busca taxas do sistema (usando as novas taxas detalhadas)
        $cashinFixo = Setting::get('cashin_pix_fixo', Setting::get('cashin_fixo', '1.00'));
        $cashinPercentual = Setting::get('cashin_pix_percentual', Setting::get('cashin_percentual', '3.00'));
        $cashoutFixo = Setting::get('cashout_pix_fixo', Setting::get('cashout_fixo', '1.00'));
        $cashoutPercentual = Setting::get('cashout_pix_percentual', Setting::get('cashout_percentual', '2.00'));

        return view('landing.index', compact('settings', 'cashinFixo', 'cashinPercentual', 'cashoutFixo', 'cashoutPercentual'));
    }
}
