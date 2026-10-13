@extends('layouts.admin')

@section('title', 'Thêm danh mục - MiniGrove')

@section('page-title', 'Danh mục sản phẩm')

@section('content')
<div class="admin-form-container">

    {{-- PAGE HEADER --}}
    <div class="admin-form-header">
        <div class="admin-form-heading">
            <h1 class="admin-form-title">Thêm danh mục</h1>
            <p class="admin-form-subtitle">Tạo phân loại mới cho hệ sinh thái sản phẩm MiniGrove.</p>
        </div>

        <div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
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
            action="{{ route('admin.categories.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @include('admin.categories._form')

            <div class="admin-form-actions">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Lưu danh mục</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection