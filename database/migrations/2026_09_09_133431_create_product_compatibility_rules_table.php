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

        Schema::create(
            'product_compatibility_rules',
            function (Blueprint $table) {


                $table->id();



                /*
                |--------------------------------------------------------------------------
                | Sản phẩm nguồn
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'source_product_id'
                )
                ->constrained(
                    'products'
                )
                ->cascadeOnDelete();



                /*
                |--------------------------------------------------------------------------
                | Sản phẩm đích
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'target_product_id'
                )
                ->constrained(
                    'products'
                )
                ->cascadeOnDelete();




                /*
                |--------------------------------------------------------------------------
                | Trạng thái tương thích
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_compatible'
                )
                ->default(true);



                /*
                |--------------------------------------------------------------------------
                | Lý do
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'reason'
                )
                ->nullable();




                /*
                |--------------------------------------------------------------------------
                | Kích hoạt
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_active'
                )
                ->default(true);



                $table->timestamps();




                /*
                |--------------------------------------------------------------------------
                | Không cho trùng cặp sản phẩm
                |--------------------------------------------------------------------------
                */

                $table->unique(
                    [
                        'source_product_id',
                        'target_product_id'
                    ],
                    'pcr_source_target_unique'
                );


            }
        );

    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists(
            'product_compatibility_rules'
        );

    }

};