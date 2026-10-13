<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_provider', 30)
                ->nullable()
                ->after('payment_method');

            $table->string('provider_payment_id', 100)
                ->nullable()
                ->unique()
                ->after('casso_transaction_id');

            $table->text('provider_checkout_url')
                ->nullable()
                ->after('provider_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(
                'payments_provider_payment_id_unique'
            );

            $table->dropColumn([
                'payment_provider',
                'provider_payment_id',
                'provider_checkout_url',
            ]);
        });
    }
};
