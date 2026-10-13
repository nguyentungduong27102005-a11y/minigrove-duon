<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(CommunityPostImage::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function hiddenByUser()
    {
        return $this->belongsTo(
            User::class,
            'hidden_by'
        );
    }
}