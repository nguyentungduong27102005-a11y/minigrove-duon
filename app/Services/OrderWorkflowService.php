<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    public function __construct(
        private GroveService $groveService
    ) {}

    /**
     * Chuyển trạng thái Order theo đúng luồng nghiệp vụ.
     */
    public function updateStatus(
        User $actor,
        Order $order,
        string $newStatus,
        ?string $description = null
    ): Order {

        $newStatus =
            strtolower(
                trim($newStatus)
            );

        /*
        |--------------------------------------------------------------------------
        | LUỒNG TRẠNG THÁI CHO PHÉP
        |--------------------------------------------------------------------------
        */

        $allowedTransitions = [

            'pending' => [
                'confirmed',
            ],

            'confirmed' => [
                'preparing',
            ],

            'preparing' => [
                'packed',
            ],

            'packed' => [
                'shipping',
            ],

            'shipping' => [
                'completed',
            ],

            /*
             * Completed và Cancelled
             * là trạng thái kết thúc.
             */
            'completed' => [],

            'cancelled' => [],
        ];

        return DB::transaction(
            function () use (
                $actor,
                $order,
                $newStatus,
                $description,
                $allowedTransitions
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder =
    Order::query()
        ->select([
            'id',
            'user_id',
            'order_status',
            'payment_method',
            'payment_status',
        ])
        ->whereKey(
            $order->id
        )
        ->lockForUpdate()
        ->firstOrFail();

                $currentStatus =
                    $lockedOrder
                        ->order_status;

                /*
                |--------------------------------------------------------------------------
                | KIỂM TRA TRẠNG THÁI HIỆN TẠI
                |--------------------------------------------------------------------------
                */

                if (
                    ! array_key_exists(
                        $currentStatus,
                        $allowedTransitions
                    )
                ) {

                    throw ValidationException::withMessages([
                        'status' => 'Trạng thái hiện tại của đơn hàng không hợp lệ.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | KIỂM TRA CHUYỂN TRẠNG THÁI
                |--------------------------------------------------------------------------
                */

                if (
                    ! in_array(
                        $newStatus,
                        $allowedTransitions[
                            $currentStatus
                        ],
                        true
                    )
                ) {

                    throw ValidationException::withMessages([
                        'status' => 'Không thể chuyển đơn hàng từ '
                            .$currentStatus
                            .' sang '
                            .$newStatus
                            .'.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | QR PHẢI ĐƯỢC XÁC NHẬN THANH TOÁN
                |--------------------------------------------------------------------------
                |
                | Với QR:
                | Payment phải ở trạng thái Paid trước khi
                | Order được xác nhận. PayOS sẽ tự chuyển
                | Pending -> Confirmed khi thanh toán thành công.
                |
                */

                if (
                    $currentStatus === 'pending'
                    &&
                    $newStatus === 'confirmed'
                    &&
                    $lockedOrder->payment_method
                        === 'QR'
                    &&
                    $lockedOrder->payment_status
                        !== 'paid'
                ) {

                    throw ValidationException::withMessages([
                        'payment' => 'Đơn QR chưa được xác nhận thanh toán.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | SHIPPING -> COMPLETED
                |--------------------------------------------------------------------------
                |
                | COD:
                | Khi giao thành công thì mới Paid.
                |
                | QR:
                | Phải Paid từ trước.
                |
                */

                if (
                    $newStatus === 'completed'
                ) {

                    $payment =
                        Payment::query()
                            ->where(
                                'order_id',
                                $lockedOrder->id
                            )
                            ->latest('id')
                            ->lockForUpdate()
                            ->first();

                    if (! $payment) {

                        throw ValidationException::withMessages([
                            'payment' => 'Không tìm thấy thông tin thanh toán của đơn hàng.',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | COD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $lockedOrder->payment_method
                        === 'COD'
                    ) {

                        if (
                            $payment->status
                            === 'pending'
                        ) {

                            $payment->status =
                                'paid';

                            $payment->confirmed_by =
                                $actor->id;

                            $payment->paid_at =
                                now();

                            $payment->note =
                                $this->appendNote(
                                    $payment->note,
                                    'COD đã được xác nhận thanh toán khi giao hàng hoàn tất.'
                                );

                            $payment->save();
                        } elseif (
                            $payment->status
                            !== 'paid'
                        ) {

                            throw ValidationException::withMessages([
                                'payment' => 'Trạng thái Payment COD không cho phép hoàn thành đơn.',
                            ]);
                        }

                        $lockedOrder->payment_status =
                            'paid';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | QR
                    |--------------------------------------------------------------------------
                    */

                    elseif (
                        $lockedOrder->payment_method
                        === 'QR'
                    ) {

                        if (
                            $payment->status
                                !== 'paid'
                            ||
                            $lockedOrder
                                ->payment_status
                                !== 'paid'
                        ) {

                            throw ValidationException::withMessages([
                                'payment' => 'Thanh toán QR chưa được xác nhận.',
                            ]);
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ONEPAY
                    |--------------------------------------------------------------------------
                    */

                    elseif (
                        $lockedOrder->payment_method
                        === 'ONEPAY'
                    ) {

                        if (
                            $payment->status !== 'paid'
                            ||
                            $lockedOrder->payment_status !== 'paid'
                        ) {

                            throw ValidationException::withMessages([
                                'payment' => 'Thanh toán OnePay chưa được xác nhận.',
                            ]);
                        }
                    } else {

                        throw ValidationException::withMessages([
                            'payment' => 'Phương thức thanh toán không hợp lệ.',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | UPDATE ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder->order_status =
                    $newStatus;

                $lockedOrder->save();

                /*
                |--------------------------------------------------------------------------
                | HISTORY
                |--------------------------------------------------------------------------
                */

                $historyDescription =
                    $description
                        && trim(
                            $description
                        ) !== ''
                            ? trim(
                                $description
                            )
                            : $this
                                ->defaultDescription(
                                    $newStatus
                                );

                OrderStatusHistory::create([
                    'order_id' => $lockedOrder->id,

                    'status' => $newStatus,

                    'description' => $historyDescription,

                    'updated_by' => $actor->id,
                ]);
                /*
|--------------------------------------------------------------------------
| TẠO GROVE KHI GIAO HÀNG HOÀN TẤT
|--------------------------------------------------------------------------
|
| Chỉ khi Order chuyển sang completed mới sinh Grove.
|
| GroveService tự:
| - kiểm tra Product có is_grove_eligible hay không;
| - xử lý Custom Design;
| - tạo theo quantity;
| - tránh tạo trùng.
|
*/

                if (
                    $newStatus === 'completed'
                ) {
                    $this
                        ->groveService
                        ->createForCompletedOrder(
                            $lockedOrder
                        );
                }

                $lockedOrder->load([
                    'details',
                    'payments',
                    'statusHistories',
                ]);

                return $lockedOrder;
            }
        );
    }

    /**
     * Customer xác nhận đã nhận được hàng (shipping -> completed).
     */
    public function confirmReceivedByCustomer(
        User $customer,
        Order $order
    ): Order {

        return DB::transaction(
            function () use ($customer, $order) {

                $lockedOrder =
                    Order::query()
                        ->select([
                            'id',
                            'user_id',
                            'order_status',
                            'payment_method',
                            'payment_status',
                        ])
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
                    !== $customer->id
                ) {

                    abort(
                        403,
                        'Bạn không có quyền thao tác trên đơn hàng này.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CHỈ SHIPPING MỚI ĐƯỢC XÁC NHẬN NHẬN HÀNG
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedOrder->order_status
                    !== 'shipping'
                ) {

                    throw ValidationException::withMessages([
                        'order' => 'Đơn hàng không ở trạng thái đang giao nên không thể xác nhận nhận hàng.',
                    ]);
                }

                return $this->updateStatus(
                    $customer,
                    $lockedOrder,
                    'completed',
                    'Khách hàng đã xác nhận nhận được hàng.'
                );
            }
        );
    }

    /**
     * Nội dung History mặc định.
     */
    private function defaultDescription(
        string $status
    ): string {

        return match ($status) {

            'confirmed' => 'MiniGrove đã xác nhận đơn hàng.',

            'preparing' => 'MiniGrove đang chuẩn bị Terrarium.',

            'packed' => 'Đơn hàng đã được đóng gói.',

            'shipping' => 'Đơn hàng đang được giao đến Customer.',

            'completed' => 'Đơn hàng đã giao thành công.',

            default => 'Trạng thái đơn hàng đã được cập nhật.',
        };
    }

    /**
     * Nối ghi chú mà không làm mất
     * ghi chú Payment trước đó.
     */
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
            .' '
            .$newNote;
    }
}
