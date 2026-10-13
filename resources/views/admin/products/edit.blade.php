@extends('layouts.admin')

@section('title', 'Sửa sản phẩm - MiniGrove')

@section('page-title', 'Sản phẩm')

@section('content')
<div class="admin-form-container admin-form-container-wide">

    {{-- PAGE HEADER --}}
    <div class="admin-form-header">
        <div class="admin-form-heading">
            <h1 class="admin-form-title">Sửa sản phẩm</h1>
            <p class="admin-form-subtitle">Cập nhật thông tin và thư viện ảnh của <strong>{{ $product->name }}</strong> (SKU: {{ $product->sku }}).</p>
        </div>

        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Quay lại</span>
            </a>
        </div>
    </div>

    {{-- ERROR SUMMARY --}}
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="alert-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="alert-content">
                <strong>Không thể cập nhật sản phẩm. Vui lòng kiểm tra lại:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- FORM CARD --}}
    <div class="admin-form-card">
        <form
            action="{{ route('admin.products.update', $product) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            @include('admin.products._form')

            <div class="admin-form-actions">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Cập nhật sản phẩm</span>
                </button>
            </div>
        </form>

        {{-- QUẢN LÝ HÌNH ẢNH HIỆN CÓ --}}
        <div class="admin-product-gallery-section">
            <div class="admin-product-gallery-header">
                <div>
                    <h2 class="admin-form-section-title" style="font-size: 18px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                        <span>Thư viện hình ảnh sản phẩm</span>
                    </h2>
                    <p class="admin-form-section-desc">
                        Quản lý ảnh chi tiết, thay thế hoặc chọn ảnh chính. Ảnh chính sẽ được ưu tiên hiển thị trên thẻ sản phẩm và giỏ hàng.
                    </p>
                </div>

                <span class="badge badge-neutral" style="font-size: 13px; font-weight: 700; padding: 6px 12px;">
                    {{ $product->images->count() }}/8 ảnh
                </span>
            </div>

            @if ($product->images->count())
                <div class="admin-gallery-grid">
                    @foreach ($product->images as $image)
                        <div class="admin-gallery-card {{ $image->is_primary ? 'is-primary' : '' }}">
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--admin-text-secondary);">
                                <span>Ảnh #{{ $loop->iteration }}</span>
                                @if ($image->is_primary)
                                    <span class="badge badge-success" style="font-size: 11px; padding: 2px 8px;">
                                        ★ Ảnh chính
                                    </span>
                                @endif
                            </div>

                            <img
                                src="{{ asset('storage/' . $image->image_path) }}"
                                alt="{{ $product->name }}"
                                class="admin-gallery-thumb"
                                loading="lazy"
                            >

                            @unless ($image->is_primary)
                                <form
                                    action="{{ route('admin.products.images.primary', [$product, $image->id]) }}"
                                    method="POST"
                                    style="margin: 0;"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">
                                        ★ Đặt làm ảnh chính
                                    </button>
                                </form>
                            @endunless

                            {{-- Thay ảnh --}}
                            <form
                                action="{{ route('admin.products.images.update', [$product, $image->id]) }}"
                                method="POST"
                                enctype="multipart/form-data"
                                style="margin: 0; padding-top: 8px; border-top: 1px solid var(--admin-border-subtle);"
                            >
                                @csrf
                                @method('PATCH')

                                <label for="replace-image-{{ $image->id }}" style="font-size: 12px; margin-bottom: 4px; color: var(--admin-forest-800);">
                                    Thay bằng file mới:
                                </label>
                                <input
                                    id="replace-image-{{ $image->id }}"
                                    type="file"
                                    name="image"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    required
                                    style="min-height: 36px; padding: 4px 8px; font-size: 12px; margin-bottom: 6px;"
                                >
                                <button type="submit" class="btn btn-outline btn-sm" style="width: 100%;">
                                    Tải ảnh thay thế
                                </button>
                            </form>

                            {{-- Xóa ảnh --}}
                            <form
                                action="{{ route('admin.products.images.destroy', [$product, $image->id]) }}"
                                method="POST"
                                onsubmit="return confirm('Bạn chắc chắn muốn xóa ảnh này?');"
                                style="margin: 0; padding-top: 6px;"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger-soft btn-sm" style="width: 100%; color: var(--admin-danger);">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    Xóa ảnh này
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="padding: 24px; border: 1px dashed var(--admin-border); border-radius: 10px; background: var(--admin-canvas); color: var(--admin-text-secondary); text-align: center; font-size: 13.5px;">
                    Sản phẩm hiện chưa có hình ảnh. Hãy chọn ảnh ở mục “Thêm hình ảnh sản phẩm” phía trên rồi cập nhật sản phẩm.
                </div>
            @endif
        </div>

    </div>

</div>
@endsection