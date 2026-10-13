@extends('layouts.admin')

@section('title', 'Quản lý đánh giá - MiniGrove Admin')

@section('page-title', 'Đánh giá sản phẩm')

@section('content')
<div class="admin-list-page">

    {{-- PAGE HEADER --}}
    <div class="admin-index-header">
        <div class="admin-index-heading">
            <h1 class="admin-index-title">Quản lý đánh giá sản phẩm</h1>
            <p class="admin-index-subtitle">Theo dõi đánh giá của khách hàng và gửi phản hồi chính thức cho sản phẩm.</p>
        </div>
    </div>

    {{-- PRIVACY NOTICE --}}
    <div class="admin-privacy-note">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>Chỉ hiển thị định danh <strong>Customer #ID</strong> để bảo vệ thông tin riêng tư của khách hàng theo chính sách bảo mật MiniGrove.</span>
    </div>

    {{-- TOOLBAR / FILTER --}}
    <div class="admin-toolbar">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="admin-toolbar-form">
            <div class="admin-toolbar-search">
                <span class="admin-toolbar-search-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Tìm nội dung hoặc sản phẩm..."
                    aria-label="Tìm kiếm đánh giá"
                >
            </div>

            <div class="admin-toolbar-select">
                <select name="rating" aria-label="Lọc theo rating">
                    <option value="">Tất cả rating</option>
                    @for ($rating = 5; $rating >= 1; $rating--)
                        <option
                            value="{{ $rating }}"
                            @selected((string) request('rating') === (string) $rating)
                        >
                            {{ $rating }} sao
                        </option>
                    @endfor
                </select>
            </div>

            <div class="admin-toolbar-actions">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Lọc
                </button>
                @if (request()->hasAny(['search', 'rating']))
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline" title="Xóa bộ lọc">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- FEED DANH SÁCH REVIEW --}}
    @if ($reviews->count())
        <div class="admin-reviews-feed">
            @foreach ($reviews as $review)
                <article class="admin-review-card">
                    <div class="admin-review-card-header">
                        <div>
                            <div class="admin-review-customer-badge">
                                <div class="admin-privacy-avatar">
                                    #{{ $review->user_id }}
                                </div>
                                <div>
                                    <div class="admin-text-strong" style="font-size: 14.5px;">Customer #{{ $review->user_id }}</div>
                                    <div class="admin-review-product-meta">
                                        Sản phẩm: <strong>{{ $review->product?->name ?? 'Không xác định' }}</strong>
                                        @if ($review->created_at)
                                            <span style="margin: 0 4px; color: var(--admin-text-muted);">•</span>
                                            <span>{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RATING --}}
                    <div class="admin-review-stars" aria-label="Rating {{ $review->rating }} trên 5">
                        <span style="letter-spacing: 2px;">
                            @for ($star = 1; $star <= 5; $star++)
                                {{ $star <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </span>
                        <span style="font-size: 13px; font-weight: 700; color: #B45309; margin-left: 6px;">
                            {{ $review->rating }}/5
                        </span>
                    </div>

                    {{-- COMMENT --}}
                    @if ($review->comment)
                        <p class="admin-review-comment">
                            {{ $review->comment }}
                        </p>
                    @else
                        <p class="admin-review-comment" style="color: var(--admin-text-muted); font-style: italic;">
                            Customer không nhập nội dung đánh giá.
                        </p>
                    @endif

                    {{-- ẢNH REVIEW --}}
                    @if ($review->images->isNotEmpty())
                        <div class="admin-review-images">
                            @foreach ($review->images as $image)
                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank" rel="noopener" title="Xem ảnh gốc">
                                    <img
                                        src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="Ảnh đánh giá của Customer #{{ $review->user_id }}"
                                        loading="lazy"
                                    >
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- PHẢN HỒI HIỆN CÓ --}}
                    @if ($review->replies->isNotEmpty())
                        <div class="admin-review-replies" style="margin: 14px 0 6px;">
                            <div style="font-size: 13px; font-weight: 600; color: var(--admin-forest-800); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <span>Phản hồi từ quản trị viên ({{ $review->replies->count() }}):</span>
                            </div>
                            @foreach ($review->replies as $reply)
                                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; margin-bottom: 8px; font-size: 13.5px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                        <strong style="color: #166534; font-size: 12.5px;">MiniGrove Store</strong>
                                        @if ($reply->created_at)
                                            <span style="font-size: 12px; color: var(--admin-text-muted);">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                    <div style="color: #1f2937; line-height: 1.5; white-space: pre-line;">{{ $reply->content }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- ADMIN PHẢN HỒI REVIEW FORM --}}
                    <div class="admin-review-reply-section">
                        <form
                            method="POST"
                            action="{{ route('admin.reviews.reply', $review) }}"
                            class="admin-review-reply-form"
                        >
                            @csrf
                            <textarea
                                name="content"
                                rows="2"
                                maxlength="2000"
                                placeholder="Nhập phản hồi chính thức cho đánh giá này..."
                                required
                                class="form-input"
                            ></textarea>
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 5px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    Gửi phản hồi
                                </button>
                            </div>
                        </form>
                    </div>

                </article>
            @endforeach
        </div>

        {{-- PHÂN TRANG --}}
        @include('admin.partials.pagination', ['paginator' => $reviews])
    @else
        <div class="empty-state" style="padding: 48px 24px; text-align: center; background: #fff; border: 1px dashed var(--admin-border); border-radius: var(--admin-radius-lg);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 12px; color: var(--admin-text-muted); display: block;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <h3 style="font-size: 15px; font-weight: 600; color: var(--admin-text-primary); margin: 0 0 4px;">Chưa có đánh giá nào phù hợp</h3>
            <p style="font-size: 13px; color: var(--admin-text-secondary); margin: 0;">Hãy thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm.</p>
        </div>
    @endif

</div>
@endsection
