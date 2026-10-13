@extends('layouts.admin')

@section('title', 'Thêm sản phẩm - MiniGrove')

@section('page-title', 'Sản phẩm')

@section('content')
<div class="admin-form-container admin-form-container-wide">

    {{-- PAGE HEADER --}}
    <div class="admin-form-header">
        <div class="admin-form-heading">
            <h1 class="admin-form-title">Thêm sản phẩm mới</h1>
            <p class="admin-form-subtitle">Tạo sản phẩm Terrarium, rêu cảnh hoặc phụ kiện sinh thái cho cửa hàng.</p>
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
    {{-- FORM CARD --}}
    <div class="admin-form-card">
        <form
            action="{{ route('admin.products.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @include('admin.products._form')

            <div class="admin-form-actions">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Thêm sản phẩm</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection