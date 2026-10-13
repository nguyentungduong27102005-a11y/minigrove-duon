<?php

namespace App\Http\Controllers;

use App\Models\CustomDesign;
use App\Models\Product;
use App\Services\StudioCompatibilityService;
use App\Services\StudioPricingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudioController extends Controller
{
    public function __construct(
        private StudioPricingService $pricingService,
        private StudioCompatibilityService $compatibilityService
    ) {
    }

    /**
     * Màn hình tự thiết kế Terrarium bằng Product.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->with([
                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->where('is_active', true)
            ->where('is_studio_item', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('product_type')
            ->orderBy('name')
            ->get();

        $containers = $products->where('product_type', 'container');
        $plants = $products->where('product_type', 'plant');
        $mosses = $products->where('product_type', 'moss');
        $substrates = $products->where('product_type', 'substrate');
        $stones = $products->where('product_type', 'stone');
        $accessories = $products->where('product_type', 'accessory');

        $editingDesign = null;
        $defaults = [];

        if ($request->filled('design')) {
            if (!auth()->check()) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Vui lòng đăng nhập để chỉnh sửa thiết kế.');
            }

            $editingDesign = CustomDesign::query()
                ->with('items.product')
                ->where('user_id', auth()->id())
                ->whereKey((int) $request->input('design'))
                ->firstOrFail();

            if ($editingDesign->orderDetails()->exists()) {
                return redirect()
                    ->route('designs.index')
                    ->with('error', 'Thiết kế đã phát sinh đơn hàng nên không thể chỉnh sửa.');
            }

            foreach ($editingDesign->items as $item) {
                $type = $item->product?->product_type;

                if ($type) {
                    $defaults[$type . '_id'] = $item->product_id;
                }
            }

            $defaults['style'] = $editingDesign->style;
        }

        /*
         * Cho phép trang Product truyền sẵn một lựa chọn vào Studio.
         *
         * Ví dụ:
         * /studio?plant=4
         * /studio?container=1
         *
         * Chỉ nhận Product đang hoạt động, còn hàng, thuộc Studio
         * và đúng product_type tương ứng.
         */
        if (!$editingDesign) {
            $queryTypeMap = [
                'container' => 'container',
                'plant' => 'plant',
                'moss' => 'moss',
                'substrate' => 'substrate',
                'stone' => 'stone',
                'accessory' => 'accessory',
            ];

            foreach ($queryTypeMap as $queryKey => $productType) {
                if (!$request->filled($queryKey)) {
                    continue;
                }

                $productId = (int) $request->input($queryKey);

                $selectedProduct = $products->first(
                    fn (Product $product) =>
                        $product->id === $productId
                        && $product->product_type === $productType
                );

                if ($selectedProduct) {
                    $defaults[$productType . '_id'] = $selectedProduct->id;
                }
            }
        }

        return view('studio.index', compact(
            'products',
            'containers',
            'plants',
            'mosses',
            'substrates',
            'stones',
            'accessories',
            'editingDesign',
            'defaults'
        ));
    }

    /**
     * Kiểm tra cấu hình, tính giá hiện hành và hiển thị Preview.
     */
    public function preview(Request $request)
    {
        $validated = $request->validate(
            [
                'design_id' => ['nullable', 'integer', 'exists:custom_designs,id'],
                'container_id' => ['required', 'integer', 'exists:products,id'],
                'plant_id' => ['nullable', 'integer', 'exists:products,id'],
                'moss_id' => ['nullable', 'integer', 'exists:products,id'],
                'substrate_id' => ['required', 'integer', 'exists:products,id'],
                'stone_id' => ['nullable', 'integer', 'exists:products,id'],
                'accessory_id' => ['nullable', 'integer', 'exists:products,id'],
                'style' => [
                    'required',
                    Rule::in([
                        'natural',
                        'forest',
                        'zen',
                        'fairy',
                        'tropical',
                        'minimal',
                    ]),
                ],
            ],
            [
                'container_id.required' => 'Bạn phải chọn bình.',
                'substrate_id.required' => 'Bạn phải chọn nền / giá thể.',
                'style.required' => 'Bạn phải chọn phong cách.',
                'style.in' => 'Phong cách không hợp lệ.',
            ]
        );

        if (empty($validated['plant_id']) && empty($validated['moss_id'])) {
            return back()
                ->withErrors([
                    'products' => 'Bạn phải chọn ít nhất một cây hoặc rêu.',
                ])
                ->withInput();
        }

        $selectedItems = [];

        foreach (
            [
                'container_id',
                'plant_id',
                'moss_id',
                'substrate_id',
                'stone_id',
                'accessory_id',
            ] as $field
        ) {
            if (!empty($validated[$field])) {
                $selectedItems[] = [
                    'product_id' => (int) $validated[$field],
                    'quantity' => 1,
                ];
            }
        }

        $compatibility = $this->compatibilityService->validate($selectedItems);
        $pricing = $this->pricingService->calculate($selectedItems);

        $editingDesign = null;

        if (!empty($validated['design_id'])) {
            abort_unless(auth()->check(), 403);

            $editingDesign = CustomDesign::query()
                ->where('user_id', auth()->id())
                ->whereKey((int) $validated['design_id'])
                ->firstOrFail();

            abort_if($editingDesign->orderDetails()->exists(), 403);
        }

        return view('studio.preview', [
            'style' => $validated['style'],
            'items' => $pricing['items'],
            'selectedItems' => $selectedItems,
            'estimatedPrice' => $pricing['estimated_price'],
            'compatibility' => $compatibility,
            'editingDesign' => $editingDesign,
        ]);
    }
}
