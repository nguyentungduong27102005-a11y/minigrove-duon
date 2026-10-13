<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AddressController extends Controller
{
    public function index(Request $request)
{
    $addresses = $request->user()
        ->addresses()
        ->orderByDesc('is_default')
        ->orderByDesc('id')
        ->get();

    return view('profile.addresses', [
        'user' => $request->user(),
        'addresses' => $addresses,
    ]);
}
    /**
     * Thêm địa chỉ giao hàng.
     */
    public function store(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate(
            [
                'receiver_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                ],

                'receiver_phone' => [
                    'required',
                    'regex:/^0\d{9}$/',
                ],

                'province' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'district' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'ward' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'ghn_district_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'ghn_ward_code' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'address_detail' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'label' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'is_default' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'receiver_name.required' =>
                    'Vui lòng nhập tên người nhận.',

                'receiver_phone.required' =>
                    'Vui lòng nhập số điện thoại.',

                'receiver_phone.regex' =>
                    'Số điện thoại phải gồm 10 số và bắt đầu bằng 0.',

                'province.required' =>
                    'Vui lòng nhập Tỉnh/Thành phố.',

                'district.required' =>
                    'Vui lòng nhập Quận/Huyện.',

                'ward.required' =>
                    'Vui lòng nhập Phường/Xã.',

                'ghn_district_id.required' =>
                    'Vui lòng chọn Quận/Huyện hợp lệ.',

                'ghn_district_id.integer' =>
                    'Mã Quận/Huyện GHN không hợp lệ.',

                'ghn_ward_code.required' =>
                    'Vui lòng chọn Phường/Xã hợp lệ.',

                'ghn_ward_code.string' =>
                    'Mã Phường/Xã GHN không hợp lệ.',

                'address_detail.required' =>
                    'Vui lòng nhập địa chỉ chi tiết.',
            ]
        );

        $user = $request->user();
        $fromProfile =
    $request->input('context') === 'profile';

        /*
         * Địa chỉ đầu tiên tự động
         * trở thành mặc định.
         */
        $makeDefault =
            $request->boolean('is_default')
            ||
            !$user->addresses()->exists();

        $address = DB::transaction(
            function () use (
                $user,
                $validated,
                $makeDefault
            ) {

                /*
                 * Nếu địa chỉ mới được chọn làm mặc định
                 * thì bỏ mặc định của các địa chỉ cũ.
                 */
                if ($makeDefault) {

                    $user->addresses()
                        ->update([
                            'is_default' => false,
                        ]);
                }

                return $user->addresses()
                    ->create([

                        'receiver_name' =>
                            $validated[
                                'receiver_name'
                            ],

                        'receiver_phone' =>
                            $validated[
                                'receiver_phone'
                            ],

                        'province' =>
                            $validated[
                                'province'
                            ],

                        'district' =>
                            $validated[
                                'district'
                            ],

                        'ward' =>
                            $validated[
                                'ward'
                            ],

                        'ghn_district_id' =>
                            (int) $validated[
                                'ghn_district_id'
                            ],

                        'ghn_ward_code' =>
                            $validated[
                                'ghn_ward_code'
                            ],

                        'address_detail' =>
                            $validated[
                                'address_detail'
                            ],

                        'label' =>
                            $validated[
                                'label'
                            ] ?? null,

                        'is_default' =>
                            $makeDefault,
                    ]);
            }
        );

        /*
         * Tự chọn địa chỉ vừa thêm
         * cho Checkout hiện tại.
         */
        if (!$fromProfile) {

    session([
        'checkout.address_id'
            => $address->id,
    ]);
}

return redirect()
    ->route(
        $fromProfile
            ? 'profile.addresses'
            : 'checkout.index'
    )
    ->with(
        'success',
        $fromProfile
            ? 'Đã thêm địa chỉ mới.'
            : 'Đã thêm địa chỉ giao hàng.'
    );
    }


    /**
     * Chọn một địa chỉ đã lưu.
     */
    public function select(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate([
            'address_id' => [
                'required',
                'integer',
            ],
        ]);

        /*
         * Chỉ lấy Address thuộc User hiện tại.
         */
        $address =
            $request->user()
                ->addresses()
                ->findOrFail(
                    $validated[
                        'address_id'
                    ]
                );

        /*
         * Địa chỉ phải có mã GHN hợp lệ.
         */
        if (
            !$address->ghn_district_id
            ||
            !$address->ghn_ward_code
        ) {

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Địa chỉ này chưa có thông tin GHN. Vui lòng sửa hoặc thêm lại địa chỉ.'
                );
        }

        session([
            'checkout.address_id'
                => $address->id,
        ]);

        return redirect()
            ->route('checkout.index')
            ->with(
                'success',
                'Đã chọn địa chỉ giao hàng.'
            );
    }


    /**
     * Sửa địa chỉ giao hàng.
     */
    public function update(
        Request $request,
        Address $address
    ): RedirectResponse {

        /*
         * Chỉ cho phép sửa địa chỉ
         * thuộc tài khoản hiện tại.
         */
        if (
            $address->user_id
            !==
            $request->user()->id
        ) {

            abort(404);
        }
        $fromProfile =
    $request->input('context') === 'profile';

        $validated = $request->validate(
            [
                'receiver_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                ],

                'receiver_phone' => [
                    'required',
                    'regex:/^0\d{9}$/',
                ],

                'province' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'district' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'ward' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'ghn_district_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'ghn_ward_code' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'address_detail' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'label' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'is_default' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'receiver_name.required' =>
                    'Vui lòng nhập tên người nhận.',

                'receiver_phone.required' =>
                    'Vui lòng nhập số điện thoại.',

                'receiver_phone.regex' =>
                    'Số điện thoại phải gồm 10 số và bắt đầu bằng 0.',

                'province.required' =>
                    'Vui lòng chọn Tỉnh/Thành phố.',

                'district.required' =>
                    'Vui lòng chọn Quận/Huyện.',

                'ward.required' =>
                    'Vui lòng chọn Phường/Xã.',

                'ghn_district_id.required' =>
                    'Vui lòng chọn Quận/Huyện hợp lệ.',

                'ghn_ward_code.required' =>
                    'Vui lòng chọn Phường/Xã hợp lệ.',

                'address_detail.required' =>
                    'Vui lòng nhập địa chỉ chi tiết.',
            ]
        );

        $user = $request->user();

        $makeDefault =
            $request->boolean('is_default');

        DB::transaction(
            function () use (
                $user,
                $address,
                $validated,
                $makeDefault
            ) {

                /*
                 * Nếu địa chỉ này được đặt làm mặc định
                 * thì bỏ mặc định của các địa chỉ khác.
                 */
                if ($makeDefault) {

                    $user->addresses()
                        ->where(
                            'id',
                            '!=',
                            $address->id
                        )
                        ->update([
                            'is_default' => false,
                        ]);
                }

                $address->update([

                    'receiver_name' =>
                        $validated[
                            'receiver_name'
                        ],

                    'receiver_phone' =>
                        $validated[
                            'receiver_phone'
                        ],

                    'province' =>
                        $validated[
                            'province'
                        ],

                    'district' =>
                        $validated[
                            'district'
                        ],

                    'ward' =>
                        $validated[
                            'ward'
                        ],

                    /*
                     * Quan trọng:
                     * cập nhật lại mã GHN
                     * khi địa chỉ thay đổi.
                     */
                    'ghn_district_id' =>
                        (int) $validated[
                            'ghn_district_id'
                        ],

                    'ghn_ward_code' =>
                        $validated[
                            'ghn_ward_code'
                        ],

                    'address_detail' =>
                        $validated[
                            'address_detail'
                        ],

                    'label' =>
                        $validated[
                            'label'
                        ] ?? null,

                    'is_default' =>
                        $makeDefault
                        ||
                        $address->is_default,
                ]);
            }
        );

        /*
         * Nếu đang sửa đúng địa chỉ Checkout
         * thì giữ nguyên address_id trong session.
         */
        if (
            (int) session(
                'checkout.address_id'
            )
            ===
            (int) $address->id
        ) {

            session([
                'checkout.address_id'
                    => $address->id,
            ]);
        }

        return redirect()
    ->route(
        $fromProfile
            ? 'profile.addresses'
            : 'checkout.index'
    )
    ->with(
        'success',
        $fromProfile
            ? 'Đã cập nhật địa chỉ.'
            : 'Đã cập nhật địa chỉ giao hàng.'
    );
    }


    /**
     * Xóa địa chỉ giao hàng.
     */
    public function destroy(
        Request $request,
        Address $address
    ): RedirectResponse {

        /*
         * Không cho xóa địa chỉ của user khác.
         */
        if (
            $address->user_id
            !==
            $request->user()->id
        ) {

            abort(404);
        }
        $fromProfile =
    $request->input('context') === 'profile';

        $user = $request->user();

        $wasDefault =
            (bool) $address->is_default;

        $wasSelected =
            (int) session(
                'checkout.address_id'
            )
            ===
            (int) $address->id;

        DB::transaction(
            function () use (
                $address,
                $user,
                $wasDefault
            ) {

                $address->delete();

                /*
                 * Nếu xóa địa chỉ mặc định,
                 * tự chọn một địa chỉ khác làm mặc định.
                 */
                if ($wasDefault) {

                    $replacement =
                        $user->addresses()
                            ->orderByDesc('id')
                            ->first();

                    if ($replacement) {

                        $replacement->update([
                            'is_default' => true,
                        ]);
                    }
                }
            }
        );

        /*
         * Nếu xóa đúng địa chỉ đang được chọn
         * trong Checkout thì xóa session cũ.
         */
        if ($wasSelected) {

            session()->forget(
                'checkout.address_id'
            );

            /*
             * Nếu còn địa chỉ khác hợp lệ,
             * tự chọn địa chỉ mặc định mới.
             */
            $replacement =
                $user->addresses()
                    ->whereNotNull(
                        'ghn_district_id'
                    )
                    ->whereNotNull(
                        'ghn_ward_code'
                    )
                    ->orderByDesc(
                        'is_default'
                    )
                    ->orderByDesc(
                        'id'
                    )
                    ->first();

            if ($replacement) {

                session([
                    'checkout.address_id'
                        => $replacement->id,
                ]);
            }
        }

        return redirect()
    ->route(
        $fromProfile
            ? 'profile.addresses'
            : 'checkout.index'
    )
    ->with(
        'success',
        $fromProfile
            ? 'Đã xóa địa chỉ.'
            : 'Đã xóa địa chỉ giao hàng.'
    );

            
    }


    /**
     * Lấy danh sách Tỉnh/Thành phố từ GHN.
     *
     * GHN Token chỉ được sử dụng ở Backend.
     */
    public function ghnProvinces(): JsonResponse
    {
        $response = Http::withHeaders([
            'Token' => config('services.ghn.token'),
        ])->get(
            rtrim(
                config('services.ghn.base_url'),
                '/'
            ) . '/master-data/province'
        );

        if (!$response->successful()) {
            throw new RuntimeException(
                'Không thể lấy danh sách Tỉnh/Thành phố từ GHN.'
            );
        }

        $result = $response->json();

        if (($result['code'] ?? null) != 200) {
            throw new RuntimeException(
                $result['message']
                ?? 'GHN không trả về danh sách Tỉnh/Thành phố.'
            );
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'] ?? [],
        ]);
    }


    /**
     * Lấy danh sách Quận/Huyện theo ProvinceID.
     */
    public function ghnDistricts(
        int $provinceId
    ): JsonResponse {

        $response = Http::withHeaders([
            'Token' => config('services.ghn.token'),
        ])->get(
            rtrim(
                config('services.ghn.base_url'),
                '/'
            ) . '/master-data/district',
            [
                'province_id' => $provinceId,
            ]
        );

        if (!$response->successful()) {
            throw new RuntimeException(
                'Không thể lấy danh sách Quận/Huyện từ GHN.'
            );
        }

        $result = $response->json();

        if (($result['code'] ?? null) != 200) {
            throw new RuntimeException(
                $result['message']
                ?? 'GHN không trả về danh sách Quận/Huyện.'
            );
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'] ?? [],
        ]);
    }


    /**
     * Lấy danh sách Phường/Xã theo DistrictID.
     */
    public function ghnWards(
        int $districtId
    ): JsonResponse {

        $response = Http::withHeaders([
            'Token' => config('services.ghn.token'),
        ])->get(
            rtrim(
                config('services.ghn.base_url'),
                '/'
            ) . '/master-data/ward',
            [
                'district_id' => $districtId,
            ]
        );

        if (!$response->successful()) {
            throw new RuntimeException(
                'Không thể lấy danh sách Phường/Xã từ GHN.'
            );
        }

        $result = $response->json();

        if (($result['code'] ?? null) != 200) {
            throw new RuntimeException(
                $result['message']
                ?? 'GHN không trả về danh sách Phường/Xã.'
            );
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'] ?? [],
        ]);
    }
}