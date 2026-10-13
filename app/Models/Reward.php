<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;


    protected $fillable = [

        'name',

        'reward_type',

        'voucher_id',

        'points_required',

        'description',

        'stock_quantity',

        'start_at',

        'end_at',

        'is_active',

    ];



    protected $casts = [

        'points_required' => 'integer',

        'stock_quantity' => 'integer',

        'start_at' => 'datetime',

        'end_at' => 'datetime',

        'is_active' => 'boolean',

    ];



    /**
     * Reward có thể liên kết với Voucher.
     */
    public function voucher()
    {
        return $this->belongsTo(
            Voucher::class
        );
    }



    /**
     * Reward có nhiều lần đổi thưởng.
     */
    public function redemptions()
    {
        return $this->hasMany(
            RewardRedemption::class
        );
    }

}