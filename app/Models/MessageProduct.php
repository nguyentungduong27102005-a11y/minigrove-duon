<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'product_id',
        'product_name',
        'product_sku',
        'list_price',
        'sale_price',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'list_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}