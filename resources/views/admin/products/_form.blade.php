@php
    $editingProduct = $product ?? null;
    $currentImageCount = $editingProduct?->images?->count() ?? 0;
    $remainingImageSlots = max(0, 8 - $currentImageCount);
    $hasPrimaryImage = $editingProduct?->images?->contains('is_primary', true) ?? false;
@endphp

{{-- ======================================================================
     SECTION 1: THÔNG TIN CƠ BẢN
     ====================================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 7h-7L10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
            <span>1. Thông tin cơ bản</span>
        </h3>
        <p class="admin-form-section-desc">Tên gọi, định danh SKU và phân loại sản phẩm trong danh mục.</p>
    </div>

    <div class="admin-form-grid">
        {{-- Tên sản phẩm --}}
        <div class="form-group">
            <label for="name">
                Tên sản phẩm <span class="required-mark">*</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $editingProduct?->name) }}"
                placeholder="Ví dụ: Forest Calm Terrarium"
                required
            >
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- SKU --}}
        <div class="form-group">
            <label for="sku">
                Mã SKU <span class="required-mark">*</span>
            </label>
            <input
                type="text"
                id="sku"
                name="sku"
                class="input-mono"
                value="{{ old('sku', $editingProduct?->sku) }}"
                placeholder="Ví dụ: MG-FOREST-001"
                required
            >
            @error('sku')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Danh mục --}}
        <div class="form-group">
            <label for="category_id">
                Danh mục phân loại <span class="required-mark">*</span>
            </label>
            <select
                id="category_id"
                name="category_id"
                required
            >
                <option value="">-- Chọn danh mục --</option>
                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        @selected(old('category_id', $editingProduct?->category_id) == $category->id)
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nhóm kích thước --}}
        <div class="form-group">
            <label for="size">
                Nhóm kích thước (Size)
            </label>
            <select
                id="size"
                name="size"
            >
                <option value="">-- Không xác định --</option>
                <option value="mini" @selected(old('size', $editingProduct?->size) === 'mini')>Mini (Nhỏ gọn bàn làm việc)</option>
                <option value="medium" @selected(old('size', $editingProduct?->size) === 'medium')>Medium (Trung bình tiêu chuẩn)</option>
                <option value="large" @selected(old('size', $editingProduct?->size) === 'large')>Large (Kích thước lớn trưng bày)</option>
            </select>
            @error('size')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- ======================================================================
     SECTION 2: GIÁ & TỒN KHO
     ====================================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span>2. Giá bán & Tồn kho</span>
        </h3>
        <p class="admin-form-section-desc">Định giá sản phẩm, mức khuyến mãi và số lượng sẵn sàng bán.</p>
    </div>

    <div class="admin-form-grid">
        {{-- Giá niêm yết --}}
        <div class="form-group">
            <label for="list_price">
                Giá niêm yết <span class="required-mark">*</span>
            </label>
            <div class="admin-input-group">
                <input
                    type="number"
                    id="list_price"
                    name="list_price"
                    min="1000"
                    step="1000"
                    value="{{ old('list_price', $editingProduct?->list_price) }}"
                    placeholder="650000"
                    required
                >
                <span class="admin-input-suffix">VNĐ</span>
            </div>
            @error('list_price')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Giá khuyến mãi --}}
        <div class="form-group">
            <label for="sale_price">
                Giá khuyến mãi (Ưu đãi)
            </label>
            <div class="admin-input-group">
                <input
                    type="number"
                    id="sale_price"
                    name="sale_price"
                    min="0"
                    step="1000"
                    value="{{ old('sale_price', $editingProduct?->sale_price) }}"
                    placeholder="Để trống nếu bán giá gốc"
                >
                <span class="admin-input-suffix">VNĐ</span>
            </div>
            <small class="help-text">Để trống nếu sản phẩm không có chương trình giảm giá.</small>
            @error('sale_price')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tồn kho --}}
        <div class="form-group">
            <label for="stock_quantity">
                Số lượng tồn kho <span class="required-mark">*</span>
            </label>
            <input
                type="number"
                id="stock_quantity"
                name="stock_quantity"
                min="0"
                value="{{ old('stock_quantity', $editingProduct?->stock_quantity ?? 0) }}"
                required
            >
            <small class="help-text">Số lượng sản phẩm thực tế đang lưu trữ tại kho MiniGrove.</small>
            @error('stock_quantity')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- ======================================================================
     SECTION 3: ĐẶC TÍNH TERRARIUM & CHĂM SÓC
     ====================================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2a10 10 0 0 0-7.07 17.07l.07.07c3.9 3.9 10.1 3.9 14 0l.07-.07A10 10 0 0 0 12 2z"/></svg>
            <span>3. Đặc tính sinh thái & Chăm sóc</span>
        </h3>
        <p class="admin-form-section-desc">Thông số sinh thái giúp khách hàng hiểu và duy trì sức sống cho terrarium.</p>
    </div>

    <div class="admin-form-grid">
        {{-- Loại hệ sinh thái --}}
        <div class="form-group">
            <label for="ecosystem_type">
                Loại hệ sinh thái <span class="required-mark">*</span>
            </label>
            <select
                id="ecosystem_type"
                name="ecosystem_type"
                required
            >
                <option value="">-- Chọn loại hệ sinh thái --</option>
                <option value="closed" @selected(old('ecosystem_type', $editingProduct?->ecosystem_type) === 'closed')>Hệ kín (Độ ẩm cao, nắp đậy tuần hoàn)</option>
                <option value="open" @selected(old('ecosystem_type', $editingProduct?->ecosystem_type) === 'open')>Hệ mở (Thông thoáng, cần phun sương định kỳ)</option>
                <option value="dry" @selected(old('ecosystem_type', $editingProduct?->ecosystem_type) === 'dry')>Hệ khô (Xương rồng, sen đá, rêu sa mạc)</option>
            </select>
            @error('ecosystem_type')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Mức chăm sóc --}}
        <div class="form-group">
            <label for="care_level">
                Mức độ chăm sóc <span class="required-mark">*</span>
            </label>
            <select
                id="care_level"
                name="care_level"
                required
            >
                <option value="">-- Chọn mức độ --</option>
                <option value="easy" @selected(old('care_level', $editingProduct?->care_level) === 'easy')>Dễ (Phù hợp người mới bắt đầu)</option>
                <option value="medium" @selected(old('care_level', $editingProduct?->care_level) === 'medium')>Trung bình (Cần tưới nước định kỳ)</option>
                <option value="high" @selected(old('care_level', $editingProduct?->care_level) === 'high')>Cao (Cần ánh sáng & kiểm soát ẩm cẩn thận)</option>
            </select>
            @error('care_level')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Ánh sáng --}}
        <div class="form-group">
            <label for="light_requirement">
                Nhu cầu ánh sáng
            </label>
            <input
                type="text"
                id="light_requirement"
                name="light_requirement"
                value="{{ old('light_requirement', $editingProduct?->light_requirement) }}"
                placeholder="Ví dụ: Ánh sáng tán xạ gián tiếp"
            >
            @error('light_requirement')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Chu kỳ chăm sóc --}}
        <div class="form-group">
            <label for="care_cycle_days">
                Chu kỳ cấp ẩm (ngày)
            </label>
            <input
                type="number"
                id="care_cycle_days"
                name="care_cycle_days"
                min="1"
                value="{{ old('care_cycle_days', $editingProduct?->care_cycle_days) }}"
                placeholder="Ví dụ: 7"
            >
            <small class="help-text">Khoảng thời gian trung bình giữa 2 lần cần can thiệp xịt sương/tưới nước.</small>
            @error('care_cycle_days')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- ======================================================================
     SECTION 4: HÌNH ẢNH SẢN PHẨM MỚI
     ====================================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
            <span>4. {{ $editingProduct ? 'Tải thêm hình ảnh sản phẩm' : 'Hình ảnh sản phẩm' }}</span>
        </h3>
        <p class="admin-form-section-desc">Hình ảnh rõ nét, chụp nhiều góc độ giúp gia tăng tỷ lệ chuyển đổi.</p>
    </div>

    <div class="form-group admin-form-full">
        <label for="images">
            Chọn tệp ảnh tải lên
        </label>
        <input
            type="file"
            id="images"
            name="images[]"
            multiple
            accept=".jpg,.jpeg,.png,.webp"
            data-max-files="{{ $editingProduct ? $remainingImageSlots : 8 }}"
            data-auto-primary="{{ (!$editingProduct || !$hasPrimaryImage) ? '1' : '0' }}"
            @disabled($editingProduct && $remainingImageSlots === 0)
        >

        <small class="help-text">
            @if ($editingProduct)
                Hiện sản phẩm đã có <strong>{{ $currentImageCount }}/8</strong> ảnh.
                @if ($remainingImageSlots > 0)
                    Bạn có thể chọn thêm tối đa <strong>{{ $remainingImageSlots }}</strong> ảnh nữa ở lần này.
                @else
                    Đã đạt tối đa 8 ảnh. Hãy xóa bớt ảnh cũ ở danh sách bên dưới nếu muốn tải thêm.
                @endif
            @else
                Tối đa 8 ảnh, mỗi ảnh tối đa 4MB. Hỗ trợ định dạng: JPG, PNG, WEBP.
            @endif
        </small>

        {{-- Preview danh sách ảnh mới chọn --}}
        <div id="new-image-preview" class="new-image-preview"></div>

        @error('images')
            <div class="error">{{ $message }}</div>
        @enderror
        @error('images.*')
            <div class="error">{{ $message }}</div>
        @enderror
        @error('primary_image_index')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- ======================================================================
     SECTION 5: NỘI DUNG CHI TIẾT
     ====================================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <span>5. Mô tả & Hướng dẫn</span>
        </h3>
        <p class="admin-form-section-desc">Mô tả chi tiết tác phẩm, câu chuyện thiết kế concept và cẩm nang chăm sóc.</p>
    </div>

    <div class="admin-form-grid">
        {{-- Mô tả --}}
        <div class="form-group admin-form-full">
            <label for="description">
                Mô tả sản phẩm
            </label>
            <textarea
                id="description"
                name="description"
                rows="3"
                placeholder="Mô tả các thành phần rêu, đá cảnh, loại bình thủy tinh và cảm giác thư giãn..."
            >{{ old('description', $editingProduct?->description) }}</textarea>
            @error('description')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Câu chuyện Concept --}}
        <div class="form-group admin-form-full">
            <label for="concept_story">
                Câu chuyện Concept (Cảm hứng thiết kế)
            </label>
            <textarea
                id="concept_story"
                name="concept_story"
                rows="3"
                placeholder="Ý tưởng hoặc nguồn cảm hứng tự nhiên đằng sau bố cục terrarium này..."
            >{{ old('concept_story', $editingProduct?->concept_story) }}</textarea>
            @error('concept_story')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Hướng dẫn chăm sóc --}}
        <div class="form-group admin-form-full">
            <label for="care_instructions">
                Hướng dẫn chăm sóc chi tiết
            </label>
            <textarea
                id="care_instructions"
                name="care_instructions"
                rows="3"
                placeholder="Quy trình vệ sinh mặt kính, nhiệt độ phòng lý tưởng, cách cắt tỉa chồi rêu..."
            >{{ old('care_instructions', $editingProduct?->care_instructions) }}</textarea>
            @error('care_instructions')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- ======================================================================
     SECTION 6: THIẾT LẬP HIỂN THỊ
     ====================================================================== --}}
<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h3 class="admin-form-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span>6. Thiết lập kinh doanh & Hiển thị</span>
        </h3>
        <p class="admin-form-section-desc">Cấu hình trạng thái công khai, gắn cờ nổi bật và khả năng sử dụng trong Studio.</p>
    </div>

    <div class="admin-options-panel">
        <div class="checkbox-row">
            <input
                type="checkbox"
                id="is_active"
                name="is_active"
                value="1"
                @checked(old('is_active', $editingProduct ? $editingProduct->is_active : true))
            >
            <label for="is_active">
                <strong>Đang kinh doanh (Active)</strong> — Cho phép hiển thị trên website và khách hàng có thể đặt mua.
            </label>
        </div>

        <div class="checkbox-row">
            <input
                type="checkbox"
                id="is_featured"
                name="is_featured"
                value="1"
                @checked(old('is_featured', $editingProduct?->is_featured ?? false))
            >
            <label for="is_featured">
                <strong>Sản phẩm nổi bật (Featured)</strong> — Ưu tiên xuất hiện tại section nổi bật của trang chủ.
            </label>
        </div>

        <div class="checkbox-row">
            <input
                type="checkbox"
                id="is_grove_eligible"
                name="is_grove_eligible"
                value="1"
                @checked(old('is_grove_eligible', $editingProduct ? $editingProduct->is_grove_eligible : true))
            >
            <label for="is_grove_eligible">
                <strong>Cho phép sử dụng trong Studio</strong> — Sản phẩm có thể được chọn làm thành phần trong trải nghiệm thiết kế Studio.
            </label>
        </div>
    </div>
</div>

{{-- CLIENT SCRIPT CHO UPLOAD & PREVIEW ẢNH MỚI --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('images');
    const preview = document.getElementById('new-image-preview');

    if (!input || !preview) return;

    input.addEventListener('change', function () {
        preview.innerHTML = '';
        const files = Array.from(input.files || []);
        const maxFiles = Number(input.dataset.maxFiles || 8);

        if (files.length > maxFiles) {
            window.alert('Bạn chỉ có thể chọn tối đa ' + maxFiles + ' ảnh ở lần này.');
            input.value = '';
            return;
        }

        files.forEach(function (file, index) {
            const card = document.createElement('div');
            card.className = 'new-image-card';

            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = 'Ảnh mới ' + (index + 1);
            image.onload = function () {
                URL.revokeObjectURL(image.src);
            };

            const name = document.createElement('div');
            name.className = 'new-image-name';
            name.textContent = file.name;

            const primaryLabel = document.createElement('label');
            primaryLabel.className = 'new-image-primary';

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'primary_image_index';
            radio.value = String(index);

            if (index === 0 && input.dataset.autoPrimary === '1') {
                radio.checked = true;
            }

            const labelText = document.createElement('span');
            labelText.textContent = 'Đặt làm ảnh chính';

            primaryLabel.append(radio, labelText);
            card.append(image, name, primaryLabel);
            preview.appendChild(card);
        });
    });
});
</script>
