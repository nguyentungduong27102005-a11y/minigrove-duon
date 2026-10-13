<?php

namespace App\Services;

use App\Models\Order;
use RuntimeException;

class OnePayService
{
    protected string $merchant;
    protected string $accessCode;
    protected string $hashKey;
    protected string $url;

    public function __construct()
    {
        $this->merchant = config('onepay.merchant');
        $this->accessCode = config('onepay.access_code');
        $this->hashKey = config('onepay.hash_key');
        $this->url = config('onepay.url');
    }

    /**
     * Tạo URL thanh toán OnePay TEST.
     */
    public function createPaymentUrl(
        Order $order,
        string $returnUrl,
        ?string $callbackUrl = null
    ): string {

        /*
        |--------------------------------------------------------------------------
        | Tìm Payment ONEPAY đang chờ thanh toán
        |--------------------------------------------------------------------------
        */

        $payment = $order->payments()
            ->where('payment_method', 'ONEPAY')
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if (!$payment) {
            throw new RuntimeException(
                'Không tìm thấy Payment OnePay đang chờ thanh toán.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Số tiền thanh toán
        |--------------------------------------------------------------------------
        |
        | OnePay yêu cầu số tiền nhân 100.
        |
        | Ví dụ:
        | 558001 VND
        | =>
        | 55800100
        |
        */

        $amount = (int) round(
            ((float) $order->total_amount) * 100
        );

        /*
        |--------------------------------------------------------------------------
        | Mã đơn hàng gửi sang OnePay
        |--------------------------------------------------------------------------
        |
        | Không gửi trực tiếp order_code của MiniGrove.
        |
        | Ví dụ:
        |
        | Order ID = 10
        | =>
        | vpc_OrderInfo = MG10
        |
        */

        $orderReference = 'MG' . $order->id;

        /*
        |--------------------------------------------------------------------------
        | Mã giao dịch OnePay
        |--------------------------------------------------------------------------
        |
        | Phải khác nhau giữa các lần thanh toán.
        |
        | Ví dụ:
        |
        | MGPAY1017900902038
        |
        */

        $transactionRef =
            'MGPAY' .
            $order->id .
            time();

        /*
        |--------------------------------------------------------------------------
        | Lưu mã giao dịch vào Payment
        |--------------------------------------------------------------------------
        */

        $payment->update([
            'transaction_code' => $transactionRef,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tham số gửi sang OnePay
        |--------------------------------------------------------------------------
        */

        $params = [

            'vpc_Version' => '2',

            'vpc_Command' => 'pay',

            'vpc_Merchant' => $this->merchant,

            'vpc_AccessCode' => $this->accessCode,

            'vpc_MerchTxnRef' => $transactionRef,

            'vpc_OrderInfo' => $orderReference,

            'vpc_Amount' => $amount,

            'vpc_Currency' => 'VND',

            'vpc_ReturnURL' => $returnUrl,

            'vpc_Locale' => 'vn',

            'vpc_TicketNo' => request()->ip(),

            /*
             * Link quay lại nếu khách hàng
             * hủy hoặc quay lại Checkout.
             */
            'AgainLink' => url('/checkout'),

            /*
             * Tiêu đề giao dịch.
             */
            'Title' => 'MiniGrove',
        ];

        /*
        |--------------------------------------------------------------------------
        | Callback URL nếu có
        |--------------------------------------------------------------------------
        */

        if ($callbackUrl) {
            $params['vpc_CallbackURL'] = $callbackUrl;
        }

        /*
        |--------------------------------------------------------------------------
        | Loại bỏ giá trị rỗng
        |--------------------------------------------------------------------------
        */

        $params = array_filter(
            $params,
            fn ($value) =>
                $value !== null &&
                $value !== ''
        );

        /*
        |--------------------------------------------------------------------------
        | Sắp xếp tham số theo alphabet
        |--------------------------------------------------------------------------
        */

        ksort($params);

        /*
        |--------------------------------------------------------------------------
        | Tạo HashString
        |--------------------------------------------------------------------------
        |
        | Chỉ đưa các tham số bắt đầu bằng:
        |
        | vpc_
        | user_
        |
        | vào chuỗi hash.
        |
        */

        $hashData = '';

        foreach ($params as $key => $value) {

            if (
                str_starts_with($key, 'vpc_') ||
                str_starts_with($key, 'user_')
            ) {

                $hashData .=
                    $key .
                    '=' .
                    $value .
                    '&';
            }
        }

        $hashData = rtrim(
            $hashData,
            '&'
        );

        /*
        |--------------------------------------------------------------------------
        | Tạo SecureHash HMAC-SHA256
        |--------------------------------------------------------------------------
        |
        | Hash Key của OnePay là chuỗi HEX.
        | Cần chuyển sang binary bằng pack('H*', ...).
        |
        */

        $secureHash = strtoupper(
            hash_hmac(
                'sha256',
                $hashData,
                pack('H*', $this->hashKey)
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Thêm chữ ký vào request
        |--------------------------------------------------------------------------
        */

        $params['vpc_SecureHash'] = $secureHash;

        /*
        |--------------------------------------------------------------------------
        | Tạo URL thanh toán OnePay
        |--------------------------------------------------------------------------
        */

        return $this->url .
            '?' .
            http_build_query(
                $params,
                '',
                '&',
                PHP_QUERY_RFC3986
            );
    }
}