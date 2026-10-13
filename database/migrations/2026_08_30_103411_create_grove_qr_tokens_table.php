<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grove_qr_tokens', function (Blueprint $table) {
            $table->id();

            /*
             * Grove mà QR này đại diện.
             */
            $table->foreignId('grove_id')
                ->constrained('groves')
                ->restrictOnDelete();

            /*
             * Token ngẫu nhiên dùng trong URL QR.
             *
             * Không sử dụng grove_id trực tiếp.
             */
            $table->string('token', 100)
                ->unique();

            /*
             * active
             * revoked
             * inactive
             */
            $table->string('status', 30)
                ->default('active');

            /*
             * Đường dẫn ảnh QR nếu hệ thống
             * sinh file QR PNG/SVG.
             */
            $table->string('qr_code_path')
                ->nullable();

            /*
             * Staff/Admin sinh QR.
             *
             * Có thể NULL nếu hệ thống tự sinh.
             */
            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Thời điểm token bắt đầu có hiệu lực.
             */
            $table->timestamp('activated_at')
                ->nullable();

            /*
             * Khi QR bị thay thế hoặc thu hồi.
             */
            $table->timestamp('revoked_at')
                ->nullable();

            /*
             * Lý do thu hồi QR.
             */
            $table->string('revoke_reason', 255)
                ->nullable();

            $table->timestamps();

            /*
             * Hỗ trợ tìm QR theo Grove nhanh hơn.
             */
            $table->index(
                ['grove_id', 'status'],
                'gqt_grove_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grove_qr_tokens');
    }
};