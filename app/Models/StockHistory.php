<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;


    protected $fillable = [

        'stock_type',

        'product_id',

        'transaction_type',

        'quantity_before',

        'quantity_change',

        'quantity_after',

        'order_id',

        'updated_by',

        'note',

    ];


    protected $casts = [

        'quantity_before' => 'integer',

        'quantity_change' => 'integer',

        'quantity_after' => 'integer',

    ];



    /**
     * Product phát sinh biến động tồn kho.
     */
    public function product()
    {
        return $this->belongsTo(
            Product::class
        );
    }



    /**
     * Order gây ra thay đổi tồn kho.
     */
    public function order()
    {
        return $this->belongsTo(
            Order::class
        );
    }



    /**
     * Người thực hiện thay đổi kho.
     */
    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

}