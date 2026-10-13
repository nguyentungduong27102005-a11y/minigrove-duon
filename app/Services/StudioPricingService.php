<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Validation\ValidationException;

class StudioPricingService
{
    /**
     * Tính giá hiện hành cho cấu hình Studio từ bảng products.
     */
    public function calculate(array $selectedItems): array
    {
        if (empty($selectedItems)) {
            throw ValidationException::withMessages([
                'products' => 'Thiết kế phải có ít nhất một sản phẩm.',
            ]);
        }

        $resultItems = [];
        $estimatedPrice = 0;
        $usedProductIds = [];

        foreach ($selectedItems as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $quantity < 1) {
                throw ValidationException::withMessages([
                    'products' => 'Sản phẩm hoặc số lượng trong thiết kế không hợp lệ.',
                ]);
            }

            $product = Product::find($productId);

            if (!$product || !$product->is_studio_item || !$product->product_type) {
                throw ValidationException::withMessages([
                    'products' => 'Có sản phẩm không thuộc MiniGrove Studio.',
                ]);
            }

            if (!$product->is_active) {
                throw ValidationException::withMessages([
                    'products' => "Sản phẩm \"{$product->name}\" hiện không hoạt động.",
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

            if (in_array($product->id, $usedProductIds, true)) {
                throw ValidationException::withMessages([
                    'products' => "Sản phẩm \"{$product->name}\" bị chọn trùng.",
                ]);
            }

            $usedProductIds[] = $product->id;

            $unitPrice = (float) ($product->sale_price ?? $product->list_price);
            $subtotal = $unitPrice * $quantity;
            $estimatedPrice += $subtotal;

            $resultItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_type' => $product->product_type,
                'ecosystem_type' => $product->ecosystem_type,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'sku' => $product->sku,
                'weight' => (int) ($product->weight ?? 0),
            ];
        }

        return [
            'items' => $resultItems,
            'estimated_price' => $estimatedPrice,
        ];
    }
}
