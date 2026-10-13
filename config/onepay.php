<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OnePay TEST Configuration
    |--------------------------------------------------------------------------
    */

    'merchant' => env('ONEPAY_MERCHANT', 'TESTONEPAY'),

    'access_code' => env(
        'ONEPAY_ACCESS_CODE',
        '6BEB2546'
    ),

    'hash_key' => env(
        'ONEPAY_HASH_KEY'
    ),

    'url' => env(
        'ONEPAY_URL',
        'https://mtf.onepay.vn/paygate/vpcpay.op'
    ),

    'version' => '2',

    'command' => 'pay',

    'locale' => 'vn',

];