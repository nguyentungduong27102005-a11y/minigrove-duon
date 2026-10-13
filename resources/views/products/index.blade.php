@extends('layouts.customer')


@php

    $selectedCategory =
        $category
            ? $categories->firstWhere(
                'slug',
                $category
            )
            : null;

    $selectedStudioTypeLabel =
        $isStudioType
            ? ($studioTypeLabels[$type] ?? null)
            : null;

@endphp


@section(
    'title',
    $selectedStudioTypeLabel
        ? $selectedStudioTypeLabel . ' - MiniGrove Studio'
        : (
            $selectedCategory
                ? $selectedCategory->name . ' - MiniGrove'
                : 'Sản phẩm - MiniGrove'
        )
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/products.css') }}"
>

@endpush


@section('content')


<section class="shop-page">

    <div class="customer-container">

        @if ($selectedStudioTypeLabel)
            <a
                href="{{ route('studio.index') }}"
                class="product-back-link"
            >
                <span aria-hidden="true">←</span>
                Quay lại Thiết kế của bạn
            </a>
        @elseif ($selectedCategory)
            <a
                href="{{ route('home') }}#categories"
                class="product-back-link"
            >
                <span aria-hidden="true">←</span>
                Quay lại danh mục
            </a>
        @endif


        {{-- =====================================================
             HEADING
        ====================================================== --}}

        <div class="shop-heading">

            <h1>

                {{
                    $selectedStudioTypeLabel
                        ? $selectedStudioTypeLabel . ' cho MiniGrove Studio'
                        : (
                            $selectedCategory
                                ? $selectedCategory->name
                                : 'Sản phẩm MiniGrove'
                        )
                }}

            </h1>


            <p>

                @if ($selectedStudioTypeLabel)

                    Tìm kiếm và chọn
                    <strong>
                        {{ mb_strtolower($selectedStudioTypeLabel) }}
                    </strong>
                    để sử dụng trong thiết kế Terrarium.

                @elseif ($selectedCategory)

                    Khám phá các sản phẩm thuộc chủ đề
                    <strong>
                        {{ $selectedCategory->name }}
                    </strong>.

                @else

                    Khám phá các hệ sinh thái mini
                    dành cho không gian của bạn.

                @endif

            </p>

        </div>


        {{-- =====================================================
             FILTER
        ====================================================== --}}

        <form
    method="GET"
    action="{{ route('products.index') }}"
    class="
        shop-filter
        {{ $selectedCategory ? 'shop-filter-category' : '' }}
    "
>

            @if ($isStudioType)
                <input
                    type="hidden"
                    name="type"
                    value="{{ $type }}"
                >
            @endif

            <input
                type="text"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="Tên sản phẩm, SKU..."
            >


            {{-- CATEGORY --}}

            @if ($isStudioType)

                {{-- Studio dùng product_type thay cho Category. --}}

            @elseif ($selectedCategory)

    <input
        type="hidden"
        name="category"
        value="{{ $selectedCategory->slug }}"
    >

@else

    <select name="category">

        <option value="">
            Tất cả danh mục
        </option>

        @foreach ($categories as $item)

            <option
                value="{{ $item->slug }}"
                @selected(
                    $category === $item->slug
                )
            >
                {{ $item->name }}
            </option>

        @endforeach

    </select>

@endif


            {{-- ECOSYSTEM --}}

            <select name="ecosystem">

                <option value="">
                    Tất cả hệ sinh thái
                </option>


                <option
                    value="closed"
                    @selected(
                        $ecosystem === 'closed'
                    )
                >
                    Hệ kín
                </option>


                <option
                    value="open"
                    @selected(
                        $ecosystem === 'open'
                    )
                >
                    Hệ mở
                </option>


                <option
                    value="dry"
                    @selected(
                        $ecosystem === 'dry'
                    )
                >
                    Hệ khô
                </option>

            </select>


            {{-- CARE LEVEL --}}

            <select name="care">

                <option value="">
                    Mức chăm sóc
                </option>


                <option
                    value="easy"
                    @selected(
                        $care === 'easy'
                    )
                >
                    Dễ chăm
                </option>


                <option
                    value="medium"
                    @selected(
                        $care === 'medium'
                    )
                >
                    Trung bình
                </option>


                <option
                    value="high"
                    @selected(
                        $care === 'high'
                    )
                >
                    Cần chăm kỹ
                </option>

            </select>


            {{-- SORT --}}

            <select name="sort">

                <option
                    value="newest"
                    @selected(
                        $sort === 'newest'
                    )
                >
                    Mới nhất
                </option>


                <option
                    value="featured"
                    @selected(
                        $sort === 'featured'
                    )
                >
                    Nổi bật
                </option>


                <option
                    value="price_asc"
                    @selected(
                        $sort === 'price_asc'
                    )
                >
                    Giá thấp → cao
                </option>


                <option
                    value="price_desc"
                    @selected(
                        $sort === 'price_desc'
                    )
                >
                    Giá cao → thấp
                </option>

            </select>


            {{-- SUBMIT --}}

            <button
                type="submit"
                class="shop-filter-button"
            >
                Tìm / Lọc
            </button>


            {{-- RESET --}}

            @if (
    $keyword
    || $ecosystem
    || $care
    || $sort !== 'newest'
)

                <a
    href="{{
        $isStudioType
            ? route(
                'products.index',
                ['type' => $type]
            )
            : (
                $selectedCategory
                    ? route(
                        'products.index',
                        [
                            'category'
                                => $selectedCategory->slug
                        ]
                    )
                    : route('products.index')
            )
    }}"
    class="shop-filter-reset"
>
    Xóa lọc
</a>
            @endif

        </form>


        {{-- =====================================================
             RESULT INFO
        ====================================================== --}}

        <div class="shop-result-info">

            <span>

                Tìm thấy

                <strong>
                    {{ $products->total() }}
                </strong>

                sản phẩm

                @if ($selectedCategory)

                    trong danh mục

                    <strong>
                        {{ $selectedCategory->name }}
                    </strong>

                @endif

                @if ($selectedStudioTypeLabel)

                    trong nhóm

                    <strong>
                        {{ $selectedStudioTypeLabel }}
                    </strong>

                @endif

            </span>

        </div>


        {{-- =====================================================
             PRODUCT GRID
        ====================================================== --}}

        <div class="product-grid">

            @forelse ($products as $product)

                @include(
                    'partials.product-card',
                    [
                        'product' => $product
                    ]
                )

            @empty

                <div class="home-empty">

                    @if ($selectedCategory)

                        Hiện chưa có sản phẩm phù hợp
                        trong danh mục
                        {{ $selectedCategory->name }}.

                    @else

                        Không tìm thấy sản phẩm phù hợp.

                    @endif

                </div>

            @endforelse

        </div>


        {{-- =====================================================
             PAGINATION
        ====================================================== --}}

        @if ($products->hasPages())

            <div class="shop-pagination">


                {{-- PREVIOUS --}}

                <a
                    href="{{
                        $products->previousPageUrl()
                            ?? '#'
                    }}"
                    class="
                        page-item
                        {{
                            $products->onFirstPage()
                                ? 'disabled'
                                : ''
                        }}
                    "
                >
                    ←
                </a>


                {{-- PAGE NUMBERS --}}

                @for (
                    $page = 1;
                    $page <= $products->lastPage();
                    $page++
                )

                    <a
                        href="{{
                            $products->url($page)
                        }}"
                        class="
                            page-item
                            {{
                                $page
                                === $products->currentPage()
                                    ? 'active'
                                    : ''
                            }}
                        "
                    >
                        {{ $page }}
                    </a>

                @endfor


                {{-- NEXT --}}

                <a
                    href="{{
                        $products->nextPageUrl()
                            ?? '#'
                    }}"
                    class="
                        page-item
                        {{
                            !$products->hasMorePages()
                                ? 'disabled'
                                : ''
                        }}
                    "
                >
                    →
                </a>

            </div>

        @endif


    </div>

</section>

@endsection