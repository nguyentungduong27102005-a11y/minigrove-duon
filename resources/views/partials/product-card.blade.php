@php

    $primaryImage =
        $product->images
            ->firstWhere(
                'is_primary',
                true
            )
        ?? $product->images->first();

    $isCardWishlisted =
        in_array(
            $product->id,
            $wishlistProductIds ?? []
        );

@endphp


<article
    class="product-card"
    data-product-card
    data-product-url="{{
        route(
            'products.show',
            $product
        )
    }}"
    tabindex="0"
    aria-label="Xem chi tiết {{ $product->name }}"
>


    <div class="product-image-box">
        {{-- WISHLIST QUICK ACTION --}}

@guest

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
        class="product-card-wishlist"
        title="Đăng nhập để lưu yêu thích"
        aria-label="Đăng nhập để lưu yêu thích"
    >
        <svg
            viewBox="0 0 24 24"
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
    </a>

@else

    @if (
        auth()->user()->role?->name
        === 'customer'
    )

        @if ($isCardWishlisted)

            <form
                method="POST"
                action="{{
                    route(
                        'wishlist.destroy',
                        $product
                    )
                }}"
                class="product-card-wishlist-form"
                data-wishlist-toggle
                data-store-url="{{
                    route(
                        'wishlist.store',
                        $product
                    )
                }}"
                data-destroy-url="{{
                    route(
                        'wishlist.destroy',
                        $product
                    )
                }}"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="
                        product-card-wishlist
                        active
                    "
                    title="Bỏ khỏi yêu thích"
                    aria-label="Bỏ khỏi yêu thích"
                >
                    <svg
                        viewBox="0 0 24 24"
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
                class="product-card-wishlist-form"
                data-wishlist-toggle
                data-store-url="{{
                    route(
                        'wishlist.store',
                        $product
                    )
                }}"
                data-destroy-url="{{
                    route(
                        'wishlist.destroy',
                        $product
                    )
                }}"
            >

                @csrf

                <button
                    type="submit"
                    class="product-card-wishlist"
                    title="Thêm vào yêu thích"
                    aria-label="Thêm vào yêu thích"
                >
                    <svg
                        viewBox="0 0 24 24"
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

    @endif

@endguest

        @if ($primaryImage)

            <img
                class="product-image"
                src="{{
                    asset(
                        'storage/'
                        . $primaryImage->image_path
                    )
                }}"
                alt="{{ $product->name }}"
            >

        @else

            <div class="product-no-image" aria-hidden="true">
                <svg class="product-fallback-motif" width="56" height="56" viewBox="0 0 56 56" fill="none">
                    <path d="M28 8C18 20 12 32 12 44C24 44 36 38 44 26C44 14 38 8 28 8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M28 8V44" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-dasharray="2 2"/>
                    <path d="M20 25C25 27 28 31 28 31" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                    <path d="M36 21C31 23 28 27 28 27" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                </svg>
            </div>

        @endif


        @if ($product->is_featured)

            <div class="product-badge">
                NỔI BẬT
            </div>

        @endif

    </div>


    <div class="product-body">


        <div class="product-category">

            {{
                $product->category?->name
                ?? 'MiniGrove'
            }}

        </div>


        <h3 class="product-name">
            {{ $product->name }}
        </h3>


        <div class="product-meta">

            <span>

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

            </span>

            <span>•</span>

            <span>

                @switch(
                    $product->care_level
                )

                    @case('easy')
                        Dễ chăm
                        @break

                    @case('medium')
                        Chăm sóc vừa
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

            </span>

        </div>


        <div class="product-price-row">

            @if (
                $product->sale_price !== null
            )

                <div class="product-sale-price">

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


                <div class="product-old-price">

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

                <div class="product-normal-price">

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


        <div class="product-card-footer">

            <span class="product-stock">

                Còn
                {{ $product->stock_quantity }}
                sản phẩm

            </span>


            <a
                href="{{
                    route(
                        'products.show',
                        $product
                    )
                }}"
                class="product-view"
            >
                <span>Xem chi tiết</span>
                <span class="product-view-arrow" aria-hidden="true">→</span>
            </a>

        </div>

    </div>

</article>