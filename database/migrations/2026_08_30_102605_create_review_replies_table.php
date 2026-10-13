<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('review_id')
                ->constrained('reviews')
                ->cascadeOnDelete();

            /*
             * Staff/Admin phản hồi.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('content');

            /*
             * Có thể ẩn phản hồi nếu cần.
             */
            $table->boolean('is_visible')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_replies');
    }
};