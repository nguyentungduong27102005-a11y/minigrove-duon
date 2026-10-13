<?php

namespace App\Services;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        private GHNService $ghnService,
        private CustomDesignService $customDesignService
    ) {
    }

    public function getCheckoutData(User $user): array
    {
        $mode = session('checkout.mode');
        $checkoutItems = collect();
        $subtotal = 0;

        if ($mode === 'cart') {
            $ids = session('checkout.cart_item_ids', []);

            if (empty($ids)) {
                throw ValidationException::withMessages([
                    'checkout' => 'Vui lòng chọn sản phẩm cần thanh toán.',
                ]);
            }

            $items = CartItem::query()
                ->whereIn('id', $ids)
                ->whereHas('cart', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with([
                    'product.category',
                    'product.images' => function ($query) {
                        $query
                            ->orderByDesc('is_primary')
                            ->orderBy('sort_order')
                            ->orderBy('id');
                    },
                    'customDesign.items.product.images',
                ])
                ->get();

            if (
                $items->count()
                !== count(array_unique($ids))
            ) {
                throw ValidationException::withMessages([
                    'checkout' => 'Giỏ hàng đã thay đổi. Vui lòng kiểm tra lại.',
                ]);
            }

            foreach ($items as $item) {
                if ($item->item_type === 'PRODUCT') {
                    $product = $item->product;

                    if (!$product || !$product->is_active) {
                        throw ValidationException::withMessages([
                            'checkout' => 'Có sản phẩm không còn được bán.',
                        ]);
                    }

                    if (
                        $item->quantity < 1
                        || $item->quantity > $product->stock_quantity
                    ) {
                        throw ValidationException::withMessages([
                            'checkout' =>
                                'Số lượng sản phẩm không còn phù hợp với tồn kho.',
                        ]);
                    }

                    $unitPrice = $product->sale_price !== null
                        ? (float) $product->sale_price
                        : (float) $product->list_price;

                    $lineTotal = $unitPrice * $item->quantity;

                    $checkoutItems->push((object) [
                        'item_type' => 'PRODUCT',
                        'cart_item_id' => $item->id,
                        'product' => $product,
                        'custom_design' => null,
                        'quantity' => $item->quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $lineTotal,
                        'weight' =>
                            (int) ($product->weight ?? 500)
                            * $item->quantity,
                    ]);
                } elseif (
                    $item->item_type === 'CUSTOM_DESIGN'
                    && $item->customDesign
                ) {
                    if ($item->customDesign->user_id !== $user->id) {
                        throw ValidationException::withMessages([
                            'checkout' => 'Thiết kế không thuộc tài khoản hiện tại.',
                        ]);
                    }

                    $recalculated = $this->customDesignService
                        ->recalculate($item->customDesign);

                    $unitPrice =
                        (float) $recalculated['pricing']['estimated_price'];

                    $weight = collect($recalculated['pricing']['items'])
                        ->sum(fn ($designItem) =>
                            (int) ($designItem['weight'] ?? 0)
                            * (int) $designItem['quantity']
                        );

                    $checkoutItems->push((object) [
                        'item_type' => 'CUSTOM_DESIGN',
                        'cart_item_id' => $item->id,
                        'product' => null,
                        'custom_design' => $item->customDesign,
                        'quantity' => 1,
                        'unit_price' => $unitPrice,
                        'subtotal' => $unitPrice,
                        'weight' => max(1, (int) $weight),
                        'pricing' => $recalculated['pricing'],
                    ]);

                    $lineTotal = $unitPrice;
                } else {
                    throw ValidationException::withMessages([
                        'checkout' => 'Có mục trong giỏ hàng không hợp lệ.',
                    ]);
                }

                $subtotal += $lineTotal;
            }
        } elseif ($mode === 'buy_now') {
            $productId = session('checkout.product_id');
            $quantity = (int) session('checkout.quantity', 1);

            $product = Product::query()
                ->with([
                    'category',
                    'images' => function ($query) {
                        $query
                            ->orderByDesc('is_primary')
                            ->orderBy('sort_order')
                            ->orderBy('id');
                    },
                ])
                ->find($productId);

            if (
                !$product
                || !$product->is_active
                || $product->is_studio_item
            ) {
                throw ValidationException::withMessages([
                    'checkout' => 'Sản phẩm hiện không còn được bán.',
                ]);
            }

            if (
                $quantity < 1
                || $quantity > $product->stock_quantity
            ) {
                throw ValidationException::withMessages([
                    'checkout' => 'Số lượng mua không còn phù hợp với tồn kho.',
                ]);
            }

            $unitPrice = $product->sale_price !== null
                ? (float) $product->sale_price
                : (float) $product->list_price;

            $lineTotal = $unitPrice * $quantity;

            $checkoutItems->push((object) [
                'item_type' => 'PRODUCT',
                'cart_item_id' => null,
                'product' => $product,
                'custom_design' => null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $lineTotal,
                'weight' =>
                    (int) ($product->weight ?? 500)
                    * $quantity,
            ]);

            $subtotal = $lineTotal;
        } else {
            throw ValidationException::withMessages([
                'checkout' => 'Chưa có sản phẩm để thanh toán.',
            ]);
        }

        return [
            'mode' => $mode,
            'checkoutItems' => $checkoutItems,
            'subtotal' => $subtotal,
        ];
    }

    public function calculateShippingFee(
        ?Address $address,
        $checkoutItems
    ): float {
        if (!$address || $checkoutItems->isEmpty()) {
            return 0;
        }

        $weight = 0;

        foreach ($checkoutItems as $item) {
            $weight += (int) ($item->weight ?? 500);
        }

        $weight = max(1, $weight);

        $data = [
            'service_type_id' => 2,
            'from_district_id' => 3440,
            'from_ward_code' => '13005',
            'to_district_id' => (int) $address->ghn_district_id,
            'to_ward_code' => $address->ghn_ward_code,
            'weight' => $weight,
        ];

        $result = $this->ghnService->calculateFee($data);

        return (float) ($result['total'] ?? 0);
    }
}
