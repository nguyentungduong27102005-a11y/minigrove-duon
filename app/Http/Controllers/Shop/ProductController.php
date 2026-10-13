<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim(
            (string) $request->get('keyword', '')
        );

        $category = $request->get('category');

        $ecosystem = $request->get('ecosystem');

        $care = $request->get('care');

        $sort = $request->get(
            'sort',
            'newest'
        );

        $type = trim(
            (string) $request->get('type', '')
        );

        /*
        |--------------------------------------------------------------------------
        | STUDIO PRODUCT TYPE
        |--------------------------------------------------------------------------
        */

        $studioTypeLabels = [
            'container' => 'Bình',
            'plant' => 'Cây',
            'moss' => 'Rêu',
            'substrate' => 'Nền / giá thể',
            'stone' => 'Đá',
            'accessory' => 'Phụ kiện',
        ];

        $isStudioType = array_key_exists(
            $type,
            $studioTypeLabels
        );

        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PRODUCT QUERY
        |--------------------------------------------------------------------------
        */

        $query = Product::query()
            ->with([
                'category',

                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->where('is_active', true);

        /*
        |--------------------------------------------------------------------------
        | STUDIO / NORMAL PRODUCT
        |--------------------------------------------------------------------------
        */

        if ($isStudioType) {
            $query
                ->where(
                    'is_studio_item',
                    true
                )
                ->where(
                    'product_type',
                    $type
                );
        } else {
            $query->where(
                'is_studio_item',
                false
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($keyword !== '') {
            $query->where(
                function ($subQuery) use ($keyword) {
                    $subQuery
                        ->where(
                            'name',
                            'like',
                            '%' . $keyword . '%'
                        )
                        ->orWhere(
                            'sku',
                            'like',
                            '%' . $keyword . '%'
                        )
                        ->orWhere(
                            'description',
                            'like',
                            '%' . $keyword . '%'
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GIFT CATEGORY FILTER
        |--------------------------------------------------------------------------
        |
        | Sản phẩm thường được lọc theo bảng category_product.
        |
        | Ví dụ:
        | Dream Forest:
        | - Quà tặng Ba Mẹ
        | - Quà tặng Sinh Nhật
        |
        | Chân Mây:
        | - Quà tặng Tết
        | - Quà tặng Sinh Nhật
        |
        */

        if (
            $category
            && !$isStudioType
        ) {
            $query->whereHas(
                'giftCategories',
                function ($subQuery) use ($category) {
                    $subQuery->where(
                        'slug',
                        $category
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ECOSYSTEM FILTER
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $ecosystem,
                [
                    'closed',
                    'open',
                    'dry',
                ],
                true
            )
        ) {
            $query->where(
                'ecosystem_type',
                $ecosystem
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CARE LEVEL FILTER
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $care,
                [
                    'easy',
                    'medium',
                    'high',
                ],
                true
            )
        ) {
            $query->where(
                'care_level',
                $care
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */

        switch ($sort) {
            case 'price_asc':

                $query->orderByRaw(
                    '
                    CASE
                        WHEN sale_price IS NOT NULL
                        THEN sale_price
                        ELSE list_price
                    END ASC
                    '
                );

                break;

            case 'price_desc':

                $query->orderByRaw(
                    '
                    CASE
                        WHEN sale_price IS NOT NULL
                        THEN sale_price
                        ELSE list_price
                    END DESC
                    '
                );

                break;

            case 'featured':

                $query
                    ->orderByDesc('is_featured')
                    ->orderByDesc('id');

                break;

            default:

                $query->orderByDesc('id');

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->paginate(12)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | WISHLIST
        |--------------------------------------------------------------------------
        */

        $wishlistProductIds = [];

        if (
            auth()->check()
            && auth()->user()->role?->name === 'customer'
        ) {
            $wishlist = auth()
                ->user()
                ->wishlist()
                ->first();

            if ($wishlist) {
                $wishlistProductIds = $wishlist
                    ->items()
                    ->pluck('product_id')
                    ->toArray();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'products.index',
            compact(
                'products',
                'categories',
                'keyword',
                'category',
                'ecosystem',
                'care',
                'sort',
                'type',
                'studioTypeLabels',
                'isStudioType',
                'wishlistProductIds'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT DETAIL
    |--------------------------------------------------------------------------
    */

    public function show(Product $product)
    {
        abort_unless(
            $product->is_active,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | LOAD PRODUCT DATA
        |--------------------------------------------------------------------------
        */

        $product->load([
            'category',

            'images' => function ($query) {
                $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },

            'reviews' => function ($query) {
                $query
                    ->where(
                        'is_visible',
                        true
                    )
                    ->with([
                        'user',

                        'images' => function ($imageQuery) {
                            $imageQuery
                                ->orderBy('sort_order')
                                ->orderBy('id');
                        },

                        'replies' => function ($replyQuery) {
                            $replyQuery
                                ->where(
                                    'is_visible',
                                    true
                                )
                                ->with('user')
                                ->latest('id');
                        },
                    ])
                    ->latest('id');
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | REVIEW SUMMARY
        |--------------------------------------------------------------------------
        */

        $averageRating =
            $product->reviews->isNotEmpty()
                ? round(
                    (float) $product
                        ->reviews
                        ->avg('rating'),
                    1
                )
                : 0;

        $reviewCount =
            $product->reviews->count();

        /*
        |--------------------------------------------------------------------------
        | WISHLIST STATUS
        |--------------------------------------------------------------------------
        */

        $isWishlisted = false;

        if (
            auth()->check()
            && auth()->user()->role?->name === 'customer'
        ) {
            $wishlist = auth()
                ->user()
                ->wishlist()
                ->first();

            if ($wishlist) {
                $isWishlisted = $wishlist
                    ->items()
                    ->where(
                        'product_id',
                        $product->id
                    )
                    ->exists();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RELATED PRODUCTS
        |--------------------------------------------------------------------------
        |
        | Tạm thời vẫn giữ category_id để không ảnh hưởng
        | logic sản phẩm liên quan hiện tại.
        |
        */

        $relatedProducts = Product::query()
            ->with([
                'category',

                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->where(
                'is_active',
                true
            )
            ->where(
                'category_id',
                $product->category_id
            )
            ->where(
                'id',
                '!=',
                $product->id
            )
            ->when(
                $product->is_studio_item,

                fn ($query) => $query
                    ->where(
                        'is_studio_item',
                        true
                    )
                    ->where(
                        'product_type',
                        $product->product_type
                    ),

                fn ($query) => $query
                    ->where(
                        'is_studio_item',
                        false
                    )
            )
            ->take(4)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'products.show',
            compact(
                'product',
                'relatedProducts',
                'averageRating',
                'reviewCount',
                'isWishlisted'
            )
        );
    }
}