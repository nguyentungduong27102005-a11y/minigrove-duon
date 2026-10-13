<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_follows', function (Blueprint $table) {
            $table->id();

            /*
             * User thực hiện Follow.
             */
            $table->foreignId('follower_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * User được Follow.
             */
            $table->foreignId('following_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            /*
             * Không Follow cùng một User nhiều lần.
             */
            $table->unique(
                ['follower_id', 'following_id'],
                'user_follow_unique'
            );

            $table->index(
                'following_id',
                'user_following_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_follows');
    }
};