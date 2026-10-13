@extends('layouts.customer')


@section(
    'title',
    'Yêu cầu trả hàng - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/returns.css') }}"
>

@endpush


@section('content')

<section class="returns-page">

    <div class="customer-container">


        {{-- BACK --}}

        <a
            href="{{ route('orders.show', $order) }}"
            class="returns-back-button"
        >
            ← Quay lại đơn hàng
        </a>


        {{-- HEADING --}}

        <div class="returns-heading">

            <h1>
                Yêu cầu trả hàng / hoàn tiền
            </h1>

            <p>
                Chọn sản phẩm cần trả và cung cấp
                thông tin để MiniGrove xử lý yêu cầu.
            </p>

        </div>



        {{-- ORDER INFO --}}

        <div class="returns-order-info">

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
                    Ngày đặt
                </span>

                <strong>
                    {{
                        $order
                            ->created_at
                            ->format('d/m/Y H:i')
                    }}
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

        </div>


        <form
            action="{{ route('returns.store', $order) }}"
            method="POST"
            class="returns-form"
        >

            @csrf


            {{-- =============================================
                 PRODUCTS
            ============================================== --}}

            <section class="returns-card">

                <div class="returns-card-heading">

                    <div>

                        <h2>
                            Sản phẩm cần trả
                        </h2>

                        <p>
                            Chọn số lượng muốn yêu cầu
                            trả cho từng sản phẩm.
                        </p>

                    </div>

                </div>


                <div class="returns-products">

                    @foreach (
                        $returnableDetails
                        as $detail
                    )

                        @php

                            $product =
                                $detail->product;


                            $primaryImage =
                                $product
                                    ?->images
                                    ?->firstWhere(
                                        'is_primary',
                                        true
                                    )
                                ??
                                $product
                                    ?->images
                                    ?->first();


                            $oldQuantity =
                                old(
                                    'quantities.'
                                    . $detail->id,
                                    0
                                );

                        @endphp


                        <div class="returns-product">


                            <div class="returns-product-image">

                                @if ($primaryImage)

                                    <img
                                        src="{{
                                            asset(
                                                'storage/'
                                                . $primaryImage
                                                    ->image_path
                                            )
                                        }}"
                                        alt="{{
                                            $detail
                                                ->item_name
                                        }}"
                                    >

                                @else

                                    <span>
                                        🌱
                                    </span>

                                @endif

                            </div>


                            <div class="returns-product-info">

                                <strong>
                                    {{
                                        $detail
                                            ->item_name
                                    }}
                                </strong>

                                <span>
                                    Mã:
                                    {{
                                        $detail
                                            ->item_code
                                        ?? '—'
                                    }}
                                </span>

                                <span>

                                    Giá mua:

                                    {{
                                        number_format(
                                            $detail
                                                ->unit_price,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}đ

                                </span>

                                <span>

                                    Đã mua:
                                    {{
                                        $detail
                                            ->quantity
                                    }}

                                </span>

                            </div>


                            <div class="returns-product-quantity">

                                <label
                                    for="quantity_{{
                                        $detail->id
                                    }}"
                                >
                                    Số lượng trả
                                </label>


                                <select
                                    id="quantity_{{
                                        $detail->id
                                    }}"
                                    name="quantities[{{
                                        $detail->id
                                    }}]"
                                >

                                    @for (
                                        $quantity = 0;
                                        $quantity
                                            <=
                                        $detail
                                            ->returnable_quantity;
                                        $quantity++
                                    )

                                        <option
                                            value="{{ $quantity }}"
                                            @selected(
                                                (int)
                                                $oldQuantity
                                                ===
                                                $quantity
                                            )
                                        >
                                            {{ $quantity }}
                                        </option>

                                    @endfor

                                </select>


                                <small>

                                    Có thể trả tối đa:

                                    {{
                                        $detail
                                            ->returnable_quantity
                                    }}

                                </small>

                            </div>

                        </div>

                    @endforeach

                </div>

            </section>


            {{-- =============================================
                 REASON
            ============================================== --}}

            <section class="returns-card">

                <div class="returns-card-heading">

                    <div>

                        <h2>
                            Lý do trả hàng
                        </h2>

                        <p>
                            Chọn lý do phù hợp nhất
                            với tình trạng sản phẩm.
                        </p>

                    </div>

                </div>


                <div class="returns-fields">


                    <div class="returns-field">

                        <label for="reason">
                            Lý do
                            <span>*</span>
                        </label>


                        <select
                            id="reason"
                            name="reason"
                            required
                        >

                            <option value="">
                                -- Chọn lý do --
                            </option>


                            @foreach (
                                $reasons
                                as $value => $label
                            )

                                <option
                                    value="{{ $value }}"
                                    @selected(
                                        old('reason')
                                        === $value
                                    )
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="returns-field">

                        <label for="description">
                            Mô tả chi tiết
                        </label>


                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            maxlength="2000"
                            placeholder="Ví dụ: sản phẩm bị vỡ khi nhận hàng, cây bị hư hỏng..."
                        >{{ old('description') }}</textarea>


                        <small>
                            Tối đa 2.000 ký tự.
                        </small>

                    </div>

                </div>

            </section>


            {{-- =============================================
                 POLICY
            ============================================== --}}

            <div class="returns-policy">

                <strong>
                    Lưu ý
                </strong>

                <p>
                    Việc gửi yêu cầu không đồng nghĩa
                    với việc MiniGrove đã chấp nhận
                    trả hàng hoặc hoàn tiền.
                </p>

                <p>
                    Yêu cầu sẽ được Staff/Admin
                    kiểm tra và xử lý trước khi
                    tiến hành các bước tiếp theo.
                </p>

            </div>


            {{-- =============================================
                 ACTION
            ============================================== --}}

            <div class="returns-actions">

                <a
                    href="{{ route('orders.show', $order) }}"
                    class="returns-secondary-button"
                >
                    Hủy thao tác
                </a>


                <button
                    type="submit"
                    class="returns-main-button"
                >
                    Gửi yêu cầu
                </button>

            </div>

        </form>

    </div>

</section>

@endsection