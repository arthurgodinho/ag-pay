<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'amount_gross',
        'fee',
        'pix_key',
        'external_id',
        'status',
        'proof_url',
        'admin_note',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'amount_gross' => 'decimal:2',
            'fee' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento: Withdrawal pertence a um User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
