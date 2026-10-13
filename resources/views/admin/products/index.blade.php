@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm - MiniGrove')
@section('page-title', 'Sản phẩm')

@section('content')
<div class="admin-list-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">
                Quản lý sản phẩm
            </h1>
            <p class="admin-index-subtitle">
                Danh sách sản phẩm terrarium hoàn chỉnh, cây thủy sinh, rêu và phụ kiện sinh thái.
            </p>
        </div>

        <div class="admin-index-actions">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Thêm sản phẩm</span>
            </a>
        </div>
    </div>


    {{-- =========================================================
         TOOLBAR / FILTER
    ========================================================== --}}
    <div class="admin-toolbar">
        <form action="{{ route('admin.products.index') }}" method="GET" class="admin-toolbar-form">
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
                    placeholder="Tên hoặc SKU..."
                    aria-label="Tìm kiếm sản phẩm"
                >
            </div>

            <div class="admin-toolbar-select">
                <select name="category_id" aria-label="Lọc theo danh mục">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($categoryId == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-toolbar-select">
                <select name="status" aria-label="Lọc theo trạng thái">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" @selected((string) $status === '1')>Active</option>
                    <option value="0" @selected((string) $status === '0')>Inactive</option>
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-primary">
                    Lọc
                </button>

                @if ($keyword || $categoryId || ($status !== null && $status !== ''))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        Xóa lọc
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
                    Danh sách sản phẩm
                </h2>
                <p class="admin-table-desc">
                    Tổng cộng {{ $products->total() }} sản phẩm
                </p>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th scope="col" style="width: 55px;">ID</th>
                        <th scope="col" style="width: 65px;">Ảnh</th>
                        <th scope="col">Sản phẩm</th>
                        <th scope="col">SKU</th>
                        <th scope="col">Danh mục</th>
                        <th scope="col">Giá</th>
                        <th scope="col" style="text-align: center;">Tồn</th>
                        <th scope="col" style="text-align: center;">Hệ</th>
                        <th scope="col" style="text-align: center;">Trạng thái</th>
                        <th scope="col" style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                        @endphp
                        <tr>
                            <td>
                                <span class="admin-text-mono">#{{ $product->id }}</span>
                            </td>

                            <td>
                                @if ($primaryImage)
                                    <img
                                        class="admin-thumbnail"
                                        src="{{ asset('storage/' . $primaryImage->image_path) }}"
                                        alt="{{ $product->name }}"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="admin-thumbnail-fallback" title="Không có ảnh đại diện">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                            <circle cx="9" cy="9" r="2"/>
                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div style="max-width: 260px;">
                                    <strong class="admin-text-strong" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </strong>
                                    @if ($product->is_featured)
                                        <span class="badge badge-warning" style="margin-top: 3px; font-size: 11px; padding: 2px 7px;">
                                            ★ Nổi bật
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span class="admin-text-mono">{{ $product->sku ?: '—' }}</span>
                            </td>

                            <td>
                                <span style="color: var(--admin-text-secondary);">
                                    {{ $product->category?->name ?? '—' }}
                                </span>
                            </td>

                            <td>
                                @if ($product->sale_price !== null)
                                    <div>
                                        <span class="admin-price-sale">
                                            {{ number_format((float) $product->sale_price, 0, ',', '.') }}đ
                                        </span>
                                    </div>
                                    <div class="admin-price-old">
                                        {{ number_format((float) $product->list_price, 0, ',', '.') }}đ
                                    </div>
                                @else
                                    <span class="admin-cell-number">
                                        {{ number_format((float) $product->list_price, 0, ',', '.') }}đ
                                    </span>
                                @endif
                            </td>

                            <td style="text-align: center;">
                                @if ($product->stock_quantity > 0)
                                    <span class="badge badge-muted">
                                        {{ $product->stock_quantity }}
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        Hết hàng
                                    </span>
                                @endif
                            </td>

                            <td style="text-align: center;">
                                @switch($product->ecosystem_type)
                                    @case('closed')
                                        <span class="badge badge-info">Kín</span>
                                        @break
                                    @case('open')
                                        <span class="badge badge-muted">Mở</span>
                                        @break
                                    @case('dry')
                                        <span class="badge badge-warning">Khô</span>
                                        @break
                                    @default
                                        <span class="badge badge-muted">{{ $product->ecosystem_type ?: '—' }}</span>
                                @endswitch
                            </td>

                            <td style="text-align: center;">
                                @if ($product->is_active)
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
                                        href="{{ route('admin.products.edit', $product) }}"
                                        class="btn btn-sm btn-secondary"
                                        title="Chỉnh sửa sản phẩm"
                                    >
                                        Sửa
                                    </a>

                                    <form
                                        action="{{ route('admin.products.toggle-status', $product) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-status"
                                            title="Thay đổi trạng thái hiển thị"
                                        >
                                            {{ $product->is_active ? 'Ẩn' : 'Hiện' }}
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route('admin.products.destroy', $product) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Xóa sản phẩm"
                                        >
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty">
                                <p>Không có sản phẩm phù hợp.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('admin.partials.pagination', ['paginator' => $products])

    </div>

</div>
@endsection