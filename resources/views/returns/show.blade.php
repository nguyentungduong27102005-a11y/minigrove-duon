@extends('layouts.customer')


@section(
    'title',
    'Chi tiết trả hàng - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/returns.css') }}"
>

@endpush


@section('content')

@php

    $statusLabels = [

        'pending' =>
            'Chờ xử lý',

        'approved' =>
            'Đã chấp nhận',

        'rejected' =>
            'Đã từ chối',

        'returning' =>
            'Đang trả hàng',

        'received' =>
            'Đã nhận hàng trả',

        'refunded' =>
            'Đã hoàn tiền',
    ];


    $reasonLabels = [

        'damaged' =>
            'Sản phẩm bị hư hỏng',

        'wrong_item' =>
            'Giao sai sản phẩm',

        'not_as_described' =>
            'Sản phẩm không đúng mô tả',

        'quality_issue' =>
            'Sản phẩm có vấn đề chất lượng',

        'other' =>
            'Lý do khác',
    ];

@endphp


<section class="returns-page">

    <div class="customer-container">


        {{-- BACK --}}

        <a
            href="{{
                route(
                    'orders.index',
                    [
                        'tab' => 'returns',
                    ]
                )
            }}"
            class="returns-back-button"
        >
            ← Trả hàng / Hoàn tiền
        </a>


        {{-- FLASH --}}



        {{-- HEADING --}}

        <div class="return-detail-heading">

            <div>

                <span>
                    Mã yêu cầu
                </span>

                <h1>
                    {{
                        $returnRequest
                            ->return_code
                    }}
                </h1>

                <p>

                    Gửi lúc

                    {{
                        $returnRequest
                            ->created_at
                            ->format(
                                'd/m/Y H:i'
                            )
                    }}

                </p>

            </div>


            <span
                class="
                    return-detail-status
                    return-detail-status-{{
                        $returnRequest->status
                    }}
                "
            >

                {{
                    $statusLabels[
                        $returnRequest->status
                    ]
                    ?? $returnRequest->status
                }}

            </span>

        </div>


        <div class="return-detail-layout">


            {{-- LEFT --}}

            <div class="return-detail-main">


                {{-- ORDER --}}

                <section class="returns-card">

                    <div class="returns-card-heading">

                        <div>

                            <h2>
                                Đơn hàng
                            </h2>

                            <p>
                                Đơn hàng liên quan
                                đến yêu cầu này.
                            </p>

                        </div>

                    </div>


                    <div class="return-order-link">

                        <div>

                            <span>
                                Mã đơn
                            </span>

                            <strong>

                                {{
                                    $returnRequest
                                        ->order
                                        ?->order_code
                                    ?? '—'
                                }}

                            </strong>

                        </div>


                        <a
                            href="{{
                                route(
                                    'orders.show',
                                    $returnRequest
                                        ->order_id
                                )
                            }}"
                            class="
                                returns-secondary-button
                            "
                        >
                            Xem đơn hàng
                        </a>

                    </div>

                </section>


                {{-- ITEMS --}}

                <section class="returns-card">

                    <div class="returns-card-heading">

                        <div>

                            <h2>
                                Sản phẩm yêu cầu trả
                            </h2>

                        </div>

                    </div>


                    <div class="return-detail-items">


                        @foreach (
                            $returnRequest->items
                            as $item
                        )

                            @php

                                $detail =
                                    $item
                                        ->orderDetail;


                                $product =
                                    $detail
                                        ?->product;


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

                            @endphp


                            <div class="return-detail-item">


                                <div
                                    class="
                                        returns-product-image
                                    "
                                >

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
                                                    ?->item_name
                                                ?? 'Sản phẩm'
                                            }}"
                                        >

                                    @else

                                        <span>
                                            🌱
                                        </span>

                                    @endif

                                </div>


                                <div class="return-item-info">

                                    <strong>
                                        {{
                                            $detail
                                                ?->item_name
                                            ?? 'Sản phẩm'
                                        }}
                                    </strong>

                                    <span>

                                        {{
                                            number_format(
                                                $item
                                                    ->unit_price,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}đ

                                        ×

                                        {{
                                            $item
                                                ->quantity
                                        }}

                                    </span>

                                </div>


                                <div class="return-item-refund">

                                    <span>
                                        Dự kiến hoàn
                                    </span>

                                    <strong>

                                        {{
                                            number_format(
                                                $item
                                                    ->refund_amount,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}đ

                                    </strong>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </section>


                {{-- REASON --}}

                <section class="returns-card">

                    <div class="returns-card-heading">

                        <div>
                            <h2>
                                Thông tin yêu cầu
                            </h2>
                        </div>

                    </div>


                    <div class="return-request-info">


                        <div>

                            <span>
                                Lý do
                            </span>

                            <strong>

                                {{
                                    $reasonLabels[
                                        $returnRequest
                                            ->reason
                                    ]
                                    ?? $returnRequest
                                        ->reason
                                }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Mô tả
                            </span>

                            <p>

                                {{
                                    $returnRequest
                                        ->description
                                    ?: 'Không có mô tả.'
                                }}

                            </p>

                        </div>


                        @if (
                            $returnRequest
                                ->processing_note
                        )

                            <div>

                                <span>
                                    Phản hồi từ MiniGrove
                                </span>

                                <p>

                                    {{
                                        $returnRequest
                                            ->processing_note
                                    }}

                                </p>

                            </div>

                        @endif

                    </div>

                </section>

            </div>


            {{-- RIGHT --}}

            <aside class="return-detail-sidebar">


                <section class="return-refund-card">

                    <h2>
                        Hoàn tiền
                    </h2>


                    <div class="return-refund-row">

                        <span>
                            Trạng thái
                        </span>

                        <strong>

                            {{
                                $statusLabels[
                                    $returnRequest
                                        ->status
                                ]
                                ?? $returnRequest
                                    ->status
                            }}

                        </strong>

                    </div>


                    <div
                        class="
                            return-refund-row
                            return-refund-total
                        "
                    >

                        <span>
                            Số tiền dự kiến
                        </span>

                        <strong>

                            {{
                                number_format(
                                    $returnRequest
                                        ->refund_amount,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </strong>

                    </div>


                    @if (
                        $returnRequest
                            ->processed_at
                    )

                        <div
                            class="
                                return-processed-at
                            "
                        >

                            Cập nhật:

                            {{
                                $returnRequest
                                    ->processed_at
                                    ->format(
                                        'd/m/Y H:i'
                                    )
                            }}

                        </div>

                    @endif

                </section>


                <div class="return-status-note">

                    @if (
                        $returnRequest->status
                        === 'pending'
                    )

                        MiniGrove đã nhận yêu cầu
                        và đang chờ Staff/Admin
                        kiểm tra.

                    @elseif (
                        $returnRequest->status
                        === 'approved'
                    )

                        Yêu cầu đã được chấp nhận.
                        Vui lòng thực hiện bước
                        trả hàng theo hướng dẫn.

                    @elseif (
                        $returnRequest->status
                        === 'rejected'
                    )

                        Yêu cầu không được chấp nhận.
                        Xem phản hồi của MiniGrove
                        để biết thêm chi tiết.

                    @elseif (
                        $returnRequest->status
                        === 'returning'
                    )

                        Sản phẩm đang trong quá trình
                        được gửi trả về MiniGrove.

                    @elseif (
                        $returnRequest->status
                        === 'received'
                    )

                        MiniGrove đã nhận được
                        sản phẩm trả lại và đang
                        xử lý hoàn tiền.

                    @elseif (
                        $returnRequest->status
                        === 'refunded'
                    )

                        Yêu cầu đã hoàn tất
                        và được ghi nhận hoàn tiền.

                    @endif

                </div>

            </aside>

        </div>

    </div>

</section>

@endsection