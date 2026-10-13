<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();

            /*
             * PRODUCT
             */
            $table->string('stock_type', 30);

            /*
             * Sản phẩm liên quan đến biến động kho.
             */
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * IMPORT     = nhập kho
             * EXPORT     = xuất kho / bán hàng
             * ADJUSTMENT = điều chỉnh
             * RETURN     = hoàn kho
             */
            $table->string('transaction_type', 30);

            /*
             * Tồn trước khi thay đổi.
             */
            $table->unsignedInteger('quantity_before');

            /*
             * Số lượng thay đổi.
             *
             * Ví dụ:
             * nhập +10
             * bán   -2
             * hoàn  +2
             */
            $table->integer('quantity_change');

            /*
             * Tồn sau khi thay đổi.
             */
            $table->unsignedInteger('quantity_after');

            /*
             * Order liên quan nếu biến động kho
             * phát sinh từ mua/hủy đơn.
             */
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->restrictOnDelete();

            /*
             * User / Staff / Admin thực hiện.
             *
             * NULL nếu hệ thống tự động xử lý.
             */
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Lý do / ghi chú.
             */
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};