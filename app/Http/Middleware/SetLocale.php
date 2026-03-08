<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Idiomas disponíveis
        $availableLocales = ['pt', 'es', 'en'];
        
        // Determina o idioma a ser usado
        $locale = null;
        
        // 1. Verifica se o usuário definiu um idioma na sessão (via seletor)
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, $availableLocales)) {
                $locale = $sessionLocale;
            }
        }
        
        // 2. Se não tem na sessão, verifica o idioma preferido do usuário autenticado
        if (!$locale && Auth::check()) {
            try {
                $userLocale = Auth::user()->language;
                if ($userLocale && in_array($userLocale, $availableLocales)) {
                    $locale = $userLocale;
                    // Salva na sessão para evitar consultas ao banco
                    Session::put('locale', $locale);
                }
            } catch (\Exception $e) {
                // Ignora erros ao buscar idioma do usuário
            }
        }
        
        // 3. Se ainda não tem, usa o idioma padrão do sistema (com cache)
        if (!$locale) {
            try {
                // Usa cache para evitar queries repetidas
                $defaultLocale = \Illuminate\Support\Facades\Cache::remember('app.default_locale', 3600, function () use ($availableLocales) {
                    // Primeiro tenta buscar do banco de dados
                    $locale = \App\Models\Setting::get('default_language', 'pt');
                    
                    // Se não encontrou ou não é válido, verifica o config
                    if (!$locale || !in_array($locale, $availableLocales)) {
                        $locale = config('app.locale', 'pt');
                        
                        // Se o config está como 'en', força para 'pt'
                        if ($locale === 'en' || !in_array($locale, $availableLocales)) {
                            $locale = 'pt';
                        }
                    }
                    
                    return $locale;
                });
                
                $locale = $defaultLocale;
            } catch (\Exception $e) {
                // Se houver erro, usa português como padrão
                $locale = 'pt';
            }
        }
        
        // Garante que o locale é válido
        if (!in_array($locale, $availableLocales)) {
            $locale = 'pt';
        }
        
        // Define o locale da aplicação
        App::setLocale($locale);
        
        // Define também no Carbon para datas
        try {
            \Carbon\Carbon::setLocale($locale);
        } catch (\Exception $e) {
            // Ignora erros do Carbon
        }
        
        return $next($request);
    }
}
