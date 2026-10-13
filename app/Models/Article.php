<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'topic',
        'excerpt',
        'content',
        'cover_image',
        'status',
        'author_id',
        'published_at',
        'hidden_by',
        'hidden_at',
        'moderation_note',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'hidden_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    /**
     * Staff/Admin viết bài.
     */
    public function author()
    {
        return $this->belongsTo(
            User::class,
            'author_id'
        );
    }

    /**
     * Staff/Admin ẩn bài.
     */
    public function hiddenByUser()
    {
        return $this->belongsTo(
            User::class,
            'hidden_by'
        );
    }
}