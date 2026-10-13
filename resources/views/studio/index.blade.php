<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Thiết kế của bạn - MiniGrove
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f7f5;

            color: #27332c;

        }



        .studio-header {

            background: white;

            border-bottom: 1px solid #e2e8e3;

            padding: 18px 30px;

        }



        .studio-header-inner {

            max-width: 1250px;

            margin: auto;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }



        .brand {

            font-size: 24px;

            font-weight: bold;

            color: #28633d;

        }



        .brand span {

            color: #777;

            font-size: 14px;

            font-weight: normal;

            margin-left: 8px;

        }



        .studio-container {

            max-width: 1250px;

            margin: 30px auto;

            padding: 0 20px 50px;

        }



        .studio-title {

            text-align: center;

            margin-bottom: 30px;

        }



        .studio-title h1 {

            margin-bottom: 8px;

            color: #245c39;

        }



        .studio-title p {

            color: #6b756e;

        }



        .alert-error {

            background: #fdecec;

            color: #9e3434;

            border: 1px solid #efcaca;

            padding: 15px;

            border-radius: 9px;

            margin-bottom: 20px;

        }



        .studio-section {

            background: white;

            border-radius: 13px;

            padding: 22px;

            margin-bottom: 22px;

            box-shadow:
                0 3px 14px rgba(0,0,0,.05);

        }



        .studio-section h2 {

            margin-top: 0;

            margin-bottom: 6px;

            font-size: 20px;

        }



        .section-description {

            color: #788079;

            margin-top: 0;

            margin-bottom: 18px;

        }



        /* PRODUCT GRID */

        .product-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(190px,1fr)
                );

            gap: 15px;

        }



        .product-option {

            position: relative;

        }



        .product-option input {

            position: absolute;

            opacity: 0;

        }



        .product-card {

            display: block;

            height: 100%;

            border: 2px solid #e0e6e2;

            border-radius: 11px;

            overflow: hidden;

            background: white;

            cursor: pointer;

            transition: .2s;

        }



        .product-card:hover {

            border-color: #86ac91;

            transform: translateY(-2px);

        }



        .product-option input:checked
        + .product-card {

            border-color: #38714b;

            box-shadow:
                0 0 0 2px rgba(56,113,75,.12);

        }



        .product-image {

            width:100%;

            height:125px;

            object-fit:cover;

            background:#edf1ee;

        }



        .no-image {

            height:125px;

            display:flex;

            align-items:center;

            justify-content:center;

            background:#edf1ee;

            color:#818981;

        }



        .product-info {

            padding:13px;

        }



        .product-name {

            font-weight:bold;

            margin-bottom:6px;

        }



        .product-sku {

            color:#888;

            font-size:12px;

            margin-bottom:9px;

        }



        .product-bottom {

            display:flex;

            justify-content:space-between;

            gap:8px;

            align-items:center;

        }



        .price {

            color:#b34e38;

            font-weight:bold;

        }



        .stock {

            color:#69746c;

            font-size:12px;

        }



        /* =========================================================
           THIẾT KẾ CỦA BẠN + TÌM SẢN PHẨM
        ========================================================= */

        .studio-builder-form {
            display:grid;
            grid-template-columns:minmax(0, 1fr) 330px;
            gap:22px;
            align-items:start;
        }

        .studio-builder-form > .studio-section,
        .studio-builder-form > .studio-submit {
            grid-column:1;
        }

        .studio-design-summary {
            grid-column:2;
            /*
             * 6 bước chính, trong đó bước 2 tách 2A + 2B
             * => tổng cộng 7 khối lựa chọn.
             * Chỉ span tới hết bước 6 để không tạo hàng rỗng
             * và kéo trang xuống quá dài.
             */
            grid-row:1 / span 7;
            position:sticky;
            top:22px;
            padding:20px;
            border:1px solid #d9e4de;
            border-radius:16px;
            background:#ffffff;
            box-shadow:0 10px 28px rgba(24, 72, 51, .08);
        }

        .studio-design-summary h2 {
            margin:0 0 6px;
            color:#174d37;
            font-size:21px;
        }

        .studio-design-summary > p {
            margin:0 0 16px;
            color:#69746c;
            font-size:13px;
            line-height:1.55;
        }

        .studio-summary-list {
            display:grid;
            border-top:1px solid #edf2ef;
        }

        .studio-summary-row {
            display:grid;
            grid-template-columns:82px minmax(0, 1fr);
            gap:10px;
            padding:11px 0;
            border-bottom:1px solid #edf2ef;
        }

        .studio-summary-label {
            color:#7b8780;
            font-size:12px;
        }

        .studio-summary-value {
            min-width:0;
            color:#23372e;
            font-size:13px;
            font-weight:700;
            text-align:right;
        }

        .studio-summary-empty {
            color:#9aa39e;
            font-weight:500;
        }

        .studio-summary-total {
            display:flex;
            justify-content:space-between;
            align-items:flex-end;
            gap:12px;
            margin-top:17px;
            padding-top:16px;
        }

        .studio-summary-total span {
            color:#69746c;
            font-size:13px;
        }

        .studio-summary-total strong {
            color:#b34e38;
            font-size:22px;
        }

        .studio-summary-preview,
        .preview-button {
            width:100%;
            min-height:46px;
            margin-top:16px;
            padding:0 18px;
            border:0;
            border-radius:9px;
            background:#174d37;
            color:#ffffff;
            font-family:inherit;
            font-size:14px;
            font-weight:700;
            cursor:pointer;
            transition:.2s;
        }

        .studio-summary-preview:hover,
        .preview-button:hover {
            background:#216548;
        }

        .studio-preview-validation {
            display:none;
            margin:14px 0 0;
            padding:12px 13px;
            border:1px solid #efc9bf;
            border-radius:9px;
            background:#fff5f2;
            color:#9a3f2c;
            font-size:12px;
            line-height:1.55;
        }

        .studio-preview-validation.is-visible {
            display:block;
        }

        .studio-submit {
            /* Giữ markup cũ để không mất logic form,
               nhưng giao diện mới chỉ dùng nút trong bảng tóm tắt. */
            display:none;
            margin-top:2px;
        }

        .preview-button {
            width:auto;
            min-width:220px;
            margin-top:0;
        }

        .studio-search-toolbar {
            display:flex;
            align-items:center;
            gap:10px;
            margin:14px 0 16px;
        }

        .studio-search-wrap {
            position:relative;
            flex:1;
            min-width:0;
        }

        .studio-search-wrap span {
            position:absolute;
            left:12px;
            top:50%;
            transform:translateY(-50%);
            pointer-events:none;
        }

        .studio-search-input {
            width:100%;
            height:42px;
            padding:0 13px 0 37px;
            border:1px solid #d7e1dc;
            border-radius:9px;
            background:#fff;
            color:#23372e;
            font-family:inherit;
            outline:none;
        }

        .studio-search-input:focus {
            border-color:#2f7a57;
            box-shadow:0 0 0 3px rgba(47, 122, 87, .09);
        }

        .studio-show-all {
            height:42px;
            padding:0 13px;
            border:1px solid #cfe0d5;
            border-radius:9px;
            background:#f2f8f4;
            color:#174d37;
            font-family:inherit;
            font-size:12px;
            font-weight:700;
            cursor:pointer;
            white-space:nowrap;
        }

        .studio-products-page-link {
            height:42px;
            padding:0 13px;

            display:inline-flex;
            align-items:center;
            justify-content:center;

            border:1px solid #174d37;
            border-radius:9px;

            background:#ffffff;
            color:#174d37;

            text-decoration:none;
            font-size:12px;
            font-weight:700;
            white-space:nowrap;

            transition:.2s;
        }

        .studio-products-page-link:hover {
            background:#174d37;
            color:#ffffff;
        }

        .studio-search-empty {
            display:none;
            padding:14px;
            border:1px dashed #cfdad4;
            border-radius:9px;
            color:#7a857f;
            text-align:center;
            font-size:13px;
        }

        .studio-home-link {
            display:inline-flex;
            align-items:center;
            gap:7px;

            margin-bottom:18px;

            color:#174d37;
            text-decoration:none;

            font-size:14px;
            font-weight:700;

            transition:
                color .2s ease,
                transform .2s ease;
        }

        .studio-home-link:hover {
            color:#216548;
            transform:translateX(-2px);
        }

        .studio-home-link:focus-visible {
            outline:2px solid currentColor;
            outline-offset:4px;
            border-radius:4px;
        }

        @media (max-width:1050px) {
            .studio-builder-form {
                grid-template-columns:1fr;
            }

            .studio-builder-form > .studio-section,
            .studio-builder-form > .studio-submit,
            .studio-design-summary {
                grid-column:1;
            }

            .studio-design-summary {
                grid-row:auto;
                position:static;
                order:-1;
            }
        }

        @media (max-width:640px) {
            .studio-search-toolbar {
                align-items:stretch;
                flex-direction:column;
            }

            .studio-show-all,
            .studio-products-page-link,
            .preview-button {
                width:100%;
            }
        }

    </style>


</head>


<body>



<header class="studio-header">

    <div class="studio-header-inner">


        <div class="brand">

            MiniGrove

            <span>
                Nhỏ một khu rừng,
                lớn một thế giới.
            </span>

        </div>


        <div>
            Thiết kế của bạn
        </div>


    </div>

</header>




<main class="studio-container">


<a
    href="{{ route('home') }}"
    class="studio-home-link"
>
    <span aria-hidden="true">←</span>
    Quay lại trang chủ
</a>


<div class="studio-title">

    <h1>
        Thiết kế của bạn
    </h1>


    <p>
        Tự tạo một hệ sinh thái nhỏ
        theo phong cách của riêng bạn.
    </p>


</div>




@if ($errors->any())

<div class="alert-error">

<strong>
    Cấu hình chưa hợp lệ:
</strong>


<div>

    {{ $errors->first() }}

</div>


</div>

@endif




<form
    action="{{ route('studio.preview') }}"
    method="POST"
    class="studio-builder-form"
>

@csrf

@if ($editingDesign)
    <input
        type="hidden"
        name="design_id"
        value="{{ $editingDesign->id }}"
    >
@endif

@php
    $studioProductMeta = $products
        ->mapWithKeys(function ($product) {
            return [
                (string) $product->id => [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'type' => $product->product_type,
                    'ecosystem' => $product->ecosystem_type,
                    'price' => (float) (
                        $product->sale_price
                        ?? $product->list_price
                    ),
                ],
            ];
        })
        ->all();
@endphp

<aside class="studio-design-summary" aria-live="polite">
    <h2>Thiết kế của bạn</h2>

    <p>
        Theo dõi các lựa chọn và giá tạm tính trong lúc bạn thiết kế Terrarium.
    </p>

    <div class="studio-summary-list">
        @foreach ([
            'container_id' => 'Bình',
            'plant_id' => 'Cây',
            'moss_id' => 'Rêu',
            'substrate_id' => 'Nền',
            'stone_id' => 'Đá',
            'accessory_id' => 'Phụ kiện',
            'style' => 'Phong cách',
        ] as $field => $label)
            <div class="studio-summary-row">
                <span class="studio-summary-label">
                    {{ $label }}
                </span>

                <span
                    class="studio-summary-value studio-summary-empty"
                    data-summary-field="{{ $field }}"
                >
                    Chưa chọn
                </span>
            </div>
        @endforeach
    </div>

    <div class="studio-summary-total">
        <span>Tạm tính</span>
        <strong data-studio-total>0đ</strong>
    </div>

    <button
        type="submit"
        class="studio-summary-preview"
    >
        Kiểm tra & Xem Preview
    </button>

    <div
        class="studio-preview-validation"
        data-preview-validation
        role="alert"
    ></div>
</aside>



{{-- ================================================= --}}
{{-- BƯỚC 1: BÌNH --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>
    1. Chọn bình
    <span class="required">*</span>
</h2>


<p class="section-description">

    Bình quyết định môi trường chính
    của Terrarium.

</p>




<div class="product-grid">


@foreach ($containers as $product)



@php

$image =
    $product->images
        ->firstWhere(
            'is_primary',
            true
        )
    ??
    $product->images->first();

@endphp




<div class="product-option">



<input

type="radio"

name="container_id"

id="container_{{ $product->id }}"

value="{{ $product->id }}"

@checked(
    old(
        'container_id',
        $defaults['container_id'] ?? null
    )
    ==
    $product->id
)

>




<label

class="product-card"

for="container_{{ $product->id }}"

>



@if($image)


<img

class="product-image"

src="{{ asset(
    'storage/'.$image->image_path
) }}"

alt="{{ $product->name }}"

>


@else


<div class="no-image">

Không ảnh

</div>


@endif




<div class="product-info">


<div class="product-name">

{{ $product->name }}

</div>



<div class="product-sku">

{{ $product->sku }}

</div>




<div class="product-bottom">


<span class="price">

{{ number_format(
    (float)(
        $product->sale_price
        ??
        $product->list_price
    ),
    0,
    ',',
    '.'
) }}đ


</span>



<span class="stock">

Còn

{{ $product->stock_quantity }}

</span>



</div>



</div>




</label>


</div>



@endforeach


</div>



</section>
{{-- ================================================= --}}
{{-- BƯỚC 2: CÂY --}}
{{-- ================================================= --}}


<section class="studio-section">


<h2>
    2A. Chọn cây
</h2>


<p class="section-description">

    Có thể bỏ qua nếu bạn chọn rêu.

</p>




<div class="optional-none">

<label>


<input

type="radio"

name="plant_id"

value=""

@checked(
    !old(
        'plant_id',
        $defaults['plant_id'] ?? null
    )
)

>


Không chọn cây


</label>


</div>





<div class="product-grid">


@foreach ($plants as $product)



@php

$image =
    $product->images
        ->firstWhere(
            'is_primary',
            true
        )
    ??
    $product->images->first();

@endphp




<div class="product-option">



<input

type="radio"

name="plant_id"

id="plant_{{ $product->id }}"

value="{{ $product->id }}"

@checked(
    old(
        'plant_id',
        $defaults['plant_id'] ?? null
    )
    ==
    $product->id
)

>



<label

class="product-card"

for="plant_{{ $product->id }}"

>



@if($image)


<img

class="product-image"

src="{{ asset(
    'storage/'.$image->image_path
) }}"

alt="{{ $product->name }}"

>


@else


<div class="no-image">

Không ảnh

</div>


@endif




<div class="product-info">


<div class="product-name">

{{ $product->name }}

</div>



<div class="product-sku">

{{ $product->sku }}

</div>




<div class="product-bottom">


<span class="price">


{{ number_format(

    (float)(
        $product->sale_price
        ??
        $product->list_price
    ),

    0,

    ',',

    '.'

) }}đ



</span>



<span class="stock">

Còn

{{ $product->stock_quantity }}

</span>



</div>



</div>



</label>



</div>



@endforeach


</div>



</section>







{{-- ================================================= --}}
{{-- BƯỚC 3: RÊU --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

2B. Chọn rêu

</h2>



<div class="optional-none">


<label>


<input

type="radio"

name="moss_id"

value=""

@checked(
    !old(
        'moss_id',
        $defaults['moss_id'] ?? null
    )
)

>


Không chọn rêu


</label>


</div>





<div class="product-grid">



@foreach ($mosses as $product)


@include(

'studio.partials.product-radio',

[

'product' => $product,

'field' => 'moss_id',

'prefix' => 'moss'

]

)



@endforeach



</div>



</section>









{{-- ================================================= --}}
{{-- BƯỚC 4: NỀN --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

3. Chọn nền / giá thể

<span class="required">*</span>

</h2>




<div class="product-grid">



@foreach ($substrates as $product)



@include(

'studio.partials.product-radio',

[

'product' => $product,

'field' => 'substrate_id',

'prefix' => 'substrate'

]

)



@endforeach



</div>



</section>









{{-- ================================================= --}}
{{-- BƯỚC 5: ĐÁ --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

4. Chọn đá

</h2>




<div class="optional-none">


<label>


<input

type="radio"

name="stone_id"

value=""

@checked(
    !old(
        'stone_id',
        $defaults['stone_id'] ?? null
    )
)

>


Không chọn đá


</label>


</div>





<div class="product-grid">



@foreach ($stones as $product)



@include(

'studio.partials.product-radio',

[

'product' => $product,

'field' => 'stone_id',

'prefix' => 'stone'

]

)



@endforeach



</div>



</section>









{{-- ================================================= --}}
{{-- BƯỚC 6: PHỤ KIỆN --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

5. Chọn phụ kiện

</h2>




<div class="optional-none">


<label>


<input

type="radio"

name="accessory_id"

value=""

@checked(
    !old(
        'accessory_id',
        $defaults['accessory_id'] ?? null
    )
)

>


Không chọn phụ kiện


</label>


</div>





<div class="product-grid">



@foreach ($accessories as $product)



@include(

'studio.partials.product-radio',

[

'product' => $product,

'field' => 'accessory_id',

'prefix' => 'accessory'

]

)



@endforeach



</div>



</section>








{{-- ================================================= --}}
{{-- PHONG CÁCH --}}
{{-- ================================================= --}}



<section class="studio-section">


<h2>

6. Chọn phong cách

<span class="required">*</span>

</h2>




@php

$styles = [

'natural' => 'Tự nhiên',

'forest' => 'Rừng xanh',

'zen' => 'Zen',

'fairy' => 'Cổ tích',

'tropical' => 'Nhiệt đới',

'minimal' => 'Tối giản',

];


@endphp





<div class="style-grid">



@foreach ($styles as $value => $label)



<div class="style-option">


<input

type="radio"

name="style"

id="style_{{ $value }}"

value="{{ $value }}"

@checked(
    old(
        'style',
        $defaults['style'] ?? null
    ) === $value
)

>



<label

class="style-card"

for="style_{{ $value }}"

>


{{ $label }}


</label>


</div>



@endforeach



</div>



</section>







<div class="studio-submit">


<button

type="submit"

class="preview-button"

>


Kiểm tra & Xem Preview


</button>



</div>





</form>




<script>
document.addEventListener('DOMContentLoaded', function () {
    const productMeta = @json($studioProductMeta);
    const productsIndexUrl = @json(route('products.index'));

    const pickerConfigs = [
        { field: 'container_id', label: 'bình', type: 'container' },
        { field: 'plant_id', label: 'cây', type: 'plant' },
        { field: 'moss_id', label: 'rêu', type: 'moss' },
        { field: 'substrate_id', label: 'nền', type: 'substrate' },
        { field: 'stone_id', label: 'đá', type: 'stone' },
        { field: 'accessory_id', label: 'phụ kiện', type: 'accessory' },
    ];

    const formatMoney = function (value) {
        return new Intl.NumberFormat('vi-VN')
            .format(Math.round(value)) + 'đ';
    };

    const setupPicker = function (config) {
        const firstRadio = document.querySelector(
            'input[name="' + config.field + '"]'
        );

        if (!firstRadio) {
            return;
        }

        const section = firstRadio.closest('.studio-section');
        const grid = section?.querySelector('.product-grid');

        if (!section || !grid) {
            return;
        }

        const options = Array.from(
            grid.querySelectorAll('.product-option')
        );

        if (!options.length) {
            return;
        }

        const toolbar = document.createElement('div');
        toolbar.className = 'studio-search-toolbar';
        toolbar.innerHTML =
            '<div class="studio-search-wrap">'
            + '<span aria-hidden="true">🔍</span>'
            + '<input type="search" class="studio-search-input" '
            + 'placeholder="Tìm ' + config.label + '..." '
            + 'aria-label="Tìm ' + config.label + '">'
            + '</div>'
            + '<button type="button" class="studio-show-all">'
            + 'Xem tất cả ' + config.label
            + '</button>'
            + '<a class="studio-products-page-link" href="'
            + productsIndexUrl
            + '?type=' + encodeURIComponent(config.type)
            + '">Trang sản phẩm →</a>';

        const empty = document.createElement('div');
        empty.className = 'studio-search-empty';
        empty.textContent =
            'Không tìm thấy ' + config.label + ' phù hợp.';

        grid.before(toolbar);
        grid.after(empty);

        const searchInput =
            toolbar.querySelector('.studio-search-input');
        const toggleButton =
            toolbar.querySelector('.studio-show-all');

        let expanded = false;
        const initialLimit = 4;

        if (options.length <= initialLimit) {
            toggleButton.style.display = 'none';
        }

        const render = function () {
            const keyword = searchInput.value
                .trim()
                .toLocaleLowerCase('vi');

            let visibleIndex = 0;
            let visibleCount = 0;

            options.forEach(function (option) {
                const text = option.textContent
                    .toLocaleLowerCase('vi');
                const radio = option.querySelector(
                    'input[type="radio"]'
                );
                const isSelected = Boolean(radio?.checked);
                const matches =
                    keyword === ''
                    || text.includes(keyword);

                const withinLimit =
                    expanded
                    || keyword !== ''
                    || visibleIndex < initialLimit
                    || isSelected;

                const visible = matches && withinLimit;

                option.style.display = visible ? '' : 'none';

                if (matches) {
                    visibleIndex++;
                }

                if (visible) {
                    visibleCount++;
                }
            });

            empty.style.display =
                visibleCount > 0 ? 'none' : 'block';

            toggleButton.textContent = expanded
                ? 'Thu gọn'
                : 'Xem tất cả ' + config.label;
        };

        searchInput.addEventListener('input', render);

        toggleButton.addEventListener('click', function () {
            expanded = !expanded;
            render();
        });

        section.addEventListener('change', render);
        render();
    };

    pickerConfigs.forEach(setupPicker);

    const summaryFields = [
        'container_id',
        'plant_id',
        'moss_id',
        'substrate_id',
        'stone_id',
        'accessory_id',
    ];

    const updateSummary = function () {
        let total = 0;

        summaryFields.forEach(function (field) {
            const target = document.querySelector(
                '[data-summary-field="' + field + '"]'
            );
            const selected = document.querySelector(
                'input[name="' + field + '"]:checked'
            );

            if (!target) {
                return;
            }

            const product =
                selected?.value
                    ? productMeta[selected.value]
                    : null;

            if (product) {
                target.textContent = product.name;
                target.classList.remove(
                    'studio-summary-empty'
                );
                total += Number(product.price || 0);
            } else {
                target.textContent = 'Chưa chọn';
                target.classList.add(
                    'studio-summary-empty'
                );
            }
        });

        const styleTarget = document.querySelector(
            '[data-summary-field="style"]'
        );
        const selectedStyle = document.querySelector(
            'input[name="style"]:checked'
        );

        if (styleTarget) {
            if (selectedStyle) {
                const label = document.querySelector(
                    'label[for="' + selectedStyle.id + '"]'
                );

                styleTarget.textContent =
                    label?.textContent.trim()
                    || selectedStyle.value;

                styleTarget.classList.remove(
                    'studio-summary-empty'
                );
            } else {
                styleTarget.textContent = 'Chưa chọn';
                styleTarget.classList.add(
                    'studio-summary-empty'
                );
            }
        }

        const totalTarget = document.querySelector(
            '[data-studio-total]'
        );

        if (totalTarget) {
            totalTarget.textContent = formatMoney(total);
        }
    };

    document
        .querySelector('.studio-builder-form')
        ?.addEventListener('change', updateSummary);

    const builderForm = document.querySelector(
        '.studio-builder-form'
    );

    builderForm?.addEventListener('submit', function (event) {
        const missing = [];

        const selectedContainer = builderForm.querySelector(
            'input[name="container_id"]:checked'
        );
        const selectedPlant = builderForm.querySelector(
            'input[name="plant_id"]:checked'
        );
        const selectedMoss = builderForm.querySelector(
            'input[name="moss_id"]:checked'
        );
        const selectedSubstrate = builderForm.querySelector(
            'input[name="substrate_id"]:checked'
        );
        const selectedStyle = builderForm.querySelector(
            'input[name="style"]:checked'
        );

        if (!selectedContainer?.value) {
            missing.push({
                field: 'container_id',
                message: 'Chọn một bình.'
            });
        }

        if (
            !selectedPlant?.value
            && !selectedMoss?.value
        ) {
            missing.push({
                field: 'plant_id',
                message: 'Chọn ít nhất một cây hoặc rêu.'
            });
        }

        if (!selectedSubstrate?.value) {
            missing.push({
                field: 'substrate_id',
                message: 'Chọn nền / giá thể.'
            });
        }

        if (!selectedStyle?.value) {
            missing.push({
                field: 'style',
                message: 'Chọn phong cách.'
            });
        }

        const validationBox = document.querySelector(
            '[data-preview-validation]'
        );

        if (!missing.length) {
            validationBox?.classList.remove('is-visible');
            return;
        }

        event.preventDefault();

        if (validationBox) {
            validationBox.innerHTML =
                '<strong>Thiết kế chưa đủ thông tin:</strong><br>'
                + missing
                    .map(item => '• ' + item.message)
                    .join('<br>');

            validationBox.classList.add('is-visible');
        }

        const firstMissingInput = builderForm.querySelector(
            'input[name="' + missing[0].field + '"]'
        );

        firstMissingInput
            ?.closest('.studio-section')
            ?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
    });

    updateSummary();
});
</script>

</main>



</body>


</html>