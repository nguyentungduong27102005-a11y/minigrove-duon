<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReturnRequestService
{
    /**
     * Tạo yêu cầu trả hàng / hoàn tiền.
     *
     * $quantities:
     *
     * [
     *     order_detail_id => quantity,
     * ]
     */
    public function create(
        User $user,
        Order $order,
        array $quantities,
        string $reason,
        ?string $description = null
    ): ReturnRequest {

        return DB::transaction(
            function () use (
                $user,
                $order,
                $quantities,
                $reason,
                $description
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
                | CHỈ COMPLETED MỚI TRẢ HÀNG
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedOrder->order_status
                    !== 'completed'
                ) {

                    throw ValidationException::withMessages([
                        'order' =>
                            'Chỉ đơn hàng đã hoàn thành mới có thể yêu cầu trả hàng / hoàn tiền.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | LỌC QUANTITY ĐƯỢC CHỌN
                |--------------------------------------------------------------------------
                */

                $selectedQuantities =
                    collect($quantities)
                        ->map(
                            fn ($quantity) =>
                                (int) $quantity
                        )
                        ->filter(
                            fn ($quantity) =>
                                $quantity > 0
                        );


                if (
                    $selectedQuantities->isEmpty()
                ) {

                    throw ValidationException::withMessages([
                        'items' =>
                            'Vui lòng chọn ít nhất một sản phẩm cần trả.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | ORDER DETAILS
                |--------------------------------------------------------------------------
                */

                $details =
                    OrderDetail::query()
                        ->where(
                            'order_id',
                            $lockedOrder->id
                        )
                        ->whereIn(
                            'id',
                            $selectedQuantities
                                ->keys()
                                ->map(
                                    fn ($id) =>
                                        (int) $id
                                )
                                ->all()
                        )
                        ->get()
                        ->keyBy('id');


                if (
                    $details->count()
                    !==
                    $selectedQuantities->count()
                ) {

                    throw ValidationException::withMessages([
                        'items' =>
                            'Có sản phẩm không thuộc đơn hàng này.',
                    ]);
                }


                $returnItems =
                    collect();


                $grossRefundAmount =
                    0;


                /*
                |--------------------------------------------------------------------------
                | KIỂM TRA TỪNG ITEM
                |--------------------------------------------------------------------------
                */

                foreach (
                    $selectedQuantities
                    as $detailId => $quantity
                ) {

                    $detail =
                        $details->get(
                            (int) $detailId
                        );


                    if (!$detail) {

                        throw ValidationException::withMessages([
                            'items' =>
                                'Sản phẩm trả hàng không hợp lệ.',
                        ]);
                    }


                    /*
                     * Hiện mới hỗ trợ PRODUCT.
                     *
                     * CUSTOM_DESIGN sẽ xử lý
                     * theo chính sách riêng sau.
                     */
                    if (
                        $detail->item_type
                        !== 'PRODUCT'
                    ) {

                        throw ValidationException::withMessages([
                            'items' =>
                                'Sản phẩm thiết kế riêng chưa hỗ trợ trả hàng tự động.',
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SỐ LƯỢNG ĐÃ YÊU CẦU TRẢ TRƯỚC ĐÓ
                    |--------------------------------------------------------------------------
                    |
                    | rejected không tính,
                    | vì yêu cầu đó đã bị từ chối.
                    |
                    */

                    $alreadyRequested =
                        ReturnRequestItem::query()
                            ->where(
                                'order_detail_id',
                                $detail->id
                            )
                            ->whereHas(
                                'returnRequest',
                                function ($query) {

                                    $query->where(
                                        'status',
                                        '!=',
                                        'rejected'
                                    );
                                }
                            )
                            ->sum('quantity');


                    $remainingQuantity =
                        (int) $detail->quantity
                        - (int) $alreadyRequested;


                    if (
                        $remainingQuantity <= 0
                    ) {

                        throw ValidationException::withMessages([
                            'items' =>
                                'Sản phẩm '
                                . $detail->item_name
                                . ' đã được yêu cầu trả đủ số lượng.',
                        ]);
                    }


                    if (
                        $quantity >
                        $remainingQuantity
                    ) {

                        throw ValidationException::withMessages([
                            'items' =>
                                'Sản phẩm '
                                . $detail->item_name
                                . ' chỉ còn '
                                . $remainingQuantity
                                . ' sản phẩm có thể yêu cầu trả.',
                        ]);
                    }


                    $unitPrice =
                        (float)
                        $detail->unit_price;


                    $itemGross =
                        $unitPrice
                        * $quantity;


                    $grossRefundAmount +=
                        $itemGross;


                    $returnItems->push([
                        'detail' =>
                            $detail,

                        'quantity' =>
                            $quantity,

                        'unit_price' =>
                            $unitPrice,

                        'gross_amount' =>
                            $itemGross,
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | TÍNH TIỀN HOÀN
                |--------------------------------------------------------------------------
                |
                | Nếu Order từng dùng Voucher,
                | số tiền hoàn không được tính
                | theo giá gốc hoàn toàn.
                |
                | Discount được phân bổ
                | theo tỷ lệ giá trị sản phẩm trả.
                |
                | Shipping Fee hiện không hoàn.
                |
                */

                $orderSubtotal =
                    (float)
                    $lockedOrder->subtotal;


                $orderDiscount =
                    (float)
                    $lockedOrder->discount_amount;


                $discountShare =
                    $orderSubtotal > 0
                        ? (
                            $orderDiscount
                            * (
                                $grossRefundAmount
                                / $orderSubtotal
                            )
                        )
                        : 0;


                $refundAmount =
                    max(
                        0,
                        round(
                            $grossRefundAmount
                            - $discountShare,
                            2
                        )
                    );


                /*
                |--------------------------------------------------------------------------
                | CREATE RETURN REQUEST
                |--------------------------------------------------------------------------
                */

                $returnRequest =
                    ReturnRequest::create([
                        'return_code' =>
                            $this
                                ->generateReturnCode(),

                        'order_id' =>
                            $lockedOrder->id,

                        'user_id' =>
                            $user->id,

                        'reason' =>
                            $reason,

                        'description' =>
                            $description
                                ? trim($description)
                                : null,

                        'status' =>
                            'pending',

                        'refund_amount' =>
                            $refundAmount,

                        'processed_by' =>
                            null,

                        'processed_at' =>
                            null,

                        'processing_note' =>
                            null,
                    ]);


                /*
                |--------------------------------------------------------------------------
                | RETURN ITEMS
                |--------------------------------------------------------------------------
                */

                foreach (
                    $returnItems as $item
                ) {

                    /*
                     * Phân bổ Discount cho từng Item.
                     */
                    $itemDiscountShare =
                        $orderSubtotal > 0
                            ? (
                                $orderDiscount
                                * (
                                    $item[
                                        'gross_amount'
                                    ]
                                    / $orderSubtotal
                                )
                            )
                            : 0;


                    $itemRefund =
                        max(
                            0,
                            round(
                                $item[
                                    'gross_amount'
                                ]
                                - $itemDiscountShare,
                                2
                            )
                        );


                    ReturnRequestItem::create([
                        'return_request_id' =>
                            $returnRequest->id,

                        'order_detail_id' =>
                            $item[
                                'detail'
                            ]->id,

                        'quantity' =>
                            $item[
                                'quantity'
                            ],

                        'unit_price' =>
                            $item[
                                'unit_price'
                            ],

                        'refund_amount' =>
                            $itemRefund,
                    ]);
                }


                /*
                 * Đảm bảo tổng Refund
                 * khớp tổng Item sau làm tròn.
                 */
                $actualRefund =
                    (float)
                    $returnRequest
                        ->items()
                        ->sum(
                            'refund_amount'
                        );


                $returnRequest->refund_amount =
                    $actualRefund;

                $returnRequest->save();


                return $returnRequest->load([
                    'order',
                    'items.orderDetail',
                ]);
            }
        );
    }


    /**
     * Mã yêu cầu trả hàng.
     *
     * Ví dụ:
     * RTN-20260831-X7K29A
     */
    private function generateReturnCode(): string
    {
        do {

            $code =
                'RTN-'
                . now()->format('Ymd')
                . '-'
                . Str::upper(
                    Str::random(6)
                );

        } while (
            ReturnRequest::query()
                ->where(
                    'return_code',
                    $code
                )
                ->exists()
        );


        return $code;
    }
}