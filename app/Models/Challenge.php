<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'rules',
        'cover_image',
        'start_at',
        'end_at',
        'reward_points',
        'status',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'reward_points' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Staff/Admin tạo Challenge.
     */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Danh sách người tham gia Challenge.
     */
    public function participants()
    {
        return $this->hasMany(ChallengeParticipant::class);
    }
}