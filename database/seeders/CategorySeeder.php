<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Các danh mục quà tặng chính thức của MiniGrove.
         */
        $categories = [
            [
                'name' => 'Quà tặng Ba Mẹ',
                'slug' => 'qua-tang-ba-me',
                'description' => 'Những mẫu MiniGrove phù hợp làm quà dành tặng ba mẹ, mang ý nghĩa yêu thương, bình an và gắn kết.',
                'is_active' => true,
                'display_order' => 1,
            ],

            [
                'name' => 'Quà tặng Tết',
                'slug' => 'qua-tang-tet',
                'description' => 'Những mẫu MiniGrove mang ý nghĩa may mắn, bình an, tài lộc và lời chúc tốt đẹp trong dịp năm mới.',
                'is_active' => true,
                'display_order' => 2,
            ],

            [
                'name' => 'Quà tặng Valentine',
                'slug' => 'qua-tang-valentine',
                'description' => 'Những mẫu MiniGrove lãng mạn và đáng yêu dành tặng người thương trong dịp Valentine.',
                'is_active' => true,
                'display_order' => 3,
            ],

            [
                'name' => 'Quà tặng Sinh Nhật',
                'slug' => 'qua-tang-sinh-nhat',
                'description' => 'Những mẫu MiniGrove phù hợp làm quà sinh nhật với thông điệp về niềm vui, ước mơ và những khởi đầu mới.',
                'is_active' => true,
                'display_order' => 4,
            ],

            [
                'name' => 'Quà tặng Phật Giáo',
                'slug' => 'qua-tang-phat-giao',
                'description' => 'Những mẫu MiniGrove mang phong cách Phật giáo, hướng đến sự an nhiên, thanh tịnh và bình an.',
                'is_active' => true,
                'display_order' => 5,
            ],
            [
                'name' => 'Bình',
                'slug' => 'binh',
                'description' => 'Bình dùng để thiết kế terrarium trong Studio.',
                'is_active' => true,
                'display_order' => 6,
            ],
            [
                'name' => 'Cây',
                'slug' => 'cay',
                'description' => 'Cây dùng để thiết kế terrarium trong Studio.',
                'is_active' => true,
                'display_order' => 7,
            ],
            [
                'name' => 'Rêu',
                'slug' => 'reu',
                'description' => 'Rêu dùng để thiết kế terrarium trong Studio.',
                'is_active' => true,
                'display_order' => 8,
            ],
            [
                'name' => 'Nền',
                'slug' => 'nen',
                'description' => 'Vật liệu nền dùng để thiết kế terrarium trong Studio.',
                'is_active' => true,
                'display_order' => 9,
            ],
            [
                'name' => 'Đá',
                'slug' => 'da',
                'description' => 'Đá trang trí dùng để thiết kế terrarium trong Studio.',
                'is_active' => true,
                'display_order' => 10,
            ],
            [
                'name' => 'Phụ kiện',
                'slug' => 'phu-kien',
                'description' => 'Phụ kiện trang trí dùng để thiết kế terrarium trong Studio.',
                'is_active' => true,
                'display_order' => 11,
            ],
        ];

        /*
         * Ánh xạ ảnh seed cho các danh mục quà tặng.
         * Tách biệt khỏi dữ liệu updateOrCreate để không đưa trường phụ vào database.
         */
        $seedImages = [
            'qua-tang-ba-me' => 'qua-tang-ba-me.png',
            'qua-tang-tet' => 'qua-tang-tet.png',
            'qua-tang-valentine' => 'qua-tang-valentine.png',
            'qua-tang-sinh-nhat' => 'qua-tang-sinh-nhat.png',
            'qua-tang-phat-giao' => 'qua-tang-phat-giao.png',
        ];

        /*
         * Tắt các category mẫu cũ.
         *
         * Không xóa cứng vì sản phẩm/lịch sử cũ có thể vẫn tham chiếu.
         */
        Category::query()
            ->whereNotIn(
                'slug',
                array_column($categories, 'slug')
            )
            ->update([
                'is_active' => false,
            ]);

        foreach ($categories as $categoryData) {
            $category = Category::updateOrCreate(
                [
                    'slug' => $categoryData['slug'],
                ],
                $categoryData
            );

            if (! isset($seedImages[$category->slug])) {
                continue;
            }

            $seedFileName = $seedImages[$category->slug];
            $standardPath = 'categories/'.$seedFileName;
            $sourcePath = database_path('seeders/assets/categories/'.$seedFileName);

            $hasValidImage = ! empty($category->image)
                && Storage::disk('public')->exists($category->image);

            if (! $hasValidImage && file_exists($sourcePath)) {
                Storage::disk('public')->put(
                    $standardPath,
                    file_get_contents($sourcePath)
                );

                $category->update([
                    'image' => $standardPath,
                ]);
            }
        }
    }
}
