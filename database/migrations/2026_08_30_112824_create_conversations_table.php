<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            /*
             * Customer tạo Conversation.
             */
            $table->foreignId('customer_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Staff được phân công hỗ trợ.
             *
             * Có thể NULL khi Conversation
             * mới tạo và chưa có Staff nhận.
             */
            $table->foreignId('assigned_staff_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Chủ đề tư vấn.
             *
             * GENERAL
             * PRODUCT
             * CARE
             * ECO_RESCUE
             * MAINTENANCE
             * WARRANTY
             * OTHER
             */
            $table->string('topic', 50)
                ->default('GENERAL');

            /*
             * Tiêu đề ngắn của cuộc trò chuyện.
             */
            $table->string('subject', 150)
                ->nullable();

            /*
             * open
             * waiting
             * closed
             */
            $table->string('status', 30)
                ->default('open');

            /*
             * Thời điểm có tin nhắn gần nhất.
             *
             * Dùng DATETIME để tránh lỗi TIMESTAMP
             * giống Challenge vừa gặp.
             */
            $table->dateTime('last_message_at')
                ->nullable();

            /*
             * Người đóng Conversation.
             */
            $table->foreignId('closed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('closed_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['customer_id', 'status'],
                'conversation_customer_status_index'
            );

            $table->index(
                ['assigned_staff_id', 'status'],
                'conversation_staff_status_index'
            );

            $table->index(
                'last_message_at',
                'conversation_last_message_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};