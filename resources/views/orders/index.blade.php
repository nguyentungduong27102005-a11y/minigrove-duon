@extends('layouts.customer')

@section('title', 'Đơn mua - MiniGrove')


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


    /*
    |--------------------------------------------------------------------------
    | ORDER STATUS
    |--------------------------------------------------------------------------
    */

    $orderStatusLabels = [

        'pending' =>
            'Chờ xác nhận',

        'confirmed' =>
            'Đã xác nhận',

        'preparing' =>
            'Đang chuẩn bị',

        'packed' =>
            'Đã đóng gói',

        'shipping' =>
            'Đang giao',

        'completed' =>
            'Hoàn thành',

        'cancelled' =>
            'Đã hủy',
    ];


    /*
    |--------------------------------------------------------------------------
    | PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    $paymentStatusLabels = [

        'pending' =>
            'Chờ thanh toán',

        'paid' =>
            'Đã thanh toán',

        'failed' =>
            'Thanh toán thất bại',

        'refunded' =>
            'Đã hoàn tiền',
    ];


    /*
    |--------------------------------------------------------------------------
    | RETURN STATUS
    |--------------------------------------------------------------------------
    */

    $returnStatusLabels = [

        'pending' =>
            'Chờ xử lý',

        'approved' =>
            'Đã chấp nhận',

        'rejected' =>
            'Đã từ chối',

        'returning' =>
            'Đang trả hàng',

        'received' =>
            'Đã nhận hàng trả',

        'refunded' =>
            'Đã hoàn tiền',
    ];


    /*
    |--------------------------------------------------------------------------
    | RETURN REASON
    |--------------------------------------------------------------------------
    */

    $returnReasonLabels = [

        'damaged' =>
            'Sản phẩm bị hư hỏng',

        'wrong_item' =>
            'Giao sai sản phẩm',

        'not_as_described' =>
            'Sản phẩm không đúng mô tả',

        'quality_issue' =>
            'Sản phẩm có vấn đề chất lượng',

        'other' =>
            'Lý do khác',
    ];

@endphp


@push('styles')

<style>

/* =========================================================
   PAGE
========================================================= */

.purchase-page {
    min-height: 720px;
    padding: 34px 0 70px;
    background: #f6f8f7;
}

.purchase-layout {
    display: grid;
    grid-template-columns: 260px minmax(0, 1fr);
    gap: 28px;
    align-items: start;
}


/* =========================================================
   SIDEBAR
========================================================= */

.purchase-sidebar {
    background: #fff;

    border: 1px solid #e3e9e5;
    border-radius: 14px;

    overflow: hidden;
}

.purchase-user {
    display: flex;
    align-items: center;
    gap: 12px;

    padding: 22px 18px;

    border-bottom: 1px solid #edf1ef;
}

.purchase-user-avatar {
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

.purchase-user-avatar img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.purchase-user-info {
    min-width: 0;
}

.purchase-user-info strong {
    display: block;

    overflow: hidden;

    color: #173f30;

    font-size: 15px;

    white-space: nowrap;
    text-overflow: ellipsis;
}

.purchase-user-info span {
    display: block;

    margin-top: 4px;

    color: #7a8781;

    font-size: 13px;
}

.purchase-menu {
    padding: 10px;
}

.purchase-menu-title {
    padding: 11px 12px;

    color: #174d37;

    font-size: 14px;
    font-weight: 700;
}

.purchase-menu-sub {
    padding-left: 36px;

    margin-bottom: 5px;
}

.purchase-menu-sub a,
.purchase-menu-link {
    display: block;

    box-sizing: border-box;

    width: 100%;

    padding: 9px 10px;

    border-radius: 8px;

    color: #5d6c65;

    font-size: 14px;

    text-decoration: none;
}

.purchase-menu-link {
    padding: 11px 12px;

    color: #31483e;
}

.purchase-menu-sub a:hover,
.purchase-menu-link:hover {
    background: #eef6f1;

    color: #174d37;
}

.purchase-menu-link.active {
    background: #e8f3ed;

    color: #174d37;

    font-weight: 700;
}


/* =========================================================
   MAIN
========================================================= */

.purchase-main {
    min-width: 0;
}


/* =========================================================
   FLASH
========================================================= */

.purchase-alert {
    margin-bottom: 16px;

    padding: 14px 16px;

    border-radius: 10px;

    font-size: 14px;
}

.purchase-alert.success {
    border: 1px solid #c8e5d4;

    background: #edf8f1;

    color: #17623f;
}

.purchase-alert.error {
    border: 1px solid #f0c7c7;

    background: #fff1f1;

    color: #b42318;
}


/* =========================================================
   TABS
========================================================= */

.purchase-tabs {
    display: grid;

    grid-template-columns:
        repeat(
            7,
            minmax(105px, 1fr)
        );

    overflow-x: auto;

    margin-bottom: 14px;

    background: #fff;

    border: 1px solid #e4e9e6;
    border-radius: 12px;
}

.purchase-tab {
    position: relative;

    display: flex;
    align-items: center;
    justify-content: center;

    min-height: 62px;

    padding: 0 13px;

    color: #53645c;

    font-size: 13px;
    font-weight: 600;

    text-align: center;
    text-decoration: none;

    white-space: nowrap;
}

.purchase-tab:hover {
    color: #174d37;
}

.purchase-tab.active {
    color: #174d37;

    font-weight: 700;
}

.purchase-tab.active::after {
    content: "";

    position: absolute;

    left: 14px;
    right: 14px;
    bottom: 0;

    height: 3px;

    border-radius:
        3px 3px 0 0;

    background: #174d37;
}


/* =========================================================
   SEARCH
========================================================= */

.purchase-search {
    position: relative;

    margin-bottom: 18px;
}

.purchase-search-icon {
    position: absolute;

    top: 50%;
    left: 18px;

    transform: translateY(-50%);

    color: #87938d;

    font-size: 18px;
}

.purchase-search input {
    width: 100%;

    min-height: 50px;

    box-sizing: border-box;

    padding:
        0
        18px
        0
        48px;

    border: 1px solid #e2e8e4;
    border-radius: 10px;

    outline: none;

    background: #fff;

    color: #31483e;

    font: inherit;
}

.purchase-search input:focus {
    border-color: #2f7658;

    box-shadow:
        0 0 0 3px
        rgba(47, 118, 88, .08);
}


/* =========================================================
   ORDER CARD
========================================================= */

.purchase-order-list {
    display: flex;
    flex-direction: column;

    gap: 16px;
}

.purchase-order-card {
    overflow: hidden;

    background: #fff;

    border: 1px solid #e3e9e5;
    border-radius: 12px;
}

.purchase-order-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;

    padding: 17px 20px;

    border-bottom: 1px solid #edf1ef;
}

.purchase-order-code {
    color: #174d37;

    font-size: 15px;
    font-weight: 800;
}

.purchase-order-date {
    margin-top: 4px;

    color: #85918b;

    font-size: 12px;
}

.purchase-status {
    flex-shrink: 0;

    padding: 7px 12px;

    border-radius: 999px;

    font-size: 12px;
    font-weight: 700;
}

.purchase-status-pending {
    background: #fff5df;

    color: #9a6200;
}

.purchase-status-confirmed,
.purchase-status-preparing,
.purchase-status-packed {
    background: #edf5ff;

    color: #27659d;
}

.purchase-status-shipping {
    background: #e7f7f5;

    color: #16766d;
}

.purchase-status-completed {
    background: #eaf7ee;

    color: #1b7147;
}

.purchase-status-cancelled {
    background: #fff0f0;

    color: #c33c3c;
}


/* =========================================================
   PRODUCTS
========================================================= */

.purchase-products {
    padding: 0 20px;
}

.purchase-product {
    display: grid;

    grid-template-columns:
        78px
        minmax(0, 1fr)
        auto;

    gap: 15px;

    align-items: center;

    padding: 16px 0;

    border-bottom: 1px solid #edf1ef;
}

.purchase-product:last-child {
    border-bottom: 0;
}

.purchase-product-image {
    width: 78px;
    height: 78px;

    overflow: hidden;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #e5ebe7;
    border-radius: 8px;

    background: #f7f9f8;
}

.purchase-product-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.purchase-product-placeholder {
    font-size: 29px;
}

.purchase-product-info {
    min-width: 0;
}

.purchase-product-info strong {
    display: block;

    margin-bottom: 7px;

    overflow: hidden;

    color: #213a2f;

    font-size: 14px;

    white-space: nowrap;
    text-overflow: ellipsis;
}

.purchase-product-info span {
    display: block;

    color: #7b8882;

    font-size: 13px;
}

.purchase-product-price {
    color: #174d37;

    font-size: 14px;
    font-weight: 700;

    white-space: nowrap;
}

.purchase-more {
    padding: 11px 0 14px;

    color: #74827b;

    font-size: 12px;

    text-align: center;
}


/* =========================================================
   ORDER FOOTER
========================================================= */

.purchase-order-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 16px 20px;

    border-top: 1px solid #edf1ef;

    background: #fbfcfb;
}

.purchase-payment {
    display: flex;
    align-items: center;
    gap: 7px;

    color: #73817a;

    font-size: 13px;
}

.purchase-payment strong {
    color: #174d37;
}

.purchase-order-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    gap: 13px;
}

.purchase-total {
    margin-right: 7px;

    text-align: right;
}

.purchase-total span {
    display: block;

    margin-bottom: 3px;

    color: #87928d;

    font-size: 11px;
}

.purchase-total strong {
    color: #174d37;

    font-size: 20px;
}

.purchase-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 42px;

    box-sizing: border-box;

    padding: 0 17px;

    border: 1px solid #174d37;
    border-radius: 8px;

    background: #174d37;

    color: #fff;

    font-size: 13px;
    font-weight: 700;

    text-decoration: none;

    cursor: pointer;
}

.purchase-button:hover {
    background: #123e2d;
}

.purchase-button.secondary {
    background: #fff;

    color: #174d37;
}

.purchase-button.secondary:hover {
    background: #f2f8f4;
}


/* =========================================================
   EMPTY
========================================================= */

.purchase-empty {
    padding: 70px 25px;

    background: #fff;

    border: 1px solid #e3e9e5;
    border-radius: 12px;

    text-align: center;
}

.purchase-empty-icon {
    margin-bottom: 14px;

    font-size: 45px;
}

.purchase-empty h2 {
    margin: 0 0 8px;

    color: #173f30;

    font-size: 21px;
}

.purchase-empty p {
    margin: 0 0 22px;

    color: #7c8983;

    font-size: 14px;
}


/* =========================================================
   RETURNS
========================================================= */

.return-list {
    display: flex;
    flex-direction: column;

    gap: 16px;
}

.return-card {
    overflow: hidden;

    background: #fff;

    border: 1px solid #e3e9e5;
    border-radius: 12px;
}

.return-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    padding: 17px 20px;

    border-bottom: 1px solid #edf1ef;
}

.return-code {
    color: #174d37;

    font-weight: 800;
}

.return-order {
    margin-top: 4px;

    color: #7d8983;

    font-size: 12px;
}

.return-status {
    padding: 7px 12px;

    border-radius: 999px;

    background: #eef6f1;

    color: #17623f;

    font-size: 12px;
    font-weight: 700;
}

.return-body {
    padding: 18px 20px;
}

.return-row {
    display: flex;
    justify-content: space-between;

    gap: 20px;

    padding: 9px 0;

    color: #718078;

    font-size: 13px;
}

.return-row strong {
    color: #263c32;

    text-align: right;
}

.return-items {
    margin-top: 10px;

    padding: 12px 14px;

    border-radius: 8px;

    background: #f7f9f8;
}

.return-item {
    display: flex;
    justify-content: space-between;

    gap: 15px;

    padding: 5px 0;

    color: #53645c;

    font-size: 13px;
}

.return-footer {
    display: flex;
    justify-content: flex-end;

    gap: 10px;

    padding: 15px 20px;

    border-top: 1px solid #edf1ef;

    background: #fbfcfb;
}


/* =========================================================
   PAGINATION
========================================================= */

.purchase-pagination {
    margin-top: 24px;
}


/* =========================================================
   CLIENT SEARCH EMPTY
========================================================= */

.purchase-search-empty {
    display: none;

    padding: 45px 25px;

    background: #fff;

    border: 1px solid #e3e9e5;
    border-radius: 12px;

    color: #7b8882;

    text-align: center;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1000px) {

    .purchase-layout {
        grid-template-columns: 220px minmax(0, 1fr);
    }

    .purchase-tabs {
        display: flex;
    }

    .purchase-tab {
        min-width: 130px;
    }
}


@media (max-width: 760px) {

    .purchase-layout {
        grid-template-columns: 1fr;
    }

    .purchase-product {
        grid-template-columns:
            65px
            minmax(0, 1fr);
    }

    .purchase-product-image {
        width: 65px;
        height: 65px;
    }

    .purchase-product-price {
        grid-column: 2;
    }

    .purchase-order-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    .purchase-order-actions {
        width: 100%;

        flex-wrap: wrap;
        justify-content: space-between;
    }
}

</style>

@endpush


@section('content')

<section class="purchase-page">

    <div class="customer-container">

        <div class="purchase-layout">


            {{-- =====================================================
                 SIDEBAR
            ====================================================== --}}

            <aside class="purchase-sidebar">


                <div class="purchase-user">

                    <div class="purchase-user-avatar">

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


                    <div class="purchase-user-info">

                        <strong>
                            {{ $accountUser->name }}
                        </strong>

                        <span>
                            Khách hàng
                        </span>

                    </div>

                </div>


                <nav class="purchase-menu">

                    <div class="purchase-menu-title">
                        Tài khoản của tôi
                    </div>


                    <div class="purchase-menu-sub">

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
                        class="purchase-menu-link active"
                    >
                        Đơn mua
                    </a>


                    <a
    href="{{ route('profile.vouchers') }}"
    class="purchase-menu-link"
>
    Kho Voucher
</a>

                </nav>

            </aside>


            {{-- =====================================================
                 MAIN
            ====================================================== --}}

            <main class="purchase-main">



                {{-- =================================================
                     TABS
                ================================================== --}}

                <div class="purchase-tabs">

                    @foreach (
                        $tabs
                        as $tabKey => $tabLabel
                    )

                        <a
                            href="{{
                                route(
                                    'orders.index',
                                    [
                                        'tab' =>
                                            $tabKey,
                                    ]
                                )
                            }}"
                            class="
                                purchase-tab

                                {{
                                    $tab === $tabKey
                                        ? 'active'
                                        : ''
                                }}
                            "
                        >

                            {{ $tabLabel }}

                        </a>

                    @endforeach

                </div>


                {{-- =================================================
                     SEARCH
                ================================================== --}}

                @if ($tab !== 'returns')

                    <div class="purchase-search">

                        <span class="purchase-search-icon">
                            ⌕
                        </span>

                        <input
                            id="purchaseOrderSearch"
                            type="text"
                            placeholder="Tìm kiếm theo mã đơn hàng hoặc tên sản phẩm"
                            autocomplete="off"
                        >

                    </div>

                @endif


                {{-- =================================================
                     RETURNS TAB
                ================================================== --}}

                @if ($tab === 'returns')


                    @if (
                        !$returnRequests
                        ||
                        $returnRequests->isEmpty()
                    )

                        <div class="purchase-empty">

                            <div class="purchase-empty-icon">
                                ↩
                            </div>

                            <h2>
                                Chưa có yêu cầu trả hàng
                            </h2>

                            <p>
                                Các yêu cầu trả hàng hoặc hoàn tiền
                                của bạn sẽ xuất hiện tại đây.
                            </p>

                            <a
                                href="{{
                                    route(
                                        'orders.index',
                                        [
                                            'tab' =>
                                                'completed',
                                        ]
                                    )
                                }}"
                                class="purchase-button"
                            >
                                Xem đơn đã hoàn thành
                            </a>

                        </div>


                    @else


                        <div class="return-list">

                            @foreach (
                                $returnRequests
                                as $returnRequest
                            )

                                <article class="return-card">


                                    <div class="return-card-header">

                                        <div>

                                            <div class="return-code">

                                                {{
                                                    $returnRequest
                                                        ->return_code
                                                }}

                                            </div>

                                            <div class="return-order">

                                                Đơn hàng:

                                                <strong>

                                                    {{
                                                        $returnRequest
                                                            ->order
                                                            ?->order_code
                                                        ?? 'Không xác định'
                                                    }}

                                                </strong>

                                            </div>

                                        </div>


                                        <span class="return-status">

                                            {{
                                                $returnStatusLabels[
                                                    $returnRequest
                                                        ->status
                                                ]
                                                ??
                                                $returnRequest
                                                    ->status
                                            }}

                                        </span>

                                    </div>


                                    <div class="return-body">


                                        <div class="return-row">

                                            <span>
                                                Ngày yêu cầu
                                            </span>

                                            <strong>

                                                {{
                                                    $returnRequest
                                                        ->created_at
                                                        ->format(
                                                            'd/m/Y H:i'
                                                        )
                                                }}

                                            </strong>

                                        </div>


                                        <div class="return-row">

                                            <span>
                                                Lý do
                                            </span>

                                            <strong>

                                                {{
                                                    $returnReasonLabels[
                                                        $returnRequest
                                                            ->reason
                                                    ]
                                                    ??
                                                    $returnRequest
                                                        ->reason
                                                }}

                                            </strong>

                                        </div>


                                        <div class="return-row">

                                            <span>
                                                Số sản phẩm yêu cầu trả
                                            </span>

                                            <strong>

                                                {{
                                                    $returnRequest
                                                        ->items
                                                        ->sum(
                                                            'quantity'
                                                        )
                                                }}

                                                sản phẩm

                                            </strong>

                                        </div>


                                        <div class="return-row">

                                            <span>
                                                Số tiền dự kiến hoàn
                                            </span>

                                            <strong>

                                                {{
                                                    number_format(
                                                        $returnRequest
                                                            ->refund_amount,
                                                        0,
                                                        ',',
                                                        '.'
                                                    )
                                                }}đ

                                            </strong>

                                        </div>


                                        <div class="return-items">

                                            @foreach (
                                                $returnRequest
                                                    ->items
                                                    ->take(2)
                                                as $returnItem
                                            )

                                                <div class="return-item">

                                                    <span>

                                                        {{
                                                            $returnItem
                                                                ->orderDetail
                                                                ?->item_name
                                                            ?? 'Sản phẩm'
                                                        }}

                                                    </span>

                                                    <strong>

                                                        ×
                                                        {{
                                                            $returnItem
                                                                ->quantity
                                                        }}

                                                    </strong>

                                                </div>

                                            @endforeach


                                            @if (
                                                $returnRequest
                                                    ->items
                                                    ->count()
                                                > 2
                                            )

                                                <div class="return-item">

                                                    <span>

                                                        Và còn

                                                        {{
                                                            $returnRequest
                                                                ->items
                                                                ->count()
                                                            - 2
                                                        }}

                                                        sản phẩm khác

                                                    </span>

                                                </div>

                                            @endif

                                        </div>

                                    </div>


                                    <div class="return-footer">

                                        <a
                                            href="{{
                                                route(
                                                    'orders.show',
                                                    $returnRequest
                                                        ->order_id
                                                )
                                            }}"
                                            class="
                                                purchase-button
                                                secondary
                                            "
                                        >
                                            Xem đơn hàng
                                        </a>


                                        <a
                                            href="{{
                                                route(
                                                    'returns.show',
                                                    $returnRequest
                                                )
                                            }}"
                                            class="purchase-button"
                                        >
                                            Xem chi tiết
                                        </a>

                                    </div>

                                </article>

                            @endforeach

                        </div>


                        <div class="purchase-pagination">

                            {{
                                $returnRequests
                                    ->links()
                            }}

                        </div>

                    @endif


                {{-- =================================================
                     ORDER TABS
                ================================================== --}}

                @else


                    @if (
                        !$orders
                        ||
                        $orders->isEmpty()
                    )

                        <div class="purchase-empty">

                            <div class="purchase-empty-icon">
                                📦
                            </div>

                            <h2>
                                Chưa có đơn hàng
                            </h2>

                            <p>
                                Không có đơn hàng nào
                                trong trạng thái này.
                            </p>

                            <a
                                href="{{ route('products.index') }}"
                                class="purchase-button"
                            >
                                Khám phá sản phẩm
                            </a>

                        </div>


                    @else


                        <div
                            id="purchaseOrderList"
                            class="purchase-order-list"
                        >


                            @foreach (
                                $orders
                                as $order
                            )


                                <article
                                    class="purchase-order-card"
                                    data-order-search="{{
                                        strtolower(
                                            $order->order_code
                                            . ' '
                                            . $order->details
                                                ->pluck(
                                                    'item_name'
                                                )
                                                ->implode(' ')
                                        )
                                    }}"
                                >


                                    {{-- =============================
                                         HEADER
                                    ============================== --}}

                                    <div class="purchase-order-header">

                                        <div>

                                            <div class="purchase-order-code">

                                                {{
                                                    $order
                                                        ->order_code
                                                }}

                                            </div>

                                            <div class="purchase-order-date">

                                                Đặt lúc

                                                {{
                                                    $order
                                                        ->created_at
                                                        ->format(
                                                            'd/m/Y H:i'
                                                        )
                                                }}

                                            </div>

                                        </div>


                                        <span
                                            class="
                                                purchase-status
                                                purchase-status-{{
                                                    $order
                                                        ->order_status
                                                }}
                                            "
                                        >

                                            {{
                                                $orderStatusLabels[
                                                    $order
                                                        ->order_status
                                                ]
                                                ??
                                                $order
                                                    ->order_status
                                            }}

                                        </span>

                                    </div>


                                    {{-- =============================
                                         PRODUCTS
                                    ============================== --}}

                                    <div class="purchase-products">


                                        @foreach (
                                            $order
                                                ->details
                                                ->take(3)
                                            as $detail
                                        )


                                            @php

                                                $product =
                                                    $detail
                                                        ->product;


                                                $primaryImage =
                                                    $product
                                                        ?->images
                                                        ?->firstWhere(
                                                            'is_primary',
                                                            true
                                                        )
                                                    ??
                                                    $product
                                                        ?->images
                                                        ?->first();

                                            @endphp


                                            <div class="purchase-product">


                                                <div class="purchase-product-image">

                                                    @if ($primaryImage)

                                                        <img
                                                            src="{{
                                                                asset(
                                                                    'storage/'
                                                                    .
                                                                    $primaryImage
                                                                        ->image_path
                                                                )
                                                            }}"
                                                            alt="{{
                                                                $detail
                                                                    ->item_name
                                                            }}"
                                                        >

                                                    @else

                                                        <span
                                                            class="
                                                                purchase-product-placeholder
                                                            "
                                                        >
                                                            🌱
                                                        </span>

                                                    @endif

                                                </div>


                                                <div class="purchase-product-info">

                                                    <strong>

                                                        {{
                                                            $detail
                                                                ->item_name
                                                        }}

                                                    </strong>

                                                    <span>

                                                        {{
                                                            number_format(
                                                                $detail
                                                                    ->unit_price,
                                                                0,
                                                                ',',
                                                                '.'
                                                            )
                                                        }}đ

                                                        ×

                                                        {{
                                                            $detail
                                                                ->quantity
                                                        }}

                                                    </span>

                                                </div>


                                                <div class="purchase-product-price">

                                                    {{
                                                        number_format(
                                                            $detail
                                                                ->subtotal,
                                                            0,
                                                            ',',
                                                            '.'
                                                        )
                                                    }}đ

                                                </div>

                                            </div>

                                        @endforeach


                                        @if (
                                            $order
                                                ->details_count
                                            > 3
                                        )

                                            <div class="purchase-more">

                                                Xem thêm

                                                {{
                                                    $order
                                                        ->details_count
                                                    - 3
                                                }}

                                                sản phẩm trong đơn

                                            </div>

                                        @endif

                                    </div>


                                    {{-- =============================
                                         FOOTER
                                    ============================== --}}

                                    <div class="purchase-order-footer">


                                        <div class="purchase-payment">

                                            <strong>
                                                {{
                                                    $order
                                                        ->payment_method
                                                }}
                                            </strong>

                                            <span>
                                                •
                                            </span>

                                            <span>

                                                {{
                                                    $paymentStatusLabels[
                                                        $order
                                                            ->payment_status
                                                    ]
                                                    ??
                                                    $order
                                                        ->payment_status
                                                }}

                                            </span>

                                        </div>


                                        <div class="purchase-order-actions">


                                            <div class="purchase-total">

                                                <span>
                                                    Thành tiền
                                                </span>

                                                <strong>

                                                    {{
                                                        number_format(
                                                            $order
                                                                ->total_amount,
                                                            0,
                                                            ',',
                                                            '.'
                                                        )
                                                    }}đ

                                                </strong>

                                            </div>


                                            @if (
    $order->order_status === 'pending'
    && $order->payment_status !== 'paid'
)

                                                <form
                                                    method="POST"
                                                    action="{{
                                                        route(
                                                            'orders.cancel',
                                                            $order
                                                        )
                                                    }}"
                                                    onsubmit="
                                                        return confirm(
                                                            'Bạn có chắc muốn hủy đơn hàng này?'
                                                        );
                                                    "
                                                >

                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="
                                                            purchase-button
                                                            secondary
                                                        "
                                                    >
                                                        Hủy đơn
                                                    </button>

                                                </form>

                                            @endif


                                            @if ($order->order_status === 'shipping')

                                                <form
                                                    method="POST"
                                                    action="{{
                                                        route(
                                                            'orders.received',
                                                            $order
                                                        )
                                                    }}"
                                                    onsubmit="
                                                        return confirm(
                                                            'Bạn xác nhận đã nhận được hàng và sản phẩm không có vấn đề?'
                                                        );
                                                    "
                                                >

                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="purchase-button"
                                                    >
                                                        Đã nhận được hàng
                                                    </button>

                                                </form>

                                            @endif


                                            <a
                                                href="{{
                                                    route(
                                                        'orders.show',
                                                        $order
                                                    )
                                                }}"
                                                class="
                                                    purchase-button
                                                    {{ $order->order_status === 'shipping' ? 'secondary' : '' }}
                                                "
                                            >
                                                Xem chi tiết
                                            </a>

                                        </div>

                                    </div>

                                </article>

                            @endforeach

                        </div>


                        <div
                            id="purchaseSearchEmpty"
                            class="purchase-search-empty"
                        >
                            Không tìm thấy đơn hàng phù hợp.
                        </div>


                        <div class="purchase-pagination">

                            {{
                                $orders->links()
                            }}

                        </div>

                    @endif

                @endif

            </main>

        </div>

    </div>

</section>


@if ($tab !== 'returns')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const searchInput =
            document.getElementById(
                'purchaseOrderSearch'
            );

        const cards =
            document.querySelectorAll(
                '.purchase-order-card'
            );

        const empty =
            document.getElementById(
                'purchaseSearchEmpty'
            );


        if (!searchInput) {
            return;
        }


        searchInput.addEventListener(
            'input',
            function () {

                const keyword =
                    this.value
                        .trim()
                        .toLowerCase();


                let visibleCount = 0;


                cards.forEach(
                    function (card) {

                        const text =
                            (
                                card.dataset
                                    .orderSearch
                                || ''
                            )
                            .toLowerCase();


                        const matched =
                            keyword === ''
                            ||
                            text.includes(
                                keyword
                            );


                        card.style.display =
                            matched
                                ? ''
                                : 'none';


                        if (matched) {
                            visibleCount++;
                        }
                    }
                );


                if (empty) {

                    empty.style.display =
                        visibleCount === 0
                            ? 'block'
                            : 'none';
                }
            }
        );
    }
);

</script>

@endif

@endsection