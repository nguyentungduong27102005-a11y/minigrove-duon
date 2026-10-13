<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Request này được phép thực thi.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation đăng nhập.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Thông báo validation.
     */
    public function messages(): array
    {
        return [
            'email.required' =>
                'Email không được để trống.',

            'email.email' =>
                'Email không đúng định dạng.',

            'password.required' =>
                'Mật khẩu không được để trống.',
        ];
    }

    /**
     * Xác thực tài khoản.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();


        /*
         * Chuẩn hóa Email.
         */
        $email = strtolower(
            trim(
                (string) $this->input('email')
            )
        );


        /*
         * Tìm User trước để kiểm tra trạng thái.
         */
        $user = User::where(
            'email',
            $email
        )->first();


        /*
         * Nếu tài khoản tồn tại nhưng đã bị khóa
         * thì không cho đăng nhập.
         */
        if (
            $user &&
            !$user->is_active
        ) {
            throw ValidationException::withMessages([
                'email' =>
                    'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ]);
        }


        /*
         * Kiểm tra Email + Password.
         */
        if (
            !Auth::attempt(
                [
                    'email' => $email,
                    'password' =>
                        $this->input('password'),
                ],
                $this->boolean('remember')
            )
        ) {
            RateLimiter::hit(
                $this->throttleKey()
            );

            throw ValidationException::withMessages([
                'email' =>
                    'Email hoặc mật khẩu không chính xác.',
            ]);
        }


        /*
         * Đăng nhập thành công
         * => xóa bộ đếm thử đăng nhập sai.
         */
        RateLimiter::clear(
            $this->throttleKey()
        );
    }

    /**
     * Chống thử đăng nhập quá nhiều lần.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (
            !RateLimiter::tooManyAttempts(
                $this->throttleKey(),
                5
            )
        ) {
            return;
        }


        event(
            new Lockout($this)
        );


        $seconds =
            RateLimiter::availableIn(
                $this->throttleKey()
            );


        throw ValidationException::withMessages([
            'email' =>
                'Bạn đã thử đăng nhập quá nhiều lần. Vui lòng thử lại sau '
                . $seconds
                . ' giây.',
        ]);
    }

    /**
     * Khóa theo Email + IP.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower(
                trim(
                    (string) $this->input('email')
                )
            )
            . '|'
            . $this->ip()
        );
    }
}