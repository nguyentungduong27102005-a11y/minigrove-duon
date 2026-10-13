@extends('layouts.admin')

@section('title', 'Thêm quy tắc tương thích - MiniGrove')
@section('page-title', 'Thêm quy tắc tương thích')

@section('content')

<div class="admin-form-container">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="admin-form-header">
        <div class="admin-form-heading">
            <h1 class="admin-form-title">
                Thêm quy tắc tương thích
            </h1>
            <p class="admin-form-subtitle">
                Thiết lập điều kiện kết hợp giữa hai sản phẩm trong Studio.
            </p>
        </div>

        <div class="admin-form-header-actions">
            <a
                href="{{ route('admin.compatibility-rules.index') }}"
                class="btn btn-secondary"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Quay lại danh sách
            </a>
        </div>
    </div>

    {{-- =========================================================
         FORM CARD
    ========================================================== --}}
    <div class="card admin-form-card">
        <form
            action="{{ route('admin.compatibility-rules.store') }}"
            method="POST"
            novalidate
        >
            @csrf

            @include('admin.compatibility-rules._form')

            <div class="admin-form-actions">
                <a
                    href="{{ route('admin.compatibility-rules.index') }}"
                    class="btn btn-secondary"
                >
                    Hủy bỏ
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Thêm quy tắc
                </button>
            </div>
        </form>
    </div>

</div>

@endsection