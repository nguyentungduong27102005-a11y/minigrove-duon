<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('community_post_id')
                ->constrained('community_posts')
                ->cascadeOnDelete();

            /*
             * User viết bình luận.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('content');

            /*
             * Staff/Admin có thể ẩn bình luận
             * vi phạm thay vì xóa ngay.
             */
            $table->boolean('is_visible')
                ->default(true);

            $table->foreignId('hidden_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('hidden_at')
                ->nullable();

            $table->text('moderation_note')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['community_post_id', 'created_at'],
                'post_comment_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};