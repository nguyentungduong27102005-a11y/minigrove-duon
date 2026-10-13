<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'started_at',
        'completed_at',
        'top_product_id',
        'top_match_score',
        'result_snapshot',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'top_match_score' => 'decimal:2',
        'result_snapshot' => 'array',
    ];

    /**
     * Customer thực hiện Quiz.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Các câu trả lời của Session.
     */
    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * Product được đề xuất cao nhất.
     */
    public function topProduct()
    {
        return $this->belongsTo(
            Product::class,
            'top_product_id'
        );
    }
}