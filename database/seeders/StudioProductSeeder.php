<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCompatibilityRule;
use Illuminate\Database\Seeder;

class StudioProductSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CATEGORY KỸ THUẬT CHO STUDIO
        |--------------------------------------------------------------------------
        |
        | Không hiển thị ở danh mục bán hàng thông thường.
        |
        */

        $category = Category::updateOrCreate(
            ['slug' => 'studio-materials'],
            [
                'name' => 'Sản phẩm MiniGrove Studio',
                'description' =>
                    'Các thành phần dùng để tự thiết kế Terrarium trong MiniGrove Studio.',
                'image' => null,
                'is_active' => false,
                'display_order' => 999,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | STUDIO PRODUCTS
        |--------------------------------------------------------------------------
        */

        $products = [

            /*
            |--------------------------------------------------------------------------
            | 1. BÌNH
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'ST-CONTAINER-CLOSED-001',
                'name' => 'Bình thủy tinh nắp kín 18cm',
                'product_type' => 'container',
                'ecosystem_type' => 'closed',
                'studio_capacity' => 4,
                'list_price' => 120000,
                'size' => 'medium',
                'weight' => 450,
                'container_diameter_cm' => 18,
                'container_height_cm' => 22,
                'care_level' => 'easy',
                'stock_quantity' => 20,
            ],

            [
                'sku' => 'ST-CONTAINER-CLOSED-002',
                'name' => 'Bình giọt nước nắp kín 22cm',
                'product_type' => 'container',
                'ecosystem_type' => 'closed',
                'studio_capacity' => 6,
                'list_price' => 165000,
                'size' => 'large',
                'weight' => 620,
                'container_diameter_cm' => 22,
                'container_height_cm' => 27,
                'care_level' => 'easy',
                'stock_quantity' => 15,
            ],

            [
                'sku' => 'ST-CONTAINER-OPEN-001',
                'name' => 'Bình thủy tinh mở 20cm',
                'product_type' => 'container',
                'ecosystem_type' => 'open',
                'studio_capacity' => 6,
                'list_price' => 135000,
                'size' => 'medium',
                'weight' => 500,
                'container_diameter_cm' => 20,
                'container_height_cm' => 18,
                'care_level' => 'easy',
                'stock_quantity' => 18,
            ],

            [
                'sku' => 'ST-CONTAINER-OPEN-002',
                'name' => 'Bình lập phương mở 18cm',
                'product_type' => 'container',
                'ecosystem_type' => 'open',
                'studio_capacity' => 5,
                'list_price' => 145000,
                'size' => 'medium',
                'weight' => 540,
                'container_diameter_cm' => 18,
                'container_height_cm' => 18,
                'care_level' => 'easy',
                'stock_quantity' => 16,
            ],

            [
                'sku' => 'ST-CONTAINER-DRY-001',
                'name' => 'Bình sa mạc mở 16cm',
                'product_type' => 'container',
                'ecosystem_type' => 'dry',
                'studio_capacity' => 5,
                'list_price' => 110000,
                'size' => 'small',
                'weight' => 380,
                'container_diameter_cm' => 16,
                'container_height_cm' => 14,
                'care_level' => 'easy',
                'stock_quantity' => 15,
            ],

            /*
            |--------------------------------------------------------------------------
            | 2. CÂY
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'ST-PLANT-FITTONIA-001',
                'name' => 'Fittonia đỏ mini',
                'product_type' => 'plant',
                'ecosystem_type' => 'closed',
                'list_price' => 45000,
                'size' => 'small',
                'weight' => 80,
                'care_level' => 'medium',
                'light_requirement' => 'indirect',
                'care_cycle_days' => 4,
                'stock_quantity' => 30,
            ],

            [
                'sku' => 'ST-PLANT-FITTONIA-002',
                'name' => 'Fittonia xanh mini',
                'product_type' => 'plant',
                'ecosystem_type' => 'closed',
                'list_price' => 43000,
                'size' => 'small',
                'weight' => 75,
                'care_level' => 'easy',
                'light_requirement' => 'indirect',
                'care_cycle_days' => 5,
                'stock_quantity' => 30,
            ],

            [
                'sku' => 'ST-PLANT-PEPEROMIA-001',
                'name' => 'Peperomia mini',
                'product_type' => 'plant',
                'ecosystem_type' => 'closed',
                'list_price' => 48000,
                'size' => 'small',
                'weight' => 85,
                'care_level' => 'medium',
                'light_requirement' => 'indirect',
                'care_cycle_days' => 5,
                'stock_quantity' => 24,
            ],

            [
                'sku' => 'ST-PLANT-FERN-001',
                'name' => 'Dương xỉ mini',
                'product_type' => 'plant',
                'ecosystem_type' => 'open',
                'list_price' => 42000,
                'size' => 'small',
                'weight' => 75,
                'care_level' => 'medium',
                'light_requirement' => 'indirect',
                'care_cycle_days' => 3,
                'stock_quantity' => 25,
            ],

            [
                'sku' => 'ST-PLANT-PILEA-001',
                'name' => 'Pilea mini',
                'product_type' => 'plant',
                'ecosystem_type' => 'open',
                'list_price' => 46000,
                'size' => 'small',
                'weight' => 80,
                'care_level' => 'easy',
                'light_requirement' => 'indirect',
                'care_cycle_days' => 5,
                'stock_quantity' => 25,
            ],

            [
                'sku' => 'ST-PLANT-SUCCULENT-001',
                'name' => 'Sen đá xanh mini',
                'product_type' => 'plant',
                'ecosystem_type' => 'dry',
                'list_price' => 38000,
                'size' => 'small',
                'weight' => 90,
                'care_level' => 'easy',
                'light_requirement' => 'bright',
                'care_cycle_days' => 7,
                'stock_quantity' => 28,
            ],

            [
                'sku' => 'ST-PLANT-HAWORTHIA-001',
                'name' => 'Haworthia mini',
                'product_type' => 'plant',
                'ecosystem_type' => 'dry',
                'list_price' => 42000,
                'size' => 'small',
                'weight' => 95,
                'care_level' => 'easy',
                'light_requirement' => 'bright',
                'care_cycle_days' => 8,
                'stock_quantity' => 25,
            ],

            /*
            |--------------------------------------------------------------------------
            | 3. RÊU
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'ST-MOSS-CLOSED-001',
                'name' => 'Rêu xanh giữ ẩm',
                'product_type' => 'moss',
                'ecosystem_type' => 'closed',
                'list_price' => 28000,
                'size' => 'small',
                'weight' => 60,
                'care_level' => 'easy',
                'stock_quantity' => 35,
            ],

            [
                'sku' => 'ST-MOSS-CLOSED-002',
                'name' => 'Rêu tấm rừng xanh',
                'product_type' => 'moss',
                'ecosystem_type' => 'closed',
                'list_price' => 32000,
                'size' => 'small',
                'weight' => 70,
                'care_level' => 'easy',
                'stock_quantity' => 30,
            ],

            [
                'sku' => 'ST-MOSS-OPEN-001',
                'name' => 'Rêu cushion mini',
                'product_type' => 'moss',
                'ecosystem_type' => 'open',
                'list_price' => 30000,
                'size' => 'small',
                'weight' => 60,
                'care_level' => 'easy',
                'stock_quantity' => 30,
            ],

            [
                'sku' => 'ST-MOSS-OPEN-002',
                'name' => 'Rêu sao mini',
                'product_type' => 'moss',
                'ecosystem_type' => 'open',
                'list_price' => 34000,
                'size' => 'small',
                'weight' => 65,
                'care_level' => 'medium',
                'stock_quantity' => 26,
            ],

            /*
            |--------------------------------------------------------------------------
            | 4. NỀN / GIÁ THỂ
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'ST-SUBSTRATE-CLOSED-001',
                'name' => 'Nền giữ ẩm Terrarium',
                'product_type' => 'substrate',
                'ecosystem_type' => 'closed',
                'list_price' => 35000,
                'size' => 'small',
                'weight' => 250,
                'care_level' => 'easy',
                'stock_quantity' => 40,
            ],

            [
                'sku' => 'ST-SUBSTRATE-CLOSED-002',
                'name' => 'Nền rừng nhiệt đới',
                'product_type' => 'substrate',
                'ecosystem_type' => 'closed',
                'list_price' => 39000,
                'size' => 'small',
                'weight' => 270,
                'care_level' => 'easy',
                'stock_quantity' => 35,
            ],

            [
                'sku' => 'ST-SUBSTRATE-OPEN-001',
                'name' => 'Nền thoáng khí Terrarium',
                'product_type' => 'substrate',
                'ecosystem_type' => 'open',
                'list_price' => 33000,
                'size' => 'small',
                'weight' => 240,
                'care_level' => 'easy',
                'stock_quantity' => 40,
            ],

            [
                'sku' => 'ST-SUBSTRATE-DRY-001',
                'name' => 'Nền cát sỏi sa mạc',
                'product_type' => 'substrate',
                'ecosystem_type' => 'dry',
                'list_price' => 32000,
                'size' => 'small',
                'weight' => 300,
                'care_level' => 'easy',
                'stock_quantity' => 40,
            ],

            /*
            |--------------------------------------------------------------------------
            | 5. ĐÁ
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'ST-STONE-WHITE-001',
                'name' => 'Đá suối trắng mini',
                'product_type' => 'stone',
                'ecosystem_type' => 'all',
                'list_price' => 22000,
                'size' => 'small',
                'weight' => 180,
                'care_level' => 'easy',
                'stock_quantity' => 50,
            ],

            [
                'sku' => 'ST-STONE-BLACK-001',
                'name' => 'Đá núi đen mini',
                'product_type' => 'stone',
                'ecosystem_type' => 'all',
                'list_price' => 25000,
                'size' => 'small',
                'weight' => 190,
                'care_level' => 'easy',
                'stock_quantity' => 45,
            ],

            [
                'sku' => 'ST-STONE-VOLCANIC-001',
                'name' => 'Đá nham thạch mini',
                'product_type' => 'stone',
                'ecosystem_type' => 'all',
                'list_price' => 28000,
                'size' => 'small',
                'weight' => 200,
                'care_level' => 'easy',
                'stock_quantity' => 40,
            ],

            [
                'sku' => 'ST-STONE-GRAVEL-001',
                'name' => 'Sỏi tự nhiên mini',
                'product_type' => 'stone',
                'ecosystem_type' => 'all',
                'list_price' => 20000,
                'size' => 'small',
                'weight' => 180,
                'care_level' => 'easy',
                'stock_quantity' => 55,
            ],

            /*
            |--------------------------------------------------------------------------
            | 6. PHỤ KIỆN
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'ST-ACCESSORY-HOUSE-001',
                'name' => 'Nhà gỗ mini',
                'product_type' => 'accessory',
                'ecosystem_type' => 'all',
                'list_price' => 26000,
                'size' => 'small',
                'weight' => 45,
                'care_level' => 'easy',
                'stock_quantity' => 30,
            ],

            [
                'sku' => 'ST-ACCESSORY-BRIDGE-001',
                'name' => 'Cầu gỗ mini',
                'product_type' => 'accessory',
                'ecosystem_type' => 'all',
                'list_price' => 29000,
                'size' => 'small',
                'weight' => 55,
                'care_level' => 'easy',
                'stock_quantity' => 30,
            ],

            [
                'sku' => 'ST-ACCESSORY-BENCH-001',
                'name' => 'Ghế gỗ mini',
                'product_type' => 'accessory',
                'ecosystem_type' => 'all',
                'list_price' => 24000,
                'size' => 'small',
                'weight' => 40,
                'care_level' => 'easy',
                'stock_quantity' => 32,
            ],

            [
                'sku' => 'ST-ACCESSORY-MUSHROOM-001',
                'name' => 'Nấm trang trí mini',
                'product_type' => 'accessory',
                'ecosystem_type' => 'all',
                'list_price' => 22000,
                'size' => 'small',
                'weight' => 25,
                'care_level' => 'easy',
                'stock_quantity' => 35,
            ],

            [
                'sku' => 'ST-ACCESSORY-DEER-001',
                'name' => 'Hươu mini trang trí',
                'product_type' => 'accessory',
                'ecosystem_type' => 'all',
                'list_price' => 27000,
                'size' => 'small',
                'weight' => 30,
                'care_level' => 'easy',
                'stock_quantity' => 30,
            ],

            [
                'sku' => 'ST-ACCESSORY-LANTERN-001',
                'name' => 'Đèn lồng mini',
                'product_type' => 'accessory',
                'ecosystem_type' => 'all',
                'list_price' => 25000,
                'size' => 'small',
                'weight' => 30,
                'care_level' => 'easy',
                'stock_quantity' => 30,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | CREATE / UPDATE PRODUCTS
        |--------------------------------------------------------------------------
        */

        foreach ($products as $data) {
            Product::updateOrCreate(
                [
                    'sku' => $data['sku'],
                ],
                array_merge(
                    [
                        'category_id' => $category->id,

                        'sale_price' => null,

                        'description' =>
                            'Thành phần dùng trong chức năng Thiết kế của bạn - MiniGrove Studio.',

                        'concept_story' => null,

                        'care_instructions' => null,

                        'low_stock_threshold' => 5,

                        'is_active' => true,

                        'is_featured' => false,

                        'is_grove_eligible' => false,

                        'is_warranty_eligible' => false,

                        'warranty_days' => null,

                        'warranty_policy' => null,

                        'is_studio_item' => true,

                        'studio_capacity' => null,

                        'light_requirement' => null,

                        'care_cycle_days' => null,

                        'container_diameter_cm' => null,

                        'container_height_cm' => null,
                    ],
                    $data
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | COMPATIBILITY RULE
        |--------------------------------------------------------------------------
        |
        | Bình kín 18cm khá nhỏ nên không cho dùng cầu gỗ.
        |
        */

        $closedContainer = Product::query()
            ->where(
                'sku',
                'ST-CONTAINER-CLOSED-001'
            )
            ->firstOrFail();

        $bridge = Product::query()
            ->where(
                'sku',
                'ST-ACCESSORY-BRIDGE-001'
            )
            ->firstOrFail();

        ProductCompatibilityRule::updateOrCreate(
            [
                'source_product_id' =>
                    $closedContainer->id,

                'target_product_id' =>
                    $bridge->id,
            ],
            [
                'is_compatible' => false,

                'reason' =>
                    'Cầu gỗ chiếm nhiều không gian, không phù hợp với bình nắp kín 18cm.',

                'is_active' => true,
            ]
        );
    }
}