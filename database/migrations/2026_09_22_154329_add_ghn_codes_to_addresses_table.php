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
    Schema::table('addresses', function (Blueprint $table) {
        $table->unsignedBigInteger('ghn_district_id')
            ->nullable()
            ->after('district');

        $table->string('ghn_ward_code', 20)
            ->nullable()
            ->after('ward');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('addresses', function (Blueprint $table) {
        $table->dropColumn([
            'ghn_district_id',
            'ghn_ward_code',
        ]);
    });
}
};
