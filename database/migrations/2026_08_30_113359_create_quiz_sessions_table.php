<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_sessions', function (Blueprint $table) {
            $table->id();

            /*
             * Customer thực hiện GroveMatch.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * started
             * completed
             * abandoned
             */
            $table->string('status', 30)
                ->default('started');

            /*
             * Thời điểm bắt đầu Quiz.
             *
             * Dùng DATETIME để tương thích
             * với MySQL hiện tại.
             */
            $table->dateTime('started_at');

            /*
             * Thời điểm hoàn thành.
             */
            $table->dateTime('completed_at')
                ->nullable();

            /*
             * Product phù hợp nhất.
             *
             * Có thể NULL khi chưa hoàn thành Quiz
             * hoặc không tìm được Product phù hợp.
             */
            $table->foreignId('top_product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            /*
             * Điểm phù hợp cao nhất.
             *
             * Ví dụ:
             * 92.00 = 92%
             */
            $table->decimal('top_match_score', 5, 2)
                ->nullable();

            /*
             * Snapshot toàn bộ kết quả xếp hạng.
             *
             * Ví dụ:
             * [
             *   {
             *     "product_id": 1,
             *     "score": 92,
             *     "reasons": [...]
             *   }
             * ]
             */
            $table->json('result_snapshot')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['user_id', 'created_at'],
                'quiz_session_user_date_index'
            );

            $table->index(
                ['user_id', 'status'],
                'quiz_session_user_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_sessions');
    }
};