<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VoucherController extends Controller
{
    /**
     * Danh sách Voucher.
     */
    public function index(Request $request)
    {
        $keyword = trim(
            (string) $request->query('keyword', '')
        );

        $active = trim(
            (string) $request->query('active', '')
        );

        $period = trim(
            (string) $request->query('period', '')
        );


        $query = Voucher::query()
            ->withCount([
                'usages as used_usage_count' => function ($query) {
                    $query->where('status', 'used');
                },
            ])
            ->latest('id');


        /*
         * Tìm theo mã Voucher.
         */
        if ($keyword !== '') {
            $query->where(
                'code',
                'like',
                '%' . $keyword . '%'
            );
        }


        /*
         * Active / Inactive.
         */
        if ($active === '1') {
            $query->where('is_active', true);
        }

        if ($active === '0') {
            $query->where('is_active', false);
        }


        /*
         * Tình trạng thời gian.
         */
        $now = now();

        if ($period === 'upcoming') {
            $query->where(
                'start_at',
                '>',
                $now
            );
        }

        if ($period === 'running') {
            $query
                ->where(
                    'start_at',
                    '<=',
                    $now
                )
                ->where(
                    'end_at',
                    '>=',
                    $now
                );
        }

        if ($period === 'expired') {
            $query->where(
                'end_at',
                '<',
                $now
            );
        }


        $vouchers = $query
            ->paginate(12)
            ->withQueryString();


        $totalCount = Voucher::count();

        $activeCount = Voucher::where(
            'is_active',
            true
        )->count();

        $runningCount = Voucher::where(
            'start_at',
            '<=',
            $now
        )
            ->where(
                'end_at',
                '>=',
                $now
            )
            ->where(
                'is_active',
                true
            )
            ->count();

        $expiredCount = Voucher::where(
            'end_at',
            '<',
            $now
        )->count();


        return view(
            'admin.vouchers.index',
            compact(
                'vouchers',
                'keyword',
                'active',
                'period',
                'totalCount',
                'activeCount',
                'runningCount',
                'expiredCount'
            )
        );
    }


    /**
     * Form thêm Voucher.
     */
    public function create()
    {
        return view(
            'admin.vouchers.create'
        );
    }


    /**
     * Lưu Voucher mới.
     */
    public function store(Request $request)
    {
        $validated = $this->validateVoucher(
            $request
        );


        $validated['code'] = strtoupper(
            trim($validated['code'])
        );

        $validated['usage_count'] = 0;


        /*
         * Voucher fixed không cần
         * giới hạn giảm tối đa.
         */
        if (
            $validated['discount_type']
            === 'fixed'
        ) {
            $validated['max_discount_amount']
                = null;
        }


        Voucher::create(
            $validated
        );


        return redirect()
            ->route('admin.vouchers.index')
            ->with(
                'success',
                'Thêm Voucher thành công.'
            );
    }


    /**
     * Form sửa Voucher.
     */
    public function edit(Voucher $voucher)
    {
        return view(
            'admin.vouchers.edit',
            compact('voucher')
        );
    }


    /**
     * Cập nhật Voucher.
     */
    public function update(
        Request $request,
        Voucher $voucher
    ) {
        $validated = $this->validateVoucher(
            $request,
            $voucher
        );


        $validated['code'] = strtoupper(
            trim($validated['code'])
        );


        /*
         * Không được giảm usage_limit
         * xuống thấp hơn số lượt đã dùng.
         */
        if (
            isset($validated['usage_limit'])
            &&
            $validated['usage_limit'] !== null
            &&
            (int) $validated['usage_limit']
                < (int) $voucher->usage_count
        ) {
            throw ValidationException::withMessages([
                'usage_limit' =>
                    'Tổng lượt sử dụng không được nhỏ hơn số lượt Voucher đã được sử dụng.',
            ]);
        }


        if (
            $validated['discount_type']
            === 'fixed'
        ) {
            $validated['max_discount_amount']
                = null;
        }


        /*
         * Tuyệt đối không cập nhật
         * usage_count từ form Admin.
         */
        unset(
            $validated['usage_count']
        );


        $voucher->update(
            $validated
        );


        return redirect()
            ->route('admin.vouchers.index')
            ->with(
                'success',
                'Cập nhật Voucher thành công.'
            );
    }


    /**
     * Active / Inactive.
     */
    public function toggleStatus(
        Voucher $voucher
    ) {
        $voucher->is_active =
            !$voucher->is_active;

        $voucher->save();


        return back()->with(
            'success',
            $voucher->is_active
                ? 'Voucher đã được kích hoạt.'
                : 'Voucher đã được ngừng hoạt động.'
        );
    }


    /**
     * Xóa Voucher.
     *
     * Chỉ được xóa Voucher
     * chưa từng phát sinh lịch sử sử dụng.
     */
    public function destroy(
        Voucher $voucher
    ) {
        if ($voucher->usages()->exists()) {

            throw ValidationException::withMessages([
                'voucher' =>
                    'Voucher đã có lịch sử sử dụng nên không thể xóa. Hãy chuyển Voucher sang Inactive.',
            ]);
        }


        $voucher->delete();


        return redirect()
            ->route('admin.vouchers.index')
            ->with(
                'success',
                'Xóa Voucher thành công.'
            );
    }


    /**
     * Lịch sử sử dụng Voucher.
     */
    public function history(
        Voucher $voucher
    ) {
        $usages = $voucher
            ->usages()
            ->with([
                'user:id,name,email',
                'order:id,order_code,total_amount,order_status',
            ])
            ->latest('id')
            ->paginate(15);


        return view(
            'admin.vouchers.history',
            compact(
                'voucher',
                'usages'
            )
        );
    }


    /**
     * Validation dùng chung
     * cho Create / Update.
     */
    private function validateVoucher(
        Request $request,
        ?Voucher $voucher = null
    ): array {
        $voucherId = $voucher?->id;

        /*
         * Voucher mới có hiệu lực ngay lập tức
         * và mặc định ở trạng thái Active.
         *
         * Khi sửa Voucher, giữ nguyên start_at
         * và is_active hiện tại. Trạng thái được
         * bật/tắt từ danh sách Voucher.
         */
        $request->merge([
            'start_at' =>
                $voucher?->start_at?->format(
                    'Y-m-d H:i:s'
                )
                ?? now()->format('Y-m-d H:i:s'),

            'is_active' =>
                $voucher
                    ? (int) $voucher->is_active
                    : 1,

            'voucher_scope' =>
                $request->input(
                    'voucher_scope',
                    $voucher?->voucher_scope ?? 'order'
                ),

            'is_public' =>
                $request->has('is_public')
                    ? (bool) $request->input('is_public')
                    : ($voucher ? (bool) $voucher->is_public : true),
        ]);


        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'vouchers',
                    'code'
                )->ignore($voucherId),
            ],

            'voucher_scope' => [
                'required',
                Rule::in([
                    'order',
                    'shipping',
                ]),
            ],

            'is_public' => [
                'required',
                'boolean',
            ],

            'discount_type' => [
                'required',
                Rule::in([
                    'percentage',
                    'fixed',
                ]),
            ],

            'discount_value' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'min_order_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'max_discount_amount' => [
                'nullable',
                'numeric',
                'gt:0',
            ],

            'start_at' => [
                'required',
                'date',
            ],

            'end_at' => [
                'required',
                'date',
                'after:start_at',
            ],

            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'per_user_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

        ], [

            'code.required' =>
                'Vui lòng nhập mã Voucher.',

            'code.max' =>
                'Mã Voucher không được vượt quá 50 ký tự.',

            'code.unique' =>
                'Mã Voucher đã tồn tại.',

            'voucher_scope.required' =>
                'Vui lòng chọn phạm vi áp dụng của Voucher.',

            'voucher_scope.in' =>
                'Phạm vi áp dụng của Voucher không hợp lệ.',

            'is_public.required' =>
                'Vui lòng chọn trạng thái hiển thị công khai.',

            'is_public.boolean' =>
                'Trạng thái hiển thị công khai không hợp lệ.',

            'discount_type.required' =>
                'Vui lòng chọn loại giảm giá.',

            'discount_type.in' =>
                'Loại giảm giá không hợp lệ.',


    'discount_value.required' =>
        'Vui lòng nhập giá trị giảm.',

    'discount_value.numeric' =>
        'Giá trị giảm phải là số.',

    'discount_value.gt' =>
        'Giá trị giảm phải lớn hơn 0.',


    'min_order_amount.required' =>
        'Vui lòng nhập giá trị đơn tối thiểu.',

    'min_order_amount.numeric' =>
        'Giá trị đơn tối thiểu phải là số.',

    'min_order_amount.min' =>
        'Giá trị đơn tối thiểu không được nhỏ hơn 0.',


    'max_discount_amount.numeric' =>
        'Số tiền giảm tối đa phải là số.',

    'max_discount_amount.gt' =>
        'Số tiền giảm tối đa phải lớn hơn 0.',


    'start_at.required' =>
        'Vui lòng chọn thời gian bắt đầu.',

    'start_at.date' =>
        'Thời gian bắt đầu không hợp lệ.',


    'end_at.required' =>
        'Vui lòng chọn thời gian kết thúc.',

    'end_at.date' =>
        'Thời gian kết thúc không hợp lệ.',

    'end_at.after' =>
        'Thời gian kết thúc phải sau thời gian bắt đầu.',


    'usage_limit.integer' =>
        'Tổng lượt sử dụng phải là số nguyên.',

    'usage_limit.min' =>
        'Tổng lượt sử dụng phải từ 1 trở lên.',


    'per_user_limit.integer' =>
        'Giới hạn mỗi Customer phải là số nguyên.',

    'per_user_limit.min' =>
        'Giới hạn mỗi Customer phải từ 1 trở lên.',


    'is_active.required' =>
        'Vui lòng chọn trạng thái Voucher.',

    'is_active.boolean' =>
        'Trạng thái Voucher không hợp lệ.',

]);


        /*
         * Percentage không được > 100%.
         */
        if (
            $validated['discount_type']
                === 'percentage'
            &&
            (float) $validated['discount_value']
                > 100
        ) {
            throw ValidationException::withMessages([
                'discount_value' =>
                    'Voucher phần trăm không được giảm quá 100%.',
            ]);
        }


        /*
         * Với Voucher phần trăm,
         * cho phép max_discount_amount
         * để giới hạn tiền giảm.
         *
         * Với fixed sẽ tự chuyển thành NULL
         * trong store/update.
         */


        /*
         * Mỗi Customer không thể được phép
         * dùng nhiều hơn tổng lượt Voucher.
         */
        if (
            !empty($validated['usage_limit'])
            &&
            !empty($validated['per_user_limit'])
            &&
            (int) $validated['per_user_limit']
                > (int) $validated['usage_limit']
        ) {
            throw ValidationException::withMessages([
                'per_user_limit' =>
                    'Giới hạn mỗi Customer không được lớn hơn tổng lượt sử dụng Voucher.',
            ]);
        }


        return $validated;
    }
}