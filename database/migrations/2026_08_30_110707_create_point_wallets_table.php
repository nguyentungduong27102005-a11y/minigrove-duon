<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_wallets', function (Blueprint $table) {
            $table->id();

            /*
             * Mỗi User chỉ có một ví Grove Point.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Số điểm hiện tại.
             * Không được âm.
             */
            $table->unsignedBigInteger('balance')
                ->default(0);

            /*
             * Tổng điểm đã từng kiếm được.
             */
            $table->unsignedBigInteger('total_earned')
                ->default(0);

            /*
             * Tổng điểm đã sử dụng.
             */
            $table->unsignedBigInteger('total_spent')
                ->default(0);

            $table->timestamps();

            $table->unique(
                'user_id',
                'point_wallet_user_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_wallets');
    }
};