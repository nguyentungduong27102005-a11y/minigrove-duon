<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_design_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('custom_design_id')
                ->constrained('custom_designs')
                ->cascadeOnDelete();

            /*
             * Product được sử dụng trong Custom Design.
             */
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Snapshot thông tin Product
             * tại thời điểm thiết kế được lưu.
             */
            $table->string('product_name', 150);

            $table->decimal('unit_price', 15, 2);

            $table->unsignedInteger('quantity')->default(1);

            $table->decimal('subtotal', 15, 2);

            $table->timestamps();

            /*
             * Một Product chỉ xuất hiện một lần
             * trong cùng một Custom Design.
             */
            $table->unique(
                ['custom_design_id', 'product_id'],
                'cdi_design_product_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_design_items');
    }
};