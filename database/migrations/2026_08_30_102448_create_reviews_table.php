<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            /*
             * Customer viết Review.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Dùng để xác minh Customer
             * thực sự đã mua Item này.
             *
             * Mỗi OrderDetail chỉ có tối đa 1 Review.
             */
            $table->foreignId('order_detail_id')
                ->constrained('order_details')
                ->restrictOnDelete();

            /*
             * PRODUCT
             * CUSTOM_DESIGN
             */
            $table->string('review_target', 30);

            /*
             * Có giá trị nếu review_target = PRODUCT.
             */
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Có giá trị nếu review_target = CUSTOM_DESIGN.
             */
            $table->foreignId('custom_design_id')
                ->nullable()
                ->constrained('custom_designs')
                ->restrictOnDelete();

            /*
             * Rating từ 1 đến 5.
             * Validation cụ thể sẽ làm ở phía Server.
             */
            $table->unsignedTinyInteger('rating');

            $table->text('comment')->nullable();

            /*
             * Customer chỉ được chỉnh sửa Review tối đa 1 lần.
             */
            $table->unsignedTinyInteger('edit_count')
                ->default(0);

            /*
             * false = Staff/Admin đã ẩn Review.
             */
            $table->boolean('is_visible')
                ->default(true);

            $table->timestamps();

            /*
             * Một OrderDetail chỉ được tạo một Review.
             */
            $table->unique(
                'order_detail_id',
                'review_order_detail_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};