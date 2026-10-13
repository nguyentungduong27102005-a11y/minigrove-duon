@extends('layouts.customer')

@section('title', 'Thanh toán - MiniGrove')

@push('styles')
    <link
        rel="stylesheet"
        href="{{
            asset('css/checkout.css')
            . '?v='
            . filemtime(
                public_path(
                    'css/checkout.css'
                )
            )
        }}"
    >
@endpush

@section('content')

<section class="checkout-page">

    <div class="customer-container">

        {{-- =====================================================
             TOP BAR: QUAY LẠI & TIÊU ĐỀ
        ====================================================== --}}
        <div class="checkout-header-bar">
            <a
                href="{{
                    $mode === 'cart'
                        ? route('cart.index')
                        : route(
                            'products.show',
                            $checkoutItems->first()->product
                        )
                }}"
                class="checkout-back-button"
            >
                ← Quay lại {{ $mode === 'cart' ? 'Giỏ hàng' : 'Sản phẩm' }}
            </a>

            <div class="checkout-heading">
                <h1>Thanh toán</h1>
                <p>Kiểm tra thông tin trước khi đặt hàng.</p>
            </div>
        </div>

        {{-- THÔNG BÁO LỖI VOUCHER RIÊNG (CHỈ KHI CHƯA ĐƯỢC LAYOUT CUSTOMER CHUNG HIỂN THỊ) --}}
        @if ($voucherError && !session('error'))
            <div class="checkout-alert checkout-alert-error">
                {{ $voucherError }}
            </div>
        @endif

        {{-- =====================================================
             BỐ CỤC 2 CỘT (DESKTOP: 64% / 36%)
        ====================================================== --}}
        <div class="checkout-layout">

            {{-- -------------------------------------------------
                 CỘT TRÁI (64%)
                 1. Địa chỉ nhận hàng
                 2. Danh sách sản phẩm
                 3. Phương thức thanh toán
                 4. Ghi chú đơn hàng
            -------------------------------------------------- --}}
            <div class="checkout-main">

                {{-- =============================================
                     1. ĐỊA CHỈ NHẬN HÀNG
                ============================================== --}}
                <div class="checkout-card checkout-card-address">
                    <div class="checkout-card-header">
                        <div class="checkout-card-title">
                            📍 Địa chỉ nhận hàng
                        </div>
                    </div>

                    @if ($addresses->isNotEmpty())

                        @if ($selectedAddress)
                            {{-- ĐỊA CHỈ ĐANG CHỌN (Gọn gàng) --}}
                            <div class="checkout-selected-address-box">
                                <div class="checkout-address-item selected">
                                    <div class="checkout-address-radio">
                                        <span class="checkout-radio-dot-selected" title="Địa chỉ đang chọn">●</span>
                                    </div>

                                    <div class="checkout-address-content">
                                        <div class="checkout-address-main">
                                            <strong>{{ $selectedAddress->receiver_name }}</strong>
                                            <span>{{ $selectedAddress->receiver_phone }}</span>

                                            @if ($selectedAddress->label)
                                                <span class="checkout-address-label">
                                                    {{ $selectedAddress->label }}
                                                </span>
                                            @endif

                                            @if ($selectedAddress->is_default)
                                                <span class="checkout-address-default">
                                                    Mặc định
                                                </span>
                                            @endif

                                            <span class="checkout-address-current-pill">
                                                Đang chọn
                                            </span>
                                        </div>

                                        <div class="checkout-address-text">
                                            {{ $selectedAddress->address_detail }},
                                            {{ $selectedAddress->ward }},
                                            {{ $selectedAddress->district }},
                                            {{ $selectedAddress->province }}
                                        </div>

                                        <div class="checkout-address-actions">
                                            <button
                                                type="button"
                                                class="checkout-secondary-button checkout-address-edit-button"
                                                data-address-edit="{{ $selectedAddress->id }}"
                                            >
                                                Sửa
                                            </button>

                                            @if ($addresses->count() > 1)
                                                <button
                                                    type="button"
                                                    class="checkout-btn-toggle-others"
                                                    id="btnToggleOtherAddresses"
                                                    aria-expanded="false"
                                                >
                                                    Đổi địa chỉ khác ({{ $addresses->count() - 1 }}) ▾
                                                </button>
                                            @endif
                                        </div>

                                        {{-- FORM SỬA ĐỊA CHỈ ĐANG CHỌN --}}
                                        <div
                                            class="checkout-address-edit-form"
                                            id="editAddress{{ $selectedAddress->id }}"
                                            style="display: none; margin-top: 14px;"
                                        >
                                            <form
                                                method="POST"
                                                action="{{ route('checkout.address.update', $selectedAddress->id) }}"
                                                class="checkout-address-form"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <div class="checkout-form-grid">
                                                    <div class="checkout-field">
                                                        <label>Tên người nhận</label>
                                                        <input
                                                            type="text"
                                                            name="receiver_name"
                                                            value="{{ $selectedAddress->receiver_name }}"
                                                            maxlength="100"
                                                            required
                                                        >
                                                    </div>

                                                    <div class="checkout-field">
                                                        <label>Số điện thoại</label>
                                                        <input
                                                            type="text"
                                                            name="receiver_phone"
                                                            value="{{ $selectedAddress->receiver_phone }}"
                                                            maxlength="10"
                                                            inputmode="numeric"
                                                            required
                                                        >
                                                    </div>

                                                    <div class="checkout-field">
                                                        <label>Tỉnh / Thành phố</label>
                                                        <select
                                                            class="checkout-edit-province"
                                                            name="province"
                                                            data-address-id="{{ $selectedAddress->id }}"
                                                            data-current-province="{{ $selectedAddress->province }}"
                                                            required
                                                        >
                                                            <option value="">Đang tải Tỉnh / Thành phố...</option>
                                                        </select>
                                                    </div>

                                                    <div class="checkout-field">
                                                        <label>Quận / Huyện</label>
                                                        <select
                                                            class="checkout-edit-district"
                                                            name="district"
                                                            data-address-id="{{ $selectedAddress->id }}"
                                                            data-current-district="{{ $selectedAddress->district }}"
                                                            required
                                                        >
                                                            <option value="{{ $selectedAddress->district }}">{{ $selectedAddress->district }}</option>
                                                        </select>
                                                    </div>

                                                    <div class="checkout-field">
                                                        <label>Phường / Xã</label>
                                                        <select
                                                            class="checkout-edit-ward"
                                                            name="ward"
                                                            data-address-id="{{ $selectedAddress->id }}"
                                                            data-current-ward="{{ $selectedAddress->ward }}"
                                                            required
                                                        >
                                                            <option value="{{ $selectedAddress->ward }}">{{ $selectedAddress->ward }}</option>
                                                        </select>
                                                    </div>

                                                    <div class="checkout-field checkout-field-full">
                                                        <label>Địa chỉ chi tiết</label>
                                                        <input
                                                            type="text"
                                                            name="address_detail"
                                                            value="{{ $selectedAddress->address_detail }}"
                                                            maxlength="255"
                                                            required
                                                        >
                                                    </div>

                                                    <div class="checkout-field">
                                                        <label>Nhãn địa chỉ</label>
                                                        <div class="checkout-label-options">
                                                            <label class="checkout-label-option">
                                                                <input type="radio" name="label" value="Nhà" @checked($selectedAddress->label === 'Nhà')>
                                                                <span>Nhà</span>
                                                            </label>
                                                            <label class="checkout-label-option">
                                                                <input type="radio" name="label" value="Công ty" @checked($selectedAddress->label === 'Công ty')>
                                                                <span>Công ty</span>
                                                            </label>
                                                            <label class="checkout-label-option">
                                                                <input type="radio" name="label" value="Khác" @checked(!in_array($selectedAddress->label, ['Nhà', 'Công ty']))>
                                                                <span>Khác</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="checkout-field checkout-field-full">
                                                        <label class="checkout-default-check">
                                                            <input type="checkbox" name="is_default" value="1" @checked($selectedAddress->is_default)>
                                                            <span>Đặt làm địa chỉ mặc định</span>
                                                        </label>
                                                    </div>

                                                    <input type="hidden" class="checkout-edit-ghn-district" name="ghn_district_id" value="{{ $selectedAddress->ghn_district_id }}">
                                                    <input type="hidden" class="checkout-edit-ghn-ward" name="ghn_ward_code" value="{{ $selectedAddress->ghn_ward_code }}">

                                                    <div class="checkout-form-actions checkout-field-full">
                                                        <button type="submit" class="checkout-secondary-button">Cập nhật</button>
                                                        <button type="button" class="checkout-secondary-button checkout-address-cancel-edit" data-address-edit="{{ $selectedAddress->id }}">Hủy</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- DANH SÁCH ĐỊA CHỈ KHÁC (Thu gọn mặc định) --}}
                        <div
                            class="checkout-other-addresses-wrapper"
                            id="checkoutOtherAddressesWrapper"
                            style="{{ $selectedAddress && $addresses->count() > 1 ? 'display: none;' : '' }}"
                        >
                            <div class="checkout-other-addresses-title">
                                Danh sách địa chỉ nhận hàng
                            </div>

                            <div class="checkout-address-list">
                                @foreach ($addresses as $address)
                                    @if ($selectedAddress && $address->id === $selectedAddress->id)
                                        @continue
                                    @endif

                                    <div class="checkout-address-item">
                                        <div class="checkout-address-radio">
                                            <form method="POST" action="{{ route('checkout.address.select') }}">
                                                @csrf
                                                <input type="hidden" name="address_id" value="{{ $address->id }}">
                                                <button type="submit" class="checkout-address-select-button" title="Chọn địa chỉ này">
                                                    ○
                                                </button>
                                            </form>
                                        </div>

                                        <div class="checkout-address-content">
                                            <div class="checkout-address-main">
                                                <strong>{{ $address->receiver_name }}</strong>
                                                <span>{{ $address->receiver_phone }}</span>

                                                @if ($address->label)
                                                    <span class="checkout-address-label">
                                                        {{ $address->label }}
                                                    </span>
                                                @endif

                                                @if ($address->is_default)
                                                    <span class="checkout-address-default">
                                                        Mặc định
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="checkout-address-text">
                                                {{ $address->address_detail }},
                                                {{ $address->ward }},
                                                {{ $address->district }},
                                                {{ $address->province }}
                                            </div>

                                            <div class="checkout-address-actions">
                                                <form method="POST" action="{{ route('checkout.address.select') }}" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="address_id" value="{{ $address->id }}">
                                                    <button type="submit" class="checkout-secondary-button checkout-btn-choose">
                                                        Giao đến địa chỉ này
                                                    </button>
                                                </form>

                                                <button
                                                    type="button"
                                                    class="checkout-secondary-button checkout-address-edit-button"
                                                    data-address-edit="{{ $address->id }}"
                                                >
                                                    Sửa
                                                </button>

                                                <form
                                                    method="POST"
                                                    action="{{ route('checkout.address.destroy', $address->id) }}"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này không?');"
                                                    style="display:inline;"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="checkout-secondary-button checkout-address-delete-button"
                                                        data-address-id="{{ $address->id }}"
                                                    >
                                                        Xóa
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- FORM SỬA ĐỊA CHỈ NÀY --}}
                                            <div
                                                class="checkout-address-edit-form"
                                                id="editAddress{{ $address->id }}"
                                                style="display: none; margin-top: 14px;"
                                            >
                                                <form
                                                    method="POST"
                                                    action="{{ route('checkout.address.update', $address->id) }}"
                                                    class="checkout-address-form"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <div class="checkout-form-grid">
                                                        <div class="checkout-field">
                                                            <label>Tên người nhận</label>
                                                            <input
                                                                type="text"
                                                                name="receiver_name"
                                                                value="{{ $address->receiver_name }}"
                                                                maxlength="100"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="checkout-field">
                                                            <label>Số điện thoại</label>
                                                            <input
                                                                type="text"
                                                                name="receiver_phone"
                                                                value="{{ $address->receiver_phone }}"
                                                                maxlength="10"
                                                                inputmode="numeric"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="checkout-field">
                                                            <label>Tỉnh / Thành phố</label>
                                                            <select
                                                                class="checkout-edit-province"
                                                                name="province"
                                                                data-address-id="{{ $address->id }}"
                                                                data-current-province="{{ $address->province }}"
                                                                required
                                                            >
                                                                <option value="">Đang tải Tỉnh / Thành phố...</option>
                                                            </select>
                                                        </div>

                                                        <div class="checkout-field">
                                                            <label>Quận / Huyện</label>
                                                            <select
                                                                class="checkout-edit-district"
                                                                name="district"
                                                                data-address-id="{{ $address->id }}"
                                                                data-current-district="{{ $address->district }}"
                                                                required
                                                            >
                                                                <option value="{{ $address->district }}">{{ $address->district }}</option>
                                                            </select>
                                                        </div>

                                                        <div class="checkout-field">
                                                            <label>Phường / Xã</label>
                                                            <select
                                                                class="checkout-edit-ward"
                                                                name="ward"
                                                                data-address-id="{{ $address->id }}"
                                                                data-current-ward="{{ $address->ward }}"
                                                                required
                                                            >
                                                                <option value="{{ $address->ward }}">{{ $address->ward }}</option>
                                                            </select>
                                                        </div>

                                                        <div class="checkout-field checkout-field-full">
                                                            <label>Địa chỉ chi tiết</label>
                                                            <input
                                                                type="text"
                                                                name="address_detail"
                                                                value="{{ $address->address_detail }}"
                                                                maxlength="255"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="checkout-field">
                                                            <label>Nhãn địa chỉ</label>
                                                            <div class="checkout-label-options">
                                                                <label class="checkout-label-option">
                                                                    <input type="radio" name="label" value="Nhà" @checked($address->label === 'Nhà')>
                                                                    <span>Nhà</span>
                                                                </label>
                                                                <label class="checkout-label-option">
                                                                    <input type="radio" name="label" value="Công ty" @checked($address->label === 'Công ty')>
                                                                    <span>Công ty</span>
                                                                </label>
                                                                <label class="checkout-label-option">
                                                                    <input type="radio" name="label" value="Khác" @checked(!in_array($address->label, ['Nhà', 'Công ty']))>
                                                                    <span>Khác</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="checkout-field checkout-field-full">
                                                            <label class="checkout-default-check">
                                                                <input type="checkbox" name="is_default" value="1" @checked($address->is_default)>
                                                                <span>Đặt làm địa chỉ mặc định</span>
                                                            </label>
                                                        </div>

                                                        <input type="hidden" class="checkout-edit-ghn-district" name="ghn_district_id" value="{{ $address->ghn_district_id }}">
                                                        <input type="hidden" class="checkout-edit-ghn-ward" name="ghn_ward_code" value="{{ $address->ghn_ward_code }}">

                                                        <div class="checkout-form-actions checkout-field-full">
                                                            <button type="submit" class="checkout-secondary-button">Cập nhật</button>
                                                            <button type="button" class="checkout-secondary-button checkout-address-cancel-edit" data-address-edit="{{ $address->id }}">Hủy</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    @else
                        <div class="checkout-no-address">
                            Bạn chưa có địa chỉ nhận hàng. Vui lòng thêm địa chỉ bên dưới.
                        </div>
                    @endif

                    {{-- THÊM ĐỊA CHỈ MỚI --}}
                    <details
                        class="checkout-address-create"
                        @if (
                            $errors->has('receiver_name')
                            || $errors->has('receiver_phone')
                            || $errors->has('province')
                            || $errors->has('district')
                            || $errors->has('ward')
                            || $errors->has('address_detail')
                            || $errors->has('label')
                        )
                            open
                        @endif
                    >
                        <summary>+ Thêm địa chỉ mới</summary>

                        <form
                            method="POST"
                            action="{{ route('checkout.address.store') }}"
                            class="checkout-address-form"
                        >
                            @csrf

                            <div class="checkout-form-grid">
                                <div class="checkout-field">
                                    <label for="receiverName">Tên người nhận</label>
                                    <input
                                        id="receiverName"
                                        type="text"
                                        name="receiver_name"
                                        value="{{ old('receiver_name', auth()->user()?->name) }}"
                                        maxlength="100"
                                        required
                                    >
                                    @error('receiver_name')
                                        <div class="checkout-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="receiverPhone">Số điện thoại</label>
                                    <input
                                        id="receiverPhone"
                                        type="text"
                                        name="receiver_phone"
                                        value="{{ old('receiver_phone', auth()->user()?->phone) }}"
                                        maxlength="10"
                                        inputmode="numeric"
                                        required
                                    >
                                    @error('receiver_phone')
                                        <div class="checkout-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="checkoutProvince">Tỉnh / Thành phố</label>
                                    <select
                                        id="checkoutProvince"
                                        name="province"
                                        data-old-value="{{ old('province') }}"
                                        required
                                    >
                                        <option value="">Đang tải danh sách Tỉnh / Thành phố...</option>
                                    </select>
                                    @error('province')
                                        <div class="checkout-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="checkoutDistrict">Quận / Huyện</label>
                                    <select
                                        id="checkoutDistrict"
                                        name="district"
                                        data-old-value="{{ old('district') }}"
                                        disabled
                                        required
                                    >
                                        <option value="">Vui lòng chọn Tỉnh / Thành phố trước</option>
                                    </select>
                                    @error('district')
                                        <div class="checkout-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="checkoutWard">Phường / Xã</label>
                                    <select
                                        id="checkoutWard"
                                        name="ward"
                                        data-old-value="{{ old('ward') }}"
                                        disabled
                                        required
                                    >
                                        <option value="">Vui lòng chọn Quận / Huyện trước</option>
                                    </select>
                                    @error('ward')
                                        <div class="checkout-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="checkout-field checkout-field-full">
                                    <label for="addressDetail">Địa chỉ chi tiết</label>
                                    <input
                                        id="addressDetail"
                                        type="text"
                                        name="address_detail"
                                        value="{{ old('address_detail') }}"
                                        maxlength="255"
                                        placeholder="Số nhà, tên đường, tòa nhà..."
                                        required
                                    >
                                    @error('address_detail')
                                        <div class="checkout-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label>Nhãn địa chỉ</label>
                                    <div class="checkout-label-options">
                                        <label class="checkout-label-option">
                                            <input type="radio" name="label" value="Nhà" @checked(old('label', 'Nhà') === 'Nhà')>
                                            <span>Nhà</span>
                                        </label>
                                        <label class="checkout-label-option">
                                            <input type="radio" name="label" value="Công ty" @checked(old('label') === 'Công ty')>
                                            <span>Công ty</span>
                                        </label>
                                        <label class="checkout-label-option">
                                            <input type="radio" name="label" value="Khác" @checked(old('label') === 'Khác')>
                                            <span>Khác</span>
                                        </label>
                                    </div>
                                    @error('label')
                                        <div class="checkout-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="checkout-field checkout-field-full">
                                    <label class="checkout-default-check">
                                        <input
                                            type="checkbox"
                                            id="isDefault"
                                            name="is_default"
                                            value="1"
                                            @checked(old('is_default', $addresses->isEmpty()))
                                        >
                                        <span>Đặt làm địa chỉ mặc định</span>
                                    </label>
                                </div>

                                <input type="hidden" id="ghnDistrictId" name="ghn_district_id" value="{{ old('ghn_district_id') }}">
                                <input type="hidden" id="ghnWardCode" name="ghn_ward_code" value="{{ old('ghn_ward_code') }}">

                                <div class="checkout-field-full">
                                    <button type="submit" class="checkout-secondary-button" style="min-height: 38px; padding: 0 20px;">
                                        Lưu địa chỉ
                                    </button>
                                </div>
                            </div>
                        </form>
                    </details>
                </div>

                {{-- =============================================
                     2. DANH SÁCH SẢN PHẨM
                ============================================== --}}
                <div class="checkout-card checkout-products-card">
                    <div class="checkout-card-header">
                        <div class="checkout-card-title">
                            🌿 Danh sách sản phẩm ({{ $checkoutItems->sum('quantity') }})
                        </div>
                        <span class="checkout-items-count-badge">{{ $checkoutItems->count() }} mặt hàng</span>
                    </div>

                    <div class="checkout-products-scroll">
                        @foreach ($checkoutItems as $item)
                            @php
                                $product = $item->product;
                                $design = $item->custom_design ?? null;
                                $primaryImage = $product
                                    ? ($product->images->firstWhere('is_primary', true) ?? $product->images->first())
                                    : null;
                            @endphp

                            <div class="checkout-product-row-compact">
                                <div class="checkout-product-thumb">
                                    @if ($primaryImage && $product)
                                        <img
                                            src="{{ asset('storage/' . $primaryImage->image_path) }}"
                                            alt="{{ $product ? $product->name : 'Sản phẩm' }}"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="checkout-no-image">🌱</div>
                                    @endif
                                </div>

                                <div class="checkout-product-info">
                                    <div class="checkout-product-title-row">
                                        <span class="checkout-product-name" title="{{ $product ? $product->name : ($design?->name ?? 'Thiết kế Terrarium') }}">
                                            {{ $product ? $product->name : ($design?->name ?? 'Thiết kế Terrarium') }}
                                        </span>
                                        <span class="checkout-product-sep">·</span>
                                        <span class="checkout-product-qty">SL: {{ $item->quantity }}</span>
                                    </div>
                                    <div class="checkout-product-meta-row">
                                        <span class="checkout-product-unit-price">{{ number_format($item->unit_price, 0, ',', '.') }}đ / sp</span>
                                        @if ($design)
                                            <span class="checkout-product-meta-dot">·</span>
                                            <span class="checkout-product-tag">{{ $design->design_code }} ({{ ucfirst($design->style) }})</span>
                                        @elseif ($product?->category)
                                            <span class="checkout-product-meta-dot">·</span>
                                            <span class="checkout-product-tag">{{ $product->category->name }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="checkout-product-total">
                                    {{ number_format($item->subtotal, 0, ',', '.') }}đ
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- =============================================
                     3 & 4. PLACE ORDER FORM (PAYMENT + NOTE)
                ============================================== --}}
                <form
                    method="POST"
                    action="{{ route('checkout.place-order') }}"
                    id="checkoutPlaceOrderForm"
                    class="checkout-order-form-wrapper"
                >
                    @csrf

                    @if ($selectedAddress)
                        <input
                            type="hidden"
                            name="address_id"
                            id="checkoutSelectedAddressId"
                            value="{{ $selectedAddress->id }}"
                        >
                    @endif

                    @php
                        $selectedPaymentMethod = old('payment_method');

                        if (!$selectedPaymentMethod || !$paymentSettings->has($selectedPaymentMethod)) {
                            $selectedPaymentMethod = $paymentSettings->keys()->first();
                        }

                        $codSetting = $paymentSettings->get('COD');
                        $qrSetting = $paymentSettings->get('QR');
                        $onePaySetting = $paymentSettings->get('ONEPAY');
                    @endphp

                    {{-- 3. PHƯƠNG THỨC THANH TOÁN --}}
                    <div class="checkout-card checkout-payment-card">
                        <div class="checkout-card-header">
                            <div class="checkout-card-title">
                                💳 Phương thức thanh toán
                            </div>
                        </div>

                        @if ($paymentSettings->isEmpty())
                            <div class="checkout-payment-unavailable">
                                Hiện MiniGrove chưa có phương thức thanh toán khả dụng. Vui lòng quay lại sau.
                            </div>
                        @else
                            <div class="checkout-payment-options">
                                {{-- COD --}}
                                @if ($codSetting)
                                    <label class="checkout-payment-option {{ $selectedPaymentMethod === 'COD' ? 'is-selected' : '' }}" for="paymentMethodCOD">
                                        <input
                                            type="radio"
                                            id="paymentMethodCOD"
                                            name="payment_method"
                                            value="COD"
                                            @checked($selectedPaymentMethod === 'COD')
                                        >
                                        <div class="checkout-payment-text">
                                            <strong class="checkout-payment-name">
                                                {{ $codSetting->display_name }}
                                            </strong>
                                            <small class="checkout-payment-desc">
                                                Thanh toán khi nhận hàng
                                            </small>
                                        </div>
                                    </label>
                                @endif

                                {{-- QR (PayOS) --}}
                                @if ($qrSetting)
                                    <label class="checkout-payment-option {{ $selectedPaymentMethod === 'QR' ? 'is-selected' : '' }}" for="paymentMethodQR">
                                        <input
                                            type="radio"
                                            id="paymentMethodQR"
                                            name="payment_method"
                                            value="QR"
                                            @checked($selectedPaymentMethod === 'QR')
                                        >
                                        <div class="checkout-payment-text">
                                            <strong class="checkout-payment-name">
                                                {{ $qrSetting->display_name }}
                                            </strong>
                                            <small class="checkout-payment-desc">
                                                Quét mã QR PayOS
                                            </small>
                                        </div>
                                    </label>
                                @endif

                                {{-- ONEPAY --}}
                                @if ($onePaySetting)
                                    <label class="checkout-payment-option {{ $selectedPaymentMethod === 'ONEPAY' ? 'is-selected' : '' }}" for="paymentMethodONEPAY">
                                        <input
                                            type="radio"
                                            id="paymentMethodONEPAY"
                                            name="payment_method"
                                            value="ONEPAY"
                                            @checked($selectedPaymentMethod === 'ONEPAY')
                                        >
                                        <div class="checkout-payment-text">
                                            <strong class="checkout-payment-name">
                                                {{ $onePaySetting->display_name }}
                                            </strong>
                                            <small class="checkout-payment-desc">
                                                Thẻ ATM / Visa (OnePay)
                                            </small>
                                        </div>
                                    </label>
                                @endif
                            </div>
                        @endif

                        @error('payment_method')
                            <div class="checkout-field-error" style="padding: 0 18px 12px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 4. GHI CHÚ ĐƠN HÀNG --}}
                    <div class="checkout-card checkout-note-card">
                        <details class="checkout-note-details" {{ old('note') || $errors->has('note') ? 'open' : '' }}>
                            <summary class="checkout-note-summary">
                                <span class="checkout-note-summary-text">
                                    <span>📝 Ghi chú đơn hàng</span>
                                    <span class="checkout-note-tag">(tùy chọn)</span>
                                </span>
                                <span class="checkout-note-toggle-action">+ Thêm ghi chú</span>
                            </summary>
                            <div class="checkout-note-body">
                                <textarea
                                    name="note"
                                    id="checkoutNoteTextarea"
                                    rows="2"
                                    maxlength="1000"
                                    placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."
                                >{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="checkout-field-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </details>
                    </div>

                </form>

            </div>

            {{-- -------------------------------------------------
                 CỘT PHẢI (36%)
                 1. Voucher MiniGrove
                 2. Tóm tắt đơn hàng
                 3. Tổng thanh toán
                 4. Nút Đặt hàng
            -------------------------------------------------- --}}
            <div class="checkout-sidebar">

                {{-- =============================================
                     1. VOUCHER MINIGROVE (COMPACT SHOPEE-STYLE ROW)
                ============================================== --}}
                <div
                    class="checkout-card checkout-voucher-bar"
                    id="openVoucherModalBtn"
                    role="button"
                    tabindex="0"
                    aria-haspopup="dialog"
                    aria-expanded="false"
                    aria-controls="checkoutVoucherModal"
                >
                    <div class="checkout-voucher-bar-main">
                        <div class="checkout-voucher-bar-header">
                            <span class="checkout-voucher-bar-icon" aria-hidden="true">🎟️</span>
                            <span class="checkout-voucher-bar-title">Voucher MiniGrove</span>
                        </div>
                        @if ($appliedOrderVoucher || $appliedShippingVoucher)
                            <div class="checkout-voucher-bar-tags" onclick="event.stopPropagation();">
                                @if ($appliedShippingVoucher)
                                    <div class="checkout-voucher-bar-tag">
                                        <span class="checkout-voucher-tag-icon" aria-hidden="true">🚚</span>
                                        <span class="checkout-voucher-tag-code">{{ $appliedShippingVoucher->code }}</span>
                                        <span class="checkout-voucher-tag-disc">(-{{ number_format($shippingDiscountAmount, 0, ',', '.') }}đ)</span>
                                        <form
                                            method="POST"
                                            action="{{ route('checkout.voucher.remove') }}"
                                            class="checkout-voucher-tag-remove-form"
                                            onclick="event.stopPropagation();"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="scope" value="shipping">
                                            <button
                                                type="submit"
                                                class="checkout-voucher-tag-remove-btn"
                                                title="Gỡ mã vận chuyển"
                                                aria-label="Gỡ mã vận chuyển {{ $appliedShippingVoucher->code }}"
                                                onclick="event.stopPropagation();"
                                            >&times;</button>
                                        </form>
                                    </div>
                                @endif
                                @if ($appliedOrderVoucher)
                                    <div class="checkout-voucher-bar-tag">
                                        <span class="checkout-voucher-tag-icon" aria-hidden="true">🏷️</span>
                                        <span class="checkout-voucher-tag-code">{{ $appliedOrderVoucher->code }}</span>
                                        <span class="checkout-voucher-tag-disc">(-{{ number_format($orderDiscountAmount, 0, ',', '.') }}đ)</span>
                                        <form
                                            method="POST"
                                            action="{{ route('checkout.voucher.remove') }}"
                                            class="checkout-voucher-tag-remove-form"
                                            onclick="event.stopPropagation();"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="scope" value="order">
                                            <button
                                                type="submit"
                                                class="checkout-voucher-tag-remove-btn"
                                                title="Gỡ mã giảm giá đơn hàng"
                                                aria-label="Gỡ mã giảm giá đơn hàng {{ $appliedOrderVoucher->code }}"
                                                onclick="event.stopPropagation();"
                                            >&times;</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="checkout-voucher-bar-hint">
                                Chọn hoặc nhập mã voucher
                            </div>
                        @endif
                    </div>
                    <div class="checkout-voucher-bar-action-wrap">
                        <span>{{ ($appliedOrderVoucher || $appliedShippingVoucher) ? 'Đổi mã' : 'Chọn mã' }}</span>
                        <span class="checkout-voucher-bar-chevron" aria-hidden="true">›</span>
                    </div>
                </div>

                {{-- =============================================
                     2, 3, 4. TÓM TẮT ĐƠN HÀNG, TỔNG TIỀN, ĐẶT HÀNG
                ============================================== --}}
                <div class="checkout-card checkout-summary-card">
                    <div class="checkout-card-header">
                        <div class="checkout-card-title">
                            📋 Tóm tắt đơn hàng
                        </div>
                    </div>

                    <div class="checkout-summary-body">
                        <div class="checkout-summary-row">
                            <span>Tạm tính</span>
                            <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
                        </div>

                        <div class="checkout-summary-row">
                            <span>Giảm giá đơn hàng</span>
                            <strong class="checkout-discount-val">
                                {{ $orderDiscountAmount > 0 ? '-' . number_format($orderDiscountAmount, 0, ',', '.') . 'đ' : '0đ' }}
                            </strong>
                        </div>

                        <div class="checkout-summary-row">
                            <span>Phí vận chuyển</span>
                            <strong>
                                @if ($selectedAddress)
                                    {{ number_format($shippingFee, 0, ',', '.') }}đ
                                @else
                                    <span class="checkout-muted" style="font-size: 12px; color: #7A8782; font-weight: normal;">Chưa chọn địa chỉ</span>
                                @endif
                            </strong>
                        </div>

                        <div class="checkout-summary-row">
                            <span>Giảm phí vận chuyển</span>
                            <strong class="checkout-discount-val">
                                {{ $shippingDiscountAmount > 0 ? '-' . number_format($shippingDiscountAmount, 0, ',', '.') . 'đ' : '0đ' }}
                            </strong>
                        </div>

                        <div class="checkout-summary-divider"></div>

                        <div class="checkout-summary-total">
                            <span>Tổng thanh toán</span>
                            <strong>{{ number_format($total, 0, ',', '.') }}đ</strong>
                        </div>

                        @if (!$selectedAddress)
                            <div class="checkout-order-warning">
                                ⚠️ Vui lòng thêm hoặc chọn địa chỉ nhận hàng trước khi đặt hàng.
                            </div>
                        @endif

                        <button
                            type="submit"
                            form="checkoutPlaceOrderForm"
                            class="checkout-order-button"
                            id="checkoutOrderButton"
                            @disabled(!$selectedAddress)
                        >
                            Đặt hàng ({{ number_format($total, 0, ',', '.') }}đ)
                        </button>

                        <div class="checkout-note">
                            Khi bấm Đặt hàng, MiniGrove sẽ kiểm tra lại giá và tồn kho trước khi tạo đơn.
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- =============================================
         POPUP MODAL CHỌN VOUCHER (SHOPEE STYLE)
    ============================================== --}}
    <div
        class="checkout-modal-backdrop"
        id="checkoutVoucherModal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="checkoutVoucherModalTitle"
        tabindex="-1"
    >
        <div class="checkout-modal-dialog">
            <div class="checkout-modal-header">
                <h2 class="checkout-modal-title" id="checkoutVoucherModalTitle">Chọn Voucher MiniGrove</h2>
                <button
                    type="button"
                    class="checkout-modal-close-btn"
                    id="closeVoucherModalBtn"
                    aria-label="Đóng popup chọn voucher"
                >
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="checkout-modal-body">
                {{-- Ô NHẬP MÃ THỦ CÔNG --}}
                <div class="checkout-modal-input-section">
                    <form
                        method="POST"
                        action="{{ route('checkout.voucher.apply') }}"
                        class="checkout-modal-input-form"
                    >
                        @csrf
                        <div class="checkout-modal-input-wrap">
                            <input
                                type="text"
                                name="voucher_code"
                                value="{{ old('voucher_code') }}"
                                maxlength="50"
                                placeholder="Nhập mã voucher..."
                                autocomplete="off"
                                class="checkout-modal-input"
                            >
                            <button type="submit" class="checkout-modal-apply-btn">
                                Áp dụng
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 1. NHÓM MÃ MIỄN PHÍ VẬN CHUYỂN --}}
                @php
                    $sortedShippingVouchers = $shippingVouchers->sortByDesc(function ($v) use ($appliedShippingVoucher) {
                        return ($appliedShippingVoucher && $appliedShippingVoucher->id === $v->id) ? 1 : 0;
                    })->values();
                @endphp
                <div class="checkout-modal-group" id="groupShippingVouchers">
                    <div class="checkout-modal-group-header">
                        <span class="checkout-modal-group-icon" aria-hidden="true">🚚</span>
                        <div class="checkout-modal-group-info">
                            <span class="checkout-modal-group-title">Mã miễn phí vận chuyển</span>
                            <span class="checkout-modal-group-sub">Áp dụng tối đa 1 mã</span>
                        </div>
                    </div>

                    <div class="checkout-modal-vouchers-list">
                        @forelse ($sortedShippingVouchers as $idx => $vItem)
                            @php
                                $isApplied = ($appliedShippingVoucher && $appliedShippingVoucher->id === $vItem->id);
                                $isCollapsed = ($idx >= 2);
                            @endphp
                            @if ($vItem->checkout_eligible)
                                <div class="checkout-modal-voucher-wrapper {{ $isCollapsed ? 'is-collapsed-item' : '' }}">
                                    <button
                                        type="button"
                                        class="checkout-shopee-voucher-card is-eligible {{ $isApplied ? 'is-applied' : '' }}"
                                        data-voucher-select
                                        data-voucher-scope="shipping"
                                        data-voucher-code="{{ $vItem->code }}"
                                        title="Bấm để chọn hoặc bỏ chọn mã này"
                                    >
                                        <div class="checkout-shopee-voucher-left">
                                            <div class="checkout-shopee-badge-icon">🚚</div>
                                        </div>
                                        <div class="checkout-shopee-voucher-main">
                                            <div class="checkout-shopee-code-row">
                                                <span class="checkout-shopee-code">{{ $vItem->code }}</span>
                                            </div>
                                            <div class="checkout-shopee-disc">
                                                @if ($vItem->discount_type === 'percentage')
                                                    Giảm {{ number_format($vItem->discount_value, 0, ',', '.') }}% phí vận chuyển
                                                    @if ($vItem->max_discount_amount)
                                                        (tối đa {{ number_format($vItem->max_discount_amount, 0, ',', '.') }}đ)
                                                    @endif
                                                @else
                                                    Giảm tối đa {{ number_format($vItem->discount_value, 0, ',', '.') }}đ phí ship
                                                @endif
                                            </div>
                                            <div class="checkout-shopee-min">
                                                Đơn tối thiểu {{ number_format($vItem->min_order_amount, 0, ',', '.') }}đ
                                            </div>
                                        </div>
                                        <div class="checkout-shopee-voucher-radio">
                                            <div class="checkout-shopee-circle {{ $isApplied ? 'is-checked' : '' }}" aria-hidden="true">
                                                <svg viewBox="0 0 16 16" fill="currentColor" width="11" height="11">
                                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            @else
                                <div class="checkout-modal-voucher-wrapper {{ $isCollapsed ? 'is-collapsed-item' : '' }}">
                                    <div class="checkout-shopee-voucher-card is-ineligible" aria-disabled="true">
                                        <div class="checkout-shopee-voucher-left">
                                            <div class="checkout-shopee-badge-icon">🚚</div>
                                        </div>
                                        <div class="checkout-shopee-voucher-main">
                                            <div class="checkout-shopee-code-row">
                                                <span class="checkout-shopee-code">{{ $vItem->code }}</span>
                                            </div>
                                            <div class="checkout-shopee-disc">
                                                @if ($vItem->discount_type === 'percentage')
                                                    Giảm {{ number_format($vItem->discount_value, 0, ',', '.') }}% phí vận chuyển
                                                    @if ($vItem->max_discount_amount)
                                                        (tối đa {{ number_format($vItem->max_discount_amount, 0, ',', '.') }}đ)
                                                    @endif
                                                @else
                                                    Giảm tối đa {{ number_format($vItem->discount_value, 0, ',', '.') }}đ phí ship
                                                @endif
                                            </div>
                                            <div class="checkout-shopee-min">
                                                Đơn tối thiểu {{ number_format($vItem->min_order_amount, 0, ',', '.') }}đ
                                            </div>
                                            <div class="checkout-shopee-error">
                                                {{ $vItem->checkout_error }}
                                            </div>
                                        </div>
                                        <div class="checkout-shopee-voucher-radio">
                                            <div class="checkout-shopee-circle is-disabled" aria-hidden="true"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="checkout-modal-empty-note">
                                Chưa có mã vận chuyển khả dụng.
                            </div>
                        @endforelse
                    </div>

                    @if ($sortedShippingVouchers->count() > 2)
                        <button
                            type="button"
                            class="checkout-modal-toggle-more-btn"
                            data-target="groupShippingVouchers"
                            data-more-text="Xem thêm ({{ $sortedShippingVouchers->count() - 2 }}) ▾"
                            data-less-text="Thu gọn ▴"
                        >
                            Xem thêm ({{ $sortedShippingVouchers->count() - 2 }}) ▾
                        </button>
                    @endif
                </div>

                {{-- 2. NHÓM MÃ GIẢM GIÁ ĐƠN HÀNG --}}
                @php
                    $sortedOrderVouchers = $orderVouchers->sortByDesc(function ($v) use ($appliedOrderVoucher) {
                        return ($appliedOrderVoucher && $appliedOrderVoucher->id === $v->id) ? 1 : 0;
                    })->values();
                @endphp
                <div class="checkout-modal-group" id="groupOrderVouchers">
                    <div class="checkout-modal-group-header">
                        <span class="checkout-modal-group-icon" aria-hidden="true">🏷️</span>
                        <div class="checkout-modal-group-info">
                            <span class="checkout-modal-group-title">Mã giảm giá đơn hàng</span>
                            <span class="checkout-modal-group-sub">Áp dụng tối đa 1 mã</span>
                        </div>
                    </div>

                    <div class="checkout-modal-vouchers-list">
                        @forelse ($sortedOrderVouchers as $idx => $vItem)
                            @php
                                $isApplied = ($appliedOrderVoucher && $appliedOrderVoucher->id === $vItem->id);
                                $isCollapsed = ($idx >= 2);
                            @endphp
                            @if ($vItem->checkout_eligible)
                                <div class="checkout-modal-voucher-wrapper {{ $isCollapsed ? 'is-collapsed-item' : '' }}">
                                    <button
                                        type="button"
                                        class="checkout-shopee-voucher-card is-eligible {{ $isApplied ? 'is-applied' : '' }}"
                                        data-voucher-select
                                        data-voucher-scope="order"
                                        data-voucher-code="{{ $vItem->code }}"
                                        title="Bấm để chọn hoặc bỏ chọn mã này"
                                    >
                                        <div class="checkout-shopee-voucher-left">
                                            <div class="checkout-shopee-badge-icon">🏷️</div>
                                        </div>
                                        <div class="checkout-shopee-voucher-main">
                                            <div class="checkout-shopee-code-row">
                                                <span class="checkout-shopee-code">{{ $vItem->code }}</span>
                                            </div>
                                            <div class="checkout-shopee-disc">
                                                @if ($vItem->discount_type === 'percentage')
                                                    Giảm {{ number_format($vItem->discount_value, 0, ',', '.') }}%
                                                    @if ($vItem->max_discount_amount)
                                                        (tối đa {{ number_format($vItem->max_discount_amount, 0, ',', '.') }}đ)
                                                    @endif
                                                @else
                                                    Giảm {{ number_format($vItem->discount_value, 0, ',', '.') }}đ
                                                @endif
                                            </div>
                                            <div class="checkout-shopee-min">
                                                Đơn tối thiểu {{ number_format($vItem->min_order_amount, 0, ',', '.') }}đ
                                            </div>
                                        </div>
                                        <div class="checkout-shopee-voucher-radio">
                                            <div class="checkout-shopee-circle {{ $isApplied ? 'is-checked' : '' }}" aria-hidden="true">
                                                <svg viewBox="0 0 16 16" fill="currentColor" width="11" height="11">
                                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            @else
                                <div class="checkout-modal-voucher-wrapper {{ $isCollapsed ? 'is-collapsed-item' : '' }}">
                                    <div class="checkout-shopee-voucher-card is-ineligible" aria-disabled="true">
                                        <div class="checkout-shopee-voucher-left">
                                            <div class="checkout-shopee-badge-icon">🏷️</div>
                                        </div>
                                        <div class="checkout-shopee-voucher-main">
                                            <div class="checkout-shopee-code-row">
                                                <span class="checkout-shopee-code">{{ $vItem->code }}</span>
                                            </div>
                                            <div class="checkout-shopee-disc">
                                                @if ($vItem->discount_type === 'percentage')
                                                    Giảm {{ number_format($vItem->discount_value, 0, ',', '.') }}%
                                                    @if ($vItem->max_discount_amount)
                                                        (tối đa {{ number_format($vItem->max_discount_amount, 0, ',', '.') }}đ)
                                                    @endif
                                                @else
                                                    Giảm {{ number_format($vItem->discount_value, 0, ',', '.') }}đ
                                                @endif
                                            </div>
                                            <div class="checkout-shopee-min">
                                                Đơn tối thiểu {{ number_format($vItem->min_order_amount, 0, ',', '.') }}đ
                                            </div>
                                            <div class="checkout-shopee-error">
                                                {{ $vItem->checkout_error }}
                                            </div>
                                        </div>
                                        <div class="checkout-shopee-voucher-radio">
                                            <div class="checkout-shopee-circle is-disabled" aria-hidden="true"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="checkout-modal-empty-note">
                                Chưa có mã giảm giá đơn hàng khả dụng.
                            </div>
                        @endforelse
                    </div>

                    @if ($sortedOrderVouchers->count() > 2)
                        <button
                            type="button"
                            class="checkout-modal-toggle-more-btn"
                            data-target="groupOrderVouchers"
                            data-more-text="Xem thêm ({{ $sortedOrderVouchers->count() - 2 }}) ▾"
                            data-less-text="Thu gọn ▴"
                        >
                            Xem thêm ({{ $sortedOrderVouchers->count() - 2 }}) ▾
                        </button>
                    @endif
                </div>
            </div>

            <div class="checkout-modal-footer">
                <form
                    method="POST"
                    action="{{ route('checkout.vouchers.confirm') }}"
                    id="checkoutConfirmVouchersForm"
                    class="checkout-modal-confirm-form"
                >
                    @csrf
                    <input
                        type="hidden"
                        name="order_voucher_code"
                        id="modalInputOrderVoucherCode"
                        value="{{ $appliedOrderVoucher ? $appliedOrderVoucher->code : '' }}"
                    >
                    <input
                        type="hidden"
                        name="shipping_voucher_code"
                        id="modalInputShippingVoucherCode"
                        value="{{ $appliedShippingVoucher ? $appliedShippingVoucher->code : '' }}"
                    >
                    <button
                        type="submit"
                        class="checkout-modal-done-btn"
                        id="doneVoucherModalBtn"
                    >
                        Đồng ý
                    </button>
                </form>
            </div>
        </div>
    </div>

</section>

@endsection

@push('scripts')
    <script src="{{ asset('js/checkout-address.js') }}"></script>
    <script
        src="{{
            asset('js/checkout-voucher.js')
            . '?v='
            . filemtime(
                public_path(
                    'js/checkout-voucher.js'
                )
            )
        }}"
    ></script>

    <script>
    (function () {
        'use strict';

        // 1. Sync .is-selected on payment options when radio changes
        var radios = document.querySelectorAll('input[name="payment_method"]');
        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.checkout-payment-option').forEach(function (opt) {
                    opt.classList.remove('is-selected');
                });
                var parentOption = radio.closest('.checkout-payment-option');
                if (parentOption) {
                    parentOption.classList.add('is-selected');
                }
            });
        });

        // 2. Toggle Other Addresses list
        var btnToggleOthers = document.getElementById('btnToggleOtherAddresses');
        var othersWrapper = document.getElementById('checkoutOtherAddressesWrapper');
        if (btnToggleOthers && othersWrapper) {
            btnToggleOthers.addEventListener('click', function (e) {
                e.preventDefault();
                var isHidden = (othersWrapper.style.display === 'none' || getComputedStyle(othersWrapper).display === 'none');
                othersWrapper.style.display = isHidden ? 'block' : 'none';
                btnToggleOthers.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                var itemsCount = othersWrapper.querySelectorAll('.checkout-address-item').length;
                btnToggleOthers.textContent = isHidden
                    ? 'Thu gọn danh sách địa chỉ ▴'
                    : 'Đổi địa chỉ khác (' + itemsCount + ') ▾';
            });
        }

        // 3. Fallback submit connection for checkoutOrderButton
        var orderBtn = document.getElementById('checkoutOrderButton');
        var orderForm = document.getElementById('checkoutPlaceOrderForm');
        if (orderBtn && orderForm) {
            orderBtn.addEventListener('click', function (e) {
                if (orderBtn.disabled) {
                    e.preventDefault();
                    return;
                }
                // If browser does not natively link button to form via form attribute
                if (orderBtn.form !== orderForm) {
                    e.preventDefault();
                    if (typeof orderForm.requestSubmit === 'function') {
                        orderForm.requestSubmit(orderBtn);
                    } else {
                        orderForm.submit();
                    }
                }
            });
        }

        // 4. Shopee-style Voucher Modal Logic
        var openBtn = document.getElementById('openVoucherModalBtn');
        var modal = document.getElementById('checkoutVoucherModal');
        var closeBtn = document.getElementById('closeVoucherModalBtn');
        var inputOrder = document.getElementById('modalInputOrderVoucherCode');
        var inputShipping = document.getElementById('modalInputShippingVoucherCode');

        if (openBtn && modal) {
            // Giá trị đang được áp dụng thực tế trên server
            var serverOrderCode = @json($appliedOrderVoucher ? $appliedOrderVoucher->code : '');
            var serverShippingCode = @json($appliedShippingVoucher ? $appliedShippingVoucher->code : '');

            // Trạng thái lựa chọn tạm thời trong modal
            var tempOrderCode = serverOrderCode;
            var tempShippingCode = serverShippingCode;

            var voucherCards = modal.querySelectorAll('[data-voucher-select]');

            var syncVoucherUI = function () {
                voucherCards.forEach(function (card) {
                    var scope = card.getAttribute('data-voucher-scope');
                    var code = card.getAttribute('data-voucher-code');
                    var isSelected = (scope === 'order' && code === tempOrderCode)
                        || (scope === 'shipping' && code === tempShippingCode);

                    card.classList.toggle('is-applied', isSelected);
                    var circle = card.querySelector('.checkout-shopee-circle');
                    if (circle) {
                        circle.classList.toggle('is-checked', isSelected);
                    }
                });

                if (inputOrder) {
                    inputOrder.value = tempOrderCode;
                }
                if (inputShipping) {
                    inputShipping.value = tempShippingCode;
                }
            };

            var openModal = function () {
                // Khởi tạo lựa chọn tạm thời từ voucher đang được áp dụng trên server
                tempOrderCode = serverOrderCode;
                tempShippingCode = serverShippingCode;
                syncVoucherUI();

                modal.style.display = 'flex';
                openBtn.setAttribute('aria-expanded', 'true');
                document.body.classList.add('checkout-modal-open');
                modal.focus();
            };

            var closeModal = function () {
                // Hủy toàn bộ lựa chọn tạm chưa xác nhận khi bấm đóng / Esc / click backdrop
                tempOrderCode = serverOrderCode;
                tempShippingCode = serverShippingCode;
                syncVoucherUI();

                modal.style.display = 'none';
                openBtn.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('checkout-modal-open');
                openBtn.focus();
            };

            // Click vào toàn bộ dòng voucher: chỉ cập nhật lựa chọn tạm trên giao diện
            voucherCards.forEach(function (card) {
                card.addEventListener('click', function (e) {
                    e.preventDefault();
                    var scope = card.getAttribute('data-voucher-scope');
                    var code = card.getAttribute('data-voucher-code');

                    if (scope === 'order') {
                        // Click vào mã đang chọn tạm -> bỏ chọn; click mã khác -> chọn mã đó
                        tempOrderCode = (tempOrderCode === code) ? '' : code;
                    } else if (scope === 'shipping') {
                        tempShippingCode = (tempShippingCode === code) ? '' : code;
                    }

                    syncVoucherUI();
                });
            });

            openBtn.addEventListener('click', openModal);
            openBtn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openModal();
                }
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    closeModal();
                }
            });

            // Toggle Xem thêm / Thu gọn
            var toggleBtns = modal.querySelectorAll('.checkout-modal-toggle-more-btn');
            toggleBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var targetId = btn.getAttribute('data-target');
                    var group = document.getElementById(targetId);
                    if (!group) return;

                    var isExpanded = group.classList.toggle('is-expanded');
                    var moreText = btn.getAttribute('data-more-text');
                    var lessText = btn.getAttribute('data-less-text');
                    btn.textContent = isExpanded ? lessText : moreText;
                });
            });
        }
    })();
    </script>
@endpush