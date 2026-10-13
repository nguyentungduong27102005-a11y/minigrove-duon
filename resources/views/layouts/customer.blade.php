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
        @yield('title', 'MiniGrove')
    </title>

    {{-- Google Fonts: Cormorant (heading) + Be Vietnam Pro (body) --}}
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
        href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap"
    >

    <link
        rel="stylesheet"
        href="{{
            asset('css/customer.css')
            . '?v='
            . filemtime(
                public_path(
                    'css/customer.css'
                )
            )
        }}"
    >

    @stack('styles')

</head>

<body>

{{-- Skip to content — accessibility --}}
<a
    href="#main-content"
    class="skip-to-content"
>
    Chuyển đến nội dung chính
</a>

<div class="customer-sticky-shell">

{{-- HEADER --}}
<header class="customer-header">

    <div
        class="
            customer-container
            customer-header-inner
        "
    >

        {{-- Mobile Menu Trigger (Hamburger) --}}
        <button
            type="button"
            class="customer-mobile-trigger"
            id="customer-mobile-toggle"
            aria-label="Mở menu điều hướng"
            aria-expanded="false"
            aria-controls="customer-mobile-drawer"
        >
            <span class="customer-hamburger-icon" aria-hidden="true">
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
            </span>
        </button>

        {{-- Brand --}}
        <a
            href="{{ route('home') }}"
            class="customer-brand"
            title="MiniGrove — Nhỏ một khu rừng, lớn một thế giới."
        >

            <span class="customer-brand-icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22V12"></path>
                    <path d="M12 12C12 7.5 8.5 4 3 4c0 6.5 4 8.5 9 8z" fill="currentColor" fill-opacity="0.16"></path>
                    <path d="M12 12c0-4 3.5-7 9-7 0 5.5-3.5 7.5-9 7z" fill="currentColor" fill-opacity="0.09"></path>
                </svg>
            </span>

            <span class="customer-brand-text">
                <span class="customer-brand-name">
                    MiniGrove
                </span>
                <span class="customer-brand-slogan">
                    Nhỏ một khu rừng, lớn một thế giới.
                </span>
            </span>

        </a>

        {{-- MAIN NAVIGATION --}}
        <nav class="customer-main-nav" aria-label="Điều hướng chính">

            <a
                href="{{ route('home') }}"
                class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
            >
                Trang chủ
            </a>

            <a
                href="{{ route('home') }}#categories"
                class="nav-link"
            >
                Danh mục
            </a>

            <a
                href="{{ route('products.index') }}"
                class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
            >
                Sản phẩm
            </a>

            @auth

                @if (
                    auth()->user()->role?->name
                    === 'customer'
                )

                    <a
                        href="{{ route('designs.index') }}"
                        class="nav-link {{ request()->routeIs('designs.*') ? 'active' : '' }}"
                    >
                        Thiết kế của tôi
                    </a>

                    <a
                        href="{{ route('chat.index') }}"
                        class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"
                    >
                        Chat
                        <span class="chat-badge unread-count customer-badge" style="display:none">0</span>
                    </a>

                    <a
                        href="{{ route('profile.vouchers') }}"
                        class="nav-link {{ request()->routeIs('profile.vouchers') ? 'active' : '' }}"
                    >
                        Voucher
                    </a>

                @endif

            @endauth

        </nav>


        {{-- Search --}}
        <form
            class="customer-search customer-search-form"
            action="{{ route('products.index') }}"
            method="GET"
            role="search"
        >
            <div class="search-input-wrapper">
                <span class="search-input-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>

                <input
                    type="text"
                    name="keyword"
                    value="{{ request('keyword') }}"
                    placeholder="Tìm sản phẩm, SKU..."
                    class="customer-search-input"
                    autocomplete="off"
                    aria-label="Tìm kiếm sản phẩm"
                >

                <button
                    type="submit"
                    class="search-button customer-search-btn"
                    aria-label="Tìm kiếm"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </form>


        {{-- Actions --}}
        <div class="header-actions">

            @guest

                <a
                    href="{{ route('login') }}"
                    class="header-action header-action-login"
                >
                    Đăng nhập
                </a>

                <a
                    href="{{ route('register') }}"
                    class="
                        header-action
                        header-action-primary
                    "
                >
                    Đăng ký
                </a>

            @else

                @if (
                    auth()->user()->role?->name
                    === 'customer'
                )

                    @php

                        $headerCartQuantity = 0;

                        $headerCartQuantity =
                            \App\Models\CartItem::query()
                                ->whereHas(
                                    'cart',
                                    function ($query) {

                                        $query->where(
                                            'user_id',
                                            auth()->id()
                                        );
                                    }
                                )
                                ->sum('quantity');

                    @endphp


                    {{-- YÊU THÍCH --}}
                    <a
                        href="{{ Route::has('wishlist.index')
                            ? route('wishlist.index')
                            : '#'
                        }}"
                        class="
                            header-action
                            customer-icon-btn
                            header-wishlist-link
                        "
                        title="Yêu thích"
                        aria-label="Yêu thích"
                    >
                        <span class="header-action-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </span>

                        <span class="header-action-text">
                            Yêu thích
                        </span>

                        <span id="wishlist-count-badge" class="customer-badge header-wishlist-badge" style="display:none">0</span>
                    </a>


                    {{-- GIỎ HÀNG --}}
                    <a
                        href="{{ route('cart.index') }}"
                        class="
                            header-action
                            customer-icon-btn
                            header-cart-link
                        "
                        title="Giỏ hàng"
                        aria-label="Giỏ hàng"
                    >

                        <span class="header-action-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                        </span>

                        <span class="header-action-text">
                            Giỏ hàng
                        </span>


                        @if ($headerCartQuantity > 0)

                            <span
                                id="cart-count-badge"
                                class="header-cart-badge customer-badge"
                            >

                                {{
                                    $headerCartQuantity > 99
                                        ? '99+'
                                        : $headerCartQuantity
                                }}

                            </span>

                        @endif

                    </a>


                    {{-- TÀI KHOẢN --}}
                    @php

                        $headerUser = auth()->user();

                        $headerAvatarUrl = null;

                        if ($headerUser && $headerUser->avatar) {

                            if (
                                str_starts_with(
                                    $headerUser->avatar,
                                    'http://'
                                )
                                ||
                                str_starts_with(
                                    $headerUser->avatar,
                                    'https://'
                                )
                            ) {

                                /*
                                 * Avatar Google.
                                 */
                                $headerAvatarUrl =
                                    $headerUser->avatar;

                            } else {

                                /*
                                 * Avatar upload local.
                                 */
                                $headerAvatarUrl =
                                    asset(
                                        'storage/' .
                                        ltrim(
                                            $headerUser->avatar,
                                            '/'
                                        )
                                    );
                            }
                        }

                    @endphp

                    <details class="header-account customer-details">

                        <summary
                            class="header-account-trigger"
                            aria-label="Menu tài khoản"
                            title="{{ $headerUser?->name ?? 'Tài khoản' }}"
                        >

                            <span class="header-account-avatar">

                                @if ($headerAvatarUrl)

                                    <img
                                        src="{{ $headerAvatarUrl }}"
                                        alt="{{ $headerUser?->name ?? 'Tài khoản' }}"
                                    >

                                @else

                                    {{
                                        strtoupper(
                                            mb_substr(
                                                $headerUser?->name ?? 'U',
                                                0,
                                                1
                                            )
                                        )
                                    }}

                                @endif

                            </span>

                            <span class="header-account-name">
                                {{ $headerUser?->name }}
                            </span>

                            <span class="header-account-arrow" aria-hidden="true">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>

                        </summary>


                        <div class="header-account-menu customer-dropdown customer-user-menu">

                            {{-- User Info Header Snippet --}}
                            <div class="header-account-user-info">
                                <div class="header-account-user-name">
                                    {{ $headerUser?->name }}
                                </div>
                                <div class="header-account-user-email">
                                    {{ $headerUser?->email }}
                                </div>
                            </div>

                            <div class="header-account-divider" role="separator"></div>

                            <a
                                href="{{ route('profile.edit') }}"
                                class="header-account-menu-item"
                            >
                                <span class="header-account-menu-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <span>Tài khoản của tôi</span>
                            </a>

                            <a
                                href="{{ route('orders.index') }}"
                                class="header-account-menu-item"
                            >
                                <span class="header-account-menu-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="21 8 21 21 3 21 3 8"></polyline>
                                        <rect x="1" y="3" width="22" height="5"></rect>
                                        <line x1="10" y1="12" x2="14" y2="12"></line>
                                    </svg>
                                </span>
                                <span>Đơn mua</span>
                            </a>

                            @if (Route::has('profile.vouchers'))
                                <a
                                    href="{{ route('profile.vouchers') }}"
                                    class="header-account-menu-item"
                                >
                                    <span class="header-account-menu-icon" aria-hidden="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                            <line x1="7" y1="8" x2="7.01" y2="8"></line>
                                            <line x1="17" y1="16" x2="17.01" y2="16"></line>
                                            <line x1="7" y1="16" x2="17" y2="8"></line>
                                        </svg>
                                    </span>
                                    <span>Kho Voucher</span>
                                </a>
                            @endif

                            <div class="header-account-divider" role="separator"></div>

                            <form
                                method="POST"
                                action="{{ route('logout') }}"
                                class="header-account-logout"
                            >
                                @csrf
                                <button
                                    type="submit"
                                    class="header-account-menu-item header-account-menu-item-danger"
                                >
                                    <span class="header-account-menu-icon" aria-hidden="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                    </span>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>

                        </div>

                    </details>

                @elseif (
                    auth()->user()->role?->name
                    === 'admin'
                )

                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="
                            header-action
                            header-action-primary
                        "
                    >
                        Khu quản trị
                    </a>

                @endif

            @endguest

        </div>

    </div>

    {{-- MOBILE NAVIGATION DRAWER (< 1024px) --}}
    <div
        id="customer-mobile-drawer"
        class="customer-mobile-drawer"
        aria-hidden="true"
        inert
    >
        <div class="customer-container customer-mobile-drawer-inner">

            <nav class="mobile-drawer-nav" aria-label="Menu di động">

                <div class="mobile-drawer-group-title">
                    Khám phá
                </div>

                <a
                    href="{{ route('home') }}"
                    class="mobile-nav-link customer-nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                >
                    <span class="mobile-nav-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </span>
                    <span>Trang chủ</span>
                </a>

                <a
                    href="{{ route('home') }}#categories"
                    class="mobile-nav-link customer-nav-link"
                >
                    <span class="mobile-nav-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </span>
                    <span>Danh mục</span>
                </a>

                <a
                    href="{{ route('products.index') }}"
                    class="mobile-nav-link customer-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                >
                    <span class="mobile-nav-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                    </span>
                    <span>Sản phẩm</span>
                </a>

                @auth
                    @if (
                        auth()->user()->role?->name
                        === 'customer'
                    )
                        <a
                            href="{{ route('designs.index') }}"
                            class="mobile-nav-link customer-nav-link {{ request()->routeIs('designs.*') ? 'active' : '' }}"
                        >
                            <span class="mobile-nav-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                                    <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                                    <path d="M2 2l7.586 7.586"></path>
                                    <circle cx="11" cy="11" r="2"></circle>
                                </svg>
                            </span>
                            <span>Thiết kế của tôi</span>
                        </a>

                        <a
                            href="{{ route('chat.index') }}"
                            class="mobile-nav-link customer-nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"
                        >
                            <span class="mobile-nav-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </span>
                            <span>Chat</span>
                        </a>

                        <a
                            href="{{ route('profile.vouchers') }}"
                            class="mobile-nav-link customer-nav-link {{ request()->routeIs('profile.vouchers') ? 'active' : '' }}"
                        >
                            <span class="mobile-nav-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                    <line x1="7" y1="8" x2="7.01" y2="8"></line>
                                    <line x1="17" y1="16" x2="17.01" y2="16"></line>
                                    <line x1="7" y1="16" x2="17" y2="8"></line>
                                </svg>
                            </span>
                            <span>Voucher</span>
                        </a>
                    @endif
                @endauth

            </nav>

            <div class="mobile-drawer-auth">
                @guest
                    <div class="mobile-auth-guest">
                        <a
                            href="{{ route('login') }}"
                            class="mobile-auth-btn mobile-auth-btn-outline"
                        >
                            Đăng nhập
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="mobile-auth-btn mobile-auth-btn-primary"
                        >
                            Đăng ký
                        </a>
                    </div>
                @else
                    @if (
                        auth()->user()->role?->name
                        === 'customer'
                    )
                        <div class="mobile-auth-user">
                            <a
                                href="{{ route('profile.edit') }}"
                                class="mobile-user-link"
                            >
                                <span class="mobile-user-link-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <span>Hồ sơ cá nhân</span>
                            </a>

                            <a
                                href="{{ route('orders.index') }}"
                                class="mobile-user-link"
                            >
                                <span class="mobile-user-link-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="21 8 21 21 3 21 3 8"></polyline>
                                        <rect x="1" y="3" width="22" height="5"></rect>
                                        <line x1="10" y1="12" x2="14" y2="12"></line>
                                    </svg>
                                </span>
                                <span>Đơn mua của tôi</span>
                            </a>

                            <form
                                method="POST"
                                action="{{ route('logout') }}"
                                class="mobile-logout-form"
                            >
                                @csrf
                                <button
                                    type="submit"
                                    class="mobile-user-link mobile-logout-btn"
                                >
                                    <span class="mobile-user-link-icon" aria-hidden="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                    </span>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>
                    @elseif (
                        auth()->user()->role?->name
                        === 'admin'
                    )
                        <div class="mobile-auth-guest">
                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="mobile-auth-btn mobile-auth-btn-primary"
                            >
                                Vào Khu quản trị
                            </a>
                        </div>
                    @endif
                @endguest
            </div>

        </div>
    </div>

</header>

</div>


{{-- CONTENT --}}
<main id="main-content">

    {{-- THÔNG BÁO THÀNH CÔNG --}}
    @if (session('success'))

        <div class="customer-container">

            <div
                class="customer-alert customer-alert-success"
                role="alert"
            >
                ✓ {{ session('success') }}
            </div>

        </div>

    @endif


    {{-- THÔNG BÁO LỖI --}}
    @if (session('error'))

        <div class="customer-container">

            <div
                class="customer-alert customer-alert-error"
                role="alert"
            >
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if ($errors->any())

        <div class="customer-container">

            <div
                class="customer-alert customer-alert-error"
                role="alert"
            >

                {{ $errors->first() }}

            </div>

        </div>

    @endif


    @yield('content')

</main>


{{-- FOOTER --}}
<footer class="customer-footer">

    <div
        class="
            customer-container
            customer-footer-inner
        "
    >

        {{-- Cột 1: Brand & Slogan & Mô tả --}}
        <div class="footer-col footer-col-brand">

            <div class="footer-brand">
                <span class="footer-brand-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22V12"></path>
                        <path d="M12 12C12 7.5 8.5 4 3 4c0 6.5 4 8.5 9 8z" fill="currentColor" fill-opacity="0.22"></path>
                        <path d="M12 12c0-4 3.5-7 9-7 0 5.5-3.5 7.5-9 7z" fill="currentColor" fill-opacity="0.12"></path>
                    </svg>
                </span>
                <span class="footer-brand-name">
                    MiniGrove
                </span>
            </div>

            <div class="footer-slogan">
                Nhỏ một khu rừng, lớn một thế giới.
            </div>

            <p class="footer-description">
                Terrarium không chỉ là vật trang trí,
                mà còn là món quà mang thông điệp
                “trao tặng sự sống”. Mỗi bình chứa
                đựng cả một hệ sinh thái, tượng trưng
                cho sự khởi đầu, may mắn và bình an.
            </p>

        </div>


        {{-- Cột 2: Khám phá & Tài khoản --}}
        <div class="footer-col footer-col-links">

            <div class="footer-links-grid">

                {{-- Nhóm Khám phá --}}
                <div class="footer-link-group">

                    <h3 class="footer-title">
                        Khám phá
                    </h3>

                    <a
                        href="{{ route('products.index') }}"
                        class="footer-link"
                    >
                        Sản phẩm
                    </a>

                    <a
                        href="{{ route('about') }}"
                        class="footer-link"
                    >
                        Về chúng tôi
                    </a>

                    @auth

                        @if (
                            auth()->user()->role?->name
                            === 'customer'
                        )

                            <a
                                href="{{ route('chat.index') }}"
                                class="footer-link"
                            >
                                Chat hỗ trợ
                            </a>

                        @endif

                    @endauth

                </div>


                {{-- Nhóm Tài khoản --}}
                <div class="footer-link-group">

                    <h3 class="footer-title">
                        Tài khoản
                    </h3>

                    @guest

                        <a
                            href="{{ route('login') }}"
                            class="footer-link"
                        >
                            Đăng nhập
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="footer-link"
                        >
                            Đăng ký
                        </a>

                    @else

                        <a
                            href="{{ route('profile.edit') }}"
                            class="footer-link"
                        >
                            Hồ sơ cá nhân
                        </a>

                    @endguest

                </div>

            </div>

        </div>


        {{-- Cột 3: Liên hệ --}}
        <div class="footer-col footer-col-contact">

            <h3 class="footer-title">
                Liên hệ
            </h3>

            <div class="footer-contact-item">
                <span class="footer-contact-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </span>

                <span>
                    Số 41A phường Phú Diễn,
                    quận Bắc Từ Liêm, Hà Nội
                </span>
            </div>

            <a
                href="tel:035791347"
                class="footer-link footer-contact-link"
            >
                <span class="footer-contact-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </span>

                <span>035791347</span>
            </a>

            <a
                href="mailto:gioinhatlop@gmail.com"
                class="footer-link footer-contact-link"
            >
                <span class="footer-contact-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </span>

                <span>gioinhatlop@gmail.com</span>
            </a>

        </div>

    </div>


    <div class="customer-footer-bottom">

        <div class="customer-container customer-footer-bottom-inner">
            <span>
                MiniGrove được thực hiện bởi nhóm 13:
                Nguyễn Thị Mến, Nguyễn Minh Hà,
                Nguyễn Tùng Dương.
            </span>
        </div>

    </div>

</footer>


@vite(['resources/js/app.js'])

@stack('scripts')

{{-- Mobile Navigation Toggle Script (Presentation only) --}}
<script>
(function() {
    var toggle = document.getElementById('customer-mobile-toggle');
    var drawer = document.getElementById('customer-mobile-drawer');
    if (!toggle || !drawer) return;

    toggle.addEventListener('click', function() {
        var isOpen = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', !isOpen);
        toggle.setAttribute('aria-label', isOpen ? 'Mở menu điều hướng' : 'Đóng menu điều hướng');
        drawer.classList.toggle('is-open', !isOpen);
        drawer.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
        if (isOpen) {
            drawer.setAttribute('inert', '');
        } else {
            drawer.removeAttribute('inert');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Mở menu điều hướng');
            drawer.classList.remove('is-open');
            drawer.setAttribute('aria-hidden', 'true');
            drawer.setAttribute('inert', '');
            toggle.focus();
        }
    });
})();
</script>

</body>

</html>
