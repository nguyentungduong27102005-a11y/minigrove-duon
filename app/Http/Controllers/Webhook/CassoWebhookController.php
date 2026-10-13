<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CassoWebhookController extends Controller
{
    /**
     * Nhận giao dịch từ Casso Webhook V2.
     */
    public function handle(Request $request): JsonResponse
    {
        $rawPayload =
    json_decode(
        $request->getContent(),
        true
    );

if (!is_array($rawPayload)) {
    return response()->json(
        [
            'success' => false,
            'message' => 'Payload Casso không hợp lệ.',
        ],
        400
    );
}
        /*
        |--------------------------------------------------------------------------
        | 1. KIỂM TRA CHỮ KÝ CASSO
        |--------------------------------------------------------------------------
        */

        $signature =
            (string) $request->header(
                'X-Casso-Signature',
                ''
            );

        $checksumKey =
            (string) config(
                'services.casso.webhook_secret',
                ''
            );

        if (
            $checksumKey === ''
            ||
            !$this->verifySignature(
    $signature,
    $rawPayload,
    $checksumKey
)
        ) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'Chữ ký Casso không hợp lệ.',
                ],
                401
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 2. ĐỌC GIAO DỊCH
        |--------------------------------------------------------------------------
        */

        $data = $rawPayload['data'] ?? null;

        if (!is_array($data)) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'Dữ liệu giao dịch không hợp lệ.',
                ],
                422
            );
        }


        $transactionId =
            isset($data['id'])
                ? (string) $data['id']
                : '';

        $reference =
            trim(
                (string) (
                    $data['reference']
                    ?? ''
                )
            );

        $description =
            trim(
                (string) (
                    $data['description']
                    ?? ''
                )
            );

        $amount =
            isset($data['amount'])
                ? (int) round(
                    (float) $data['amount']
                )
                : 0;


        /*
        |--------------------------------------------------------------------------
        | 3. KIỂM TRA DỮ LIỆU TỐI THIỂU
        |--------------------------------------------------------------------------
        */

        if (
            $transactionId === ''
            ||
            $amount <= 0
        ) {
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'Thiếu thông tin giao dịch Casso.',
                ],
                422
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 4. TÌM MÃ ĐƠN MINI GROVE TRONG NỘI DUNG
        |--------------------------------------------------------------------------
        |
        | Ví dụ:
        |
        | MINIGROVE MG-20260904-MMBRS5
        |
        */

        if (
    !preg_match(
        '/MG[-\s]?(\d{8})[-\s]?([A-Z0-9]+)/i',
        $description,
        $matches
    )
) {

            /*
             * Đây có thể là giao dịch ngân hàng khác
             * không thuộc MiniGrove.
             *
             * Trả 200 để Casso không gửi lại.
             */

            return response()->json([
                'success' => true,
                'ignored' => true,
                'message' =>
                    'Giao dịch không thuộc đơn hàng MiniGrove.',
            ]);
        }


        $orderCode =
    'MG-'
    . strtoupper($matches[1])
    . '-'
    . strtoupper($matches[2]);

        /*
        |--------------------------------------------------------------------------
        | 5. XỬ LÝ GIAO DỊCH
        |--------------------------------------------------------------------------
        */

        try {

            $result =
                DB::transaction(
                    function () use (
                        $transactionId,
                        $reference,
                        $amount,
                        $orderCode
                    ) {

                        /*
                         * Chống webhook bị gửi lại.
                         */

                        $processedPayment =
                            Payment::query()
                                ->select([
                                    'id',
                                    'casso_transaction_id',
                                ])
                                ->where(
                                    'casso_transaction_id',
                                    $transactionId
                                )
                                ->first();

                        if ($processedPayment) {
                            return 'duplicate';
                        }


                        /*
                         * Tìm Order theo mã đơn.
                         *
                         * Không load thông tin cá nhân Customer.
                         */

                        $order =
                            Order::query()
                                ->select([
                                    'id',
                                    'order_code',
                                    'total_amount',
                                    'payment_method',
                                    'payment_status',
                                    'order_status',
                                ])
                                ->where(
                                    'order_code',
                                    $orderCode
                                )
                                ->lockForUpdate()
                                ->first();

                        if (!$order) {
                            return 'order_not_found';
                        }


                        /*
                         * Chỉ Casso xử lý thanh toán QR.
                         */

                        if (
                            $order->payment_method
                            !== 'QR'
                        ) {
                            return 'not_qr';
                        }


                        /*
                         * Lấy Payment QR của Order.
                         */

                        $payment =
                            Payment::query()
                                ->select([
                                    'id',
                                    'order_id',
                                    'payment_method',
                                    'amount',
                                    'status',
                                    'transaction_code',
                                    'casso_transaction_id',
                                    'confirmed_by',
                                    'paid_at',
                                    'note',
                                ])
                                ->where(
                                    'order_id',
                                    $order->id
                                )
                                ->where(
                                    'payment_method',
                                    'QR'
                                )
                                ->latest('id')
                                ->lockForUpdate()
                                ->first();

                        if (!$payment) {
                            return 'payment_not_found';
                        }


                        /*
                         * Nếu Payment đã Paid từ trước,
                         * không xác nhận lại.
                         */

                        if (
                            $payment->status === 'paid'
                        ) {
                            return 'already_paid';
                        }


                        /*
                         * Đối chiếu chính xác số tiền.
                         */

                        $expectedAmount =
                            (int) round(
                                (float) $payment->amount
                            );

                        if (
                            $expectedAmount !== $amount
                        ) {
                            return 'amount_mismatch';
                        }


                        /*
                         * Payment hợp lệ.
                         *
                         * Casso tự động xác nhận.
                         * Không có Admin/Staff xác nhận tay.
                         */

                        $payment->status =
                            'paid';

                        $payment->casso_transaction_id =
                            $transactionId;

                        $payment->transaction_code =
                            $reference !== ''
                                ? $reference
                                : (
                                    'CASSO-'
                                    . $transactionId
                                );

                        $payment->confirmed_by =
                            null;

                        $payment->paid_at =
                            now();

                        $payment->note =
                            'Thanh toán QR được xác nhận '
                            . 'tự động qua Casso Webhook.';

                        $payment->save();


                        /*
                         * Đồng bộ trạng thái thanh toán Order.
                         *
                         * Không tự chuyển order_status.
                         * Staff vẫn xử lý nghiệp vụ đơn hàng.
                         */

                        $order->payment_status =
                            'paid';

                        $order->save();


                        return 'paid';
                    }
                );

        } catch (Throwable $e) {

            report($e);

            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'Không thể xử lý giao dịch.',
                ],
                500
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 6. RESPONSE
        |--------------------------------------------------------------------------
        */

        if ($result === 'paid') {
            return response()->json([
                'success' => true,
                'message' =>
                    'Thanh toán đã được xác nhận tự động.',
            ]);
        }


        /*
         * Các trường hợp không cần Casso retry.
         */

        return response()->json([
            'success' => true,
            'ignored' => true,
            'reason' => $result,
        ]);
    }


    /**
     * Xác thực chữ ký Webhook V2 của Casso.
     */
    private function verifySignature(
        string $signatureHeader,
        array $webhookData,
        string $checksumKey
    ): bool {

        if ($signatureHeader === '') {
            return false;
        }


        /*
         * Header có dạng:
         *
         * t=1727948258788,v1=abcdef...
         */

        if (
            !preg_match(
                '/t=(\d+),v1=([a-f0-9]+)/i',
                $signatureHeader,
                $matches
            )
        ) {
            return false;
        }


        $timestamp =
            $matches[1];

        $receivedSignature =
            strtolower(
                $matches[2]
            );


        /*
         * Casso yêu cầu sort key A -> Z.
         */

        $sortedData =
            $this->sortByKeyRecursively(
                $webhookData
            );


        $json =
            json_encode(
                $sortedData,
                JSON_UNESCAPED_SLASHES
            );

        if ($json === false) {
            return false;
        }


        /*
         * Format:
         *
         * timestamp.JSON
         */

        $message =
            $timestamp
            . '.'
            . $json;


        /*
         * Casso Webhook V2:
         * HMAC SHA-512.
         */

        $expectedSignature =
            hash_hmac(
                'sha512',
                $message,
                $checksumKey
            );


        return hash_equals(
            $expectedSignature,
            $receivedSignature
        );
    }


    /**
     * Sort Object/Array theo key tăng dần.
     */
    private function sortByKeyRecursively(
        array $data
    ): array {

        ksort($data);

        foreach (
            $data as $key => $value
        ) {

            if (is_array($value)) {
                $data[$key] =
                    $this->sortByKeyRecursively(
                        $value
                    );
            }
        }

        return $data;
    }
}