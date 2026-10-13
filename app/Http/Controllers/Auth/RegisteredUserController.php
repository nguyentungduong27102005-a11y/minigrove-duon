<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Hiển thị form đăng ký.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản Customer.
     */
    public function store(Request $request): RedirectResponse
    {
        /*
         * Chuẩn hóa dữ liệu trước khi validate.
         */
        $request->merge([
            'name' => trim(
                preg_replace(
                    '/\s+/',
                    ' ',
                    (string) $request->name
                )
            ),

            'email' => strtolower(
                trim(
                    (string) $request->email
                )
            ),

            'phone' => trim(
                (string) $request->phone
            ),
        ]);


        /*
         * Validation theo đặc tả MiniGrove.
         */
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:50',

                    /*
                     * Cho phép chữ tiếng Việt và khoảng trắng.
                     * Không cho số / ký tự đặc biệt.
                     */
                    'regex:/^[\pL ]+$/u',
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'phone' => [
                    'required',

                    /*
                     * Đúng 10 số và bắt đầu bằng 0.
                     */
                    'regex:/^0\d{9}$/',

                    'unique:users,phone',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    'max:64',

                    Rules\Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ],
            [
                'name.required' =>
                    'Họ và tên không được để trống.',

                'name.min' =>
                    'Họ và tên phải có ít nhất 2 ký tự.',

                'name.max' =>
                    'Họ và tên không được vượt quá 50 ký tự.',

                'name.regex' =>
                    'Họ và tên chỉ được chứa chữ cái và khoảng trắng.',


                'email.required' =>
                    'Email không được để trống.',

                'email.email' =>
                    'Email không đúng định dạng.',

                'email.unique' =>
                    'Email đã được sử dụng.',


                'phone.required' =>
                    'Số điện thoại không được để trống.',

                'phone.regex' =>
                    'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',

                'phone.unique' =>
                    'Số điện thoại đã được sử dụng.',


                'password.required' =>
                    'Mật khẩu không được để trống.',

                'password.confirmed' =>
                    'Xác nhận mật khẩu không khớp.',

                'password.max' =>
                    'Mật khẩu không được vượt quá 64 ký tự.',
            ]
        );


        /*
         * Không nhận role_id từ form.
         *
         * Tài khoản đăng ký trực tiếp
         * luôn là Customer.
         */
        $customerRole = Role::where(
            'name',
            'customer'
        )->firstOrFail();


        $user = User::create([
            'role_id' =>
                $customerRole->id,

            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'phone' =>
                $validated['phone'],

            'avatar' =>
                null,

            'position' =>
                null,

            'is_active' =>
                true,

            'password' =>
                Hash::make(
                    $validated['password']
                ),
        ]);


        event(
            new Registered($user)
        );


        /*
         * Đăng nhập ngay sau khi
         * đăng ký thành công.
         */
        Auth::login($user);


        /*
         * Customer đăng ký xong
         * quay về trang chủ.
         */
        return redirect('/');
    }
}