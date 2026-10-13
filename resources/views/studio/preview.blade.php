{{-- 
    Preview MiniGrove Studio
    Product version
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview - MiniGrove Studio</title>

    {{-- Google Fonts: Be Vietnam Pro --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #F7F9F7;
            color: #1A2E26;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px 16px;
        }

        .preview-container {
            width: 100%;
            max-width: 1080px;
            margin: 0 auto;
        }

        .preview-card {
            background: #FFFFFF;
            border: 1px solid #E2E8E4;
            border-radius: 14px;
            padding: 18px 24px;
            box-shadow: 0 4px 20px rgba(23, 63, 53, 0.06);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* 1. Header */
        .preview-header {
            margin: 0;
        }

        .preview-header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #173F35;
            line-height: 1.25;
            letter-spacing: -0.01em;
        }

        /* 2. Success Alert Box */
        .success-box {
            padding: 7px 14px;
            background: #EBF6EE;
            border: 1px solid #BBDCC5;
            border-radius: 7px;
            color: #1E663B;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1.4;
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
        }

        /* 3. Design Meta Chips */
        .preview-meta-chips {
            display: flex;
            align-items: center;
            gap: 8px 12px;
            flex-wrap: wrap;
            background: #F4F7F5;
            border: 1px solid #E1E8E3;
            border-radius: 8px;
            padding: 6px 14px;
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            white-space: nowrap;
        }

        .meta-chip:not(:last-child)::after {
            content: "·";
            color: #B5C4BC;
            font-weight: 700;
            margin-left: 10px;
        }

        .meta-label {
            color: #617067;
        }

        .meta-value {
            color: #102F28;
            font-weight: 600;
        }

        /* 4. Product Table & Subtotal Box */
        .preview-table-box {
            border: 1px solid #E1E8E3;
            border-radius: 9px;
            overflow: hidden;
            background: #FFFFFF;
        }

        .preview-table-scroll {
            max-height: 275px;
            overflow-y: auto;
            overflow-x: auto;
        }

        .preview-table-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .preview-table-scroll::-webkit-scrollbar-thumb {
            background: #CCD7D0;
            border-radius: 4px;
        }

        .preview-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .preview-table th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #F1F5F2;
            color: #173F35;
            font-size: 12.5px;
            font-weight: 600;
            padding: 8px 12px;
            border-bottom: 1px solid #DCE4DE;
            white-space: nowrap;
            text-align: left;
        }

        .preview-table td {
            padding: 7px 12px;
            border-bottom: 1px solid #EEF2EF;
            color: #27332C;
            vertical-align: middle;
            line-height: 1.35;
        }

        .preview-table tr:last-child td {
            border-bottom: none;
        }

        .preview-table tr:hover td {
            background: #FAFBF9;
        }

        .col-product {
            text-align: left;
            font-weight: 500;
        }

        .col-type {
            text-align: left;
            color: #55665D;
            white-space: nowrap;
        }

        .col-sku {
            text-align: left;
            white-space: nowrap;
        }

        .col-sku code {
            font-family: SFMono-Regular, Consolas, 'Liberation Mono', Menlo, monospace;
            font-size: 12px;
            color: #4C5D55;
            background: #F3F6F4;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .col-price {
            text-align: right;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .col-qty {
            text-align: center;
            white-space: nowrap;
        }

        .col-subtotal {
            text-align: right;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .col-subtotal strong {
            color: #102F28;
            font-weight: 600;
        }

        /* 5. Subtotal Summary Bar */
        .preview-table-footer {
            display: flex;
            align-items: baseline;
            justify-content: flex-end;
            gap: 10px;
            padding: 8px 16px;
            background: #F8FAF8;
            border-top: 1px solid #E1E8E3;
        }

        .footer-label {
            font-size: 13px;
            font-weight: 600;
            color: #4A5852;
        }

        .total-price {
            font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 19px;
            font-weight: 700;
            color: #C46F4A;
            font-variant-numeric: tabular-nums;
            letter-spacing: normal;
        }

        /* 6. Notice */
        .notice {
            padding: 7px 12px;
            border-radius: 7px;
            background: #FFF9EE;
            border: 1px solid rgba(196, 111, 74, 0.25);
            color: #8C561B;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .notice strong {
            color: #7A470E;
            flex-shrink: 0;
        }

        /* 7. Bottom Actions */
        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 0;
        }

        .actions-form {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .design-name-input {
            height: 38px;
            padding: 0 12px;
            border: 1px solid #CFDAD3;
            border-radius: 7px;
            font-family: inherit;
            font-size: 13px;
            color: #173F35;
            width: 220px;
            outline: none;
            background: #FFFFFF;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .design-name-input:focus {
            border-color: #173F35;
            box-shadow: 0 0 0 2px rgba(23, 63, 53, 0.12);
        }

        .btn {
            height: 38px;
            padding: 0 16px;
            border-radius: 7px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .btn-secondary {
            background: #F0F4F1;
            color: #173F35;
            border: 1px solid #D5E0D8;
        }

        .btn-secondary:hover {
            background: #E4ECE6;
            border-color: #C0D0C4;
        }

        .btn-primary {
            background: #173F35;
            color: #FFFFFF;
            border: 1px solid #173F35;
        }

        .btn-primary:hover {
            background: #1F4E42;
            border-color: #1F4E42;
        }

        /* 8. Responsive */
        @media (max-width: 768px) {
            body {
                padding: 16px 12px;
                display: block;
                min-height: auto;
            }

            .preview-card {
                padding: 16px;
                gap: 12px;
            }

            .preview-meta-chips {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
                padding: 10px 12px;
            }

            .meta-chip {
                width: 100%;
                justify-content: space-between;
                padding: 2px 0;
            }

            .meta-chip:not(:last-child)::after {
                display: none;
            }

            .preview-table-scroll {
                max-height: none;
            }

            .preview-table {
                min-width: 560px;
            }

            .preview-table-footer {
                justify-content: space-between;
            }

            .notice {
                flex-direction: column;
                gap: 2px;
            }

            .actions {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .actions-form {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
                gap: 8px;
            }

            .design-name-input {
                width: 100%;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<main class="preview-container">
    <div class="preview-card">

        {{-- 1. Tiêu đề --}}
        <div class="preview-header">
            <h1>Preview MiniGrove</h1>
        </div>

        {{-- 2. Thông báo kiểm tra tương thích & tồn kho --}}
        <div class="success-box">
            <span>✓ Cấu hình hợp lệ, các sản phẩm đã vượt qua kiểm tra tương thích và tồn kho.</span>
        </div>

        {{-- 3. Thông tin thiết kế --}}
        <div class="preview-meta-chips">
            <div class="meta-chip">
                <span class="meta-label">Phong cách:</span>
                <strong class="meta-value">{{ $style }}</strong>
            </div>

            <div class="meta-chip">
                <span class="meta-label">Hệ sinh thái:</span>
                <strong class="meta-value">{{ $compatibility['ecosystem_type'] }}</strong>
            </div>

            <div class="meta-chip">
                <span class="meta-label">Bình:</span>
                <strong class="meta-value">{{ $compatibility['container']['name'] }}</strong>
            </div>

            <div class="meta-chip">
                <span class="meta-label">Compatibility Rule:</span>
                <strong class="meta-value">{{ $compatibility['checked_rules'] }}</strong>
            </div>
        </div>

        {{-- 4 & 5. Bảng sản phẩm & Giá tạm tính --}}
        <div class="preview-table-box">
            <div class="preview-table-scroll">
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th class="col-product">Sản phẩm</th>
                            <th class="col-type">Loại</th>
                            <th class="col-sku">SKU</th>
                            <th class="col-price">Đơn giá</th>
                            <th class="col-qty">SL</th>
                            <th class="col-subtotal">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td class="col-product">{{ $item['product_name'] }}</td>
                                <td class="col-type">
                                    @switch($item['product_type'])
                                        @case('container')
                                            Bình
                                            @break
                                        @case('plant')
                                            Cây
                                            @break
                                        @case('moss')
                                            Rêu
                                            @break
                                        @case('substrate')
                                            Nền / Giá thể
                                            @break
                                        @case('stone')
                                            Đá
                                            @break
                                        @case('accessory')
                                            Phụ kiện
                                            @break
                                        @default
                                            {{ $item['product_type'] }}
                                    @endswitch
                                </td>
                                <td class="col-sku">
                                    <code>{{ $item['sku'] }}</code>
                                </td>
                                <td class="col-price">
                                    {{ number_format($item['unit_price'], 0, ',', '.') }}đ
                                </td>
                                <td class="col-qty">
                                    {{ $item['quantity'] }}
                                </td>
                                <td class="col-subtotal">
                                    <strong>{{ number_format($item['subtotal'], 0, ',', '.') }}đ</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 5. Giá tạm tính tích hợp ngay dưới bảng --}}
            <div class="preview-table-footer">
                <span class="footer-label">Giá tạm tính:</span>
                <strong class="total-price">{{ number_format($estimatedPrice, 0, ',', '.') }}đ</strong>
            </div>
        </div>

        {{-- 6. Lưu ý --}}
        <div class="notice">
            <strong>Lưu ý:</strong>
            <span>Giá trên là giá tạm tính theo dữ liệu <strong>sản phẩm</strong> hiện tại. Giá và tồn kho sẽ được kiểm tra lại khi thêm thiết kế vào giỏ hàng và Checkout.</span>
        </div>

        {{-- 7. Các nút cuối trang --}}
        <div class="actions">
            <a href="{{ route('studio.index') }}" class="btn btn-secondary">
                ← Thiết kế lại
            </a>

            @auth
                @if (auth()->user()->role?->name === 'customer')
                    <form
                        method="POST"
                        action="{{
                            $editingDesign
                                ? route('designs.update', $editingDesign)
                                : route('designs.store')
                        }}"
                        class="actions-form"
                    >
                        @csrf

                        @if ($editingDesign)
                            @method('PATCH')
                        @endif

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $editingDesign?->name ?? 'Terrarium của tôi') }}"
                            maxlength="150"
                            required
                            placeholder="Tên thiết kế"
                            class="design-name-input"
                        >

                        <input
                            type="hidden"
                            name="style"
                            value="{{ $style }}"
                        >

                        @foreach ($selectedItems as $index => $selectedItem)
                            <input
                                type="hidden"
                                name="items[{{ $index }}][product_id]"
                                value="{{ $selectedItem['product_id'] }}"
                            >
                            <input
                                type="hidden"
                                name="items[{{ $index }}][quantity]"
                                value="{{ $selectedItem['quantity'] }}"
                            >
                        @endforeach

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            {{ $editingDesign ? 'Cập nhật thiết kế' : 'Lưu thiết kế' }}
                        </button>
                    </form>
                @endif
            @else
                <a
                    href="{{ route('login') }}"
                    class="btn btn-primary"
                >
                    Đăng nhập để lưu thiết kế
                </a>
            @endauth
        </div>

    </div>
</main>

</body>
</html>