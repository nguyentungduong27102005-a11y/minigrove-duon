<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained('carts')
                ->cascadeOnDelete();

            /*
             * PRODUCT
             * CUSTOM_DESIGN
             */
            $table->string('item_type', 30);

            /*
             * Chỉ dùng khi item_type = PRODUCT
             */
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->cascadeOnDelete();

            /*
             * Chỉ dùng khi item_type = CUSTOM_DESIGN
             */
            $table->foreignId('custom_design_id')
                ->nullable()
                ->constrained('custom_designs')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            /*
             * Không cho cùng Product xuất hiện
             * hai lần trong một Cart.
             */
            $table->unique(
                ['cart_id', 'product_id'],
                'cart_product_unique'
            );

            /*
             * Không cho cùng Custom Design xuất hiện
             * hai lần trong một Cart.
             */
            $table->unique(
                ['cart_id', 'custom_design_id'],
                'cart_design_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};