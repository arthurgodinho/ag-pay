<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatewayCredential extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'client_id',
        'client_secret',
        'wallet_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relacionamento: GatewayCredential pertence a um User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
