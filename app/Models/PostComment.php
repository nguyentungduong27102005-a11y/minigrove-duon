<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_post_id',
        'user_id',
        'content',
        'is_visible',
        'hidden_by',
        'hidden_at',
        'moderation_note',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'hidden_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(
            CommunityPost::class,
            'community_post_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hiddenByUser()
    {
        return $this->belongsTo(
            User::class,
            'hidden_by'
        );
    }
}