<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'project',
        'name',
        'token',
        'last_used_at',
        'expires_at',
        'is_active',
        'withdrawal_mode',
        'webhook_url',
    ];

    protected $hidden = [
        'token',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot do model para gerar token automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($apiToken) {
            // Gera Client ID se não existir
            if (empty($apiToken->client_id)) {
                $username = $apiToken->user->name ?? 'user';
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $username));
                $timestamp = time() * 1000; // milissegundos
                $random = rand(1000, 9999);
                $apiToken->client_id = $username . '_' . $timestamp . $random;
            }
            
            // Gera token se não existir
            if (empty($apiToken->token)) {
                // Gera um token único: prefixo + token aleatório
                $prefix = 'nxp_';
                $randomToken = Str::random(64);
                $apiToken->token = $prefix . $randomToken;
            }
        });
    }

    /**
     * Relacionamento: ApiToken pertence a um User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: ApiToken tem muitos IPs permitidos
     */
    public function allowedIps()
    {
        return $this->hasMany(\App\Models\ApiTokenAllowedIp::class);
    }

    /**
     * Verifica se o token está expirado
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Verifica se o token está válido
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Verifica se o token está ativo
     */
    public function isActive(): bool
    {
        return $this->isValid();
    }

    /**
     * Busca um token pelo valor completo
     */
    public static function findByToken(string $token): ?self
    {
        return self::where('token', $token)->first();
    }
}
