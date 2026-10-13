<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroveQrToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'grove_id',
        'token',
        'status',
        'qr_code_path',
        'generated_by',
        'activated_at',
        'revoked_at',
        'revoke_reason',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * QR Token thuộc một Grove.
     */
    public function grove()
    {
        return $this->belongsTo(Grove::class);
    }

    /**
     * Staff/Admin sinh QR.
     */
    public function generator()
    {
        return $this->belongsTo(
            User::class,
            'generated_by'
        );
    }
}