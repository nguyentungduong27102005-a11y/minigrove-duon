<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrowthJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'grove_id',
        'user_id',
        'recorded_at',
        'image_path',
        'description',
        'note',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function grove()
    {
        return $this->belongsTo(Grove::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}