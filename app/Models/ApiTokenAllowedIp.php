<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiTokenAllowedIp extends Model
{
    protected $fillable = [
        'api_token_id',
        'ip_address',
    ];

    /**
     * Relacionamento: ApiTokenAllowedIp pertence a um ApiToken
     */
    public function apiToken(): BelongsTo
    {
        return $this->belongsTo(ApiToken::class);
    }
}









