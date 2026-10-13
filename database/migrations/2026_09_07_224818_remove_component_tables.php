<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Tắt kiểm tra Foreign Key
        |--------------------------------------------------------------------------
        */
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        /*
        |--------------------------------------------------------------------------
        | Xóa các bảng Component nếu còn tồn tại
        |--------------------------------------------------------------------------
        */
        $tables = [
            'product_components',
            'component_images',
            'component_compatibility_rules',
            'components',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Xóa Foreign Key và component_id
        |--------------------------------------------------------------------------
        */
        $columns = [
            'stock_histories',
            'care_rules',
            'rewards',
        ];

        foreach ($columns as $table) {

            if (!Schema::hasTable($table)) {
                continue;
            }

            if (!Schema::hasColumn($table, 'component_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {

                /*
                |--------------------------------------------------------------
                | Xóa Foreign Key trước
                |--------------------------------------------------------------
                */
                $foreignKeyName = $table . '_component_id_foreign';

                $blueprint->dropForeign($foreignKeyName);

                /*
                |--------------------------------------------------------------
                | Sau đó mới xóa component_id
                |--------------------------------------------------------------
                */
                $blueprint->dropColumn('component_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Bật lại kiểm tra Foreign Key
        |--------------------------------------------------------------------------
        */
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        //
    }
};