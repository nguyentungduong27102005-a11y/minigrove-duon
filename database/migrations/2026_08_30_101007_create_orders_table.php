<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_code', 50)->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Snapshot thông tin người nhận.
             * Không phụ thuộc vào việc User sửa địa chỉ sau này.
             */
            $table->string('customer_name', 100);

            $table->string('phone', 20);

            $table->string('email', 150);

            $table->text('shipping_address');

            /*
             * Tiền hàng trước giảm giá.
             */
            $table->decimal('subtotal', 15, 2);

            /*
             * Tổng số tiền được giảm.
             */
            $table->decimal('discount_amount', 15, 2)
                ->default(0);

            /*
             * Phí vận chuyển.
             */
            $table->decimal('shipping_fee', 15, 2)
                ->default(0);

            /*
             * Tổng thanh toán cuối cùng.
             */
            $table->decimal('total_amount', 15, 2);

            /*
             * COD
             * QR
             */
            $table->string('payment_method', 30);

            /*
             * pending
             * paid
             * failed
             * refunded
             */
            $table->string('payment_status', 30)
                ->default('pending');

            /*
             * pending
             * confirmed
             * preparing
             * packed
             * shipping
             * completed
             * cancelled
             */
            $table->string('order_status', 30)
                ->default('pending');

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};