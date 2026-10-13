<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Hiển thị trang đăng nhập.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập.
     */
    public function store(
        LoginRequest $request
    ): RedirectResponse {
        /*
         * Kiểm tra Email + Password
         * + trạng thái tài khoản.
         */
        $request->authenticate();


        /*
         * Chống Session Fixation.
         */
        $request->session()
            ->regenerate();


        $user = $request->user();

        $roleName =
            $user->role?->name;


        /*
         * Chuyển hướng theo Role.
         */
        return match ($roleName) {

            'admin' =>
                redirect()->intended(
                    '/admin/dashboard'
                ),


            'customer' =>
    $this->customerRedirect($request),

            default =>
                redirect('/'),
        };
    }
/**
 * Chuyển Customer về trang trước khi Login.
 */
private function customerRedirect(
    Request $request
): RedirectResponse {

    $redirect =
        $request->input('redirect');


    /*
     * Chỉ chấp nhận đường dẫn nội bộ.
     * Tránh redirect sang website bên ngoài.
     */
    if (
        is_string($redirect) &&
        str_starts_with($redirect, '/') &&
        !str_starts_with($redirect, '//')
    ) {
        return redirect($redirect);
    }


    return redirect()->intended('/');
}
    /**
     * Đăng xuất.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {
        Auth::guard('web')->logout();


        /*
         * Hủy Session cũ.
         */
        $request->session()
            ->invalidate();


        /*
         * Sinh CSRF Token mới.
         */
        $request->session()
            ->regenerateToken();


        return redirect('/');
    }
}