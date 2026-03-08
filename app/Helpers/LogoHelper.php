<?php

namespace App\Helpers;

use App\Models\LandingPageSetting;
use Illuminate\Support\Facades\Storage;

class LogoHelper
{
    /**
     * Obtém a URL da logo configurada (com tratamento de erros)
     *
     * @return string|null
     */
    public static function getLogoUrl(): ?string
    {
        try {
            $logo = LandingPageSetting::get('logo', '');
            
            if (empty($logo)) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
        
        // Se já é uma URL completa, retorna
        if (filter_var($logo, FILTER_VALIDATE_URL)) {
            return $logo;
        }
        
        // Primeiro, tenta na pasta IMG (novo padrão)
        $imgPath = public_path($logo);
        if (file_exists($imgPath)) {
            return asset($logo);
        }
        
        // Compatibilidade: tenta no storage antigo
        $storagePath = storage_path('app/public/' . str_replace('IMG/', '', $logo));
        if (file_exists($storagePath)) {
            return asset('storage/' . str_replace('IMG/', '', $logo));
        }
        
        // Compatibilidade: tenta no public/storage
        $publicStoragePath = public_path('storage/' . str_replace('IMG/', '', $logo));
        if (file_exists($publicStoragePath)) {
            return asset('storage/' . str_replace('IMG/', '', $logo));
        }
        
        // Se o caminho começa com IMG/, retorna direto (mesmo que o arquivo não exista ainda)
        if (strpos($logo, 'IMG/') === 0) {
            return asset($logo);
        }
        
        return null;
    }
    
    /**
     * Obtém a URL do favicon configurado (com tratamento de erros)
     *
     * @return string|null
     */
    public static function getFaviconUrl(): ?string
    {
        try {
            $favicon = LandingPageSetting::get('favicon', '');
            
            if (empty($favicon)) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
        
        // Se já é uma URL completa, retorna
        if (filter_var($favicon, FILTER_VALIDATE_URL)) {
            return $favicon;
        }
        
        // Primeiro, tenta na pasta IMG (novo padrão)
        $imgPath = public_path($favicon);
        if (file_exists($imgPath)) {
            return asset($favicon);
        }
        
        // Compatibilidade: tenta no storage antigo
        $storagePath = storage_path('app/public/' . str_replace('IMG/', '', $favicon));
        if (file_exists($storagePath)) {
            return asset('storage/' . str_replace('IMG/', '', $favicon));
        }
        
        // Compatibilidade: tenta no public/storage
        $publicStoragePath = public_path('storage/' . str_replace('IMG/', '', $favicon));
        if (file_exists($publicStoragePath)) {
            return asset('storage/' . str_replace('IMG/', '', $favicon));
        }
        
        // Se o caminho começa com IMG/, retorna direto (mesmo que o arquivo não exista ainda)
        if (strpos($favicon, 'IMG/') === 0) {
            return asset($favicon);
        }
        
        return null;
    }
    
    /**
     * Renderiza a tag de favicon
     *
     * @return string
     */
    public static function renderFavicon(): string
    {
        $faviconUrl = self::getFaviconUrl();
        
        if ($faviconUrl) {
            return '<link rel="icon" type="image/x-icon" href="' . e($faviconUrl) . '">';
        }
        
        return '';
    }
    
    /**
     * Renderiza a logo como imagem ou texto
     *
     * @param string $class Classes CSS para a imagem
     * @param string $alt Texto alternativo
     * @param string $fallbackText Texto a ser exibido se não houver logo
     * @return string
     */
    public static function renderLogo(string $class = 'h-8', string $alt = 'Logo', ?string $fallbackText = null): string
    {
        $logoUrl = self::getLogoUrl();
        
        if ($logoUrl) {
            return '<img src="' . e($logoUrl) . '" alt="' . e($alt) . '" class="' . e($class) . '">';
        }
        
        // Se não especificar fallback, usa o nome do sistema
        if ($fallbackText === null) {
            $fallbackText = self::getSystemName();
        }
        
        return '<span class="text-2xl font-bold">' . e($fallbackText) . '</span>';
    }
    
    /**
     * Obtém o nome do sistema/empresa configurado pelo admin (com cache e tratamento de erros)
     *
     * @return string
     */
    public static function getSystemName(): string
    {
        try {
            // Usa cache estático para evitar múltiplas queries
            static $cachedName = null;
            
            if ($cachedName !== null) {
                return $cachedName;
            }
            
            // Busca de Setting (configurações gerais do admin)
            $siteName = \App\Models\Setting::get('gateway_name', '');
            
            // Se não encontrar, tenta buscar de LandingPageSetting (configurado na landing page)
            if (empty($siteName)) {
                $siteName = LandingPageSetting::get('site_name', '');
            }
            
            // Se ainda não encontrar, tenta extrair do footer_text
            if (empty($siteName)) {
                $footerText = LandingPageSetting::get('footer_text', '');
                if (!empty($footerText) && preg_match('/©\s*\d{4}\s*(.+?)(?:\s|\.|Todos)/i', $footerText, $matches)) {
                    $siteName = trim($matches[1]);
                }
            }
            
            // Se ainda não encontrar, usa o padrão
            if (empty($siteName)) {
                $siteName = 'PagueMax';
            }
            
            $cachedName = $siteName;
            return $siteName;
        } catch (\Exception $e) {
            // Retorna valor padrão em caso de erro
            return 'PagueMax';
        }
    }
}




