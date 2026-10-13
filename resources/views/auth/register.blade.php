<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Đăng ký - MiniGrove</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/auth.css') }}"
    >
</head>

<body>

<div class="auth-page auth-page-register">

    <section class="auth-brand">

        <div class="auth-brand-content">

            <a
                href="{{ route('home') }}"
                class="auth-logo auth-logo-link"
            >
                🌿 MiniGrove
            </a>

            <div class="auth-slogan">
                Nhỏ một khu rừng,<br>
                lớn một thế giới.
            </div>

            <div class="auth-description">
                Tạo tài khoản để lưu những sản phẩm bạn yêu thích,
                tự thiết kế Terrarium và theo dõi đơn hàng của riêng bạn.
            </div>

            <div class="auth-features">
                <span class="auth-feature">Terrarium</span>
                <span class="auth-feature">Thiết kế của tôi</span>
                <span class="auth-feature">Yêu thích</span>
                <span class="auth-feature">Đơn hàng</span>
            </div>

        </div>

    </section>


    <main class="auth-main">

        <div class="auth-box auth-box-register">

            <div class="auth-heading">

                <span class="auth-eyebrow">
                    TÀI KHOẢN MINIGROVE
                </span>

                <h1>
                    Tạo tài khoản
                </h1>

                <p>
                    Đăng ký để bắt đầu hành trình cùng MiniGrove.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('register') }}"
                class="auth-register-form"
            >

                @csrf

                <div class="auth-form-grid">

                    <div class="auth-form-group">

                        <label for="name">
                            Họ và tên
                        </label>

                        <input
                            id="name"
                            class="auth-input"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            maxlength="50"
                            placeholder="Ví dụ: Nguyễn Văn An"
                        >

                        @error('name')
                            <div class="auth-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="auth-form-group">

                        <label for="phone">
                            Số điện thoại
                        </label>

                        <input
                            id="phone"
                            class="auth-input"
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            autocomplete="tel"
                            inputmode="numeric"
                            maxlength="10"
                            pattern="0[0-9]{9}"
                            placeholder="0901234567"
                        >

                        @error('phone')
                            <div class="auth-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="auth-form-group auth-form-group-full">

                        <label for="email">
                            Email
                        </label>

                        <input
                            id="email"
                            class="auth-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            maxlength="255"
                            placeholder="example@gmail.com"
                        >

                        @error('email')
                            <div class="auth-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="auth-form-group">

                        <label for="password">
                            Mật khẩu
                        </label>

                        <div class="password-input-wrap">

                            <input
                                id="password"
                                class="auth-input"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="64"
                                placeholder="Nhập mật khẩu"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-password-toggle="password"
                                aria-label="Hiện mật khẩu"
                            >
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
                                        d="M3.98 8.223A10.477 10.477 0 0 0 2.458 12c1.274 4.057 5.065 6.75 9.542 6.75 1.03 0 2.024-.143 2.967-.41M6.228 6.228A10.45 10.45 0 0 1 12 5.25c4.477 0 8.268 2.693 9.542 6.75a10.495 10.495 0 0 1-4.293 5.568M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.372 7.372L21 21m-3.75-3.75-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.243 4.243L9.357 9.357"
                                    />
                                </svg>
                            </button>

                        </div>

                        <div class="auth-help">
                            8–64 ký tự, gồm chữ hoa, chữ thường,
                            số và ký tự đặc biệt.
                        </div>

                        @error('password')
                            <div class="auth-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="auth-form-group">

                        <label for="password_confirmation">
                            Xác nhận mật khẩu
                        </label>

                        <div class="password-input-wrap">

                            <input
                                id="password_confirmation"
                                class="auth-input"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="64"
                                placeholder="Nhập lại mật khẩu"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-password-toggle="password_confirmation"
                                aria-label="Hiện mật khẩu"
                            >
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
                                        d="M3.98 8.223A10.477 10.477 0 0 0 2.458 12c1.274 4.057 5.065 6.75 9.542 6.75 1.03 0 2.024-.143 2.967-.41M6.228 6.228A10.45 10.45 0 0 1 12 5.25c4.477 0 8.268 2.693 9.542 6.75a10.495 10.495 0 0 1-4.293 5.568M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.372 7.372L21 21m-3.75-3.75-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.243 4.243L9.357 9.357"
                                    />
                                </svg>
                            </button>

                        </div>

                    </div>

                </div>


                <button
                    type="submit"
                    class="auth-button auth-register-button"
                >
                    ĐĂNG KÝ
                </button>

            </form>


            <div class="auth-footer">
                Bạn đã có tài khoản?

                <a
                    href="{{ route('login') }}"
                    class="auth-link"
                >
                    Đăng nhập ngay
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


<script>
    document.addEventListener(
        'click',
        function (event) {
            const button = event.target.closest(
                '[data-password-toggle]'
            );

            if (!button) {
                return;
            }

            const input = document.getElementById(
                button.dataset.passwordToggle
            );

            if (!input) {
                return;
            }

            const eyeOpen = button.querySelector(
                '.eye-open'
            );

            const eyeClosed = button.querySelector(
                '.eye-closed'
            );

            const isHidden =
                input.type === 'password';

            input.type =
                isHidden ? 'text' : 'password';

            eyeOpen.style.display =
                isHidden ? 'none' : 'block';

            eyeClosed.style.display =
                isHidden ? 'block' : 'none';

            button.setAttribute(
                'aria-label',
                isHidden
                    ? 'Ẩn mật khẩu'
                    : 'Hiện mật khẩu'
            );
        }
    );
</script>

</body>

</html>