<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Danh sách Review dành cho Admin.
     *
     * Privacy:
     * - Chỉ hiển thị Customer #ID.
     * - Không hiển thị thông tin PII của Customer.
     */
    public function index(Request $request): View
    {
        $query = Review::query()
            ->with([
                'product:id,name',
                'images:id,review_id,image_path,sort_order',
                'replies',
            ])
            ->withCount('replies')
            ->latest();

        // Lọc theo rating.
        if ($request->filled('rating')) {
            $query->where(
                'rating',
                (int) $request->input('rating')
            );
        }

        // Tìm theo nội dung Review hoặc tên sản phẩm.
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where(
                    'comment',
                    'like',
                    "%{$search}%"
                )->orWhereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                });
            });
        }

        $reviews = $query
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.reviews.index',
            compact('reviews')
        );
    }

    /**
     * Admin phản hồi Review.
     */
    public function reply(
        Request $request,
        Review $review
    ): RedirectResponse {
        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $review->replies()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'is_visible' => true,
        ]);

        return back()->with(
            'success',
            'Đã gửi phản hồi cho đánh giá.'
        );
    }

}
