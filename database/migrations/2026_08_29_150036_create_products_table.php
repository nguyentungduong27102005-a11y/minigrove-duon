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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('name', 150);

            $table->string('sku', 50)->unique();

            // Loại hệ sinh thái: closed / open / dry
            $table->string('ecosystem_type', 20);

            // Giá niêm yết
            $table->decimal('list_price', 15, 2);

            // Giá khuyến mãi
            $table->decimal('sale_price', 15, 2)->nullable();

            // Nhóm kích thước: mini / medium / large...
            $table->string('size', 50)->nullable();

            // Kích thước bình, đơn vị cm
            $table->decimal('container_diameter_cm', 8, 2)->nullable();
            $table->decimal('container_height_cm', 8, 2)->nullable();

            // easy / medium / high
            $table->string('care_level', 20);

            $table->string('light_requirement', 150)->nullable();

            // Chu kỳ chăm sóc theo số ngày
            $table->unsignedInteger('care_cycle_days')->nullable();

            $table->text('description')->nullable();

            $table->text('concept_story')->nullable();

            $table->text('care_instructions')->nullable();

            $table->unsignedInteger('stock_quantity')->default(0);

            $table->boolean('is_active')->default(true);

            $table->boolean('is_featured')->default(false);

            // Product có tạo My Grove sau khi mua hay không
            $table->boolean('is_grove_eligible')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};