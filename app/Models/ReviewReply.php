<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'content',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Staff/Admin thực hiện phản hồi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}