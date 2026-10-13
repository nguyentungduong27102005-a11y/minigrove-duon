@extends('layouts.admin')

@section('title', 'Quản lý thanh toán - MiniGrove')
@section('page-title', 'Thanh toán')

@section('content')

@php
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
                Thanh toán
            </h1>
            <p class="admin-index-subtitle">
                Theo dõi giao dịch và quản lý trạng thái phương thức thanh toán MiniGrove.
            </p>
        </div>
    </div>

    {{-- =========================================================
         QUICK STATS SUMMARY
    ========================================================== --}}
    <div class="admin-stat-summary" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="admin-stat-summary-card">
            <div class="admin-stat-summary-title">Tổng giao dịch</div>
            <div class="admin-stat-summary-val">{{ number_format($totalCount) }}</div>
        </div>

        <div class="admin-stat-summary-card {{ $pendingCount > 0 ? 'is-warning' : '' }}">
            <div class="admin-stat-summary-title">Chờ thanh toán</div>
            <div class="admin-stat-summary-val">{{ number_format($pendingCount) }}</div>
        </div>

        <div class="admin-stat-summary-card">
            <div class="admin-stat-summary-title">Đã thanh toán</div>
            <div class="admin-stat-summary-val" style="color: var(--admin-forest-900);">{{ number_format($paidCount) }}</div>
        </div>

        <div class="admin-stat-summary-card {{ $failedCount > 0 ? 'is-danger' : '' }}">
            <div class="admin-stat-summary-title">Thất bại</div>
            <div class="admin-stat-summary-val">{{ number_format($failedCount) }}</div>
        </div>

        <div class="admin-stat-summary-card">
            <div class="admin-stat-summary-title">Đã hoàn tiền</div>
            <div class="admin-stat-summary-val">{{ number_format($refundedCount) }}</div>
        </div>
    </div>

    {{-- =========================================================
         PAYMENT METHODS CONFIGURATION SECTION
    ========================================================== --}}
    <div style="margin-bottom: 32px;">
        <div style="margin-bottom: 16px;">
            <h2 style="font-size: 18px; margin: 0 0 4px;">
                Cấu hình phương thức thanh toán
            </h2>
            <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0;">
                Bật hoặc tắt COD, QR qua PayOS và OnePay. Khóa API và bảo mật được lưu an toàn trong hệ thống.
            </p>
        </div>

        <div class="admin-payment-grid">
            {{-- COD --}}
            @php $codSetting = $paymentSettings->get('COD'); @endphp
            @if ($codSetting)
                <form
                    method="POST"
                    action="{{ route('admin.payments.settings.update', $codSetting) }}"
                    class="admin-payment-card"
                >
                    @csrf
                    @method('PATCH')

                    <div class="admin-payment-card-top">
                        <div>
                            <span class="badge badge-method-cod" style="margin-bottom: 6px;">
                                COD
                            </span>
                            <h3 style="font-size: 16px; margin: 0;">
                                Thanh toán khi nhận hàng
                            </h3>
                        </div>
                        <span class="badge {{ $codSetting->is_enabled ? 'badge-success' : 'badge-muted' }}">
                            {{ $codSetting->is_enabled ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                    </div>

                    <div>
                        <label for="display_name_cod" style="font-size: 13px; margin-bottom: 5px;">
                            Tên hiển thị
                        </label>
                        <input
                            type="text"
                            id="display_name_cod"
                            name="display_name"
                            maxlength="100"
                            value="{{ old('display_name', $codSetting->display_name) }}"
                            required
                        >
                    </div>

                    <div class="admin-payment-toggle-box">
                        <div class="admin-payment-toggle-label">
                            <strong>Cho phép sử dụng COD</strong>
                            <small>Khách hàng có thể chọn COD khi thanh toán</small>
                        </div>
                        <label class="admin-switch">
                            <input type="hidden" name="is_enabled" value="0">
                            <input
                                type="checkbox"
                                name="is_enabled"
                                value="1"
                                @checked($codSetting->is_enabled)
                                aria-label="Bật/tắt thanh toán khi nhận hàng COD"
                            >
                            <span class="admin-switch-slider"></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width: 100%;">
                        Lưu thiết lập COD
                    </button>
                </form>
            @endif

            {{-- QR (PAYOS) --}}
            @php $qrSetting = $paymentSettings->get('QR'); @endphp
            @if ($qrSetting)
                <form
                    method="POST"
                    action="{{ route('admin.payments.settings.update', $qrSetting) }}"
                    class="admin-payment-card"
                >
                    @csrf
                    @method('PATCH')

                    <div class="admin-payment-card-top">
                        <div>
                            <span class="badge badge-method-qr" style="margin-bottom: 6px;">
                                QR PayOS
                            </span>
                            <h3 style="font-size: 16px; margin: 0;">
                                Thanh toán QR tự động
                            </h3>
                        </div>
                        <span class="badge {{ $qrSetting->is_enabled ? 'badge-success' : 'badge-muted' }}">
                            {{ $qrSetting->is_enabled ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                    </div>

                    <div>
                        <label for="display_name_qr" style="font-size: 13px; margin-bottom: 5px;">
                            Tên hiển thị
                        </label>
                        <input
                            type="text"
                            id="display_name_qr"
                            name="display_name"
                            maxlength="100"
                            value="{{ old('display_name', $qrSetting->display_name) }}"
                            required
                        >
                    </div>

                    <p style="font-size: 12px; color: var(--admin-text-secondary); line-height: 1.45; margin: 0; background: var(--admin-canvas); padding: 8px 12px; border-radius: var(--admin-radius-sm); border: 1px solid var(--admin-border);">
                        PayOS tạo mã QR động và tự động cập nhật trạng thái qua Webhook. Client ID, API Key và Checksum Key được cấu hình bảo mật ở cấp môi trường.
                    </p>

                    <div class="admin-payment-toggle-box">
                        <div class="admin-payment-toggle-label">
                            <strong>Cho phép thanh toán QR</strong>
                            <small>Tự động chuyển cổng PayOS khi thanh toán</small>
                        </div>
                        <label class="admin-switch">
                            <input type="hidden" name="is_enabled" value="0">
                            <input
                                type="checkbox"
                                name="is_enabled"
                                value="1"
                                @checked($qrSetting->is_enabled)
                                aria-label="Bật/tắt thanh toán QR qua PayOS"
                            >
                            <span class="admin-switch-slider"></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width: 100%;">
                        Lưu thiết lập PayOS
                    </button>
                </form>
            @endif

            {{-- ONEPAY --}}
            @php $onePaySetting = $paymentSettings->get('ONEPAY'); @endphp
            @if ($onePaySetting)
                <form
                    method="POST"
                    action="{{ route('admin.payments.settings.update', $onePaySetting) }}"
                    class="admin-payment-card"
                >
                    @csrf
                    @method('PATCH')

                    <div class="admin-payment-card-top">
                        <div>
                            <span class="badge badge-method-onepay" style="margin-bottom: 6px;">
                                OnePay
                            </span>
                            <h3 style="font-size: 16px; margin: 0;">
                                Cổng thanh toán OnePay
                            </h3>
                        </div>
                        <span class="badge {{ $onePaySetting->is_enabled ? 'badge-success' : 'badge-muted' }}">
                            {{ $onePaySetting->is_enabled ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                    </div>

                    <div>
                        <label for="display_name_onepay" style="font-size: 13px; margin-bottom: 5px;">
                            Tên hiển thị
                        </label>
                        <input
                            type="text"
                            id="display_name_onepay"
                            name="display_name"
                            maxlength="100"
                            value="{{ old('display_name', $onePaySetting->display_name) }}"
                            required
                        >
                    </div>

                    <p style="font-size: 12px; color: var(--admin-text-secondary); line-height: 1.45; margin: 0; background: var(--admin-canvas); padding: 8px 12px; border-radius: var(--admin-radius-sm); border: 1px solid var(--admin-border);">
                        Cổng thanh toán OnePay hỗ trợ thẻ quốc tế và nội địa. Thông số kết nối được lưu trữ bảo mật.
                    </p>

                    <div class="admin-payment-toggle-box">
                        <div class="admin-payment-toggle-label">
                            <strong>Cho phép thanh toán OnePay</strong>
                            <small>Khách hàng được chuyển hướng sang OnePay</small>
                        </div>
                        <label class="admin-switch">
                            <input type="hidden" name="is_enabled" value="0">
                            <input
                                type="checkbox"
                                name="is_enabled"
                                value="1"
                                @checked($onePaySetting->is_enabled)
                                aria-label="Bật/tắt thanh toán qua cổng OnePay"
                            >
                            <span class="admin-switch-slider"></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width: 100%;">
                        Lưu thiết lập OnePay
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- =========================================================
         TRANSACTION HISTORY / TOOLBAR & TABLE
    ========================================================== --}}
    <div style="margin-bottom: 16px;">
        <h2 style="font-size: 18px; margin: 0 0 4px;">
            Lịch sử giao dịch
        </h2>
        <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0;">
            Toàn bộ giao dịch phát sinh từ các đơn hàng của khách hàng.
        </p>
    </div>

    {{-- TOOLBAR / FILTER --}}
    <div class="admin-toolbar">
        <form
            action="{{ route('admin.payments.index') }}"
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
                    aria-label="Tìm kiếm giao dịch"
                >
            </div>

            <div class="admin-toolbar-select">
                <select id="method" name="method" aria-label="Phương thức thanh toán">
                    <option value="" @selected($method === '')>Tất cả phương thức</option>
                    <option value="COD" @selected($method === 'COD')>COD</option>
                    <option value="QR" @selected($method === 'QR')>QR (PayOS)</option>
                    <option value="ONEPAY" @selected($method === 'ONEPAY')>OnePay</option>
                </select>
            </div>

            <div class="admin-toolbar-select">
                <select id="status" name="status" aria-label="Trạng thái thanh toán">
                    <option value="" @selected($status === '')>Tất cả trạng thái</option>
                    <option value="pending" @selected($status === 'pending')>Chờ thanh toán</option>
                    <option value="paid" @selected($status === 'paid')>Đã thanh toán</option>
                    <option value="failed" @selected($status === 'failed')>Thất bại</option>
                    <option value="refunded" @selected($status === 'refunded')>Đã hoàn tiền</option>
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-primary">
                    Lọc
                </button>

                @if ($keyword !== '' || $method !== '' || $status !== '')
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- DATA TABLE CARD --}}
    <div class="card admin-table-card">
        <div class="admin-table-header">
            <div>
                <h3 class="admin-table-title" style="font-size: 16px;">
                    Danh sách giao dịch
                </h3>
                <p class="admin-table-desc">
                    Tổng cộng {{ $payments->total() }} giao dịch được ghi nhận
                </p>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">Mã đơn</th>
                        <th scope="col">Khách hàng</th>
                        <th scope="col">Phương thức</th>
                        <th scope="col">Số tiền</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Người xác nhận</th>
                        <th scope="col">Thời gian</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            {{-- ORDER --}}
                            <td>
                                @if ($payment->order)
                                    <a
                                        href="{{ route('admin.orders.show', $payment->order) }}"
                                        class="admin-code"
                                    >
                                        {{ $payment->order->order_code }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>

                            {{-- CUSTOMER IDENTIFIER --}}
                            <td>
                                @if ($payment->order)
                                    <div style="display: flex; flex-direction: column; gap: 2px;">
                                        <strong>
                                            Customer #{{ $payment->order->user_id }}
                                        </strong>
                                        <span style="font-size: 12px; color: var(--admin-text-muted);">
                                            ID #{{ $payment->order->user_id }}
                                        </span>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>

                            {{-- METHOD --}}
                            <td>
                                <span class="badge {{ $payment->payment_method === 'QR' ? 'badge-method-qr' : ($payment->payment_method === 'ONEPAY' ? 'badge-method-onepay' : 'badge-method-cod') }}">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>

                            {{-- AMOUNT --}}
                            <td>
                                <strong style="font-size: 14px; color: var(--admin-forest-900); font-variant-numeric: tabular-nums;">
                                    {{ number_format((float) $payment->amount, 0, ',', '.') }}đ
                                </strong>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span class="badge badge-payment-{{ $payment->status }}">
                                    {{ $paymentLabels[$payment->status] ?? $payment->status }}
                                </span>
                            </td>

                            {{-- CONFIRMER --}}
                            <td>
                                <span style="font-size: 13px; color: var(--admin-text-secondary);">
                                    {{ $payment->confirmer?->name ?? '—' }}
                                </span>
                            </td>

                            {{-- TIME --}}
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px; font-size: 13px;">
                                    <span>
                                        {{ optional($payment->created_at)->format('d/m/Y H:i') }}
                                    </span>
                                    @if ($payment->paid_at)
                                        <span style="font-size: 11.5px; color: var(--admin-forest-800);">
                                            Trả: {{ $payment->paid_at->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="actions">
                                    @if ($payment->order)
                                        <a
                                            href="{{ route('admin.orders.show', $payment->order) }}"
                                            class="btn btn-sm btn-secondary"
                                        >
                                            Xem đơn
                                        </a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px 20px;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <span style="font-size: 28px;" aria-hidden="true">💳</span>
                                    <strong style="font-size: 15px; color: var(--admin-text-primary);">
                                        Chưa có giao dịch phù hợp
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

        {{-- PAGINATION --}}
        @include('admin.partials.pagination', ['paginator' => $payments])
    </div>

</div>

@endsection