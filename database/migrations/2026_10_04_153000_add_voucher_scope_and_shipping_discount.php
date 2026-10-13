<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // A. Bảng vouchers
        if (! Schema::hasColumn('vouchers', 'voucher_scope')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->string('voucher_scope', 20)
                    ->default('order')
                    ->after('code');
            });
        }

        if (! Schema::hasColumn('vouchers', 'is_public')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->boolean('is_public')
                    ->default(true)
                    ->after('is_active');
            });
        }

        // B. Bảng orders
        if (! Schema::hasColumn('orders', 'shipping_discount_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('shipping_discount_amount', 15, 2)
                    ->default(0)
                    ->after('discount_amount');
            });
        }

        // C. Bảng voucher_usages: Cột voucher_scope
        if (! Schema::hasColumn('voucher_usages', 'voucher_scope')) {
            Schema::table('voucher_usages', function (Blueprint $table) {
                $table->string('voucher_scope', 20)
                    ->default('order')
                    ->after('order_id');
            });
        }

        // C. Bảng voucher_usages: Xử lý indexes theo đúng thứ tự an toàn cho Foreign Key
        // Bước 1: Tạo unique mới trước để luôn có index cho foreign key order_id
        if (! $this->indexExists('voucher_usages', 'voucher_usages_order_scope_unique')) {
            Schema::table('voucher_usages', function (Blueprint $table) {
                $table->unique(
                    ['order_id', 'voucher_scope'],
                    'voucher_usages_order_scope_unique'
                );
            });
        }

        // Bước 2: Sau khi index mới đã tồn tại, mới drop unique cũ
        if ($this->indexExists('voucher_usages', 'voucher_usage_order_unique')) {
            Schema::table('voucher_usages', function (Blueprint $table) {
                $table->dropUnique('voucher_usage_order_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1 & 2. Kiểm tra nếu đã có đơn hàng sử dụng đồng thời nhiều voucher
        if (Schema::hasTable('voucher_usages')) {
            $hasMultipleVouchersPerOrder = DB::table('voucher_usages')
                ->select('order_id')
                ->groupBy('order_id')
                ->havingRaw('COUNT(*) > 1')
                ->exists();

            if ($hasMultipleVouchersPerOrder) {
                throw new RuntimeException('Không thể rollback migration voucher vì đã có đơn hàng sử dụng đồng thời nhiều voucher.');
            }

            // B. Giữ index cho foreign key theo đúng thứ tự:
            // 1. Tạo lại unique cũ trước nếu chưa có
            if (! $this->indexExists('voucher_usages', 'voucher_usage_order_unique')) {
                Schema::table('voucher_usages', function (Blueprint $table) {
                    $table->unique('order_id', 'voucher_usage_order_unique');
                });
            }

            // 2. Drop unique mới nếu tồn tại
            if ($this->indexExists('voucher_usages', 'voucher_usages_order_scope_unique')) {
                Schema::table('voucher_usages', function (Blueprint $table) {
                    $table->dropUnique('voucher_usages_order_scope_unique');
                });
            }

            // 3. Drop voucher_scope nếu cột tồn tại
            if (Schema::hasColumn('voucher_usages', 'voucher_scope')) {
                Schema::table('voucher_usages', function (Blueprint $table) {
                    $table->dropColumn('voucher_scope');
                });
            }
        }

        // C. Bảng orders
        if (Schema::hasColumn('orders', 'shipping_discount_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('shipping_discount_amount');
            });
        }

        // C. Bảng vouchers
        $voucherColumnsToDrop = [];
        if (Schema::hasColumn('vouchers', 'voucher_scope')) {
            $voucherColumnsToDrop[] = 'voucher_scope';
        }
        if (Schema::hasColumn('vouchers', 'is_public')) {
            $voucherColumnsToDrop[] = 'is_public';
        }

        if (! empty($voucherColumnsToDrop)) {
            Schema::table('vouchers', function (Blueprint $table) use ($voucherColumnsToDrop) {
                $table->dropColumn($voucherColumnsToDrop);
            });
        }
    }

    /**
     * Kiểm tra index tồn tại qua information_schema.statistics.
     */
    private function indexExists(
        string $table,
        string $index
    ): bool {
        return DB::table('information_schema.statistics')
            ->where(
                'table_schema',
                DB::connection()->getDatabaseName()
            )
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
