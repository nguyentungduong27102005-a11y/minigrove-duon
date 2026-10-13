<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_type',
        'product_id',
        'custom_design_id',
        'item_name',
        'item_code',
        'unit_price',
        'quantity',
        'subtotal',
        'item_snapshot',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
        'item_snapshot' => 'array',
    ];

    /**
     * OrderDetail thuộc một Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Product nếu item_type = PRODUCT.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * CustomDesign nếu item_type = CUSTOM_DESIGN.
     */
    public function customDesign()
    {
        return $this->belongsTo(CustomDesign::class);
    }
/**
 * Mỗi OrderDetail có tối đa một Review.
 */
public function review()
{
    return $this->hasOne(Review::class);
}
/**
 * Một OrderDetail có thể tạo nhiều Grove.
 *
 * Nếu quantity = n thì có thể tạo n Grove.
 */
public function groves()
{
    return $this->hasMany(Grove::class);
}
/**
 * Warranty của OrderDetail.
 */
public function warranty()
{
    return $this->hasOne(Warranty::class);
}
/**
 * Các lần OrderDetail được
 * yêu cầu trả hàng.
 */
public function returnRequestItems()
{
    return $this->hasMany(
        ReturnRequestItem::class
    );
}

}