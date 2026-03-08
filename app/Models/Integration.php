<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Integration extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'store_url',
        'api_key',
        'api_secret',
        'webhook_secret',
        'settings',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    /**
     * Relacionamento: Integration pertence a um User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

