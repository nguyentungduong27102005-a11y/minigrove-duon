<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'return_requests',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | MÃ YÊU CẦU
                |--------------------------------------------------------------------------
                |
                | Ví dụ:
                | RTN-20260831-A8K2XQ
                |
                */

                $table
                    ->string(
                        'return_code',
                        40
                    )
                    ->unique();


                /*
                |--------------------------------------------------------------------------
                | ORDER + CUSTOMER
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('order_id')
                    ->constrained('orders')
                    ->cascadeOnDelete();


                $table
                    ->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();


                /*
                |--------------------------------------------------------------------------
                | LÝ DO
                |--------------------------------------------------------------------------
                */

                $table
                    ->string(
                        'reason',
                        100
                    );


                $table
                    ->text(
                        'description'
                    )
                    ->nullable();


                /*
                |--------------------------------------------------------------------------
                | TRẠNG THÁI YÊU CẦU
                |--------------------------------------------------------------------------
                |
                | pending
                | approved
                | rejected
                | returning
                | received
                | refunded
                |
                */

                $table
                    ->enum(
                        'status',
                        [
                            'pending',
                            'approved',
                            'rejected',
                            'returning',
                            'received',
                            'refunded',
                        ]
                    )
                    ->default(
                        'pending'
                    );


                /*
                |--------------------------------------------------------------------------
                | TỔNG SỐ TIỀN DỰ KIẾN HOÀN
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal(
                        'refund_amount',
                        12,
                        2
                    )
                    ->default(0);


                /*
                |--------------------------------------------------------------------------
                | STAFF / ADMIN XỬ LÝ
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId(
                        'processed_by'
                    )
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();


                $table
                    ->timestamp(
                        'processed_at'
                    )
                    ->nullable();


                /*
                |--------------------------------------------------------------------------
                | GHI CHÚ XỬ LÝ
                |--------------------------------------------------------------------------
                */

                $table
                    ->text(
                        'processing_note'
                    )
                    ->nullable();


                $table->timestamps();


                /*
                |--------------------------------------------------------------------------
                | INDEX
                |--------------------------------------------------------------------------
                */

                $table->index([
                    'user_id',
                    'status',
                ]);

                $table->index([
                    'order_id',
                    'status',
                ]);
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'return_requests'
        );
    }
};