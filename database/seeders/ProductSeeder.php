<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Category kỹ thuật tạm thời.
         *
         * Các danh mục quà tặng thực tế sẽ được xây dựng
         * sau bằng quan hệ nhiều-nhiều.
         *
         * is_active = false để category này không xuất hiện
         * trong bộ lọc danh mục phía khách hàng.
         */
        $baseCategory = Category::updateOrCreate(
            ['slug' => 'san-pham-minigrove'],
            [
                'name' => 'Sản phẩm MiniGrove',
                'description' => 'Danh mục kỹ thuật dùng cho các sản phẩm MiniGrove.',
                'image' => null,
                'is_active' => false,
                'display_order' => 999,
            ]
        );

        $products = [

            /*
            |--------------------------------------------------------------------------
            | MG-001 -> MG-011
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'MG-001',
                'name' => 'An Mộc',
                'ecosystem_type' => 'closed',
                'list_price' => 399000,
                'size' => 'medium',
                'weight' => 650,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 20,
                'is_featured' => false,
                'description' => 'Terrarium mang phong cách mộc mạc với sắc xanh dịu nhẹ, phù hợp làm quà tặng mang ý nghĩa bình an.',
                'concept_story' => 'An Mộc gợi lên một khoảng xanh giản dị, bình yên và gần gũi với thiên nhiên.',
            ],

            [
                'sku' => 'MG-002',
                'name' => 'Dream Forest',
                'ecosystem_type' => 'closed',
                'list_price' => 529000,
                'size' => 'large',
                'weight' => 950,
                'care_level' => 'medium',
                'care_cycle_days' => 7,
                'stock_quantity' => 15,
                'is_featured' => true,
                'description' => 'Khu rừng thu nhỏ với nhiều tầng cây và rêu tạo cảm giác xanh mát, huyền ảo.',
                'concept_story' => 'Dream Forest tái hiện một khu rừng trong mơ nằm gọn trong không gian nhỏ.',
            ],

            [
                'sku' => 'MG-003',
                'name' => 'Evergreen',
                'ecosystem_type' => 'closed',
                'list_price' => 449000,
                'size' => 'medium',
                'weight' => 720,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 18,
                'is_featured' => false,
                'description' => 'Terrarium xanh quanh năm với bố cục tự nhiên, nhẹ nhàng và dễ kết hợp trong nhiều không gian.',
                'concept_story' => 'Evergreen tượng trưng cho sức sống bền bỉ và những điều tốt đẹp luôn được gìn giữ.',
            ],

            [
                'sku' => 'MG-004',
                'name' => 'Hơi Thở Của Rừng',
                'ecosystem_type' => 'closed',
                'list_price' => 579000,
                'size' => 'large',
                'weight' => 1050,
                'care_level' => 'medium',
                'care_cycle_days' => 7,
                'stock_quantity' => 12,
                'is_featured' => true,
                'description' => 'Không gian rừng nhiệt đới thu nhỏ với rêu và cây xanh tạo chiều sâu tự nhiên.',
                'concept_story' => 'Một góc rừng xanh mang hơi thở thiên nhiên vào không gian sống mỗi ngày.',
            ],

            [
                'sku' => 'MG-005',
                'name' => 'Hương Hoa',
                'ecosystem_type' => 'open',
                'list_price' => 469000,
                'size' => 'medium',
                'weight' => 700,
                'care_level' => 'medium',
                'care_cycle_days' => 5,
                'stock_quantity' => 16,
                'is_featured' => false,
                'description' => 'Thiết kế mềm mại lấy cảm hứng từ khu vườn hoa nhỏ kết hợp cùng những mảng xanh tự nhiên.',
                'concept_story' => 'Hương Hoa gửi gắm vẻ đẹp nhẹ nhàng và sự ấm áp trong từng món quà.',
            ],

            [
                'sku' => 'MG-006',
                'name' => 'Lil Cube',
                'ecosystem_type' => 'closed',
                'list_price' => 299000,
                'size' => 'small',
                'weight' => 420,
                'care_level' => 'easy',
                'care_cycle_days' => 12,
                'stock_quantity' => 25,
                'is_featured' => false,
                'description' => 'Terrarium nhỏ gọn trong thiết kế hình khối hiện đại, phù hợp bàn học và bàn làm việc.',
                'concept_story' => 'Một khu rừng nhỏ vừa đủ để mang màu xanh vào những góc không gian nhỏ nhất.',
            ],

            [
                'sku' => 'MG-007',
                'name' => 'Mai Đào',
                'ecosystem_type' => 'open',
                'list_price' => 549000,
                'size' => 'large',
                'weight' => 920,
                'care_level' => 'medium',
                'care_cycle_days' => 6,
                'stock_quantity' => 12,
                'is_featured' => false,
                'description' => 'Thiết kế mang sắc xuân với điểm nhấn hoa và cây xanh, phù hợp làm quà trong những dịp đặc biệt.',
                'concept_story' => 'Mai Đào tượng trưng cho mùa xuân, sự sum vầy và khởi đầu nhiều may mắn.',
            ],

            [
                'sku' => 'MG-008',
                'name' => 'Spring Petals',
                'ecosystem_type' => 'open',
                'list_price' => 479000,
                'size' => 'medium',
                'weight' => 730,
                'care_level' => 'medium',
                'care_cycle_days' => 6,
                'stock_quantity' => 15,
                'is_featured' => false,
                'description' => 'Khu vườn mini mang sắc màu tươi sáng với bố cục mềm mại lấy cảm hứng từ những cánh hoa mùa xuân.',
                'concept_story' => 'Spring Petals lưu giữ cảm giác tươi mới của mùa xuân trong một thế giới thu nhỏ.',
            ],

            [
                'sku' => 'MG-009',
                'name' => 'Tháp Rêu',
                'ecosystem_type' => 'closed',
                'list_price' => 599000,
                'size' => 'large',
                'weight' => 1100,
                'care_level' => 'medium',
                'care_cycle_days' => 8,
                'stock_quantity' => 10,
                'is_featured' => false,
                'description' => 'Terrarium tạo hình theo chiều cao với các lớp rêu xanh mang đến cảm giác sâu và tự nhiên.',
                'concept_story' => 'Tháp Rêu mô phỏng những tầng thực vật nối tiếp nhau trong một khu rừng xanh.',
            ],

            [
                'sku' => 'MG-010',
                'name' => 'Thuận Buồm Xuôi Gió',
                'ecosystem_type' => 'open',
                'list_price' => 649000,
                'size' => 'large',
                'weight' => 1250,
                'care_level' => 'medium',
                'care_cycle_days' => 7,
                'stock_quantity' => 10,
                'is_featured' => true,
                'description' => 'Tiểu cảnh MiniGrove mang chủ đề thuận lợi và thành công, thích hợp làm quà chúc mừng.',
                'concept_story' => 'Thiết kế gửi gắm lời chúc mọi hành trình đều thuận lợi và đạt được điều mong muốn.',
            ],

            [
                'sku' => 'MG-011',
                'name' => 'Ước Mơ Xanh',
                'ecosystem_type' => 'closed',
                'list_price' => 429000,
                'size' => 'medium',
                'weight' => 680,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 18,
                'is_featured' => false,
                'description' => 'Terrarium xanh dịu với bố cục cân bằng, mang cảm giác tươi mới và thư giãn.',
                'concept_story' => 'Ước Mơ Xanh là lời nhắn về những ước mơ được nuôi dưỡng và lớn lên từng ngày.',
            ],

            /*
            |--------------------------------------------------------------------------
            | MG-012 -> MG-022
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'MG-012',
                'name' => 'An Khang',
                'ecosystem_type' => 'closed',
                'list_price' => 459000,
                'size' => 'medium',
                'weight' => 720,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 18,
                'is_featured' => false,
                'description' => 'Tiểu cảnh xanh mang ý nghĩa sức khỏe và bình an, phù hợp làm quà đầu năm.',
                'concept_story' => 'An Khang gửi lời chúc một năm mới khỏe mạnh, yên vui và nhiều điều tốt đẹp.',
            ],

            [
                'sku' => 'MG-013',
                'name' => 'Cát Tường',
                'ecosystem_type' => 'open',
                'list_price' => 499000,
                'size' => 'medium',
                'weight' => 780,
                'care_level' => 'medium',
                'care_cycle_days' => 7,
                'stock_quantity' => 15,
                'is_featured' => false,
                'description' => 'Terrarium có bố cục hài hòa, mang ý nghĩa may mắn và những điều thuận lợi.',
                'concept_story' => 'Cát Tường tượng trưng cho những điều tốt lành đến trong năm mới.',
            ],

            [
                'sku' => 'MG-014',
                'name' => 'Chân Mây',
                'ecosystem_type' => 'closed',
                'list_price' => 539000,
                'size' => 'large',
                'weight' => 900,
                'care_level' => 'medium',
                'care_cycle_days' => 8,
                'stock_quantity' => 14,
                'is_featured' => true,
                'description' => 'Tiểu cảnh xanh với bố cục nhiều tầng gợi hình ảnh vùng núi ẩn hiện dưới những tầng mây.',
                'concept_story' => 'Chân Mây gợi cảm giác bình yên của một hành trình tìm đến thiên nhiên.',
            ],

            [
                'sku' => 'MG-015',
                'name' => 'Kim',
                'ecosystem_type' => 'dry',
                'list_price' => 389000,
                'size' => 'medium',
                'weight' => 620,
                'care_level' => 'easy',
                'care_cycle_days' => 14,
                'stock_quantity' => 20,
                'is_featured' => false,
                'description' => 'Tiểu cảnh thuộc bộ Ngũ Hành với phong cách sáng, gọn và hiện đại.',
                'concept_story' => 'Kim đại diện cho sự vững vàng, sắc nét và nguồn năng lượng mạnh mẽ.',
            ],

            [
                'sku' => 'MG-016',
                'name' => 'Mộc',
                'ecosystem_type' => 'closed',
                'list_price' => 389000,
                'size' => 'medium',
                'weight' => 650,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 20,
                'is_featured' => false,
                'description' => 'Tiểu cảnh thuộc bộ Ngũ Hành với sắc xanh làm chủ đạo và cảm giác gần gũi thiên nhiên.',
                'concept_story' => 'Mộc tượng trưng cho sinh trưởng, sức sống và những khởi đầu mới.',
            ],

            [
                'sku' => 'MG-017',
                'name' => 'Thủy',
                'ecosystem_type' => 'closed',
                'list_price' => 399000,
                'size' => 'medium',
                'weight' => 670,
                'care_level' => 'medium',
                'care_cycle_days' => 8,
                'stock_quantity' => 18,
                'is_featured' => false,
                'description' => 'Tiểu cảnh thuộc bộ Ngũ Hành với bố cục mềm mại và các đường nét uyển chuyển.',
                'concept_story' => 'Thủy gợi sự linh hoạt, hài hòa và dòng chảy liên tục của cuộc sống.',
            ],

            [
                'sku' => 'MG-018',
                'name' => 'Hỏa',
                'ecosystem_type' => 'dry',
                'list_price' => 399000,
                'size' => 'medium',
                'weight' => 640,
                'care_level' => 'easy',
                'care_cycle_days' => 14,
                'stock_quantity' => 18,
                'is_featured' => false,
                'description' => 'Tiểu cảnh thuộc bộ Ngũ Hành với bố cục nổi bật, tạo cảm giác ấm áp và tràn đầy năng lượng.',
                'concept_story' => 'Hỏa tượng trưng cho nhiệt huyết, sức mạnh và tinh thần tiến về phía trước.',
            ],

            [
                'sku' => 'MG-019',
                'name' => 'Thổ',
                'ecosystem_type' => 'dry',
                'list_price' => 389000,
                'size' => 'medium',
                'weight' => 700,
                'care_level' => 'easy',
                'care_cycle_days' => 14,
                'stock_quantity' => 20,
                'is_featured' => false,
                'description' => 'Tiểu cảnh thuộc bộ Ngũ Hành với sắc đất và đá tạo cảm giác chắc chắn, cân bằng.',
                'concept_story' => 'Thổ đại diện cho sự ổn định, bền vững và nền tảng vững chắc.',
            ],

            [
                'sku' => 'MG-020',
                'name' => 'Như Ý',
                'ecosystem_type' => 'open',
                'list_price' => 519000,
                'size' => 'large',
                'weight' => 880,
                'care_level' => 'medium',
                'care_cycle_days' => 7,
                'stock_quantity' => 14,
                'is_featured' => false,
                'description' => 'Tiểu cảnh mang ý nghĩa mọi điều hanh thông và đạt được những mong muốn tốt đẹp.',
                'concept_story' => 'Như Ý là lời chúc những dự định trong tương lai đều diễn ra thuận lợi.',
            ],

            [
                'sku' => 'MG-021',
                'name' => 'Phát Lộc',
                'ecosystem_type' => 'closed',
                'list_price' => 569000,
                'size' => 'large',
                'weight' => 980,
                'care_level' => 'medium',
                'care_cycle_days' => 8,
                'stock_quantity' => 12,
                'is_featured' => true,
                'description' => 'Terrarium xanh phong phú mang thông điệp phát triển, may mắn và tài lộc.',
                'concept_story' => 'Phát Lộc gửi gắm lời chúc thịnh vượng và nhiều cơ hội mới.',
            ],

            [
                'sku' => 'MG-022',
                'name' => 'Rừng Xanh',
                'ecosystem_type' => 'closed',
                'list_price' => 489000,
                'size' => 'medium',
                'weight' => 760,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 16,
                'is_featured' => false,
                'description' => 'Một khu rừng nhiệt đới thu nhỏ với nhiều sắc độ xanh và lớp rêu tự nhiên.',
                'concept_story' => 'Rừng Xanh đưa cảm giác mát lành của thiên nhiên đến gần hơn với cuộc sống.',
            ],

            /*
            |--------------------------------------------------------------------------
            | MG-023 -> MG-032
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'MG-023',
                'name' => 'Sweet Home',
                'ecosystem_type' => 'closed',
                'list_price' => 429000,
                'size' => 'medium',
                'weight' => 670,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 18,
                'is_featured' => false,
                'description' => 'Tiểu cảnh ngôi nhà nhỏ giữa cây xanh mang cảm giác ấm áp và gần gũi.',
                'concept_story' => 'Sweet Home tượng trưng cho một nơi luôn có tình yêu và sự bình yên.',
            ],

            [
                'sku' => 'MG-024',
                'name' => 'Sweet Love',
                'ecosystem_type' => 'open',
                'list_price' => 449000,
                'size' => 'medium',
                'weight' => 690,
                'care_level' => 'medium',
                'care_cycle_days' => 6,
                'stock_quantity' => 16,
                'is_featured' => false,
                'description' => 'Terrarium mang phong cách lãng mạn với các chi tiết nhỏ tượng trưng cho tình yêu.',
                'concept_story' => 'Sweet Love là món quà nhỏ lưu giữ những khoảnh khắc ngọt ngào bên người thương.',
            ],

            [
                'sku' => 'MG-025',
                'name' => 'Đồi Tình Yêu',
                'ecosystem_type' => 'open',
                'list_price' => 569000,
                'size' => 'large',
                'weight' => 960,
                'care_level' => 'medium',
                'care_cycle_days' => 6,
                'stock_quantity' => 12,
                'is_featured' => false,
                'description' => 'Tiểu cảnh tạo hình đồi xanh với không gian mở và những chi tiết mang màu sắc lãng mạn.',
                'concept_story' => 'Đồi Tình Yêu kể câu chuyện về hai người cùng nhau đi qua những hành trình đẹp.',
            ],

            [
                'sku' => 'MG-026',
                'name' => 'Growing Love',
                'ecosystem_type' => 'closed',
                'list_price' => 479000,
                'size' => 'medium',
                'weight' => 720,
                'care_level' => 'easy',
                'care_cycle_days' => 9,
                'stock_quantity' => 16,
                'is_featured' => false,
                'description' => 'Terrarium xanh tượng trưng cho một tình cảm được chăm sóc và lớn lên theo thời gian.',
                'concept_story' => 'Growing Love nhắc rằng tình yêu cũng cần được vun đắp mỗi ngày như một khu vườn nhỏ.',
            ],

            [
                'sku' => 'MG-027',
                'name' => 'Vườn Tình Yêu',
                'ecosystem_type' => 'open',
                'list_price' => 599000,
                'size' => 'large',
                'weight' => 1050,
                'care_level' => 'medium',
                'care_cycle_days' => 6,
                'stock_quantity' => 10,
                'is_featured' => true,
                'description' => 'Khu vườn mini với bố cục nhiều lớp, cây xanh và phụ kiện mang phong cách lãng mạn.',
                'concept_story' => 'Vườn Tình Yêu là một thế giới nhỏ dành cho những kỷ niệm được cùng nhau vun trồng.',
            ],

            [
                'sku' => 'MG-028',
                'name' => 'Tình Yêu',
                'ecosystem_type' => 'open',
                'list_price' => 399000,
                'size' => 'medium',
                'weight' => 640,
                'care_level' => 'easy',
                'care_cycle_days' => 7,
                'stock_quantity' => 20,
                'is_featured' => false,
                'description' => 'Mẫu terrarium đơn giản, nhẹ nhàng dành cho những món quà mang thông điệp yêu thương.',
                'concept_story' => 'Một món quà xanh thay cho lời yêu thương được gửi đến người đặc biệt.',
            ],

            [
                'sku' => 'MG-029',
                'name' => 'Cún iu',
                'ecosystem_type' => 'open',
                'list_price' => 349000,
                'size' => 'small',
                'weight' => 500,
                'care_level' => 'easy',
                'care_cycle_days' => 8,
                'stock_quantity' => 22,
                'is_featured' => false,
                'description' => 'Tiểu cảnh nhỏ đáng yêu với điểm nhấn hình chú cún giữa không gian xanh.',
                'concept_story' => 'Cún iu mang đến cảm giác vui vẻ, gần gũi và đáng yêu cho người nhận.',
            ],

            [
                'sku' => 'MG-030',
                'name' => 'Gấu iu',
                'ecosystem_type' => 'open',
                'list_price' => 349000,
                'size' => 'small',
                'weight' => 510,
                'care_level' => 'easy',
                'care_cycle_days' => 8,
                'stock_quantity' => 22,
                'is_featured' => false,
                'description' => 'Tiểu cảnh nhỏ với chú gấu dễ thương, phù hợp làm món quà tình cảm và trẻ trung.',
                'concept_story' => 'Gấu iu là món quà nhỏ mang theo cảm giác ấm áp và những khoảnh khắc đáng nhớ.',
            ],

            [
                'sku' => 'MG-031',
                'name' => 'Meo iu',
                'ecosystem_type' => 'open',
                'list_price' => 349000,
                'size' => 'small',
                'weight' => 490,
                'care_level' => 'easy',
                'care_cycle_days' => 8,
                'stock_quantity' => 22,
                'is_featured' => false,
                'description' => 'Terrarium nhỏ với điểm nhấn hình mèo đáng yêu nằm giữa khu vườn xanh.',
                'concept_story' => 'Meo iu dành cho những người yêu nét tinh nghịch, nhẹ nhàng và dễ thương.',
            ],

            [
                'sku' => 'MG-032',
                'name' => 'Thỏ iu',
                'ecosystem_type' => 'open',
                'list_price' => 349000,
                'size' => 'small',
                'weight' => 500,
                'care_level' => 'easy',
                'care_cycle_days' => 8,
                'stock_quantity' => 22,
                'is_featured' => false,
                'description' => 'Khu vườn mini nhỏ nhắn với chú thỏ làm điểm nhấn giữa cây xanh.',
                'concept_story' => 'Thỏ iu mang nét dịu dàng và dễ thương vào một món quà xanh đầy cảm xúc.',
            ],

            /*
            |--------------------------------------------------------------------------
            | MG-033 -> MG-036
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'MG-033',
                'name' => 'Giấc Mơ Xanh',
                'ecosystem_type' => 'closed',
                'list_price' => 459000,
                'size' => 'medium',
                'weight' => 710,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 17,
                'is_featured' => false,
                'description' => 'Terrarium với những mảng xanh mềm mại tạo nên cảm giác thư giãn và giàu sức sống.',
                'concept_story' => 'Giấc Mơ Xanh gửi lời chúc về những dự định mới luôn được nuôi dưỡng và phát triển.',
            ],

            [
                'sku' => 'MG-034',
                'name' => 'Hidden Gem',
                'ecosystem_type' => 'closed',
                'list_price' => 549000,
                'size' => 'large',
                'weight' => 920,
                'care_level' => 'medium',
                'care_cycle_days' => 8,
                'stock_quantity' => 12,
                'is_featured' => true,
                'description' => 'Tiểu cảnh nhiều lớp với những chi tiết ẩn giữa cây và rêu tạo cảm giác khám phá.',
                'concept_story' => 'Hidden Gem tượng trưng cho những điều quý giá đôi khi nằm trong những nơi giản dị nhất.',
            ],

            [
                'sku' => 'MG-035',
                'name' => 'Hồng Mơ',
                'ecosystem_type' => 'open',
                'list_price' => 479000,
                'size' => 'medium',
                'weight' => 730,
                'care_level' => 'medium',
                'care_cycle_days' => 6,
                'stock_quantity' => 15,
                'is_featured' => false,
                'description' => 'Tiểu cảnh có sắc màu nhẹ nhàng kết hợp cùng cây xanh tạo nên vẻ đẹp mềm mại.',
                'concept_story' => 'Hồng Mơ mang theo một lời chúc ngọt ngào cho những ngày đáng nhớ.',
            ],

            [
                'sku' => 'MG-036',
                'name' => 'The Feelings',
                'ecosystem_type' => 'open',
                'list_price' => 529000,
                'size' => 'large',
                'weight' => 870,
                'care_level' => 'medium',
                'care_cycle_days' => 7,
                'stock_quantity' => 13,
                'is_featured' => false,
                'description' => 'Terrarium được xây dựng theo phong cách cảm xúc với bố cục nhẹ nhàng và tinh tế.',
                'concept_story' => 'The Feelings là món quà dành cho những cảm xúc khó diễn đạt thành lời.',
            ],

            /*
            |--------------------------------------------------------------------------
            | MG-037 -> MG-044
            |--------------------------------------------------------------------------
            */

            [
                'sku' => 'MG-037',
                'name' => 'Phật An Yên',
                'ecosystem_type' => 'closed',
                'list_price' => 599000,
                'size' => 'large',
                'weight' => 1050,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 12,
                'is_featured' => false,
                'description' => 'Tiểu cảnh Phật giáo kết hợp không gian xanh tạo cảm giác thanh tịnh và bình an.',
                'concept_story' => 'Phật An Yên gợi nhắc về sự tĩnh tại và bình yên trong mỗi ngày.',
            ],

            [
                'sku' => 'MG-038',
                'name' => 'Phật Diệu Tâm',
                'ecosystem_type' => 'closed',
                'list_price' => 649000,
                'size' => 'large',
                'weight' => 1120,
                'care_level' => 'medium',
                'care_cycle_days' => 8,
                'stock_quantity' => 10,
                'is_featured' => false,
                'description' => 'Tiểu cảnh xanh mang chủ đề Phật giáo với bố cục cân bằng và trang nghiêm.',
                'concept_story' => 'Phật Diệu Tâm hướng đến sự an tĩnh, lòng thiện lành và sự cân bằng trong tâm.',
            ],

            [
                'sku' => 'MG-039',
                'name' => 'Phật Thiền',
                'ecosystem_type' => 'open',
                'list_price' => 579000,
                'size' => 'large',
                'weight' => 1000,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 12,
                'is_featured' => false,
                'description' => 'Không gian tiểu cảnh mang phong cách thiền với cây xanh, đá và bố cục tối giản.',
                'concept_story' => 'Phật Thiền gợi nên một khoảng lặng để tìm lại sự thư thái giữa nhịp sống bận rộn.',
            ],

            [
                'sku' => 'MG-040',
                'name' => 'Quan Âm An Đài',
                'ecosystem_type' => 'open',
                'list_price' => 699000,
                'size' => 'large',
                'weight' => 1250,
                'care_level' => 'medium',
                'care_cycle_days' => 7,
                'stock_quantity' => 8,
                'is_featured' => true,
                'description' => 'Tiểu cảnh Quan Âm kết hợp cây xanh và địa hình nhiều lớp tạo không gian trang nhã.',
                'concept_story' => 'Quan Âm An Đài mang thông điệp về lòng từ bi, sự bình an và che chở.',
            ],

            [
                'sku' => 'MG-041',
                'name' => 'Quan Âm Diệu Pháp',
                'ecosystem_type' => 'open',
                'list_price' => 749000,
                'size' => 'large',
                'weight' => 1350,
                'care_level' => 'medium',
                'care_cycle_days' => 7,
                'stock_quantity' => 8,
                'is_featured' => false,
                'description' => 'Tiểu cảnh Phật giáo cao cấp với không gian xanh rộng và các chi tiết được bố trí cân đối.',
                'concept_story' => 'Quan Âm Diệu Pháp hướng đến sự thanh tịnh, lòng từ bi và những giá trị tốt đẹp.',
            ],

            [
                'sku' => 'MG-042',
                'name' => 'Thích Ca An Nhiên',
                'ecosystem_type' => 'closed',
                'list_price' => 699000,
                'size' => 'large',
                'weight' => 1200,
                'care_level' => 'medium',
                'care_cycle_days' => 8,
                'stock_quantity' => 9,
                'is_featured' => false,
                'description' => 'Terrarium Phật giáo mang sắc xanh trầm, tạo cảm giác an nhiên và yên tĩnh.',
                'concept_story' => 'Thích Ca An Nhiên là lời nhắc về sự bình thản và cân bằng trong cuộc sống.',
            ],

            [
                'sku' => 'MG-043',
                'name' => 'Thích Ca Minh Tâm',
                'ecosystem_type' => 'closed',
                'list_price' => 749000,
                'size' => 'large',
                'weight' => 1300,
                'care_level' => 'medium',
                'care_cycle_days' => 8,
                'stock_quantity' => 8,
                'is_featured' => true,
                'description' => 'Tiểu cảnh cao cấp mang chủ đề Phật giáo với nhiều lớp cây xanh và chi tiết trang trí.',
                'concept_story' => 'Thích Ca Minh Tâm gửi gắm ý nghĩa giữ tâm sáng, sống an hòa và hướng đến điều thiện.',
            ],

            [
                'sku' => 'MG-044',
                'name' => 'Vạn An',
                'ecosystem_type' => 'closed',
                'list_price' => 629000,
                'size' => 'large',
                'weight' => 1100,
                'care_level' => 'easy',
                'care_cycle_days' => 10,
                'stock_quantity' => 10,
                'is_featured' => false,
                'description' => 'Tiểu cảnh xanh mang phong cách thanh tịnh, phù hợp làm quà tặng với lời chúc bình an.',
                'concept_story' => 'Vạn An gửi gắm mong ước về sự bình an lâu dài cho người nhận.',
            ],
        ];

        foreach ($products as $data) {

            /*
             * Yêu cầu ánh sáng theo loại hệ sinh thái.
             */
            $lightRequirement = match ($data['ecosystem_type']) {
                'closed' =>
                    'Ánh sáng gián tiếp, tránh ánh nắng trực tiếp.',

                'open' =>
                    'Ánh sáng tự nhiên nhẹ, đặt ở nơi thông thoáng.',

                'dry' =>
                    'Nơi sáng và thông thoáng, có thể nhận nắng nhẹ.',

                default =>
                    'Ánh sáng tự nhiên gián tiếp.',
            };

            /*
             * Hướng dẫn chăm sóc cơ bản theo loại hệ sinh thái.
             */
            $careInstructions = match ($data['ecosystem_type']) {
                'closed' =>
                    'Đặt sản phẩm ở nơi có ánh sáng gián tiếp. Theo dõi độ ẩm bên trong và chỉ bổ sung nước khi môi trường bắt đầu khô.',

                'open' =>
                    'Đặt ở nơi sáng và thông thoáng. Kiểm tra độ ẩm định kỳ, tưới lượng nước vừa phải và tránh để nước đọng.',

                'dry' =>
                    'Đặt ở nơi sáng, thông thoáng. Hạn chế tưới quá nhiều và đảm bảo giá thể khô tương đối giữa các lần tưới.',

                default =>
                    'Theo dõi độ ẩm và tình trạng cây thường xuyên.',
            };

            Product::updateOrCreate(
                [
                    'sku' => $data['sku'],
                ],
                array_merge(
                    [
                        'category_id' => $baseCategory->id,

                        'sale_price' => null,

                        'container_diameter_cm' => null,
                        'container_height_cm' => null,

                        'light_requirement' => $lightRequirement,

                        'care_instructions' => $careInstructions,

                        'low_stock_threshold' => 5,

                        'is_active' => true,

                        'is_featured' => false,

                        'is_grove_eligible' => true,

                        'is_warranty_eligible' => false,

                        'warranty_days' => null,

                        'warranty_policy' => null,

                        'is_studio_item' => false,

                        'product_type' => null,

                        'studio_capacity' => null,
                    ],
                    $data
                )
            );
        }
    }
}