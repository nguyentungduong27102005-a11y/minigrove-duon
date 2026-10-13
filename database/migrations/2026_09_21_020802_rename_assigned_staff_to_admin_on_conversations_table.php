<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE conversations
            DROP FOREIGN KEY conversations_assigned_staff_id_foreign
        ");

        DB::statement("
            ALTER TABLE conversations
            DROP INDEX conversation_staff_status_index
        ");

        DB::statement("
            ALTER TABLE conversations
            CHANGE COLUMN assigned_staff_id assigned_admin_id BIGINT UNSIGNED NULL
        ");

        DB::statement("
            ALTER TABLE conversations
            ADD CONSTRAINT conversations_assigned_admin_id_foreign
            FOREIGN KEY (assigned_admin_id)
            REFERENCES users(id)
            ON DELETE SET NULL
        ");

        DB::statement("
            ALTER TABLE conversations
            ADD INDEX conversation_admin_status_index
            (assigned_admin_id, status)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE conversations
            DROP FOREIGN KEY conversations_assigned_admin_id_foreign
        ");

        DB::statement("
            ALTER TABLE conversations
            DROP INDEX conversation_admin_status_index
        ");

        DB::statement("
            ALTER TABLE conversations
            CHANGE COLUMN assigned_admin_id assigned_staff_id BIGINT UNSIGNED NULL
        ");

        DB::statement("
            ALTER TABLE conversations
            ADD CONSTRAINT conversations_assigned_staff_id_foreign
            FOREIGN KEY (assigned_staff_id)
            REFERENCES users(id)
            ON DELETE SET NULL
        ");

        DB::statement("
            ALTER TABLE conversations
            ADD INDEX conversation_staff_status_index
            (assigned_staff_id, status)
        ");
    }
};