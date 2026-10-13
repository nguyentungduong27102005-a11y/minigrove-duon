@extends('layouts.admin')

@section('title', 'Quy tắc tương thích - MiniGrove')
@section('page-title', 'Quy tắc tương thích')

@section('content')

<div class="admin-list-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">
                Quy tắc tương thích
            </h1>
            <p class="admin-index-subtitle">
                Quản lý điều kiện kết hợp giữa các thành phần trong Studio.
            </p>
        </div>

        <div class="admin-index-actions">
            <a
                href="{{ route('admin.compatibility-rules.create') }}"
                class="btn btn-primary"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Thêm quy tắc
            </a>
        </div>
    </div>

    {{-- =========================================================
         TOOLBAR / FILTER
    ========================================================== --}}
    <div class="admin-toolbar">
        <form
            action="{{ route('admin.compatibility-rules.index') }}"
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
                    placeholder="Tên hoặc SKU sản phẩm..."
                    aria-label="Tìm kiếm quy tắc tương thích"
                >
            </div>

            <div class="admin-toolbar-select">
                <select id="compatibility" name="compatibility" aria-label="Lọc kết quả tương thích">
                    <option value="" @selected($compatibility === null || $compatibility === '')>Tất cả kết quả</option>
                    <option value="1" @selected((string) $compatibility === '1')>Tương thích</option>
                    <option value="0" @selected((string) $compatibility === '0')>Không tương thích</option>
                </select>
            </div>

            <div class="admin-toolbar-select">
                <select id="status" name="status" aria-label="Lọc theo trạng thái áp dụng">
                    <option value="" @selected($status === null || $status === '')>Tất cả trạng thái</option>
                    <option value="1" @selected((string) $status === '1')>Active</option>
                    <option value="0" @selected((string) $status === '0')>Inactive</option>
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-primary">
                    Lọc
                </button>

                @if ($keyword || ($compatibility !== null && $compatibility !== '') || ($status !== null && $status !== ''))
                    <a href="{{ route('admin.compatibility-rules.index') }}" class="btn btn-secondary">
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
                    Danh sách quy tắc tương thích
                </h2>
                <p class="admin-table-desc">
                    Tổng cộng {{ $rules->total() }} quy tắc được thiết lập
                </p>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col" style="width: 60px;">ID</th>
                        <th scope="col">Thành phần nguồn (A)</th>
                        <th scope="col" style="width: 50px; text-align: center;">Chiều</th>
                        <th scope="col">Thành phần đích (B)</th>
                        <th scope="col">Kết quả</th>
                        <th scope="col">Lý do / Ghi chú</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col" style="min-width: 200px;">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($rules as $rule)
                        <tr>
                            {{-- ID --}}
                            <td>
                                <span class="admin-code" style="font-size: 12px; padding: 2px 6px;">
                                    #{{ $rule->id }}
                                </span>
                            </td>

                            {{-- SOURCE PRODUCT --}}
                            <td>
                                <div class="admin-rule-entity">
                                    <span class="admin-rule-product-name">
                                        {{ $rule->sourceProduct?->name ?? '—' }}
                                    </span>
                                    @if ($rule->sourceProduct?->sku)
                                        <span class="admin-rule-sku">
                                            SKU: {{ $rule->sourceProduct->sku }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- RELATION DIRECTION --}}
                            <td style="text-align: center;">
                                <div class="admin-rule-relation" title="Quy tắc áp dụng theo cặp thành phần">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </div>
                            </td>

                            {{-- TARGET PRODUCT --}}
                            <td>
                                <div class="admin-rule-entity">
                                    <span class="admin-rule-product-name">
                                        {{ $rule->targetProduct?->name ?? '—' }}
                                    </span>
                                    @if ($rule->targetProduct?->sku)
                                        <span class="admin-rule-sku">
                                            SKU: {{ $rule->targetProduct->sku }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- RESULT --}}
                            <td>
                                @if ($rule->is_compatible)
                                    <span class="badge badge-success">
                                        Tương thích
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        Không tương thích
                                    </span>
                                @endif
                            </td>

                            {{-- REASON --}}
                            <td>
                                <div class="admin-rule-reason-text" title="{{ $rule->reason }}">
                                    {{ $rule->reason ?: '—' }}
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                @if ($rule->is_active)
                                    <span class="badge badge-success">
                                        Active
                                    </span>
                                @else
                                    <span class="badge badge-muted">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="actions">
                                    <a
                                        href="{{ route('admin.compatibility-rules.edit', $rule) }}"
                                        class="btn btn-sm btn-secondary"
                                    >
                                        Sửa
                                    </a>

                                    <form
                                        action="{{ route('admin.compatibility-rules.toggle-status', $rule) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-status"
                                        >
                                            {{ $rule->is_active ? 'Tắt' : 'Bật' }}
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route('admin.compatibility-rules.destroy', $rule) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bạn chắc chắn muốn xóa quy tắc này?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                        >
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px 20px;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <span style="font-size: 28px;" aria-hidden="true">🔄</span>
                                    <strong style="font-size: 15px; color: var(--admin-text-primary);">
                                        Không tìm thấy quy tắc phù hợp
                                    </strong>
                                    <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0;">
                                        Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm sản phẩm.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @include('admin.partials.pagination', ['paginator' => $rules])
    </div>

</div>

@endsection