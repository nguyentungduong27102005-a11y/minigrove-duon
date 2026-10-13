<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcoRescueRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'grove_id',
        'issue_type',
        'description',
        'support_need',
        'image_paths',
        'status',
        'handled_by',
        'staff_response',
        'processing_at',
        'resolved_at',
    ];

    protected $casts = [
        'image_paths' => 'array',
        'processing_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Grove cần hỗ trợ.
     */
    public function grove()
    {
        return $this->belongsTo(Grove::class);
    }

    /**
     * Staff/Admin xử lý yêu cầu.
     */
    public function handler()
    {
        return $this->belongsTo(
            User::class,
            'handled_by'
        );
    }
}