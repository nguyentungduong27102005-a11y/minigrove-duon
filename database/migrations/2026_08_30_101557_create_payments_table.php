<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete();

            /*
             * COD
             * QR
             */
            $table->string('payment_method', 30);

            $table->decimal('amount', 15, 2);

            /*
             * pending
             * paid
             * failed
             * refunded
             */
            $table->string('status', 30)
                ->default('pending');

            /*
             * Mã giao dịch/mã tham chiếu nếu có.
             */
            $table->string('transaction_code', 100)
                ->nullable();

            /*
             * Thông tin phục vụ QR mô phỏng.
             */
            $table->string('bank_name', 100)
                ->nullable();

            $table->string('account_name', 150)
                ->nullable();

            $table->string('account_number', 50)
                ->nullable();

            $table->string('transfer_content', 255)
                ->nullable();

            $table->string('qr_code_path')
                ->nullable();

            /*
             * Staff/Admin xác nhận thanh toán.
             */
            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('paid_at')
                ->nullable();

            $table->timestamp('refunded_at')
                ->nullable();

            $table->text('note')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};