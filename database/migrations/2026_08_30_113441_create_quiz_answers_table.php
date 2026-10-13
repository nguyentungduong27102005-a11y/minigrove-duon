<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();

            /*
             * Quiz Session chứa câu trả lời.
             */
            $table->foreignId('quiz_session_id')
                ->constrained('quiz_sessions')
                ->cascadeOnDelete();

            /*
             * Tiêu chí GroveMatch.
             *
             * STYLE
             * CARE_TIME
             * SIZE
             * LOCATION
             * BUDGET
             */
            $table->string('criterion', 50);

            /*
             * Giá trị Customer đã chọn.
             *
             * Dùng JSON để hỗ trợ cả:
             * - giá trị đơn
             * - khoảng giá
             * - dữ liệu mở rộng
             */
            $table->json('answer_value');

            /*
             * Nhãn hiển thị của câu trả lời.
             *
             * Ví dụ:
             * "Ít thời gian chăm sóc"
             * "Bàn làm việc"
             * "Dưới 500.000đ"
             */
            $table->string('answer_label', 150)
                ->nullable();

            $table->timestamps();

            /*
             * Mỗi Session chỉ có một câu trả lời
             * cho mỗi tiêu chí.
             */
            $table->unique(
                ['quiz_session_id', 'criterion'],
                'quiz_answer_session_criterion_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};