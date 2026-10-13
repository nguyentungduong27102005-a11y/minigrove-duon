@extends('layouts.admin')

@section('title', 'Chi tiết khách hàng')


@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/admin-customers.css') }}"
    >

@endpush


@section('content')


<div class="customer-detail-page">


    {{-- =========================================================
         TOP
    ========================================================== --}}
    <div class="customer-detail-top">

        <a
            href="{{ route('admin.customers.index') }}"
            class="customer-back-btn"
        >
            ← Quay lại Khách hàng
        </a>

    </div>



    {{-- =========================================================
         CUSTOMER HEADER
    ========================================================== --}}
    <div class="customer-profile-card">


        <div class="customer-profile-main">


            <div class="customer-profile-avatar">


                    @if ($customer->is_active)

                        <span
                            class="
                                customer-status
                                customer-status-active
                            "
                        >
                            Active
                        </span>

                    @else

                        <span
                            class="
                                customer-status
                                customer-status-inactive
                            "
                        >
                            Inactive
                        </span>

                    @endif

                </div>


                <p>
                    Customer ID #{{ $customer->id }}
                </p>

            </div>


        </div>



        <form
            method="POST"
            action="{{
                route(
                    'admin.customers.toggle-status',
                    $customer
                )
            }}"
        >

            @csrf
            @method('PATCH')


            <button
                type="submit"
                class="
                    customer-btn
                    {{
                        $customer->is_active
                            ? 'customer-btn-inactive'
                            : 'customer-btn-active'
                    }}
                "
                onclick="
                    return confirm(
                        '{{ $customer->is_active
                            ? 'Bạn có chắc muốn khóa Customer này?'
                            : 'Bạn có chắc muốn kích hoạt Customer này?'
                        }}'
                    );
                "
            >

                {{
                    $customer->is_active
                        ? 'Chuyển Inactive'
                        : 'Chuyển Active'
                }}

            </button>

        </form>


    </div>



    {{-- =========================================================
         THỐNG KÊ
    ========================================================== --}}
    <div class="customer-detail-stats">


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Tổng đơn hàng
            </div>

            <div class="customer-stat-number">
                {{ $orderCount }}
            </div>

        </div>


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Đơn hoàn thành
            </div>

            <div class="customer-stat-number">
                {{ $completedOrderCount }}
            </div>

        </div>


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Đơn đã hủy
            </div>

            <div class="customer-stat-number">
                {{ $cancelledOrderCount }}
            </div>

        </div>


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Tổng giá trị Completed
            </div>

            <div class="customer-stat-money">

                {{
                    number_format(
                        $completedSpending,
                        0,
                        ',',
                        '.'
                    )
                }}đ

            </div>

        </div>


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Đã hoàn tiền
            </div>

            <div class="customer-stat-money customer-refund-money">

                {{
                    number_format(
                        $refundedAmount,
                        0,
                        ',',
                        '.'
                    )
                }}đ

            </div>

        </div>


        <div class="customer-stat-card">

            <div class="customer-stat-title">
                Tổng chi tiêu thực tế
            </div>

            <div class="customer-stat-money">

                {{
                    number_format(
                        $totalSpending,
                        0,
                        ',',
                        '.'
                    )
                }}đ

            </div>

        </div>


    </div>



    {{-- =========================================================
         THÔNG TIN QUẢN TRỊ TÀI KHOẢN
    ========================================================== --}}
    <div class="customer-detail-card customer-account-card">


        <h2>
            Thông tin tài khoản
        </h2>


        <div class="customer-detail-info">


            <div class="customer-detail-row">

                <span>
                    Customer ID
                </span>

                <strong>
                    #{{ $customer->id }}
                </strong>

            </div>


           


            <div class="customer-detail-row">

                <span>
                    Vai trò
                </span>

                <strong>
                    {{
                        $customer->role->display_name
                        ?? 'Khách hàng'
                    }}
                </strong>

            </div>


            <div class="customer-detail-row">

                <span>
                    Trạng thái
                </span>

                <strong>
                    {{
                        $customer->is_active
                            ? 'Active'
                            : 'Inactive'
                    }}
                </strong>

            </div>


            <div class="customer-detail-row">

                <span>
                    Ngày tham gia
                </span>

                <strong>
                    {{
                        optional(
                            $customer->created_at
                        )->format(
                            'd/m/Y H:i'
                        )
                    }}
                </strong>

            </div>


        </div>


        <div class="customer-privacy-note">

            Thông tin liên hệ và thông tin nhận hàng của Customer
            không được hiển thị tại khu vực quản trị.

        </div>


    </div>



    {{-- =========================================================
         ORDER HISTORY
    ========================================================== --}}
    <div class="customer-detail-card customer-order-section">


        <div class="customer-detail-card-heading">

            <div>

                <h2>
                    Lịch sử đơn hàng
                </h2>

                <p>
                    Theo dõi lịch sử giao dịch của Customer.
                </p>

            </div>


            <span>
                {{ $orderCount }} đơn
            </span>

        </div>



        <div class="customer-table-wrapper">


            <table class="customer-table customer-order-table">


                <thead>

                    <tr>

                        <th>
                            Mã đơn
                        </th>

                        <th>
                            Ngày đặt
                        </th>

                        <th>
                            Phương thức
                        </th>

                        <th>
                            Tổng tiền
                        </th>

                        <th>
                            Thanh toán
                        </th>

                        <th>
                            Trạng thái đơn
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>


                    @forelse ($customer->orders as $order)


                        @php

                            $orderStatusLabels = [

                                'pending' =>
                                    'Chờ xác nhận',

                                'confirmed' =>
                                    'Đã xác nhận',

                                'preparing' =>
                                    'Đang chuẩn bị',

                                'packed' =>
                                    'Đã đóng gói',

                                'shipping' =>
                                    'Đang giao',

                                'completed' =>
                                    'Hoàn thành',

                                'cancelled' =>
                                    'Đã hủy',

                            ];


                            $paymentStatusLabels = [

                                'pending' =>
                                    'Chờ thanh toán',

                                'paid' =>
                                    'Đã thanh toán',

                                'failed' =>
                                    'Thất bại',

                                'refunded' =>
                                    'Đã hoàn tiền',

                            ];

                        @endphp


                        <tr>


                            {{-- MÃ ĐƠN --}}
                            <td>

                                <strong class="customer-order-code">

                                    {{ $order->order_code }}

                                </strong>

                            </td>



                            {{-- NGÀY ĐẶT --}}
                            <td>

                                {{
                                    optional(
                                        $order->created_at
                                    )->format(
                                        'd/m/Y H:i'
                                    )
                                }}

                            </td>



                            {{-- PHƯƠNG THỨC --}}
                            <td>

                                <span class="customer-method-badge">

                                    {{ $order->payment_method }}

                                </span>

                            </td>



                            {{-- TỔNG TIỀN --}}
                            <td class="customer-money">

                                {{
                                    number_format(
                                        (float)
                                        $order->total_amount,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}đ

                            </td>



                            {{-- THANH TOÁN --}}
                            <td>

                                <span
                                    class="
                                        customer-payment-status
                                        customer-payment-{{
                                            $order->payment_status
                                        }}
                                    "
                                >

                                    {{
                                        $paymentStatusLabels[
                                            $order->payment_status
                                        ]
                                        ??
                                        $order->payment_status
                                    }}

                                </span>

                            </td>



                            {{-- TRẠNG THÁI ĐƠN --}}
                            <td>

                                <span
                                    class="
                                        customer-order-status
                                        customer-order-{{
                                            $order->order_status
                                        }}
                                    "
                                >

                                    {{
                                        $orderStatusLabels[
                                            $order->order_status
                                        ]
                                        ??
                                        $order->order_status
                                    }}

                                </span>

                            </td>



                            {{-- THAO TÁC --}}
                            <td>

                                <a
                                    href="{{
                                        route(
                                            'admin.orders.show',
                                            $order
                                        )
                                    }}"
                                    class="
                                        customer-btn
                                        customer-btn-small
                                    "
                                >
                                    Xem đơn
                                </a>

                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td
                                colspan="7"
                                class="customer-empty"
                            >
                                Customer chưa có đơn hàng.
                            </td>

                        </tr>


                    @endforelse


                </tbody>


            </table>


        </div>


    </div>


</div>


@endsection