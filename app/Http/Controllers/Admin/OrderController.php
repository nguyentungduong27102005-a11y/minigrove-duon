<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Order;

use App\Services\OrderWorkflowService;


use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Danh sách Order cho Admin.
     *
     * Không lấy PII của Customer.
     */
    public function index(
        Request $request
    ): View {

        $status =
            $request->query(
                'status'
            );


        $paymentMethod =
            $request->query(
                'payment_method'
            );


        $keyword =
            trim(
                (string)
                $request->query(
                    'keyword',
                    ''
                )
            );


        $allowedStatuses = [
            'pending',
            'confirmed',
            'preparing',
            'packed',
            'shipping',
            'completed',
            'cancelled',
        ];


        $allowedPaymentMethods = [
            'COD',
            'QR',
        ];


        /*
        |--------------------------------------------------------------------------
        | QUERY AN TOÀN
        |--------------------------------------------------------------------------
        |
        | Không SELECT:
        | - customer_name
        | - phone
        | - email
        | - shipping_address
        | - note
        |
        | Không load relation user.
        |
        */

        $query =
            Order::query()
                ->select([
                    'id',
                    'order_code',
                    'user_id',
                    'total_amount',
                    'payment_method',
                    'payment_status',
                    'order_status',
                    'created_at',
                ])
                ->withCount(
                    'details'
                )
                ->latest('id');


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $status
            &&
            in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {

            $query->where(
                'order_status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER PAYMENT METHOD
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod
            &&
            in_array(
                $paymentMethod,
                $allowedPaymentMethods,
                true
            )
        ) {

            $query->where(
                'payment_method',
                $paymentMethod
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        |
        | Admin chỉ tìm bằng:
        | - Mã đơn
        | - Customer ID
        |
        */

        if (
            $keyword !== ''
        ) {

            $query->where(
                function ($query) use (
                    $keyword
                ) {

                    $query->where(
                        'order_code',
                        'like',
                        '%'
                        . $keyword
                        . '%'
                    );


                    if (
                        ctype_digit(
                            $keyword
                        )
                    ) {

                        $query->orWhere(
                            'user_id',
                            (int) $keyword
                        );
                    }
                }
            );
        }


        $orders =
            $query
                ->paginate(15)
                ->withQueryString();


        return view(
            'admin.orders.index',
            compact(
                'orders',
                'status',
                'paymentMethod',
                'keyword'
            )
        );
    }


    /**
     * Chi tiết Order.
     *
     * Không sử dụng Implicit Model Binding ở màn hình xem
     * để tránh tải PII từ bảng orders.
     */
    public function show(
        int $order
    ): View {

        /*
        |--------------------------------------------------------------------------
        | ORDER AN TOÀN
        |--------------------------------------------------------------------------
        */

        $order =
            Order::query()
                ->select([
                    'id',
                    'order_code',
                    'user_id',

                    'subtotal',
                    'discount_amount',
                    'shipping_fee',
                    'shipping_discount_amount',
                    'total_amount',

                    'payment_method',
                    'payment_status',
                    'order_status',

                    'created_at',
                ])
                ->with([

                    /*
                     * Snapshot sản phẩm.
                     */
                    'details.product.images',


                    /*
                     * Payment.
                     */
                    'payments' =>
                        function ($query) {

                            $query
                                ->latest('id');
                        },


                    /*
                     * Timeline.
                     */
                    'statusHistories' =>
                        function ($query) {

                            $query
                                ->orderBy(
                                    'created_at'
                                )
                                ->orderBy('id');
                        },


                    /*
                     * Voucher.
                     */
                    'voucherUsage.voucher',
                    'orderVoucherUsage.voucher',
                    'shippingVoucherUsage.voucher',
                    'voucherUsages.voucher',


                    /*
                     * Return:
                     * chỉ lấy dữ liệu quản trị cần thiết.
                     *
                     * Không lấy reason / description /
                     * processing_note.
                     */
                    'returnRequests' =>
                        function ($query) {

                            $query
                                ->select([
                                    'id',
                                    'order_id',
                                    'return_code',
                                    'status',
                                    'refund_amount',
                                    'created_at',
                                ])
                                ->latest('id');
                        },
                ])
                ->whereKey(
                    $order
                )
                ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | TRẠNG THÁI TIẾP THEO
        |--------------------------------------------------------------------------
        */

        $nextStatus =
            match (
                $order
                    ->order_status
            ) {

                'pending' =>
                    'confirmed',

                'confirmed' =>
                    'preparing',

                'preparing' =>
                    'packed',

                'packed' =>
                    'shipping',

                'shipping' =>
                    'completed',

                default =>
                    null,
            };


        return view(
            'admin.orders.show',
            compact(
                'order',
                'nextStatus'
            )
        );
    }


    /**
     * Chuyển trạng thái Order.
     */
    public function updateStatus(
        Request $request,
        Order $order,
        OrderWorkflowService $service
    ): RedirectResponse {

        $validated =
            $request->validate(
                [
                    'status' => [
                        'required',
                        'in:confirmed,preparing,packed,shipping,completed',
                    ],

                    'description' => [
                        'nullable',
                        'string',
                        'max:500',
                    ],
                ]
            );


        try {

            $service->updateStatus(
                $request->user(),
                $order,
                $validated[
                    'status'
                ],
                $validated[
                    'description'
                ]
                ?? null
            );

        } catch (
            ValidationException $e
        ) {

            return redirect()
                ->route(
                    'admin.orders.show',
                    $order->id
                )
                ->with(
                    'error',
                    $e
                        ->validator
                        ->errors()
                        ->first()
                );
        }


        return redirect()
            ->route(
                'admin.orders.show',
                $order->id
            )
            ->with(
                'success',
                'Trạng thái đơn hàng đã được cập nhật.'
            );
    }



}