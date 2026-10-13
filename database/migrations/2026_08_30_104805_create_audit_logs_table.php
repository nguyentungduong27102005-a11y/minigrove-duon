<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            /*
             * User thực hiện thao tác.
             *
             * Có thể NULL nếu hệ thống tự động thực hiện.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Ví dụ:
             *
             * ORDER_STATUS_UPDATE
             * ORDER_STATUS_OVERRIDE
             * STOCK_ADJUSTMENT
             * QR_REVOKE
             * USER_LOCK
             * USER_UNLOCK
             * ROLE_CHANGE
             * PAYMENT_CONFIRM
             */
            $table->string('action', 100);

            /*
             * Đối tượng bị tác động.
             *
             * ORDER
             * PRODUCT
             * COMPONENT
             * GROVE
             * USER
             * PAYMENT
             * VOUCHER
             */
            $table->string('entity_type', 50);

            /*
             * ID đối tượng.
             *
             * Không tạo Foreign Key vì entity
             * có thể thuộc nhiều bảng khác nhau.
             */
            $table->unsignedBigInteger('entity_id')
                ->nullable();

            /*
             * Mô tả ngắn thao tác.
             */
            $table->text('description')
                ->nullable();

            /*
             * Snapshot dữ liệu trước thay đổi.
             */
            $table->json('old_values')
                ->nullable();

            /*
             * Snapshot dữ liệu sau thay đổi.
             */
            $table->json('new_values')
                ->nullable();

            /*
             * Thông tin phục vụ kiểm tra bảo mật.
             */
            $table->string('ip_address', 45)
                ->nullable();

            $table->text('user_agent')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['entity_type', 'entity_id'],
                'audit_entity_index'
            );

            $table->index(
                ['user_id', 'created_at'],
                'audit_user_date_index'
            );

            $table->index(
                'action',
                'audit_action_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};