<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            /*
             * pending
             * confirmed
             * preparing
             * packed
             * shipping
             * completed
             * cancelled
             */
            $table->string('status', 30);

            $table->text('description')->nullable();

            /*
             * User/Staff/Admin thực hiện cập nhật.
             * NULL nếu trạng thái được hệ thống tự tạo.
             */
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};