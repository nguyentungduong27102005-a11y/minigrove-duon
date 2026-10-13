<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('voucher_id')
                ->constrained('vouchers')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete();

            /*
             * Snapshot số tiền Voucher
             * thực tế giảm cho Order này.
             */
            $table->decimal('discount_amount', 15, 2);

            /*
             * used     = đang tính là đã sử dụng
             * released = đã hoàn lượt vì Order hủy hợp lệ
             */
            $table->string('status', 30)
                ->default('used');

            $table->timestamp('used_at')
                ->nullable();

            $table->timestamp('released_at')
                ->nullable();

            $table->timestamps();

            /*
             * MVP: mỗi Order tối đa một Voucher.
             */
            $table->unique(
                'order_id',
                'voucher_usage_order_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
    }
};