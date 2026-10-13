@extends('layouts.admin')

@section('title', 'Dashboard - MiniGrove Admin')

@section('page-title', 'Bảng điều khiển')

@section('content')
<div class="dashboard-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="dashboard-header">
        <div class="dashboard-header-main">
            <h1 class="dashboard-title">
                Tổng quan hoạt động MiniGrove
            </h1>
            <p class="dashboard-subtitle">
                Theo dõi tình trạng vận hành, đơn hàng phát sinh và hiệu quả kinh doanh của cửa hàng.
            </p>
        </div>

        <div class="dashboard-header-actions">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" x2="12" y1="20" y2="10"/>
                    <line x1="18" x2="18" y1="20" y2="4"/>
                    <line x1="6" x2="6" y1="20" y2="16"/>
                </svg>
                <span>Báo cáo & thống kê</span>
            </a>
        </div>
    </div>


    {{-- =========================================================
         KPI OVERVIEW CARDS (6 REAL METRICS)
    ========================================================== --}}
    <div class="dashboard-kpi-grid">

        {{-- 1. DOANH THU HOÀN THÀNH --}}
        <div class="kpi-card kpi-card--forest">
            <div class="kpi-card-header">
                <span class="kpi-label">Doanh thu hoàn thành</span>
                <span class="kpi-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" x2="12" y1="2" y2="22"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </span>
            </div>
            <div class="kpi-value">
                {{ number_format($completedRevenue, 0, ',', '.') }}<span class="kpi-currency">đ</span>
            </div>
            <div class="kpi-helper">
                Đơn hàng đã giao thành công
            </div>
        </div>

        {{-- 2. ĐƠN ĐANG XỬ LÝ --}}
        <div class="kpi-card kpi-card--warning">
            <div class="kpi-card-header">
                <span class="kpi-label">Đơn đang xử lý</span>
                <span class="kpi-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                </span>
            </div>
            <div class="kpi-value">
                {{ number_format($processingOrderCount) }}
            </div>
            <div class="kpi-helper">
                Cần đóng gói & điều phối giao hàng
            </div>
        </div>

        {{-- 3. HOÀN TRẢ CHỜ DUYỆT --}}
        <div class="kpi-card {{ $pendingReturnCount > 0 ? 'kpi-card--danger' : 'kpi-card--neutral' }}">
            <div class="kpi-card-header">
                <span class="kpi-label">Yêu cầu hoàn trả</span>
                <span class="kpi-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                </span>
            </div>
            <div class="kpi-value">
                {{ number_format($pendingReturnCount) }}
            </div>
            <div class="kpi-helper">
                {{ $pendingReturnCount > 0 ? 'Đang chờ quản trị viên xử lý' : 'Không có yêu cầu tồn đọng' }}
            </div>
        </div>

        {{-- 4. TỔNG ĐƠN HÀNG --}}
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-label">Tổng đơn hàng</span>
                <span class="kpi-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </span>
            </div>
            <div class="kpi-value">
                {{ number_format($orderCount) }}
            </div>
            <div class="kpi-helper">
                Toàn bộ đơn phát sinh từ trước tới nay
            </div>
        </div>

        {{-- 5. KHÁCH HÀNG --}}
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-label">Khách hàng</span>
                <span class="kpi-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>
            </div>
            <div class="kpi-value">
                {{ number_format($customerCount) }}
            </div>
            <div class="kpi-helper">
                Tài khoản khách hàng đã đăng ký
            </div>
        </div>

        {{-- 6. SẢN PHẨM & DANH MỤC --}}
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-label">Sản phẩm & Cây cảnh</span>
                <span class="kpi-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7.5 4.27 9 5.15"/>
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                        <path d="m3.3 7 8.7 5 8.7-5"/>
                        <path d="M12 22V12"/>
                    </svg>
                </span>
            </div>
            <div class="kpi-value">
                {{ number_format($productCount) }}
            </div>
            <div class="kpi-helper">
                Phân bổ trong {{ $categoryCount }} danh mục
            </div>
        </div>

    </div>


    {{-- =========================================================
         RECENT ORDERS SECTION (FULL WIDTH)
    ========================================================== --}}
    <div class="card dashboard-card">

        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">
                    Đơn hàng gần nhất
                </h2>
                <p class="dashboard-card-desc">
                    5 đơn hàng phát sinh mới nhất cần theo dõi và xử lý
                </p>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-secondary">
                <span>Xem tất cả đơn hàng</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>

        <div class="table-wrapper">
            <table class="dashboard-orders-table">
                <thead>
                    <tr>
                        <th scope="col">Mã đơn</th>
                        <th scope="col">Khách hàng</th>
                        <th scope="col">Tổng tiền</th>
                        <th scope="col">Thanh toán</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Thời gian</th>
                        <th scope="col" style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="dashboard-order-code">
                                    #{{ $order->order_code }}
                                </a>
                            </td>
                            <td>
                                <div class="dashboard-customer-info">
                                    <span class="dashboard-customer-name">
                                        {{ $order->customer_name ?: ('Khách #' . $order->user_id) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="dashboard-amount">
                                    {{ number_format($order->total_amount, 0, ',', '.') }} đ
                                </span>
                            </td>
                            <td>
                                @php
                                    $paymentClass = match(strtolower($order->payment_status ?? '')) {
                                        'paid', 'completed' => 'badge-success',
                                        'pending', 'unpaid' => 'badge-warning',
                                        'failed', 'cancelled' => 'badge-danger',
                                        default => 'badge-muted'
                                    };
                                @endphp
                                <span class="badge {{ $paymentClass }}">
                                    {{ $order->payment_status ?: 'Chưa rõ' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match(strtolower($order->order_status ?? '')) {
                                        'completed' => 'badge-success',
                                        'pending', 'preparing' => 'badge-warning',
                                        'confirmed', 'shipping' => 'badge-info',
                                        'cancelled', 'returned' => 'badge-danger',
                                        default => 'badge-muted'
                                    };
                                    $statusLabel = match(strtolower($order->order_status ?? '')) {
                                        'pending' => 'Chờ xử lý',
                                        'confirmed' => 'Đã xác nhận',
                                        'preparing' => 'Đang chuẩn bị',
                                        'shipping' => 'Đang giao',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy',
                                        'returned' => 'Đã trả hàng',
                                        default => $order->order_status
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <span class="dashboard-time">
                                    {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '—' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-secondary">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">
                                <div class="dashboard-empty-state">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                                        <path d="M3 6h18"/>
                                        <path d="M16 10a4 4 0 0 1-8 0"/>
                                    </svg>
                                    <p>Chưa có đơn hàng nào được ghi nhận gần đây.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>


    {{-- =========================================================
         OPERATIONAL DUAL SECTION (TOP PRODUCTS & LOW STOCK)
    ========================================================== --}}
    <div class="dashboard-dual-grid">

        {{-- TOP PRODUCTS --}}
        <div class="card dashboard-card">

            <div class="dashboard-card-header">
                <div>
                    <h2 class="dashboard-card-title">
                        Sản phẩm bán chạy
                    </h2>
                    <p class="dashboard-card-desc">
                        Top sản phẩm đạt doanh số cao nhất
                    </p>
                </div>

                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-secondary">
                    <span>Xem sản phẩm</span>
                </a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col" style="text-align: center;">Đã bán</th>
                            <th scope="col" style="text-align: right;">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                            <tr>
                                <td>
                                    <div class="dashboard-top-product-name" title="{{ $product->item_name }}">
                                        {{ $product->item_name }}
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-muted">
                                        {{ number_format($product->total_sold) }} cái
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <span class="dashboard-amount">
                                        {{ number_format($product->total_revenue, 0, ',', '.') }} đ
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="empty">
                                    <p>Chưa có dữ liệu bán hàng ghi nhận.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>


        {{-- LOW STOCK ALERT --}}
        <div class="card dashboard-card">

            <div class="dashboard-card-header">
                <div>
                    <h2 class="dashboard-card-title">
                        Cảnh báo tồn kho
                    </h2>
                    <p class="dashboard-card-desc">
                        Sản phẩm đạt hoặc dưới ngưỡng an toàn
                    </p>
                </div>

                <a href="{{ route('admin.stocks.index') }}" class="btn btn-sm btn-secondary">
                    <span>Quản lý kho</span>
                </a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col">SKU</th>
                            <th scope="col" style="text-align: center;">Tồn kho</th>
                            <th scope="col" style="text-align: right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts as $product)
                            <tr>
                                <td>
                                    <div class="dashboard-low-stock-name" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </div>
                                </td>
                                <td>
                                    <span class="dashboard-sku">
                                        {{ $product->sku ?: '—' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-danger">
                                        {{ $product->stock_quantity }} cái
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.stocks.index') }}" class="btn btn-sm btn-secondary">
                                        Nhập kho
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">
                                    <div class="dashboard-stock-safe">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2F7D5B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                        <p>Kho hàng đang ở trạng thái an toàn. Không có sản phẩm nào chạm ngưỡng báo động.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>
@endsection