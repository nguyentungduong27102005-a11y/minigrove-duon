<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QrPaymentService
{
    /**
     * Staff/Admin xác nhận
     * đã nhận được tiền QR.
     */
    public function confirm(
        User $actor,
        Order $order,
        ?string $transactionCode = null,
        ?string $note = null
    ): Order {

        return DB::transaction(
            function () use (
                $actor,
                $order,
                $transactionCode,
                $note
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder =
                    Order::query()
                        ->whereKey(
                            $order->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | CHỈ QR
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedOrder
                        ->payment_method
                    !== 'QR'
                ) {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Đơn hàng này không sử dụng thanh toán QR.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | ORDER ĐÃ HỦY
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedOrder
                        ->order_status
                    === 'cancelled'
                ) {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Không thể xác nhận thanh toán cho đơn đã hủy.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | PAYMENT
                |--------------------------------------------------------------------------
                */

                $payment =
                    Payment::query()
                        ->where(
                            'order_id',
                            $lockedOrder->id
                        )
                        ->where(
                            'payment_method',
                            'QR'
                        )
                        ->latest('id')
                        ->lockForUpdate()
                        ->first();


                if (!$payment) {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Không tìm thấy Payment QR của đơn hàng.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | ĐÃ PAID
                |--------------------------------------------------------------------------
                */

                if (
                    $payment->status
                    === 'paid'
                    ||
                    $lockedOrder
                        ->payment_status
                    === 'paid'
                ) {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Thanh toán QR đã được xác nhận trước đó.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | CHỈ PENDING ĐƯỢC CONFIRM
                |--------------------------------------------------------------------------
                */

                if (
                    $payment->status
                    !== 'pending'
                ) {

                    throw ValidationException::withMessages([
                        'payment' =>
                            'Payment hiện không ở trạng thái chờ xác nhận.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE PAYMENT
                |--------------------------------------------------------------------------
                */

                $payment->status =
                    'paid';

                $payment->confirmed_by =
                    $actor->id;

                $payment->paid_at =
                    now();


                if (
                    $transactionCode
                    &&
                    trim(
                        $transactionCode
                    ) !== ''
                ) {

                    $payment->transaction_code =
                        trim(
                            $transactionCode
                        );
                }


                $payment->note =
                    $this->appendNote(
                        $payment->note,
                        $note
                            && trim(
                                $note
                            ) !== ''
                                ? trim(
                                    $note
                                )
                                : 'Staff/Admin đã xác nhận nhận tiền QR.'
                    );


                $payment->save();


                /*
                |--------------------------------------------------------------------------
                | UPDATE ORDER PAYMENT STATUS
                |--------------------------------------------------------------------------
                */

                $lockedOrder->payment_status =
                    'paid';

                $lockedOrder->save();


                return $lockedOrder
                    ->fresh([
                        'payments',
                        'statusHistories',
                    ]);
            }
        );
    }


    private function appendNote(
        ?string $currentNote,
        string $newNote
    ): string {

        $currentNote =
            trim(
                (string)
                $currentNote
            );


        if (
            $currentNote === ''
        ) {

            return $newNote;
        }


        return $currentNote
            . ' '
            . $newNote;
    }
}