@extends('layouts.customer')

@section('title', 'Địa chỉ của tôi - MiniGrove')

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
        padding: 11px 12px;
        box-sizing: border-box;
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

    .account-main {
        background: #fff;
        border: 1px solid #e3e9e5;
        border-radius: 14px;
        padding: 30px;
    }

    .account-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding-bottom: 20px;
        margin-bottom: 10px;
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

    .address-add-button {
        flex-shrink: 0;
        padding: 11px 18px;
        border: 0;
        border-radius: 9px;
        background: #174d37;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
    }

    .address-list {
        display: flex;
        flex-direction: column;
    }

    .address-item {
        display: flex;
        justify-content: space-between;
        gap: 25px;
        padding: 23px 4px;
        border-bottom: 1px solid #edf1ef;
    }

    .address-item:last-child {
        border-bottom: 0;
    }

    .address-info {
        min-width: 0;
    }

    .address-person {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 9px;
    }

    .address-person strong {
        color: #24392f;
        font-size: 15px;
    }

    .address-phone {
        padding-left: 10px;
        border-left: 1px solid #d8dfdb;
        color: #69766f;
        font-size: 14px;
    }

    .address-label,
    .address-default {
        display: inline-flex;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
    }

    .address-label {
        background: #f1f4f2;
        color: #607068;
    }

    .address-default {
        border: 1px solid #2a7353;
        color: #1d6245;
        background: #f1f8f4;
    }

    .address-text {
        max-width: 650px;
        color: #64726b;
        font-size: 14px;
        line-height: 1.7;
    }

    .address-actions {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        flex-shrink: 0;
    }

    .address-action-button {
        border: 0;
        background: transparent;
        padding: 5px 0;
        color: #176543;
        font: inherit;
        font-size: 13px;
        cursor: pointer;
    }

    .address-empty {
        padding: 65px 20px;
        text-align: center;
        color: #7a8781;
    }

    .address-empty-icon {
        margin-bottom: 12px;
        font-size: 42px;
    }

    .profile-address-create-panel {
    margin: 22px 0;
    padding: 24px;
    border: 1px solid #dfe8e3;
    border-radius: 12px;
    background: #f9fbfa;
}

.profile-address-form-heading {
    margin-bottom: 20px;
}

.profile-address-form-heading h2 {
    margin: 0 0 5px;
    color: #173f30;
    font-size: 19px;
}

.profile-address-form-heading p {
    margin: 0;
    color: #75827c;
    font-size: 13px;
}

.profile-address-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.profile-address-field {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.profile-address-field-full {
    grid-column: 1 / -1;
}

.profile-address-field label {
    color: #31483e;
    font-size: 13px;
    font-weight: 700;
}

.profile-address-field input,
.profile-address-field select {
    width: 100%;
    min-height: 44px;
    box-sizing: border-box;
    padding: 9px 12px;
    border: 1px solid #d7e0db;
    border-radius: 8px;
    background: #fff;
    color: #293c33;
    font: inherit;
    outline: none;
}

.profile-address-field input:focus,
.profile-address-field select:focus {
    border-color: #276c4e;
    box-shadow: 0 0 0 3px rgba(39, 108, 78, .08);
}

.profile-address-default {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 18px;
    color: #53645b;
    font-size: 13px;
}

.profile-address-form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.profile-address-save,
.profile-address-cancel {
    min-height: 42px;
    padding: 0 18px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
}

.profile-address-save {
    border: 0;
    background: #174d37;
    color: #fff;
}

.profile-address-cancel {
    border: 1px solid #d7e0db;
    background: #fff;
    color: #506159;
}

.profile-address-error {
    color: #b42318;
    font-size: 12px;
}

@media (max-width: 650px) {
    .profile-address-form-grid {
        grid-template-columns: 1fr;
    }

    .profile-address-field-full {
        grid-column: auto;
    }
}

    @media (max-width: 900px) {
        .account-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .account-heading,
        .address-item {
            flex-direction: column;
        }

        .address-add-button {
            width: 100%;
        }
    }
    .profile-address-edit-form {
    width: 100%;
    margin-top: 22px;
    padding: 22px;
    box-sizing: border-box;

    border: 1px solid #dfe8e3;
    border-radius: 12px;

    background: #f9fbfa;
}

.address-info {
    flex: 1;
}
</style>
@endpush


@section('content')

<section class="account-page">

    <div class="customer-container">

        <div class="account-layout">

            {{-- SIDEBAR --}}
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

                            <a href="{{ route('profile.edit') }}">
                                Hồ sơ
                            </a>

                            <a
                                href="{{ route('profile.addresses') }}"
                                class="active"
                            >
                                Địa chỉ
                            </a>

                            <a href="{{ route('profile.password') }}">
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


            {{-- MAIN --}}
            <main class="account-main">

                <div class="account-heading">

                    <div>

                        <h1>
                            Địa chỉ của tôi
                        </h1>

                        <p>
                            Quản lý địa chỉ nhận hàng của bạn.
                        </p>

                    </div>


                    <button
    type="button"
    class="address-add-button"
    id="profileAddAddressButton"
>
    + Thêm địa chỉ mới
</button>

                </div>

                <div
    id="profileAddAddressPanel"
    class="profile-address-create-panel"
    style="{{ $errors->any() ? 'display:block;' : 'display:none;' }}"
>

    <div class="profile-address-form-heading">

        <div>
            <h2>Thêm địa chỉ mới</h2>
            <p>
                Nhập thông tin địa chỉ nhận hàng.
            </p>
        </div>

    </div>


    <form
        method="POST"
        action="{{ route('checkout.address.store') }}"
    >

        @csrf

        <input
            type="hidden"
            name="context"
            value="profile"
        >


        <div class="profile-address-form-grid">

            {{-- TÊN --}}
            <div class="profile-address-field">

                <label for="receiver_name">
                    Tên người nhận
                </label>

                <input
                    id="receiver_name"
                    type="text"
                    name="receiver_name"
                    value="{{ old('receiver_name', $user->name) }}"
                    maxlength="100"
                    required
                >

                @error('receiver_name')
                    <div class="profile-address-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- SĐT --}}
            <div class="profile-address-field">

                <label for="receiver_phone">
                    Số điện thoại
                </label>

                <input
                    id="receiver_phone"
                    type="text"
                    name="receiver_phone"
                    value="{{ old('receiver_phone', $user->phone) }}"
                    maxlength="10"
                    inputmode="numeric"
                    required
                >

                @error('receiver_phone')
                    <div class="profile-address-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- TỈNH --}}
            <div class="profile-address-field">

                <label for="checkoutProvince">
                    Tỉnh / Thành phố
                </label>

                <select
                    id="checkoutProvince"
                    name="province"
                    data-old="{{ old('province') }}"
                    required
                >
                    <option value="">
                        -- Chọn Tỉnh / Thành phố --
                    </option>
                </select>

                @error('province')
                    <div class="profile-address-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- QUẬN --}}
            <div class="profile-address-field">

                <label for="checkoutDistrict">
                    Quận / Huyện
                </label>

                <select
                    id="checkoutDistrict"
                    name="district"
                    data-old="{{ old('district') }}"
                    required
                    disabled
                >
                    <option value="">
                        -- Chọn Quận / Huyện --
                    </option>
                </select>

                @error('district')
                    <div class="profile-address-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- PHƯỜNG --}}
            <div class="profile-address-field">

                <label for="checkoutWard">
                    Phường / Xã
                </label>

                <select
                    id="checkoutWard"
                    name="ward"
                    data-old="{{ old('ward') }}"
                    required
                    disabled
                >
                    <option value="">
                        -- Chọn Phường / Xã --
                    </option>
                </select>

                @error('ward')
                    <div class="profile-address-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- NHÃN --}}
            <div class="profile-address-field">

                <label for="addressLabel">
                    Nhãn địa chỉ
                </label>

                <input
                    id="addressLabel"
                    type="text"
                    name="label"
                    value="{{ old('label') }}"
                    maxlength="50"
                    placeholder="Ví dụ: Nhà riêng, Công ty"
                >

            </div>


            {{-- GHN --}}
            <input
                type="hidden"
                name="ghn_district_id"
                id="ghnDistrictId"
                value="{{ old('ghn_district_id') }}"
            >

            <input
                type="hidden"
                name="ghn_ward_code"
                id="ghnWardCode"
                value="{{ old('ghn_ward_code') }}"
            >


            {{-- CHI TIẾT --}}
            <div
                class="
                    profile-address-field
                    profile-address-field-full
                "
            >

                <label for="addressDetail">
                    Số nhà / Tên đường
                </label>

                <input
                    id="addressDetail"
                    type="text"
                    name="address_detail"
                    value="{{ old('address_detail') }}"
                    maxlength="255"
                    placeholder="Ví dụ: Ngõ 85 Đức Diễn"
                    required
                >

                @error('address_detail')
                    <div class="profile-address-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>


        <label class="profile-address-default">

            <input
                type="checkbox"
                name="is_default"
                value="1"
                @checked(old('is_default'))
            >

            Đặt làm địa chỉ mặc định

        </label>


        <div class="profile-address-form-actions">

            <button
                type="submit"
                class="profile-address-save"
            >
                Lưu địa chỉ
            </button>

            <button
                type="button"
                class="profile-address-cancel"
                id="profileCancelAddressButton"
            >
                Hủy
            </button>

        </div>

    </form>

</div>

                @if ($addresses->isNotEmpty())

                    <div class="address-list">

                        @foreach ($addresses as $address)

                            <div class="address-item">

                                <div class="address-info">

                                    <div class="address-person">

                                        <strong>
                                            {{ $address->receiver_name }}
                                        </strong>

                                        <span class="address-phone">
                                            {{ $address->receiver_phone }}
                                        </span>


                                        @if ($address->label)

                                            <span class="address-label">
                                                {{ $address->label }}
                                            </span>

                                        @endif


                                        @if ($address->is_default)

                                            <span class="address-default">
                                                Mặc định
                                            </span>

                                        @endif

                                    </div>


                                    <div class="address-text">

                                        {{ $address->address_detail }}

                                        <br>

                                        {{ $address->ward }},
                                        {{ $address->district }},
                                        {{ $address->province }}

                                    </div>
                                    <div
    class="checkout-address-edit-form profile-address-edit-form"
    id="editAddress{{ $address->id }}"
    style="display: none;"
>

    <div class="profile-address-form-heading">

        <h2>
            Cập nhật địa chỉ
        </h2>

        <p>
            Chỉnh sửa thông tin địa chỉ nhận hàng.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route(
            'checkout.address.update',
            $address->id
        ) }}"
    >

        @csrf
        @method('PATCH')

        <input
            type="hidden"
            name="context"
            value="profile"
        >


        <div class="profile-address-form-grid">

            {{-- TÊN NGƯỜI NHẬN --}}
            <div class="profile-address-field">

                <label>
                    Tên người nhận
                </label>

                <input
                    type="text"
                    name="receiver_name"
                    value="{{ $address->receiver_name }}"
                    maxlength="100"
                    required
                >

            </div>


            {{-- SĐT --}}
            <div class="profile-address-field">

                <label>
                    Số điện thoại
                </label>

                <input
                    type="text"
                    name="receiver_phone"
                    value="{{ $address->receiver_phone }}"
                    maxlength="10"
                    inputmode="numeric"
                    required
                >

            </div>


            {{-- TỈNH --}}
            <div class="profile-address-field">

                <label>
                    Tỉnh / Thành phố
                </label>

                <select
                    class="checkout-edit-province"
                    name="province"
                    data-address-id="{{ $address->id }}"
                    data-current-province="{{ $address->province }}"
                    required
                >

                    <option value="">
                        Đang tải Tỉnh / Thành phố...
                    </option>

                </select>

            </div>


            {{-- QUẬN --}}
            <div class="profile-address-field">

                <label>
                    Quận / Huyện
                </label>

                <select
                    class="checkout-edit-district"
                    name="district"
                    data-address-id="{{ $address->id }}"
                    data-current-district="{{ $address->district }}"
                    required
                    disabled
                >

                    <option value="">
                        -- Chọn Quận / Huyện --
                    </option>

                </select>

            </div>


            {{-- PHƯỜNG --}}
            <div class="profile-address-field">

                <label>
                    Phường / Xã
                </label>

                <select
                    class="checkout-edit-ward"
                    name="ward"
                    data-address-id="{{ $address->id }}"
                    data-current-ward="{{ $address->ward }}"
                    required
                    disabled
                >

                    <option value="">
                        -- Chọn Phường / Xã --
                    </option>

                </select>

            </div>


            {{-- NHÃN --}}
            <div class="profile-address-field">

                <label>
                    Nhãn địa chỉ
                </label>

                <input
                    type="text"
                    name="label"
                    value="{{ $address->label }}"
                    maxlength="50"
                    placeholder="Ví dụ: Nhà riêng, Công ty"
                >

            </div>


            {{-- GHN DISTRICT --}}
            <input
                type="hidden"
                class="checkout-edit-ghn-district"
                name="ghn_district_id"
                data-address-id="{{ $address->id }}"
                value="{{ $address->ghn_district_id }}"
            >


            {{-- GHN WARD --}}
            <input
                type="hidden"
                class="checkout-edit-ghn-ward"
                name="ghn_ward_code"
                data-address-id="{{ $address->id }}"
                value="{{ $address->ghn_ward_code }}"
            >


            {{-- ĐỊA CHỈ CHI TIẾT --}}
            <div
                class="
                    profile-address-field
                    profile-address-field-full
                "
            >

                <label>
                    Số nhà / Tên đường
                </label>

                <input
                    type="text"
                    name="address_detail"
                    value="{{ $address->address_detail }}"
                    maxlength="255"
                    required
                >

            </div>

        </div>


        <label class="profile-address-default">

            <input
                type="checkbox"
                name="is_default"
                value="1"
                @checked($address->is_default)
            >

            Đặt làm địa chỉ mặc định

        </label>


        <div class="profile-address-form-actions">

            <button
                type="submit"
                class="profile-address-save"
            >
                Lưu thay đổi
            </button>


            <button
                type="button"
                class="
                    profile-address-cancel
                    checkout-address-cancel-edit
                "
                data-address-id="{{ $address->id }}"
            >
                Hủy
            </button>

        </div>

    </form>

</div>

                                </div>


                                <div class="address-actions">

                                    <button
    type="button"
    class="
        address-action-button
        checkout-address-edit-button
    "
    data-address-edit="{{ $address->id }}"
>
    Cập nhật
</button>

                                    <form
    method="POST"
    action="{{ route(
        'checkout.address.destroy',
        $address->id
    ) }}"
    onsubmit="
        return confirm(
            'Bạn có chắc muốn xóa địa chỉ này không?'
        );
    "
>
    @csrf
    @method('DELETE')

    <input
        type="hidden"
        name="context"
        value="profile"
    >

    <button
        type="submit"
        class="address-action-button"
    >
        Xóa
    </button>
</form>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="address-empty">

                        <div class="address-empty-icon">
                            📍
                        </div>

                        <strong>
                            Bạn chưa có địa chỉ nào
                        </strong>

                        <p>
                            Thêm địa chỉ để quá trình đặt hàng
                            thuận tiện hơn.
                        </p>

                    </div>

                @endif

            </main>

        </div>

    </div>

</section>

@endsection
@push('scripts')

    <script
        src="{{ asset('js/checkout-address.js') }}"
    ></script>

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const openButton =
                    document.getElementById(
                        'profileAddAddressButton'
                    );

                const cancelButton =
                    document.getElementById(
                        'profileCancelAddressButton'
                    );

                const panel =
                    document.getElementById(
                        'profileAddAddressPanel'
                    );

                if (openButton && panel) {

                    openButton.addEventListener(
                        'click',
                        function () {
                            panel.style.display = 'block';

                            panel.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    );
                }

                if (cancelButton && panel) {

                    cancelButton.addEventListener(
                        'click',
                        function () {
                            panel.style.display = 'none';
                        }
                    );
                }
            }
        );
    </script>

@endpush