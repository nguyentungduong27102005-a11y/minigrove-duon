@php
    $isEdit = isset($voucher);
@endphp

{{-- ======================================================================
     SECTION 1: THÔNG TIN VOUCHER
     ====================================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            <span>1. Thông tin mã ưu đãi</span>
        </h3>
        <p class="admin-form-section-desc">Mã nhận diện và hình thức chiết khấu áp dụng cho đơn hàng.</p>
    </div>

    <div class="admin-form-grid">
        {{-- MÃ VOUCHER --}}
        <div class="form-group">
            <label for="code">
                Mã Voucher <span class="required-mark">*</span>
            </label>
            <input
                type="text"
                id="code"
                name="code"
                class="input-mono"
                style="text-transform: uppercase;"
                maxlength="50"
                value="{{ old('code', $voucher->code ?? '') }}"
                placeholder="Ví dụ: MINIGROVE20"
                required
            >
            <small class="help-text">Hệ thống sẽ tự động chuyển đổi mã thành ký tự in hoa.</small>
            @error('code')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- PHẠM VI VOUCHER --}}
        <div class="form-group">
            <label for="voucher_scope">
                Phạm vi áp dụng <span class="required-mark">*</span>
            </label>
            <select
                id="voucher_scope"
                name="voucher_scope"
                required
            >
                <option
                    value="order"
                    @selected(old('voucher_scope', $voucher->voucher_scope ?? 'order') === 'order')
                >
                    Giảm giá đơn hàng
                </option>
                <option
                    value="shipping"
                    @selected(old('voucher_scope', $voucher->voucher_scope ?? '') === 'shipping')
                >
                    Giảm phí vận chuyển
                </option>
            </select>
            <small class="help-text">Áp dụng chiết khấu vào tiền hàng hoặc phí vận chuyển GHN.</small>
            @error('voucher_scope')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- HIỂN THỊ CÔNG KHAI TẠI CHECKOUT --}}
        <div class="form-group">
            <label for="is_public">
                Hiển thị công khai tại Checkout <span class="required-mark">*</span>
            </label>
            <select
                id="is_public"
                name="is_public"
                required
            >
                <option
                    value="1"
                    @selected((string) old('is_public', isset($voucher) ? (int)$voucher->is_public : 1) === '1')
                >
                    Có – hiển thị công khai để khách chọn
                </option>
                <option
                    value="0"
                    @selected((string) old('is_public', isset($voucher) ? (int)$voucher->is_public : 1) === '0')
                >
                    Không – chỉ dùng khi khách nhập mã
                </option>
            </select>
            <small class="help-text">Voucher riêng tư sẽ không hiện trong danh sách nhưng vẫn nhập mã thủ công được.</small>
            @error('is_public')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- LOẠI GIẢM --}}
        <div class="form-group">
            <label for="discount_type">
                Loại giảm giá <span class="required-mark">*</span>
            </label>
            <select
                id="discount_type"
                name="discount_type"
                required
            >
                <option
                    value="percentage"
                    @selected(old('discount_type', $voucher->discount_type ?? 'percentage') === 'percentage')
                >
                    Giảm theo phần trăm (%)
                </option>
                <option
                    value="fixed"
                    @selected(old('discount_type', $voucher->discount_type ?? '') === 'fixed')
                >
                    Giảm số tiền cố định (VNĐ)
                </option>
            </select>
            @error('discount_type')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- GIÁ TRỊ GIẢM --}}
        <div class="form-group admin-form-full">
            <label for="discount_value">
                Mức giảm chiết khấu <span class="required-mark">*</span>
            </label>
            <input
                type="number"
                id="discount_value"
                name="discount_value"
                min="0.01"
                step="0.01"
                value="{{ old('discount_value', $voucher->discount_value ?? '') }}"
                placeholder="Nhập 15 nếu là 15%, hoặc 50000 nếu là 50.000 VNĐ"
                required
            >
            <small class="help-text">Nếu là %, nhập 10 = giảm 10%. Nếu là số tiền cố định, nhập giá trị VNĐ tương ứng.</small>
            @error('discount_value')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- ======================================================================
     SECTION 2: ĐIỀU KIỆN & GIỚI HẠN
     ====================================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/></svg>
            <span>2. Điều kiện & Giới hạn áp dụng</span>
        </h3>
        <p class="admin-form-section-desc">Quy định giá trị giỏ hàng tối thiểu, mức trần giảm giá và giới hạn số lượt sử dụng.</p>
    </div>

    <div class="admin-form-grid">
        {{-- ĐƠN TỐI THIỂU --}}
        <div class="form-group">
            <label for="min_order_amount">
                Giá trị đơn hàng tối thiểu <span class="required-mark">*</span>
            </label>
            <div class="admin-input-group">
                <input
                    type="number"
                    id="min_order_amount"
                    name="min_order_amount"
                    min="0"
                    step="1000"
                    value="{{ old('min_order_amount', $voucher->min_order_amount ?? 0) }}"
                    required
                >
                <span class="admin-input-suffix">VNĐ</span>
            </div>
            <small class="help-text">Khách hàng cần đạt giá trị giỏ hàng này để mã có hiệu lực.</small>
            @error('min_order_amount')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- GIẢM TỐI ĐA --}}
        <div class="form-group">
            <label for="max_discount_amount">
                Mức giảm tối đa (Trần giảm)
            </label>
            <div class="admin-input-group">
                <input
                    type="number"
                    id="max_discount_amount"
                    name="max_discount_amount"
                    min="1000"
                    step="1000"
                    value="{{ old('max_discount_amount', $voucher->max_discount_amount ?? '') }}"
                    placeholder="Không giới hạn"
                >
                <span class="admin-input-suffix">VNĐ</span>
            </div>
            <small class="help-text">Áp dụng trần tối đa khi dùng voucher % (ví dụ: tối đa 100.000 VNĐ). Để trống = không giới hạn.</small>
            @error('max_discount_amount')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- TỔNG LƯỢT DÙNG --}}
        <div class="form-group">
            <label for="usage_limit">
                Tổng lượt sử dụng toàn hệ thống
            </label>
            <input
                type="number"
                id="usage_limit"
                name="usage_limit"
                min="1"
                value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}"
                placeholder="Để trống nếu không giới hạn"
            >
            <small class="help-text">Voucher sẽ tự động hết hiệu lực khi đạt tổng số lượt này.</small>
            @error('usage_limit')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- GIỚI HẠN MỖI KHÁCH HÀNG --}}
        <div class="form-group">
            <label for="per_user_limit">
                Giới hạn lượt dùng mỗi khách hàng
            </label>
            <input
                type="number"
                id="per_user_limit"
                name="per_user_limit"
                min="1"
                value="{{ old('per_user_limit', $voucher->per_user_limit ?? '') }}"
                placeholder="Để trống nếu không giới hạn"
            >
            <small class="help-text">Số lần tối đa mà một Customer có thể áp dụng mã ưu đãi này.</small>
            @error('per_user_limit')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- ĐÃ SỬ DỤNG (KHI EDIT) --}}
        @if ($isEdit)
            <div class="form-group admin-form-full">
                <label>Lượt đã áp dụng thành công</label>
                <div class="admin-options-panel" style="background: #F8FAF8; font-size: 13.5px;">
                    <div>Đã có <strong>{{ $voucher->usage_count }}</strong> lượt sử dụng mã này. Chỉ số này được ghi nhận tự động theo đơn hàng thực tế và không thể sửa thủ công.</div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ======================================================================
     SECTION 3: THỜI GIAN HIỆU LỰC
     ====================================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>3. Thời gian hiệu lực</span>
        </h3>
        <p class="admin-form-section-desc">Thời hạn kết thúc chương trình ưu đãi.</p>
    </div>

    <div class="admin-form-grid">
        <div class="form-group admin-form-full">
            <label for="end_at">
                Thời gian hết hạn áp dụng <span class="required-mark">*</span>
            </label>
            <input
                type="datetime-local"
                id="end_at"
                name="end_at"
                value="{{ old('end_at', isset($voucher) ? $voucher->end_at->format('Y-m-d\TH:i') : now()->addMonth()->format('Y-m-d\TH:i')) }}"
                required
            >
            <small class="help-text">
                @if ($isEdit)
                    Voucher bảo lưu mốc bắt đầu ban đầu. Sau thời điểm kết thúc này, khách hàng sẽ không thể nhập mã để giảm giá.
                @else
                    Mã ưu đãi sẽ có hiệu lực ngay sau khi bạn lưu và kết thúc vào thời điểm trên.
                @endif
            </small>
            @error('end_at')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- ACTIONS --}}
<div class="admin-form-actions">
    <a
        href="{{ route('admin.vouchers.index') }}"
        class="btn btn-secondary"
    >
        Hủy
    </a>

    <button
        type="submit"
        class="btn btn-primary"
    >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <polyline points="7 3 7 8 15 8"></polyline>
        </svg>
        <span>{{ $isEdit ? 'Cập nhật Voucher' : 'Lưu Voucher' }}</span>
    </button>
</div>