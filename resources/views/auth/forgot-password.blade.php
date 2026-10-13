<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Quên mật khẩu - MiniGrove</title>

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
                    Quên mật khẩu?
                </h1>

                <p>
                    Nhập email của bạn.
                    MiniGrove sẽ gửi liên kết để
                    bạn thiết lập mật khẩu mới.
                </p>

            </div>


            {{-- STATUS --}}
            @if (session('status'))

                <div
                    style="
                        margin-bottom: 18px;
                        padding: 12px 14px;
                        border: 1px solid #b9ddc8;
                        border-radius: 8px;
                        background: #edf8f1;
                        color: #1d6545;
                        font-size: 14px;
                        line-height: 1.5;
                    "
                >
                    {{ session('status') }}
                </div>

            @endif


            <form
                method="POST"
                action="{{ route('password.email') }}"
            >

                @csrf


                <div class="auth-form-group">

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
                        autofocus
                        autocomplete="email"
                        placeholder="example@gmail.com"
                    >


                    @error('email')

                        <div class="auth-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <button
                    type="submit"
                    class="auth-button"
                >
                    GỬI LIÊN KẾT ĐẶT LẠI MẬT KHẨU
                </button>

            </form>


            <div class="auth-footer">

                Bạn đã nhớ mật khẩu?

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

</body>

</html>