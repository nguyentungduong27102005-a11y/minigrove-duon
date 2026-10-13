<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ReturnRequest;

use App\Services\ReturnProcessingService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReturnRequestController extends Controller
{
    /**
     * Danh sách yêu cầu trả hàng.
     */
    public function index(
        Request $request
    ): View {

        $status =
            $request->query(
                'status'
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
            'approved',
            'rejected',
            'returning',
            'received',
            'refunded',
        ];


        $query =
    ReturnRequest::query()
        ->with([
            'order:id,order_code,user_id,total_amount,payment_method,payment_status,order_status,created_at',
        ])
        ->withCount(
            'items'
        )
        ->latest('id');


        /*
        |--------------------------------------------------------------------------
        | STATUS
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
                'status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (
    $keyword !== ''
) {

    $query->where(
        function ($query) use (
            $keyword
        ) {

            /*
             * Tìm theo mã yêu cầu trả hàng.
             */
            $query->where(
                'return_code',
                'like',
                '%' . $keyword . '%'
            );


            /*
             * Tìm theo mã đơn hàng.
             */
            $query->orWhereHas(
                'order',
                function ($orderQuery) use (
                    $keyword
                ) {

                    $orderQuery->where(
                        'order_code',
                        'like',
                        '%' . $keyword . '%'
                    );
                }
            );


            /*
             * Nếu nhập số nguyên thì cho phép
             * tìm chính xác theo Customer ID.
             */
            if (
                ctype_digit($keyword)
            ) {

                $query->orWhere(
                    'user_id',
                    (int) $keyword
                );
            }
        }
    );
}


        $returnRequests =
            $query
                ->paginate(15)
                ->withQueryString();


        return view(
            'admin.returns.index',
            compact(
                'returnRequests',
                'status',
                'keyword'
            )
        );
    }


    /**
     * Chi tiết yêu cầu.
     */
    public function show(
        ReturnRequest $returnRequest
    ): View {

        $returnRequest->load([
    'order' => function ($query) {

        $query->select([
            'id',
            'order_code',
            'user_id',
            'total_amount',
            'payment_method',
            'payment_status',
            'order_status',
            'created_at',
        ]);
    },

    'order.payments',

    'processor:id,name,email',

    'items.orderDetail.product.images',
]);


        return view(
            'admin.returns.show',
            compact(
                'returnRequest'
            )
        );
    }


    /**
     * Pending -> Approved
     */
    public function approve(
        Request $request,
        ReturnRequest $returnRequest,
        ReturnProcessingService $service
    ): RedirectResponse {

        $validated =
            $request->validate([
                'processing_note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        try {

            $service->approve(
                $request->user(),
                $returnRequest,
                $validated[
                    'processing_note'
                ]
                ?? null
            );

        } catch (
            ValidationException $e
        ) {

            return $this->validationError(
                $returnRequest,
                $e
            );
        }


        return redirect()
            ->route(
                'admin.returns.show',
                $returnRequest
            )
            ->with(
                'success',
                'Yêu cầu trả hàng đã được chấp nhận.'
            );
    }


    /**
     * Pending -> Rejected
     */
    public function reject(
        Request $request,
        ReturnRequest $returnRequest,
        ReturnProcessingService $service
    ): RedirectResponse {

        $validated =
            $request->validate([
                'processing_note' => [
                    'required',
                    'string',
                    'max:2000',
                ],
            ]);


        try {

            $service->reject(
                $request->user(),
                $returnRequest,
                $validated[
                    'processing_note'
                ]
            );

        } catch (
            ValidationException $e
        ) {

            return $this->validationError(
                $returnRequest,
                $e
            );
        }


        return redirect()
            ->route(
                'admin.returns.show',
                $returnRequest
            )
            ->with(
                'success',
                'Yêu cầu trả hàng đã bị từ chối.'
            );
    }


    /**
     * Approved -> Returning
     */
    public function markReturning(
        Request $request,
        ReturnRequest $returnRequest,
        ReturnProcessingService $service
    ): RedirectResponse {

        $validated =
            $request->validate([
                'processing_note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        try {

            $service->markReturning(
                $request->user(),
                $returnRequest,
                $validated[
                    'processing_note'
                ]
                ?? null
            );

        } catch (
            ValidationException $e
        ) {

            return $this->validationError(
                $returnRequest,
                $e
            );
        }


        return redirect()
            ->route(
                'admin.returns.show',
                $returnRequest
            )
            ->with(
                'success',
                'Yêu cầu đã chuyển sang trạng thái đang trả hàng.'
            );
    }


    /**
     * Returning -> Received
     */
    public function markReceived(
        Request $request,
        ReturnRequest $returnRequest,
        ReturnProcessingService $service
    ): RedirectResponse {

        $validated =
            $request->validate([
                'restock_quantities' => [
                    'nullable',
                    'array',
                ],

                'restock_quantities.*' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'processing_note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        try {

            $service->markReceived(
                $request->user(),
                $returnRequest,
                $validated[
                    'restock_quantities'
                ]
                ?? [],
                $validated[
                    'processing_note'
                ]
                ?? null
            );

        } catch (
            ValidationException $e
        ) {

            return $this->validationError(
                $returnRequest,
                $e
            );
        }


        return redirect()
            ->route(
                'admin.returns.show',
                $returnRequest
            )
            ->with(
                'success',
                'MiniGrove đã xác nhận nhận lại hàng.'
            );
    }


    /**
     * Received -> Refunded
     */
    public function refund(
        Request $request,
        ReturnRequest $returnRequest,
        ReturnProcessingService $service
    ): RedirectResponse {

        $validated =
            $request->validate([
                'processing_note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        try {

            $service->refund(
                $request->user(),
                $returnRequest,
                $validated[
                    'processing_note'
                ]
                ?? null
            );

        } catch (
            ValidationException $e
        ) {

            return $this->validationError(
                $returnRequest,
                $e
            );
        }


        return redirect()
            ->route(
                'admin.returns.show',
                $returnRequest
            )
            ->with(
                'success',
                'Yêu cầu đã được ghi nhận hoàn tiền.'
            );
    }


    /**
     * Redirect lỗi nghiệp vụ.
     */
    private function validationError(
        ReturnRequest $returnRequest,
        ValidationException $exception
    ): RedirectResponse {

        return redirect()
            ->route(
                'admin.returns.show',
                $returnRequest
            )
            ->with(
                'error',
                $exception
                    ->validator
                    ->errors()
                    ->first()
            );
    }
}