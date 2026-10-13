<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GreenReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_type',
        'description',
        'condition_description',
        'image_paths',
        'status',
        'handled_by',
        'staff_note',
        'rejection_reason',
        'reward_type',
        'point_transaction_id',
        'voucher_id',
        'rewarded_by',
        'submitted_at',
        'approved_at',
        'received_at',
        'rewarded_at',
    ];

    protected $casts = [
        'image_paths' => 'array',

        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    /**
     * Customer gửi yêu cầu.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Staff/Admin xử lý.
     */
    public function handler()
    {
        return $this->belongsTo(
            User::class,
            'handled_by'
        );
    }

    /**
     * Point Transaction được tạo khi thưởng Point.
     */
    public function pointTransaction()
    {
        return $this->belongsTo(PointTransaction::class);
    }

    /**
     * Voucher được cấp nếu thưởng Voucher.
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Staff/Admin phát thưởng.
     */
    public function rewarder()
    {
        return $this->belongsTo(
            User::class,
            'rewarded_by'
        );
    }
}