<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_requests', function (Blueprint $table) {
            $table->id();

            /*
             * Warranty được yêu cầu xử lý.
             */
            $table->foreignId('warranty_id')
                ->constrained('warranties')
                ->restrictOnDelete();

            /*
             * Customer mô tả vấn đề.
             */
            $table->string('issue_type', 100);

            $table->text('description');

            /*
             * Hình ảnh minh họa.
             */
            $table->json('image_paths')
                ->nullable();

            /*
             * pending
             * processing
             * approved
             * rejected
             * completed
             * cancelled
             */
            $table->string('status', 30)
                ->default('pending');

            /*
             * Staff/Admin xử lý.
             */
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Phản hồi/kết quả xử lý.
             */
            $table->text('staff_response')
                ->nullable();

            /*
             * Lý do từ chối nếu có.
             */
            $table->text('rejection_reason')
                ->nullable();

            $table->timestamp('processing_at')
                ->nullable();

            $table->timestamp('resolved_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['warranty_id', 'status'],
                'warranty_request_status_index'
            );

            $table->index(
                ['handled_by', 'status'],
                'warranty_handler_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_requests');
    }
};