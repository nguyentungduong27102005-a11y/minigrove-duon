@extends('layouts.customer')


@section(
    'title',
    'Giỏ hàng - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/cart.css') }}"
>

@endpush


@section('content')

@php

    $productItems = $cart->items->filter(
        function ($item) {

            return
                $item->item_type === 'PRODUCT'
                && $item->product;
        }
    );

    $designItems = $cart->items->filter(
        function ($item) {
            return
                $item->item_type === 'CUSTOM_DESIGN'
                && $item->customDesign;
        }
    );


    /*
     * Những CartItem Customer đã chọn
     * trước khi sang Checkout.
     *
     * Khi quay lại Cart,
     * các checkbox này sẽ được tick lại.
     */
    $selectedCartItemIds =
        session(
            'checkout.cart_item_ids',
            []
        );

@endphp


<section class="cart-page">

    <div class="customer-container">


        <div class="cart-heading">

            <h1>
                Giỏ hàng
            </h1>

            <p>
                Chọn những sản phẩm bạn muốn mua
                và kiểm tra lại số lượng.
            </p>

        </div>


        @if ($productItems->isEmpty() && $designItems->isEmpty())


            {{-- EMPTY CART --}}
            <div class="cart-empty">

                <div class="cart-empty-icon">
                    🛒
                </div>

                <h2>
                    Giỏ hàng đang trống
                </h2>

                <p>
                    Hãy khám phá những khu rừng
                    MiniGrove dành cho bạn.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="cart-main-button"
                >
                    Khám phá sản phẩm
                </a>

            </div>


        @else


            {{-- CART TABLE --}}
            <div class="cart-box">


                {{-- HEADER --}}
                <div class="cart-table-header">

                    <div class="cart-checkbox-cell">

                        <input
                            type="checkbox"
                            id="selectAllTop"
                            class="cart-checkbox"
                        >

                    </div>


                    <div>
                        Sản phẩm
                    </div>


                    <div class="cart-center">
                        Đơn giá
                    </div>


                    <div class="cart-center">
                        Số lượng
                    </div>


                    <div class="cart-center">
                        Số tiền
                    </div>


                    <div class="cart-center">
                        Thao tác
                    </div>

                </div>


                {{-- ITEMS --}}
                @foreach ($productItems as $item)

                    @php

                        $product =
                            $item->product;


                        $primaryImage =
                            $product->images
                                ->firstWhere(
                                    'is_primary',
                                    true
                                )
                            ?? $product
                                ->images
                                ->first();

                    @endphp


                    <div class="cart-item-row">


                        {{-- CHECKBOX --}}
                        <div class="cart-checkbox-cell">

                            <input
    type="checkbox"
    class="
        cart-checkbox
        cart-item-checkbox
    "
    value="{{ $item->id }}"
    data-subtotal="{{
        (float) $item->subtotal
    }}"
    data-quantity="{{
        $item->quantity
    }}"
    data-item-type="product"
    data-product-id="{{ $product->id }}"
    data-wishlisted="{{
        in_array(
            $product->id,
            $wishlistProductIds ?? []
        )
            ? '1'
            : '0'
    }}"
    data-wishlist-url="{{
        route(
            'wishlist.store',
            $product
        )
    }}"
    @checked(
        in_array(
            $item->id,
            $selectedCartItemIds
        )
    )
>

                        </div>


                        {{-- PRODUCT --}}
                        <div class="cart-product">

                            <div class="cart-product-image">

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
                                            $product->name
                                        }}"
                                    >

                                @else

                                    <div
                                        class="
                                            cart-product-no-image
                                        "
                                    >
                                        🌱
                                    </div>

                                @endif

                            </div>


                            <div class="cart-product-info">

                                <div
                                    class="
                                        cart-product-category
                                    "
                                >
                                    {{
                                        $product
                                            ->category
                                            ?->name
                                        ?? 'MiniGrove'
                                    }}
                                </div>


                                <a
                                    href="{{
                                        route(
                                            'products.show',
                                            $product
                                        )
                                    }}"
                                    class="
                                        cart-product-name
                                    "
                                >
                                    {{ $product->name }}
                                </a>


                                <div
                                    class="
                                        cart-product-stock
                                    "
                                >
                                    Còn
                                    {{
                                        $product
                                            ->stock_quantity
                                    }}
                                    sản phẩm
                                </div>

                            </div>

                        </div>


                        {{-- UNIT PRICE --}}
                        <div
                            class="
                                cart-price
                                cart-center
                            "
                        >

                            {{
                                number_format(
                                    (float)
                                    $item->unit_price,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </div>


                        {{-- QUANTITY --}}
                        <div class="cart-center">

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'cart.update',
                                        $item
                                    )
                                }}"
                                class="
                                    cart-quantity-form
                                "
                            >

                                @csrf
                                @method('PATCH')


                                <button
                                    type="button"
                                    class="
                                        cart-quantity-button
                                        quantity-minus
                                    "
                                >
                                    −
                                </button>


                                <input
                                    type="number"
                                    name="quantity"
                                    value="{{
                                        $item->quantity
                                    }}"
                                    min="1"
                                    max="{{
                                        $product
                                            ->stock_quantity
                                    }}"
                                    class="
                                        cart-quantity-input
                                    "
                                    readonly
                                >


                                <button
                                    type="button"
                                    class="
                                        cart-quantity-button
                                        quantity-plus
                                    "
                                >
                                    +
                                </button>

                            </form>

                        </div>


                        {{-- SUBTOTAL --}}
                        <div
                            class="
                                cart-subtotal
                                cart-center
                            "
                        >

                            {{
                                number_format(
                                    (float)
                                    $item->subtotal,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}đ

                        </div>


                        {{-- DELETE --}}
                        <div class="cart-center">

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'cart.destroy',
                                        $item
                                    )
                                }}"
                                onsubmit="
                                    return confirm(
                                        'Bạn muốn xóa sản phẩm này khỏi giỏ hàng?'
                                    );
                                "
                            >

                                @csrf
                                @method('DELETE')


                                <button
                                    type="submit"
                                    class="
                                        cart-delete-button
                                    "
                                >
                                    Xóa
                                </button>

                            </form>

                        </div>

                    </div>

                @endforeach

                @foreach ($designItems as $item)
                    @php
                        $design = $item->customDesign;
                    @endphp

                    <div class="cart-item-row">
                        <div class="cart-checkbox-cell">
                            <input
                                type="checkbox"
                                class="cart-checkbox cart-item-checkbox"
                                value="{{ $item->id }}"
                                data-subtotal="{{ (float) $item->subtotal }}"
                                data-quantity="1"
                                data-item-type="custom-design"
                                @disabled(!empty($item->availability_error))
                                @checked(
                                    in_array(
                                        $item->id,
                                        $selectedCartItemIds
                                    )
                                )
                            >
                        </div>

                        <div class="cart-product">
                            <div class="cart-product-image">
                                <div class="cart-product-no-image">🌿</div>
                            </div>

                            <div class="cart-product-info">
                                <div class="cart-product-category">
                                    MiniGrove Studio
                                </div>

                                <a
                                    href="{{ route('designs.edit', $design) }}"
                                    class="cart-product-name"
                                >
                                    {{ $design->name }}
                                </a>

                                <div class="cart-product-stock">
                                    {{ $design->design_code }}
                                    · {{ ucfirst($design->style) }}
                                </div>

                                @if (!empty($item->availability_error))
                                    <div class="cart-product-stock">
                                        {{ $item->availability_error }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="cart-price cart-center">
                            @if (!empty($item->availability_error))
                                —
                            @else
                                {{ number_format((float) $item->unit_price, 0, ',', '.') }}đ
                            @endif
                        </div>

                        <div class="cart-center">
                            1
                        </div>

                        <div class="cart-subtotal cart-center">
                            @if (!empty($item->availability_error))
                                —
                            @else
                                {{ number_format((float) $item->subtotal, 0, ',', '.') }}đ
                            @endif
                        </div>

                        <div class="cart-center">
                            <form
                                method="POST"
                                action="{{ route('cart.destroy', $item) }}"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="cart-delete-button"
                                >
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

            </div>


            {{-- =====================================================
                 BOTTOM CHECKOUT BAR
            ====================================================== --}}
            <div class="cart-bottom-bar">


                <div class="cart-bottom-left">


                    <label class="cart-select-all">

                        <input
                            type="checkbox"
                            id="selectAllBottom"
                            class="cart-checkbox"
                        >

                        <span>
                            Chọn tất cả
                            ({{ $productItems->count() }})
                        </span>

                    </label>


                    {{-- DELETE SELECTED --}}
                    <form
                        method="POST"
                        action="{{
                            route(
                                'cart.selected.destroy'
                            )
                        }}"
                        id="deleteSelectedForm"
                        class="cart-inline-form"
                    >

                        @csrf


                        <div
                            id="
                                deleteSelectedInputs
                            "
                        ></div>


                        <button
                            type="submit"
                            class="
                                cart-bottom-action
                            "
                            id="deleteSelectedButton"
                            disabled
                        >
                            Xóa
                        </button>

                    </form>


                    {{-- WISHLIST --}}
                    <button
                        type="button"
                        class="
                            cart-bottom-action
                            cart-save-wishlist
                        "
                        id="saveWishlistButton"
                        disabled
                        title="Chọn sản phẩm để lưu vào mục đã thích."
                    >
                        ♡ Lưu vào mục đã thích
                    </button>

                </div>


                <div class="cart-bottom-right">


                    <div class="cart-checkout-total">

                        <span
                            id="selectedCountText"
                            class="
                                cart-selected-count
                            "
                        >
                            Tổng cộng
                            (0 sản phẩm):
                        </span>


                        <strong
                            id="selectedTotal"
                            class="
                                cart-selected-total
                            "
                        >
                            0đ
                        </strong>

                    </div>



                    <form
    method="POST"
    action="{{ route('checkout.cart') }}"
    id="checkoutSelectedForm"
    class="cart-inline-form"
>

    @csrf

    <div
        id="checkoutSelectedInputs"
    ></div>

    <button
        type="submit"
        id="buySelectedButton"
        class="cart-buy-button"
        disabled
    >
        Mua hàng
    </button>

</form>

                </div>

            </div>


        @endif

    </div>

</section>

@endsection


@push('scripts')

<script
    src="{{ asset('js/cart.js') }}"
></script>

@endpush