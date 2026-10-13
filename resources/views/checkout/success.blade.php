@extends('layouts.customer')


@section(
    'title',
    'Đặt hàng thành công - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/checkout.css') }}"
>

@endpush


@section('content')

<section class="checkout-page">

    <div class="customer-container">


        <div class="checkout-success-box">


            <div class="checkout-success-icon">
                ✓
            </div>


            <h1>
                Đặt hàng thành công
            </h1>


            <p>
                MiniGrove đã tiếp nhận
                đơn hàng của bạn.
            </p>


            <div class="checkout-success-info">


                <div>

                    <span>
                        Mã đơn hàng
                    </span>

                    <strong>
                        {{ $order->order_code }}
                    </strong>

                </div>


                <div>

                    <span>
                        Tổng thanh toán
                    </span>

                    <strong>

                        {{
                            number_format(
                                $order->total_amount,
                                0,
                                ',',
                                '.'
                            )
                        }}đ

                    </strong>

                </div>


                <div>

                    <span>
                        Phương thức
                    </span>

                    <strong>
                        {{
                            $order->payment_method
                        }}
                    </strong>

                </div>


                <div>

                    <span>
                        Trạng thái đơn
                    </span>

                    <strong>
                        Chờ xác nhận
                    </strong>

                </div>

                @if ((float) $order->discount_amount > 0)
                    <div>
                        <span>Giảm giá đơn hàng</span>
                        <strong style="color: var(--mg-success, #2D7A3A);">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</strong>
                    </div>
                @endif

                @if ((float) $order->shipping_discount_amount > 0)
                    <div>
                        <span>Giảm phí ship</span>
                        <strong style="color: var(--mg-success, #2D7A3A);">-{{ number_format($order->shipping_discount_amount, 0, ',', '.') }}đ</strong>
                    </div>
                @endif
            </div>


            <div class="checkout-success-actions">

                <a
                    href="{{ route('orders.show', $order) }}"
                    class="checkout-secondary-button"
                >
                    Xem chi tiết đơn hàng
                </a>

                <a
                    href="{{ route('products.index') }}"
                    class="checkout-secondary-button"
                >
                    Tiếp tục mua sắm
                </a>


                <a
                    href="{{ route('home') }}"
                    class="checkout-success-main-button"
                >
                    Về trang chủ
                </a>

            </div>

        </div>

    </div>

</section>

@endsection