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
        Schema::create('custom_designs', function (Blueprint $table) {
            $table->id();

            // Customer sở hữu thiết kế
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Mã thiết kế riêng
            $table->string('design_code', 50)->unique();

            // Tên Customer đặt cho thiết kế
            $table->string('name', 150);

            /*
             * Phong cách thiết kế.
             * Ví dụ: natural, fairy, zen, tropical...
             */
            $table->string('style', 50)->nullable();

            /*
             * Giá tạm tính tại thời điểm lưu thiết kế.
             * Khi cho vào Cart/Checkout sẽ tính lại từ DB.
             */
            $table->decimal('estimated_price', 15, 2)->default(0);

            /*
             * Snapshot toàn bộ cấu hình tại thời điểm lưu.
             */
            $table->json('configuration_snapshot')->nullable();

            /*
             * saved     = đã lưu
             * archived  = đã ẩn/lưu trữ
             */
            $table->string('status', 30)->default('saved');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_designs');
    }
};