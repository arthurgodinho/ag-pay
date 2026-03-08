<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chargeback extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'amount',
        'status',
        'external_id',
        'reason',
        'admin_note',
        'withdrawal_blocked',
        'balance_debited',
        'account_negativated',
        'negative_balance',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'negative_balance' => 'decimal:2',
            'withdrawal_blocked' => 'boolean',
            'balance_debited' => 'boolean',
            'account_negativated' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento: Chargeback pertence a uma Transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relacionamento: Chargeback pertence a um User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
