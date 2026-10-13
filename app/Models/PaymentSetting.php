<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;


    protected $fillable = [
        'payment_method',
        'display_name',
        'is_enabled',
        'bank_name',
        'account_name',
        'account_number',
    ];


    protected $casts = [
        'is_enabled' => 'boolean',
    ];


    /**
     * Kiểm tra một phương thức
     * có đang được bật hay không.
     */
    public static function isEnabled(
        string $paymentMethod
    ): bool {
        return static::query()
            ->where(
                'payment_method',
                strtoupper($paymentMethod)
            )
            ->where(
                'is_enabled',
                true
            )
            ->exists();
    }


    /**
     * Lấy cấu hình theo mã phương thức.
     */
    public static function findByMethod(
        string $paymentMethod
    ): ?self {
        return static::query()
            ->where(
                'payment_method',
                strtoupper($paymentMethod)
            )
            ->first();
    }
}