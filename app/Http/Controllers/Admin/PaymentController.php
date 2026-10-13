<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Trang quản lý Thanh toán.
     *
     * Privacy:
     * - Không lấy tên Customer.
     * - Không lấy email Customer.
     * - Không lấy số điện thoại / địa chỉ Customer.
     * - Admin chỉ nhận diện khách hàng bằng Customer ID.
     */
    public function index(Request $request)
    {
        $keyword = trim(
            (string) $request->query('keyword', '')
        );

        $method = trim(
            (string) $request->query('method', '')
        );

        $status = trim(
            (string) $request->query('status', '')
        );


        /*
        |--------------------------------------------------------------------------
        | Danh sách thanh toán
        |--------------------------------------------------------------------------
        |
        | Chỉ lấy dữ liệu nghiệp vụ cần thiết của Order.
        | Tuyệt đối không eager load User Customer.
        |
        */
        $query = Payment::query()
            ->with([
                'order:id,order_code,user_id,total_amount,order_status,payment_status',
                'confirmer:id,name,email',
            ])
            ->latest('id');


        /*
        |--------------------------------------------------------------------------
        | Tìm kiếm
        |--------------------------------------------------------------------------
        |
        | Cho phép:
        | - Mã đơn hàng
        | - Customer ID
        |
        | Không cho phép:
        | - Tên Customer
        | - Email Customer
        | - Số điện thoại Customer
        |
        */
        if ($keyword !== '') {
            $query->whereHas(
                'order',
                function ($orderQuery) use ($keyword) {
                    $orderQuery->where(
                        function ($searchQuery) use ($keyword) {
                            $searchQuery->where(
                                'order_code',
                                'like',
                                '%' . $keyword . '%'
                            );

                            if (ctype_digit($keyword)) {
                                $searchQuery->orWhere(
                                    'user_id',
                                    (int) $keyword
                                );
                            }
                        }
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Lọc phương thức
        |--------------------------------------------------------------------------
        */
        if (in_array(
            $method,
            ['COD', 'QR', 'ONEPAY'],
            true
        )) {
            $query->where(
                'payment_method',
                $method
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Lọc trạng thái
        |--------------------------------------------------------------------------
        */
        if (in_array(
            $status,
            [
                'pending',
                'paid',
                'failed',
                'refunded',
            ],
            true
        )) {
            $query->where(
                'status',
                $status
            );
        }


        $payments = $query
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Thống kê
        |--------------------------------------------------------------------------
        */
        $totalCount =
            Payment::count();

        $pendingCount =
            Payment::where(
                'status',
                'pending'
            )->count();

        $paidCount =
            Payment::where(
                'status',
                'paid'
            )->count();

        $failedCount =
            Payment::where(
                'status',
                'failed'
            )->count();

        $refundedCount =
            Payment::where(
                'status',
                'refunded'
            )->count();


        /*
        |--------------------------------------------------------------------------
        | Cấu hình phương thức thanh toán
        |--------------------------------------------------------------------------
        */
        $paymentSettings =
            PaymentSetting::query()
                ->orderByRaw(
                    "
                    CASE payment_method
                        WHEN 'COD' THEN 1
                        WHEN 'QR' THEN 2
                        ELSE 3
                    END
                    "
                )
                ->get()
                ->keyBy(
                    'payment_method'
                );


        return view(
            'admin.payments.index',
            compact(
                'payments',
                'paymentSettings',
                'keyword',
                'method',
                'status',
                'totalCount',
                'pendingCount',
                'paidCount',
                'failedCount',
                'refundedCount'
            )
        );
    }


    /**
     * Cập nhật cấu hình một phương thức thanh toán.
     */
    public function updateSetting(
        Request $request,
        PaymentSetting $paymentSetting
    ) {
        $rules = [
            'display_name' => [
                'required',
                'string',
                'max:100',
            ],

            'is_enabled' => [
                'required',
                'boolean',
            ],
        ];

        $validated = $request->validate(
            $rules,
            [
                'display_name.required' =>
                    'Vui lòng nhập tên hiển thị phương thức thanh toán.',

                'display_name.max' =>
                    'Tên hiển thị không được vượt quá 100 ký tự.',

                'is_enabled.required' =>
                    'Vui lòng chọn trạng thái phương thức thanh toán.',

                'is_enabled.boolean' =>
                    'Trạng thái phương thức thanh toán không hợp lệ.',

            ]
        );


        /*
         * COD và QR PayOS không cấu hình tài khoản
         * ngân hàng thủ công tại MiniGrove.
         */
        if (
            in_array(
                $paymentSetting->payment_method,
                ['COD', 'QR'],
                true
            )
        ) {
            $validated['bank_name'] = null;
            $validated['account_name'] = null;
            $validated['account_number'] = null;
        }


        $paymentSetting->update(
            $validated
        );


        return back()->with(
            'success',
            'Cập nhật phương thức thanh toán thành công.'
        );
    }
}