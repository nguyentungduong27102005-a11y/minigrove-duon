@extends('layouts.customer')


@section(
    'title',
    'My Grove - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/groves.css') }}"
>

@endpush


@section('content')

@php

    $ecosystemLabels = [
        'closed' => 'Hệ sinh thái kín',
        'open' => 'Hệ sinh thái mở',
        'dry' => 'Hệ sinh thái khô',
        'custom' => 'Terrarium thiết kế riêng',
        'terrarium' => 'Terrarium',
    ];


    $statusLabels = [
        'pending_activation' => 'Chờ kích hoạt',
        'active' => 'Đang hoạt động',
        'needs_attention' => 'Cần chú ý',
        'archived' => 'Đã lưu trữ',
    ];


    $careLevelLabels = [
        'easy' => 'Dễ chăm sóc',
        'medium' => 'Trung bình',
        'high' => 'Cần chăm sóc kỹ',
    ];

@endphp


<section class="groves-page">

    <div class="customer-container">


        {{-- =====================================================
             HEADING
        ====================================================== --}}

        <div class="groves-heading">

            <div>

                <span class="groves-eyebrow">
                    🌿 BỘ SƯU TẬP CỦA BẠN
                </span>

                <h1>
                    My Grove
                </h1>

                <p>
                    Những hệ sinh thái MiniGrove
                    bạn đang sở hữu và chăm sóc.
                </p>

            </div>


            <div class="groves-summary">

                <span class="groves-summary-number">
                    {{ $groves->total() }}
                </span>

                <span class="groves-summary-label">
                    Grove
                </span>

            </div>

        </div>


        {{-- =====================================================
             EMPTY
        ====================================================== --}}

        @if ($groves->isEmpty())

            <div class="groves-empty">

                <div class="groves-empty-icon">
                    🌱
                </div>

                <h2>
                    Bạn chưa có Grove nào
                </h2>

                <p>
                    Khi một Terrarium đủ điều kiện
                    được giao thành công,
                    Grove sẽ tự động xuất hiện tại đây.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="groves-primary-button"
                >
                    Khám phá Terrarium
                </a>

            </div>

        @else


            {{-- =================================================
                 GRID
            ================================================== --}}

            <div class="groves-grid">

                @foreach ($groves as $grove)

                    <article class="grove-card">


                        {{-- IMAGE / PLACEHOLDER --}}

                        <div class="grove-card-image">

                            @if ($grove->cover_image)

                                <img
                                    src="{{ asset($grove->cover_image) }}"
                                    alt="{{ $grove->terrarium_name }}"
                                >

                            @else

                                <div class="grove-card-placeholder">
                                    🌿
                                </div>

                            @endif


                            <span
                                class="
                                    grove-status
                                    grove-status-{{ $grove->status }}
                                "
                            >
                                {{
                                    $statusLabels[$grove->status]
                                    ?? ucfirst($grove->status)
                                }}
                            </span>

                        </div>


                        {{-- BODY --}}

                        <div class="grove-card-body">

                            <div class="grove-code">
                                {{ $grove->grove_code }}
                            </div>


                            <h2 class="grove-name">
                                {{ $grove->terrarium_name }}
                            </h2>


                            <div class="grove-meta">

                                <div class="grove-meta-item">

                                    <span class="grove-meta-label">
                                        Hệ sinh thái
                                    </span>

                                    <strong>
                                        {{
                                            $ecosystemLabels[
                                                $grove->ecosystem_type
                                            ]
                                            ?? ucfirst(
                                                $grove->ecosystem_type
                                            )
                                        }}
                                    </strong>

                                </div>


                                <div class="grove-meta-item">

                                    <span class="grove-meta-label">
                                        Mức chăm sóc
                                    </span>

                                    <strong>
                                        {{
                                            $grove->care_level
                                                ? (
                                                    $careLevelLabels[
                                                        $grove->care_level
                                                    ]
                                                    ?? ucfirst(
                                                        $grove->care_level
                                                    )
                                                )
                                                : 'Chưa cập nhật'
                                        }}
                                    </strong>

                                </div>


                                <div class="grove-meta-item">

                                    <span class="grove-meta-label">
                                        Ngày nhận
                                    </span>

                                    <strong>
                                        {{
                                            $grove->received_at
                                                ? $grove
                                                    ->received_at
                                                    ->format('d/m/Y')
                                                : 'Chưa cập nhật'
                                        }}
                                    </strong>

                                </div>

                            </div>


                            @if ($grove->current_condition)

                                <div class="grove-condition">

                                    <span>
                                        Tình trạng hiện tại
                                    </span>

                                    <strong>
                                        {{ $grove->current_condition }}
                                    </strong>

                                </div>

                            @endif


                            <a
                                href="{{
                                    route(
                                        'groves.show',
                                        $grove->id
                                    )
                                }}"
                                class="groves-detail-button"
                            >
                                Xem chi tiết
                                <span>→</span>
                            </a>

                        </div>

                    </article>

                @endforeach

            </div>


            {{-- =================================================
                 PAGINATION
            ================================================== --}}

            @if ($groves->hasPages())

                <div class="groves-pagination">

                    @if ($groves->onFirstPage())

                        <span
                            class="
                                groves-page-button
                                disabled
                            "
                        >
                            ← Trang trước
                        </span>

                    @else

                        <a
                            href="{{ $groves->previousPageUrl() }}"
                            class="groves-page-button"
                        >
                            ← Trang trước
                        </a>

                    @endif


                    <span class="groves-page-info">

                        Trang
                        {{ $groves->currentPage() }}
                        /
                        {{ $groves->lastPage() }}

                    </span>


                    @if ($groves->hasMorePages())

                        <a
                            href="{{ $groves->nextPageUrl() }}"
                            class="groves-page-button"
                        >
                            Trang sau →
                        </a>

                    @else

                        <span
                            class="
                                groves-page-button
                                disabled
                            "
                        >
                            Trang sau →
                        </span>

                    @endif

                </div>

            @endif

        @endif

    </div>

</section>

@endsection