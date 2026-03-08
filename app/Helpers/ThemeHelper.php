<?php

namespace App\Helpers;

use App\Models\Setting;

class ThemeHelper
{
    /**
     * Obtém todas as cores do tema (com cache e tratamento de erros)
     */
    public static function getThemeColors(): array
    {
        try {
            // Usa cache estático para evitar múltiplas queries
            static $cachedColors = null;
            
            if ($cachedColors !== null) {
                return $cachedColors;
            }
            
            $cachedColors = [
                'primary' => Setting::get('theme_primary_color', '#0097c9'), // Azul ciano
                'secondary' => Setting::get('theme_secondary_color', '#64748b'), // Cinza azulado
                'accent' => Setting::get('theme_accent_color', '#0097c9'), // Azul ciano
                'background' => Setting::get('theme_background_color', '#121b2f'), // Azul escuro
                'dashboard_bg' => Setting::get('theme_dashboard_bg', '#121b2f'), // Azul escuro
                'landing_bg' => Setting::get('theme_landing_bg', '#121b2f'), // Azul escuro
                'sidebar_bg' => Setting::get('theme_sidebar_bg', '#121b2f'), // Azul escuro
                'text' => Setting::get('theme_text_color', '#e2e8f0'), // Slate 200
                'card_bg' => Setting::get('theme_card_bg', '#1a2332'), // Azul escuro mais claro
            ];
            
            return $cachedColors;
        } catch (\Exception $e) {
            // Retorna valores padrão em caso de erro
            return [
                'primary' => '#0097c9',
                'secondary' => '#64748b',
                'accent' => '#0097c9',
                'background' => '#121b2f',
                'dashboard_bg' => '#121b2f',
                'landing_bg' => '#121b2f',
                'sidebar_bg' => '#121b2f',
                'text' => '#e2e8f0',
                'card_bg' => '#1a2332',
            ];
        }
    }

    /**
     * Obtém uma cor específica do tema
     */
    public static function getColor(string $key, string $default = null): string
    {
        $colors = self::getThemeColors();
        return $colors[$key] ?? $default ?? '#0097c9';
    }

    /**
     * Gera CSS customizado com as cores do tema
     */
    public static function generateThemeCSS(): string
    {
        $colors = self::getThemeColors();
        
        return "
            :root {
                --theme-primary: {$colors['primary']};
                --theme-secondary: {$colors['secondary']};
                --theme-accent: {$colors['accent']};
                --theme-background: {$colors['background']};
                --theme-dashboard-bg: {$colors['dashboard_bg']};
                --theme-landing-bg: {$colors['landing_bg']};
                --theme-sidebar-bg: {$colors['sidebar_bg']};
                --theme-text: {$colors['text']};
                --theme-card-bg: {$colors['card_bg']};
            }
        ";
    }
}

