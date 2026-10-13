@extends('layouts.admin')

@section('title', 'Trả hàng / Hoàn tiền - MiniGrove')
@section('page-title', 'Trả hàng / Hoàn tiền')

@section('content')

@php
    $statusLabels = [
        'pending'   => 'Chờ xử lý',
        'approved'  => 'Đã chấp nhận',
        'returning' => 'Đang trả hàng',
        'received'  => 'Đã nhận hàng',
        'refunded'  => 'Đã hoàn tiền',
        'rejected'  => 'Đã từ chối',
    ];

    $reasonLabels = [
        'damaged'          => 'Sản phẩm bị hư hỏng',
        'wrong_item'       => 'Giao sai sản phẩm',
        'not_as_described' => 'Không đúng mô tả',
        'quality_issue'    => 'Vấn đề chất lượng',
        'other'            => 'Lý do khác',
    ];
@endphp

<div class="admin-list-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">
                Trả hàng / Hoàn tiền
            </h1>
            <p class="admin-index-subtitle">
                Theo dõi và xử lý các yêu cầu trả hàng của khách hàng MiniGrove.
            </p>
        </div>

        <div class="admin-index-actions">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                Xem đơn hàng
            </a>
        </div>
    </div>

    {{-- =========================================================
         STATUS TABS NAVIGATION
    ========================================================== --}}
    <nav class="admin-status-tabs" aria-label="Lọc theo trạng thái yêu cầu trả hàng">
        <a
            href="{{ route('admin.returns.index', array_filter(['keyword' => $keyword])) }}"
            class="admin-status-tab {{ !$status ? 'active' : '' }}"
        >
            Tất cả
        </a>

        @foreach ($statusLabels as $value => $label)
            <a
                href="{{ route('admin.returns.index', array_filter(['status' => $value, 'keyword' => $keyword])) }}"
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
            action="{{ route('admin.returns.index') }}"
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
                    placeholder="Mã yêu cầu, mã đơn hoặc Customer ID..."
                    aria-label="Tìm kiếm yêu cầu trả hàng"
                >
            </div>

            <div class="admin-toolbar-select">
                <select id="status" name="status" aria-label="Trạng thái yêu cầu">
                    <option value="" @selected($status === '')>Tất cả trạng thái</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-primary">
                    Lọc
                </button>

                @if ($keyword !== '' || $status !== '')
                    <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary">
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
                    Danh sách yêu cầu trả hàng
                </h2>
                <p class="admin-table-desc">
                    Tổng cộng {{ $returnRequests->total() }} yêu cầu được ghi nhận
                </p>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">Mã yêu cầu</th>
                        <th scope="col">Đơn hàng</th>
                        <th scope="col">Khách hàng</th>
                        <th scope="col">Sản phẩm trả</th>
                        <th scope="col">Lý do</th>
                        <th scope="col">Dự kiến hoàn</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Ngày gửi</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($returnRequests as $returnReq)
                        <tr>
                            {{-- RETURN CODE --}}
                            <td>
                                <a
                                    href="{{ route('admin.returns.show', $returnReq) }}"
                                    class="admin-code"
                                    title="Xem chi tiết yêu cầu {{ $returnReq->return_code }}"
                                >
                                    {{ $returnReq->return_code }}
                                </a>
                            </td>

                            {{-- ORDER --}}
                            <td>
                                @if ($returnReq->order)
                                    <div style="display: flex; flex-direction: column; gap: 2px;">
                                        <a
                                            href="{{ route('admin.orders.show', $returnReq->order) }}"
                                            class="admin-code"
                                            style="font-size: 12px;"
                                        >
                                            {{ $returnReq->order->order_code }}
                                        </a>
                                        <span style="font-size: 11.5px; color: var(--admin-text-muted);">
                                            {{ $returnReq->order->order_status }}
                                        </span>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>

                            {{-- CUSTOMER IDENTIFIER --}}
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <strong>
                                        Customer #{{ $returnReq->user_id }}
                                    </strong>
                                    <span style="font-size: 12px; color: var(--admin-text-muted);">
                                        ID #{{ $returnReq->user_id }}
                                    </span>
                                </div>
                            </td>

                            {{-- ITEM COUNT --}}
                            <td>
                                <span style="font-size: 13px; color: var(--admin-text-primary);">
                                    {{ $returnReq->items_count }} dòng sản phẩm
                                </span>
                            </td>

                            {{-- REASON --}}
                            <td>
                                <span style="font-size: 13px; color: var(--admin-text-secondary); max-width: 180px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $reasonLabels[$returnReq->reason] ?? $returnReq->reason }}">
                                    {{ $reasonLabels[$returnReq->reason] ?? $returnReq->reason }}
                                </span>
                            </td>

                            {{-- REFUND AMOUNT --}}
                            <td>
                                <strong style="font-size: 14px; color: var(--admin-forest-900); font-variant-numeric: tabular-nums;">
                                    {{ number_format((float) $returnReq->refund_amount, 0, ',', '.') }}đ
                                </strong>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span class="badge badge-order-{{ $returnReq->status }}">
                                    {{ $statusLabels[$returnReq->status] ?? $returnReq->status }}
                                </span>
                            </td>

                            {{-- CREATED AT --}}
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px; font-size: 13px;">
                                    <span>
                                        {{ $returnReq->created_at->format('d/m/Y') }}
                                    </span>
                                    <span style="font-size: 11.5px; color: var(--admin-text-muted);">
                                        {{ $returnReq->created_at->format('H:i') }}
                                    </span>
                                </div>
                            </td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="actions">
                                    <a
                                        href="{{ route('admin.returns.show', $returnReq) }}"
                                        class="btn btn-sm btn-secondary"
                                    >
                                        Chi tiết
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 48px 20px;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <span style="font-size: 28px;" aria-hidden="true">↩️</span>
                                    <strong style="font-size: 15px; color: var(--admin-text-primary);">
                                        Không tìm thấy yêu cầu trả hàng phù hợp
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
        @include('admin.partials.pagination', ['paginator' => $returnRequests])
    </div>

</div>

@endsection