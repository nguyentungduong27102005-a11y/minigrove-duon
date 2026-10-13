<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'item_type',
        'product_id',
        'custom_design_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * CartItem thuộc một Cart.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Product nếu đây là item PRODUCT.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Custom Design nếu đây là item CUSTOM_DESIGN.
     */
    public function customDesign()
    {
        return $this->belongsTo(CustomDesign::class);
    }
}