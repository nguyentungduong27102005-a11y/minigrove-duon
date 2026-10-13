<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\StockHistory;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnProcessingService
{
    /**
     * Pending -> Approved
     */
    public function approve(
        User $actor,
        ReturnRequest $returnRequest,
        ?string $note = null
    ): ReturnRequest {

        return $this->simpleTransition(
            $actor,
            $returnRequest,
            'pending',
            'approved',
            $note
        );
    }


    /**
     * Pending -> Rejected
     */
    public function reject(
        User $actor,
        ReturnRequest $returnRequest,
        string $note
    ): ReturnRequest {

        return $this->simpleTransition(
            $actor,
            $returnRequest,
            'pending',
            'rejected',
            $note
        );
    }


    /**
     * Approved -> Returning
     */
    public function markReturning(
        User $actor,
        ReturnRequest $returnRequest,
        ?string $note = null
    ): ReturnRequest {

        return $this->simpleTransition(
            $actor,
            $returnRequest,
            'approved',
            'returning',
            $note
        );
    }


    /**
     * Returning -> Received
     *
     * Tại bước này Admin xác nhận MiniGrove
     * đã nhận lại hàng.
     *
     * Không tự động hoàn toàn bộ về stock.
     * Admin chỉ định số lượng có thể nhập lại kho.
     */
    public function markReceived(
        User $actor,
        ReturnRequest $returnRequest,
        array $restockQuantities = [],
        ?string $note = null
    ): ReturnRequest {

        return DB::transaction(
            function () use (
                $actor,
                $returnRequest,
                $restockQuantities,
                $note
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK RETURN REQUEST
                |--------------------------------------------------------------------------
                */

                $lockedReturn =
                    ReturnRequest::query()
                        ->whereKey(
                            $returnRequest->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                $this->assertStatus(
                    $lockedReturn,
                    'returning'
                );


                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $order =
                    $lockedReturn
                        ->order()
                        ->lockForUpdate()
                        ->firstOrFail();


                if (
                    $order->order_status
                    !== 'completed'
                ) {

                    throw ValidationException::withMessages([
                        'return' =>
                            'Chỉ xử lý nhận hàng trả cho đơn đã hoàn thành.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | RETURN ITEMS
                |--------------------------------------------------------------------------
                */

                $items =
                    $lockedReturn
                        ->items()
                        ->with(
                            'orderDetail'
                        )
                        ->get();


                if ($items->isEmpty()) {

                    throw ValidationException::withMessages([
                        'return' =>
                            'Yêu cầu trả hàng không có sản phẩm.',
                    ]);
                }


                foreach (
                    $items as $item
                ) {

                    /*
                     * Key của restock_quantities
                     * là ReturnRequestItem ID.
                     */
                    $restockQuantity =
                        (int)
                        (
                            $restockQuantities[
                                $item->id
                            ]
                            ?? 0
                        );


                    if (
                        $restockQuantity < 0
                        ||
                        $restockQuantity
                            > $item->quantity
                    ) {

                        throw ValidationException::withMessages([
                            'restock' =>
                                'Số lượng nhập lại kho không hợp lệ.',
                        ]);
                    }


                    /*
                     * Không nhập lại kho
                     * nếu Admin chọn 0.
                     *
                     * Ví dụ:
                     * - hàng vỡ
                     * - cây hỏng
                     * - hàng không còn bán được
                     */
                    if (
                        $restockQuantity === 0
                    ) {

                        continue;
                    }


                    $detail =
                        $item
                            ->orderDetail;


                    if (!$detail) {

                        throw ValidationException::withMessages([
                            'return' =>
                                'Không tìm thấy OrderDetail của sản phẩm trả.',
                        ]);
                    }


                    /*
                     * Hiện giai đoạn này mới tự động
                     * xử lý stock cho PRODUCT.
                     */
                    if (
                        $detail->item_type
                            !== 'PRODUCT'
                        ||
                        !$detail->product_id
                    ) {

                        throw ValidationException::withMessages([
                            'restock' =>
                                'Sản phẩm tùy chỉnh chưa hỗ trợ nhập kho tự động.',
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | LOCK PRODUCT
                    |--------------------------------------------------------------------------
                    */

                    $product =
                        Product::query()
                            ->whereKey(
                                $detail->product_id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();


                    $quantityBefore =
                        (int)
                        $product
                            ->stock_quantity;


                    $quantityAfter =
                        $quantityBefore
                        +
                        $restockQuantity;


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE STOCK
                    |--------------------------------------------------------------------------
                    */

                    $product
                        ->stock_quantity =
                            $quantityAfter;

                    $product->save();


                    /*
                    |--------------------------------------------------------------------------
                    | STOCK HISTORY
                    |--------------------------------------------------------------------------
                    */

                    StockHistory::create([
                        'stock_type' =>
                            'PRODUCT',

                        'product_id' =>
                            $product->id,


                        'transaction_type' =>
                            'RETURN_RECEIVED',

                        'quantity_before' =>
                            $quantityBefore,

                        'quantity_change' =>
                            $restockQuantity,

                        'quantity_after' =>
                            $quantityAfter,

                        'order_id' =>
                            $order->id,

                        'updated_by' =>
                            $actor->id,

                        'note' =>
                            'Nhập lại kho từ yêu cầu trả hàng '
                            . $lockedReturn
                                ->return_code,
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE RETURN REQUEST
                |--------------------------------------------------------------------------
                */

                $lockedReturn->status =
                    'received';

                $lockedReturn->processed_by =
                    $actor->id;

                $lockedReturn->processed_at =
                    now();

                $lockedReturn->processing_note =
                    $this->appendNote(
                        $lockedReturn
                            ->processing_note,
                        $note
                    );

                $lockedReturn->save();


                return $lockedReturn
                    ->fresh([
                        'order',
                        'user',
                        'processor',
                        'items.orderDetail.product',
                    ]);
            }
        );
    }


    /**
     * Received -> Refunded
     *
     * Hoàn tiền cho yêu cầu.
     */
    public function refund(
        User $actor,
        ReturnRequest $returnRequest,
        ?string $note = null
    ): ReturnRequest {

        return DB::transaction(
            function () use (
                $actor,
                $returnRequest,
                $note
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK RETURN
                |--------------------------------------------------------------------------
                */

                $lockedReturn =
                    ReturnRequest::query()
                        ->whereKey(
                            $returnRequest->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                $this->assertStatus(
                    $lockedReturn,
                    'received'
                );


                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $order =
                    $lockedReturn
                        ->order()
                        ->lockForUpdate()
                        ->firstOrFail();


                if (
                    $order->order_status
                    !== 'completed'
                ) {

                    throw ValidationException::withMessages([
                        'refund' =>
                            'Chỉ hoàn tiền cho đơn đã hoàn thành.',
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
                            $order->id
                        )
                        ->latest('id')
                        ->lockForUpdate()
                        ->first();


                if (!$payment) {

                    throw ValidationException::withMessages([
                        'refund' =>
                            'Không tìm thấy Payment của đơn hàng.',
                    ]);
                }


                if (
                    $payment->status
                    !== 'paid'
                ) {

                    throw ValidationException::withMessages([
                        'refund' =>
                            'Payment hiện không ở trạng thái Paid.',
                    ]);
                }


                $refundAmount =
                    round(
                        (float)
                        $lockedReturn
                            ->refund_amount,
                        2
                    );


                if (
                    $refundAmount <= 0
                ) {

                    throw ValidationException::withMessages([
                        'refund' =>
                            'Số tiền hoàn không hợp lệ.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | TỔNG SỐ TIỀN ĐÃ HOÀN TRƯỚC ĐÓ
                |--------------------------------------------------------------------------
                */

                $alreadyRefunded =
                    (float)
                    ReturnRequest::query()
                        ->where(
                            'order_id',
                            $order->id
                        )
                        ->where(
                            'status',
                            'refunded'
                        )
                        ->sum(
                            'refund_amount'
                        );


                $totalRefundedAfter =
                    round(
                        $alreadyRefunded
                        +
                        $refundAmount,
                        2
                    );


                $paymentAmount =
                    round(
                        (float)
                        $payment->amount,
                        2
                    );


                /*
                 * Không được hoàn vượt quá
                 * số tiền Payment.
                 */
                if (
                    $totalRefundedAfter
                    >
                    $paymentAmount + 0.01
                ) {

                    throw ValidationException::withMessages([
                        'refund' =>
                            'Tổng số tiền hoàn vượt quá giá trị thanh toán.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | PAYMENT NOTE
                |--------------------------------------------------------------------------
                */

                $payment->note =
                    $this->appendNote(
                        $payment->note,
                        'Đã hoàn '
                        . number_format(
                            $refundAmount,
                            0,
                            ',',
                            '.'
                        )
                        . 'đ theo yêu cầu '
                        . $lockedReturn
                            ->return_code
                        . '.'
                    );


                /*
                |--------------------------------------------------------------------------
                | FULL REFUND
                |--------------------------------------------------------------------------
                |
                | Chỉ khi tổng tiền hoàn đạt toàn bộ
                | Payment thì mới chuyển Payment
                | sang refunded.
                |
                | Partial refund:
                | Payment vẫn Paid.
                | ReturnRequest mới là nơi ghi nhận
                | khoản tiền đã hoàn.
                |
                */

                if (
                    $totalRefundedAfter
                    >=
                    $paymentAmount - 0.01
                ) {

                    $payment->status =
                        'refunded';

                    $payment->refunded_at =
                        now();


                    $order->payment_status =
                        'refunded';

                    $order->save();
                }


                $payment->save();


                /*
                |--------------------------------------------------------------------------
                | RETURN REQUEST
                |--------------------------------------------------------------------------
                */

                $lockedReturn->status =
                    'refunded';

                $lockedReturn->processed_by =
                    $actor->id;

                $lockedReturn->processed_at =
                    now();

                $lockedReturn->processing_note =
                    $this->appendNote(
                        $lockedReturn
                            ->processing_note,
                        $note
                            ?: (
                                'Đã hoàn tiền '
                                . number_format(
                                    $refundAmount,
                                    0,
                                    ',',
                                    '.'
                                )
                                . 'đ.'
                            )
                    );

                $lockedReturn->save();


                return $lockedReturn
                    ->fresh([
                        'order',
                        'order.payments',
                        'user',
                        'processor',
                        'items.orderDetail.product',
                    ]);
            }
        );
    }


    /**
     * Transition đơn giản:
     *
     * pending -> approved
     * pending -> rejected
     * approved -> returning
     */
    private function simpleTransition(
        User $actor,
        ReturnRequest $returnRequest,
        string $expectedStatus,
        string $newStatus,
        ?string $note
    ): ReturnRequest {

        return DB::transaction(
            function () use (
                $actor,
                $returnRequest,
                $expectedStatus,
                $newStatus,
                $note
            ) {

                $lockedReturn =
                    ReturnRequest::query()
                        ->whereKey(
                            $returnRequest->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                $this->assertStatus(
                    $lockedReturn,
                    $expectedStatus
                );


                /*
                 * Order của Return Request
                 * phải vẫn là Completed.
                 */
                $order =
                    $lockedReturn
                        ->order()
                        ->lockForUpdate()
                        ->firstOrFail();


                if (
                    $order->order_status
                    !== 'completed'
                ) {

                    throw ValidationException::withMessages([
                        'return' =>
                            'Order liên quan không ở trạng thái Completed.',
                    ]);
                }


                $lockedReturn->status =
                    $newStatus;

                $lockedReturn->processed_by =
                    $actor->id;

                $lockedReturn->processed_at =
                    now();

                $lockedReturn->processing_note =
                    $this->appendNote(
                        $lockedReturn
                            ->processing_note,
                        $note
                    );

                $lockedReturn->save();


                return $lockedReturn
                    ->fresh([
                        'order',
                        'user',
                        'processor',
                        'items.orderDetail.product',
                    ]);
            }
        );
    }


    /**
     * Kiểm tra status hiện tại.
     */
    private function assertStatus(
        ReturnRequest $returnRequest,
        string $expectedStatus
    ): void {

        if (
            $returnRequest->status
            !== $expectedStatus
        ) {

            throw ValidationException::withMessages([
                'status' =>
                    'Không thể xử lý yêu cầu từ trạng thái '
                    . $returnRequest->status
                    . '.',
            ]);
        }
    }


    /**
     * Nối processing note mà không
     * xóa lịch sử ghi chú trước đó.
     */
    private function appendNote(
        ?string $currentNote,
        ?string $newNote
    ): ?string {

        $current =
            trim(
                (string)
                $currentNote
            );


        $new =
            trim(
                (string)
                $newNote
            );


        if (
            $new === ''
        ) {

            return $current !== ''
                ? $current
                : null;
        }


        if (
            $current === ''
        ) {

            return $new;
        }


        return $current
            . PHP_EOL
            . $new;
    }
}