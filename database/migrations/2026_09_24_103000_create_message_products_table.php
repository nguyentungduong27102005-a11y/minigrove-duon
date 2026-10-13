<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('message_id')
                ->constrained('messages')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            /*
             * Snapshot thông tin tại thời điểm Admin gửi sản phẩm.
             * Nhờ vậy lịch sử chat vẫn giữ đúng tên/giá/ảnh đã gửi.
             */
            $table->string('product_name', 150);
            $table->string('product_sku', 50)->nullable();
            $table->decimal('list_price', 15, 2);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(
                ['message_id', 'sort_order'],
                'message_product_order_index'
            );
        });

        /*
         * Backfill các tin sản phẩm đã gửi theo định dạng URL cũ.
         * Không sửa nội dung Message gốc để rollback vẫn an toàn.
         */
        $legacyMessages = DB::table('messages')
            ->where(
                'content',
                'like',
                '%Sản phẩm MiniGrove gợi ý:%'
            )
            ->get([
                'id',
                'content',
            ]);

        foreach ($legacyMessages as $message) {
            preg_match_all(
                '~\/products\/(\d+)~',
                (string) $message->content,
                $matches
            );

            $productIds = array_slice(
                array_values(
                    array_unique(
                        array_map(
                            'intval',
                            $matches[1] ?? []
                        )
                    )
                ),
                0,
                3
            );

            if ($productIds === []) {
                continue;
            }

            $products = DB::table('products')
                ->whereIn('id', $productIds)
                ->get([
                    'id',
                    'name',
                    'sku',
                    'list_price',
                    'sale_price',
                ])
                ->keyBy('id');

            foreach ($productIds as $index => $productId) {
                $product = $products->get($productId);

                if (!$product) {
                    continue;
                }

                $imagePath = DB::table('product_images')
                    ->where('product_id', $productId)
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->value('image_path');

                DB::table('message_products')->insert([
                    'message_id' => $message->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'list_price' => $product->list_price,
                    'sale_price' => $product->sale_price,
                    'image_path' => $imagePath,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('message_products');
    }
};