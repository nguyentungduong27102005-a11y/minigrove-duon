<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grove_id')
                ->constrained('groves')
                ->restrictOnDelete();

            /*
             * Có thể NULL nếu Customer tự ghi
             * hoạt động chăm sóc ngoài lịch.
             */
            $table->foreignId('care_schedule_id')
                ->nullable()
                ->constrained('care_schedules')
                ->nullOnDelete();

            $table->string('care_type', 50);

            /*
             * Thời điểm thực tế thực hiện.
             */
            $table->dateTime('performed_at');

            $table->text('note')
                ->nullable();

            /*
             * Customer/Staff thực hiện.
             */
            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_logs');
    }
};