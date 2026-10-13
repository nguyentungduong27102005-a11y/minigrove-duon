<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type', 30)
                ->nullable()
                ->after('ecosystem_type')
                ->index();

            $table->boolean('is_studio_item')
                ->default(false)
                ->after('product_type')
                ->index();

            $table->unsignedSmallInteger('studio_capacity')
                ->nullable()
                ->after('is_studio_item');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'product_type',
                'is_studio_item',
                'studio_capacity',
            ]);
        });
    }
};
