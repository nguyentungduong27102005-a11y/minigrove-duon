<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grove_id')
                ->constrained('groves')
                ->restrictOnDelete();

            /*
             * Rule gốc tạo ra lịch này.
             * Nullable để vẫn giữ lịch sử nếu rule thay đổi/xóa.
             */
            $table->foreignId('care_rule_id')
                ->nullable()
                ->constrained('care_rules')
                ->nullOnDelete();

            $table->string('task_type', 50);

            /*
             * Ngày dự kiến thực hiện.
             */
            $table->dateTime('scheduled_at');

            /*
             * pending
             * done
             * skipped
             */
            $table->string('status', 30)
                ->default('pending');

            $table->text('note')
                ->nullable();

            /*
             * Thời điểm hoàn thành.
             */
            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['grove_id', 'status', 'scheduled_at'],
                'care_schedule_lookup_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_schedules');
    }
};