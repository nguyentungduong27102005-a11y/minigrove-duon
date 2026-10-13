<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::updateOrCreate(
            [
                'code' => 'WELCOME10',
            ],
            [
                'discount_type' => 'percentage',
                'discount_value' => 10,

                'min_order_amount' => 300000,

                'max_discount_amount' => 100000,

                'start_at' => '2026-01-01 00:00:00',
                'end_at' => '2026-12-31 23:59:59',

                'usage_limit' => 100,

                'usage_count' => 0,

                'per_user_limit' => 1,

                'is_active' => true,
            ]
        );

        Voucher::updateOrCreate(
            [
                'code' => 'MINIGROVE50',
            ],
            [
                'discount_type' => 'fixed',
                'discount_value' => 50000,

                'min_order_amount' => 500000,

                'max_discount_amount' => null,

                'start_at' => '2026-01-01 00:00:00',
                'end_at' => '2026-12-31 23:59:59',

                'usage_limit' => 50,

                'usage_count' => 0,

                'per_user_limit' => 1,

                'is_active' => true,
            ]
        );

        Voucher::updateOrCreate(
            [
                'code' => 'GREEN15',
            ],
            [
                'discount_type' => 'percentage',
                'discount_value' => 15,

                'min_order_amount' => 800000,

                'max_discount_amount' => 150000,

                'start_at' => '2026-01-01 00:00:00',
                'end_at' => '2026-12-31 23:59:59',

                'usage_limit' => 30,

                'usage_count' => 0,

                'per_user_limit' => 1,

                'is_active' => true,
            ]
        );
    }
}