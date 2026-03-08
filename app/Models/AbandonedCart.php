<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'email',
        'name',
        'session_id',
        'cart_data',
        'total_amount',
        'reminder_sent_count',
        'last_reminder_at',
        'recovered_at',
    ];

    protected $casts = [
        'cart_data' => 'array',
        'total_amount' => 'decimal:2',
        'reminder_sent_count' => 'integer',
        'last_reminder_at' => 'datetime',
        'recovered_at' => 'datetime',
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Busca produto do checkout
     */
    public function getProductAttribute()
    {
        if (!$this->product_id) {
            return null;
        }
        
        return \Illuminate\Support\Facades\DB::table('products')
            ->where('id', $this->product_id)
            ->first();
    }

    /**
     * Busca carrinhos abandonados que precisam de lembrete
     */
    public static function getPendingReminders($hoursAgo = 24, $maxReminders = 3)
    {
        return self::whereNull('recovered_at')
            ->where('reminder_sent_count', '<', $maxReminders)
            ->where(function($query) use ($hoursAgo) {
                $query->whereNull('last_reminder_at')
                    ->orWhere('last_reminder_at', '<', now()->subHours($hoursAgo));
            })
            ->where('created_at', '<', now()->subHours($hoursAgo))
            ->get();
    }
}

