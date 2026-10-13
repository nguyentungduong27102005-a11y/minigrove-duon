<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_participants', function (Blueprint $table) {
            $table->id();

            /*
             * Challenge được tham gia.
             */
            $table->foreignId('challenge_id')
                ->constrained('challenges')
                ->restrictOnDelete();

            /*
             * Customer tham gia.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Grove được dùng để tham gia Challenge.
             *
             * Có thể NULL nếu Challenge
             * không yêu cầu một Grove cụ thể.
             */
            $table->foreignId('grove_id')
                ->nullable()
                ->constrained('groves')
                ->restrictOnDelete();

            /*
             * joined
             * completed
             * withdrawn
             * disqualified
             */
            $table->string('status', 30)
                ->default('joined');

            /*
             * Thời điểm tham gia.
             *
             * Dùng DATETIME để tương thích
             * tốt với MySQL hiện tại.
             */
            $table->dateTime('joined_at');

            /*
             * Thời điểm hoàn thành.
             */
            $table->dateTime('completed_at')
                ->nullable();

            /*
             * Point Transaction nếu Challenge
             * này được thưởng Grove Point.
             *
             * NULL khi chưa thưởng.
             */
            $table->foreignId('point_transaction_id')
                ->nullable()
                ->constrained('point_transactions')
                ->restrictOnDelete();

            /*
             * Ghi chú nếu cần.
             */
            $table->text('note')
                ->nullable();

            $table->timestamps();

            /*
             * Một Customer chỉ tham gia
             * một Challenge một lần.
             */
            $table->unique(
                ['challenge_id', 'user_id'],
                'challenge_participant_unique'
            );

            /*
             * Một Point Transaction chỉ dùng
             * để thưởng một lượt Challenge.
             */
            $table->unique(
                'point_transaction_id',
                'challenge_point_transaction_unique'
            );

            /*
             * Tăng tốc truy vấn danh sách
             * participant theo Challenge + status.
             */
            $table->index(
                ['challenge_id', 'status'],
                'challenge_participant_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_participants');
    }
};