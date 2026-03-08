<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Busca uma configuração
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        try {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
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
    }
}








