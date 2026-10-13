<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            /*
             * Conversation chứa Message.
             */
            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            /*
             * Người gửi:
             * Customer / Staff / Admin.
             */
            $table->foreignId('sender_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Nội dung tin nhắn.
             *
             * Nullable vì có thể gửi
             * chỉ hình ảnh/file.
             */
            $table->text('content')
                ->nullable();

            /*
             * TEXT
             * IMAGE
             * FILE
             * MIXED
             */
            $table->string('message_type', 30)
                ->default('TEXT');

            /*
             * Thời điểm gửi.
             */
            $table->dateTime('sent_at');

            /*
             * Thời điểm người nhận đã đọc.
             */
            $table->dateTime('read_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['conversation_id', 'sent_at'],
                'message_conversation_date_index'
            );

            $table->index(
                ['sender_id', 'sent_at'],
                'message_sender_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};