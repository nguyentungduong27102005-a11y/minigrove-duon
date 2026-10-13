<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'redemption_code',
        'user_id',
        'reward_id',
        'point_transaction_id',
        'points_spent',
        'reward_snapshot',
        'status',
        'handled_by',
        'redeemed_at',
        'fulfilled_at',
        'note',
    ];

    protected $casts = [
        'points_spent' => 'integer',
        'reward_snapshot' => 'array',
        'redeemed_at' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function pointTransaction()
    {
        return $this->belongsTo(PointTransaction::class);
    }

    public function handler()
    {
        return $this->belongsTo(
            User::class,
            'handled_by'
        );
    }
}