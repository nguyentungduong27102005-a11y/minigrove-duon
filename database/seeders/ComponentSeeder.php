<?php

namespace Database\Seeders;

use App\Models\Component;
use Illuminate\Database\Seeder;

class ComponentSeeder extends Seeder
{
    public function run(): void
    {
        $components = [

            // =========================
            // BÌNH
            // =========================

            [
                'name' => 'Bình thủy tinh tròn 18cm',
                'sku' => 'CP-CONTAINER-001',
                'component_type' => 'container',
                'price' => 120000,
                'description' => 'Bình thủy tinh tròn dùng cho Terrarium kích thước vừa.',
                'stock_quantity' => 30,
                'unit' => 'cái',
                'size_group' => 'medium',
                'compatible_ecosystem_type' => 'closed',
                'compatibility_note' => 'Phù hợp Terrarium kín kích thước vừa.',
            ],

            [
                'name' => 'Bình thủy tinh mở 22cm',
                'sku' => 'CP-CONTAINER-002',
                'component_type' => 'container',
                'price' => 150000,
                'description' => 'Bình mở dùng cho hệ sinh thái mở.',
                'stock_quantity' => 25,
                'unit' => 'cái',
                'size_group' => 'medium',
                'compatible_ecosystem_type' => 'open',
                'compatibility_note' => 'Phù hợp Terrarium mở.',
            ],

            [
                'name' => 'Bình mini 16cm',
                'sku' => 'CP-CONTAINER-003',
                'component_type' => 'container',
                'price' => 90000,
                'description' => 'Bình nhỏ phù hợp hệ sinh thái mini và khô.',
                'stock_quantity' => 35,
                'unit' => 'cái',
                'size_group' => 'mini',
                'compatible_ecosystem_type' => 'all',
                'compatibility_note' => null,
            ],

            // =========================
            // CÂY
            // =========================

            [
                'name' => 'Fittonia mini',
                'sku' => 'CP-PLANT-001',
                'component_type' => 'plant',
                'price' => 35000,
                'description' => 'Cây Fittonia mini phù hợp môi trường ẩm.',
                'stock_quantity' => 80,
                'unit' => 'cây',
                'size_group' => 'mini',
                'compatible_ecosystem_type' => 'closed',
                'compatibility_note' => 'Ưa ẩm, phù hợp Terrarium kín.',
            ],

            [
                'name' => 'Dương xỉ mini',
                'sku' => 'CP-PLANT-002',
                'component_type' => 'plant',
                'price' => 40000,
                'description' => 'Dương xỉ nhỏ dùng cho Terrarium nhiệt đới.',
                'stock_quantity' => 60,
                'unit' => 'cây',
                'size_group' => 'mini',
                'compatible_ecosystem_type' => 'closed',
                'compatibility_note' => 'Ưa môi trường ẩm và ánh sáng gián tiếp.',
            ],

            [
                'name' => 'Sen đá mini',
                'sku' => 'CP-PLANT-003',
                'component_type' => 'plant',
                'price' => 30000,
                'description' => 'Sen đá mini dùng cho hệ sinh thái khô.',
                'stock_quantity' => 70,
                'unit' => 'cây',
                'size_group' => 'mini',
                'compatible_ecosystem_type' => 'dry',
                'compatibility_note' => 'Không phù hợp môi trường quá ẩm.',
            ],

            // =========================
            // RÊU
            // =========================

            [
                'name' => 'Rêu xanh Terrarium',
                'sku' => 'CP-MOSS-001',
                'component_type' => 'moss',
                'price' => 25000,
                'description' => 'Rêu xanh tạo lớp phủ tự nhiên cho Terrarium.',
                'stock_quantity' => 100,
                'unit' => 'gói',
                'size_group' => null,
                'compatible_ecosystem_type' => 'closed',
                'compatibility_note' => 'Phù hợp môi trường ẩm.',
            ],

            // =========================
            // NỀN / GIÁ THỂ
            // =========================

            [
                'name' => 'Sỏi thoát nước',
                'sku' => 'CP-SUBSTRATE-001',
                'component_type' => 'substrate',
                'price' => 15000,
                'description' => 'Lớp sỏi hỗ trợ thoát nước đáy bình.',
                'stock_quantity' => 150,
                'unit' => 'gói',
                'size_group' => null,
                'compatible_ecosystem_type' => 'all',
                'compatibility_note' => null,
            ],

            [
                'name' => 'Than hoạt tính',
                'sku' => 'CP-SUBSTRATE-002',
                'component_type' => 'substrate',
                'price' => 20000,
                'description' => 'Than hoạt tính hỗ trợ giảm mùi và nấm mốc.',
                'stock_quantity' => 120,
                'unit' => 'gói',
                'size_group' => null,
                'compatible_ecosystem_type' => 'closed',
                'compatibility_note' => 'Khuyến nghị cho Terrarium kín.',
            ],

            [
                'name' => 'Giá thể Terrarium',
                'sku' => 'CP-SUBSTRATE-003',
                'component_type' => 'substrate',
                'price' => 30000,
                'description' => 'Giá thể chuyên dụng cho cây Terrarium.',
                'stock_quantity' => 120,
                'unit' => 'gói',
                'size_group' => null,
                'compatible_ecosystem_type' => 'all',
                'compatibility_note' => null,
            ],

            // =========================
            // ĐÁ
            // =========================

            [
                'name' => 'Đá tự nhiên mini',
                'sku' => 'CP-STONE-001',
                'component_type' => 'stone',
                'price' => 25000,
                'description' => 'Đá tự nhiên dùng trang trí cảnh quan.',
                'stock_quantity' => 90,
                'unit' => 'gói',
                'size_group' => null,
                'compatible_ecosystem_type' => 'all',
                'compatibility_note' => null,
            ],

            // =========================
            // PHỤ KIỆN
            // =========================

            [
                'name' => 'Nhà gỗ mini',
                'sku' => 'CP-ACCESSORY-001',
                'component_type' => 'accessory',
                'price' => 45000,
                'description' => 'Nhà gỗ nhỏ dùng trang trí Terrarium.',
                'stock_quantity' => 50,
                'unit' => 'cái',
                'size_group' => 'mini',
                'compatible_ecosystem_type' => 'all',
                'compatibility_note' => null,
            ],

            [
                'name' => 'Nấm trang trí mini',
                'sku' => 'CP-ACCESSORY-002',
                'component_type' => 'accessory',
                'price' => 30000,
                'description' => 'Mô hình nấm trang trí khu rừng mini.',
                'stock_quantity' => 60,
                'unit' => 'cái',
                'size_group' => 'mini',
                'compatible_ecosystem_type' => 'all',
                'compatibility_note' => null,
            ],

            [
                'name' => 'Khủng long mini',
                'sku' => 'CP-ACCESSORY-003',
                'component_type' => 'accessory',
                'price' => 40000,
                'description' => 'Mô hình khủng long dùng cho Jurassic Mini World.',
                'stock_quantity' => 40,
                'unit' => 'cái',
                'size_group' => 'mini',
                'compatible_ecosystem_type' => 'all',
                'compatibility_note' => null,
            ],
        ];

        foreach ($components as $component) {
            Component::updateOrCreate(
                [
                    'sku' => $component['sku'],
                ],
                [
                    ...$component,
                    'is_active' => true,
                ]
            );
        }
    }
}