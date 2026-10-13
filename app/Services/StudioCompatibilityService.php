<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCompatibilityRule;
use Illuminate\Validation\ValidationException;

class StudioCompatibilityService
{
    /**
     * Kiểm tra cấu hình MiniGrove Studio chỉ bằng Product.
     *
     * Mỗi phần tử:
     * [
     *     'product_id' => 1,
     *     'quantity' => 1,
     * ]
     */
    public function validate(array $selectedItems): array
    {
        $items = $this->normalizeItems($selectedItems);

        if (empty($items)) {
            throw ValidationException::withMessages([
                'products' => 'Thiết kế chưa có sản phẩm.',
            ]);
        }

        $productIds = array_column($items, 'product_id');

        if (count($productIds) !== count(array_unique($productIds))) {
            throw ValidationException::withMessages([
                'products' => 'Một sản phẩm không được chọn lặp lại trong cùng thiết kế.',
            ]);
        }

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        if ($products->count() !== count($productIds)) {
            throw ValidationException::withMessages([
                'products' => 'Có sản phẩm trong thiết kế không còn tồn tại.',
            ]);
        }

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            $quantity = $item['quantity'];

            if (!$product->is_studio_item || !$product->product_type) {
                throw ValidationException::withMessages([
                    'products' => "Sản phẩm \"{$product->name}\" không thuộc MiniGrove Studio.",
                ]);
            }

            if (!$product->is_active) {
                throw ValidationException::withMessages([
                    'products' => "Sản phẩm \"{$product->name}\" hiện không hoạt động.",
                ]);
            }

            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'products' => "Số lượng \"{$product->name}\" phải từ 1 trở lên.",
                ]);
            }

            if ($product->stock_quantity <= 0) {
                throw ValidationException::withMessages([
                    'products' => "Sản phẩm \"{$product->name}\" đã hết hàng.",
                ]);
            }

            if ($quantity > $product->stock_quantity) {
                throw ValidationException::withMessages([
                    'products' => "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock_quantity}.",
                ]);
            }
        }

        $allowedTypes = [
            'container',
            'plant',
            'moss',
            'substrate',
            'stone',
            'accessory',
        ];

        foreach ($products as $product) {
            if (!in_array($product->product_type, $allowedTypes, true)) {
                throw ValidationException::withMessages([
                    'products' => "Loại sản phẩm \"{$product->name}\" không hợp lệ cho Studio.",
                ]);
            }
        }

        $byType = $products->groupBy('product_type');

        if (($byType->get('container')?->count() ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'products' => 'Thiết kế phải có đúng một bình.',
            ]);
        }

        if (($byType->get('substrate')?->count() ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'products' => 'Thiết kế phải có đúng một loại nền.',
            ]);
        }

        if (
            ($byType->get('plant')?->count() ?? 0) < 1
            && ($byType->get('moss')?->count() ?? 0) < 1
        ) {
            throw ValidationException::withMessages([
                'products' => 'Thiết kế phải có ít nhất một cây hoặc rêu.',
            ]);
        }

        foreach (['plant', 'moss', 'stone', 'accessory'] as $type) {
            if (($byType->get($type)?->count() ?? 0) > 1) {
                throw ValidationException::withMessages([
                    'products' => 'Mỗi nhóm sản phẩm trong Studio chỉ được chọn tối đa một mẫu.',
                ]);
            }
        }

        /** @var Product $container */
        $container = $byType->get('container')->first();

        if (!in_array($container->ecosystem_type, ['closed', 'open', 'dry'], true)) {
            throw ValidationException::withMessages([
                'products' => 'Bình chưa được cấu hình môi trường Terrarium hợp lệ.',
            ]);
        }

        if (!$container->studio_capacity || $container->studio_capacity < 1) {
            throw ValidationException::withMessages([
                'products' => 'Bình chưa được cấu hình sức chứa kỹ thuật.',
            ]);
        }

        /*
         * Môi trường của cây/rêu/nền phải phù hợp với bình.
         * Product dùng chung có ecosystem_type = all.
         */
        foreach ($products as $product) {
            if ($product->id === $container->id) {
                continue;
            }

            if (
                $product->ecosystem_type !== 'all'
                && $product->ecosystem_type !== $container->ecosystem_type
            ) {
                $label = $product->product_type === 'plant'
                    ? 'Cây'
                    : 'Sản phẩm';

                throw ValidationException::withMessages([
                    'products' => "{$label} \"{$product->name}\" không phù hợp với môi trường của bình \"{$container->name}\".",
                ]);
            }
        }

        /*
         * Quy tắc tương thích cụ thể giữa hai Product.
         */
        $checkedRules = ProductCompatibilityRule::query()
            ->where('is_active', true)
            ->whereIn('source_product_id', $productIds)
            ->whereIn('target_product_id', $productIds)
            ->count();

        $rule = ProductCompatibilityRule::query()
            ->where('is_active', true)
            ->where('is_compatible', false)
            ->whereIn('source_product_id', $productIds)
            ->whereIn('target_product_id', $productIds)
            ->with(['sourceProduct', 'targetProduct'])
            ->first();

        if ($rule) {
            $message =
                "Sản phẩm \"{$rule->sourceProduct?->name}\" không tương thích với \"{$rule->targetProduct?->name}\".";

            if ($rule->reason) {
                $message .= ' ' . $rule->reason;
            }

            throw ValidationException::withMessages([
                'products' => $message,
            ]);
        }

        /*
         * Sức chứa tính trên tổng số Product đặt bên trong bình,
         * không tính chính chiếc bình.
         */
        $selectedQuantity = 0;

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);

            if ($product->product_type !== 'container') {
                $selectedQuantity += $item['quantity'];
            }
        }

        if ($selectedQuantity > $container->studio_capacity) {
            throw ValidationException::withMessages([
                'products' =>
                    "Cấu hình có {$selectedQuantity} sản phẩm, vượt sức chứa tối đa {$container->studio_capacity} của bình \"{$container->name}\".",
            ]);
        }

        return [
            'compatible' => true,
            'ecosystem_type' => $container->ecosystem_type,
            'container' => [
                'id' => $container->id,
                'name' => $container->name,
                'capacity' => (int) $container->studio_capacity,
            ],
            'checked_rules' => $checkedRules,
            'selected_quantity' => $selectedQuantity,
        ];
    }

    private function normalizeItems(array $selectedItems): array
    {
        $items = [];

        foreach ($selectedItems as $item) {
            if (is_scalar($item)) {
                $productId = (int) $item;
                $quantity = 1;
            } else {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 1);
            }

            if ($productId <= 0) {
                throw ValidationException::withMessages([
                    'products' => 'Có sản phẩm không hợp lệ trong thiết kế.',
                ]);
            }

            $items[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        return $items;
    }
}
