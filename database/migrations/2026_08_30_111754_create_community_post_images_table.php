<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_post_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('community_post_id')
                ->constrained('community_posts')
                ->cascadeOnDelete();

            $table->string('image_path');

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->index(
                ['community_post_id', 'sort_order'],
                'community_image_sort_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_post_images');
    }
};