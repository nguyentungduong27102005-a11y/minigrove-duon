<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            /*
             * Tiêu đề bài viết.
             */
            $table->string('title', 180);

            /*
             * Dùng cho URL.
             *
             * Ví dụ:
             * cach-cham-soc-reu
             */
            $table->string('slug', 200)
                ->unique();

            /*
             * Nhóm/chủ đề bài viết.
             *
             * Ví dụ:
             * TERRARIUM_BASIC
             * CARE
             * MOLD
             * LIGHT
             * WATERING
             */
            $table->string('topic', 80)
                ->nullable();

            /*
             * Mô tả ngắn để hiển thị ngoài danh sách.
             */
            $table->text('excerpt')
                ->nullable();

            /*
             * Nội dung bài viết.
             */
            $table->longText('content');

            /*
             * Ảnh đại diện bài viết.
             */
            $table->string('cover_image')
                ->nullable();

            /*
             * draft
             * published
             * hidden
             */
            $table->string('status', 30)
                ->default('draft');

            /*
             * Staff/Admin tạo bài.
             */
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Thời điểm công khai.
             *
             * Dùng DATETIME để tương thích
             * với MySQL hiện tại của dự án.
             */
            $table->dateTime('published_at')
                ->nullable();

            /*
             * Staff/Admin ẩn bài.
             */
            $table->foreignId('hidden_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('hidden_at')
                ->nullable();

            /*
             * Lý do ẩn / ghi chú kiểm duyệt.
             */
            $table->text('moderation_note')
                ->nullable();

            /*
             * Cho phép đánh dấu bài nổi bật.
             */
            $table->boolean('is_featured')
                ->default(false);

            $table->timestamps();

            $table->index(
                ['status', 'published_at'],
                'article_status_published_index'
            );

            $table->index(
                ['topic', 'status'],
                'article_topic_status_index'
            );

            $table->index(
                ['is_featured', 'status'],
                'article_featured_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};