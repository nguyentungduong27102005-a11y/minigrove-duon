<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequestItem extends Model
{
    use HasFactory;


    protected $fillable = [
        'return_request_id',
        'order_detail_id',
        'quantity',
        'unit_price',
        'refund_amount',
    ];


    protected $casts = [
        'quantity' =>
            'integer',

        'unit_price' =>
            'decimal:2',

        'refund_amount' =>
            'decimal:2',
    ];


    /**
     * Yêu cầu trả hàng.
     */
    public function returnRequest()
    {
        return $this->belongsTo(
            ReturnRequest::class
        );
    }


    /**
     * OrderDetail được trả.
     */
    public function orderDetail()
    {
        return $this->belongsTo(
            OrderDetail::class
        );
    }
}