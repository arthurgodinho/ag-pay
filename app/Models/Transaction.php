<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'amount_gross',
        'amount_net',
        'fee',
        'type',
        'status',
        'gateway_provider',
        'external_id',
        'payer_name',
        'payer_email',
        'payer_cpf',
        'payer_phone',
        'payer_address',
        'description',
        'expires_at',
        'paid_at',
        'released_at',
        'available_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_gross' => 'decimal:2',
            'amount_net' => 'decimal:2',
            'fee' => 'decimal:2',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'released_at' => 'datetime',
            'available_at' => 'datetime',
        ];
    }

    /**
     * Boot do model para gerar UUID automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->uuid)) {
                $transaction->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relacionamento: Transaction pertence a um User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: Transaction pertence a um Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


    /**
     * Relacionamento: Transaction tem muitos TransactionSplits
     */
    public function splits()
    {
        return $this->hasMany(TransactionSplit::class);
    }

    /**
     * Relacionamento: Transaction tem muitos Chargebacks
     */
    public function chargebacks()
    {
        return $this->hasMany(Chargeback::class);
    }
}
