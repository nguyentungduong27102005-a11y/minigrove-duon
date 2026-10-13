<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use HasFactory;


    protected $fillable = [
        'return_code',
        'order_id',
        'user_id',
        'reason',
        'description',
        'status',
        'refund_amount',
        'processed_by',
        'processed_at',
        'processing_note',
    ];


    protected $casts = [
        'refund_amount' =>
            'decimal:2',

        'processed_at' =>
            'datetime',
    ];


    /**
     * Order được yêu cầu trả.
     */
    public function order()
    {
        return $this->belongsTo(
            Order::class
        );
    }


    /**
     * Customer gửi yêu cầu.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }


    /**
     * Staff/Admin xử lý.
     */
    public function processor()
    {
        return $this->belongsTo(
            User::class,
            'processed_by'
        );
    }


    /**
     * Các sản phẩm cần trả.
     */
    public function items()
    {
        return $this->hasMany(
            ReturnRequestItem::class
        );
    }
}