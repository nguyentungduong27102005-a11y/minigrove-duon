<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'payment_settings',
            function (Blueprint $table) {

                $table->id();

                /*
                 * COD
                 * QR
                 */
                $table->string(
                    'payment_method',
                    30
                )->unique();

                /*
                 * Tên hiển thị cho Customer.
                 */
                $table->string(
                    'display_name',
                    100
                );

                /*
                 * Admin bật / tắt phương thức.
                 */
                $table->boolean(
                    'is_enabled'
                )->default(true);

                /*
                 * Các trường chỉ dùng cho QR.
                 * COD sẽ để NULL.
                 */
                $table->string(
                    'bank_name',
                    100
                )->nullable();

                $table->string(
                    'account_name',
                    150
                )->nullable();

                $table->string(
                    'account_number',
                    50
                )->nullable();

                $table->timestamps();
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'payment_settings'
        );
    }
};