<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VoucherController extends Controller
{
    /**
     * Kho Voucher của Customer.
     */
    public function index(
        Request $request
    ): View {

        $tab = $request->query(
            'tab',
            'available'
        );

        $allowedTabs = [
            'available',
            'used',
            'expired',
        ];

        if (
            !in_array(
                $tab,
                $allowedTabs,
                true
            )
        ) {
            $tab = 'available';
        }


        $userId = auth()->id();

        $now = now();

        $savedVoucherIds = $request->user()
            ->savedVouchers()
            ->pluck('vouchers.id')
            ->map(
                fn ($voucherId) =>
                    (int) $voucherId
            )
            ->all();


        /*
         * Lấy toàn bộ Voucher và chỉ load
         * lịch sử sử dụng của Customer hiện tại.
         */
        $allVouchers =
            Voucher::query()
                ->with([
                    'usages' =>
                        function ($query) use ($userId) {

                            $query
                                ->where(
                                    'user_id',
                                    $userId
                                )
                                ->latest('id');
                        },
                ])
                ->latest('id')
                ->get();


        /*
         * Tính trạng thái riêng cho từng Customer.
         */
        $allVouchers->each(
            function ($voucher) use (
                $now,
                $savedVoucherIds
            ) {

                $userUsedCount =
                    $voucher
                        ->usages
                        ->where(
                            'status',
                            'used'
                        )
                        ->count();


                $notStarted =
                    $voucher->start_at
                    &&
                    $voucher->start_at
                        ->gt($now);


                $expired =
                    $voucher->end_at
                    &&
                    $voucher->end_at
                        ->lt($now);


                $globalLimitReached =
                    $voucher->usage_limit !== null
                    &&
                    $voucher->usage_count
                        >=
                    $voucher->usage_limit;


                $userLimitReached =
                    $voucher->per_user_limit !== null
                    &&
                    $userUsedCount
                        >=
                    $voucher->per_user_limit;


                $available =
                    $voucher->is_active
                    &&
                    !$notStarted
                    &&
                    !$expired
                    &&
                    !$globalLimitReached
                    &&
                    !$userLimitReached;


                $voucher->setAttribute(
                    'user_used_count',
                    $userUsedCount
                );

                $voucher->setAttribute(
                    'customer_available',
                    $available
                );

                $voucher->setAttribute(
                    'customer_expired',
                    $expired
                    || !$voucher->is_active
                    || $globalLimitReached
                );

                $voucher->setAttribute(
                    'customer_limit_reached',
                    $userLimitReached
                );

                $voucher->setAttribute(
                    'customer_saved',
                    in_array(
                        (int) $voucher->id,
                        $savedVoucherIds,
                        true
                    )
                );
            }
        );


        /*
         * Có thể sử dụng.
         */
        $availableVouchers =
            $allVouchers
                ->filter(
                    fn ($voucher) =>
                        $voucher
                            ->customer_available
                )
                ->values();


        /*
         * Customer đã từng sử dụng.
         *
         * VoucherUsage status = used
         * mới được tính.
         *
         * status = released do hủy Order
         * không được tính là đã dùng.
         */
        $usedVouchers =
            $allVouchers
                ->filter(
                    fn ($voucher) =>
                        $voucher
                            ->user_used_count
                        > 0
                )
                ->values();


        /*
         * Hết hiệu lực.
         */
        $expiredVouchers =
            $allVouchers
                ->filter(
                    fn ($voucher) =>
                        $voucher
                            ->customer_expired
                )
                ->values();


        $vouchers =
            match ($tab) {

                'used' =>
                    $usedVouchers,

                'expired' =>
                    $expiredVouchers,

                default =>
                    $availableVouchers,
            };


        $counts = [

            'available' =>
                $availableVouchers->count(),

            'used' =>
                $usedVouchers->count(),

            'expired' =>
                $expiredVouchers->count(),
        ];


        return view(
            'profile.vouchers',
            compact(
                'vouchers',
                'tab',
                'counts'
            )
        );
    }

    /**
     * Lưu Voucher vào tài khoản Customer.
     */
    public function save(
        Request $request,
        Voucher $voucher
    ): RedirectResponse {
        $request->user()
            ->savedVouchers()
            ->syncWithoutDetaching([
                $voucher->id,
            ]);

        return back()->with(
            'success',
            'Đã lưu mã Voucher '
                . $voucher->code
                . '.'
        );
    }

    /**
     * Bỏ Voucher khỏi danh sách đã lưu.
     */
    public function unsave(
        Request $request,
        Voucher $voucher
    ): RedirectResponse {
        $request->user()
            ->savedVouchers()
            ->detach($voucher->id);

        return back()->with(
            'success',
            'Đã bỏ lưu mã Voucher '
                . $voucher->code
                . '.'
        );
    }
}