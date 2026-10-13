@extends('layouts.customer')

@section('title', 'Tài khoản của tôi - MiniGrove')

@php

    $avatarUrl = null;

    if ($user->avatar) {

        if (
            str_starts_with($user->avatar, 'http://')
            || str_starts_with($user->avatar, 'https://')
        ) {

            $avatarUrl = $user->avatar;

        } else {

            $avatarUrl = asset(
                'storage/' .
                ltrim(
                    $user->avatar,
                    '/'
                )
            );
        }
    }

@endphp


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

        border-radius: 50%;
        overflow: hidden;

        display: flex;
        align-items: center;
        justify-content: center;

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
        line-height: 1.4;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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

    .account-menu-group {
        margin-bottom: 5px;
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
        font-weight: 700;
        color: #174d37;
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
        padding: 30px;

        background: #fff;

        border: 1px solid #e3e9e5;
        border-radius: 14px;
    }

    .account-heading {
        padding-bottom: 20px;
        margin-bottom: 26px;

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
       PROFILE
    ========================================================= */

    .profile-content-grid {
        display: grid;

        grid-template-columns:
            minmax(0, 1fr)
            230px;

        gap: 40px;

        align-items: start;
    }

    .profile-form-group {
        margin-bottom: 20px;
    }

    .profile-form-group label {
        display: block;

        margin-bottom: 7px;

        color: #32483e;

        font-size: 14px;
        font-weight: 600;
    }

    .profile-input {
        width: 100%;

        min-height: 46px;

        box-sizing: border-box;

        padding: 10px 13px;

        border: 1px solid #d8e1dc;
        border-radius: 9px;

        outline: none;

        font: inherit;

        color: #24372f;

        background: #fff;
    }

    .profile-input:focus {
        border-color: #246b4d;

        box-shadow:
            0 0 0 3px
            rgba(36, 107, 77, .08);
    }

    .profile-input[readonly] {
        background: #f6f8f7;

        color: #68736e;

        cursor: not-allowed;
    }

    .profile-help {
        margin-top: 6px;

        color: #89938e;

        font-size: 12px;
        line-height: 1.5;
    }

    .profile-error {
        margin-top: 6px;

        color: #b42318;

        font-size: 12px;
    }

    .profile-save-row {
        display: flex;
        align-items: center;

        gap: 14px;

        margin-top: 26px;
    }

    .profile-save-button {
        min-width: 125px;
        min-height: 44px;

        padding: 0 18px;

        border: 0;
        border-radius: 9px;

        background: #174d37;

        color: #fff;

        font-weight: 700;

        cursor: pointer;
    }

    .profile-save-button:hover {
        background: #123d2c;
    }

    .profile-saved {
        color: #24734f;

        font-size: 13px;
        font-weight: 600;
    }


    /* =========================================================
       AVATAR
    ========================================================= */

    .profile-avatar-panel {
        padding-left: 35px;

        border-left: 1px solid #edf1ef;

        text-align: center;
    }

    .profile-avatar-large {
        width: 120px;
        height: 120px;

        margin: 8px auto 15px;

        border-radius: 50%;

        overflow: hidden;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #e8f2ec;

        color: #174d37;

        font-size: 42px;
        font-weight: 700;
    }

    .profile-avatar-large img {
        width: 100%;
        height: 100%;

        object-fit: cover;
    }

    .profile-avatar-title {
        display: block;

        margin-bottom: 13px;

        color: #173f30;

        font-size: 14px;
    }

    .profile-avatar-input {
        display: none;
    }

    .profile-avatar-button {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        min-width: 110px;
        min-height: 40px;

        box-sizing: border-box;

        padding: 8px 14px;

        border: 1px solid #d7e1db;
        border-radius: 8px;

        background: #fff;

        color: #174d37;

        font-size: 13px;
        font-weight: 700;

        cursor: pointer;

        transition: 0.2s ease;
    }

    .profile-avatar-button:hover {
        border-color: #174d37;

        background: #f4f9f6;
    }

    .profile-avatar-note {
        margin-top: 12px;

        color: #89938e;

        font-size: 12px;
        line-height: 1.7;
    }

    .profile-avatar-file-name {
        margin-top: 8px;

        color: #5f6d66;

        font-size: 12px;

        word-break: break-word;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 900px) {

        .account-layout {
            grid-template-columns: 1fr;
        }

        .profile-content-grid {
            grid-template-columns: 1fr;
        }

        .profile-avatar-panel {
            padding: 25px 0 0;

            border-left: 0;
            border-top: 1px solid #edf1ef;
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

                        @if ($avatarUrl)

                            <img
                                src="{{ $avatarUrl }}"
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

                    <div class="account-menu-group">

                        <div class="account-menu-title">
                            Tài khoản của tôi
                        </div>


                        <div class="account-menu-sub">

                            <a
                                href="{{ route('profile.edit') }}"
                                class="active"
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
                        Hồ sơ của tôi
                    </h1>

                    <p>
                        Quản lý thông tin hồ sơ để bảo mật tài khoản.
                    </p>

                </div>


                <form
                    method="POST"
                    action="{{ route('profile.update') }}"
                    enctype="multipart/form-data"
                >

                    @csrf
                    @method('PATCH')


                    <div class="profile-content-grid">


                        {{-- =====================================
                             INFORMATION
                        ====================================== --}}
                        <div>


                            {{-- NAME --}}
                            <div class="profile-form-group">

                                <label for="name">
                                    Họ và tên
                                </label>

                                <input
                                    id="name"
                                    class="profile-input"
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                    autocomplete="name"
                                >


                                @error('name')

                                    <div class="profile-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- EMAIL --}}
                            <div class="profile-form-group">

                                <label for="email">
                                    Email
                                </label>

                                <input
                                    id="email"
                                    class="profile-input"
                                    type="email"
                                    value="{{ $user->email }}"
                                    readonly
                                >

                                <div class="profile-help">
                                    Email đăng nhập không thể thay đổi tại đây.
                                </div>

                            </div>


                            {{-- PHONE --}}
                            <div class="profile-form-group">

                                <label for="phone">
                                    Số điện thoại
                                </label>

                                <input
                                    id="phone"
                                    class="profile-input"
                                    type="tel"
                                    name="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    maxlength="10"
                                    inputmode="numeric"
                                    autocomplete="tel"
                                    placeholder="Ví dụ: 0357891347"
                                >


                                @error('phone')

                                    <div class="profile-error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- SAVE --}}
                            <div class="profile-save-row">

                                <button
                                    type="submit"
                                    class="profile-save-button"
                                >
                                    Lưu thay đổi
                                </button>


                                @if (
                                    session('status')
                                    === 'profile-updated'
                                )

                                    <span class="profile-saved">
                                        ✓ Đã lưu thay đổi
                                    </span>

                                @endif

                            </div>

                        </div>


                        {{-- =====================================
                             AVATAR
                        ====================================== --}}
                        <div class="profile-avatar-panel">


                            <div class="profile-avatar-large">

                                <img
                                    id="avatarPreviewImage"
                                    src="{{ $avatarUrl ?? '' }}"
                                    alt="{{ $user->name }}"
                                    style="{{ $avatarUrl
                                        ? ''
                                        : 'display:none;' }}"
                                >


                                <span
                                    id="avatarPreviewInitial"
                                    style="{{ $avatarUrl
                                        ? 'display:none;'
                                        : '' }}"
                                >
                                    {{
                                        strtoupper(
                                            mb_substr(
                                                $user->name,
                                                0,
                                                1
                                            )
                                        )
                                    }}
                                </span>

                            </div>


                            <strong class="profile-avatar-title">
                                Ảnh đại diện
                            </strong>


                            <input
                                id="avatar"
                                class="profile-avatar-input"
                                type="file"
                                name="avatar"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            >


                            <label
                                for="avatar"
                                class="profile-avatar-button"
                            >
                                Chọn ảnh
                            </label>


                            <div
                                id="avatarFileName"
                                class="profile-avatar-file-name"
                            ></div>


                            @error('avatar')

                                <div class="profile-error">
                                    {{ $message }}
                                </div>

                            @enderror


                            <div class="profile-avatar-note">

                                Dung lượng tối đa 1 MB.

                                <br>

                                Định dạng:
                                JPG, JPEG, PNG hoặc WEBP.

                            </div>

                        </div>

                    </div>

                </form>

            </main>

        </div>

    </div>

</section>


<script>

    const avatarInput =
        document.getElementById('avatar');

    const avatarPreviewImage =
        document.getElementById(
            'avatarPreviewImage'
        );

    const avatarPreviewInitial =
        document.getElementById(
            'avatarPreviewInitial'
        );

    const avatarFileName =
        document.getElementById(
            'avatarFileName'
        );


    if (avatarInput) {

        avatarInput.addEventListener(
            'change',
            function () {

                const file =
                    this.files
                        ? this.files[0]
                        : null;


                if (!file) {

                    avatarFileName.textContent =
                        '';

                    return;
                }


                avatarFileName.textContent =
                    file.name;


                const reader =
                    new FileReader();


                reader.onload = function (event) {

                    avatarPreviewImage.src =
                        event.target.result;

                    avatarPreviewImage.style.display =
                        'block';

                    avatarPreviewInitial.style.display =
                        'none';
                };


                reader.readAsDataURL(file);
            }
        );
    }

</script>

@endsection