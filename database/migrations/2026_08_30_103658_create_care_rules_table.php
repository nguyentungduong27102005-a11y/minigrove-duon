<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_rules', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);

            /*
             * ECOSYSTEM
             * PRODUCT
             */
            $table->string('source_type', 30);

            /*
             * Dùng khi source_type = ECOSYSTEM.
             *
             * Ví dụ:
             * closed
             * open
             * dry
             */
            $table->string('ecosystem_type', 30)
                ->nullable();

            /*
             * Dùng khi source_type = PRODUCT.
             */
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * WATER_CHECK
             * WATERING
             * HUMIDITY_CHECK
             * LIGHT_ADJUSTMENT
             * PRUNING
             * CLEANING
             * MOLD_CHECK
             * LEAF_CHECK
             */
            $table->string('task_type', 50);

            /*
             * Chu kỳ lặp lại theo ngày.
             */
            $table->unsignedInteger('interval_days');

            /*
             * Hướng dẫn thực hiện.
             */
            $table->text('instructions')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_rules');
    }
};