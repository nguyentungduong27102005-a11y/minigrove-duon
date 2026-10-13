<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\User;
use App\Services\OrderCancellationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnePayController extends Controller
{
    /**
     * OnePay chuyển khách hàng về đây sau khi thanh toán.
     */
    public function return(
        Request $request,
        OrderCancellationService $cancellationService
    ) {
        $params = $request->query();

        /*
        |--------------------------------------------------------------------------
        | 1. Lấy SecureHash
        |--------------------------------------------------------------------------
        */

        $receivedHash =
            $params['vpc_SecureHash'] ?? '';

        if (! $receivedHash) {
            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Không nhận được chữ ký xác thực từ OnePay.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Tạo lại HashString
        |--------------------------------------------------------------------------
        */

        $hashParams = [];

        foreach ($params as $key => $value) {
            if (
                (
                    str_starts_with($key, 'vpc_') ||
                    str_starts_with($key, 'user_')
                )
                &&
                $key !== 'vpc_SecureHash'
            ) {
                $hashParams[$key] = $value;
            }
        }

        ksort($hashParams);

        $hashData = '';

        foreach ($hashParams as $key => $value) {
            $hashData .=
                $key.
                '='.
                $value.
                '&';
        }

        $hashData = rtrim(
            $hashData,
            '&'
        );

        /*
        |--------------------------------------------------------------------------
        | 3. Tạo lại SecureHash
        |--------------------------------------------------------------------------
        */

        $hashKey =
            (string) config('onepay.hash_key');

        $calculatedHash = strtoupper(
            hash_hmac(
                'sha256',
                $hashData,
                pack('H*', $hashKey)
            )
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Kiểm tra SecureHash
        |--------------------------------------------------------------------------
        */

        if (
            ! hash_equals(
                $calculatedHash,
                strtoupper($receivedHash)
            )
        ) {
            Log::warning(
                'OnePay SecureHash không hợp lệ.',
                [
                    'received' => $receivedHash,

                    'calculated' => $calculatedHash,

                    'hash_data' => $hashData,

                    'params' => $params,
                ]
            );

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Xác thực giao dịch OnePay không thành công.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Lấy thông tin giao dịch
        |--------------------------------------------------------------------------
        */

        $orderReference =
            $params['vpc_OrderInfo']
            ?? null;

        $transactionCode =
            $params['vpc_MerchTxnRef']
            ?? null;

        $responseCode =
            (string) (
                $params['vpc_TxnResponseCode']
                ?? ''
            );

        /*
        |--------------------------------------------------------------------------
        | 6. Kiểm tra dữ liệu bắt buộc
        |--------------------------------------------------------------------------
        */

        if (
            ! $orderReference ||
            ! $transactionCode
        ) {
            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Dữ liệu giao dịch OnePay không đầy đủ.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Kiểm tra OrderInfo
        |--------------------------------------------------------------------------
        |
        | OnePayService tạo:
        |
        | MG18
        | MG19
        | MG20
        |
        */

        if (
            ! preg_match(
                '/^MG([0-9]+)$/',
                $orderReference,
                $matches
            )
        ) {
            Log::warning(
                'OnePay OrderInfo không hợp lệ.',
                [
                    'order_info' => $orderReference,
                ]
            );

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Mã đơn hàng OnePay không hợp lệ.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Lấy Order
        |--------------------------------------------------------------------------
        */

        $orderId =
            (int) $matches[1];

        $order = Order::query()
            ->whereKey($orderId)
            ->first();

        if (! $order) {
            Log::warning(
                'Không tìm thấy Order OnePay.',
                [
                    'order_id' => $orderId,

                    'order_info' => $orderReference,

                    'transaction_code' => $transactionCode,
                ]
            );

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Không tìm thấy đơn hàng.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Tìm đúng Payment
        |--------------------------------------------------------------------------
        */

        $payment = Payment::query()
            ->where(
                'order_id',
                $order->id
            )
            ->where(
                'payment_method',
                'ONEPAY'
            )
            ->where(
                'transaction_code',
                $transactionCode
            )
            ->latest('id')
            ->first();

        if (! $payment) {
            Log::warning(
                'Không tìm thấy Payment OnePay tương ứng.',
                [
                    'order_id' => $order->id,

                    'order_code' => $order->order_code,

                    'transaction_code' => $transactionCode,
                ]
            );

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Không tìm thấy giao dịch OnePay tương ứng.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Kiểm tra số tiền
        |--------------------------------------------------------------------------
        */

        $returnedAmount =
            isset($params['vpc_Amount'])
                ? (
                    (int) $params['vpc_Amount']
                    / 100
                )
                : 0;

        $orderAmount =
            (float) $order->total_amount;

        if (
            abs(
                $returnedAmount -
                $orderAmount
            ) > 0.01
        ) {
            Log::warning(
                'OnePay amount không khớp.',
                [
                    'order_id' => $order->id,

                    'order_amount' => $orderAmount,

                    'onepay_amount' => $returnedAmount,

                    'transaction_code' => $transactionCode,
                ]
            );

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Số tiền giao dịch OnePay không khớp với đơn hàng.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 11. Giao dịch thành công
        |--------------------------------------------------------------------------
        |
        | vpc_TxnResponseCode = 0
        |
        */

        if ($responseCode === '0') {
            DB::transaction(
                function () use (
                    $order,
                    $payment,
                    $transactionCode
                ) {
                    /*
                    |--------------------------------------------------------------
                    | PAYMENT
                    |--------------------------------------------------------------
                    */

                    if (
                        $payment->status
                        !== 'paid'
                    ) {
                        $payment->update([
                            'status' => 'paid',

                            'transaction_code' => $transactionCode,

                            'paid_at' => now(),
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------
                    | ORDER
                    |--------------------------------------------------------------
                    */

                    $wasPending =
    $order->order_status === 'pending';

                    $order->forceFill([
                        'payment_status' => 'paid',

                        'order_status' => $wasPending
                                ? 'confirmed'
                                : $order->order_status,
                    ])->save();

                    if ($wasPending) {
                        OrderStatusHistory::create([
                            'order_id' => $order->id,

                            'status' => 'confirmed',

                            'description' => 'OnePay xác nhận thanh toán thành công. '
                                .'Đơn hàng được hệ thống tự động xác nhận.',

                            'updated_by' => null,
                        ]);
                    }
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Xóa Checkout Session
            |--------------------------------------------------------------------------
            */

            session()->forget([
                'checkout.mode',
                'checkout.cart_item_ids',
                'checkout.product_id',
                'checkout.quantity',
                'checkout.address_id',
                'checkout.order_voucher_code',
                'checkout.shipping_voucher_code',
                'checkout.voucher_code',
            ]);

            return redirect()
                ->route(
                    'checkout.success',
                    $order
                )
                ->with(
                    'success',
                    'Thanh toán OnePay thành công.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 12. Giao dịch OnePay thất bại
        |--------------------------------------------------------------------------
        |
        | Không chỉ đánh dấu Payment failed.
        |
        | Hệ thống sẽ:
        |
        | - Payment -> failed
        | - Order -> cancelled
        | - payment_status -> failed
        | - hoàn tồn kho
        | - release voucher
        | - ghi StockHistory
        | - ghi OrderStatusHistory
        |
        */

        Log::info(
            'OnePay giao dịch thất bại.',
            [
                'order_id' => $order->id,

                'response_code' => $responseCode,

                'transaction_code' => $transactionCode,

                'params' => $params,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Tìm Customer sở hữu Order
        |--------------------------------------------------------------------------
        */

        $customer = User::query()
            ->whereKey(
                $order->user_id
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Nếu Order vẫn Pending thì tự động hủy
        |--------------------------------------------------------------------------
        */

        if (
            $customer &&
            $order->order_status === 'pending' &&
            $order->payment_status !== 'paid' &&
            $payment->status !== 'paid'
        ) {
            try {
                $cancellationService->cancel(
                    $customer,
                    $order,
                    'Thanh toán OnePay không thành công. Mã lỗi: '
                    .(
                        $responseCode !== ''
                            ? $responseCode
                            : 'UNKNOWN'
                    ),
                    true
                );

            } catch (\Throwable $e) {
                /*
                |--------------------------------------------------------------------------
                | Nếu quá trình hủy phát sinh lỗi
                |--------------------------------------------------------------------------
                |
                | Không để Payment tiếp tục Pending.
                |
                */

                Log::error(
                    'Không thể tự động hủy Order sau khi OnePay thất bại.',
                    [
                        'order_id' => $order->id,

                        'transaction_code' => $transactionCode,

                        'response_code' => $responseCode,

                        'error' => $e->getMessage(),
                    ]
                );

                DB::transaction(
                    function () use (
                        $order,
                        $payment,
                        $transactionCode
                    ) {
                        if (
                            $payment->status
                            !== 'paid'
                        ) {
                            $payment->update([
                                'status' => 'failed',

                                'transaction_code' => $transactionCode,
                            ]);
                        }

                        if (
                            $order->payment_status
                            !== 'paid'
                        ) {
                            $order->update([
                                'payment_status' => 'failed',
                            ]);
                        }
                    }
                );
            }

        } else {
            /*
            |--------------------------------------------------------------------------
            | Fallback
            |--------------------------------------------------------------------------
            */

            DB::transaction(
                function () use (
                    $order,
                    $payment,
                    $transactionCode
                ) {
                    if (
                        $payment->status
                        !== 'paid'
                    ) {
                        $payment->update([
                            'status' => 'failed',

                            'transaction_code' => $transactionCode,
                        ]);
                    }

                    if (
                        $order->payment_status
                        !== 'paid'
                    ) {
                        $order->update([
                            'payment_status' => 'failed',
                        ]);
                    }
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Xóa Checkout Session khi giao dịch đã kết thúc
        |--------------------------------------------------------------------------
        */

        session()->forget([
            'checkout.mode',
            'checkout.cart_item_ids',
            'checkout.product_id',
            'checkout.quantity',
            'checkout.address_id',
            'checkout.order_voucher_code',
            'checkout.shipping_voucher_code',
            'checkout.voucher_code',
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with(
                'error',
                'Thanh toán OnePay không thành công. '
                .'Đơn hàng đã được hủy và tồn kho đã được hoàn lại. '
                .'Mã lỗi: '
                .(
                    $responseCode !== ''
                        ? $responseCode
                        : 'UNKNOWN'
                )
            );
    }
}
