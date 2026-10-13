@extends('layouts.admin')

@section('title', 'Quản lý danh mục - MiniGrove')
@section('page-title', 'Danh mục')

@section('content')
<div class="admin-list-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">
                Quản lý danh mục
            </h1>
            <p class="admin-index-subtitle">
                Danh sách các phân loại terrarium, cây cảnh và vật liệu sinh thái tại MiniGrove.
            </p>
        </div>

        <div class="admin-index-actions">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Thêm danh mục</span>
            </a>
        </div>
    </div>


    {{-- =========================================================
         TOOLBAR / SEARCH
    ========================================================== --}}
    <div class="admin-toolbar">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="admin-toolbar-form">
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
                    placeholder="Tìm tên hoặc slug..."
                    aria-label="Tìm kiếm danh mục"
                >
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-primary">
                    Tìm kiếm
                </button>

                @if ($keyword)
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
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
                    Danh sách danh mục
                </h2>
                <p class="admin-table-desc">
                    Tổng cộng {{ $categories->total() }} danh mục
                </p>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th scope="col" style="width: 60px;">ID</th>
                        <th scope="col" style="width: 70px;">Ảnh</th>
                        <th scope="col">Tên danh mục</th>
                        <th scope="col">Slug</th>
                        <th scope="col" style="text-align: center;">Sản phẩm</th>
                        <th scope="col" style="text-align: center;">Thứ tự</th>
                        <th scope="col" style="text-align: center;">Trạng thái</th>
                        <th scope="col" style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <span class="admin-text-mono">#{{ $category->id }}</span>
                            </td>

                            <td>
                                @if ($category->image)
                                    <img
                                        class="admin-thumbnail"
                                        src="{{ asset('storage/' . $category->image) }}"
                                        alt="{{ $category->name }}"
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
                                <strong class="admin-text-strong">{{ $category->name }}</strong>
                            </td>

                            <td>
                                <span class="admin-text-mono">{{ $category->slug }}</span>
                            </td>

                            <td style="text-align: center;">
                                <span class="badge badge-muted">
                                    {{ $category->products_count }} SP
                                </span>
                            </td>

                            <td style="text-align: center;">
                                <span class="admin-cell-number">{{ $category->display_order }}</span>
                            </td>

                            <td style="text-align: center;">
                                @if ($category->is_active)
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
                                        href="{{ route('admin.categories.edit', $category) }}"
                                        class="btn btn-sm btn-secondary"
                                        title="Chỉnh sửa danh mục"
                                    >
                                        Sửa
                                    </a>

                                    <form
                                        action="{{ route('admin.categories.toggle-status', $category) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-status"
                                            title="Thay đổi trạng thái hiển thị"
                                        >
                                            {{ $category->is_active ? 'Ẩn' : 'Hiện' }}
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route('admin.categories.destroy', $category) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bạn chắc chắn muốn xóa danh mục này?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Xóa danh mục"
                                        >
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty">
                                <p>Không có danh mục phù hợp.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('admin.partials.pagination', ['paginator' => $categories])

    </div>

</div>
@endsection