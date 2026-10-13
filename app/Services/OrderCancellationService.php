<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderCancellationService
{
    /**
     * Customer hủy Order.
     *
     * Chỉ được hủy khi Order = pending.
     */
    public function cancel(
    User $user,
    Order $order,
    ?string $reason = null,
    bool $system = false
): Order {

        return DB::transaction(
            function () use (
                $user,
                $order,
                $reason,
                $system
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder =
                    Order::query()
                        ->whereKey($order->id)
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | OWNERSHIP
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedOrder->user_id
                    !== $user->id
                ) {

                    abort(403);
                }


                /*
                |--------------------------------------------------------------------------
                | CHỈ PENDING MỚI ĐƯỢC HỦY
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedOrder->order_status
                    !== 'pending'
                ) {

                    throw ValidationException::withMessages([
                        'order' =>
                            'Đơn hàng không còn ở trạng thái có thể hủy.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | LOCK PAYMENT
                |--------------------------------------------------------------------------
                */

                $payment =
                    Payment::query()
                        ->where(
                            'order_id',
                            $lockedOrder->id
                        )
                        ->latest('id')
                        ->lockForUpdate()
                        ->first();


                /*
                 * Nếu Staff/Admin đã xác nhận Paid,
                 * Customer không tự hủy vì cần
                 * xử lý hoàn tiền nghiệp vụ.
                 */
                if (
                    $lockedOrder->payment_status
                    === 'paid'
                    ||
                    $payment?->status
                    === 'paid'
                ) {

                    throw ValidationException::withMessages([
                        'order' =>
                            'Đơn hàng đã được xác nhận thanh toán. Vui lòng liên hệ MiniGrove để được hỗ trợ hủy và hoàn tiền.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | LOAD ORDER DETAILS
                |--------------------------------------------------------------------------
                */

                $details =
                    $lockedOrder
                        ->details()
                        ->get();


                /*
                |--------------------------------------------------------------------------
                | HOÀN STOCK
                |--------------------------------------------------------------------------
                |
                | Order đang Pending và sau Transaction
                | sẽ chuyển sang Cancelled.
                |
                | Vì vậy cùng Order không thể chạy
                | hoàn Stock lần thứ hai.
                |
                */

                foreach ($details as $detail) {

                    if (
                        $detail->item_type
                        !== 'PRODUCT'
                    ) {
                        continue;
                    }


                    if (!$detail->product_id) {
                        continue;
                    }


                    /*
                     * Lock Product.
                     */
                    $product =
                        Product::query()
                            ->whereKey(
                                $detail->product_id
                            )
                            ->lockForUpdate()
                            ->first();


                    if (!$product) {

                        throw ValidationException::withMessages([
                            'stock' =>
                                'Không thể hoàn tồn kho vì sản phẩm trong đơn không còn tồn tại.',
                        ]);
                    }


                    $quantityBefore =
                        (int)
                        $product->stock_quantity;


                    $quantityRestore =
                        (int)
                        $detail->quantity;


                    $quantityAfter =
                        $quantityBefore
                        + $quantityRestore;


                    /*
                     * Hoàn Product Stock.
                     */
                    $product->stock_quantity =
                        $quantityAfter;

                    $product->save();


                    /*
                     * Ghi lịch sử Stock.
                     */
                    StockHistory::create([
                        'stock_type' =>
                            'PRODUCT',

                        'product_id' =>
    $product->id,

'transaction_type' =>
    'ORDER_CANCEL',

                        'quantity_before' =>
                            $quantityBefore,

                        'quantity_change' =>
                            $quantityRestore,

                        'quantity_after' =>
                            $quantityAfter,

                        'order_id' =>
                            $lockedOrder->id,

                        'updated_by' =>
                            null,

                        'note' =>
    (
        $system
            ? 'Hoàn tồn kho do hệ thống tự động hủy đơn '
            : 'Hoàn tồn kho do Customer hủy đơn '
    )
    . $lockedOrder->order_code,
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | RELEASE VOUCHERS (ORDER & SHIPPING)
                |--------------------------------------------------------------------------
                */

                $voucherUsages =
                    VoucherUsage::query()
                        ->where(
                            'order_id',
                            $lockedOrder->id
                        )
                        ->where(
                            'status',
                            'used'
                        )
                        ->lockForUpdate()
                        ->get();

                foreach ($voucherUsages as $voucherUsage) {
                    $voucherUsage->status =
                        'released';

                    $voucherUsage->released_at =
                        now();

                    $voucherUsage->save();

                    $voucher =
                        Voucher::query()
                            ->whereKey(
                                $voucherUsage->voucher_id
                            )
                            ->lockForUpdate()
                            ->first();

                    if ($voucher) {
                        $voucher->usage_count =
                            max(
                                0,
                                (int)
                                $voucher->usage_count
                                - 1
                            );

                        $voucher->save();
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | PAYMENT
                |--------------------------------------------------------------------------
                |
                | Payment schema hiện không có
                | trạng thái "cancelled".
                |
                | Với đơn chưa Paid,
                | đánh dấu failed để kết thúc
                | Payment chưa hoàn tất.
                |
                */

                if ($payment) {

                    if (
                        $payment->status
                        === 'pending'
                    ) {

                        $payment->status =
                            'failed';


                        $payment->note =
                            trim(
                                (
                                    $payment->note
                                    ? $payment->note . ' '
                                    : ''
                                )
                                . (
    $system
        ? 'Đơn hàng được hệ thống tự động hủy do thanh toán không hoàn tất.'
        : 'Đơn hàng đã bị Customer hủy trước khi thanh toán hoàn tất.'
)
                            );


                        $payment->save();
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder->order_status =
                    'cancelled';


                if (
                    $lockedOrder->payment_status
                    === 'pending'
                ) {

                    $lockedOrder->payment_status =
                        'failed';
                }


                $lockedOrder->save();


                /*
                |--------------------------------------------------------------------------
                | ORDER STATUS HISTORY
                |--------------------------------------------------------------------------
                */

                $description =
    $system
        ? 'Hệ thống tự động hủy đơn hàng.'
        : 'Customer đã hủy đơn hàng.';


                if (
                    $reason
                    && trim($reason) !== ''
                ) {

                    $description .=
                        ' Lý do: '
                        . trim($reason);
                }


                OrderStatusHistory::create([
                    'order_id' =>
                        $lockedOrder->id,

                    'status' =>
                        'cancelled',

                    'description' =>
                        $description,

                    /*
                     * Customer tự thao tác.
                     */
                    'updated_by' =>
    $system
        ? null
        : $user->id,
                ]);


                return $lockedOrder->fresh([
                    'details',
                    'payments',
                    'statusHistories',
                    'voucherUsage',
                ]);
            }
        );
    }
}