<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grove extends Model
{
    use HasFactory;

    protected $fillable = [
        'grove_code',
        'user_id',
        'order_detail_id',
        'terrarium_name',
        'cover_image',
        'ecosystem_type',
        'plant_snapshot',
        'moss_snapshot',
        'substrate_snapshot',
        'stone_snapshot',
        'accessory_snapshot',
        'care_level',
        'light_requirement',
        'care_cycle_days',
        'current_condition',
        'status',
        'received_at',
        'activated_at',
    ];

    protected $casts = [
        'plant_snapshot' => 'array',
        'moss_snapshot' => 'array',
        'substrate_snapshot' => 'array',
        'stone_snapshot' => 'array',
        'accessory_snapshot' => 'array',

        'care_cycle_days' => 'integer',

        'received_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    /**
     * Customer sở hữu Grove.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * OrderDetail tạo ra Grove này.
     */
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }
    /**
 * Lịch sử QR Token của Grove.
 */
public function qrTokens()
{
    return $this->hasMany(GroveQrToken::class);
}
/**
 * Các lịch chăm sóc của Grove.
 */
public function careSchedules()
{
    return $this->hasMany(CareSchedule::class);
}

/**
 * Lịch sử chăm sóc của Grove.
 */
public function careLogs()
{
    return $this->hasMany(CareLog::class);
}
/**
 * Nhật ký phát triển của Grove.
 */
public function growthJournals()
{
    return $this->hasMany(GrowthJournal::class);
}
/**
 * Các yêu cầu Eco Rescue của Grove.
 */
public function ecoRescueRequests()
{
    return $this->hasMany(EcoRescueRequest::class);
}
/**
 * Các yêu cầu bảo dưỡng của Grove.
 */
public function maintenanceRequests()
{
    return $this->hasMany(MaintenanceRequest::class);
}
/**
 * Các Challenge mà Grove này được sử dụng để tham gia.
 */
public function challengeParticipations()
{
    return $this->hasMany(ChallengeParticipant::class);
}

}