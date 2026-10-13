@extends('layouts.customer')


@section(
    'title',
    $product->name . ' - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/products.css') }}"
>

@endpush


@section('content')

@php

    $primaryImage =
        $product->images
            ->firstWhere(
                'is_primary',
                true
            )
        ?? $product->images->first();

    $studioQueryKey = [
        'container' => 'container',
        'plant' => 'plant',
        'moss' => 'moss',
        'substrate' => 'substrate',
        'stone' => 'stone',
        'accessory' => 'accessory',
    ][$product->product_type] ?? null;

    $studioActionLabel = [
        'container' => 'Dùng bình này trong Studio',
        'plant' => 'Dùng cây này trong Studio',
        'moss' => 'Dùng rêu này trong Studio',
        'substrate' => 'Dùng nền này trong Studio',
        'stone' => 'Dùng đá này trong Studio',
        'accessory' => 'Dùng phụ kiện này trong Studio',
    ][$product->product_type] ?? 'Dùng sản phẩm này trong Studio';

@endphp


<section class="product-detail-page">

    <div class="customer-container">

    <a
    href="{{ route('products.index') }}"
    class="product-back-link"
>
    ← Quay lại sản phẩm
</a>
        <div class="product-detail-grid">


            {{-- LEFT: IMAGE --}}
            <div>

                <div class="product-gallery-main">

                    @if ($primaryImage)

                        <img
                            id="product-main-image"
                            src="{{
                                asset(
                                    'storage/'
                                    . $primaryImage->image_path
                                )
                            }}"
                            alt="{{ $product->name }}"
                        >

                    @else

                        <div class="product-gallery-empty">
                            🌱
                        </div>

                    @endif

                </div>


                @if ($product->images->count() > 1)

                    <div class="product-thumbnails">

                        @foreach (
                            $product->images
                            as $image
                        )

                            <button
                                type="button"
                                class="product-thumbnail{{ $primaryImage?->id === $image->id ? ' active' : '' }}"
                                data-gallery-src="{{ asset('storage/' . $image->image_path) }}"
                                data-gallery-alt="{{ $product->name }} - ảnh {{ $loop->iteration }}"
                                aria-label="Xem ảnh {{ $loop->iteration }} của {{ $product->name }}"
                                aria-pressed="{{ $primaryImage?->id === $image->id ? 'true' : 'false' }}"
                            >

                                <img
                                    src="{{ asset('storage/' . $image->image_path) }}"
                                    alt="{{ $product->name }} - ảnh {{ $loop->iteration }}"
                                >

                            </button>

                        @endforeach

                    </div>

                @endif

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const mainImage = document.getElementById('product-main-image');
                        const thumbnails = document.querySelectorAll('.product-thumbnail[data-gallery-src]');

                        if (!mainImage || thumbnails.length === 0) {
                            return;
                        }

                        thumbnails.forEach(function (thumbnail) {
                            thumbnail.addEventListener('click', function () {
                                const nextSrc = thumbnail.dataset.gallerySrc;
                                const nextAlt = thumbnail.dataset.galleryAlt;

                                if (!nextSrc) {
                                    return;
                                }

                                mainImage.src = nextSrc;
                                mainImage.alt = nextAlt || mainImage.alt;

                                thumbnails.forEach(function (item) {
                                    item.classList.remove('active');
                                    item.setAttribute('aria-pressed', 'false');
                                });

                                thumbnail.classList.add('active');
                                thumbnail.setAttribute('aria-pressed', 'true');
                            });
                        });
                    });
                </script>

            </div>


            {{-- RIGHT: INFO --}}
            <div>

                <div class="product-detail-category">

                    {{
                        $product->category?->name
                        ?? 'MiniGrove'
                    }}

                </div>


                <h1 class="product-detail-title">
                    {{ $product->name }}
                </h1>


                <div class="product-detail-sku">
                    SKU: {{ $product->sku }}
                </div>


                <div class="product-detail-price">

                    @if (
                        $product->sale_price !== null
                    )

                        <div class="detail-sale-price">

                            {{
                                number_format(
                                    (float)
                                    $product->sale_price,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </div>

                        <div class="detail-old-price">

                            {{
                                number_format(
                                    (float)
                                    $product->list_price,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </div>

                    @else

                        <div class="detail-normal-price">

                            {{
                                number_format(
                                    (float)
                                    $product->list_price,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </div>

                    @endif

                </div>


                <div class="product-detail-info">


                    <div class="detail-info-item">

                        <div class="detail-info-label">
                            Hệ sinh thái
                        </div>

                        <div class="detail-info-value">

                            @switch(
                                $product->ecosystem_type
                            )

                                @case('closed')
                                    Hệ kín
                                    @break

                                @case('open')
                                    Hệ mở
                                    @break

                                @case('dry')
                                    Hệ khô
                                    @break

                                @default
                                    {{
                                        $product
                                            ->ecosystem_type
                                    }}

                            @endswitch

                        </div>

                    </div>


                    <div class="detail-info-item">

                        <div class="detail-info-label">
                            Mức chăm sóc
                        </div>

                        <div class="detail-info-value">

                            @switch(
                                $product->care_level
                            )

                                @case('easy')
                                    Dễ chăm
                                    @break

                                @case('medium')
                                    Trung bình
                                    @break

                                @case('high')
                                    Cần chăm sóc kỹ
                                    @break

                                @default
                                    {{
                                        $product
                                            ->care_level
                                    }}

                            @endswitch

                        </div>

                    </div>


                    <div class="detail-info-item">

                        <div class="detail-info-label">
                            Kích thước
                        </div>

                        <div class="detail-info-value">

                            {{
                                $product->size
                                ?: 'Đang cập nhật'
                            }}

                        </div>

                    </div>


                    <div class="detail-info-item">

                        <div class="detail-info-label">
                            Chu kỳ chăm sóc
                        </div>

                        <div class="detail-info-value">

                            @if (
                                $product->care_cycle_days
                            )

                                {{
                                    $product
                                        ->care_cycle_days
                                }}
                                ngày

                            @else

                                Theo hướng dẫn

                            @endif

                        </div>

                    </div>

                </div>


                @if ($product->description)

                    <div class="product-description">

                        {{
                            $product->description
                        }}

                    </div>

                @endif


                @if ($product->stock_quantity > 0)

                    <div class="product-stock-status">

                        Còn
                        {{ $product->stock_quantity }}
                        sản phẩm

                    </div>

                @else

                    <div
                        class="
                            product-stock-status
                            out
                        "
                    >

                        Sản phẩm hiện đã hết hàng.

                    </div>

                @endif

<div class="product-purchase-panel">

    @guest

        <div class="product-buy-area">

            <a
                href="{{
                    route(
                        'login',
                        [
                            'redirect'
                                => request()->getRequestUri()
                        ]
                    )
                }}"
                class="
                    product-buy-button
                    secondary
                "
            >
                🛒 Thêm vào giỏ hàng
            </a>

            <a
                href="{{
                    route(
                        'login',
                        [
                            'redirect'
                                => request()->getRequestUri()
                        ]
                    )
                }}"
                class="product-buy-button"
            >
                Mua ngay
            </a>

        </div>

    @else

        @if (
            auth()->user()->role?->name
            === 'customer'
        )

            @if ($product->stock_quantity > 0)

                {{-- SỐ LƯỢNG --}}
                <div class="product-quantity-row">

                    <span class="product-quantity-label">
                        Số lượng
                    </span>

                    <div class="product-quantity-box">

                        <button
                            type="button"
                            class="product-quantity-btn"
                            id="quantity-decrease"
                            aria-label="Giảm số lượng"
                        >
                            −
                        </button>

                        <input
                            type="text"
                            id="product-quantity-display"
                            class="product-quantity-input"
                            value="1"
                            readonly
                            aria-label="Số lượng sản phẩm"
                        >

                        <button
                            type="button"
                            class="product-quantity-btn"
                            id="quantity-increase"
                            aria-label="Tăng số lượng"
                        >
                            +
                        </button>

                    </div>

                    <span class="product-quantity-stock">
                        Còn {{ $product->stock_quantity }} sản phẩm
                    </span>

                </div>


                {{-- MUA HÀNG + YÊU THÍCH --}}
                <div class="product-buy-area">

                    <form
                        method="POST"
                        action="{{
                            route(
                                'cart.add',
                                $product
                            )
                        }}"
                    >

                        @csrf

                        <input
                            type="hidden"
                            name="quantity"
                            id="cart-quantity-input"
                            value="1"
                        >

                        <button
                            type="submit"
                            class="
                                product-buy-button
                                secondary
                            "
                        >
                            🛒 Thêm vào giỏ hàng
                        </button>

                    </form>


                    <form
                        method="POST"
                        action="{{
                            route(
                                'checkout.buy-now',
                                $product
                            )
                        }}"
                    >

                        @csrf

                        <input
                            type="hidden"
                            name="quantity"
                            id="buy-now-quantity-input"
                            value="1"
                        >

                        <button
                            type="submit"
                            class="product-buy-button"
                        >
                            Mua ngay
                        </button>

                    </form>


                    @if ($isWishlisted)

                        <form
                            method="POST"
                            action="{{
                                route(
                                    'wishlist.destroy',
                                    $product
                                )
                            }}"
                            class="product-wishlist-form"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="
                                    product-wishlist-button
                                    active
                                "
                                title="Bỏ khỏi yêu thích"
                                aria-label="Bỏ khỏi yêu thích"
                                aria-pressed="true"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="product-wishlist-icon"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="
                                            M12 21
                                            C10.6 19.7 4 15.2 4 9.5
                                            C4 6.5 6.2 4.5 8.9 4.5
                                            C10.4 4.5 11.5 5.2 12 6
                                            C12.5 5.2 13.6 4.5 15.1 4.5
                                            C17.8 4.5 20 6.5 20 9.5
                                            C20 15.2 13.4 19.7 12 21
                                            Z
                                        "
                                    />
                                </svg>
                            </button>

                        </form>

                    @else

                        <form
                            method="POST"
                            action="{{
                                route(
                                    'wishlist.store',
                                    $product
                                )
                            }}"
                            class="product-wishlist-form"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="product-wishlist-button"
                                title="Thêm vào yêu thích"
                                aria-label="Thêm vào yêu thích"
                                aria-pressed="false"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="product-wishlist-icon"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="
                                            M12 21
                                            C10.6 19.7 4 15.2 4 9.5
                                            C4 6.5 6.2 4.5 8.9 4.5
                                            C10.4 4.5 11.5 5.2 12 6
                                            C12.5 5.2 13.6 4.5 15.1 4.5
                                            C17.8 4.5 20 6.5 20 9.5
                                            C20 15.2 13.4 19.7 12 21
                                            Z
                                        "
                                    />
                                </svg>
                            </button>

                        </form>

                    @endif

                </div>


                <script>
                    document.addEventListener(
                        'DOMContentLoaded',
                        function () {

                            const stock =
                                {{ (int) $product->stock_quantity }};

                            const displayInput =
                                document.getElementById(
                                    'product-quantity-display'
                                );

                            const cartInput =
                                document.getElementById(
                                    'cart-quantity-input'
                                );

                            const buyNowInput =
                                document.getElementById(
                                    'buy-now-quantity-input'
                                );

                            const decreaseButton =
                                document.getElementById(
                                    'quantity-decrease'
                                );

                            const increaseButton =
                                document.getElementById(
                                    'quantity-increase'
                                );

                            if (
                                !displayInput
                                || !cartInput
                                || !buyNowInput
                                || !decreaseButton
                                || !increaseButton
                            ) {
                                return;
                            }

                            let quantity = 1;

                            const syncQuantity = function () {

                                quantity =
                                    Math.max(
                                        1,
                                        Math.min(
                                            quantity,
                                            stock
                                        )
                                    );

                                displayInput.value =
                                    quantity;

                                cartInput.value =
                                    quantity;

                                buyNowInput.value =
                                    quantity;

                                decreaseButton.disabled =
                                    quantity <= 1;

                                increaseButton.disabled =
                                    quantity >= stock;
                            };

                            decreaseButton.addEventListener(
                                'click',
                                function () {

                                    if (quantity > 1) {
                                        quantity--;
                                        syncQuantity();
                                    }

                                }
                            );

                            increaseButton.addEventListener(
                                'click',
                                function () {

                                    if (quantity < stock) {
                                        quantity++;
                                        syncQuantity();
                                    }

                                }
                            );

                            syncQuantity();
                        }
                    );
                </script>

            @else

                <div class="product-buy-area">

                    <button
                        type="button"
                        class="
                            product-buy-button
                            secondary
                        "
                        disabled
                    >
                        🛒 Hết hàng
                    </button>


                    @if ($isWishlisted)

                        <form
                            method="POST"
                            action="{{
                                route(
                                    'wishlist.destroy',
                                    $product
                                )
                            }}"
                            class="product-wishlist-form"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="
                                    product-wishlist-button
                                    active
                                "
                                title="Bỏ khỏi yêu thích"
                                aria-label="Bỏ khỏi yêu thích"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="product-wishlist-icon"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="
                                            M12 21
                                            C10.6 19.7 4 15.2 4 9.5
                                            C4 6.5 6.2 4.5 8.9 4.5
                                            C10.4 4.5 11.5 5.2 12 6
                                            C12.5 5.2 13.6 4.5 15.1 4.5
                                            C17.8 4.5 20 6.5 20 9.5
                                            C20 15.2 13.4 19.7 12 21
                                            Z
                                        "
                                    />
                                </svg>
                            </button>

                        </form>

                    @else

                        <form
                            method="POST"
                            action="{{
                                route(
                                    'wishlist.store',
                                    $product
                                )
                            }}"
                            class="product-wishlist-form"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="product-wishlist-button"
                                title="Thêm vào yêu thích"
                                aria-label="Thêm vào yêu thích"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="product-wishlist-icon"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="
                                            M12 21
                                            C10.6 19.7 4 15.2 4 9.5
                                            C4 6.5 6.2 4.5 8.9 4.5
                                            C10.4 4.5 11.5 5.2 12 6
                                            C12.5 5.2 13.6 4.5 15.1 4.5
                                            C17.8 4.5 20 6.5 20 9.5
                                            C20 15.2 13.4 19.7 12 21
                                            Z
                                        "
                                    />
                                </svg>
                            </button>

                        </form>

                    @endif

                </div>

            @endif

        @else

            <div class="product-stock-status">

                Chức năng mua hàng dành cho
                tài khoản Customer.

            </div>

        @endif

    @endguest

    @if ($product->is_studio_item && $studioQueryKey)
        <div class="product-studio-callout">
            <div class="product-studio-callout-icon" aria-hidden="true">
                🌿
            </div>

            <div class="product-studio-callout-content">
                <strong>
                    Có thể dùng trong MiniGrove Studio
                </strong>

                <span>
                    Chọn sẵn sản phẩm này và tiếp tục hoàn thiện Terrarium của bạn.
                </span>
            </div>

            <a
                href="{{
                    route(
                        'studio.index',
                        [$studioQueryKey => $product->id]
                    )
                }}"
                class="product-studio-callout-button"
            >
                {{ $studioActionLabel }}
            </a>
        </div>
    @endif

</div>
            </div>

        </div>


        {{-- EXTRA --}}
        <div class="product-extra">


            @if ($product->concept_story)

                <div class="product-extra-card">

                    <h3>
                        Câu chuyện MiniGrove
                    </h3>

                    <p>
                        {{ $product->concept_story }}
                    </p>

                </div>

            @endif


            @if ($product->care_instructions)

                <div class="product-extra-card">

                    <h3>
                        Hướng dẫn chăm sóc
                    </h3>

                    <p>
                        {{
                            $product
                                ->care_instructions
                        }}
                    </p>

                </div>

            @endif

        </div>



{{-- REVIEWS --}}
<section class="product-reviews" id="product-reviews">

    <div class="section-heading">
        <div>
            <div class="section-eyebrow">
                ĐÁNH GIÁ KHÁCH HÀNG
            </div>

            <h2>
                Đánh giá sản phẩm
            </h2>
        </div>
    </div>


    {{-- REVIEW SUMMARY --}}
    <div class="product-review-summary">

        <div class="product-review-score">
            <strong>
                {{ number_format((float) $averageRating, 1) }}
            </strong>

            <span>/ 5</span>
        </div>

        <div
            class="product-review-stars"
            aria-label="Điểm trung bình {{ $averageRating }} trên 5"
        >
            @for ($star = 1; $star <= 5; $star++)
                <span>
                    {{ $star <= round($averageRating) ? '★' : '☆' }}
                </span>
            @endfor
        </div>

        <div class="product-review-count">
            {{ $reviewCount }} đánh giá
        </div>

    </div>


    {{-- REVIEW LIST: COLLAPSIBLE --}}
    <details
        class="product-review-collapsible"
        style="
            margin-top: 18px;
            border: 1px solid #dce8df;
            border-radius: 14px;
            background: #ffffff;
            overflow: hidden;
        "
    >
        <summary
            style="
                cursor: pointer;
                list-style: none;
                padding: 16px 18px;
                font-weight: 700;
                color: #174d39;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            "
        >
            <span>
                Xem đánh giá của khách hàng
                ({{ $reviewCount }})
            </span>

            <span style="font-size: 13px; color: #6d7d75;">
                Bấm để mở / thu gọn
            </span>
        </summary>

        <div
            style="
                padding: 0 18px 18px;
                border-top: 1px solid #eef3ef;
            "
        >
    {{-- REVIEW LIST --}}
    <div class="product-review-list">

        @forelse ($product->reviews as $review)

            <article class="product-review-item">

                <div class="product-review-item-header">

                    <div class="product-review-author">

                        <strong>
                            {{ $review->user?->name ?? 'Khách hàng' }}
                        </strong>

                        <div class="product-review-item-stars">

                            @for ($star = 1; $star <= 5; $star++)

                                <span>
                                    {{ $star <= $review->rating ? '★' : '☆' }}
                                </span>

                            @endfor

                        </div>

                    </div>


                    <time
                        datetime="{{ $review->created_at?->toIso8601String() }}"
                    >
                        {{ $review->created_at?->format('d/m/Y') }}
                    </time>

                </div>


                @if ($review->comment)

                    <p class="product-review-comment">
                        {{ $review->comment }}
                    </p>

                @endif


                {{-- REVIEW IMAGES --}}
                @if ($review->images->isNotEmpty())

                    <div class="product-review-images">

                        @foreach ($review->images as $reviewImage)

                            <img
                                src="{{ asset('storage/' . $reviewImage->image_path) }}"
                                alt="Ảnh đánh giá của khách hàng"
                            >

                        @endforeach

                    </div>

                @endif


                {{-- STAFF / ADMIN REPLIES --}}
                @if ($review->replies->isNotEmpty())

                    <div class="product-review-replies">

                        @foreach ($review->replies as $reply)

                            @if ($reply->is_visible)

                                <div class="product-review-reply">

                                    <strong>
                                        {{ $reply->user?->name ?? 'MiniGrove' }}
                                    </strong>

                                    <p>
                                        {{ $reply->content }}
                                    </p>

                                </div>

                            @endif

                        @endforeach

                    </div>

                @endif

            </article>

        @empty

            <div class="product-review-empty">

                <p>
                    Sản phẩm chưa có đánh giá nào.
                </p>

                <p>
                    Hãy là người đầu tiên chia sẻ trải nghiệm của bạn.
                </p>

            </div>

        @endforelse

    </div>
        </div>
    </details>

</section>


{{-- RELATED --}}

        {{-- RELATED --}}
        @if ($relatedProducts->isNotEmpty())

            <div class="related-products">

                <div class="section-heading">

                    <div>

                        <div class="section-eyebrow">
                            CÓ THỂ BẠN SẼ THÍCH
                        </div>

                        <h2>
                            Sản phẩm liên quan
                        </h2>

                    </div>

                </div>


                <div class="product-grid">

                    @foreach (
                        $relatedProducts
                        as $relatedProduct
                    )

                        @include(
                            'partials.product-card',
                            [
                                'product'
                                    => $relatedProduct
                            ]
                        )

                    @endforeach

                </div>

            </div>

        @endif

    </div>

</section>

@endsection