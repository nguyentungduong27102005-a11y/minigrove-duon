<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Component compatibility feature was removed.
        // Product compatibility is handled by
        // 2026_09_09_133431_create_product_compatibility_rules_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to rollback because the old component
        // compatibility table is no longer created.
    }
};