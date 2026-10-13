<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();

            /*
             * Mã bảo hành duy nhất.
             *
             * Ví dụ:
             * WR-2026-00001
             */
            $table->string('warranty_code', 50)
                ->unique();

            /*
             * Customer sở hữu bảo hành.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * OrderDetail phát sinh bảo hành.
             */
            $table->foreignId('order_detail_id')
                ->constrained('order_details')
                ->restrictOnDelete();

            /*
             * Product được bảo hành.
             */
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Ngày bắt đầu / hết hạn.
             */
            $table->date('start_date');
            $table->date('end_date');

            /*
             * active
             * expired
             * cancelled
             */
            $table->string('status', 30)
                ->default('active');

            /*
             * Snapshot nội dung chính sách
             * tại thời điểm tạo Warranty.
             */
            $table->text('warranty_content');

            $table->timestamps();

            /*
             * MVP: mỗi OrderDetail/Product
             * chỉ tạo một Warranty.
             */
            $table->unique(
                ['order_detail_id', 'product_id'],
                'warranty_order_product_unique'
            );

            $table->index(
                ['user_id', 'status'],
                'warranty_user_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};