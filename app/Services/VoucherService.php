<?php

namespace App\Services;

use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use DateTimeInterface;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    /**
     * Kiểm tra Voucher và tính số tiền giảm.
     *
     * Hỗ trợ cả 2 phạm vi:
     * - order: căn cứ giảm là subtotal
     * - shipping: căn cứ giảm là shippingFee, điều kiện min_order_amount vẫn theo subtotal
     */
    public function validateAndCalculate(
        User $user,
        string $code,
        float $subtotal,
        float $shippingFee = 0.0,
        ?string $expectedScope = null
    ): array {
        $code = strtoupper(trim($code));

        if ($code === '') {
            throw ValidationException::withMessages([
                'voucher' => 'Vui lòng nhập mã Voucher.',
            ]);
        }

        $voucher = Voucher::query()
            ->whereRaw('UPPER(code) = ?', [$code])
            ->first();

        if (!$voucher) {
            throw ValidationException::withMessages([
                'voucher' => 'Mã Voucher không tồn tại.',
            ]);
        }

        /*
         * Trạng thái hoạt động
         */
        if (!$voucher->is_active) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher hiện không còn hoạt động.',
            ]);
        }

        $now = now();

        /*
         * Chưa đến thời gian sử dụng
         */
        if ($voucher->start_at->gt($now)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher chưa đến thời gian sử dụng.',
            ]);
        }

        /*
         * Đã hết hạn
         */
        if ($voucher->end_at->lt($now)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher đã hết hạn.',
            ]);
        }

        /*
         * Tổng lượt sử dụng
         */
        if ($this->isUsageLimitReached($voucher)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher đã hết lượt sử dụng.',
            ]);
        }

        /*
         * Giới hạn theo từng Customer
         */
        if ($this->isUserLimitReached($user, $voucher)) {
            throw ValidationException::withMessages([
                'voucher' => 'Bạn đã sử dụng hết số lượt cho Voucher này.',
            ]);
        }

        /*
         * Kiểm tra Scope hợp lệ
         */
        $scope = $voucher->voucher_scope ?? Voucher::SCOPE_ORDER;
        if (!in_array($scope, [Voucher::SCOPE_ORDER, Voucher::SCOPE_SHIPPING], true)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher có cấu hình phạm vi không hợp lệ.',
            ]);
        }

        /*
         * Kiểm tra expectedScope nếu được truyền vào
         */
        if ($expectedScope !== null && $expectedScope !== $scope) {
            $msg = $scope === Voucher::SCOPE_SHIPPING
                ? 'Mã này là mã giảm phí vận chuyển, vui lòng sử dụng tại ô mã vận chuyển.'
                : 'Mã này là mã giảm giá đơn hàng, vui lòng sử dụng tại ô mã giảm giá đơn hàng.';

            throw ValidationException::withMessages([
                'voucher' => $msg,
            ]);
        }

        /*
         * Giá trị đơn tối thiểu (luôn kiểm tra theo subtotal)
         */
        if ($subtotal < (float) $voucher->min_order_amount) {
            throw ValidationException::withMessages([
                'voucher' => 'Đơn hàng chưa đạt giá trị tối thiểu '
                    . number_format((float) $voucher->min_order_amount, 0, ',', '.')
                    . 'đ để sử dụng Voucher.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TÍNH TIỀN GIẢM
        |--------------------------------------------------------------------------
        */
        $baseAmount = $scope === Voucher::SCOPE_SHIPPING ? $shippingFee : $subtotal;

        if ($voucher->discount_type === 'percentage') {
            $discount = $baseAmount * ((float) $voucher->discount_value / 100);
        } elseif ($voucher->discount_type === 'fixed') {
            $discount = (float) $voucher->discount_value;
        } else {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher có cấu hình loại giảm giá không hợp lệ.',
            ]);
        }

        /*
         * Giới hạn tiền giảm tối đa (thường áp dụng cho percentage)
         */
        if ($voucher->max_discount_amount !== null) {
            $discount = min($discount, (float) $voucher->max_discount_amount);
        }

        /*
         * Không cho tiền giảm lớn hơn giá trị căn cứ (subtotal hoặc shippingFee)
         */
        $discount = min($discount, $baseAmount);

        /*
         * Kết quả không âm
         */
        $discount = max($discount, 0.0);

        return [
            'voucher' => $voucher,
            'discount_amount' => $discount,
            'scope' => $scope,
        ];
    }

    /**
     * Kiểm tra Voucher có hết tổng lượt sử dụng hay không.
     */
    public function isUsageLimitReached(Voucher $voucher): bool
    {
        return $voucher->usage_limit !== null
            && $voucher->usage_count >= $voucher->usage_limit;
    }

    /**
     * Kiểm tra Customer đã hết số lượt sử dụng của Voucher này hay chưa.
     */
    public function isUserLimitReached(User $user, Voucher $voucher): bool
    {
        if ($voucher->per_user_limit === null) {
            return false;
        }

        $usedCount = VoucherUsage::query()
            ->where('voucher_id', $voucher->id)
            ->where('user_id', $user->id)
            ->where('status', 'used')
            ->count();

        return $usedCount >= $voucher->per_user_limit;
    }

    /**
     * Kiểm tra Voucher có đủ điều kiện để liệt kê công khai cho Customer hay không.
     *
     * Chỉ liệt kê khi:
     * - is_public = true
     * - is_active = true
     * - start_at <= now <= end_at
     * - chưa hết tổng lượt usage_limit
     * - Customer chưa hết per_user_limit
     */
    public function isPubliclyListable(
        Voucher $voucher,
        User $user,
        ?DateTimeInterface $now = null
    ): bool {
        $now = $now ?? now();

        if (!$voucher->isPublic()) {
            return false;
        }

        if (!$voucher->is_active) {
            return false;
        }

        if ($voucher->start_at->gt($now) || $voucher->end_at->lt($now)) {
            return false;
        }

        if ($this->isUsageLimitReached($voucher)) {
            return false;
        }

        if ($this->isUserLimitReached($user, $voucher)) {
            return false;
        }

        return true;
    }
}