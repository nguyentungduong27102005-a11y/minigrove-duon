<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * firstOrCreate để chỉ bổ sung phương thức bị thiếu.
         * Không ghi đè trạng thái hoặc thông tin ngân hàng
         * mà Admin đã cấu hình trước đó.
         */
        PaymentSetting::firstOrCreate(
            ['payment_method' => 'COD'],
            [
                'display_name' => 'Thanh toán khi nhận hàng (COD)',
                'is_enabled' => true,
                'bank_name' => null,
                'account_name' => null,
                'account_number' => null,
            ]
        );

        PaymentSetting::firstOrCreate(
            ['payment_method' => 'QR'],
            [
                'display_name' => 'Thanh toán QR qua PayOS',
                'is_enabled' => true,
                'bank_name' => null,
                'account_name' => null,
                'account_number' => null,
            ]
        );

        PaymentSetting::firstOrCreate(
            ['payment_method' => 'ONEPAY'],
            [
                'display_name' => 'Thanh toán qua OnePay',
                'is_enabled' => true,
                'bank_name' => null,
                'account_name' => null,
                'account_number' => null,
            ]
        );
    }
}
