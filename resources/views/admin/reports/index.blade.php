@extends('layouts.admin')

@section('title', 'Báo cáo & Thống kê - MiniGrove Admin')

@section('page-title', 'Báo cáo & Thống kê')

@push('styles')
<style>
    /* =========================================================
       REPORT PAGE
    ========================================================= */

    .report-page {
        padding: 30px;
        background: #f4f7f5;
        min-height: calc(100vh - 70px);
        color: #172b2a;
        font-family: Inter, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
    }

    .report-container {
        width: 100%;
        max-width: 1380px;
        margin: 0 auto;
    }

    .report-page *,
    .report-page *::before,
    .report-page *::after {
        box-sizing: border-box;
    }

    /* =========================================================
       HEADER
    ========================================================= */

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 28px;
        margin-bottom: 24px;
    }

    .report-header-content h1 {
        margin: 0 0 8px;
        font-size: 30px;
        line-height: 1.2;
        font-weight: 800;
        letter-spacing: -0.35px;
        color: #173b31;
    }

    .report-header-content p {
        margin: 0;
        color: #71807c;
        font-size: 14px;
        line-height: 1.6;
    }

    /* =========================================================
       FILTER
    ========================================================= */

    .report-filter {
        flex: 0 0 auto;
        background: #ffffff;
        border: 1px solid #dce6e1;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: flex-end;
        gap: 14px;
        box-shadow: 0 3px 12px rgba(20, 55, 43, 0.04);
    }

    .report-filter-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .report-filter-group label {
        font-size: 12px;
        font-weight: 700;
        color: #344c46;
    }

    .report-filter-group input {
        width: 170px;
        height: 40px;
        padding: 0 12px;
        border: 1px solid #ccd9d4;
        border-radius: 10px;
        background: #fff;
        color: #20332f;
        outline: none;
        font-size: 13px;
        box-sizing: border-box;
    }

    .report-filter-group input:focus {
        border-color: #23694f;
        box-shadow: 0 0 0 3px rgba(35, 105, 79, 0.08);
    }

    .report-filter-button {
        height: 40px;
        padding: 0 18px;
        border: 0;
        border-radius: 10px;
        background: #162236;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .report-filter-button:hover {
        transform: translateY(-1px);
        opacity: 0.94;
    }

    /* =========================================================
       SUMMARY CARDS
    ========================================================= */

    .report-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 18px;
    }

    .report-summary-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #fff;
        border: 1px solid #dce6e1;
        border-radius: 15px;
        padding: 20px;
        min-height: 118px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(20, 55, 43, 0.035);
    }

    .report-summary-card::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 3px;
    }

    .report-summary-card.revenue::after {
        background: #55b979;
    }

    .report-summary-card.orders::after {
        background: #438bc1;
    }

    .report-summary-card.completed::after {
        background: #43aa76;
    }

    .report-summary-card.cancelled::after {
        background: #d35d5d;
    }

    .report-summary-label {
        color: #65746f;
        font-size: 13px;
        line-height: 1.4;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .report-summary-value {
        font-size: 27px;
        line-height: 1.15;
        font-weight: 800;
        letter-spacing: -0.2px;
        color: #12251f;
    }

    .report-summary-note {
        margin-top: 8px;
        font-size: 11px;
        line-height: 1.45;
        color: #94a19d;
    }

    .report-summary-icon {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .report-summary-card.revenue .report-summary-icon {
        background: #e2f5e9;
        color: #3da56a;
    }

    .report-summary-card.orders .report-summary-icon {
        background: #e4f0f8;
        color: #3981b5;
    }

    .report-summary-card.completed .report-summary-icon {
        background: #e1f4e8;
        color: #39a16b;
    }

    .report-summary-card.cancelled .report-summary-icon {
        background: #f8e2e2;
        color: #d15757;
    }

    /* =========================================================
       GENERAL CARD
    ========================================================= */

    .report-card {
        background: #fff;
        border: 1px solid #dce6e1;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(20, 55, 43, 0.035);
    }

    .report-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e5ece9;
    }

    .report-card-header h2 {
        margin: 0 0 5px;
        font-size: 18px;
        line-height: 1.35;
        font-weight: 750;
        color: #17352e;
    }

    .report-card-header p {
        margin: 0;
        font-size: 12px;
        line-height: 1.5;
        color: #899691;
    }

    .report-card-body {
        padding: 20px;
    }

    /* =========================================================
       FINANCIAL OVERVIEW
    ========================================================= */

    .report-finance-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .report-finance-item {
        background: #f8faf9;
        border-radius: 12px;
        padding: 18px;
        min-height: 74px;
        box-sizing: border-box;
    }

    .report-finance-label {
        color: #6f7e79;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .report-finance-value {
        color: #102d25;
        font-size: 20px;
        font-weight: 800;
    }

    /* =========================================================
       TWO COLUMN
    ========================================================= */

    .report-two-column {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(360px, 0.65fr);
        gap: 18px;
        margin-top: 18px;
        align-items: stretch;
    }

    /* =========================================================
       CHART
    ========================================================= */

    .report-chart-wrapper {
        position: relative;
        height: 285px;
    }

    .report-chart-wrapper canvas {
        width: 100% !important;
        height: 100% !important;
    }

    /* =========================================================
       STATUS
    ========================================================= */

    .report-status-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .report-status-row {
        display: grid;
        grid-template-columns: 110px 1fr 42px;
        align-items: center;
        gap: 10px;
    }

    .report-status-name {
        font-size: 13px;
        color: #465953;
    }

    .report-status-bar {
        height: 9px;
        background: #e8efec;
        border-radius: 20px;
        overflow: hidden;
    }

    .report-status-fill {
        height: 100%;
        background: #438bc1;
        border-radius: 20px;
        min-width: 3px;
    }

    .report-status-count {
        text-align: right;
        font-size: 13px;
        font-weight: 700;
        color: #203b33;
    }

    /* =========================================================
       ORDER STATUS DONUT CHART
    ========================================================= */

    .report-status-chart {
        display: grid;
        grid-template-columns: minmax(175px, 190px) minmax(0, 1fr);
        align-items: center;
        gap: 20px;
        min-height: 235px;
    }

    .report-status-donut {
        position: relative;
        width: 190px;
        height: 190px;
        margin: 0 auto;
    }

    .report-status-donut canvas {
        display: block;
        width: 190px !important;
        height: 190px !important;
    }

    .report-status-legend {
        display: flex;
        flex-direction: column;
        gap: 13px;
    }

    .report-status-legend-item {
        display: grid;
        grid-template-columns: 10px minmax(0, 1fr) auto;
        align-items: center;
        gap: 9px;
        min-width: 0;
        font-size: 12px;
    }

    .report-status-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #438bc1;
        flex: 0 0 auto;
    }

    .report-status-legend-name {
        color: #53645e;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .report-status-legend-value {
        color: #203b33;
        font-size: 12px;
    }

    /* =========================================================
       TABLE
    ========================================================= */

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th {
        text-align: left;
        padding: 12px 14px;
        font-size: 11px;
        line-height: 1.4;
        font-weight: 700;
        color: #64736e;
        background: #f7f9f8;
        border-bottom: 1px solid #e3ebe7;
    }

    .report-table td {
        padding: 13px 14px;
        font-size: 13px;
        line-height: 1.5;
        color: #273d37;
        border-bottom: 1px solid #edf1ef;
    }

    .report-table tr:last-child td {
        border-bottom: 0;
    }

    .report-table-number {
        font-weight: 700;
    }

    .report-empty {
        padding: 28px !important;
        text-align: center;
        color: #99a5a1 !important;
    }

    /* =========================================================
       TOP PRODUCTS + VOUCHER + REVIEW
       Bố cục 2/3 + 1/3: card bên trái cao bằng toàn bộ
       hai card bên phải.
    ========================================================= */

    .report-products-layout {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr);
        gap: 18px;
        margin-top: 18px;
        align-items: stretch;
    }

    .report-products-layout > .report-card {
        min-width: 0;
        height: 100%;
    }

    .report-side-stack {
        display: grid;
        grid-template-rows: repeat(2, minmax(0, 1fr));
        gap: 18px;
        min-width: 0;
    }

    .report-side-stack .report-card {
        min-width: 0;
        min-height: 0;
        display: flex;
        flex-direction: column;
    }

    .report-side-stack .report-card-header {
        flex: 0 0 auto;
        padding: 18px 20px;
    }

    .report-side-stack .report-card-body {
        flex: 1 1 auto;
        padding: 18px 20px;
        display: flex;
        align-items: center;
    }

    .report-metric-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .report-metric {
        min-height: 88px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: linear-gradient(135deg, #f7faf8 0%, #f1f6f3 100%);
        border: 1px solid #e4ece8;
        border-radius: 12px;
        padding: 16px 18px;
    }

    .report-metric-label {
        font-size: 12px;
        line-height: 1.4;
        font-weight: 600;
        color: #73817c;
        margin-bottom: 7px;
    }

    .report-metric-value {
        font-size: 22px;
        line-height: 1.2;
        font-weight: 800;
        letter-spacing: -0.2px;
        color: #17362e;
    }

    /* =========================================================
       RATING DISTRIBUTION
    ========================================================= */

    .report-rating-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

   .report-rating-row {
    display: grid;
    grid-template-columns: 24px 110px minmax(0, 1fr) 50px;
    align-items: center;
    gap: 14px;
    min-height: 34px;
}

    .report-rating-number {
        font-size: 13px;
        font-weight: 700;
        color: #52645e;
        text-align: center;
    }

    .report-rating-stars {
        display: inline-flex;
        align-items: center;
        gap: 1px;
        color: #d79b16;
        font-size: 13px;
        line-height: 1;
        white-space: nowrap;
        letter-spacing: 0;
    }

    .report-rating-stars .rating-star-empty {
        color: #d9dfdc;
    }

    .report-rating-stars .rating-star-filled {
        color: #d79b16;
    }

    .report-rating-bar {
    width: 100%;
    height: 9px;
    background: #e4e9e7;
    border-radius: 20px;
    overflow: hidden;
}

    .report-rating-fill {
        height: 100%;
        background: #438bc1;
        border-radius: 20px;
    }

    .report-rating-count {
        font-size: 12px;
        color: #5c6b66;
        text-align: right;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 1100px) {
        .report-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .report-two-column {
            grid-template-columns: 1fr;
        }

        .report-status-chart {
            grid-template-columns: 190px minmax(0, 1fr);
        }
    }

    @media (max-width: 800px) {
        .report-header {
            flex-direction: column;
            align-items: stretch;
        }

        .report-filter {
            flex-wrap: wrap;
        }

        .report-filter-group {
            flex: 1;
        }

        .report-filter-group input {
            width: 100%;
        }

        .report-finance-grid {
            grid-template-columns: 1fr;
        }

        .report-products-layout {
            grid-template-columns: 1fr;
        }

        .report-side-stack {
            grid-template-rows: none;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .report-page {
            padding: 15px;
        }

        .report-side-stack {
            grid-template-columns: 1fr;
            grid-template-rows: none;
        }

        .report-header-content h1 {
            font-size: 26px;
        }

        .report-summary-grid {
            grid-template-columns: 1fr;
        }

        .report-filter {
            flex-direction: column;
            align-items: stretch;
        }

        .report-filter-button {
            width: 100%;
        }

        .report-table {
            min-width: 600px;
        }

        .report-card {
            overflow-x: auto;
        }
    }
</style>
@endpush


@section('content')

<div class="report-page">

    <div class="report-container">

        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div class="report-header">

            <div class="report-header-content">

                <h1>
                    Báo cáo & Thống kê
                </h1>

                <p>
                    Tổng hợp tình hình kinh doanh MiniGrove trong khoảng thời gian được chọn.
                </p>

            </div>


            {{-- DATE FILTER --}}

            <form
                method="GET"
                action="{{ route('admin.reports.index') }}"
                class="report-filter"
            >

                <div class="report-filter-group">

                    <label for="from">
                        Từ ngày
                    </label>

                    <input
                        id="from"
                        type="date"
                        name="from"
                        value="{{ $from }}"
                    >

                </div>


                <div class="report-filter-group">

                    <label for="to">
                        Đến ngày
                    </label>

                    <input
                        id="to"
                        type="date"
                        name="to"
                        value="{{ $to }}"
                    >

                </div>


                <button
                    type="submit"
                    class="report-filter-button"
                >
                    Xem báo cáo
                </button>

            </form>

        </div>


        {{-- =====================================================
             SUMMARY
        ====================================================== --}}

        <div class="report-summary-grid">


            {{-- REVENUE --}}

            <div class="report-summary-card revenue">

                <div class="report-summary-label">
                    Doanh thu
                </div>

                <div class="report-summary-value">
                    {{ number_format($revenue, 0, ',', '.') }} đ
                </div>

                <div class="report-summary-note">
                    Chỉ tính đơn đã hoàn thành
                </div>

                <div class="report-summary-icon">
                    ↑
                </div>

            </div>


            {{-- ORDERS --}}

            <div class="report-summary-card orders">

                <div class="report-summary-label">
                    Tổng đơn hàng
                </div>

                <div class="report-summary-value">
                    {{ $orderCount }}
                </div>

                <div class="report-summary-note">
                    Trong khoảng thời gian đã chọn
                </div>

                <div class="report-summary-icon">
                    🛒
                </div>

            </div>


            {{-- COMPLETED --}}

            <div class="report-summary-card completed">

                <div class="report-summary-label">
                    Đơn hoàn thành
                </div>

                <div class="report-summary-value">
                    {{ $completedOrderCount }}
                </div>

                <div class="report-summary-note">
                    Đơn đã hoàn tất
                </div>

                <div class="report-summary-icon">
                    ✓
                </div>

            </div>


            {{-- CANCELLED --}}

            <div class="report-summary-card cancelled">

                <div class="report-summary-label">
                    Đơn đã hủy
                </div>

                <div class="report-summary-value">
                    {{ $cancelledOrderCount }}
                </div>

                <div class="report-summary-note">
                    Đơn bị hủy
                </div>

                <div class="report-summary-icon">
                    ×
                </div>

            </div>

        </div>


        {{-- =====================================================
             FINANCIAL OVERVIEW
        ====================================================== --}}

        <div class="report-card">

            <div class="report-card-header">

                <h2>
                    Tổng quan tài chính
                </h2>

                <p>
                    Các khoản chính trong khoảng thời gian đã chọn.
                </p>

            </div>


            <div class="report-card-body">

                <div class="report-finance-grid">


                    <div class="report-finance-item">

                        <div class="report-finance-label">
                            Doanh thu
                        </div>

                        <div class="report-finance-value">
                            {{ number_format($revenue, 0, ',', '.') }} đ
                        </div>

                    </div>


                    <div class="report-finance-item">

                        <div class="report-finance-label">
                            Tổng giảm giá
                        </div>

                        <div class="report-finance-value">
                            {{ number_format($discountAmount, 0, ',', '.') }} đ
                        </div>

                    </div>


                    <div class="report-finance-item">

                        <div class="report-finance-label">
                            Phí vận chuyển
                        </div>

                        <div class="report-finance-value">
                            {{ number_format($shippingRevenue, 0, ',', '.') }} đ
                        </div>

                    </div>


                    <div class="report-finance-item">

                        <div class="report-finance-label">
                            Customer
                        </div>

                        <div class="report-finance-value">
                            {{ $customerCount }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             REVENUE + ORDER STATUS
        ====================================================== --}}

        <div class="report-two-column">


            {{-- REVENUE BY DATE --}}

            <div class="report-card">

                <div class="report-card-header">

                    <h2>
                        Doanh thu theo ngày
                    </h2>

                    <p>
                        Chỉ tính các đơn hoàn thành.
                    </p>

                </div>


                <div class="report-card-body">

                    <div class="report-chart-wrapper">

                        <canvas id="revenueChart"></canvas>

                    </div>

                </div>

            </div>


            {{-- ORDER STATUS --}}

<div class="report-card">

    <div class="report-card-header">

        <h2>
            Đơn hàng theo trạng thái
        </h2>

        <p>
            Phân bố đơn hàng trong khoảng thời gian.
        </p>

    </div>

    <div class="report-card-body">

        @php

            $statusLabels = [
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'preparing' => 'Đang chuẩn bị',
                'packed' => 'Đã đóng gói',
                'shipping' => 'Đang giao',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã hủy',
            ];

        @endphp

        <div class="report-status-chart">

            <div class="report-status-donut">

                <canvas id="orderStatusChart"></canvas>

            </div>

            <div class="report-status-legend">

                @forelse($ordersByStatus as $status)

                    @php
                        $label = $statusLabels[$status->order_status]
                            ?? $status->order_status;
                    @endphp

                    <div class="report-status-legend-item">

                        <span
                            class="report-status-legend-dot"
                            data-status="{{ $status->order_status }}"
                        ></span>

                        <span class="report-status-legend-name">
                            {{ $label }}
                        </span>

                        <strong class="report-status-legend-value">
                            {{ $status->total }}
                        </strong>

                    </div>

                                @empty

                    <div class="report-empty">
                        Chưa có dữ liệu đơn hàng.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>


{{-- =====================================================
     TOP PRODUCTS + VOUCHER + REVIEW
====================================================== --}}

<div class="report-products-layout">

    {{-- TOP PRODUCTS --}}

    <div class="report-card">

        <div class="report-card-header">

            <h2>
                Sản phẩm bán chạy
            </h2>

            <p>
                Top 10 sản phẩm theo số lượng bán.
            </p>

        </div>

        <div class="report-card-body" style="padding: 0;">

            <table class="report-table">

                <thead>

                    <tr>

                        <th>
                            Sản phẩm
                        </th>

                        <th>
                            Đã bán
                        </th>

                        <th>
                            Doanh thu
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($topProducts as $product)

                        <tr>

                            <td>
                                {{ $product->item_name }}
                            </td>

                            <td class="report-table-number">
                                {{ $product->total_sold }}
                            </td>

                            <td class="report-table-number">
                                {{ number_format($product->total_revenue, 0, ',', '.') }} đ
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="3"
                                class="report-empty"
                            >
                                Chưa có dữ liệu sản phẩm.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- VOUCHER + REVIEW --}}

    <div class="report-side-stack">

        {{-- VOUCHER --}}

        <div class="report-card">

            <div class="report-card-header">

                <h2>
                    Voucher
                </h2>

                <p>
                    Tình hình sử dụng Voucher trong kỳ.
                </p>

            </div>

            <div class="report-card-body">

                <div class="report-metric-grid">

                    <div class="report-metric">

                        <div class="report-metric-label">
                            Lượt sử dụng
                        </div>

                        <div class="report-metric-value">
                            {{ $voucherUsageCount }}
                        </div>

                    </div>

                    <div class="report-metric">

                        <div class="report-metric-label">
                            Tổng tiền giảm
                        </div>

                        <div class="report-metric-value">
                            {{ number_format($voucherDiscount, 0, ',', '.') }} đ
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- REVIEW --}}

        <div class="report-card">

            <div class="report-card-header">

                <h2>
                    Đánh giá sản phẩm
                </h2>

                <p>
                    Tổng quan Review của Customer.
                </p>

            </div>

            <div class="report-card-body">

                <div class="report-metric-grid">

                    <div class="report-metric">

                        <div class="report-metric-label">
                            Tổng đánh giá
                        </div>

                        <div class="report-metric-value">
                            {{ $reviewCount }}
                        </div>

                    </div>

                    <div class="report-metric">

                        <div class="report-metric-label">
                            Điểm trung bình
                        </div>

                        <div class="report-metric-value">

                            {{ $averageRating !== null
                                ? number_format($averageRating, 1)
                                : '0.0'
                            }}

                            / 5

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =====================================================
     RATING DISTRIBUTION
====================================================== --}}

        <div
            class="report-card report-rating-distribution-card"
            style="margin-top: 18px;"
        >

            <div class="report-card-header">

                <h2>
                    Phân bổ đánh giá
                </h2>

                <p>
                    Số lượng Review theo từng mức sao.
                </p>

            </div>


            <div class="report-card-body">

                @php

                    $ratingMap = [];

                    foreach ($reviewsByRating as $rating) {
                        $ratingMap[(int) $rating->rating] = (int) $rating->total;
                    }

                    $maxRatingCount = max(
                        $ratingMap[1] ?? 0,
                        $ratingMap[2] ?? 0,
                        $ratingMap[3] ?? 0,
                        $ratingMap[4] ?? 0,
                        $ratingMap[5] ?? 0,
                        1
                    );

                @endphp


                <div class="report-rating-list">

                    @for($star = 5; $star >= 1; $star--)

                        @php

                            $ratingCount = $ratingMap[$star] ?? 0;

                            $ratingPercentage = ($ratingCount / $maxRatingCount) * 100;

                        @endphp


                        <div class="report-rating-row">

                            <div class="report-rating-number">
                                {{ $star }}
                            </div>

                            <div class="report-rating-stars">

                                <span class="rating-star-filled">{{ str_repeat('★', $star) }}</span><span class="rating-star-empty">{{ str_repeat('☆', 5 - $star) }}</span>

                            </div>


                            <div class="report-rating-bar">

                                <div
                                    class="report-rating-fill"
                                    style="width: {{ $ratingPercentage }}%;"
                                ></div>

                            </div>


                            <div class="report-rating-count">

                                {{ $ratingCount }}

                            </div>


                        </div>

                    @endfor

                </div>

            </div>

        </div>


    </div>

</div>
{{-- =====================================================
     INVENTORY + GROVE
====================================================== --}}

<div class="report-products-layout">

    {{-- INVENTORY --}}
    <div class="report-card">

        <div class="report-card-header">

            <h2>
                Tồn kho
            </h2>

            <p>
                Tổng quan tình trạng tồn kho sản phẩm hiện tại.
            </p>

        </div>

        <div class="report-card-body">

            <div class="report-metric-grid">

                <div class="report-metric">

                    <div class="report-metric-label">
                        Tổng sản phẩm
                    </div>

                    <div class="report-metric-value">
                        {{ $totalProducts }}
                    </div>

                </div>


                <div class="report-metric">

                    <div class="report-metric-label">
                        Tổng số lượng tồn
                    </div>

                    <div class="report-metric-value">
                        {{ $totalStockQuantity }}
                    </div>

                </div>


                <div class="report-metric">

                    <div class="report-metric-label">
                        Sắp hết hàng
                    </div>

                    <div class="report-metric-value">
                        {{ $lowStockProducts }}
                    </div>

                </div>


                <div class="report-metric">

                    <div class="report-metric-label">
                        Hết hàng
                    </div>

                    <div class="report-metric-value">
                        {{ $outOfStockProducts }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- GROVE --}}
    <div class="report-card">

        <div class="report-card-header">

            <h2>
                Tổng số thiết kế
            </h2>

            <p>
                Tổng quan số lượng thiết kế Studio đã được lưu trong hệ thống.
            </p>

        </div>

        <div class="report-card-body">

            <div class="report-metric-grid">

                <div class="report-metric">

                    <div class="report-metric-label">
                        Thiết kế Studio
                    </div>

                    <div class="report-metric-value">
                        {{ $groveCount }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       REVENUE BY DATE
    ========================================================= */

    const revenueCanvas =
        document.getElementById('revenueChart');

    if (revenueCanvas) {

        const revenueLabels = @json(
            $revenueByDate->pluck('report_date')
        );

        const revenueData = @json(
            $revenueByDate->pluck('revenue')
        );

        new Chart(
            revenueCanvas,
            {
                type: 'bar',

                data: {
                    labels: revenueLabels,

                    datasets: [
                        {
                            label: 'Doanh thu',
                            data: revenueData,
                            backgroundColor: '#6FA8CF',
                            borderColor: '#4F8FB9',
                            borderWidth: 1,
                            borderRadius: 6,
                            maxBarThickness: 42
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display: false
                        }
                    },

                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },

                        y: {
                            beginAtZero: true,

                            ticks: {
                                callback: function (value) {
                                    return new Intl.NumberFormat(
                                        'vi-VN'
                                    ).format(value) + ' đ';
                                }
                            }
                        }
                    }
                }
            }
        );
    }


    /* =========================================================
       ORDER STATUS DONUT
    ========================================================= */

    const orderStatusCanvas =
        document.getElementById('orderStatusChart');

    if (orderStatusCanvas) {

        const statusLabels = @json(
            $ordersByStatus->map(function ($item) use ($statusLabels) {
                return $statusLabels[$item->order_status]
                    ?? $item->order_status;
            })->values()
        );

        const statusData = @json(
            $ordersByStatus->pluck('total')->values()
        );

        const statusColors = [
            '#6FA8CF',
            '#78B99A',
            '#D5A45F',
            '#9E82C7',
            '#D47F91',
            '#62A991',
            '#8A82C0'
        ];

        new Chart(
            orderStatusCanvas,
            {
                type: 'doughnut',

                data: {
                    labels: statusLabels,

                    datasets: [
                        {
                            data: statusData,
                            backgroundColor: statusColors.slice(
                                0,
                                statusData.length
                            ),
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const total =
                                        context.dataset.data.reduce(
                                            function (sum, value) {
                                                return sum + Number(value);
                                            },
                                            0
                                        );

                                    const value = Number(context.raw);

                                    const percentage =
                                        total > 0
                                            ? (value / total * 100).toFixed(0)
                                            : 0;

                                    return ` ${value} đơn (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            }
        );

        document
            .querySelectorAll('.report-status-legend-dot')
            .forEach(function (dot, index) {
                dot.style.background =
                    statusColors[index] || '#6FA8CF';
            });
    }

});

</script>

@endpush