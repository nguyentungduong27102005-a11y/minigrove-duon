<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [

            /*
            |--------------------------------------------------------------------------
            | QUÀ TẶNG BA MẸ
            |--------------------------------------------------------------------------
            */

            'qua-tang-ba-me' => [
                'MG-001', // An Mộc
                'MG-002', // Dream Forest
                'MG-003', // Evergreen
                'MG-004', // Hơi Thở Của Rừng
                'MG-005', // Hương Hoa
                'MG-006', // Lil Cube
                'MG-007', // Mai Đào
                'MG-008', // Spring Petals
                'MG-009', // Tháp Rêu
                'MG-010', // Thuận Buồm Xuôi Gió
                'MG-011', // Ước Mơ Xanh
            ],

            /*
            |--------------------------------------------------------------------------
            | QUÀ TẶNG TẾT
            |--------------------------------------------------------------------------
            */

            'qua-tang-tet' => [
                'MG-012', // An Khang
                'MG-013', // Cát Tường
                'MG-014', // Chân Mây
                'MG-015', // Kim
                'MG-016', // Mộc
                'MG-017', // Thủy
                'MG-018', // Hỏa
                'MG-019', // Thổ
                'MG-020', // Như Ý
                'MG-021', // Phát Lộc
                'MG-022', // Rừng Xanh
            ],

            /*
            |--------------------------------------------------------------------------
            | QUÀ TẶNG VALENTINE
            |--------------------------------------------------------------------------
            */

            'qua-tang-valentine' => [
                'MG-023', // Sweet Home
                'MG-024', // Sweet Love
                'MG-025', // Đồi Tình Yêu
                'MG-026', // Growing Love
                'MG-027', // Vườn Tình Yêu
                'MG-028', // Tình Yêu
                'MG-029', // Cún iu
                'MG-030', // Gấu iu
                'MG-031', // Meo iu
                'MG-032', // Thỏ iu
            ],

            /*
            |--------------------------------------------------------------------------
            | QUÀ TẶNG SINH NHẬT
            |--------------------------------------------------------------------------
            */

            'qua-tang-sinh-nhat' => [
                'MG-014', // Chân Mây
                'MG-002', // Dream Forest
                'MG-033', // Giấc Mơ Xanh
                'MG-034', // Hidden Gem
                'MG-035', // Hồng Mơ
                'MG-036', // The Feelings
            ],

            /*
            |--------------------------------------------------------------------------
            | QUÀ TẶNG PHẬT GIÁO
            |--------------------------------------------------------------------------
            */

            'qua-tang-phat-giao' => [
                'MG-037', // Phật An Yên
                'MG-038', // Phật Diệu Tâm
                'MG-039', // Phật Thiền
                'MG-040', // Quan Âm An Đài
                'MG-041', // Quan Âm Diệu Pháp
                'MG-042', // Thích Ca An Nhiên
                'MG-043', // Thích Ca Minh Tâm
                'MG-044', // Vạn An
            ],
        ];

        /*
         * Xóa các liên kết danh mục của riêng bộ MG-001 -> MG-044
         * trước khi tạo lại.
         *
         * Không xóa Product.
         * Không xóa Category.
         */
        $productIds = Product::query()
            ->whereBetween('sku', ['MG-001', 'MG-044'])
            ->pluck('id');

        DB::table('category_product')
            ->whereIn('product_id', $productIds)
            ->delete();

        foreach ($assignments as $categorySlug => $skus) {

            $category = Category::where(
                'slug',
                $categorySlug
            )->firstOrFail();

            $products = Product::whereIn(
                'sku',
                $skus
            )->get();

            foreach ($products as $product) {

                DB::table('category_product')
                    ->updateOrInsert(
                        [
                            'category_id' => $category->id,
                            'product_id' => $product->id,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
            }
        }

        /*
         * Giữ category_id hiện tại làm "danh mục chính"
         * để các chức năng cũ của project tiếp tục hoạt động.
         */

        $primaryCategories = [
            'qua-tang-ba-me' => [
                'MG-001', 'MG-002', 'MG-003', 'MG-004',
                'MG-005', 'MG-006', 'MG-007', 'MG-008',
                'MG-009', 'MG-010', 'MG-011',
            ],

            'qua-tang-tet' => [
                'MG-012', 'MG-013', 'MG-014', 'MG-015',
                'MG-016', 'MG-017', 'MG-018', 'MG-019',
                'MG-020', 'MG-021', 'MG-022',
            ],

            'qua-tang-valentine' => [
                'MG-023', 'MG-024', 'MG-025', 'MG-026',
                'MG-027', 'MG-028', 'MG-029', 'MG-030',
                'MG-031', 'MG-032',
            ],

            'qua-tang-sinh-nhat' => [
                'MG-033', 'MG-034', 'MG-035', 'MG-036',
            ],

            'qua-tang-phat-giao' => [
                'MG-037', 'MG-038', 'MG-039', 'MG-040',
                'MG-041', 'MG-042', 'MG-043', 'MG-044',
            ],
        ];

        foreach ($primaryCategories as $categorySlug => $skus) {

            $category = Category::where(
                'slug',
                $categorySlug
            )->firstOrFail();

            Product::whereIn('sku', $skus)
                ->update([
                    'category_id' => $category->id,
                ]);
        }
    }
}