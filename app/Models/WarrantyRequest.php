<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_id',
        'issue_type',
        'description',
        'image_paths',
        'status',
        'handled_by',
        'staff_response',
        'rejection_reason',
        'processing_at',
        'resolved_at',
    ];

    protected $casts = [
        'image_paths' => 'array',
        'processing_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function handler()
    {
        return $this->belongsTo(
            User::class,
            'handled_by'
        );
    }
}