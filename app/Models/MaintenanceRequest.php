<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'grove_id',
        'service_type',
        'requested_at',
        'note',
        'image_paths',
        'status',
        'handled_by',
        'quoted_price',
        'quote_note',
        'confirmed_at',
        'processing_at',
        'completed_at',
        'cancelled_at',
        'staff_note',
    ];

    protected $casts = [
        'image_paths' => 'array',

        'quoted_price' => 'decimal:2',

        'requested_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'processing_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Grove cần bảo dưỡng.
     */
    public function grove()
    {
        return $this->belongsTo(Grove::class);
    }

    /**
     * Staff/Admin xử lý.
     */
    public function handler()
    {
        return $this->belongsTo(
            User::class,
            'handled_by'
        );
    }
}