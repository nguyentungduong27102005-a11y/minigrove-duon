<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductCompatibilityRule extends Model
{
    use HasFactory;


    protected $table = 'product_compatibility_rules';



    protected $fillable = [

        'source_product_id',

        'target_product_id',

        'is_compatible',

        'reason',

        'is_active',

    ];



    protected $casts = [

        'is_compatible' => 'boolean',

        'is_active' => 'boolean',

    ];



    /*
    |--------------------------------------------------------------------------
    | Sản phẩm nguồn
    |--------------------------------------------------------------------------
    */

    public function sourceProduct()
    {
        return $this->belongsTo(
            Product::class,
            'source_product_id'
        );
    }




    /*
    |--------------------------------------------------------------------------
    | Sản phẩm đích
    |--------------------------------------------------------------------------
    */

    public function targetProduct()
    {
        return $this->belongsTo(
            Product::class,
            'target_product_id'
        );
    }




    /*
    |--------------------------------------------------------------------------
    | Scope: chỉ lấy rule đang hoạt động
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where(
            'is_active',
            true
        );
    }

}