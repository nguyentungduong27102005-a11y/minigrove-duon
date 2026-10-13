<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'phone',
        'email',
        'shipping_address',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'shipping_discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'shipping_discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Order thuộc một User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order có nhiều OrderDetail.
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
/**
 * Lịch sử trạng thái Order.
 */
public function statusHistories()
{
    return $this->hasMany(OrderStatusHistory::class);
}

/**
 * Các lần thanh toán của Order.
 */
public function payments()
{
    return $this->hasMany(Payment::class);
}

/**
 * Danh sách các Voucher được sử dụng cho Order.
 */
public function voucherUsages()
{
    return $this->hasMany(VoucherUsage::class);
}

/**
 * Voucher được sử dụng cho Order (giới hạn scope order để tương thích code cũ).
 */
public function voucherUsage()
{
    return $this->hasOne(VoucherUsage::class)
        ->where(function ($query) {
            $query->where('voucher_scope', Voucher::SCOPE_ORDER)
                ->orWhereNull('voucher_scope');
        });
}

/**
 * Voucher giảm giá đơn hàng.
 */
public function orderVoucherUsage()
{
    return $this->hasOne(VoucherUsage::class)
        ->where(function ($query) {
            $query->where('voucher_scope', Voucher::SCOPE_ORDER)
                ->orWhereNull('voucher_scope');
        });
}

/**
 * Voucher miễn/giảm phí vận chuyển.
 */
public function shippingVoucherUsage()
{
    return $this->hasOne(VoucherUsage::class)
        ->where('voucher_scope', Voucher::SCOPE_SHIPPING);
}
/**
 * Các biến động kho phát sinh từ Order.
 */
public function stockHistories()
{
    return $this->hasMany(StockHistory::class);
}
/**
 * Các yêu cầu trả hàng / hoàn tiền
 * của Order.
 */
public function returnRequests()
{
    return $this->hasMany(
        ReturnRequest::class
    );
}

}