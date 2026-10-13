@extends('layouts.admin')

@section('title', 'Quản lý khách hàng - MiniGrove')
@section('page-title', 'Khách hàng')

@section('content')
<div class="admin-list-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">
                Khách hàng
            </h1>
            <p class="admin-index-subtitle">
                Quản lý tài khoản Customer theo mã định danh, trạng thái hoạt động và lịch sử giao dịch.
            </p>
        </div>
    </div>


    {{-- =========================================================
         QUICK STATS SUMMARY
    ========================================================== --}}
    <div class="admin-stat-summary">
        <div class="admin-stat-summary-card">
            <div class="admin-stat-summary-title">Tổng Customer</div>
            <div class="admin-stat-summary-val">{{ number_format($totalCustomers) }}</div>
        </div>

        <div class="admin-stat-summary-card is-running">
            <div class="admin-stat-summary-title">Active</div>
            <div class="admin-stat-summary-val">{{ number_format($activeCustomers) }}</div>
        </div>

        <div class="admin-stat-summary-card {{ $inactiveCustomers > 0 ? 'is-warning' : '' }}">
            <div class="admin-stat-summary-title">Inactive</div>
            <div class="admin-stat-summary-val">{{ number_format($inactiveCustomers) }}</div>
        </div>

        <div class="admin-stat-summary-card">
            <div class="admin-stat-summary-title">Mới trong tháng</div>
            <div class="admin-stat-summary-val">{{ number_format($newCustomersThisMonth) }}</div>
        </div>
    </div>


    {{-- =========================================================
         PRIVACY NOTICE
    ========================================================== --}}
    <div class="admin-privacy-note">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        <span>
            <strong>Chính sách bảo mật:</strong> Khu vực quản trị chỉ định danh qua Customer ID. Thông tin nhận dạng cá nhân không được hiển thị để đảm bảo tính riêng tư.
        </span>
    </div>


    {{-- =========================================================
         TOOLBAR / FILTER
    ========================================================== --}}
    <div class="admin-toolbar">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="admin-toolbar-form">
            <div class="admin-toolbar-search">
                <span class="admin-toolbar-search-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input
                    type="text"
                    name="keyword"
                    value="{{ $keyword }}"
                    placeholder="Nhập Customer ID (ví dụ: 5)..."
                    inputmode="numeric"
                    autocomplete="off"
                    aria-label="Tìm kiếm theo Customer ID"
                >
            </div>

            <div class="admin-toolbar-select">
                <select name="status" aria-label="Lọc theo trạng thái">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-primary">
                    Lọc
                </button>

                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
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
                    Danh sách khách hàng
                </h2>
                <p class="admin-table-desc">
                    Tổng cộng {{ $customers->total() }} tài khoản
                </p>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Customer ID</th>
                        <th scope="col" style="text-align: center;">Đơn hàng</th>
                        <th scope="col" style="text-align: right;">Tổng chi tiêu</th>
                        <th scope="col">Ngày tham gia</th>
                        <th scope="col" style="text-align: center;">Trạng thái</th>
                        <th scope="col" style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        @php
                            $completedSpending = (float) ($customer->completed_spending ?? 0);
                            $refundedAmount = (float) ($customer->refunded_amount ?? 0);
                            $actualSpending = max(0, $completedSpending - $refundedAmount);
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span class="admin-privacy-avatar" aria-hidden="true">
                                        C
                                    </span>
                                    <div>
                                        <strong class="admin-text-strong">
                                            Customer #{{ $customer->id }}
                                        </strong>
                                        <div style="font-size: 11.5px; color: var(--admin-text-muted);">
                                            Mã định danh #{{ $customer->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td style="text-align: center;">
                                <span class="badge badge-muted">
                                    {{ $customer->orders_count }} đơn
                                </span>
                            </td>

                            <td style="text-align: right;">
                                <span class="admin-cell-number">
                                    {{ number_format($actualSpending, 0, ',', '.') }}đ
                                </span>
                            </td>

                            <td>
                                <span style="font-size: 12.5px; color: var(--admin-text-secondary);">
                                    {{ optional($customer->created_at)->format('d/m/Y H:i') ?: '—' }}
                                </span>
                            </td>

                            <td style="text-align: center;">
                                @if ($customer->is_active)
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
                                        href="{{ route('admin.customers.show', $customer->id) }}"
                                        class="btn btn-sm btn-secondary"
                                        title="Xem chi tiết khách hàng"
                                    >
                                        Chi tiết
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.customers.toggle-status', $customer->id) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-sm {{ $customer->is_active ? 'btn-status' : 'btn-primary' }}"
                                            onclick="return confirm('{{ $customer->is_active ? 'Bạn có chắc muốn khóa Customer #' . $customer->id . '?' : 'Bạn có chắc muốn kích hoạt Customer #' . $customer->id . '?' }}');"
                                            title="Thay đổi trạng thái tài khoản"
                                        >
                                            {{ $customer->is_active ? 'Khóa' : 'Kích hoạt' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">
                                <p>Không tìm thấy Customer phù hợp.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('admin.partials.pagination', ['paginator' => $customers])

    </div>

</div>
@endsection