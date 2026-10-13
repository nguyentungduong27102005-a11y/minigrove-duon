<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'attachment_type',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Message chứa Attachment.
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}