<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    use HasFactory;

    protected $fillable = [ //cho phép tạo 1 mục yêu thích
        'wishlist_id', //mục này thuộc Wishlist số ...
        'product_id', //sản phẩm được yêu thích là sản phẩm số ...
    ];

    /**
     * Mỗi WishlistItem chỉ thuộc về một Wishlist.
     */
    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    /**
     * Mỗi WishlistItem tham chiếu đến một Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}