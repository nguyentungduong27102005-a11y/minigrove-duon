<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    public const SCOPE_ORDER = 'order';
    public const SCOPE_SHIPPING = 'shipping';

    protected $fillable = [
        'code',
        'voucher_scope',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'start_at',
        'end_at',
        'usage_limit',
        'usage_count',
        'per_user_limit',
        'is_public',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',

        'start_at' => 'datetime',
        'end_at' => 'datetime',

        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'per_user_limit' => 'integer',

        'is_public' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function isOrderScope(): bool
    {
        return ($this->voucher_scope ?? self::SCOPE_ORDER) === self::SCOPE_ORDER;
    }

    public function isShippingScope(): bool
    {
        return $this->voucher_scope === self::SCOPE_SHIPPING;
    }

    public function isPublic(): bool
    {
        return (bool) ($this->is_public ?? true);
    }
    /**
 * Các Reward sử dụng Voucher này.
 */
public function rewards()
{
    return $this->hasMany(Reward::class);
}
/**
 * Các Green Return nhận Voucher này.
 */
public function greenReturnRequests()
{
    return $this->hasMany(GreenReturnRequest::class);
}
/**
 * Các lượt sử dụng Voucher.
 */
public function usages()
{
    return $this->hasMany(VoucherUsage::class);
}

/**
 * Các Customer đã lưu Voucher này.
 */
public function savedByUsers()
{
    return $this->belongsToMany(
        User::class,
        'saved_vouchers'
    )->withTimestamps();
}

}