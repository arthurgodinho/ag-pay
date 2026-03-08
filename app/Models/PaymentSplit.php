<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentSplit extends Model
{
    protected $fillable = [
        'user_id',
        'recipient_user_id',
        'split_type',
        'split_value',
        'is_active',
        'description',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'split_value' => 'decimal:2',
            'is_active' => 'boolean',
            'priority' => 'integer',
        ];
    }

    /**
     * Relacionamento: Split pertence a um User (quem configura)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: Split tem um recipient (quem recebe)
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * Relacionamento: Split tem muitos transaction_splits (histórico)
     */
    public function transactionSplits(): HasMany
    {
        return $this->hasMany(TransactionSplit::class);
    }
}
