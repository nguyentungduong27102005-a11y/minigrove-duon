<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();

            /*
             * Customer đăng bài.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Nội dung bài viết.
             */
            $table->text('content');

            /*
             * Cho phép Staff/Admin ẩn bài vi phạm
             * thay vì xóa cứng ngay.
             */
            $table->boolean('is_visible')
                ->default(true);

            /*
             * Người kiểm duyệt/ẩn bài.
             */
            $table->foreignId('hidden_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('hidden_at')
                ->nullable();

            /*
             * Lý do kiểm duyệt.
             */
            $table->text('moderation_note')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['is_visible', 'created_at'],
                'community_post_visible_date_index'
            );

            $table->index(
                ['user_id', 'created_at'],
                'community_post_user_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_posts');
    }
};