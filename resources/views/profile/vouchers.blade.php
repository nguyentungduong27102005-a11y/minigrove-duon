@extends('layouts.customer')

@section('title', 'Kho Voucher - MiniGrove')


@php

    /*
    |--------------------------------------------------------------------------
    | USER + AVATAR
    |--------------------------------------------------------------------------
    */

    $accountUser = auth()->user();

    $accountAvatarUrl = null;

    if ($accountUser->avatar) {

        if (
            str_starts_with(
                $accountUser->avatar,
                'http://'
            )
            ||
            str_starts_with(
                $accountUser->avatar,
                'https://'
            )
        ) {

            $accountAvatarUrl =
                $accountUser->avatar;

        } else {

            $accountAvatarUrl =
                asset(
                    'storage/' .
                    ltrim(
                        $accountUser->avatar,
                        '/'
                    )
                );
        }
    }

@endphp


@push('styles')

<style>

/* =========================================================
   PAGE
========================================================= */

.voucher-page {
    min-height: 720px;

    padding: 42px 0 70px;

    background: #f7f9f8;
}

.voucher-layout {
    display: grid;

    grid-template-columns:
        260px
        minmax(0, 1fr);

    gap: 28px;

    align-items: start;
}


/* =========================================================
   SIDEBAR
========================================================= */

.voucher-sidebar {
    overflow: hidden;

    background: #fff;

    border: 1px solid #e3e9e5;
    border-radius: 14px;
}

.voucher-user {
    display: flex;
    align-items: center;

    gap: 12px;

    padding: 22px 18px;

    border-bottom:
        1px solid #edf1ef;
}

.voucher-user-avatar {
    width: 54px;
    height: 54px;

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

.voucher-user-avatar img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.voucher-user-info {
    min-width: 0;
}

.voucher-user-info strong {
    display: block;

    overflow: hidden;

    color: #173f30;

    font-size: 15px;

    white-space: nowrap;

    text-overflow: ellipsis;
}

.voucher-user-info span {
    display: block;

    margin-top: 4px;

    color: #7b8882;

    font-size: 13px;
}

.voucher-menu {
    padding: 10px;
}

.voucher-menu-title {
    padding: 11px 12px;

    color: #174d37;

    font-size: 14px;
    font-weight: 700;
}

.voucher-menu-sub {
    padding-left: 36px;

    margin-bottom: 5px;
}

.voucher-menu-sub a,
.voucher-menu-link {
    display: block;

    width: 100%;

    box-sizing: border-box;

    padding: 9px 10px;

    border-radius: 8px;

    color: #5d6c65;

    font-size: 14px;

    text-decoration: none;
}

.voucher-menu-link {
    padding: 11px 12px;

    color: #31483e;
}

.voucher-menu-sub a:hover,
.voucher-menu-link:hover {
    background: #eef6f1;

    color: #174d37;
}

.voucher-menu-link.active {
    background: #e8f3ed;

    color: #174d37;

    font-weight: 700;
}


/* =========================================================
   MAIN
========================================================= */

.voucher-main {
    min-width: 0;

    padding: 30px;

    background: #fff;

    border: 1px solid #e3e9e5;
    border-radius: 14px;
}

.voucher-heading {
    margin-bottom: 24px;

    padding-bottom: 20px;

    border-bottom:
        1px solid #edf1ef;
}

.voucher-heading h1 {
    margin: 0 0 6px;

    color: #173f30;

    font-size: 25px;
}

.voucher-heading p {
    margin: 0;

    color: #75827c;

    font-size: 14px;
}


/* =========================================================
   TABS
========================================================= */

.voucher-tabs {
    display: grid;

    grid-template-columns:
        repeat(
            3,
            1fr
        );

    margin-bottom: 24px;

    border: 1px solid #e1e8e4;
    border-radius: 10px;

    background: #fff;

    overflow: hidden;
}

.voucher-tab {
    position: relative;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    min-height: 54px;

    padding: 0 14px;

    color: #627169;

    text-decoration: none;

    font-size: 14px;
    font-weight: 600;
}

.voucher-tab:hover {
    color: #174d37;
}

.voucher-tab.active {
    color: #174d37;

    background: #f7fbf8;

    font-weight: 700;
}

.voucher-tab.active::after {
    content: "";

    position: absolute;

    left: 22px;
    right: 22px;
    bottom: 0;

    height: 3px;

    background: #174d37;

    border-radius:
        3px 3px 0 0;
}

.voucher-tab-count {
    min-width: 20px;
    height: 20px;

    box-sizing: border-box;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    padding: 0 6px;

    border-radius: 999px;

    background: #edf4f0;

    color: #174d37;

    font-size: 11px;
}


/* =========================================================
   VOUCHER LIST
========================================================= */

.voucher-list {
    display: grid;

    grid-template-columns:
        repeat(
            2,
            minmax(0, 1fr)
        );

    gap: 18px;
}


/* =========================================================
   VOUCHER CARD
========================================================= */

.voucher-card {
    position: relative;

    display: grid;

    grid-template-columns:
        125px
        minmax(0, 1fr);

    min-height: 180px;

    overflow: hidden;

    border: 1px solid #dfe8e3;
    border-radius: 12px;

    background: #fff;

    box-shadow:
        0 4px 18px
        rgba(25, 70, 50, .05);
}


/* =========================================================
   LEFT
========================================================= */

.voucher-card-left {
    position: relative;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    gap: 9px;

    padding: 18px 12px;

    background:
        linear-gradient(
            145deg,
            #174d37,
            #27684d
        );

    color: #fff;

    text-align: center;
}

.voucher-card-left::before,
.voucher-card-left::after {
    content: "";

    position: absolute;

    right: -9px;

    width: 18px;
    height: 18px;

    border-radius: 50%;

    background: #fff;
}

.voucher-card-left::before {
    top: -9px;
}

.voucher-card-left::after {
    bottom: -9px;
}

.voucher-brand-icon {
    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background:
        rgba(
            255,
            255,
            255,
            .13
        );

    font-size: 25px;
}

.voucher-brand-name {
    font-size: 13px;
    font-weight: 700;
}

.voucher-brand-subtitle {
    opacity: .8;

    font-size: 10px;
}


/* =========================================================
   RIGHT
========================================================= */

.voucher-card-body {
    display: flex;
    flex-direction: column;

    padding: 18px;
}

.voucher-discount {
    margin-bottom: 6px;

    color: #174d37;

    font-size: 20px;
    font-weight: 800;
}

.voucher-condition {
    min-height: 38px;

    margin-bottom: 9px;

    color: #596961;

    font-size: 12px;
    line-height: 1.55;
}

.voucher-limit {
    margin-bottom: 5px;

    color: #849089;

    font-size: 11px;
}

.voucher-expiry {
    margin-top: auto;

    color: #7d8a83;

    font-size: 11px;
}

.voucher-expiry strong {
    color: #52635a;
}


/* =========================================================
   CODE
========================================================= */

.voucher-code-row {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 10px;

    margin-top: 12px;

    padding-top: 12px;

    border-top:
        1px dashed #dbe5df;
}

.voucher-code {
    color: #174d37;

    font-size: 13px;
    font-weight: 800;

    letter-spacing: .4px;
}

.voucher-copy {
    padding: 6px 9px;

    border: 1px solid #bfd3c7;
    border-radius: 6px;

    background: #fff;

    color: #174d37;

    font-size: 11px;
    font-weight: 700;

    cursor: pointer;
}

.voucher-copy:hover {
    background: #eff7f2;
}

.voucher-save-form {
    margin: 0;
}

.voucher-copy.voucher-saved {
    border-color: #87b79b;
    background: #e8f6ee;
    color: #17623f;
}


/* =========================================================
   BADGE
========================================================= */

.voucher-badge {
    position: absolute;

    top: 10px;
    right: 10px;

    padding: 5px 8px;

    border-radius: 999px;

    font-size: 10px;
    font-weight: 700;
}

.voucher-badge.available {
    background: #e8f6ee;

    color: #17623f;
}

.voucher-badge.used {
    background: #eef2f5;

    color: #5c6870;
}

.voucher-badge.expired {
    background: #fff0f0;

    color: #b83d3d;
}


/* =========================================================
   DISABLED CARD
========================================================= */

.voucher-card.disabled {
    opacity: .7;
}

.voucher-card.disabled
.voucher-card-left {
    background:
        linear-gradient(
            145deg,
            #718078,
            #8b9690
        );
}


/* =========================================================
   EMPTY
========================================================= */

.voucher-empty {
    grid-column:
        1 / -1;

    padding: 65px 25px;

    border: 1px solid #e3e9e5;
    border-radius: 12px;

    background: #fafcfb;

    text-align: center;
}

.voucher-empty-icon {
    margin-bottom: 14px;

    font-size: 45px;
}

.voucher-empty h2 {
    margin:
        0
        0
        8px;

    color: #173f30;

    font-size: 20px;
}

.voucher-empty p {
    margin:
        0
        0
        20px;

    color: #7d8983;

    font-size: 13px;
}

.voucher-shop-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 42px;

    padding: 0 17px;

    border-radius: 8px;

    background: #174d37;

    color: #fff;

    font-size: 13px;
    font-weight: 700;

    text-decoration: none;
}


/* =========================================================
   COPY MESSAGE
========================================================= */

.voucher-copy-message {
    position: fixed;

    left: 50%;
    bottom: 35px;

    z-index: 9999;

    transform:
        translateX(-50%);

    display: none;

    padding: 11px 17px;

    border-radius: 8px;

    background: #173f30;

    color: #fff;

    font-size: 13px;

    box-shadow:
        0 8px 30px
        rgba(0, 0, 0, .15);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1050px) {

    .voucher-list {
        grid-template-columns: 1fr;
    }
}


@media (max-width: 800px) {

    .voucher-layout {
        grid-template-columns: 1fr;
    }

    .voucher-main {
        padding: 20px;
    }
}


@media (max-width: 520px) {

    .voucher-card {
        grid-template-columns:
            100px
            minmax(0, 1fr);
    }

    .voucher-tabs {
        display: flex;

        overflow-x: auto;
    }

    .voucher-tab {
        min-width: 130px;
    }
}

</style>

@endpush


@section('content')

<section class="voucher-page">

    <div class="customer-container">

        <div class="voucher-layout">


            {{-- =================================================
                 SIDEBAR
            ================================================== --}}

            <aside class="voucher-sidebar">


                <div class="voucher-user">


                    <div class="voucher-user-avatar">

                        @if ($accountAvatarUrl)

                            <img
                                src="{{ $accountAvatarUrl }}"
                                alt="{{ $accountUser->name }}"
                            >

                        @else

                            {{
                                strtoupper(
                                    mb_substr(
                                        $accountUser->name,
                                        0,
                                        1
                                    )
                                )
                            }}

                        @endif

                    </div>


                    <div class="voucher-user-info">

                        <strong>
                            {{ $accountUser->name }}
                        </strong>

                        <span>
                            Khách hàng
                        </span>

                    </div>

                </div>


                <nav class="voucher-menu">


                    <div class="voucher-menu-title">
                        Tài khoản của tôi
                    </div>


                    <div class="voucher-menu-sub">

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
                        >
                            Đổi mật khẩu
                        </a>

                    </div>


                    <a
                        href="{{ route('orders.index') }}"
                        class="voucher-menu-link"
                    >
                        Đơn mua
                    </a>


                    <a
                        href="{{ route('profile.vouchers') }}"
                        class="
                            voucher-menu-link
                            active
                        "
                    >
                        Kho Voucher
                    </a>

                </nav>

            </aside>


            {{-- =================================================
                 MAIN
            ================================================== --}}

            <main class="voucher-main">


                <div class="voucher-heading">

                    <h1>
                        Kho Voucher
                    </h1>

                    <p>
                        Xem và khám phá các mã ưu đãi dành cho bạn.
                    </p>

                    <div style="margin-top: 14px; padding: 12px 16px; background: #eaf5ee; border: 1px solid #b8dfc7; border-radius: 10px; font-size: 13.5px; color: #164e33; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 18px;">💡</span>
                        <span><strong>Mẹo mua sắm:</strong> Bạn không cần phải lưu mã trước! Hãy <strong>chọn voucher trực tiếp khi thanh toán</strong> để nhận ưu đãi nhanh chóng.</span>
                    </div>

                </div>


                {{-- =================================================
                     TABS
                ================================================== --}}

                <div class="voucher-tabs">


                    <a
                        href="{{
                            route(
                                'profile.vouchers',
                                [
                                    'tab' =>
                                        'available',
                                ]
                            )
                        }}"
                        class="
                            voucher-tab

                            {{
                                $tab === 'available'
                                    ? 'active'
                                    : ''
                            }}
                        "
                    >

                        Có thể sử dụng

                        <span class="voucher-tab-count">

                            {{
                                $counts[
                                    'available'
                                ]
                            }}

                        </span>

                    </a>


                    <a
                        href="{{
                            route(
                                'profile.vouchers',
                                [
                                    'tab' =>
                                        'used',
                                ]
                            )
                        }}"
                        class="
                            voucher-tab

                            {{
                                $tab === 'used'
                                    ? 'active'
                                    : ''
                            }}
                        "
                    >

                        Đã sử dụng

                        <span class="voucher-tab-count">

                            {{
                                $counts[
                                    'used'
                                ]
                            }}

                        </span>

                    </a>


                    <a
                        href="{{
                            route(
                                'profile.vouchers',
                                [
                                    'tab' =>
                                        'expired',
                                ]
                            )
                        }}"
                        class="
                            voucher-tab

                            {{
                                $tab === 'expired'
                                    ? 'active'
                                    : ''
                            }}
                        "
                    >

                        Hết hiệu lực

                        <span class="voucher-tab-count">

                            {{
                                $counts[
                                    'expired'
                                ]
                            }}

                        </span>

                    </a>

                </div>


                {{-- =================================================
                     LIST
                ================================================== --}}

                <div class="voucher-list">


                    @forelse (
                        $vouchers
                        as $voucher
                    )


                        @php

                            /*
                             * Nội dung mức giảm.
                             */
                            if (
                                $voucher
                                    ->discount_type
                                === 'percentage'
                            ) {

                                $discountText =
                                    'Giảm '
                                    .
                                    rtrim(
                                        rtrim(
                                            number_format(
                                                $voucher
                                                    ->discount_value,
                                                2,
                                                '.',
                                                ''
                                            ),
                                            '0'
                                        ),
                                        '.'
                                    )
                                    .
                                    '%';

                            } else {

                                $discountText =
                                    'Giảm '
                                    .
                                    number_format(
                                        $voucher
                                            ->discount_value,
                                        0,
                                        ',',
                                        '.'
                                    )
                                    .
                                    'đ';
                            }


                            /*
                             * Trạng thái giao diện.
                             */
                            $cardState =
                                $tab === 'available'
                                    ? 'available'
                                    : (
                                        $tab === 'used'
                                            ? 'used'
                                            : 'expired'
                                    );


                            $badgeText =
                                $tab === 'available'
                                    ? 'Có thể dùng'
                                    : (
                                        $tab === 'used'
                                            ? 'Đã sử dụng'
                                            : 'Hết hiệu lực'
                                    );

                        @endphp


                        <article
                            class="
                                voucher-card

                                {{
                                    $tab !== 'available'
                                        ? 'disabled'
                                        : ''
                                }}
                            "
                        >


                            {{-- LEFT --}}

                            <div class="voucher-card-left">

                                <div class="voucher-brand-icon">
                                    🌿
                                </div>

                                <div class="voucher-brand-name">
                                    MiniGrove
                                </div>

                                <div class="voucher-brand-subtitle">
                                    MINI ECOSYSTEM
                                </div>

                            </div>


                            {{-- BODY --}}

                            <div class="voucher-card-body">


                                <span
                                    class="
                                        voucher-badge
                                        {{ $cardState }}
                                    "
                                >
                                    {{ $badgeText }}
                                </span>


                                <div class="voucher-discount">

                                    {{ $discountText }}

                                </div>


                                <div class="voucher-condition">

                                    <div style="margin-bottom: 5px;">
                                        <span style="display: inline-block; font-size: 11px; font-weight: 700; color: {{ $voucher->isShippingScope() ? '#8a531b' : '#174d37' }}; background: {{ $voucher->isShippingScope() ? '#fef3e6' : '#eaf5ee' }}; padding: 2px 7px; border-radius: 4px;">
                                            {{ $voucher->isShippingScope() ? '🚚 Giảm phí vận chuyển' : '🏷️ Giảm giá đơn hàng' }}
                                        </span>
                                    </div>

                                    Đơn tối thiểu

                                    <strong>

                                        {{
                                            number_format(
                                                $voucher
                                                    ->min_order_amount,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}đ

                                    </strong>


                                    @if (
                                        $voucher
                                            ->discount_type
                                        === 'percentage'
                                        &&
                                        $voucher
                                            ->max_discount_amount
                                    )

                                        <br>

                                        Giảm tối đa

                                        <strong>

                                            {{
                                                number_format(
                                                    $voucher
                                                        ->max_discount_amount,
                                                    0,
                                                    ',',
                                                    '.'
                                                )
                                            }}đ

                                        </strong>

                                    @endif

                                </div>


                                @if (
                                    $voucher
                                        ->per_user_limit
                                )

                                    <div class="voucher-limit">

                                        Giới hạn:

                                        {{
                                            $voucher
                                                ->per_user_limit
                                        }}

                                        lượt / tài khoản

                                    </div>

                                @endif


                                <div class="voucher-expiry">

                                    @if (
                                        $voucher
                                            ->end_at
                                    )

                                        Hạn sử dụng:

                                        <strong>

                                            {{
                                                $voucher
                                                    ->end_at
                                                    ->format(
                                                        'd/m/Y H:i'
                                                    )
                                            }}

                                        </strong>

                                    @else

                                        Không giới hạn thời gian

                                    @endif

                                </div>


                                <div class="voucher-code-row">

                                    <span class="voucher-code">

                                        {{
                                            $voucher
                                                ->code
                                        }}

                                    </span>


                                    @if (
    $tab === 'available'
)

    <div
        style="
            display: flex;
            align-items: center;
            gap: 8px;
        "
    >
        <button
            type="button"
            class="voucher-copy"
            onclick="navigator.clipboard.writeText('{{ $voucher->code }}'); alert('Đã sao chép mã {{ $voucher->code }}');"
            title="Sao chép mã để sử dụng tại bước thanh toán"
        >
            Sao chép mã
        </button>

        <a
            href="{{ route('products.index') }}"
            class="voucher-copy"
            style="
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            "
        >
            Dùng ngay
        </a>

    </div>

@elseif (
    $tab === 'used'
)

    <span class="voucher-limit">

        Đã dùng:

        {{
            $voucher
                ->user_used_count
        }}

        lượt

    </span>

@endif

                                </div>

                            </div>

                        </article>


                    @empty


                        <div class="voucher-empty">

                            <div class="voucher-empty-icon">
                                🎟️
                            </div>


                            @if (
                                $tab === 'available'
                            )

                                <h2>
                                    Chưa có Voucher khả dụng
                                </h2>

                                <p>
                                    Hiện chưa có mã ưu đãi nào
                                    có thể sử dụng cho tài khoản này.
                                </p>

                            @elseif (
                                $tab === 'used'
                            )

                                <h2>
                                    Chưa sử dụng Voucher
                                </h2>

                                <p>
                                    Những Voucher bạn đã sử dụng
                                    sẽ xuất hiện tại đây.
                                </p>

                            @else

                                <h2>
                                    Không có Voucher hết hiệu lực
                                </h2>

                                <p>
                                    Voucher đã hết hạn hoặc hết lượt
                                    sẽ xuất hiện tại đây.
                                </p>

                            @endif


                            <a
                                href="{{ route('products.index') }}"
                                class="voucher-shop-button"
                            >
                                Tiếp tục mua sắm
                            </a>

                        </div>

                    @endforelse

                </div>

            </main>

        </div>

    </div>

</section>

@endsection