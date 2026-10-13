<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();

            /*
             * Mã đổi thưởng duy nhất.
             *
             * Ví dụ:
             * RDM-2026-00001
             */
            $table->string('redemption_code', 50)
                ->unique();

            /*
             * Customer đổi thưởng.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Reward được đổi.
             */
            $table->foreignId('reward_id')
                ->constrained('rewards')
                ->restrictOnDelete();

            /*
             * PointTransaction đã trừ điểm.
             *
             * Một Redemption tương ứng
             * một giao dịch trừ điểm.
             */
            $table->foreignId('point_transaction_id')
                ->constrained('point_transactions')
                ->restrictOnDelete();

            /*
             * Snapshot số điểm đã dùng.
             */
            $table->unsignedBigInteger('points_spent');

            /*
             * Snapshot Reward tại thời điểm đổi.
             */
            $table->json('reward_snapshot')
                ->nullable();

            /*
             * pending
             * approved
             * fulfilled
             * cancelled
             */
            $table->string('status', 30)
                ->default('pending');

            /*
             * Staff/Admin xử lý nếu Reward
             * cần giao phụ kiện hoặc xác nhận.
             */
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Thời điểm Customer đổi điểm.
             */
            $table->timestamp('redeemed_at');

            /*
             * Thời điểm hoàn tất trao Reward.
             */
            $table->timestamp('fulfilled_at')
                ->nullable();

            $table->text('note')
                ->nullable();

            $table->timestamps();

            /*
             * Một PointTransaction không được
             * gắn cho nhiều Redemption.
             */
            $table->unique(
                'point_transaction_id',
                'redemption_point_transaction_unique'
            );

            $table->index(
                ['user_id', 'status'],
                'redemption_user_status_index'
            );

            $table->index(
                ['reward_id', 'status'],
                'redemption_reward_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_redemptions');
    }
};