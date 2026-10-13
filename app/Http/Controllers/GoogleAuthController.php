<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Chuyển người dùng sang Google để đăng nhập.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google callback sau khi người dùng đăng nhập thành công.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Tìm tài khoản theo Google ID.
            $user = User::where(
                'google_id',
                $googleUser->getId()
            )->first();

            // Nếu chưa liên kết Google ID,
            // thử tìm theo email.
            if (!$user && $googleUser->getEmail()) {
                $user = User::where(
                    'email',
                    $googleUser->getEmail()
                )->first();
            }

            if ($user) {
                // Tài khoản đã tồn tại.
                // Liên kết Google ID nếu chưa có.
                $user->google_id = $googleUser->getId();
                $user->avatar = $googleUser->getAvatar();

                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                }

                $user->save();
            } else {
                // Lấy role Customer.
                $customerRole = Role::where(
                    'name',
                    'customer'
                )->first();

                if (!$customerRole) {
                    return redirect()
                        ->route('login')
                        ->withErrors([
                            'email' => 'Không tìm thấy role customer.',
                        ]);
                }

                // Tạo tài khoản Customer mới.
                $user = User::create([
                    'role_id' => $customerRole->id,
                    'name' => $googleUser->getName()
                        ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'phone' => null,
                    'position' => null,
                    'is_active' => true,

                    // Password ngẫu nhiên vì database
                    // đang yêu cầu password NOT NULL.
                    'password' => Str::random(40),
                ]);

                $user->email_verified_at = now();
                $user->save();
            }

            // Kiểm tra tài khoản có đang hoạt động không.
            if (!$user->is_active) {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' => 'Tài khoản của bạn đã bị khóa.',
                    ]);
            }

            // Đăng nhập vào Laravel.
            Auth::login($user, true);

            // Tạo session mới sau khi đăng nhập.
            request()->session()->regenerate();

            return redirect()->route('home');

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Đăng nhập bằng Google thất bại. Vui lòng thử lại.',
                ]);
        }
    }
}