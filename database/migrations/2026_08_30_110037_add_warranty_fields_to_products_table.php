<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            /*
             * Product có đủ điều kiện
             * bảo hành hay không.
             */
            $table->boolean('is_warranty_eligible')
                ->default(false)
                ->after('is_grove_eligible');

            /*
             * Thời hạn bảo hành tính theo ngày.
             *
             * Ví dụ:
             * 30
             * 60
             * 90
             */
            $table->unsignedInteger('warranty_days')
                ->nullable()
                ->after('is_warranty_eligible');

            /*
             * Nội dung/chính sách bảo hành.
             */
            $table->text('warranty_policy')
                ->nullable()
                ->after('warranty_days');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_warranty_eligible',
                'warranty_days',
                'warranty_policy',
            ]);
        });
    }
};