<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'MiniGrove Admin')
    </title>

    {{-- Google Fonts: Cormorant (brand accent) + Be Vietnam Pro (clean sans for UI) --}}
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,500;0,600;0,700;1,500&family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/admin.css') }}"
    >

    @stack('styles')

</head>


<body>

<div class="admin-layout">

    {{-- =========================================================
         MOBILE BACKDROP
    ========================================================== --}}
    <div
        class="admin-drawer-backdrop"
        id="adminDrawerBackdrop"
        aria-hidden="true"
    ></div>


    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}
    <aside
        class="sidebar"
        id="adminSidebar"
        aria-label="Thanh điều hướng quản trị"
    >

        {{-- BRAND --}}
        <div class="sidebar-brand">

            <a
                href="{{ route('admin.dashboard') }}"
                class="sidebar-brand-link"
                title="Bảng điều khiển MiniGrove"
            >

                <span class="sidebar-brand-logo" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 4 13C4 8 11 2 11 2s7 6 7 11a7 7 0 0 1-7 7Z"/>
                        <path d="M11 20V10"/>
                    </svg>
                </span>

                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-name">MiniGrove</span>
                    <span class="sidebar-brand-subtitle">ADMIN PANEL</span>
                </div>

            </a>

            {{-- Close button for mobile drawer --}}
            <button
                type="button"
                class="sidebar-close-btn"
                id="adminSidebarClose"
                aria-label="Đóng menu điều hướng"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

        </div>


        {{-- =====================================================
             MENU CUỘN RIÊNG
        ====================================================== --}}
        <nav class="sidebar-menu" aria-label="Menu chính">

            {{-- NHÓM: TỔNG QUAN --}}
            <div class="sidebar-group">

                <div class="sidebar-group-title">
                    Tổng quan
                </div>

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Dashboard</span>
                </a>

            </div>


            {{-- NHÓM: DANH MỤC & SẢN PHẨM --}}
            <div class="sidebar-group">

                <div class="sidebar-group-title">
                    Danh mục & Sản phẩm
                </div>

                {{-- DANH MỤC --}}
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="7" x="3" y="3" rx="1"/>
                            <rect width="7" height="7" x="14" y="3" rx="1"/>
                            <rect width="7" height="7" x="14" y="14" rx="1"/>
                            <rect width="7" height="7" x="3" y="14" rx="1"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Danh mục</span>
                </a>

                {{-- SẢN PHẨM --}}
                <a
                    href="{{ route('admin.products.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m7.5 4.27 9 5.15"/>
                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                            <path d="m3.3 7 8.7 5 8.7-5"/>
                            <path d="M12 22V12"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Sản phẩm</span>
                </a>

                {{-- QUY TẮC TƯƠNG THÍCH --}}
                <a
                    href="{{ route('admin.compatibility-rules.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.compatibility-rules.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m16 3 4 4-4 4"/>
                            <path d="M20 7H4"/>
                            <path d="m8 21-4-4 4-4"/>
                            <path d="M4 17h16"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Quy tắc tương thích</span>
                </a>

                {{-- TỒN KHO --}}
                <a
                    href="{{ route('admin.stocks.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.stocks.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/>
                            <path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/>
                            <path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Tồn kho</span>
                </a>

            </div>


            {{-- NHÓM: BÁN HÀNG --}}
            <div class="sidebar-group">

                <div class="sidebar-group-title">
                    Bán hàng
                </div>

                {{-- VOUCHER --}}
                <a
                    href="{{ route('admin.vouchers.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                            <path d="M13 5v2"/>
                            <path d="M13 17v2"/>
                            <path d="M13 11v2"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Voucher</span>
                </a>

                {{-- ĐƠN HÀNG --}}
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                            <path d="M3 6h18"/>
                            <path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Đơn hàng</span>
                </a>

                {{-- THANH TOÁN --}}
                <a
                    href="{{ route('admin.payments.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Thanh toán</span>
                </a>

                {{-- TRẢ HÀNG / HOÀN TIỀN --}}
                <a
                    href="{{ route('admin.returns.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Trả hàng / Hoàn tiền</span>
                </a>

            </div>


            {{-- NHÓM: KHÁCH HÀNG --}}
            <div class="sidebar-group">

                <div class="sidebar-group-title">
                    Khách hàng
                </div>

                {{-- KHÁCH HÀNG --}}
                <a
                    href="{{ route('admin.customers.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Khách hàng</span>
                </a>

                {{-- CHAT --}}
                <a
                    href="{{ route('admin.chat.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </span>

                    <span class="sidebar-link-label">Chat</span>

                    <span
                        id="adminChatUnreadBadge"
                        class="sidebar-unread-badge {{ ($adminUnreadChatCount ?? 0) > 0 ? '' : 'is-hidden' }}"
                        aria-label="Tin nhắn chưa đọc"
                    >
                        {{
                            ($adminUnreadChatCount ?? 0) > 99
                                ? '99+'
                                : ($adminUnreadChatCount ?? 0)
                        }}
                    </span>
                </a>

                {{-- ĐÁNH GIÁ SẢN PHẨM --}}
                <a
                    href="{{ route('admin.reviews.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Đánh giá sản phẩm</span>
                </a>

            </div>


            {{-- NHÓM: PHÂN TÍCH --}}
            <div class="sidebar-group">

                <div class="sidebar-group-title">
                    Phân tích
                </div>

                {{-- BÁO CÁO & THỐNG KÊ --}}
                <a
                    href="{{ route('admin.reports.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" x2="12" y1="20" y2="10"/>
                            <line x1="18" x2="18" y1="20" y2="4"/>
                            <line x1="6" x2="6" y1="20" y2="16"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Báo cáo & thống kê</span>
                </a>

            </div>

        </nav>


        {{-- =====================================================
             PHẦN TIỆN ÍCH DƯỚI CÙNG
        ====================================================== --}}
        <div class="sidebar-bottom">

            {{-- VỀ TRANG CHỦ --}}
            <a
                href="{{ route('home') }}"
                class="sidebar-link sidebar-home-link"
                target="_blank"
                rel="noopener noreferrer"
            >
                <span class="sidebar-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" x2="21" y1="14" y2="3"/>
                    </svg>
                </span>
                <span class="sidebar-link-label">Về trang chủ</span>
            </a>

            {{-- TÀI KHOẢN ADMIN --}}
            <div class="sidebar-user">
                <div class="sidebar-user-avatar" aria-hidden="true">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name" title="{{ auth()->user()->name }}">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="sidebar-user-role">
                        Quản trị viên
                    </div>
                </div>
            </div>

            {{-- ĐĂNG XUẤT --}}
            <form
                method="POST"
                action="{{ route('logout') }}"
                class="sidebar-logout-form"
            >
                @csrf

                <button
                    type="submit"
                    class="sidebar-link sidebar-logout"
                    title="Đăng xuất khỏi hệ thống"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" x2="9" y1="12" y2="12"/>
                        </svg>
                    </span>
                    <span class="sidebar-link-label">Đăng xuất</span>
                </button>
            </form>

        </div>

    </aside>


    {{-- =========================================================
         MAIN WORKSPACE
    ========================================================== --}}
    <div class="admin-main">

        {{-- TOP BAR --}}
        <header class="admin-topbar">

            <div class="admin-topbar-left">

                {{-- Hamburger button on mobile / tablet --}}
                <button
                    type="button"
                    class="admin-hamburger"
                    id="adminSidebarToggle"
                    aria-label="Mở menu quản trị"
                    aria-controls="adminSidebar"
                    aria-expanded="false"
                >
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="4" x2="20" y1="12" y2="12"/>
                        <line x1="4" x2="20" y1="6" y2="6"/>
                        <line x1="4" x2="20" y1="18" y2="18"/>
                    </svg>
                </button>

                <div class="admin-topbar-context">
                    <span class="admin-topbar-badge">Quản trị</span>
                    <div class="admin-topbar-title">
                        @yield('page-title', 'Bảng điều khiển')
                    </div>
                </div>

            </div>

            <div class="admin-topbar-right">

                <div class="admin-topbar-user" title="{{ auth()->user()->name }}">
                    <span class="admin-topbar-user-avatar" aria-hidden="true">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </span>
                    <span class="admin-topbar-user-name">
                        {{ auth()->user()->name }}
                    </span>
                </div>

            </div>

        </header>


        {{-- =====================================================
             CONTENT SHELL
        ====================================================== --}}
        <main class="container admin-content-shell">

            {{-- =================================================
                 THÔNG BÁO THÀNH CÔNG
            ================================================== --}}
            @if (session('success'))
                <div class="alert alert-success" role="status">
                    <div class="alert-icon" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="alert-content">
                        {{ session('success') }}
                    </div>
                </div>
            @endif


            {{-- =================================================
                 THÔNG BÁO LỖI NGHIỆP VỤ
            ================================================== --}}
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    <div class="alert-icon" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div class="alert-content">
                        {{ session('error') }}
                    </div>
                </div>
            @endif


            {{-- =================================================
                 VALIDATION ERROR
            ================================================== --}}
          @if ($errors->any() && !request()->routeIs('admin.products.create', 'admin.products.edit'))
                <div class="alert alert-danger" role="alert">
                    <div class="alert-icon" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <strong>Không thể thực hiện thao tác:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif


            {{-- =================================================
                 NỘI DUNG TRANG
            ================================================== --}}
            @yield('content')

        </main>

    </div>

</div>


@vite(['resources/js/app.js'])

{{-- Chat unread count handler --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const badge = document.getElementById('adminChatUnreadBadge');
    if (!badge) {
        return;
    }

    const unreadUrl = @json(route('admin.chat.unread-count'));

    const renderUnread = function (count) {
        const safeCount = Math.max(0, Number(count) || 0);
        badge.textContent = safeCount > 99 ? '99+' : String(safeCount);
        badge.classList.toggle('is-hidden', safeCount === 0);
    };

    const refreshUnread = async function () {
        try {
            const response = await fetch(unreadUrl, {
                headers: {
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
                cache: 'no-store',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            renderUnread(data.unread_count);
        } catch (error) {
            // Giữ số hiện tại nếu mạng tạm thời lỗi.
        }
    };

    refreshUnread();

    if (window.Echo) {
        window.Echo
            .private('chat.admin')
            .listen('.chat.message.sent', refreshUnread)
            .listen('.chat.conversation.updated', refreshUnread);
    }
});
</script>

{{-- Mobile Sidebar Drawer Handler --}}
<script>
(function () {
    const sidebar = document.getElementById('adminSidebar');
    const toggleBtn = document.getElementById('adminSidebarToggle');
    const closeBtn = document.getElementById('adminSidebarClose');
    const backdrop = document.getElementById('adminDrawerBackdrop');

    if (!sidebar || !toggleBtn || !backdrop) {
        return;
    }

    function openDrawer() {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-open');
        toggleBtn.setAttribute('aria-expanded', 'true');
        sidebar.setAttribute('aria-hidden', 'false');
        sidebar.removeAttribute('inert');
        document.body.classList.add('admin-drawer-active');
        if (closeBtn) {
            closeBtn.focus();
        }
    }

    function closeDrawer() {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
        if (window.innerWidth < 1024) {
            sidebar.setAttribute('aria-hidden', 'true');
            sidebar.setAttribute('inert', '');
        } else {
            sidebar.removeAttribute('aria-hidden');
            sidebar.removeAttribute('inert');
        }
        document.body.classList.remove('admin-drawer-active');
        toggleBtn.focus();
    }

    toggleBtn.addEventListener('click', function () {
        const isOpen = sidebar.classList.contains('is-open');
        if (isOpen) {
            closeDrawer();
        } else {
            openDrawer();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeDrawer);
    }

    backdrop.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
            closeDrawer();
        }
    });

    function handleResize() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('is-open');
            backdrop.classList.remove('is-open');
            toggleBtn.setAttribute('aria-expanded', 'false');
            sidebar.removeAttribute('aria-hidden');
            sidebar.removeAttribute('inert');
            document.body.classList.remove('admin-drawer-active');
        } else {
            if (!sidebar.classList.contains('is-open')) {
                sidebar.setAttribute('aria-hidden', 'true');
                sidebar.setAttribute('inert', '');
            } else {
                sidebar.removeAttribute('inert');
            }
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize();
})();
</script>

@stack('scripts')

</body>

</html>