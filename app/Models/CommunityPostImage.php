<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityPostImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_post_id',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function post()
    {
        return $this->belongsTo(
            CommunityPost::class,
            'community_post_id'
        );
    }
}