<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('growth_journals', function (Blueprint $table) {
            $table->id();

            /*
             * Grove được ghi nhật ký.
             */
            $table->foreignId('grove_id')
                ->constrained('groves')
                ->restrictOnDelete();

            /*
             * Customer tạo Journal.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Ngày ghi nhận tình trạng Terrarium.
             */
            $table->dateTime('recorded_at');

            /*
             * Ảnh Terrarium tại thời điểm ghi nhận.
             */
            $table->string('image_path')
                ->nullable();

            /*
             * Mô tả sự phát triển/thay đổi.
             */
            $table->text('description');

            /*
             * Ghi chú thêm.
             */
            $table->text('note')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['grove_id', 'recorded_at'],
                'journal_grove_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('growth_journals');
    }
};