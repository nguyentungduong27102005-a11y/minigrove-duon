<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | CASSO TRANSACTION ID
            |--------------------------------------------------------------------------
            |
            | ID giao dịch do Casso gửi về qua Webhook.
            |
            | unique giúp một giao dịch Casso
            | không thể được xử lý hai lần.
            |
            */

            $table->string(
                'casso_transaction_id',
                100
            )
                ->nullable()
                ->unique()
                ->after('transaction_code');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropUnique([
                'casso_transaction_id',
            ]);

            $table->dropColumn(
                'casso_transaction_id'
            );
        });
    }
};