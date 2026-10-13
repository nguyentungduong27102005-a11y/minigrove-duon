<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            /*
             * User nhận thông báo.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * ORDER
             * CARE
             * GROVE
             * REVIEW
             * SYSTEM
             */
            $table->string('type', 50);

            /*
             * Tiêu đề thông báo.
             */
            $table->string('title', 150);

            /*
             * Nội dung thông báo.
             */
            $table->text('message');

            /*
             * Loại đối tượng liên quan.
             *
             * Ví dụ:
             * ORDER
             * GROVE
             * CARE_SCHEDULE
             * REVIEW
             */
            $table->string('reference_type', 50)
                ->nullable();

            /*
             * ID đối tượng liên quan.
             */
            $table->unsignedBigInteger('reference_id')
                ->nullable();

            /*
             * URL để User bấm vào thông báo.
             *
             * Ví dụ:
             * /orders/25
             * /my-grove/MG-2026-00125
             */
            $table->string('action_url')
                ->nullable();

            /*
             * Dữ liệu bổ sung nếu cần.
             */
            $table->json('data')
                ->nullable();

            /*
             * NULL = chưa đọc.
             * Có giá trị = đã đọc.
             */
            $table->timestamp('read_at')
                ->nullable();

            $table->timestamps();

            /*
             * Tối ưu truy vấn danh sách
             * thông báo chưa đọc của User.
             */
            $table->index(
                ['user_id', 'read_at'],
                'notification_user_read_index'
            );

            /*
             * Tìm thông báo theo nghiệp vụ liên quan.
             */
            $table->index(
                ['reference_type', 'reference_id'],
                'notification_reference_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};