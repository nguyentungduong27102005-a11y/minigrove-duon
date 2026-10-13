<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_detail_id',
        'review_target',
        'product_id',
        'custom_design_id',
        'rating',
        'comment',
        'edit_count',
        'is_visible',
    ];

    protected $casts = [
        'rating' => 'integer',
        'edit_count' => 'integer',
        'is_visible' => 'boolean',
    ];

    /**
     * Customer viết Review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * OrderDetail dùng để xác minh đã mua hàng.
     */
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    /**
     * Product được đánh giá nếu target = PRODUCT.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Custom Design được đánh giá
     * nếu target = CUSTOM_DESIGN.
     */
    public function customDesign()
    {
        return $this->belongsTo(CustomDesign::class);
    }

    /**
     * Review có nhiều hình ảnh.
     */
    public function images()
    {
        return $this->hasMany(ReviewImage::class);
    }

    /**
     * Review có thể có nhiều phản hồi.
     */
    public function replies()
    {
        return $this->hasMany(ReviewReply::class);
    }
}