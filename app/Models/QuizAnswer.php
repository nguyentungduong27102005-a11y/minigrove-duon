<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_session_id',
        'criterion',
        'answer_value',
        'answer_label',
    ];

    protected $casts = [
        'answer_value' => 'array',
    ];

    /**
     * Quiz Session chứa câu trả lời.
     */
    public function quizSession()
    {
        return $this->belongsTo(QuizSession::class);
    }
}