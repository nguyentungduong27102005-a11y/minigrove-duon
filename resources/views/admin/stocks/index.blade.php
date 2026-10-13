@extends('layouts.admin')

@section('title', 'Quản lý tồn kho - MiniGrove')
@section('page-title', 'Tồn kho')

@section('content')
<div class="admin-list-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">
                Quản lý tồn kho
            </h1>
            <p class="admin-index-subtitle">
                Theo dõi số lượng hàng tồn, thiết lập ngưỡng an toàn và quản lý luồng nhập/xuất kho.
            </p>
        </div>
    </div>


    {{-- =========================================================
         QUICK STATS SUMMARY
    ========================================================== --}}
    <div class="admin-stat-summary">
        <div class="admin-stat-summary-card">
            <div class="admin-stat-summary-title">Tổng sản phẩm</div>
            <div class="admin-stat-summary-val">{{ number_format($productCount) }}</div>
        </div>

        <div class="admin-stat-summary-card {{ $lowStockCount > 0 ? 'is-warning' : '' }}">
            <div class="admin-stat-summary-title">Sắp hết hàng</div>
            <div class="admin-stat-summary-val">{{ number_format($lowStockCount) }}</div>
        </div>

        <div class="admin-stat-summary-card {{ $outOfStockCount > 0 ? 'is-danger' : '' }}">
            <div class="admin-stat-summary-title">Hết hàng</div>
            <div class="admin-stat-summary-val">{{ number_format($outOfStockCount) }}</div>
        </div>
    </div>


    {{-- =========================================================
         TOOLBAR / FILTER
    ========================================================== --}}
    <div class="admin-toolbar">
        <form action="{{ route('admin.stocks.index') }}" method="GET" class="admin-toolbar-form">
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
                    placeholder="Tên hoặc SKU..."
                    aria-label="Tìm kiếm tồn kho"
                >
            </div>

            <div class="admin-toolbar-select">
                <select id="status" name="status" aria-label="Lọc theo trạng thái tồn">
                    <option value="" @selected($status === '')>Tất cả trạng thái</option>
                    <option value="in_stock" @selected($status === 'in_stock')>Còn hàng</option>
                    <option value="low_stock" @selected($status === 'low_stock')>Sắp hết hàng</option>
                    <option value="out_of_stock" @selected($status === 'out_of_stock')>Hết hàng</option>
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-primary">
                    Lọc
                </button>

                <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary">
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
                    Danh sách tồn kho
                </h2>
                <p class="admin-table-desc">
                    Tổng cộng {{ $stocks->total() }} mặt hàng
                </p>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th scope="col" style="width: 120px;">SKU</th>
                        <th scope="col">Tên sản phẩm</th>
                        <th scope="col" style="text-align: right; width: 100px;">Tồn kho</th>
                        <th scope="col" style="width: 170px;">Ngưỡng cảnh báo</th>
                        <th scope="col" style="text-align: center; width: 110px;">Tình trạng</th>
                        <th scope="col" style="text-align: center; width: 100px;">Kinh doanh</th>
                        <th scope="col" style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stocks as $stock)
                        @php
                            $quantity = (int) $stock->stock_quantity;
                            $threshold = (int) $stock->low_stock_threshold;

                            if ($quantity === 0) {
                                $badgeClass = 'badge-danger';
                                $stockStatusLabel = 'Hết hàng';
                            } elseif ($quantity <= $threshold) {
                                $badgeClass = 'badge-warning';
                                $stockStatusLabel = 'Sắp hết';
                            } else {
                                $badgeClass = 'badge-success';
                                $stockStatusLabel = 'Còn hàng';
                            }
                        @endphp
                        <tr>
                            <td>
                                <span class="admin-text-mono">{{ $stock->sku }}</span>
                            </td>

                            <td>
                                <div>
                                    <strong class="admin-text-strong">{{ $stock->name }}</strong>
                                </div>
                            </td>

                            <td style="text-align: right;">
                                <span class="admin-cell-number" style="font-size: 15px;">
                                    {{ number_format($quantity, 0, ',', '.') }}
                                </span>
                            </td>

                            <td>
                                <form
                                    action="{{ route('admin.stocks.threshold.update', ['stockType'=>'PRODUCT', 'itemId'=>$stock->item_id]) }}"
                                    method="POST"
                                    class="stock-threshold-form"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="number"
                                        name="low_stock_threshold"
                                        value="{{ $threshold }}"
                                        min="0"
                                        required
                                        aria-label="Ngưỡng cảnh báo"
                                    >

                                    <button type="submit" class="stock-small-button" title="Lưu ngưỡng cảnh báo mới">
                                        Lưu
                                    </button>
                                </form>
                            </td>

                            <td style="text-align: center;">
                                <span class="badge {{ $badgeClass }}">
                                    {{ $stockStatusLabel }}
                                </span>
                            </td>

                            <td style="text-align: center;">
                                @if($stock->is_active)
                                    <span class="badge badge-success">
                                        Đang bán
                                    </span>
                                @else
                                    <span class="badge badge-muted">
                                        Ngừng bán
                                    </span>
                                @endif
                            </td>

                            <td style="text-align: right;">
                                <div class="admin-table-actions">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-secondary"
                                        style="color: var(--admin-success); border-color: var(--admin-success-border);"
                                        onclick="openStockModal('PRODUCT', '{{ $stock->item_id }}', '{{ addslashes($stock->name) }}', 'import')"
                                        title="Nhập thêm hàng vào kho"
                                    >
                                        Nhập
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-secondary"
                                        style="color: var(--admin-warning); border-color: var(--admin-warning-border);"
                                        onclick="openStockModal('PRODUCT', '{{ $stock->item_id }}', '{{ addslashes($stock->name) }}', 'export')"
                                        title="Xuất hàng khỏi kho"
                                    >
                                        Xuất
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-secondary"
                                        onclick="openStockModal('PRODUCT', '{{ $stock->item_id }}', '{{ addslashes($stock->name) }}', 'adjust')"
                                        title="Cân chỉnh số lượng tồn kho thực tế"
                                    >
                                        Chỉnh
                                    </button>

                                    <a
                                        href="{{ route('admin.stocks.history', ['stockType'=>'PRODUCT', 'itemId'=>$stock->item_id]) }}"
                                        class="btn btn-sm btn-secondary"
                                        title="Xem lịch sử biến động kho"
                                    >
                                        Lịch sử
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">
                                <p>Không có dữ liệu tồn kho phù hợp.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('admin.partials.pagination', ['paginator' => $stocks])

    </div>

</div>


{{-- =========================================================
     MODAL THAY ĐỔI TỒN KHO
========================================================== --}}
<div id="stockModal" class="stock-modal" style="display:none" role="dialog" aria-modal="true" aria-labelledby="stockModalTitle">
    <div class="stock-modal-box">
        <div class="stock-modal-header">
            <h3 id="stockModalTitle">Điều chỉnh tồn kho</h3>
            <button type="button" onclick="closeStockModal()" class="stock-modal-close" aria-label="Đóng cửa sổ">
                &times;
            </button>
        </div>

        <form id="stockChangeForm" method="POST">
            @csrf

            <div class="form-group">
                <label for="stockItemName">Sản phẩm</label>
                <input type="text" id="stockItemName" readonly style="background: var(--admin-canvas);">
            </div>

            <div class="form-group">
                <label for="stockQuantity">Số lượng</label>
                <input type="number" id="stockQuantity" name="quantity" min="0" required>
            </div>

            <div class="form-group">
                <label for="stockNote">Ghi chú</label>
                <textarea id="stockNote" name="note" rows="3" required placeholder="Nhập lý do nhập / xuất / điều chỉnh..."></textarea>
            </div>

            <input type="hidden" name="operation" id="stockOperation">

            <div class="stock-modal-actions">
                <button type="button" onclick="closeStockModal()" class="btn btn-secondary">
                    Hủy
                </button>
                <button type="submit" class="btn btn-primary">
                    Xác nhận
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openStockModal(type, id, name, operation) {
    const modal = document.getElementById('stockModal');
    const form = document.getElementById('stockChangeForm');
    document.getElementById('stockItemName').value = name;
    document.getElementById('stockOperation').value = operation;
    form.action = `/admin/stocks/${type}/${id}/change`;
    modal.style.display = 'flex';
}

function closeStockModal() {
    document.getElementById('stockModal').style.display = 'none';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStockModal();
    }
});
</script>
@endpush
@endsection