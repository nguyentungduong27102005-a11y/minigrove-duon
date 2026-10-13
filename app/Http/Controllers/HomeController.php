<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;

class HomeController extends Controller
{
    public function index()
    {
        /*
         * Danh mục đang Active.
         */
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount([
                'products as active_products_count' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->where('is_studio_item', false);
                },
            ])
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();


        /*
         * Sản phẩm nổi bật.
         */
        $featuredProducts = Product::query()
            ->with([
                'category',
                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->where('is_active', true)
            ->where('is_studio_item', false)
            ->where('stock_quantity', '>', 0)
            ->where('is_featured', true)
            ->latest('id')
            ->take(8)
            ->get();


        /*
         * Sản phẩm mới.
         */
        $newProducts = Product::query()
            ->with([
                'category',
                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->where('is_active', true)
            ->where('is_studio_item', false)
            ->where('stock_quantity', '>', 0)
            ->latest('id')
            ->take(8)
            ->get();

        /*
         * Trạng thái Wishlist để card ở Home hiển thị đúng
         * tim đỏ cho các sản phẩm Customer đã lưu.
         */
        $wishlistProductIds = [];

        if (
            auth()->check()
            && auth()->user()->role?->name === 'customer'
        ) {
            $wishlist = Wishlist::query()
                ->where('user_id', auth()->id())
                ->first();

            if ($wishlist) {
                $wishlistProductIds = $wishlist
                    ->items()
                    ->pluck('product_id')
                    ->map(fn ($productId) => (int) $productId)
                    ->all();
            }
        }


        return view(
            'home',
            compact(
                'categories',
                'featuredProducts',
                'newProducts',
                'wishlistProductIds'
            )
        );
    }
}