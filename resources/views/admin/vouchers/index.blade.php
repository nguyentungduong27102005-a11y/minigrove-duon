@extends('layouts.admin')

@section('title', 'Quản lý Voucher - MiniGrove')
@section('page-title', 'Voucher')

@section('content')
<div class="admin-list-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">
                Quản lý Voucher
            </h1>
            <p class="admin-index-subtitle">
                Thiết lập mã khuyến mại, chiết khấu và theo dõi hạn sử dụng voucher toàn hệ thống.
            </p>
        </div>

        <div class="admin-index-actions">
            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Thêm Voucher</span>
            </a>
        </div>
    </div>


    {{-- =========================================================
         QUICK STATS SUMMARY
    ========================================================== --}}
    <div class="admin-stat-summary">
        <div class="admin-stat-summary-card">
            <div class="admin-stat-summary-title">Tổng Voucher</div>
            <div class="admin-stat-summary-val">{{ number_format($totalCount) }}</div>
        </div>

        <div class="admin-stat-summary-card">
            <div class="admin-stat-summary-title">Active</div>
            <div class="admin-stat-summary-val">{{ number_format($activeCount) }}</div>
        </div>

        <div class="admin-stat-summary-card is-running">
            <div class="admin-stat-summary-title">Đang áp dụng</div>
            <div class="admin-stat-summary-val">{{ number_format($runningCount) }}</div>
        </div>

        <div class="admin-stat-summary-card {{ $expiredCount > 0 ? 'is-danger' : '' }}">
            <div class="admin-stat-summary-title">Hết hạn</div>
            <div class="admin-stat-summary-val">{{ number_format($expiredCount) }}</div>
        </div>
    </div>


    {{-- =========================================================
         TOOLBAR / FILTER
    ========================================================== --}}
    <div class="admin-toolbar">
        <form action="{{ route('admin.vouchers.index') }}" method="GET" class="admin-toolbar-form">
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
                    placeholder="Nhập mã Voucher..."
                    aria-label="Tìm kiếm voucher"
                >
            </div>

            <div class="admin-toolbar-select">
                <select id="active" name="active" aria-label="Lọc trạng thái">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" @selected($active === '1')>Active</option>
                    <option value="0" @selected($active === '0')>Inactive</option>
                </select>
            </div>

            <div class="admin-toolbar-select">
                <select id="period" name="period" aria-label="Lọc thời gian">
                    <option value="">Tất cả thời gian</option>
                    <option value="upcoming" @selected($period === 'upcoming')>Sắp diễn ra</option>
                    <option value="running" @selected($period === 'running')>Đang áp dụng</option>
                    <option value="expired" @selected($period === 'expired')>Hết hạn</option>
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-primary">
                    Lọc
                </button>

                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                    Đặt lại
                </a>
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
                    Danh sách Voucher
                </h2>
                <p class="admin-table-desc">
                    Tổng cộng {{ $vouchers->total() }} Voucher
                </p>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Mã Voucher</th>
                        <th scope="col">Ưu đãi</th>
                        <th scope="col">Đơn tối thiểu</th>
                        <th scope="col">Hiệu lực</th>
                        <th scope="col" style="text-align: center;">Đã dùng / Giới hạn</th>
                        <th scope="col" style="text-align: center;">Mỗi khách</th>
                        <th scope="col" style="text-align: center;">Tình trạng</th>
                        <th scope="col" style="text-align: center;">Trạng thái</th>
                        <th scope="col" style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vouchers as $voucher)
                        @php
                            $now = now();

                            if ($voucher->start_at->gt($now)) {
                                $periodLabel = 'Sắp diễn ra';
                                $periodBadge = 'badge-warning';
                            } elseif ($voucher->end_at->lt($now)) {
                                $periodLabel = 'Hết hạn';
                                $periodBadge = 'badge-muted';
                            } else {
                                $periodLabel = 'Đang áp dụng';
                                $periodBadge = 'badge-success';
                            }

                            if ($voucher->usage_limit !== null && $voucher->usage_count >= $voucher->usage_limit) {
                                $periodLabel = 'Hết lượt';
                                $periodBadge = 'badge-danger';
                            }
                        @endphp
                        <tr>
                            <td>
                                <strong class="admin-text-mono" style="font-size: 13.5px;">
                                    {{ $voucher->code }}
                                </strong>
                                <div style="display: flex; gap: 4px; margin-top: 4px; flex-wrap: wrap;">
                                    <span class="badge {{ $voucher->isShippingScope() ? 'badge-warning' : 'badge-primary' }}" style="font-size: 10px; padding: 1px 6px;">
                                        {{ $voucher->isShippingScope() ? 'Vận chuyển' : 'Đơn hàng' }}
                                    </span>
                                    @if (!$voucher->isPublic())
                                        <span class="badge badge-muted" style="font-size: 10px; padding: 1px 6px;">
                                            Riêng tư
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if ($voucher->discount_type === 'percentage')
                                    <div>
                                        <strong class="admin-price-sale">
                                            {{ number_format($voucher->discount_value, 0, ',', '.') }}%
                                        </strong>
                                    </div>
                                    @if ($voucher->max_discount_amount !== null)
                                        <div style="font-size: 11.5px; color: var(--admin-text-muted);">
                                            Tối đa {{ number_format($voucher->max_discount_amount, 0, ',', '.') }}đ
                                        </div>
                                    @endif
                                @else
                                    <strong class="admin-price-sale">
                                        {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                    </strong>
                                @endif
                            </td>

                            <td>
                                <span class="admin-cell-number">
                                    {{ number_format($voucher->min_order_amount, 0, ',', '.') }}đ
                                </span>
                            </td>

                            <td>
                                <div style="font-size: 12.5px; line-height: 1.4; color: var(--admin-text-secondary);">
                                    <div>{{ $voucher->start_at->format('d/m/Y H:i') }}</div>
                                    <div style="color: var(--admin-text-muted); font-size: 11px;">đến {{ $voucher->end_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </td>

                            <td style="text-align: center;">
                                <span class="admin-cell-number">{{ $voucher->usage_count }}</span>
                                <span style="color: var(--admin-text-muted); font-size: 12px;">
                                    / {{ $voucher->usage_limit ?? '∞' }}
                                </span>
                            </td>

                            <td style="text-align: center;">
                                <span style="font-size: 12.5px; color: var(--admin-text-secondary);">
                                    {{ $voucher->per_user_limit ?? 'Không giới hạn' }}
                                </span>
                            </td>

                            <td style="text-align: center;">
                                <span class="badge {{ $periodBadge }}">
                                    {{ $periodLabel }}
                                </span>
                            </td>

                            <td style="text-align: center;">
                                @if ($voucher->is_active)
                                    <span class="badge badge-success">
                                        Active
                                    </span>
                                @else
                                    <span class="badge badge-muted">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td style="text-align: right;">
                                <div class="admin-table-actions">
                                    <a
                                        href="{{ route('admin.vouchers.edit', $voucher) }}"
                                        class="btn btn-sm btn-secondary"
                                        title="Chỉnh sửa voucher"
                                    >
                                        Sửa
                                    </a>

                                    <form
                                        action="{{ route('admin.vouchers.toggle-status', $voucher) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-status"
                                            title="Thay đổi trạng thái"
                                        >
                                            {{ $voucher->is_active ? 'Ẩn' : 'Hiện' }}
                                        </button>
                                    </form>

                                    <a
                                        href="{{ route('admin.vouchers.history', $voucher) }}"
                                        class="btn btn-sm btn-secondary"
                                        title="Lịch sử sử dụng voucher"
                                    >
                                        Lịch sử
                                    </a>

                                    <form
                                        action="{{ route('admin.vouchers.destroy', $voucher) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bạn chắc chắn muốn xóa Voucher này?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Xóa voucher"
                                        >
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty">
                                <p>Không tìm thấy Voucher phù hợp.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('admin.partials.pagination', ['paginator' => $vouchers])

    </div>

</div>
@endsection