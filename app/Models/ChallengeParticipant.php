<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'user_id',
        'grove_id',
        'status',
        'joined_at',
        'completed_at',
        'point_transaction_id',
        'note',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Challenge được tham gia.
     */
    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    /**
     * Customer tham gia.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Grove được sử dụng cho Challenge.
     */
    public function grove()
    {
        return $this->belongsTo(Grove::class);
    }

    /**
     * Giao dịch Grove Point nếu được thưởng.
     */
    public function pointTransaction()
    {
        return $this->belongsTo(PointTransaction::class);
    }
}