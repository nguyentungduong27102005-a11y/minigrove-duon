<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Customer tạo Review cho Product đã mua và đã hoàn thành.
     */
    public function store(Request $request, Product $product)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'order_detail_id' => [
                'required',
                'integer',
                'exists:order_details,id',
            ],
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'comment' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'images' => [
                'nullable',
                'array',
                'max:5',
            ],
            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $orderDetail = OrderDetail::query()
            ->whereKey($validated['order_detail_id'])
            ->where('product_id', $product->id)
            ->whereHas('order', function ($query) use ($user) {
                $query
                    ->where('user_id', $user->id)
                    ->where('order_status', 'completed');
            })
            ->first();

        if (!$orderDetail) {
            throw ValidationException::withMessages([
                'review' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua và đã hoàn thành.',
            ]);
        }

        $existingReview = Review::query()
            ->where('user_id', $user->id)
            ->where('order_detail_id', $orderDetail->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            throw ValidationException::withMessages([
                'review' => 'Bạn đã đánh giá sản phẩm này.',
            ]);
        }

        DB::transaction(function () use (
            $request,
            $user,
            $orderDetail,
            $product,
            $validated
        ) {
            $review = Review::create([
                'user_id' => $user->id,
                'order_detail_id' => $orderDetail->id,
                'review_target' => 'PRODUCT',
                'product_id' => $product->id,
                'custom_design_id' => null,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'edit_count' => 0,
                'is_visible' => true,
            ]);

            foreach ($request->file('images', []) as $index => $image) {
                $path = $image->store(
                    'reviews',
                    'public'
                );

                ReviewImage::create([
                    'review_id' => $review->id,
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        });

        return back()->with(
            'success',
            'Đánh giá sản phẩm thành công.'
        );
    }

    /**
     * Customer sửa Review của chính mình.
     *
     * Dùng ID cho tham số review để tránh lỗi
     * implicit route model binding hiện tại của project.
     */
    public function update(
        Request $request,
        Product $product,
        $reviewId
    ) {
        $user = Auth::user();

        $validated = $request->validate([
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'comment' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'images' => [
                'nullable',
                'array',
                'max:5',
            ],
            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $review = Review::query()
            ->whereKey($reviewId)
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->whereHas('orderDetail.order', function ($query) use ($user) {
                $query
                    ->where('user_id', $user->id)
                    ->where('order_status', 'completed');
            })
            ->firstOrFail();

        DB::transaction(function () use (
            $request,
            $review,
            $validated
        ) {
            $review->update([
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'edit_count' => ((int) $review->edit_count) + 1,
            ]);

            $currentCount = $review->images()->count();

            foreach ($request->file('images', []) as $image) {
                if ($currentCount >= 5) {
                    break;
                }

                $path = $image->store(
                    'reviews',
                    'public'
                );

                ReviewImage::create([
                    'review_id' => $review->id,
                    'image_path' => $path,
                    'sort_order' => $currentCount,
                ]);

                $currentCount++;
            }
        });

        return back()->with(
            'success',
            'Đánh giá sản phẩm đã được cập nhật.'
        );
    }

    /**
     * Customer xóa Review của chính mình.
     */
    public function destroy(
        Request $request,
        Product $product,
        $reviewId
    ) {
        $user = Auth::user();

        $review = Review::query()
            ->whereKey($reviewId)
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->whereHas('orderDetail.order', function ($query) use ($user) {
                $query
                    ->where('user_id', $user->id)
                    ->where('order_status', 'completed');
            })
            ->with('images')
            ->firstOrFail();

        DB::transaction(function () use ($review) {
            foreach ($review->images as $image) {
                if ($image->image_path) {
                    Storage::disk('public')->delete(
                        $image->image_path
                    );
                }

                $image->delete();
            }

            $review->delete();
        });

        return back()->with(
            'success',
            'Đã xóa đánh giá sản phẩm.'
        );
    }
}
