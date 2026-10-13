@extends('layouts.customer')

@section('title', 'Sản phẩm yêu thích - MiniGrove')


@push('styles')

<style>

.wishlist-page {
    min-height: 700px;
    padding: 42px 0 70px;
    background: #f7f9f8;
}

.wishlist-box {
    padding: 28px;

    background: #fff;

    border: 1px solid #e3e9e5;
    border-radius: 14px;
}

.wishlist-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 25px;
    padding-bottom: 20px;

    border-bottom: 1px solid #edf1ef;
}

.wishlist-heading h1 {
    margin: 0 0 6px;

    color: #173f30;

    font-size: 27px;
}

.wishlist-heading p {
    margin: 0;

    color: #75827c;

    font-size: 14px;
}

.wishlist-count {
    flex-shrink: 0;

    padding: 8px 13px;

    border-radius: 999px;

    background: #edf6f1;

    color: #174d37;

    font-size: 13px;
    font-weight: 700;
}


/* =========================================================
   ALERT
========================================================= */

.wishlist-alert {
    margin-bottom: 20px;

    padding: 13px 16px;

    border: 1px solid #cbe4d5;
    border-radius: 9px;

    background: #eef8f2;

    color: #17623f;

    font-size: 13px;
}


/* =========================================================
   GRID
========================================================= */

.wishlist-grid {
    display: grid;

    grid-template-columns:
        repeat(
            4,
            minmax(0, 1fr)
        );

    gap: 20px;
}


/* =========================================================
   CARD
========================================================= */

.wishlist-card {
    position: relative;

    overflow: hidden;

    border: 1px solid #e1e8e4;
    border-radius: 12px;

    background: #fff;

    transition:
        transform .2s ease,
        box-shadow .2s ease;
}

.wishlist-card:hover {
    transform: translateY(-3px);

    box-shadow:
        0 10px 26px
        rgba(25, 68, 49, .09);
}


/* =========================================================
   IMAGE
========================================================= */

.wishlist-image-wrap {
    position: relative;

    display: block;

    aspect-ratio: 1 / 1;

    overflow: hidden;

    background: #f5f8f6;
}

.wishlist-image-wrap img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    transition: transform .25s ease;
}

.wishlist-card:hover
.wishlist-image-wrap img {
    transform: scale(1.035);
}

.wishlist-image-empty {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #809088;

    font-size: 45px;
}


/* =========================================================
   STATUS
========================================================= */

.wishlist-status {
    position: absolute;

    top: 11px;
    left: 11px;

    z-index: 5;

    padding: 6px 9px;

    border-radius: 999px;

    font-size: 10px;
    font-weight: 700;
}

.wishlist-status.available {
    background: rgba(232, 247, 238, .95);

    color: #17623f;
}

.wishlist-status.out {
    background: rgba(255, 243, 226, .96);

    color: #976100;
}

.wishlist-status.inactive {
    background: rgba(255, 237, 237, .96);

    color: #b13c3c;
}


/* =========================================================
   REMOVE HEART
========================================================= */

.wishlist-remove-form {
    position: absolute;

    top: 10px;
    right: 10px;

    z-index: 6;
}

.wishlist-heart {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 0;
    border-radius: 50%;

    background: rgba(255, 255, 255, .94);

    color: #d94a59;

    font-size: 20px;

    cursor: pointer;

    box-shadow:
        0 3px 12px
        rgba(0, 0, 0, .09);
}

.wishlist-heart:hover {
    background: #fff0f2;
}


/* =========================================================
   INFO
========================================================= */

.wishlist-info {
    padding: 17px;
}

.wishlist-name {
    display: block;

    min-height: 42px;

    margin-bottom: 8px;

    color: #21392e;

    font-size: 15px;
    font-weight: 700;

    line-height: 1.4;

    text-decoration: none;
}

.wishlist-name:hover {
    color: #174d37;

    text-decoration: underline;
}

.wishlist-sku {
    margin-bottom: 11px;

    color: #8a958f;

    font-size: 11px;
}

.wishlist-price-row {
    min-height: 47px;

    margin-bottom: 13px;
}

.wishlist-sale-price {
    color: #174d37;

    font-size: 19px;
    font-weight: 800;
}

.wishlist-list-price {
    display: block;

    margin-top: 3px;

    color: #9ba49f;

    font-size: 12px;

    text-decoration: line-through;
}


/* =========================================================
   ACTIONS
========================================================= */

.wishlist-actions {
    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 8px;
}

.wishlist-button {
    min-height: 40px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    box-sizing: border-box;

    padding: 0 10px;

    border: 1px solid #174d37;
    border-radius: 8px;

    background: #174d37;

    color: #fff;

    font-size: 12px;
    font-weight: 700;

    text-align: center;
    text-decoration: none;

    cursor: pointer;
}

.wishlist-button:hover {
    background: #123e2d;
}

.wishlist-button.secondary {
    background: #fff;

    color: #174d37;
}

.wishlist-button.secondary:hover {
    background: #f0f7f3;
}

.wishlist-button.disabled {
    border-color: #d7dfdb;

    background: #eef1ef;

    color: #909b95;

    cursor: not-allowed;
}


/* =========================================================
   EMPTY
========================================================= */

.wishlist-empty {
    padding: 80px 25px;

    text-align: center;
}

.wishlist-empty-icon {
    margin-bottom: 16px;

    color: #174d37;

    font-size: 55px;
}

.wishlist-empty h2 {
    margin: 0 0 8px;

    color: #173f30;

    font-size: 22px;
}

.wishlist-empty p {
    margin: 0 0 24px;

    color: #7d8983;

    font-size: 14px;
}

.wishlist-shop-button {
    min-height: 44px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    padding: 0 20px;

    border-radius: 9px;

    background: #174d37;

    color: #fff;

    font-size: 13px;
    font-weight: 700;

    text-decoration: none;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1050px) {

    .wishlist-grid {
        grid-template-columns:
            repeat(
                3,
                minmax(0, 1fr)
            );
    }
}

@media (max-width: 780px) {

    .wishlist-grid {
        grid-template-columns:
            repeat(
                2,
                minmax(0, 1fr)
            );
    }

    .wishlist-heading {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media (max-width: 480px) {

    .wishlist-grid {
        grid-template-columns: 1fr;
    }

    .wishlist-box {
        padding: 18px;
    }
}

</style>

@endpush


@section('content')

<section class="wishlist-page">

    <div class="customer-container">

        <div class="wishlist-box">


            {{-- =================================================
                 HEADING
            ================================================== --}}

            <div class="wishlist-heading">

                <div>

                    <h1>
                        Sản phẩm yêu thích
                    </h1>

                    <p>
                        Những sản phẩm bạn đã lưu
                        để xem lại sau.
                    </p>

                </div>


                <div class="wishlist-count">

                    {{
                        $wishlist
                            ->items
                            ->count()
                    }}

                    sản phẩm

                </div>

            </div>


            


            {{-- =================================================
                 LIST
            ================================================== --}}

            @if (
                $wishlist
                    ->items
                    ->isEmpty()
            )


                <div class="wishlist-empty">

                    <div class="wishlist-empty-icon">
                        ♡
                    </div>

                    <h2>
                        Danh sách yêu thích đang trống
                    </h2>

                    <p>
                        Hãy lưu những Terrarium bạn thích
                        để dễ dàng tìm lại sau.
                    </p>

                    <a
                        href="{{ route('products.index') }}"
                        class="wishlist-shop-button"
                    >
                        Khám phá sản phẩm
                    </a>

                </div>


            @else


                <div class="wishlist-grid">


                    @foreach (
                        $wishlist->items
                        as $item
                    )


                        @php

                            $product =
                                $item->product;


                            if (!$product) {
                                continue;
                            }


                            $primaryImage =
                                $product
                                    ->images
                                    ->firstWhere(
                                        'is_primary',
                                        true
                                    )
                                ??
                                $product
                                    ->images
                                    ->first();


                            $listPrice =
                                (float)
                                $product
                                    ->list_price;


                            $salePrice =
                                $product
                                    ->sale_price !== null
                                    ?
                                    (float)
                                    $product
                                        ->sale_price
                                    :
                                    null;


                            $currentPrice =
                                $salePrice !== null
                                &&
                                $salePrice > 0
                                &&
                                $salePrice < $listPrice
                                    ?
                                    $salePrice
                                    :
                                    $listPrice;


                            $hasSale =
                                $currentPrice
                                < $listPrice;


                            $available =
                                $product->is_active
                                &&
                                (int)
                                $product
                                    ->stock_quantity
                                > 0;

                        @endphp


                        <article class="wishlist-card">


                            {{-- REMOVE --}}

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'wishlist.destroy',
                                        $product
                                    )
                                }}"
                                class="wishlist-remove-form"
                                onsubmit="
                                    return confirm(
                                        'Bạn muốn bỏ sản phẩm này khỏi danh sách yêu thích?'
                                    );
                                "
                            >

                                @csrf
                                @method('DELETE')


                                <button
                                    type="submit"
                                    class="wishlist-heart"
                                    title="Bỏ yêu thích"
                                    aria-label="Bỏ yêu thích"
                                >
                                    ♥
                                </button>

                            </form>


                            {{-- IMAGE --}}

                            <a
                                href="{{
                                    route(
                                        'products.show',
                                        $product
                                    )
                                }}"
                                class="wishlist-image-wrap"
                            >


                                @if (
                                    !$product
                                        ->is_active
                                )

                                    <span
                                        class="
                                            wishlist-status
                                            inactive
                                        "
                                    >
                                        Ngừng bán
                                    </span>


                                @elseif (
                                    (int)
                                    $product
                                        ->stock_quantity
                                    <= 0
                                )

                                    <span
                                        class="
                                            wishlist-status
                                            out
                                        "
                                    >
                                        Hết hàng
                                    </span>


                                @else

                                    <span
                                        class="
                                            wishlist-status
                                            available
                                        "
                                    >
                                        Còn hàng
                                    </span>

                                @endif


                                @if ($primaryImage)

                                    <img
                                        src="{{
                                            asset(
                                                'storage/'
                                                .
                                                $primaryImage
                                                    ->image_path
                                            )
                                        }}"
                                        alt="{{ $product->name }}"
                                    >

                                @else

                                    <div
                                        class="
                                            wishlist-image-empty
                                        "
                                    >
                                        🌿
                                    </div>

                                @endif

                            </a>


                            {{-- INFO --}}

                            <div class="wishlist-info">


                                <a
                                    href="{{
                                        route(
                                            'products.show',
                                            $product
                                        )
                                    }}"
                                    class="wishlist-name"
                                >
                                    {{ $product->name }}
                                </a>


                                <div class="wishlist-sku">

                                    SKU:
                                    {{ $product->sku }}

                                </div>


                                <div class="wishlist-price-row">

                                    <div class="wishlist-sale-price">

                                        {{
                                            number_format(
                                                $currentPrice,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}đ

                                    </div>


                                    @if ($hasSale)

                                        <span class="wishlist-list-price">

                                            {{
                                                number_format(
                                                    $listPrice,
                                                    0,
                                                    ',',
                                                    '.'
                                                )
                                            }}đ

                                        </span>

                                    @endif

                                </div>


                                <div class="wishlist-actions">


                                    <a
                                        href="{{
                                            route(
                                                'products.show',
                                                $product
                                            )
                                        }}"
                                        class="
                                            wishlist-button
                                            secondary
                                        "
                                    >
                                        Xem chi tiết
                                    </a>


                                    @if ($available)

                                        <a
                                            href="{{
                                                route(
                                                    'products.show',
                                                    $product
                                                )
                                            }}"
                                            class="wishlist-button"
                                        >
                                            Chọn mua
                                        </a>

                                    @else

                                        <span
                                            class="
                                                wishlist-button
                                                disabled
                                            "
                                        >
                                            Không khả dụng
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>

            @endif

        </div>

    </div>

</section>

@endsection