{{--
    Admin Compact Pagination Partial (Hotfix A3.1)
    Only displays:
    - Result count summary (left)
    - Previous and Next buttons (right)
    Preserves all query / filter parameters.
--}}
@if (isset($paginator) && $paginator->total() > 0)
    @php
        $paginator->withQueryString();
    @endphp
    <div class="admin-pagination">
        <div class="admin-pagination-info">
            Hiển thị <strong>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong> trong <strong>{{ $paginator->total() }}</strong> kết quả
        </div>

        @if ($paginator->hasPages())
            <nav class="admin-pagination-controls" aria-label="Phân trang danh sách">
                {{-- Nút Trang trước --}}
                @if ($paginator->onFirstPage())
                    <span
                        class="admin-pagination-btn is-disabled"
                        aria-disabled="true"
                        aria-label="Trang trước"
                        title="Trang trước"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </span>
                @else
                    <a
                        href="{{ $paginator->previousPageUrl() }}"
                        class="admin-pagination-btn"
                        aria-label="Trang trước"
                        title="Trang trước"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                @endif

                {{-- Nút Trang sau --}}
                @if ($paginator->hasMorePages())
                    <a
                        href="{{ $paginator->nextPageUrl() }}"
                        class="admin-pagination-btn"
                        aria-label="Trang sau"
                        title="Trang sau"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                @else
                    <span
                        class="admin-pagination-btn is-disabled"
                        aria-disabled="true"
                        aria-label="Trang sau"
                        title="Trang sau"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </span>
                @endif
            </nav>
        @endif
    </div>
@endif
