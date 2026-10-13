<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Services\OrderCancellationService;
use App\Services\OrderWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Đơn hàng của tôi.
     *
     * Giao diện chỉ có đúng 7 tab.
     */
    public function index(
        Request $request
    ): View {

        /*
        |--------------------------------------------------------------------------
        | 7 TAB CUSTOMER
        |--------------------------------------------------------------------------
        */

        $tabs = [
            'all' => 'Tất cả',

            'pending' => 'Chờ xác nhận',

            'preparing' => 'Đang chuẩn bị',

            'shipping' => 'Đang giao',

            'completed' => 'Hoàn thành',

            'cancelled' => 'Đã hủy',

            'returns' => 'Trả hàng / Hoàn tiền',
        ];

        $tab =
            $request->query(
                'tab',
                'all'
            );

        if (
            ! array_key_exists(
                $tab,
                $tabs
            )
        ) {

            $tab =
                'all';
        }

        /*
        |--------------------------------------------------------------------------
        | TAB RETURN
        |--------------------------------------------------------------------------
        */

        if (
            $tab === 'returns'
        ) {

            $returnRequests =
                ReturnRequest::query()
                    ->where(
                        'user_id',
                        $request
                            ->user()
                            ->id
                    )
                    ->with([
                        'order',

                        'items.orderDetail',
                    ])
                    ->latest('id')
                    ->paginate(10)
                    ->withQueryString();

            $orders =
                null;

            return view(
                'orders.index',
                compact(
                    'tabs',
                    'tab',
                    'orders',
                    'returnRequests'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ORDER QUERY
        |--------------------------------------------------------------------------
        */

        $query =
            Order::query()
                ->where(
                    'user_id',
                    $request
                        ->user()
                        ->id
                )
                ->with([
                    'details.product.images' => function ($query) {

                        $query
                            ->orderByDesc(
                                'is_primary'
                            )
                            ->orderBy(
                                'sort_order'
                            )
                            ->orderBy('id');
                    },
                ])
                ->withCount('details')
                ->latest('id');

        /*
        |--------------------------------------------------------------------------
        | FILTER THEO 7 TAB
        |--------------------------------------------------------------------------
        */

        switch ($tab) {

            case 'pending':

                $query->where(
                    'order_status',
                    'pending'
                );

                break;

                /*
                 * Gom:
                 *
                 * confirmed
                 * preparing
                 * packed
                 *
                 * thành một tab "Đang chuẩn bị".
                 */
            case 'preparing':

                $query->whereIn(
                    'order_status',
                    [
                        'confirmed',
                        'preparing',
                        'packed',
                    ]
                );

                break;

            case 'shipping':

                $query->where(
                    'order_status',
                    'shipping'
                );

                break;

            case 'completed':

                $query->where(
                    'order_status',
                    'completed'
                );

                break;

            case 'cancelled':

                $query->where(
                    'order_status',
                    'cancelled'
                );

                break;

            case 'all':
            default:

                /*
                 * Không filter.
                 */
                break;
        }

        $orders =
            $query
                ->paginate(10)
                ->withQueryString();

        $returnRequests =
            null;

        return view(
            'orders.index',
            compact(
                'tabs',
                'tab',
                'orders',
                'returnRequests'
            )
        );
    }

    /**
     * Chi tiết Order.
     */
    public function show(
        Request $request,
        Order $order
    ): View {

        /*
        |--------------------------------------------------------------------------
        | OWNERSHIP
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $order->user_id
                ===
            $request->user()->id,
            403
        );

        $order->load([

            /*
             * REVIEW CỦA TỪNG ORDER DETAIL
             *
             * Dùng để xác định:
             * - Sản phẩm chưa đánh giá
             * - Sản phẩm đã đánh giá
             */
            'details.review',

            'details.product.images' => function ($query) {

                $query
                    ->orderByDesc(
                        'is_primary'
                    )
                    ->orderBy(
                        'sort_order'
                    )
                    ->orderBy('id');
            },

            'payments' => function ($query) {

                $query
                    ->latest('id');
            },

            'statusHistories' => function ($query) {

                $query
                    ->orderBy(
                        'created_at'
                    )
                    ->orderBy('id');
            },

            'voucherUsage.voucher',

            'orderVoucherUsage.voucher',

            'shippingVoucherUsage.voucher',

            'voucherUsages.voucher',

            'returnRequests' => function ($query) {

                $query
                    ->latest('id');
            },
        ]);

        /*
         * Customer có được hủy không?
         */
        $canCancel =
            $order->order_status
            === 'pending'
            &&
            $order->payment_status
            !== 'paid';

        /*
         * Customer có được gửi
         * yêu cầu trả hàng không?
         */
        $canRequestReturn =
            $order->order_status
            === 'completed';

        return view(
            'orders.show',
            compact(
                'order',
                'canCancel',
                'canRequestReturn'
            )
        );
    }

    /**
     * Customer hủy đơn.
     */
    public function cancel(
        Request $request,
        Order $order,
        OrderCancellationService $service
    ): RedirectResponse {

        /*
         * Ownership vẫn kiểm tra
         * ở Controller trước.
         */
        abort_unless(
            $order->user_id
                ===
            $request->user()->id,
            403
        );

        $validated =
            $request->validate(
                [
                    'cancel_reason' => [
                        'nullable',
                        'string',
                        'max:500',
                    ],
                ]
            );

        try {

            $service->cancel(
                $request->user(),
                $order,
                $validated[
                    'cancel_reason'
                ]
                ?? null
            );

        } catch (
            ValidationException $e
        ) {

            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    $e->validator
                        ->errors()
                        ->first()
                );
        }

        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Đơn hàng đã được hủy thành công.'
            );
    }

    /**
     * Customer xác nhận đã nhận được hàng.
     */
    public function confirmReceived(
        Request $request,
        Order $order,
        OrderWorkflowService $workflowService
    ): RedirectResponse {

        /*
         * Ownership kiểm tra ở Controller trước.
         */
        abort_unless(
            $order->user_id
                ===
            $request->user()->id,
            403
        );

        /*
         * Chỉ cho phép khi order_status === 'shipping'.
         */
        if (
            $order->order_status
            !== 'shipping'
        ) {

            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Đơn hàng không ở trạng thái đang giao nên không thể xác nhận nhận hàng.'
                );
        }

        try {

            $workflowService->confirmReceivedByCustomer(
                $request->user(),
                $order
            );

        } catch (
            ValidationException $e
        ) {

            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    $e->validator
                        ->errors()
                        ->first()
                );

        } catch (
            \Throwable $e
        ) {

            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Không thể xác nhận nhận hàng. Vui lòng thử lại.'
                );
        }

        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Cảm ơn bạn đã xác nhận nhận hàng. Đơn hàng đã hoàn thành.'
            );
    }
}
