@extends('layouts.admin')

@section('title', 'Chi tiết yêu cầu ' . $returnRequest->return_code . ' - MiniGrove')
@section('page-title', 'Chi tiết trả hàng / hoàn tiền')

@section('content')

@php
    $order = $returnRequest->order;
    $payment = $order?->payments?->sortByDesc('id')->first();

    $statusLabels = [
        'pending'   => 'Chờ xử lý',
        'approved'  => 'Đã chấp nhận',
        'rejected'  => 'Đã từ chối',
        'returning' => 'Đang trả hàng',
        'received'  => 'Đã nhận hàng',
        'refunded'  => 'Đã hoàn tiền',
    ];

    $reasonLabels = [
        'damaged'          => 'Sản phẩm bị hư hỏng',
        'wrong_item'       => 'Giao sai sản phẩm',
        'not_as_described' => 'Sản phẩm không đúng mô tả',
        'quality_issue'    => 'Sản phẩm có vấn đề chất lượng',
        'other'            => 'Lý do khác',
    ];

    $paymentLabels = [
        'pending'  => 'Chờ thanh toán',
        'paid'     => 'Đã thanh toán',
        'failed'   => 'Thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];
@endphp

<div class="admin-ops-page">

    {{-- =========================================================
         BACK LINK
    ========================================================== --}}
    <div class="admin-ops-header-bar">
        <a href="{{ route('admin.returns.index') }}" class="admin-ops-back-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Quay lại danh sách trả hàng
        </a>
    </div>

    {{-- =========================================================
         HEADER CARD
    ========================================================== --}}
    <header class="admin-ops-heading-card">
        <div class="admin-ops-heading-content">
            <div>
                <div class="admin-ops-code-row">
                    <span class="admin-code" style="font-size: 15px; padding: 4px 10px;">
                        {{ $returnRequest->return_code }}
                    </span>
                    <span style="font-size: 13px; color: var(--admin-text-muted);">
                        #{{ $returnRequest->id }}
                    </span>
                </div>
                <p class="admin-ops-meta">
                    <span>
                        Gửi lúc: <strong>{{ $returnRequest->created_at->format('d/m/Y H:i') }}</strong>
                    </span>
                    <span>•</span>
                    <span>
                        Khách hàng: <strong>Customer #{{ $returnRequest->user_id }}</strong>
                    </span>
                </p>
            </div>

            <div class="admin-ops-badge-group">
                <span class="badge badge-order-{{ $returnRequest->status }}" style="font-size: 13px; padding: 6px 12px;">
                    Trạng thái: {{ $statusLabels[$returnRequest->status] ?? $returnRequest->status }}
                </span>
            </div>
        </div>
    </header>

    {{-- =========================================================
         2-COLUMN OPERATIONS LAYOUT
    ========================================================== --}}
    <div class="admin-ops-layout">

        {{-- =====================================================
             LEFT COLUMN: MAIN DETAILS
        ====================================================== --}}
        <main class="admin-ops-main">

            {{-- 1. ASSOCIATED ORDER CARD --}}
            <section class="card" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 style="font-size: 16px; margin: 0;">
                        Đơn hàng liên quan
                    </h2>
                    @if ($order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-secondary">
                            Xem chi tiết đơn hàng
                        </a>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 13.5px; background: var(--admin-canvas); padding: 16px; border-radius: var(--admin-radius-md); border: 1px solid var(--admin-border);">
                    <div>
                        <span style="color: var(--admin-text-muted); display: block; font-size: 12px; margin-bottom: 2px;">Mã đơn hàng</span>
                        @if ($order)
                            <a href="{{ route('admin.orders.show', $order) }}" class="admin-code" style="font-size: 13px;">
                                {{ $order->order_code }}
                            </a>
                        @else
                            <strong>—</strong>
                        @endif
                    </div>

                    <div>
                        <span style="color: var(--admin-text-muted); display: block; font-size: 12px; margin-bottom: 2px;">Khách hàng</span>
                        <strong>Customer #{{ $returnRequest->user_id }}</strong>
                    </div>

                    <div>
                        <span style="color: var(--admin-text-muted); display: block; font-size: 12px; margin-bottom: 2px;">Trạng thái Order</span>
                        <strong>{{ $order?->order_status === 'completed' ? 'Hoàn thành' : ($order?->order_status ?? '—') }}</strong>
                    </div>

                    <div>
                        <span style="color: var(--admin-text-muted); display: block; font-size: 12px; margin-bottom: 2px;">Thanh toán</span>
                        <strong>{{ $order?->payment_method ?? '—' }} ({{ $paymentLabels[$order?->payment_status] ?? ($order?->payment_status ?? '—') }})</strong>
                    </div>
                </div>
            </section>

            {{-- 2. RETURN ITEMS --}}
            <section class="card" style="margin-bottom: 0;">
                <h2 style="font-size: 16px; margin: 0 0 16px;">
                    Sản phẩm yêu cầu trả ({{ $returnRequest->items->count() }} mặt hàng)
                </h2>

                <div class="admin-ops-items-list">
                    @foreach ($returnRequest->items as $item)
                        @php
                            $detail = $item->orderDetail;
                            $product = $detail?->product;
                            $primaryImage = $product?->images?->firstWhere('is_primary', true) ?? $product?->images?->first();
                        @endphp

                        <div class="admin-ops-item-row">
                            <div class="admin-ops-item-thumb">
                                @if ($primaryImage)
                                    <img
                                        src="{{ asset('storage/' . $primaryImage->image_path) }}"
                                        alt="{{ $detail?->item_name ?? 'Sản phẩm' }}"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;"
                                    >
                                @else
                                    <span style="font-size: 20px;" aria-hidden="true">🌱</span>
                                @endif
                            </div>

                            <div class="admin-ops-item-details">
                                <h3 class="admin-ops-item-name">
                                    {{ $detail?->item_name ?? 'Sản phẩm' }}
                                </h3>
                                <div class="admin-ops-item-meta">
                                    <span>Mã: <code class="admin-code" style="font-size: 11px; padding: 1px 5px;">{{ $detail?->item_code ?? '—' }}</code></span>
                                    <span>Loại: <strong>{{ $detail?->item_type ?? '—' }}</strong></span>
                                </div>
                            </div>

                            <div class="admin-ops-item-pricing">
                                <div class="admin-ops-item-subtotal">
                                    Dự kiến hoàn: {{ number_format((float) $item->refund_amount, 0, ',', '.') }}đ
                                </div>
                                <div class="admin-ops-item-unit">
                                    {{ number_format((float) $item->unit_price, 0, ',', '.') }}đ × {{ $item->quantity }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 3. REASON & CUSTOMER NOTE --}}
            <section class="card" style="margin-bottom: 0;">
                <h2 style="font-size: 16px; margin: 0 0 14px;">
                    Nội dung yêu cầu từ khách hàng
                </h2>

                <div style="display: flex; flex-direction: column; gap: 14px; font-size: 13.5px;">
                    <div>
                        <span style="color: var(--admin-text-muted); display: block; font-size: 12px; margin-bottom: 4px;">
                            Lý do hoàn trả:
                        </span>
                        <strong style="color: var(--admin-forest-900); font-size: 14px;">
                            {{ $reasonLabels[$returnRequest->reason] ?? $returnRequest->reason }}
                        </strong>
                    </div>

                    <div>
                        <span style="color: var(--admin-text-muted); display: block; font-size: 12px; margin-bottom: 4px;">
                            Mô tả chi tiết của khách hàng:
                        </span>
                        <div style="background: var(--admin-canvas); border: 1px solid var(--admin-border); border-radius: var(--admin-radius-md); padding: 14px; font-size: 13.5px; line-height: 1.6; white-space: pre-line;">{{ $returnRequest->description ?: 'Không có mô tả thêm.' }}</div>
                    </div>
                </div>
            </section>

            {{-- 4. PROCESSING HISTORY / NOTES (IF PROCESSED) --}}
            @if ($returnRequest->processed_by || $returnRequest->processing_note)
                <section class="card" style="margin-bottom: 0;">
                    <h2 style="font-size: 16px; margin: 0 0 14px;">
                        Thông tin xử lý trước đó
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13.5px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                            <span>
                                Người xử lý: <strong>{{ $returnRequest->processor?->name ?? ('#' . $returnRequest->processed_by) }}</strong>
                            </span>
                            <span style="color: var(--admin-text-muted); font-size: 12.5px;">
                                Thời gian: {{ $returnRequest->processed_at?->format('d/m/Y H:i') ?? '—' }}
                            </span>
                        </div>

                        @if ($returnRequest->processing_note)
                            <div>
                                <span style="color: var(--admin-text-muted); display: block; font-size: 12px; margin-bottom: 4px;">
                                    Ghi chú xử lý:
                                </span>
                                <div style="background: var(--admin-canvas); border: 1px solid var(--admin-border); border-radius: var(--admin-radius-md); padding: 12px; font-size: 13px; line-height: 1.5; white-space: pre-line;">{{ $returnRequest->processing_note }}</div>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

        </main>

        {{-- =====================================================
             RIGHT COLUMN: SIDEBAR (REFUND SUMMARY & ACTIONS)
        ====================================================== --}}
        <aside class="admin-ops-sidebar">

            {{-- REFUND SUMMARY CARD --}}
            <section class="card" style="margin-bottom: 0;">
                <h2 style="font-size: 16px; margin: 0 0 16px;">
                    Tổng kết hoàn tiền
                </h2>

                <div class="admin-ops-summary-list">
                    <div class="admin-ops-summary-row">
                        <span>Trạng thái yêu cầu</span>
                        <span class="badge badge-order-{{ $returnRequest->status }}">
                            {{ $statusLabels[$returnRequest->status] ?? $returnRequest->status }}
                        </span>
                    </div>

                    <div class="admin-ops-summary-row">
                        <span>Thanh toán đơn gốc</span>
                        <span class="badge badge-payment-{{ $payment?->status ?? 'pending' }}">
                            {{ $paymentLabels[$payment?->status] ?? ($payment?->status ?? '—') }}
                        </span>
                    </div>

                    <div class="admin-ops-summary-row admin-ops-summary-total">
                        <span>Số tiền yêu cầu hoàn</span>
                        <strong>{{ number_format((float) $returnRequest->refund_amount, 0, ',', '.') }}đ</strong>
                    </div>
                </div>
            </section>

            {{-- WORKFLOW ACTION CARD --}}
            @if ($returnRequest->status === 'pending')
                {{-- PENDING: APPROVE OR REJECT --}}
                <section class="card" style="margin-bottom: 0;">
                    <h2 style="font-size: 16px; margin: 0 0 8px;">
                        Xử lý yêu cầu trả hàng
                    </h2>
                    <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0 0 16px;">
                        Kiểm tra thông tin trước khi chấp nhận hoặc từ chối yêu cầu.
                    </p>

                    {{-- APPROVE FORM --}}
                    <form
                        action="{{ route('admin.returns.approve', $returnRequest) }}"
                        method="POST"
                        onsubmit="return confirm('Chấp nhận yêu cầu trả hàng này?');"
                        style="margin-bottom: 20px;"
                    >
                        @csrf
                        @method('PATCH')

                        <div style="margin-bottom: 12px;">
                            <label for="approve_note" style="font-size: 13px; margin-bottom: 5px;">
                                Ghi chú khi chấp nhận
                            </label>
                            <textarea
                                id="approve_note"
                                name="processing_note"
                                rows="3"
                                maxlength="2000"
                                placeholder="Ghi chú nội bộ (không bắt buộc)..."
                                style="font-size: 13px; min-height: 70px;"
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Chấp nhận yêu cầu
                        </button>
                    </form>

                    <div style="position: relative; text-align: center; margin: 20px 0; border-top: 1px solid var(--admin-border-subtle);">
                        <span style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: var(--admin-surface); padding: 0 12px; font-size: 12px; color: var(--admin-text-muted);">
                            hoặc
                        </span>
                    </div>

                    {{-- REJECT FORM (DISTINCT DANGER SECONDARY WITH SPACING) --}}
                    <form
                        action="{{ route('admin.returns.reject', $returnRequest) }}"
                        method="POST"
                        onsubmit="return confirm('Bạn chắc chắn muốn từ chối yêu cầu trả hàng này?');"
                    >
                        @csrf
                        @method('PATCH')

                        <div style="margin-bottom: 12px;">
                            <label for="reject_note" style="font-size: 13px; margin-bottom: 5px;">
                                Lý do từ chối <span style="color: var(--admin-danger);">*</span>
                            </label>
                            <textarea
                                id="reject_note"
                                name="processing_note"
                                rows="3"
                                maxlength="2000"
                                required
                                placeholder="Nhập lý do từ chối (bắt buộc)..."
                                style="font-size: 13px; min-height: 70px;"
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger" style="width: 100%;">
                            Từ chối yêu cầu
                        </button>
                    </form>
                </section>

            @elseif ($returnRequest->status === 'approved')
                {{-- APPROVED: START RETURNING --}}
                <section class="card" style="margin-bottom: 0;">
                    <h2 style="font-size: 16px; margin: 0 0 8px;">
                        Bắt đầu trả hàng
                    </h2>
                    <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0 0 16px;">
                        Xác nhận khách hàng bắt đầu gửi hàng về MiniGrove.
                    </p>

                    <form
                        action="{{ route('admin.returns.returning', $returnRequest) }}"
                        method="POST"
                        onsubmit="return confirm('Chuyển yêu cầu sang trạng thái Đang trả hàng?');"
                    >
                        @csrf
                        @method('PATCH')

                        <div style="margin-bottom: 14px;">
                            <label for="returning_note" style="font-size: 13px; margin-bottom: 5px;">
                                Hướng dẫn / ghi chú gửi hàng
                            </label>
                            <textarea
                                id="returning_note"
                                name="processing_note"
                                rows="3"
                                maxlength="2000"
                                placeholder="Ví dụ: Đã gửi mã vận đơn gửi hàng về kho..."
                                style="font-size: 13px; min-height: 80px;"
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Bắt đầu quá trình trả hàng
                        </button>
                    </form>
                </section>

            @elseif ($returnRequest->status === 'returning')
                {{-- RETURNING: CONFIRM RECEIVED & RESTOCK SELECTION --}}
                <section class="card" style="margin-bottom: 0;">
                    <h2 style="font-size: 16px; margin: 0 0 8px;">
                        Xác nhận nhận hàng
                    </h2>
                    <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0 0 14px;">
                        Kiểm tra tình trạng hàng thực tế và thiết lập số lượng nhập lại kho.
                    </p>

                    <form
                        action="{{ route('admin.returns.received', $returnRequest) }}"
                        method="POST"
                        onsubmit="return confirm('Xác nhận MiniGrove đã nhận lại sản phẩm?');"
                    >
                        @csrf
                        @method('PATCH')

                        <div class="admin-ops-warning-banner" style="margin-bottom: 14px;" role="alert">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <div>
                                Hàng hư hỏng, vỡ hoặc không đủ điều kiện bán phải chọn số lượng nhập lại kho bằng 0.
                            </div>
                        </div>

                        <div class="admin-return-restock-card">
                            <div style="font-size: 12.5px; font-weight: 600; color: var(--admin-forest-900); margin-bottom: 8px;">
                                Số lượng nhập lại kho:
                            </div>

                            @foreach ($returnRequest->items as $item)
                                <div class="admin-return-restock-row">
                                    <div style="flex: 1; min-width: 0;">
                                        <strong style="display: block; font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $item->orderDetail?->item_name ?? 'Sản phẩm' }}
                                        </strong>
                                        <small style="color: var(--admin-text-muted);">
                                            Khách trả: {{ $item->quantity }}
                                        </small>
                                    </div>

                                    <div>
                                        <select
                                            id="restock_{{ $item->id }}"
                                            name="restock_quantities[{{ $item->id }}]"
                                            class="admin-return-restock-select"
                                            aria-label="Số lượng nhập lại kho cho {{ $item->orderDetail?->item_name ?? 'sản phẩm' }}"
                                        >
                                            @for ($quantity = 0; $quantity <= $item->quantity; $quantity++)
                                                <option value="{{ $quantity }}">
                                                    {{ $quantity }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div style="margin-bottom: 14px;">
                            <label for="received_note" style="font-size: 13px; margin-bottom: 5px;">
                                Ghi chú kiểm tra hàng
                            </label>
                            <textarea
                                id="received_note"
                                name="processing_note"
                                rows="3"
                                maxlength="2000"
                                placeholder="Ghi nhận tình trạng sản phẩm khi mở hộp..."
                                style="font-size: 13px; min-height: 80px;"
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Xác nhận đã nhận hàng
                        </button>
                    </form>
                </section>

            @elseif ($returnRequest->status === 'received')
                {{-- RECEIVED: EXECUTE REFUND --}}
                <section class="card" style="margin-bottom: 0;">
                    <h2 style="font-size: 16px; margin: 0 0 8px;">
                        Hoàn tiền cho khách hàng
                    </h2>
                    <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0 0 14px;">
                        Xác nhận thực hiện hoàn tiền sau khi đã nhận và kiểm tra hàng.
                    </p>

                    <div style="background: var(--admin-canvas); border: 1px solid var(--admin-border); border-radius: var(--admin-radius-md); padding: 14px; margin-bottom: 16px; text-align: center;">
                        <span style="font-size: 12.5px; color: var(--admin-text-muted); display: block; margin-bottom: 2px;">
                            Số tiền hoàn trả
                        </span>
                        <strong style="font-size: 22px; color: var(--admin-forest-900); font-variant-numeric: tabular-nums;">
                            {{ number_format((float) $returnRequest->refund_amount, 0, ',', '.') }}đ
                        </strong>
                    </div>

                    <form
                        action="{{ route('admin.returns.refund', $returnRequest) }}"
                        method="POST"
                        onsubmit="return confirm('Xác nhận đã hoàn trả số tiền {{ number_format((float) $returnRequest->refund_amount, 0, ',', '.') }}đ cho khách hàng?');"
                    >
                        @csrf
                        @method('PATCH')

                        <div style="margin-bottom: 14px;">
                            <label for="refund_note" style="font-size: 13px; margin-bottom: 5px;">
                                Ghi chú hoàn tiền
                            </label>
                            <textarea
                                id="refund_note"
                                name="processing_note"
                                rows="3"
                                maxlength="2000"
                                placeholder="Ví dụ: Đã chuyển khoản hoàn tiền thành công..."
                                style="font-size: 13px; min-height: 80px;"
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Xác nhận hoàn tiền
                        </button>
                    </form>
                </section>

            @else
                {{-- FINAL STATE: REFUNDED OR REJECTED --}}
                <section class="card" style="margin-bottom: 0;">
                    <h2 style="font-size: 16px; margin: 0 0 8px;">
                        Trạng thái hoàn tất
                    </h2>

                    <div style="padding: 16px; background: var(--admin-canvas); border-radius: var(--admin-radius-md); border: 1px solid var(--admin-border); text-align: center;">
                        @if ($returnRequest->status === 'refunded')
                            <span style="font-size: 18px; display: block; margin-bottom: 4px;">✅</span>
                            <strong style="color: var(--admin-forest-900);">Yêu cầu đã hoàn tất hoàn tiền</strong>
                            <p style="font-size: 12.5px; color: var(--admin-text-secondary); margin: 4px 0 0;">
                                Quy trình hoàn trả và hoàn tiền đã kết thúc.
                            </p>
                        @else
                            <span style="font-size: 18px; display: block; margin-bottom: 4px;">⛔</span>
                            <strong style="color: var(--admin-danger);">Yêu cầu đã bị từ chối</strong>
                            <p style="font-size: 12.5px; color: var(--admin-text-secondary); margin: 4px 0 0;">
                                Yêu cầu này không được chấp thuận hoàn trả.
                            </p>
                        @endif
                    </div>
                </section>
            @endif

        </aside>

    </div>

</div>

@endsection