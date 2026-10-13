@extends('layouts.customer')

@section('title', 'Đổi mật khẩu - MiniGrove')

@push('styles')
<style>
    .account-page {
        padding: 42px 0 70px;
        background: #f7f9f8;
        min-height: 700px;
    }

    .account-layout {
        display: grid;
        grid-template-columns: 260px minmax(0, 1fr);
        gap: 28px;
        align-items: start;
    }

    /* =========================================================
       SIDEBAR
    ========================================================= */

    .account-sidebar {
        background: #fff;
        border: 1px solid #e3e9e5;
        border-radius: 14px;
        overflow: hidden;
    }

    .account-user {
        display: flex;
        align-items: center;
        gap: 12px;

        padding: 22px 18px;

        border-bottom: 1px solid #edf1ef;
    }

    .account-user-avatar {
        width: 52px;
        height: 52px;
        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        overflow: hidden;
        border-radius: 50%;

        background: #e8f2ec;
        color: #174d37;

        font-size: 20px;
        font-weight: 700;
    }

    .account-user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .account-user-info {
        min-width: 0;
    }

    .account-user-info strong {
        display: block;

        color: #173f30;
        font-size: 15px;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .account-user-info span {
        display: block;
        margin-top: 3px;

        color: #77847e;
        font-size: 13px;
    }

    .account-menu {
        padding: 10px;
    }

    .account-menu-title,
    .account-menu-link {
        display: flex;
        align-items: center;
        gap: 10px;

        width: 100%;
        box-sizing: border-box;

        padding: 11px 12px;

        border-radius: 9px;

        color: #31483e;
        text-decoration: none;

        font-size: 14px;
    }

    .account-menu-title {
        color: #174d37;
        font-weight: 700;
    }

    .account-menu-sub {
        padding-left: 36px;
    }

    .account-menu-sub a {
        display: block;

        padding: 8px 10px;

        border-radius: 7px;

        color: #66736d;
        text-decoration: none;

        font-size: 13px;
    }

    .account-menu-link:hover,
    .account-menu-sub a:hover,
    .account-menu-sub a.active {
        background: #eef6f1;
        color: #174d37;
    }

    .account-menu-sub a.active {
        font-weight: 700;
    }


    /* =========================================================
       MAIN
    ========================================================= */

    .account-main {
        background: #fff;

        border: 1px solid #e3e9e5;
        border-radius: 14px;

        padding: 30px;
    }

    .account-heading {
        padding-bottom: 20px;
        margin-bottom: 28px;

        border-bottom: 1px solid #edf1ef;
    }

    .account-heading h1 {
        margin: 0 0 6px;

        color: #173f30;
        font-size: 25px;
    }

    .account-heading p {
        margin: 0;

        color: #75827c;
        font-size: 14px;
    }


    /* =========================================================
       PASSWORD FORM
    ========================================================= */

    .password-form {
        max-width: 650px;
    }

    .password-field {
        margin-bottom: 20px;
    }

    .password-field label {
        display: block;

        margin-bottom: 7px;

        color: #31483e;

        font-size: 14px;
        font-weight: 700;
    }

    .password-field input {
        width: 100%;
        min-height: 46px;

        box-sizing: border-box;

        padding: 10px 13px;

        border: 1px solid #d8e1dc;
        border-radius: 9px;

        background: #fff;
        color: #263a31;

        font: inherit;

        outline: none;
    }

    .password-field input:focus {
        border-color: #246b4d;

        box-shadow:
            0 0 0 3px
            rgba(36, 107, 77, .08);
    }

    .password-error {
        margin-top: 6px;

        color: #b42318;

        font-size: 12px;
    }

    .password-note {
        margin-bottom: 25px;

        padding: 14px 16px;

        border: 1px solid #dce8e1;
        border-radius: 9px;

        background: #f5faf7;
        color: #607068;

        font-size: 13px;
        line-height: 1.6;
    }

    .password-note-button {
        padding: 0;

        border: 0;
        background: transparent;

        color: #174d37;

        font: inherit;
        font-weight: 700;

        text-decoration: underline;

        cursor: pointer;
    }

    .password-actions {
        display: flex;
        align-items: center;
        gap: 14px;

        margin-top: 26px;
    }

    .password-submit {
        min-width: 135px;
        min-height: 44px;

        border: 0;
        border-radius: 9px;

        background: #174d37;
        color: #fff;

        font-weight: 700;

        cursor: pointer;
    }

    .password-submit:hover {
        background: #123d2c;
    }

    .password-success {
        color: #24734f;

        font-size: 13px;
        font-weight: 700;
    }


    /* =========================================================
       SHOW / HIDE PASSWORD
    ========================================================= */

    .password-input-wrap {
        position: relative;
        width: 100%;
    }

    .password-input-wrap input {
        padding-right: 52px;
    }

    .password-toggle {
        position: absolute;

        top: 50%;
        right: 14px;

        z-index: 20;

        width: 32px;
        height: 32px;

        padding: 0;

        transform: translateY(-50%);

        display: flex;
        align-items: center;
        justify-content: center;

        border: 0;
        background: transparent;

        color: #66736d;

        cursor: pointer;
        pointer-events: auto;
    }

    .password-toggle:hover {
        color: #174d37;
    }

    .password-toggle:focus {
        outline: none;
    }

    .toggle-eye {
        width: 21px;
        height: 21px;

        pointer-events: none;
    }

    .eye-open {
        display: block;
    }

    .eye-closed {
        display: none;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 900px) {

        .account-layout {
            grid-template-columns: 1fr;
        }

    }
</style>
@endpush


@section('content')

<section class="account-page">

    <div class="customer-container">

        <div class="account-layout">

            {{-- =================================================
                 SIDEBAR
            ================================================== --}}
            <aside class="account-sidebar">

                <div class="account-user">

                    <div class="account-user-avatar">

                        @if ($user->avatar)

                            <img
                                src="{{ $user->avatar }}"
                                alt="{{ $user->name }}"
                            >

                        @else

                            {{
                                strtoupper(
                                    mb_substr(
                                        $user->name,
                                        0,
                                        1
                                    )
                                )
                            }}

                        @endif

                    </div>


                    <div class="account-user-info">

                        <strong>
                            {{ $user->name }}
                        </strong>

                        <span>
                            Khách hàng
                        </span>

                    </div>

                </div>


                <nav class="account-menu">

                    <div>

                        <div class="account-menu-title">
                            Tài khoản của tôi
                        </div>


                        <div class="account-menu-sub">

                            <a
                                href="{{ route('profile.edit') }}"
                            >
                                Hồ sơ
                            </a>


                            <a
                                href="{{ route('profile.addresses') }}"
                            >
                                Địa chỉ
                            </a>


                            <a
                                href="{{ route('profile.password') }}"
                                class="active"
                            >
                                Đổi mật khẩu
                            </a>

                        </div>

                    </div>


                    <a
                        href="{{ route('orders.index') }}"
                        class="account-menu-link"
                    >
                        Đơn mua
                    </a>


                    <a
                        href="{{ route('profile.vouchers') }}"
                        class="account-menu-link"
                    >
                        Kho Voucher
                    </a>

                </nav>

            </aside>


            {{-- =================================================
                 MAIN
            ================================================== --}}
            <main class="account-main">

                <div class="account-heading">

                    <h1>
                        Đổi mật khẩu
                    </h1>

                    <p>
                        Cập nhật mật khẩu để bảo vệ tài khoản của bạn.
                    </p>

                </div>


                <div class="password-form">

                    {{-- GOOGLE ACCOUNT --}}
                    @if ($user->google_id)

                        <div class="password-note">

                            Tài khoản của bạn đã liên kết với Google.

                            Nếu bạn đăng ký MiniGrove bằng Google
                            và chưa có mật khẩu riêng, hãy

                            <form
                                method="POST"
                                action="{{ route('profile.password.reset') }}"
                                style="display: inline;"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="password-note-button"
                                >
                                    thiết lập mật khẩu
                                </button>

                            </form>

                            để tạo mật khẩu đăng nhập cho MiniGrove.

                        </div>

                    @endif


                    {{-- =================================================
                         CHANGE PASSWORD FORM
                    ================================================== --}}
                    <form
                        method="POST"
                        action="{{ route('password.update') }}"
                    >

                        @csrf
                        @method('PUT')


                        {{-- =============================================
                             CURRENT PASSWORD
                        ============================================== --}}
                        <div class="password-field">

                            <label for="current_password">
                                Mật khẩu hiện tại
                            </label>


                            <div class="password-input-wrap">

                                <input
                                    id="current_password"
                                    type="password"
                                    name="current_password"
                                    autocomplete="current-password"
                                    required
                                >


                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="current_password"
                                    aria-label="Hiện mật khẩu"
                                >

                                    {{-- EYE OPEN --}}
                                    <svg
                                        class="toggle-eye eye-open"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5.25 12 5.25c4.477 0 8.268 2.693 9.542 6.75-1.274 4.057-5.065 6.75-9.542 6.75-4.477 0-8.268-2.693-9.542-6.75Z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                        />

                                    </svg>


                                    {{-- EYE CLOSED --}}
                                    <svg
                                        class="toggle-eye eye-closed"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M10.584 10.587A2.25 2.25 0 0 0 12 14.25a2.25 2.25 0 0 0 1.664-.737"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.88 5.09A9.77 9.77 0 0 1 12 4.875c4.478 0 8.27 2.943 9.543 7.125a11.7 11.7 0 0 1-4.1 5.94M6.228 6.227A11.706 11.706 0 0 0 2.457 12c.502 1.65 1.393 3.127 2.58 4.318"
                                        />

                                    </svg>

                                </button>

                            </div>


                            @error(
                                'current_password',
                                'updatePassword'
                            )

                                <div class="password-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- =============================================
                             NEW PASSWORD
                        ============================================== --}}
                        <div class="password-field">

                            <label for="password">
                                Mật khẩu mới
                            </label>


                            <div class="password-input-wrap">

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    autocomplete="new-password"
                                    required
                                >


                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="password"
                                    aria-label="Hiện mật khẩu"
                                >

                                    {{-- EYE OPEN --}}
                                    <svg
                                        class="toggle-eye eye-open"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5.25 12 5.25c4.477 0 8.268 2.693 9.542 6.75-1.274 4.057-5.065 6.75-9.542 6.75-4.477 0-8.268-2.693-9.542-6.75Z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                        />

                                    </svg>


                                    {{-- EYE CLOSED --}}
                                    <svg
                                        class="toggle-eye eye-closed"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M10.584 10.587A2.25 2.25 0 0 0 12 14.25a2.25 2.25 0 0 0 1.664-.737"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.88 5.09A9.77 9.77 0 0 1 12 4.875c4.478 0 8.27 2.943 9.543 7.125a11.7 11.7 0 0 1-4.1 5.94M6.228 6.227A11.706 11.706 0 0 0 2.457 12c.502 1.65 1.393 3.127 2.58 4.318"
                                        />

                                    </svg>

                                </button>

                            </div>


                            @error(
                                'password',
                                'updatePassword'
                            )

                                <div class="password-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- =============================================
                             CONFIRM PASSWORD
                        ============================================== --}}
                        <div class="password-field">

                            <label for="password_confirmation">
                                Xác nhận mật khẩu mới
                            </label>


                            <div class="password-input-wrap">

                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    autocomplete="new-password"
                                    required
                                >


                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="password_confirmation"
                                    aria-label="Hiện mật khẩu"
                                >

                                    {{-- EYE OPEN --}}
                                    <svg
                                        class="toggle-eye eye-open"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5.25 12 5.25c4.477 0 8.268 2.693 9.542 6.75-1.274 4.057-5.065 6.75-9.542 6.75-4.477 0-8.268-2.693-9.542-6.75Z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                        />

                                    </svg>


                                    {{-- EYE CLOSED --}}
                                    <svg
                                        class="toggle-eye eye-closed"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M10.584 10.587A2.25 2.25 0 0 0 12 14.25a2.25 2.25 0 0 0 1.664-.737"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.88 5.09A9.77 9.77 0 0 1 12 4.875c4.478 0 8.27 2.943 9.543 7.125a11.7 11.7 0 0 1-4.1 5.94M6.228 6.227A11.706 11.706 0 0 0 2.457 12c.502 1.65 1.393 3.127 2.58 4.318"
                                        />

                                    </svg>

                                </button>

                            </div>


                            @error(
                                'password_confirmation',
                                'updatePassword'
                            )

                                <div class="password-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- =============================================
                             SUBMIT
                        ============================================== --}}
                        <div class="password-actions">

                            <button
                                type="submit"
                                class="password-submit"
                            >
                                Lưu mật khẩu
                            </button>


                            @if (
                                session('status')
                                === 'password-updated'
                            )

                                <span class="password-success">
                                    ✓ Đã cập nhật mật khẩu
                                </span>

                            @endif

                        </div>

                    </form>

                </div>

            </main>

        </div>

    </div>

</section>


{{-- =========================================================
     PASSWORD TOGGLE SCRIPT
========================================================= --}}
<script>
    document.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '[data-password-toggle]'
                );

            if (!button) {
                return;
            }

            const input =
                document.getElementById(
                    button.dataset.passwordToggle
                );

            if (!input) {
                return;
            }

            const eyeOpen =
                button.querySelector(
                    '.eye-open'
                );

            const eyeClosed =
                button.querySelector(
                    '.eye-closed'
                );

            const isHidden =
                input.type === 'password';


            if (isHidden) {

                input.type = 'text';

                eyeOpen.style.display =
                    'none';

                eyeClosed.style.display =
                    'block';

                button.setAttribute(
                    'aria-label',
                    'Ẩn mật khẩu'
                );

            } else {

                input.type = 'password';

                eyeOpen.style.display =
                    'block';

                eyeClosed.style.display =
                    'none';

                button.setAttribute(
                    'aria-label',
                    'Hiện mật khẩu'
                );
            }
        }
    );
</script>

@endsection