<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDesign extends Model
{
    use HasFactory;


    protected $fillable = [

        'user_id',

        'design_code',

        'name',

        'style',

        'estimated_price',

        'configuration_snapshot',

        'status',

    ];



    protected $casts = [

        'estimated_price' => 'decimal:2',

        'configuration_snapshot' => 'array',

    ];



    /**
     * Một Custom Design thuộc một User.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }



    /**
     * Một Custom Design có nhiều item cấu hình.
     */
    public function items()
    {
        return $this->hasMany(
            CustomDesignItem::class
        );
    }



    /**
     * Custom Design có thể xuất hiện trong CartItem.
     */
    public function cartItems()
    {
        return $this->hasMany(
            CartItem::class
        );
    }



    /**
     * Custom Design có thể xuất hiện trong OrderDetail.
     */
    public function orderDetails()
    {
        return $this->hasMany(
            OrderDetail::class
        );
    }



    /**
     * Các Review dành cho Custom Design.
     */
    public function reviews()
    {
        return $this->hasMany(
            Review::class
        );
    }

}