<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'grove_id',
        'care_rule_id',
        'task_type',
        'scheduled_at',
        'status',
        'note',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function grove()
    {
        return $this->belongsTo(Grove::class);
    }

    public function careRule()
    {
        return $this->belongsTo(CareRule::class);
    }

    public function logs()
    {
        return $this->hasMany(CareLog::class);
    }
}