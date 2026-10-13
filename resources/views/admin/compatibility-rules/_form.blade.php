@php
    $editingRule = $compatibilityRule ?? null;

    $typeLabels = [
        'container' => 'Bình',
        'plant'     => 'Cây',
        'moss'      => 'Rêu',
        'substrate' => 'Nền / Giá thể',
        'stone'     => 'Đá',
        'accessory' => 'Phụ kiện',
    ];
@endphp

{{-- =========================================================
     SECTION 1: SOURCE & TARGET PRODUCTS
========================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect width="7" height="7" x="3" y="3" rx="1"/>
                <rect width="7" height="7" x="14" y="14" rx="1"/>
                <path d="m14 7 3 3-3 3"/>
                <path d="M7 10h10"/>
            </svg>
            1. Cặp thành phần tham chiếu (Nguồn & Đích)
        </h3>
        <p class="admin-form-section-desc">
            Xác định rõ sản phẩm nguồn (A) và sản phẩm đích (B) để áp dụng điều kiện kết hợp trong Studio.
        </p>
    </div>

    <div class="admin-form-grid">
        {{-- SẢN PHẨM NGUỒN --}}
        <div class="form-group">
            <label for="source_product_id">
                Sản phẩm nguồn (A) <span style="color: var(--admin-danger);">*</span>
            </label>

            <select
                id="source_product_id"
                name="source_product_id"
                aria-describedby="source_help"
            >
                <option value="">-- Chọn sản phẩm nguồn --</option>
                @foreach ($products as $product)
                    <option
                        value="{{ $product->id }}"
                        @selected(old('source_product_id', $editingRule?->source_product_id) == $product->id)
                    >
                        [{{ $typeLabels[$product->ecosystem_type] ?? $product->ecosystem_type }}] {{ $product->name }} @if ($product->sku) - {{ $product->sku }} @endif
                    </option>
                @endforeach
            </select>

            <small id="source_help" class="help-text">
                Thành phần gốc được chọn trước trong Studio.
            </small>

            @error('source_product_id')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- SẢN PHẨM ĐÍCH --}}
        <div class="form-group">
            <label for="target_product_id">
                Sản phẩm đích (B) <span style="color: var(--admin-danger);">*</span>
            </label>

            <select
                id="target_product_id"
                name="target_product_id"
                aria-describedby="target_help"
            >
                <option value="">-- Chọn sản phẩm đích --</option>
                @foreach ($products as $product)
                    <option
                        value="{{ $product->id }}"
                        @selected(old('target_product_id', $editingRule?->target_product_id) == $product->id)
                    >
                        [{{ $typeLabels[$product->ecosystem_type] ?? $product->ecosystem_type }}] {{ $product->name }} @if ($product->sku) - {{ $product->sku }} @endif
                    </option>
                @endforeach
            </select>

            <small id="target_help" class="help-text">
                Thành phần dự kiến kết hợp cùng sản phẩm nguồn (khác sản phẩm nguồn).
            </small>

            @error('target_product_id')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- =========================================================
     SECTION 2: COMPATIBILITY CONDITION & REASON
========================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            2. Điều kiện & Lý do tương thích
        </h3>
        <p class="admin-form-section-desc">
            Thiết lập kết quả kiểm tra tương thích và lý do hiển thị cho khách hàng khi bị chặn kết hợp.
        </p>
    </div>

    {{-- KẾT QUẢ TƯƠNG THÍCH --}}
    <div class="form-group" style="max-width: 480px;">
        <label for="is_compatible">
            Kết quả tương thích <span style="color: var(--admin-danger);">*</span>
        </label>

        <select
            id="is_compatible"
            name="is_compatible"
            aria-describedby="compat_help"
        >
            <option value="">-- Chọn kết quả --</option>
            <option
                value="1"
                @selected((string) old('is_compatible', $editingRule ? (int) $editingRule->is_compatible : '') === '1')
            >
                Tương thích (Cho phép kết hợp)
            </option>
            <option
                value="0"
                @selected((string) old('is_compatible', $editingRule ? (int) $editingRule->is_compatible : '') === '0')
            >
                Không tương thích (Chặn kết hợp)
            </option>
        </select>

        <small id="compat_help" class="help-text">
            Nếu chọn "Không tương thích", khách hàng sẽ không thể cùng chọn 2 sản phẩm này trong Studio.
        </small>

        @error('is_compatible')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    {{-- LÝ DO --}}
    <div class="form-group">
        <label for="reason">
            Lý do / Giải thích
        </label>

        <textarea
            id="reason"
            name="reason"
            rows="3"
            maxlength="2000"
            placeholder="Ví dụ: Cây ưa ẩm cao không thích hợp sinh trưởng trong bình hở; hoặc rêu nước không sống cùng giá thể khô..."
            aria-describedby="reason_help"
        >{{ old('reason', $editingRule?->reason) }}</textarea>

        <small id="reason_help" class="help-text">
            Bắt buộc nhập khi chọn "Không tương thích". Thông điệp này sẽ hiển thị trực tiếp cho khách hàng trong Studio.
        </small>

        @error('reason')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- =========================================================
     SECTION 3: STATUS / ACTIVE
========================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 2v20"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            3. Trạng thái áp dụng
        </h3>
        <p class="admin-form-section-desc">
            Bật hoặc tạm dừng áp dụng quy tắc này trong hệ thống Studio.
        </p>
    </div>

    <div class="admin-options-panel">
        <div class="checkbox-row">
            <input
                type="hidden"
                name="is_active"
                value="0"
            >

            <input
                type="checkbox"
                id="is_active"
                name="is_active"
                value="1"
                @checked(old('is_active', $editingRule ? $editingRule->is_active : true))
            >

            <label for="is_active" style="cursor: pointer;">
                <strong>Kích hoạt quy tắc (Active)</strong>
                <span style="display: block; font-size: 12px; color: var(--admin-text-secondary); font-weight: normal; margin-top: 2px;">
                    Quy tắc chỉ có hiệu lực kiểm tra trong Studio khi ở trạng thái Active.
                </span>
            </label>
        </div>
    </div>

    @error('is_active')
        <div class="error">{{ $message }}</div>
    @enderror
</div>