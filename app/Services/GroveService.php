<?php

namespace App\Services;

use App\Models\Grove;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Str;

class GroveService
{
    /**
     * Tạo Grove cho các Terrarium thuộc đơn hàng đã hoàn thành.
     *
     * Quy tắc:
     * - Chỉ xử lý Order = completed.
     * - PRODUCT phải có is_grove_eligible = true.
     * - CUSTOM_DESIGN được xem là Terrarium thiết kế riêng.
     * - quantity = n thì có tối đa n Grove.
     * - Nếu đã có Grove thì chỉ tạo phần còn thiếu.
     */
    public function createForCompletedOrder(
        Order $order
    ): int {
        /*
        |--------------------------------------------------------------------------
        | CHỈ XỬ LÝ ĐƠN ĐÃ HOÀN THÀNH
        |--------------------------------------------------------------------------
        */

        if (
            $order->order_status !== 'completed'
        ) {
            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DỮ LIỆU CẦN THIẾT
        |--------------------------------------------------------------------------
        */

        $order->loadMissing([
            'details.product',
            'details.customDesign',
        ]);


        $createdCount = 0;


        /*
        |--------------------------------------------------------------------------
        | DUYỆT TỪNG ORDER DETAIL
        |--------------------------------------------------------------------------
        */

        foreach (
            $order->details as $detail
        ) {

            if (
                !$this->isGroveEligible($detail)
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | SỐ GROVE CẦN CÓ
            |--------------------------------------------------------------------------
            */

            $targetQuantity =
                max(
                    0,
                    (int) $detail->quantity
                );


            if (
                $targetQuantity === 0
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CHỐNG TẠO TRÙNG
            |--------------------------------------------------------------------------
            |
            | Ví dụ:
            |
            | quantity = 3
            | đã có 1 Grove
            |
            | => chỉ tạo thêm 2 Grove.
            |
            */

            $existingCount =
                Grove::query()
                    ->where(
                        'order_detail_id',
                        $detail->id
                    )
                    ->count();


            $missingCount =
                $targetQuantity
                - $existingCount;


            if (
                $missingCount <= 0
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | TẠO GROVE
            |--------------------------------------------------------------------------
            */

            for (
                $index = 0;
                $index < $missingCount;
                $index++
            ) {

                $groveData =
                    $this->buildGroveData(
                        $detail
                    );


                Grove::create([
                    'grove_code' =>
                        $this->generateGroveCode(),

                    'user_id' =>
                        $order->user_id,

                    'order_detail_id' =>
                        $detail->id,

                    'terrarium_name' =>
                        $groveData[
                            'terrarium_name'
                        ],

                    'cover_image' =>
                        $groveData[
                            'cover_image'
                        ],

                    'ecosystem_type' =>
                        $groveData[
                            'ecosystem_type'
                        ],

                    'plant_snapshot' =>
                        $groveData[
                            'plant_snapshot'
                        ],

                    'moss_snapshot' =>
                        $groveData[
                            'moss_snapshot'
                        ],

                    'substrate_snapshot' =>
                        $groveData[
                            'substrate_snapshot'
                        ],

                    'stone_snapshot' =>
                        $groveData[
                            'stone_snapshot'
                        ],

                    'accessory_snapshot' =>
                        $groveData[
                            'accessory_snapshot'
                        ],

                    'care_level' =>
                        $groveData[
                            'care_level'
                        ],

                    'light_requirement' =>
                        $groveData[
                            'light_requirement'
                        ],

                    'care_cycle_days' =>
                        $groveData[
                            'care_cycle_days'
                        ],

                    'current_condition' =>
                        'Tốt',

                    /*
                     * Không còn QR Grove.
                     *
                     * Khi Order completed nghĩa là Customer
                     * đã nhận Terrarium nên Grove được Active
                     * ngay lập tức.
                     */
                    'status' =>
                        'active',

                    'received_at' =>
                        now(),

                    'activated_at' =>
                        now(),
                ]);


                $createdCount++;
            }
        }


        return $createdCount;
    }


    /**
     * Kiểm tra OrderDetail có được tạo Grove hay không.
     */
    private function isGroveEligible(
        OrderDetail $detail
    ): bool {
        $itemType =
            strtoupper(
                trim(
                    (string) $detail->item_type
                )
            );


        /*
        |--------------------------------------------------------------------------
        | PRODUCT
        |--------------------------------------------------------------------------
        */

        if (
            $itemType === 'PRODUCT'
        ) {

            if (
                !$detail->product
            ) {
                return false;
            }


            return
                (bool)
                $detail
                    ->product
                    ->is_grove_eligible;
        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOM DESIGN
        |--------------------------------------------------------------------------
        |
        | MiniGrove Studio tạo Terrarium thiết kế riêng,
        | vì vậy Custom Design cũng sinh Grove.
        |
        */

        if (
            $itemType === 'CUSTOM_DESIGN'
        ) {
            return
                $detail->customDesign
                    !== null;
        }


        return false;
    }


    /**
     * Chuẩn bị dữ liệu Grove theo loại OrderDetail.
     */
    private function buildGroveData(
        OrderDetail $detail
    ): array {
        $itemType =
            strtoupper(
                trim(
                    (string) $detail->item_type
                )
            );


        /*
        |--------------------------------------------------------------------------
        | PRODUCT
        |--------------------------------------------------------------------------
        */

        if (
            $itemType === 'PRODUCT'
            &&
            $detail->product
        ) {

            $product =
                $detail->product;


            return [
                'terrarium_name' =>
                    $detail->item_name,

                'cover_image' =>
                    null,

                'ecosystem_type' =>
                    $product->ecosystem_type
                    ?: 'terrarium',

                'plant_snapshot' =>
                    null,

                'moss_snapshot' =>
                    null,

                'substrate_snapshot' =>
                    null,

                'stone_snapshot' =>
                    null,

                'accessory_snapshot' =>
                    null,

                'care_level' =>
                    $product->care_level,

                'light_requirement' =>
                    $product
                        ->light_requirement,

                'care_cycle_days' =>
                    $product
                        ->care_cycle_days,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOM DESIGN
        |--------------------------------------------------------------------------
        */

        $customDesign =
            $detail->customDesign;


        return [
            'terrarium_name' =>
                $detail->item_name,

            'cover_image' =>
                null,

            'ecosystem_type' =>
                'custom',

            'plant_snapshot' =>
                null,

            'moss_snapshot' =>
                null,

            'substrate_snapshot' =>
                null,

            'stone_snapshot' =>
                null,

            'accessory_snapshot' =>
                null,

            'care_level' =>
                null,

            'light_requirement' =>
                null,

            'care_cycle_days' =>
                null,
        ];
    }


    /**
     * Sinh mã Grove duy nhất.
     *
     * Ví dụ:
     * MG-2026-AB12CD34
     */
    private function generateGroveCode(): string
    {
        do {

            $code =
                'MG-'
                . now()->format('Y')
                . '-'
                . Str::upper(
                    Str::random(8)
                );

        } while (
            Grove::query()
                ->where(
                    'grove_code',
                    $code
                )
                ->exists()
        );


        return $code;
    }
}