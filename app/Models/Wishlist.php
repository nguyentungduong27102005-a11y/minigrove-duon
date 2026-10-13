<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', //khóa ngoại
    ];

    /**
     * Một Wishlist thuộc về một User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Một Wishlist có nhiều WishlistItem.
     */
    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }
}