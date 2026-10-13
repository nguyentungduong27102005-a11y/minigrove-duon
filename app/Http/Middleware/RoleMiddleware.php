<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Kiểm tra Role của User đang đăng nhập.
     *
     * Ví dụ:
     * role:admin
     * role:staff
     * role:admin,staff
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        $user = $request->user();

        /*
         * Trường hợp chưa đăng nhập.
         * Thực tế middleware auth sẽ xử lý trước.
         */
        if (!$user) {
            return redirect()->route('login');
        }

        /*
         * Lấy Role của User.
         */
        $roleName = $user->role?->name;

        /*
         * Không có Role hoặc Role không được phép.
         */
        if (
            !$roleName ||
            !in_array($roleName, $roles, true)
        ) {
            abort(
                403,
                'Bạn không có quyền truy cập khu vực này.'
            );
        }

        return $next($request);
    }
}