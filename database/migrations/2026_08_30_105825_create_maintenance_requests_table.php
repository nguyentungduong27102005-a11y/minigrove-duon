<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();

            /*
             * My Grove cần bảo dưỡng.
             */
            $table->foreignId('grove_id')
                ->constrained('groves')
                ->restrictOnDelete();

            /*
             * CLEANING       = vệ sinh bình
             * PRUNING        = cắt tỉa
             * REPLACE_PLANT  = thay cây
             * REPLACE_SOIL   = thay nền
             * REDESIGN       = thiết kế lại
             */
            $table->string('service_type', 50);

            /*
             * Ngày Customer gửi yêu cầu.
             */
            $table->timestamp('requested_at');

            /*
             * Ghi chú / yêu cầu cụ thể.
             */
            $table->text('note')
                ->nullable();

            /*
             * Danh sách hình ảnh Customer gửi.
             */
            $table->json('image_paths')
                ->nullable();

            /*
             * pending
             * confirmed
             * processing
             * completed
             * cancelled
             */
            $table->string('status', 30)
                ->default('pending');

            /*
             * Staff/Admin tiếp nhận yêu cầu.
             */
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Chi phí Staff báo cho Customer.
             *
             * NULL = chưa có báo giá / miễn phí.
             */
            $table->decimal('quoted_price', 15, 2)
                ->nullable();

            /*
             * Ghi chú về báo giá.
             */
            $table->text('quote_note')
                ->nullable();

            /*
             * Thời điểm báo giá được xác nhận.
             */
            $table->timestamp('confirmed_at')
                ->nullable();

            /*
             * Bắt đầu xử lý.
             */
            $table->timestamp('processing_at')
                ->nullable();

            /*
             * Hoàn thành.
             */
            $table->timestamp('completed_at')
                ->nullable();

            /*
             * Hủy yêu cầu.
             */
            $table->timestamp('cancelled_at')
                ->nullable();

            /*
             * Kết quả xử lý / ghi chú của Staff.
             */
            $table->text('staff_note')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['grove_id', 'status'],
                'maintenance_grove_status_index'
            );

            $table->index(
                ['handled_by', 'status'],
                'maintenance_handler_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};