<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'grove_id',
        'care_schedule_id',
        'care_type',
        'performed_at',
        'note',
        'performed_by',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function grove()
    {
        return $this->belongsTo(Grove::class);
    }

    public function careSchedule()
    {
        return $this->belongsTo(CareSchedule::class);
    }

    public function performer()
    {
        return $this->belongsTo(
            User::class,
            'performed_by'
        );
    }
}