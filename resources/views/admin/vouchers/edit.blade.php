@extends('layouts.admin')

@section('title', 'Sửa Voucher - MiniGrove')

@section('page-title', 'Mã ưu đãi')

@section('content')
<div class="admin-form-container">

    {{-- PAGE HEADER --}}
    <div class="admin-form-header">
        <div class="admin-form-heading">
            <h1 class="admin-form-title">Sửa Voucher</h1>
            <p class="admin-form-subtitle">Cập nhật thiết lập của mã ưu đãi <strong>{{ $voucher->code }}</strong>.</p>
        </div>

        <div>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
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
            action="{{ route('admin.vouchers.update', $voucher) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            @include('admin.vouchers._form')
        </form>
    </div>

</div>
@endsection