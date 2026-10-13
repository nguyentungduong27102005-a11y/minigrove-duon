<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();

            // Mã voucher
            $table->string('code', 50)->unique();

            /*
             * percentage = giảm %
             * fixed      = giảm số tiền cố định
             */
            $table->string('discount_type', 20);

            // Giá trị giảm
            $table->decimal('discount_value', 15, 2);

            // Giá trị đơn hàng tối thiểu
            $table->decimal('min_order_amount', 15, 2)
                ->default(0);

            /*
             * Giảm tối đa.
             * Chủ yếu dùng cho Voucher phần trăm.
             */
            $table->decimal('max_discount_amount', 15, 2)
                ->nullable();

            // Thời gian hiệu lực
            $table->dateTime('start_at');

            $table->dateTime('end_at');

            /*
             * Tổng số lượt sử dụng tối đa.
             * NULL = không giới hạn tổng lượt.
             */
            $table->unsignedInteger('usage_limit')
                ->nullable();

            // Tổng số lượt đã sử dụng
            $table->unsignedInteger('usage_count')
                ->default(0);

            /*
             * Giới hạn mỗi Customer.
             * NULL = không giới hạn riêng theo Customer.
             */
            $table->unsignedInteger('per_user_limit')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};