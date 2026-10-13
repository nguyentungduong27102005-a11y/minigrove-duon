<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'assigned_admin_id',
        'topic',
        'subject',
        'status',
        'last_message_at',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Customer tạo Conversation.
     */
    public function customer()
    {
        return $this->belongsTo(
            User::class,
            'customer_id'
        );
    }

    /**
     * Admin được phân công tư vấn.
     */
    public function assignedAdmin()
    {
        return $this->belongsTo(
            User::class,
            'assigned_admin_id'
        );
    }

    /**
     * Các Message trong Conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * User đóng Conversation.
     */
    public function closedByUser()
    {
        return $this->belongsTo(
            User::class,
            'closed_by'
        );
    }
}