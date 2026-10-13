<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_type',
        'points_change',
        'balance_before',
        'balance_after',
        'reason',
        'reference_type',
        'reference_id',
        'event_key',
        'performed_by',
        'occurred_at',
    ];

    protected $casts = [
        'points_change' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'reference_id' => 'integer',
        'occurred_at' => 'datetime',
    ];

    /**
     * Customer sở hữu giao dịch điểm.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Staff/Admin thực hiện điều chỉnh nếu có.
     */
    public function performer()
    {
        return $this->belongsTo(
            User::class,
            'performed_by'
        );
    }
/**
 * Redemption sử dụng Point Transaction này.
 */
public function rewardRedemption()
{
    return $this->hasOne(RewardRedemption::class);
}
/**
 * Green Return được thưởng bởi Point Transaction này.
 */
public function greenReturnRequest()
{
    return $this->hasOne(
        GreenReturnRequest::class,
        'point_transaction_id'
    );
}
/**
 * Challenge Participant được thưởng
 * bởi Point Transaction này.
 */
public function challengeParticipant()
{
    return $this->hasOne(
        ChallengeParticipant::class,
        'point_transaction_id'
    );
}

}