<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionSplit extends Model
{
    protected $fillable = [
        'transaction_id',
        'payment_split_id',
        'recipient_user_id',
        'amount',
        'split_type',
        'split_value',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'split_value' => 'decimal:2',
        ];
    }

    /**
     * Relacionamento: TransactionSplit pertence a uma Transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relacionamento: TransactionSplit pertence a um PaymentSplit
     */
    public function paymentSplit(): BelongsTo
    {
        return $this->belongsTo(PaymentSplit::class);
    }

    /**
     * Relacionamento: TransactionSplit tem um recipient
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
