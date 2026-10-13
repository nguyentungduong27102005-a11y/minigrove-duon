<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'reference_type',
        'reference_id',
        'action_url',
        'data',
        'read_at',
    ];

    protected $casts = [
        'reference_id' => 'integer',
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * User nhận thông báo.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}