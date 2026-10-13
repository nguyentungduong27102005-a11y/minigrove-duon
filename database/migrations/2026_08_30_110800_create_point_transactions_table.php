<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();

            /*
             * Customer nhận / sử dụng điểm.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * EARN
             * SPEND
             * ADJUSTMENT
             * REVERSAL
             */
            $table->string('transaction_type', 30);

            /*
             * Số điểm thay đổi.
             *
             * +100 = cộng 100
             * -50  = trừ 50
             */
            $table->bigInteger('points_change');

            /*
             * Số dư trước và sau giao dịch.
             */
            $table->unsignedBigInteger('balance_before');

            $table->unsignedBigInteger('balance_after');

            /*
             * Lý do giao dịch.
             *
             * Ví dụ:
             * Hoàn thành đơn hàng
             * Viết Review
             * Đổi Voucher
             */
            $table->string('reason', 255);

            /*
             * ORDER
             * REVIEW
             * GROWTH_JOURNAL
             * GREEN_RETURN
             * REWARD
             * ADMIN
             */
            $table->string('reference_type', 50)
                ->nullable();

            /*
             * ID của đối tượng liên quan.
             */
            $table->unsignedBigInteger('reference_id')
                ->nullable();

            /*
             * Mã sự kiện duy nhất chống cộng trùng.
             *
             * Ví dụ:
             * ORDER_COMPLETED:25
             * REVIEW_CREATED:12
             */
            $table->string('event_key', 150)
                ->nullable()
                ->unique();

            /*
             * Admin/Staff thực hiện nếu
             * đây là điều chỉnh thủ công.
             *
             * Với hệ thống tự động có thể NULL.
             */
            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Thời điểm nghiệp vụ xảy ra.
             */
            $table->timestamp('occurred_at');

            $table->timestamps();

            $table->index(
                ['user_id', 'occurred_at'],
                'point_transaction_user_date_index'
            );

            $table->index(
                ['reference_type', 'reference_id'],
                'point_transaction_reference_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};