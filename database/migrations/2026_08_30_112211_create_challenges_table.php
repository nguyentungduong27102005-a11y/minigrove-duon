<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();

            /*
             * Tên Challenge.
             * Ví dụ: 30 Days Terrarium Challenge
             */
            $table->string('title', 150);

            /*
             * Nội dung giới thiệu.
             */
            $table->text('description');

            /*
             * Hướng dẫn / thể lệ.
             */
            $table->text('rules')
                ->nullable();

            /*
             * Ảnh đại diện.
             */
            $table->string('cover_image')
                ->nullable();

            /*
             * Thời gian bắt đầu / kết thúc.
             *
             * Dùng DATETIME để tương thích
             * tốt với MySQL hiện tại.
             */
            $table->dateTime('start_at');

            $table->dateTime('end_at');

            /*
             * Grove Point thưởng khi hoàn thành.
             */
            $table->unsignedBigInteger('reward_points')
                ->nullable();

            /*
             * draft
             * active
             * ended
             * cancelled
             */
            $table->string('status', 30)
                ->default('draft');

            $table->boolean('is_active')
                ->default(true);

            /*
             * Staff/Admin tạo Challenge.
             */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['status', 'start_at', 'end_at'],
                'challenge_status_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};