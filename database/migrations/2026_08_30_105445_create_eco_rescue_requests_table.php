<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eco_rescue_requests', function (Blueprint $table) {
            $table->id();

            /*
             * My Grove đang gặp vấn đề.
             */
            $table->foreignId('grove_id')
                ->constrained('groves')
                ->restrictOnDelete();

            /*
             * Ví dụ:
             *
             * YELLOW_LEAVES
             * DRY_MOSS
             * MOLD
             * EXCESS_WATER
             * ABNORMAL_GROWTH
             * OTHER
             */
            $table->string('issue_type', 50);

            /*
             * Customer mô tả tình trạng.
             */
            $table->text('description');

            /*
             * Customer muốn MiniGrove hỗ trợ gì.
             */
            $table->text('support_need')
                ->nullable();

            /*
             * Danh sách đường dẫn hình ảnh.
             *
             * Ví dụ:
             * [
             *   "eco-rescue/image1.jpg",
             *   "eco-rescue/image2.jpg"
             * ]
             */
            $table->json('image_paths')
                ->nullable();

            /*
             * pending
             * processing
             * resolved
             * cancelled
             */
            $table->string('status', 30)
                ->default('pending');

            /*
             * Staff phụ trách xử lý.
             */
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Nội dung tư vấn / kết quả xử lý.
             */
            $table->text('staff_response')
                ->nullable();

            /*
             * Thời điểm Staff bắt đầu xử lý.
             */
            $table->timestamp('processing_at')
                ->nullable();

            /*
             * Thời điểm hoàn tất.
             */
            $table->timestamp('resolved_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['grove_id', 'status'],
                'eco_rescue_grove_status_index'
            );

            $table->index(
                ['handled_by', 'status'],
                'eco_rescue_handler_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eco_rescue_requests');
    }
};