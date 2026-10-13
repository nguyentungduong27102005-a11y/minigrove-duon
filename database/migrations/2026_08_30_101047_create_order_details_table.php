<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            /*
             * PRODUCT
             * CUSTOM_DESIGN
             */
            $table->string('item_type', 30);

            /*
             * Dùng khi item_type = PRODUCT
             */
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Dùng khi item_type = CUSTOM_DESIGN
             */
            $table->foreignId('custom_design_id')
                ->nullable()
                ->constrained('custom_designs')
                ->restrictOnDelete();

            /*
             * Snapshot tên item tại thời điểm mua.
             */
            $table->string('item_name', 150);

            /*
             * SKU Product hoặc design_code.
             */
            $table->string('item_code', 100);

            /*
             * Giá tại thời điểm mua.
             */
            $table->decimal('unit_price', 15, 2);

            $table->unsignedInteger('quantity');

            $table->decimal('subtotal', 15, 2);

            /*
             * Snapshot dữ liệu Product / Custom Design.
             */
            $table->json('item_snapshot')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};