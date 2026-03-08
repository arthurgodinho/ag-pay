<?php

namespace App\Helpers;

class WebhookUrlHelper
{
    /**
     * Gera URL absoluta para webhook usando o domínio configurado no .env
     *
     * @param string $routeName Nome da rota
     * @param array $parameters Parâmetros opcionais da rota
     * @return string URL absoluta
     */
    public static function generateUrl(string $routeName, array $parameters = []): string
    {
        // Usa o domínio configurado no .env (APP_URL)
        $baseUrl = config('app.url', env('APP_URL', 'http://localhost'));
        
        // Remove barra final se houver
        $baseUrl = rtrim($baseUrl, '/');
        
        // Gera a rota relativa (sem domínio)
        $relativeUrl = route($routeName, $parameters, false);
        
        // Remove barra inicial se houver
        $relativeUrl = ltrim($relativeUrl, '/');
        
        // Remove /public se estiver na rota relativa
        $relativeUrl = str_replace('public/', '', $relativeUrl);
        
        // Combina base URL com rota relativa
        $absoluteUrl = $baseUrl . '/' . $relativeUrl;
        
        // Remove barras duplas
        $absoluteUrl = str_replace('//', '/', $absoluteUrl);
        $absoluteUrl = str_replace('http:/', 'http://', $absoluteUrl);
        $absoluteUrl = str_replace('https:/', 'https://', $absoluteUrl);
        
        // Log para debug (sem valores sensíveis)
        \Log::info('WebhookUrlHelper: Gerando URL de webhook', [
            'route_name' => $routeName,
            'base_url_final' => $baseUrl,
            'relative_url' => $relativeUrl,
            'absolute_url' => $absoluteUrl,
        ]);
        
        return $absoluteUrl;
    }
}

