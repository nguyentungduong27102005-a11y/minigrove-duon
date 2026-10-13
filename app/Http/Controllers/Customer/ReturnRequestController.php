<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;

use App\Services\ReturnRequestService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReturnRequestController extends Controller
{
    /**
     * Form yêu cầu trả hàng.
     */
    public function create(
        Request $request,
        Order $order
    ): View|RedirectResponse {

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


        /*
        |--------------------------------------------------------------------------
        | CHỈ COMPLETED
        |--------------------------------------------------------------------------
        */

        if (
            $order->order_status
            !== 'completed'
        ) {

            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Chỉ đơn hàng đã hoàn thành mới có thể yêu cầu trả hàng / hoàn tiền.'
                );
        }


        $order->load([
            'details.product.images' =>
                function ($query) {

                    $query
                        ->orderByDesc(
                            'is_primary'
                        )
                        ->orderBy(
                            'sort_order'
                        )
                        ->orderBy('id');
                },
        ]);


        /*
        |--------------------------------------------------------------------------
        | TÍNH SỐ LƯỢNG CÒN CÓ THỂ TRẢ
        |--------------------------------------------------------------------------
        */

        foreach (
            $order->details
            as $detail
        ) {

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
                    ->sum(
                        'quantity'
                    );


            $detail->returnable_quantity =
                max(
                    0,
                    (int)
                    $detail->quantity
                    -
                    (int)
                    $alreadyRequested
                );
        }


        $returnableDetails =
            $order
                ->details
                ->filter(
                    function ($detail) {

                        return
                            $detail->item_type
                                === 'PRODUCT'
                            &&
                            $detail
                                ->returnable_quantity
                                > 0;
                    }
                );


        if (
            $returnableDetails->isEmpty()
        ) {

            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Đơn hàng này không còn sản phẩm có thể yêu cầu trả.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | REASONS
        |--------------------------------------------------------------------------
        */

        $reasons = [
            'damaged' =>
                'Sản phẩm bị hư hỏng',

            'wrong_item' =>
                'Giao sai sản phẩm',

            'not_as_described' =>
                'Sản phẩm không đúng mô tả',

            'quality_issue' =>
                'Sản phẩm có vấn đề chất lượng',

            'other' =>
                'Lý do khác',
        ];


        return view(
            'returns.create',
            compact(
                'order',
                'returnableDetails',
                'reasons'
            )
        );
    }


    /**
     * Gửi yêu cầu trả hàng.
     */
    public function store(
        Request $request,
        Order $order,
        ReturnRequestService $service
    ): RedirectResponse {

        abort_unless(
            $order->user_id
                ===
            $request->user()->id,
            403
        );


        $validated =
            $request->validate(
                [
                    'reason' => [
                        'required',
                        'in:damaged,wrong_item,not_as_described,quality_issue,other',
                    ],

                    'description' => [
                        'nullable',
                        'string',
                        'max:2000',
                    ],

                    'quantities' => [
                        'required',
                        'array',
                    ],

                    'quantities.*' => [
                        'nullable',
                        'integer',
                        'min:0',
                    ],
                ],
                [
                    'reason.required' =>
                        'Vui lòng chọn lý do trả hàng.',

                    'reason.in' =>
                        'Lý do trả hàng không hợp lệ.',

                    'quantities.required' =>
                        'Vui lòng chọn sản phẩm cần trả.',

                    'quantities.array' =>
                        'Danh sách sản phẩm trả không hợp lệ.',
                ]
            );


        try {

            $returnRequest =
                $service->create(
                    $request->user(),
                    $order,
                    $validated[
                        'quantities'
                    ],
                    $validated[
                        'reason'
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
                ->back()
                ->withInput()
                ->with(
                    'error',
                    $e->validator
                        ->errors()
                        ->first()
                );
        }


        return redirect()
            ->route(
                'returns.show',
                $returnRequest
            )
            ->with(
                'success',
                'Yêu cầu trả hàng / hoàn tiền đã được gửi.'
            );
    }


    /**
     * Chi tiết yêu cầu trả hàng.
     */
    public function show(
        Request $request,
        ReturnRequest $returnRequest
    ): View {

        abort_unless(
            $returnRequest->user_id
                ===
            $request->user()->id,
            403
        );


        $returnRequest->load([
            'order',

            'items.orderDetail.product.images',
        ]);


        return view(
            'returns.show',
            compact(
                'returnRequest'
            )
        );
    }
}