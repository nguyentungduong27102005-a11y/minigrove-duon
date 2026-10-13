<?php

namespace App\Services;

use App\Models\CustomDesign;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomDesignService
{
    public function __construct(
        private StudioPricingService $pricingService,
        private StudioCompatibilityService $compatibilityService
    ) {
    }

    /**
     * Lưu thiết kế cùng Snapshot tại thời điểm lưu.
     */
    public function create(
        User $user,
        string $name,
        string $style,
        array $selectedItems
    ): CustomDesign {
        $payload = $this->preparePayload(
            $name,
            $style,
            $selectedItems
        );

        return DB::transaction(function () use ($user, $payload) {
            $design = CustomDesign::create([
                'user_id' => $user->id,
                'design_code' => $this->generateDesignCode(),
                'name' => $payload['name'],
                'style' => $payload['style'],
                'estimated_price' => $payload['pricing']['estimated_price'],
                'configuration_snapshot' => $payload['snapshot'],
                'status' => 'saved',
            ]);

            $this->replaceItems($design, $payload['pricing']['items']);

            return $design->load('items.product');
        });
    }

    /**
     * Cập nhật thiết kế. Chỉ chủ sở hữu và thiết kế chưa phát sinh Order.
     */
    public function update(
        CustomDesign $design,
        User $user,
        string $name,
        string $style,
        array $selectedItems
    ): CustomDesign {
        $this->assertOwnedAndEditable($design, $user);

        $payload = $this->preparePayload(
            $name,
            $style,
            $selectedItems
        );

        return DB::transaction(function () use ($design, $payload) {
            $design->update([
                'name' => $payload['name'],
                'style' => $payload['style'],
                'estimated_price' => $payload['pricing']['estimated_price'],
                'configuration_snapshot' => $payload['snapshot'],
                'status' => 'saved',
            ]);

            $this->replaceItems($design, $payload['pricing']['items']);

            return $design->fresh()->load('items.product');
        });
    }

    /**
     * Tính lại giá/tồn kho/tương thích theo Product hiện hành.
     * Không thay đổi Snapshot đã lưu.
     */
    public function recalculate(CustomDesign $design): array
    {
        $design->load('items');

        $selectedItems = $design->items
            ->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ])
            ->values()
            ->all();

        $pricing = $this->pricingService->calculate($selectedItems);
        $compatibility = $this->compatibilityService->validate(
            $pricing['items']
        );

        return [
            'pricing' => $pricing,
            'compatibility' => $compatibility,
        ];
    }

    /**
     * Customer chỉ được xóa thiết kế của mình khi chưa phát sinh Order.
     */
    public function deleteForUser(
        CustomDesign $design,
        User $user
    ): void {
        $this->assertOwnedAndEditable($design, $user);

        DB::transaction(function () use ($design) {
            $design->cartItems()->delete();
            $design->delete();
        });
    }

    public function assertOwnedAndEditable(
        CustomDesign $design,
        User $user
    ): void {
        if ($design->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'design' => 'Bạn không có quyền thao tác thiết kế này.',
            ]);
        }

        if ($design->orderDetails()->exists()) {
            throw ValidationException::withMessages([
                'design' =>
                    'Thiết kế đã phát sinh đơn hàng nên không thể sửa hoặc xóa.',
            ]);
        }
    }

    private function preparePayload(
        string $name,
        string $style,
        array $selectedItems
    ): array {
        $name = trim($name);

        if ($name === '') {
            throw ValidationException::withMessages([
                'name' => 'Vui lòng nhập tên thiết kế.',
            ]);
        }

        $pricing = $this->pricingService->calculate($selectedItems);
        $compatibility = $this->compatibilityService->validate(
            $pricing['items']
        );

        $snapshot = $this->buildSnapshot(
            $style,
            $pricing,
            $compatibility
        );

        return compact(
            'name',
            'style',
            'pricing',
            'compatibility',
            'snapshot'
        );
    }

    /**
     * Snapshot giữ lựa chọn và giá tạm tính tại thời điểm lưu.
     */
    private function buildSnapshot(
        string $style,
        array $pricing,
        array $compatibility
    ): array {
        $selection = [
            'container' => null,
            'plant' => null,
            'moss' => null,
            'substrate' => null,
            'stone' => null,
            'accessory' => null,
        ];

        foreach ($pricing['items'] as $item) {
            $type = $item['product_type'] ?? null;

            if ($type && array_key_exists($type, $selection)) {
                $selection[$type] = [
                    'product_id' => $item['product_id'],
                    'name' => $item['product_name'],
                    'sku' => $item['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'ecosystem_type' => $item['ecosystem_type'],
                ];
            }
        }

        return [
            'style' => $style,
            'ecosystem_type' => $compatibility['ecosystem_type'],
            'container' => $selection['container'],
            'plant' => $selection['plant'],
            'moss' => $selection['moss'],
            'substrate' => $selection['substrate'],
            'stone' => $selection['stone'],
            'accessory' => $selection['accessory'],
            'products' => $pricing['items'],
            'estimated_price' => $pricing['estimated_price'],
            'saved_at' => now()->toIso8601String(),
        ];
    }

    private function replaceItems(
        CustomDesign $design,
        array $items
    ): void {
        $design->items()->delete();

        foreach ($items as $item) {
            $design->items()->create([
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
            ]);
        }
    }

    private function generateDesignCode(): string
    {
        do {
            $code =
                'MGD-'
                . now()->format('Ymd')
                . '-'
                . strtoupper(Str::random(6));
        } while (
            CustomDesign::where('design_code', $code)->exists()
        );

        return $code;
    }
}
