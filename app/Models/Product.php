<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'description',
        'download_url',
        'price',
        'image',
        'is_active',
        // Visual
        'checkout_title',
        'checkout_description',
        'banner_image',
        'background_color',
        'background_image',
        'primary_color',
        'checkout_logo',
        'product_image',
        'show_product_image',
        'show_security_badges',
        'show_guarantee',
        'enable_pix',
        'enable_credit_card',
        'product_image_url',
        
        // Info
        'security_info',
        'guarantee_info',
        'payment_methods_info',
        'category',
        'product_type',
        'charge_type',
        'warranty_period',
        'warranty_days',
        'support_whatsapp',
        'support_email',
        'deliverable_info',
        
        // Config
        'use_default_thankyou_page',
        'thankyou_page_url',
        'has_timer',
        'timer_minutes',
        'has_social_proof',
        'fake_reviews_json',
        
        // Tracking
        'pixel_id',
        'google_id',
        'tiktok_id',
        'pixel_facebook',
        'pixel_google',
        'pixel_tiktok',
        
        // Order Bump
        'order_bump_active',
        'order_bump_product_id',
        'order_bump_price',
        'order_bump_title',
        'order_bump_description',
        
        // Metrics
        'views_count',
        'sales_count',
        'total_revenue',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'has_timer' => 'boolean',
        'has_social_proof' => 'boolean',
        'order_bump_active' => 'boolean',
        'use_default_thankyou_page' => 'boolean',
        'show_product_image' => 'boolean',
        'show_security_badges' => 'boolean',
        'show_guarantee' => 'boolean',
        'fake_reviews_json' => 'array',
        'order_bump_price' => 'decimal:2',
        'total_revenue' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
