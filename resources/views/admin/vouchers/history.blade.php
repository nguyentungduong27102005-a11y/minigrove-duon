@extends('layouts.admin')

@section('title', 'Lịch sử sử dụng Voucher - MiniGrove')

@section('page-title', 'Lịch sử sử dụng Voucher')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/admin-vouchers.css') }}"
    >
@endpush


@section('content')

<div class="voucher-page">


    {{-- QUAY LẠI --}}
    <div class="voucher-form-page-header">

        <a
            href="{{ route('admin.vouchers.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại danh sách
        </a>

    </div>


    {{-- THÔNG TIN VOUCHER --}}
    <div class="card voucher-history-info">

        <div>

            <div class="voucher-history-label">
                Mã Voucher
            </div>

            <div class="voucher-history-code">
                {{ $voucher->code }}
            </div>

        </div>


        <div>

            <div class="voucher-history-label">
                Ưu đãi
            </div>

            <div class="voucher-history-value">

                @if ($voucher->discount_type === 'percentage')

                    {{
                        number_format(
                            $voucher->discount_value,
                            0,
                            ',',
                            '.'
                        )
                    }}%

                @else

                    {{
                        number_format(
                            $voucher->discount_value,
                            0,
                            ',',
                            '.'
                        )
                    }}đ

                @endif

            </div>

        </div>


        <div>

            <div class="voucher-history-label">
                Lượt đã sử dụng
            </div>

            <div class="voucher-history-value">

                {{ $voucher->usage_count }}

                /

                {{ $voucher->usage_limit ?? '∞' }}

            </div>

        </div>


        <div>

            <div class="voucher-history-label">
                Trạng thái
            </div>

            <div>

                @if ($voucher->is_active)

                    <span class="status status-active">
                        Active
                    </span>

                @else

                    <span class="status status-inactive">
                        Inactive
                    </span>

                @endif

            </div>

        </div>

    </div>


    {{-- DANH SÁCH LỊCH SỬ --}}
    <div class="card">

        <div class="voucher-table-header">

            <div>

                <h2>
                    Lịch sử sử dụng
                </h2>

                <p>
                    Tổng {{ $usages->total() }} lượt phát sinh
                </p>

            </div>

        </div>


        <div class="table-wrapper">

            <table class="voucher-history-table">

                <thead>

                    <tr>
                        <th>Customer</th>
                        <th>Đơn hàng</th>
                        <th>Số tiền giảm</th>
                        <th>Trạng thái</th>
                        <th>Thời gian sử dụng</th>
                        <th>Thời gian hoàn lượt</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse ($usages as $usage)

                        <tr>

                            {{-- CUSTOMER --}}
                            <td>

                                @if ($usage->user)

                                    <strong>
                                        {{ $usage->user->name }}
                                    </strong>

                                    <div class="voucher-subtext">
                                        {{ $usage->user->email }}
                                    </div>

                                @else

                                    <span class="voucher-subtext">
                                        Không xác định
                                    </span>

                                @endif

                            </td>


                            {{-- ORDER --}}
                            <td>

                                @if ($usage->order)

                                    <a
                                        href="{{
                                            route(
                                                'admin.orders.show',
                                                $usage->order
                                            )
                                        }}"
                                        class="voucher-order-link"
                                    >
                                        {{ $usage->order->order_code }}
                                    </a>

                                    <div class="voucher-subtext">

                                        Tổng đơn:

                                        {{
                                            number_format(
                                                $usage->order->total_amount,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}đ

                                    </div>

                                @else

                                    <span class="voucher-subtext">
                                        Không xác định
                                    </span>

                                @endif

                            </td>


                            {{-- DISCOUNT --}}
                            <td>

                                <strong>

                                    {{
                                        number_format(
                                            $usage->discount_amount,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}đ

                                </strong>

                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if ($usage->status === 'used')

                                    <span class="voucher-usage-status used">
                                        Đã sử dụng
                                    </span>

                                @elseif ($usage->status === 'released')

                                    <span class="voucher-usage-status released">
                                        Đã hoàn lượt
                                    </span>

                                @else

                                    <span class="voucher-usage-status">
                                        {{ $usage->status }}
                                    </span>

                                @endif

                            </td>


                            {{-- USED AT --}}
                            <td>

                                {{
                                    $usage->used_at
                                        ? $usage->used_at->format(
                                            'd/m/Y H:i'
                                        )
                                        : '—'
                                }}

                            </td>


                            {{-- RELEASED AT --}}
                            <td>

                                {{
                                    $usage->released_at
                                        ? $usage->released_at->format(
                                            'd/m/Y H:i'
                                        )
                                        : '—'
                                }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="empty"
                            >
                                Voucher này chưa có lịch sử sử dụng.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($usages->hasPages())

            <div class="voucher-pagination">

                <span>
                    Trang
                    <strong>
                        {{ $usages->currentPage() }}
                    </strong>
                    /
                    {{ $usages->lastPage() }}
                </span>


                <div>

                    @if (!$usages->onFirstPage())

                        <a
                            href="{{ $usages->previousPageUrl() }}"
                            class="btn btn-secondary"
                        >
                            Trước
                        </a>

                    @endif


                    @if ($usages->hasMorePages())

                        <a
                            href="{{ $usages->nextPageUrl() }}"
                            class="btn btn-secondary"
                        >
                            Sau
                        </a>

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>

@endsection