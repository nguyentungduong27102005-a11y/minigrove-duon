<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Đặt lại mật khẩu - MiniGrove</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/auth.css') }}"
    >

</head>

<body>

<div class="auth-page">

    {{-- =========================
         BRAND
    ========================== --}}
    <section class="auth-brand">

        <div class="auth-brand-content">

            <div class="auth-logo">
                🌿 MiniGrove
            </div>

            <div class="auth-slogan">
                Nhỏ một khu rừng,<br>
                lớn một thế giới.
            </div>

            <div class="auth-description">
                Khám phá những hệ sinh thái mini,
                tự thiết kế Terrarium và đồng hành
                cùng khu vườn của riêng bạn.
            </div>

            <div class="auth-features">

                <span class="auth-feature">
                    Terrarium
                </span>

                <span class="auth-feature">
                    MiniGrove Studio
                </span>

                <span class="auth-feature">
                    My Grove
                </span>

                <span class="auth-feature">
                    GroveCare
                </span>

            </div>

        </div>

    </section>


    {{-- =========================
         MAIN
    ========================== --}}
    <main class="auth-main">

        <div class="auth-box">

            <div class="auth-heading">

                <h1>
                    Đặt lại mật khẩu
                </h1>

                <p>
                    Tạo mật khẩu mới cho tài khoản MiniGrove của bạn.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('password.store') }}"
            >

                @csrf


                {{-- =========================
                     RESET TOKEN
                ========================== --}}
                <input
                    type="hidden"
                    name="token"
                    value="{{ $request->route('token') }}"
                >


                {{-- =========================
                     EMAIL
                ========================== --}}
                <div class="auth-form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        id="email"
                        class="auth-input"
                        type="email"
                        value="{{ old('email', $request->email) }}"
                        readonly
                    >

                    <input
                        type="hidden"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                    >


                    @error('email')

                        <div class="auth-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =========================
                     NEW PASSWORD
                ========================== --}}
                <div class="auth-form-group">

                    <label for="password">
                        Mật khẩu mới
                    </label>


                    <div class="password-input-wrap">

                        <input
                            id="password"
                            class="auth-input"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Nhập mật khẩu mới"
                        >


                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle="password"
                            aria-label="Hiện mật khẩu"
                        >

                            {{-- MẮT THƯỜNG --}}
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


                            {{-- MẮT GẠCH CHÉO --}}
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


                    @error('password')

                        <div class="auth-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =========================
                     CONFIRM PASSWORD
                ========================== --}}
                <div class="auth-form-group">

                    <label for="password_confirmation">
                        Xác nhận mật khẩu mới
                    </label>


                    <div class="password-input-wrap">

                        <input
                            id="password_confirmation"
                            class="auth-input"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Nhập lại mật khẩu mới"
                        >


                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle="password_confirmation"
                            aria-label="Hiện mật khẩu"
                        >

                            {{-- MẮT THƯỜNG --}}
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


                            {{-- MẮT GẠCH CHÉO --}}
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


                    @error('password_confirmation')

                        <div class="auth-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =========================
                     SUBMIT
                ========================== --}}
                <button
                    type="submit"
                    class="auth-button"
                >
                    ĐẶT LẠI MẬT KHẨU
                </button>

            </form>


            <div class="auth-footer">

                Quay lại

                <a
                    href="{{ route('login') }}"
                    class="auth-link"
                >
                    Đăng nhập
                </a>

            </div>


            <div class="auth-back">

                <a
                    href="{{ route('home') }}"
                    class="auth-link"
                >
                    ← Trở về trang chủ
                </a>

            </div>

        </div>

    </main>

</div>


{{-- =========================
     PASSWORD TOGGLE
========================== --}}
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

</body>

</html>