@extends('layouts.customer')


@section(
    'title',
    'Chi tiết Grove - MiniGrove'
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


<section class="grove-detail-page">

    <div class="customer-container">


        {{-- =====================================================
             BACK
        ====================================================== --}}

        <div class="grove-detail-back">

            <a
                href="{{ route('groves.index') }}"
                class="grove-back-link"
            >
                ← Quay lại My Grove
            </a>

        </div>


        {{-- =====================================================
             HERO
        ====================================================== --}}

        <div class="grove-detail-hero">


            {{-- IMAGE --}}

            <div class="grove-detail-image">

                @if ($grove->cover_image)

                    <img
                        src="{{ asset($grove->cover_image) }}"
                        alt="{{ $grove->terrarium_name }}"
                    >

                @else

                    <div class="grove-detail-placeholder">
                        🌿
                    </div>

                @endif

            </div>


            {{-- INFORMATION --}}

            <div class="grove-detail-intro">

                <div class="grove-detail-topline">

                    <span class="grove-code">
                        {{ $grove->grove_code }}
                    </span>


                    <span
                        class="
                            grove-status
                            grove-detail-status
                            grove-status-{{ $grove->status }}
                        "
                    >
                        {{
                            $statusLabels[$grove->status]
                            ?? ucfirst($grove->status)
                        }}
                    </span>

                </div>


                <h1>
                    {{ $grove->terrarium_name }}
                </h1>


                <p class="grove-detail-description">
                    Hệ sinh thái MiniGrove thuộc bộ sưu tập
                    của bạn.
                </p>


                <div class="grove-detail-overview">

                    <div class="grove-overview-item">

                        <span>
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


                    <div class="grove-overview-item">

                        <span>
                            Tình trạng
                        </span>

                        <strong>
                            {{
                                $grove->current_condition
                                ?: 'Chưa cập nhật'
                            }}
                        </strong>

                    </div>


                    <div class="grove-overview-item">

                        <span>
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


                    <div class="grove-overview-item">

                        <span>
                            Ngày kích hoạt
                        </span>

                        <strong>
                            {{
                                $grove->activated_at
                                    ? $grove
                                        ->activated_at
                                        ->format('d/m/Y')
                                    : 'Chưa cập nhật'
                            }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             CARE INFORMATION
        ====================================================== --}}

        <div class="grove-detail-section">

            <div class="grove-section-heading">

                <div>

                    <span class="groves-eyebrow">
                        🌱 CHĂM SÓC
                    </span>

                    <h2>
                        Hướng dẫn chăm sóc
                    </h2>

                </div>

            </div>


            <div class="grove-care-grid">


                <div class="grove-care-card">

                    <div class="grove-care-icon">
                        🌿
                    </div>

                    <span>
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


                <div class="grove-care-card">

                    <div class="grove-care-icon">
                        ☀️
                    </div>

                    <span>
                        Ánh sáng phù hợp
                    </span>

                    <strong>
                        {{
                            $grove->light_requirement
                            ?: 'Chưa cập nhật'
                        }}
                    </strong>

                </div>


                <div class="grove-care-card">

                    <div class="grove-care-icon">
                        📅
                    </div>

                    <span>
                        Chu kỳ chăm sóc
                    </span>

                    <strong>

                        @if ($grove->care_cycle_days)

                            Mỗi
                            {{ $grove->care_cycle_days }}
                            ngày

                        @else

                            Chưa cập nhật

                        @endif

                    </strong>

                </div>

            </div>

        </div>


        {{-- =====================================================
             COMPONENT SNAPSHOT
        ====================================================== --}}

        <div class="grove-detail-section">

            <div class="grove-section-heading">

                <div>

                    <span class="groves-eyebrow">
                        🪴 THÀNH PHẦN
                    </span>

                    <h2>
                        Thành phần Grove
                    </h2>

                    <p>
                        Thông tin thành phần được lưu lại
                        tại thời điểm Terrarium được tạo.
                    </p>

                </div>

            </div>


            @if (
                $grove->plant_snapshot
                ||
                $grove->moss_snapshot
                ||
                $grove->substrate_snapshot
                ||
                $grove->stone_snapshot
                ||
                $grove->accessory_snapshot
            )

                <div class="grove-components-grid">


                    @if ($grove->plant_snapshot)

                        <div class="grove-component-card">

                            <span class="grove-component-icon">
                                🌱
                            </span>

                            <strong>
                                Cây
                            </strong>

                            <pre>{{
                                json_encode(
                                    $grove->plant_snapshot,
                                    JSON_PRETTY_PRINT
                                    | JSON_UNESCAPED_UNICODE
                                )
                            }}</pre>

                        </div>

                    @endif


                    @if ($grove->moss_snapshot)

                        <div class="grove-component-card">

                            <span class="grove-component-icon">
                                🌿
                            </span>

                            <strong>
                                Rêu
                            </strong>

                            <pre>{{
                                json_encode(
                                    $grove->moss_snapshot,
                                    JSON_PRETTY_PRINT
                                    | JSON_UNESCAPED_UNICODE
                                )
                            }}</pre>

                        </div>

                    @endif


                    @if ($grove->substrate_snapshot)

                        <div class="grove-component-card">

                            <span class="grove-component-icon">
                                🪴
                            </span>

                            <strong>
                                Đất nền
                            </strong>

                            <pre>{{
                                json_encode(
                                    $grove->substrate_snapshot,
                                    JSON_PRETTY_PRINT
                                    | JSON_UNESCAPED_UNICODE
                                )
                            }}</pre>

                        </div>

                    @endif


                    @if ($grove->stone_snapshot)

                        <div class="grove-component-card">

                            <span class="grove-component-icon">
                                🪨
                            </span>

                            <strong>
                                Đá
                            </strong>

                            <pre>{{
                                json_encode(
                                    $grove->stone_snapshot,
                                    JSON_PRETTY_PRINT
                                    | JSON_UNESCAPED_UNICODE
                                )
                            }}</pre>

                        </div>

                    @endif


                    @if ($grove->accessory_snapshot)

                        <div class="grove-component-card">

                            <span class="grove-component-icon">
                                ✨
                            </span>

                            <strong>
                                Phụ kiện
                            </strong>

                            <pre>{{
                                json_encode(
                                    $grove->accessory_snapshot,
                                    JSON_PRETTY_PRINT
                                    | JSON_UNESCAPED_UNICODE
                                )
                            }}</pre>

                        </div>

                    @endif

                </div>

            @else

                <div class="grove-components-empty">

                    <div class="grove-components-empty-icon">
                        🪴
                    </div>

                    <div>

                        <strong>
                            Chưa có dữ liệu thành phần
                        </strong>

                        <p>
                            Grove này chưa có snapshot chi tiết
                            về cây, rêu, đất nền, đá hoặc phụ kiện.
                        </p>

                    </div>

                </div>

            @endif

        </div>


        {{-- =====================================================
             BASIC INFO
        ====================================================== --}}

        <div class="grove-detail-section">

            <div class="grove-section-heading">

                <div>

                    <span class="groves-eyebrow">
                        📋 THÔNG TIN
                    </span>

                    <h2>
                        Thông tin Grove
                    </h2>

                </div>

            </div>


            <div class="grove-info-table">

                <div class="grove-info-row">

                    <span>
                        Mã Grove
                    </span>

                    <strong>
                        {{ $grove->grove_code }}
                    </strong>

                </div>


                <div class="grove-info-row">

                    <span>
                        Tên Terrarium
                    </span>

                    <strong>
                        {{ $grove->terrarium_name }}
                    </strong>

                </div>


                <div class="grove-info-row">

                    <span>
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


                <div class="grove-info-row">

                    <span>
                        Trạng thái
                    </span>

                    <strong>
                        {{
                            $statusLabels[
                                $grove->status
                            ]
                            ?? ucfirst(
                                $grove->status
                            )
                        }}
                    </strong>

                </div>


                <div class="grove-info-row">

                    <span>
                        Tình trạng hiện tại
                    </span>

                    <strong>
                        {{
                            $grove->current_condition
                            ?: 'Chưa cập nhật'
                        }}
                    </strong>

                </div>

            </div>

        </div>


    </div>

</section>

@endsection