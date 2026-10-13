<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groves', function (Blueprint $table) {
            $table->id();

            /*
             * Mã Grove duy nhất.
             *
             * Ví dụ:
             * MG-2026-00001
             */
            $table->string('grove_code', 50)->unique();

            /*
             * Customer sở hữu My Grove.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Terrarium này sinh ra từ OrderDetail nào.
             *
             * Một OrderDetail có thể sinh nhiều Grove
             * nếu quantity > 1.
             */
            $table->foreignId('order_detail_id')
                ->constrained('order_details')
                ->restrictOnDelete();

            /*
             * Tên Terrarium.
             */
            $table->string('terrarium_name', 150);

            /*
             * Ảnh đại diện.
             */
            $table->string('cover_image')
                ->nullable();

            /*
             * closed / open / dry
             */
            $table->string('ecosystem_type', 30);

            /*
             * Snapshot thành phần tại thời điểm mua.
             */
            $table->json('plant_snapshot')
                ->nullable();

            $table->json('moss_snapshot')
                ->nullable();

            $table->json('substrate_snapshot')
                ->nullable();

            $table->json('stone_snapshot')
                ->nullable();

            $table->json('accessory_snapshot')
                ->nullable();

            /*
             * easy / medium / high
             */
            $table->string('care_level', 30)
                ->nullable();

            /*
             * Ánh sáng phù hợp.
             */
            $table->string('light_requirement', 150)
                ->nullable();

            /*
             * Chu kỳ chăm sóc, tính theo ngày.
             */
            $table->unsignedInteger('care_cycle_days')
                ->nullable();

            /*
             * Tình trạng hiện tại.
             */
            $table->string('current_condition', 100)
                ->nullable();

            /*
             * pending_activation
             * active
             * needs_attention
             * archived
             */
            $table->string('status', 30)
                ->default('pending_activation');

            /*
             * Ngày Customer nhận Terrarium.
             */
            $table->timestamp('received_at')
                ->nullable();

            /*
             * Thời điểm Grove được kích hoạt.
             */
            $table->timestamp('activated_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groves');
    }
};