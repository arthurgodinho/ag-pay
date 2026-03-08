<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'port' => 'integer',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Busca as configurações SMTP ativas
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Acessor para descriptografar a senha
     */
    public function getDecryptedPasswordAttribute()
    {
        if (empty($this->password)) {
            return '';
        }
        
        try {
            return decrypt($this->password);
        } catch (\Exception $e) {
            // Se não conseguir descriptografar, retorna como está (pode estar em texto plano)
            return $this->password;
        }
    }

    /**
     * Aplica as configurações SMTP ao config do Laravel
     */
    public function applyToConfig()
    {
        if (!$this->is_active) {
            return;
        }

        config([
            'mail.default' => $this->mailer,
            'mail.mailers.smtp.host' => $this->host,
            'mail.mailers.smtp.port' => $this->port,
            'mail.mailers.smtp.username' => $this->username,
            'mail.mailers.smtp.password' => $this->decrypted_password,
            'mail.mailers.smtp.encryption' => $this->encryption,
            'mail.from.address' => $this->from_address,
            'mail.from.name' => $this->from_name,
        ]);
    }
}

