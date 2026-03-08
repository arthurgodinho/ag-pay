<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderBump extends Model
{
    protected $fillable = [
        'product_id',
        'image',
        'name',
        'description',
        'value_from',
        'value_for',
        'is_active',
    ];

    protected $casts = [
        'value_from' => 'decimal:2',
        'value_for' => 'decimal:2',
        'is_active' => 'boolean',
    ];

}
