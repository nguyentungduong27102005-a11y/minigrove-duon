@extends('layouts.admin')

@section('title', 'Lịch sử tồn kho - MiniGrove')

@section('page-title', 'Lịch sử tồn kho')


@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/admin-stocks.css') }}"
    >
@endpush


@section('content')

<div class="stock-page">


    {{-- NÚT QUAY LẠI --}}
    <div class="stock-history-back">

        <a
            href="{{ route('admin.stocks.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại tồn kho
        </a>

    </div>


    {{-- THÔNG TIN ITEM --}}
    <div class="card stock-history-info">

        <div class="stock-history-type">

    Sản phẩm

</div>

            <h1>
                {{ $item->name }}
            </h1>

            <div class="stock-history-sku">
                SKU: {{ $item->sku }}
            </div>

        </div>


        <div class="stock-history-summary">

            <div>

                <span>
                    Tồn kho
                </span>

                <strong>
                    {{ number_format(
                        $item->stock_quantity,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>

            </div>


            <div>

                <span>
                    Ngưỡng cảnh báo
                </span>

                <strong>
                    {{ number_format(
                        $item->low_stock_threshold,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>

            </div>

        </div>

    </div>


    {{-- LỊCH SỬ --}}
    <div class="card">

        <div class="stock-table-header">

            <div>

                <h2>
                    Lịch sử biến động kho
                </h2>

                <p>
                    Tổng {{ $histories->total() }} giao dịch
                </p>

            </div>

        </div>


        <div class="table-wrapper">

            <table class="stock-history-table">

                <thead>

                <tr>
                    <th>Thời gian</th>
                    <th>Loại giao dịch</th>
                    <th>Tồn trước</th>
                    <th>Thay đổi</th>
                    <th>Tồn sau</th>
                    <th>Đơn hàng</th>
                    <th>Người thực hiện</th>
                    <th>Ghi chú</th>
                </tr>

                </thead>


                <tbody>

                @forelse ($histories as $history)

                    @php

                        $transactionLabels = [
                            'IMPORT' =>
                                'Nhập kho',

                            'EXPORT' =>
                                'Xuất kho',

                            'ADJUSTMENT' =>
                                'Điều chỉnh',

                            'ORDER' =>
                                'Bán hàng',

                            'ORDER_CANCEL' =>
                                'Hoàn kho do hủy đơn',

                            'RETURN' =>
                                'Hoàn kho',

                            'RETURN_RECEIVED' =>
                                'Nhập lại hàng trả',
                        ];


                        $transactionLabel =
                            $transactionLabels[
                                $history->transaction_type
                            ]
                            ?? $history->transaction_type;


                        $change =
                            (int) $history->quantity_change;

                    @endphp


                    <tr>

                        {{-- THỜI GIAN --}}
                        <td class="stock-history-time">

                            {{
                                $history->created_at
                                    ? $history->created_at
                                        ->format('d/m/Y H:i')
                                    : '—'
                            }}

                        </td>


                        {{-- LOẠI --}}
                        <td>

                            <span class="stock-transaction-badge">

                                {{ $transactionLabel }}

                            </span>

                        </td>


                        {{-- TỒN TRƯỚC --}}
                        <td>

                            {{
                                number_format(
                                    $history->quantity_before,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}

                        </td>


                        {{-- THAY ĐỔI --}}
                        <td>

                            <strong
                                class="
                                    {{
                                        $change > 0
                                            ? 'stock-change-positive'
                                            : (
                                                $change < 0
                                                    ? 'stock-change-negative'
                                                    : 'stock-change-neutral'
                                            )
                                    }}
                                "
                            >

                                @if ($change > 0)
                                    +
                                @endif

                                {{
                                    number_format(
                                        $change,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}

                            </strong>

                        </td>


                        {{-- TỒN SAU --}}
                        <td>

                            <strong>

                                {{
                                    number_format(
                                        $history->quantity_after,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}

                            </strong>

                        </td>


                        {{-- ORDER --}}
                        <td>

                            @if ($history->order)

                                <a
                                    href="{{
                                        route(
                                            'admin.orders.show',
                                            $history->order
                                        )
                                    }}"
                                    class="stock-order-link"
                                >
                                    {{
                                        $history->order
                                            ->order_code
                                    }}
                                </a>

                            @else

                                —

                            @endif

                        </td>


                        {{-- NGƯỜI THỰC HIỆN --}}
                        <td>

                            @if ($history->updater)

                                {{
                                    $history->updater->name
                                }}

                            @else

                                <span class="stock-system-text">
                                    Hệ thống
                                </span>

                            @endif

                        </td>


                        {{-- GHI CHÚ --}}
                        <td class="stock-history-note">

                            {{
                                $history->note
                                    ?: '—'
                            }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="empty"
                        >
                            Chưa có biến động tồn kho nào.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if ($histories->hasPages())

            <div class="stock-pagination">

                <div>
                    Trang
                    <strong>
                        {{ $histories->currentPage() }}
                    </strong>
                    /
                    {{ $histories->lastPage() }}
                </div>


                <div class="stock-pagination-actions">

                    @if (!$histories->onFirstPage())

                        <a
                            href="{{ $histories->previousPageUrl() }}"
                            class="btn btn-secondary"
                        >
                            Trước
                        </a>

                    @endif


                    @if ($histories->hasMorePages())

                        <a
                            href="{{ $histories->nextPageUrl() }}"
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