<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Hiển thị hồ sơ Customer.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }


    /**
     * Hiển thị trang đổi mật khẩu.
     */
    public function password(Request $request): View
    {
        return view('profile.password', [
            'user' => $request->user(),
        ]);
    }


    /**
     * Cập nhật hồ sơ.
     */
    public function update(
        ProfileUpdateRequest $request
    ): RedirectResponse {

        $user = $request->user();

        $validated = $request->validated();


        /*
         * Nếu có upload Avatar mới.
         */
        if ($request->hasFile('avatar')) {

            /*
             * Chỉ xóa avatar cũ nếu đó là file local.
             *
             * Avatar Google là URL nên tuyệt đối
             * không xử lý bằng Storage.
             */
            if (
                $user->avatar
                && !str_starts_with(
                    $user->avatar,
                    'http://'
                )
                && !str_starts_with(
                    $user->avatar,
                    'https://'
                )
                && Storage::disk('public')
                    ->exists($user->avatar)
            ) {

                Storage::disk('public')
                    ->delete($user->avatar);
            }


            /*
             * Lưu Avatar mới vào:
             *
             * storage/app/public/avatars
             */
            $validated['avatar'] =
                $request
                    ->file('avatar')
                    ->store(
                        'avatars',
                        'public'
                    );
        } else {

            /*
             * Không upload ảnh mới
             * thì giữ nguyên Avatar hiện tại.
             */
            unset($validated['avatar']);
        }


        $user->fill($validated);

        $user->save();


        return Redirect::route(
            'profile.edit'
        )->with(
            'status',
            'profile-updated'
        );
    }


    /**
     * Đăng xuất tài khoản hiện tại
     * rồi chuyển sang luồng thiết lập
     * hoặc đặt lại mật khẩu.
     */
    public function startPasswordReset(
        Request $request
    ): RedirectResponse {

        $email = $request->user()->email;

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect()
            ->route('password.request')
            ->withInput([
                'email' => $email,
            ]);
    }


    /**
     * Xóa tài khoản.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {

        $request->validateWithBag(
            'userDeletion',
            [
                'password' => [
                    'required',
                    'current_password',
                ],
            ]
        );


        $user = $request->user();


        Auth::logout();

        $user->delete();


        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return Redirect::to('/');
    }
}