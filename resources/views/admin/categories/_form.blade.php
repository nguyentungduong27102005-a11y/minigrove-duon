<div class="admin-form-grid">

    {{-- TÊN DANH MỤC --}}
    <div class="form-group">
        <label for="name">
            Tên danh mục <span class="required-mark">*</span>
        </label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            placeholder="Ví dụ: Rừng nhiệt đới mini"
            required
        >
        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    {{-- THỨ TỰ HIỂN THỊ --}}
    <div class="form-group">
        <label for="display_order">
            Thứ tự hiển thị
        </label>
        <input
            type="number"
            id="display_order"
            name="display_order"
            min="0"
            value="{{ old('display_order', $category->display_order ?? 0) }}"
            placeholder="0"
        >
        @error('display_order')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    {{-- MÔ TẢ --}}
    <div class="form-group admin-form-full">
        <label for="description">
            Mô tả danh mục
        </label>
        <textarea
            id="description"
            name="description"
            rows="3"
            placeholder="Mô tả ngắn gọn về đặc điểm và ứng dụng của danh mục này..."
        >{{ old('description', $category->description ?? '') }}</textarea>
        @error('description')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    {{-- ẢNH DANH MỤC --}}
    <div class="form-group admin-form-full">
        <label for="image">
            Ảnh đại diện danh mục
        </label>
        <input
            type="file"
            id="image"
            name="image"
            accept=".jpg,.jpeg,.png,.webp"
        >
        <small class="help-text">
            Dung lượng ảnh không được vượt quá 5MB. Định dạng hỗ trợ: JPG, PNG, WEBP.
        </small>
        @error('image')
            <div class="error">{{ $message }}</div>
        @enderror

        {{-- PREVIEW ẢNH HIỆN TẠI NẾU CÓ --}}
        @if (!empty($category?->image))
            <div class="admin-image-preview-box">
                <img
                    src="{{ asset('storage/' . $category->image) }}"
                    alt="{{ $category->name }}"
                    class="admin-image-preview-thumb"
                >
                <div class="admin-image-preview-meta">
                    <div class="admin-image-preview-label">Ảnh đại diện hiện tại</div>
                    <div>Tải lên ảnh mới phía trên nếu bạn muốn thay thế ảnh này.</div>
                </div>
            </div>
        @endif
    </div>

    {{-- TRẠNG THÁI ACTIVE --}}
    <div class="form-group admin-form-full">
        <div class="admin-options-panel">
            <div class="checkbox-row">
                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    @checked(
                        old(
                            'is_active',
                            isset($category)
                                ? $category->is_active
                                : true
                        )
                    )
                >
                <label for="is_active">
                    Kích hoạt hiển thị (Active) — Danh mục sẽ xuất hiện trên trang storefront và danh sách phân loại.
                </label>
            </div>
        </div>
    </div>

</div>