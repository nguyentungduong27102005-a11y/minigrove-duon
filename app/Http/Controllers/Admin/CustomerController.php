<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Danh sách Customer.
     *
     * Khu vực Admin không được lấy
     * thông tin cá nhân của Customer.
     */
    public function index(Request $request)
    {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );

        $status = $request->query(
            'status',
            ''
        );


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER QUERY
        |--------------------------------------------------------------------------
        |
        | Chỉ SELECT các trường phục vụ quản trị.
        |
        | TUYỆT ĐỐI KHÔNG SELECT:
        | - name
        | - email
        | - phone
        | - avatar
        |
        */

        $query = User::query()
            ->select([
                'users.id',
                'users.role_id',
                'users.is_active',
                'users.created_at',
            ])
            ->whereHas(
                'role',
                function ($roleQuery) {

                    $roleQuery->where(
                        'name',
                        'customer'
                    );
                }
            )

            /*
             * Tổng số Order.
             */
            ->withCount('orders')

            /*
             * Tổng giá trị Order Completed.
             */
            ->withSum(
                [
                    'orders as completed_spending' =>
                        function ($orderQuery) {

                            $orderQuery->where(
                                'order_status',
                                'completed'
                            );
                        },
                ],
                'total_amount'
            )

            /*
             * Tổng số tiền đã hoàn.
             */
            ->withSum(
                [
                    'returnRequests as refunded_amount' =>
                        function ($returnQuery) {

                            $returnQuery->where(
                                'status',
                                'refunded'
                            );
                        },
                ],
                'refund_amount'
            )

            ->latest('id');


        /*
        |--------------------------------------------------------------------------
        | TÌM KIẾM
        |--------------------------------------------------------------------------
        |
        | Không tìm theo:
        | - tên
        | - email
        | - số điện thoại
        |
        | Chỉ tìm theo Customer ID.
        |
        */

        if ($keyword !== '') {

            if (ctype_digit($keyword)) {

                $query->where(
                    'users.id',
                    (int) $keyword
                );

            } else {

                /*
                 * Keyword không phải ID hợp lệ
                 * => không trả Customer nào.
                 */
                $query->whereRaw(
                    '1 = 0'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | LỌC TRẠNG THÁI
        |--------------------------------------------------------------------------
        */

        if ($status === 'active') {

            $query->where(
                'users.is_active',
                true
            );

        } elseif ($status === 'inactive') {

            $query->where(
                'users.is_active',
                false
            );
        }


        $customers = $query
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | NHÃN ẨN DANH
        |--------------------------------------------------------------------------
        |
        | View cũ đang sử dụng $customer->name.
        | Không lấy name thật từ Database.
        |
        | Tạo nhãn quản trị:
        | Customer #5
        |
        */

        $customers
            ->getCollection()
            ->transform(
                function ($customer) {

                    $customer->setAttribute(
                        'name',
                        'Customer #' . $customer->id
                    );

                    $customer->setAttribute(
                        'email',
                        null
                    );

                    $customer->setAttribute(
                        'phone',
                        null
                    );

                    $customer->setAttribute(
                        'avatar',
                        null
                    );

                    return $customer;
                }
            );


        /*
        |--------------------------------------------------------------------------
        | THỐNG KÊ
        |--------------------------------------------------------------------------
        */

        $customerBaseQuery =
            User::query()
                ->whereHas(
                    'role',
                    function ($roleQuery) {

                        $roleQuery->where(
                            'name',
                            'customer'
                        );
                    }
                );


        $totalCustomers =
            (clone $customerBaseQuery)
                ->count();


        $activeCustomers =
            (clone $customerBaseQuery)
                ->where(
                    'is_active',
                    true
                )
                ->count();


        $inactiveCustomers =
            (clone $customerBaseQuery)
                ->where(
                    'is_active',
                    false
                )
                ->count();


        $newCustomersThisMonth =
            (clone $customerBaseQuery)
                ->where(
                    'created_at',
                    '>=',
                    now()->startOfMonth()
                )
                ->count();


        return view(
            'admin.customers.index',
            compact(
                'customers',
                'keyword',
                'status',
                'totalCustomers',
                'activeCustomers',
                'inactiveCustomers',
                'newCustomersThisMonth'
            )
        );
    }


    /**
     * Chi tiết Customer.
     *
     * Không dùng Implicit Model Binding User,
     * tránh tải toàn bộ bản ghi chứa PII.
     */
    public function show(
        int $customer
    ) {
        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        |
        | Chỉ lấy dữ liệu quản trị không nhạy cảm.
        |
        */

        $customerModel =
            User::query()
                ->select([
                    'id',
                    'role_id',
                    'is_active',
                    'created_at',
                ])
                ->where(
                    'id',
                    $customer
                )
                ->whereHas(
                    'role',
                    function ($roleQuery) {

                        $roleQuery->where(
                            'name',
                            'customer'
                        );
                    }
                )
                ->with([
                    'role:id,name,display_name',
                ])
                ->firstOrFail();


        /*
         * Không sử dụng tên thật.
         */
        $customerModel->setAttribute(
            'name',
            'Customer #' . $customerModel->id
        );

        $customerModel->setAttribute(
            'avatar',
            null
        );


        /*
        |--------------------------------------------------------------------------
        | ORDER HISTORY AN TOÀN
        |--------------------------------------------------------------------------
        |
        | Chỉ SELECT dữ liệu phục vụ thống kê.
        |
        | KHÔNG SELECT:
        | - customer_name
        | - phone
        | - email
        | - shipping_address
        | - note
        |
        */

        $orders =
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
                ->where(
                    'user_id',
                    $customerModel->id
                )
                ->latest('id')
                ->get();


        /*
         * Gắn relation an toàn cho View hiện tại.
         */
        $customerModel->setRelation(
            'orders',
            $orders
        );


        /*
        |--------------------------------------------------------------------------
        | THỐNG KÊ ORDER
        |--------------------------------------------------------------------------
        */

        $orderCount =
            $orders->count();


        $completedOrderCount =
            $orders
                ->where(
                    'order_status',
                    'completed'
                )
                ->count();


        $cancelledOrderCount =
            $orders
                ->where(
                    'order_status',
                    'cancelled'
                )
                ->count();


        $completedSpending =
            (float)
            $orders
                ->where(
                    'order_status',
                    'completed'
                )
                ->sum(
                    function ($order) {

                        return (float)
                            $order->total_amount;
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | HOÀN TIỀN
        |--------------------------------------------------------------------------
        |
        | Chỉ lấy tổng số tiền.
        | Không lấy nội dung yêu cầu trả hàng.
        |
        */

        $refundedAmount =
            (float)
            ReturnRequest::query()
                ->where(
                    'user_id',
                    $customerModel->id
                )
                ->where(
                    'status',
                    'refunded'
                )
                ->sum(
                    'refund_amount'
                );


        $totalSpending =
            max(
                0,
                $completedSpending
                -
                $refundedAmount
            );


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        $customer = $customerModel;


        return view(
            'admin.customers.show',
            compact(
                'customer',
                'orders',
                'orderCount',
                'completedOrderCount',
                'cancelledOrderCount',
                'completedSpending',
                'refundedAmount',
                'totalSpending'
            )
        );
    }


    /**
     * Active / Inactive Customer.
     *
     * Không cần lấy thông tin cá nhân
     * để khóa/mở khóa tài khoản.
     */
    public function toggleStatus(
        int $customer
    ) {
        $customerModel =
            User::query()
                ->select([
                    'id',
                    'role_id',
                    'is_active',
                ])
                ->where(
                    'id',
                    $customer
                )
                ->whereHas(
                    'role',
                    function ($roleQuery) {

                        $roleQuery->where(
                            'name',
                            'customer'
                        );
                    }
                )
                ->firstOrFail();


        $customerModel->is_active =
            !$customerModel->is_active;


        $customerModel->save();


        return back()->with(
            'success',
            $customerModel->is_active
                ? 'Đã kích hoạt tài khoản Customer.'
                : 'Đã khóa tài khoản Customer.'
        );
    }
}