<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorLog extends Model
{
    protected $table = 'error_logs';

    protected $fillable = [
        'level',
        'type',
        'title',
        'message',
        'context',
        'user_id',
        'transaction_id',
        'file',
        'line',
        'trace',
        'ip_address',
        'user_agent',
        'resolved',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];

    protected $casts = [
        'context' => 'array',
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    /**
     * Relacionamento com o usuário que causou o erro
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacionamento com o admin que resolveu
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope para erros não resolvidos
     */
    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }

    /**
     * Scope para erros resolvidos
     */
    public function scopeResolved($query)
    {
        return $query->where('resolved', true);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para filtrar por nível
     */
    public function scopeOfLevel($query, string $level)
    {
        return $query->where('level', $level);
    }
}