<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Busca ou cria uma configuração (com cache para performance)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        try {
            // Usa cache para evitar queries repetidas (cache de 5 minutos)
            return Cache::remember("setting.{$key}", 300, function () use ($key, $default) {
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
        } catch (\Exception $e) {
            // Se houver qualquer erro (banco não conectado, tabela não existe, etc), retorna o valor padrão
            return $default;
        } catch (\Error $e) {
            // Captura erros fatais também (ex: "Call to a member function connection() on null")
            return $default;
        }
    }

    /**
     * Define uma configuração
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        // Limpa o cache quando uma configuração é atualizada
        Cache::forget("setting.{$key}");
    }
}
