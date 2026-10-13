<?php

namespace Database\Seeders;

use App\Models\CareRule;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CareRuleSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. RULE THEO LOẠI HỆ SINH THÁI
        |--------------------------------------------------------------------------
        */

        $ecosystemRules = [

            // Terrarium kín
            [
                'name' => 'Kiểm tra độ ẩm Terrarium kín',
                'source_type' => 'ECOSYSTEM',
                'ecosystem_type' => 'closed',
                'task_type' => 'HUMIDITY_CHECK',
                'interval_days' => 3,
                'instructions' =>
                    'Quan sát độ ẩm trong bình và lượng hơi nước bám trên thành kính.',
            ],

            [
                'name' => 'Kiểm tra nấm mốc Terrarium kín',
                'source_type' => 'ECOSYSTEM',
                'ecosystem_type' => 'closed',
                'task_type' => 'MOLD_CHECK',
                'interval_days' => 7,
                'instructions' =>
                    'Kiểm tra rêu, nền và lá cây để phát hiện nấm mốc sớm.',
            ],

            [
                'name' => 'Vệ sinh bình Terrarium kín',
                'source_type' => 'ECOSYSTEM',
                'ecosystem_type' => 'closed',
                'task_type' => 'CLEANING',
                'interval_days' => 14,
                'instructions' =>
                    'Lau nhẹ mặt kính và loại bỏ lá hỏng nếu có.',
            ],

            // Terrarium mở
            [
                'name' => 'Tưới nước Terrarium mở',
                'source_type' => 'ECOSYSTEM',
                'ecosystem_type' => 'open',
                'task_type' => 'WATERING',
                'interval_days' => 4,
                'instructions' =>
                    'Kiểm tra độ khô của nền trước khi bổ sung lượng nước vừa phải.',
            ],

            [
                'name' => 'Điều chỉnh ánh sáng Terrarium mở',
                'source_type' => 'ECOSYSTEM',
                'ecosystem_type' => 'open',
                'task_type' => 'LIGHT_ADJUSTMENT',
                'interval_days' => 7,
                'instructions' =>
                    'Đảm bảo Terrarium nhận ánh sáng phù hợp và tránh nắng gắt trực tiếp.',
            ],

            // Terrarium khô
            [
                'name' => 'Tưới nước Terrarium khô',
                'source_type' => 'ECOSYSTEM',
                'ecosystem_type' => 'dry',
                'task_type' => 'WATERING',
                'interval_days' => 10,
                'instructions' =>
                    'Chỉ tưới khi giá thể đã khô rõ rệt, tránh tưới quá nhiều.',
            ],

            [
                'name' => 'Kiểm tra lá Terrarium khô',
                'source_type' => 'ECOSYSTEM',
                'ecosystem_type' => 'dry',
                'task_type' => 'LEAF_CHECK',
                'interval_days' => 7,
                'instructions' =>
                    'Kiểm tra lá mềm, úng hoặc đổi màu để phát hiện vấn đề sớm.',
            ],
        ];

        foreach ($ecosystemRules as $rule) {
            CareRule::updateOrCreate(
                [
                    'source_type' => $rule['source_type'],
                    'ecosystem_type' => $rule['ecosystem_type'],
                    'task_type' => $rule['task_type'],
                    'product_id' => null,
                ],
                [
                    'name' => $rule['name'],
                    'interval_days' => $rule['interval_days'],
                    'instructions' => $rule['instructions'],
                    'is_active' => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. RULE RIÊNG CHO PRODUCT
        |--------------------------------------------------------------------------
        */

        $productRules = [

            [
                'sku' => 'MG-FOREST-001',
                'name' => 'Cắt tỉa Forest Calm',
                'task_type' => 'PRUNING',
                'interval_days' => 14,
                'instructions' =>
                    'Cắt tỉa lá phát triển quá mức để giữ bố cục của Forest Calm.',
            ],

            [
                'sku' => 'MG-DESERT-001',
                'name' => 'Kiểm tra ánh sáng Desert Stone',
                'task_type' => 'LIGHT_ADJUSTMENT',
                'interval_days' => 7,
                'instructions' =>
                    'Đảm bảo Desert Stone nhận đủ ánh sáng nhưng tránh nắng quá gắt.',
            ],
        ];

        foreach ($productRules as $rule) {
            $product = Product::where(
                'sku',
                $rule['sku']
            )->first();

            if (!$product) {
                continue;
            }

            CareRule::updateOrCreate(
                [
                    'source_type' => 'PRODUCT',
                    'ecosystem_type' => null,
                    'product_id' => $product->id,
                    'task_type' => $rule['task_type'],
                ],
                [
                    'name' => $rule['name'],
                    'interval_days' => $rule['interval_days'],
                    'instructions' => $rule['instructions'],
                    'is_active' => true,
                ]
            );
        }
    }
}