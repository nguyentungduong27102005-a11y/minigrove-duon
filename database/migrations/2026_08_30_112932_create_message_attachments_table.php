<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();

            /*
             * Message sở hữu Attachment.
             */
            $table->foreignId('message_id')
                ->constrained('messages')
                ->cascadeOnDelete();

            /*
             * Tên file gốc.
             */
            $table->string('original_name');

            /*
             * Đường dẫn file trong Storage.
             */
            $table->string('file_path');

            /*
             * MIME type.
             *
             * Ví dụ:
             * image/jpeg
             * image/png
             * application/pdf
             */
            $table->string('mime_type', 100);

            /*
             * Dung lượng file tính bằng byte.
             */
            $table->unsignedBigInteger('file_size');

            /*
             * IMAGE
             * FILE
             */
            $table->string('attachment_type', 30)
                ->default('FILE');

            $table->timestamps();

            $table->index(
                'message_id',
                'message_attachment_message_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};