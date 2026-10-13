<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('green_return_requests', function (Blueprint $table) {
            $table->id();

            /*
             * Customer gửi Green Return.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * GLASS_CONTAINER = bình thủy tinh
             * POT             = chậu
             * ACCESSORY       = phụ kiện
             * OTHER           = loại khác nếu mở rộng
             */
            $table->string('item_type', 50);

            /*
             * Mô tả vật phẩm Customer gửi.
             */
            $table->text('description');

            /*
             * Tình trạng vật phẩm.
             *
             * Ví dụ:
             * còn tốt, trầy nhẹ, đã sử dụng...
             */
            $table->text('condition_description')
                ->nullable();

            /*
             * Hình ảnh vật phẩm.
             */
            $table->json('image_paths')
                ->nullable();

            /*
             * submitted
             * approved
             * rejected
             * received
             * rewarded
             */
            $table->string('status', 30)
                ->default('submitted');

            /*
             * Staff/Admin xử lý yêu cầu.
             */
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Ghi chú của Staff.
             */
            $table->text('staff_note')
                ->nullable();

            /*
             * Lý do từ chối.
             */
            $table->text('rejection_reason')
                ->nullable();

            /*
             * POINT
             * VOUCHER
             * BENEFIT
             *
             * NULL khi chưa phát thưởng.
             */
            $table->string('reward_type', 30)
                ->nullable();

            /*
             * Nếu thưởng Grove Point,
             * lưu giao dịch Point tương ứng.
             */
            $table->foreignId('point_transaction_id')
                ->nullable()
                ->constrained('point_transactions')
                ->restrictOnDelete();

            /*
             * Nếu thưởng Voucher,
             * lưu Voucher liên quan.
             */
            $table->foreignId('voucher_id')
                ->nullable()
                ->constrained('vouchers')
                ->restrictOnDelete();

            /*
             * Staff/Admin phát thưởng.
             */
            $table->foreignId('rewarded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Các mốc thời gian nghiệp vụ.
             */
            $table->timestamp('submitted_at');

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamp('received_at')
                ->nullable();

            $table->timestamp('rewarded_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['user_id', 'status'],
                'green_return_user_status_index'
            );

            $table->index(
                ['handled_by', 'status'],
                'green_return_handler_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('green_return_requests');
    }
};