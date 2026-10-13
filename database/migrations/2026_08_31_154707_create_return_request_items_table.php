<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'return_request_items',
            function (Blueprint $table) {

                $table->id();


                /*
                |--------------------------------------------------------------------------
                | RETURN REQUEST
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId(
                        'return_request_id'
                    )
                    ->constrained(
                        'return_requests'
                    )
                    ->cascadeOnDelete();


                /*
                |--------------------------------------------------------------------------
                | ORDER DETAIL ĐƯỢC TRẢ
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId(
                        'order_detail_id'
                    )
                    ->constrained(
                        'order_details'
                    )
                    ->cascadeOnDelete();


                /*
                |--------------------------------------------------------------------------
                | SỐ LƯỢNG TRẢ
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedInteger(
                        'quantity'
                    );


                /*
                |--------------------------------------------------------------------------
                | GIÁ TẠI THỜI ĐIỂM ORDER
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal(
                        'unit_price',
                        12,
                        2
                    );


                /*
                |--------------------------------------------------------------------------
                | TIỀN HOÀN CỦA ITEM
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal(
                        'refund_amount',
                        12,
                        2
                    );


                $table->timestamps();


                /*
                 * Trong cùng một Return Request,
                 * một OrderDetail chỉ xuất hiện 1 lần.
                 */
                $table->unique(
                    [
                        'return_request_id',
                        'order_detail_id',
                    ],
                    'return_request_item_unique'
                );


                $table->index(
                    'order_detail_id'
                );
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'return_request_items'
        );
    }
};