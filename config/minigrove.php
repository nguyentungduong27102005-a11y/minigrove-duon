<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Thông tin thanh toán QR mô phỏng
    |--------------------------------------------------------------------------
    |
    | Đây là dữ liệu demo cho đồ án.
    | Sau này có thể thay bằng tài khoản thật
    | mà không cần sửa OrderService.
    |
    */

    'payment' => [

        'bank_name' =>
            env(
                'MINIGROVE_BANK_NAME',
                'MB Bank'
            ),

        'account_name' =>
            env(
                'MINIGROVE_ACCOUNT_NAME',
                'MINIGROVE'
            ),

        'account_number' =>
            env(
                'MINIGROVE_ACCOUNT_NUMBER',
                '0123456789'
            ),

    ],

];