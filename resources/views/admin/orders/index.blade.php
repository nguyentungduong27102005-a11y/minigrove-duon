@extends('layouts.admin')

@section('title', 'Đơn hàng - MiniGrove')
@section('page-title', 'Đơn hàng')

@section('content')

@php
    $statusLabels = [
        'pending'   => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'packed'    => 'Đã đóng gói',
        'shipping'  => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];

    $paymentLabels = [
        'pending'  => 'Chờ thanh toán',
        'paid'     => 'Đã thanh toán',
        'failed'   => 'Thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];
@endphp

<div class="admin-list-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">
                Đơn hàng
            </h1>
            <p class="admin-index-subtitle">
                Theo dõi và xử lý đơn hàng MiniGrove.
            </p>
        </div>
    </div>

    {{-- =========================================================
         STATUS TABS NAVIGATION
    ========================================================== --}}
    <nav class="admin-status-tabs" aria-label="Lọc theo trạng thái đơn hàng">
        <a
            href="{{ route('admin.orders.index', array_filter(['keyword' => $keyword, 'payment_method' => $paymentMethod])) }}"
            class="admin-status-tab {{ !$status ? 'active' : '' }}"
        >
            Tất cả
        </a>

        @foreach ($statusLabels as $value => $label)
            <a
                href="{{ route('admin.orders.index', array_filter(['status' => $value, 'keyword' => $keyword, 'payment_method' => $paymentMethod])) }}"
                class="admin-status-tab {{ $status === $value ? 'active' : '' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </nav>

    {{-- =========================================================
         TOOLBAR / FILTER
    ========================================================== --}}
    <div class="admin-toolbar">
        <form
            action="{{ route('admin.orders.index') }}"
            method="GET"
            class="admin-toolbar-form"
        >
            <div class="admin-toolbar-search">
                <span class="admin-toolbar-search-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    placeholder="Mã đơn hoặc Customer ID..."
                    aria-label="Tìm kiếm đơn hàng"
                >
            </div>

            <div class="admin-toolbar-select">
                <select id="status" name="status" aria-label="Trạng thái đơn hàng">
                    <option value="" @selected($status === '')>Tất cả trạng thái</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-toolbar-select">
                <select id="payment_method" name="payment_method" aria-label="Phương thức thanh toán">
                    <option value="" @selected($paymentMethod === '')>Tất cả phương thức</option>
                    <option value="COD" @selected($paymentMethod === 'COD')>COD</option>
                    <option value="QR" @selected($paymentMethod === 'QR')>QR (PayOS)</option>
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-primary">
                    Lọc
                </button>

                @if ($keyword !== '' || $status !== '' || $paymentMethod !== '')
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- =========================================================
         DATA TABLE CARD
    ========================================================== --}}
    <div class="card admin-table-card">

        <div class="admin-table-header">
            <div>
                <h2 class="admin-table-title">
                    Danh sách đơn hàng
                </h2>
                <p class="admin-table-desc">
                    Tổng cộng {{ $orders->total() }} đơn hàng được tìm thấy
                </p>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">Mã đơn</th>
                        <th scope="col">Khách hàng</th>
                        <th scope="col">Tổng tiền</th>
                        <th scope="col">Thanh toán</th>
                        <th scope="col">Trạng thái đơn</th>
                        <th scope="col">Ngày tạo</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            {{-- ORDER CODE --}}
                            <td>
                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="admin-code"
                                    title="Xem chi tiết đơn hàng {{ $order->order_code }}"
                                >
                                    {{ $order->order_code }}
                                </a>
                            </td>

                            {{-- CUSTOMER IDENTIFIER --}}
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <strong>
                                        Customer #{{ $order->user_id }}
                                    </strong>
                                    <span style="font-size: 12px; color: var(--admin-text-muted);">
                                        ID #{{ $order->user_id }}
                                    </span>
                                </div>
                            </td>

                            {{-- TOTAL AMOUNT --}}
                            <td>
                                <strong style="font-size: 14px; color: var(--admin-forest-900); font-variant-numeric: tabular-nums;">
                                    {{ number_format((float) $order->total_amount, 0, ',', '.') }}đ
                                </strong>
                            </td>

                            {{-- PAYMENT METHOD & STATUS --}}
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                    <span class="badge {{ $order->payment_method === 'QR' ? 'badge-method-qr' : 'badge-method-cod' }}">
                                        {{ $order->payment_method ?? '—' }}
                                    </span>

                                    <span class="badge badge-payment-{{ $order->payment_status }}">
                                        {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                                    </span>
                                </div>
                            </td>

                            {{-- ORDER STATUS --}}
                            <td>
                                <span class="badge badge-order-{{ $order->order_status }}">
                                    {{ $statusLabels[$order->order_status] ?? $order->order_status }}
                                </span>
                            </td>

                            {{-- CREATED DATE --}}
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px; font-size: 13px;">
                                    <span style="color: var(--admin-text-primary);">
                                        {{ $order->created_at->format('d/m/Y') }}
                                    </span>
                                    <span style="font-size: 11.5px; color: var(--admin-text-muted);">
                                        {{ $order->created_at->format('H:i') }}
                                    </span>
                                </div>
                            </td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="actions">
                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="btn btn-sm btn-secondary"
                                    >
                                        Chi tiết
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px 20px;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <span style="font-size: 28px;" aria-hidden="true">📦</span>
                                    <strong style="font-size: 15px; color: var(--admin-text-primary);">
                                        Không tìm thấy đơn hàng phù hợp
                                    </strong>
                                    <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0;">
                                        Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- =====================================================
             PAGINATION (REUSE PHASE A3.1)
        ====================================================== --}}
        @include('admin.partials.pagination', ['paginator' => $orders])

    </div>

</div>

@endsection