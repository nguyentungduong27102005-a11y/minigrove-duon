<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receiver_name',
        'receiver_phone',
        'province',
        'district',
        'ward',
        'address_detail',
        'label',
        'is_default',

        'ghn_district_id',
        'ghn_ward_code',
    ];

    protected $casts = [
    'is_default' => 'boolean',
    'ghn_district_id' => 'integer',
];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}