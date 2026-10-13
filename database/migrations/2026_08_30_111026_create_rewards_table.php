<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();

            /*
             * Tên phần thưởng.
             */
            $table->string('name', 150);

            /*
             * VOUCHER
             * PRODUCT
             * BENEFIT
             */
            $table->string('reward_type', 30);

            /*
             * Voucher liên quan nếu reward_type = VOUCHER.
             */
            $table->foreignId('voucher_id')
                ->nullable()
                ->constrained('vouchers')
                ->restrictOnDelete();

            /*
             * Product liên quan nếu reward_type = PRODUCT.
             */
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Số Grove Point cần để đổi.
             */
            $table->unsignedBigInteger('points_required');

            /*
             * Mô tả phần thưởng.
             */
            $table->text('description')
                ->nullable();

            /*
             * Số lượng phần thưởng có thể đổi.
             *
             * NULL = không giới hạn.
             */
            $table->unsignedInteger('stock_quantity')
                ->nullable();

            /*
             * Thời gian áp dụng.
             */
            $table->timestamp('start_at')
                ->nullable();

            $table->timestamp('end_at')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index(
                ['reward_type', 'is_active'],
                'reward_type_active_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};