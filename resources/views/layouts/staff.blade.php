    <!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'MiniGrove Staff')
    </title>


    <link
        rel="stylesheet"
        href="{{ asset('css/staff-panel.css') }}"
    >


    @stack('styles')

</head>


<body>


<div class="staff-layout">


    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}
    <aside class="staff-sidebar">


        {{-- BRAND --}}
        <div class="staff-brand">

            <div class="staff-brand-name">
                MiniGrove
            </div>

            <div class="staff-brand-role">
                STAFF PANEL
            </div>

        </div>



        {{-- =====================================================
             MENU
        ====================================================== --}}
        <nav class="staff-nav">


            {{-- DASHBOARD --}}
            <a
                href="{{ route('staff.dashboard') }}"
                class="staff-nav-link
                {{
                    request()->routeIs('staff.dashboard')
                        ? 'active'
                        : ''
                }}"
            >

                <span class="staff-nav-icon">
                    ⌂
                </span>

                Bảng điều khiển

            </a>



            {{-- ĐƠN HÀNG --}}
<a
    href="{{ route('staff.orders.index') }}"
    class="staff-nav-link
    {{
        request()->routeIs('staff.orders.*')
            ? 'active'
            : ''
    }}"
>

    <span class="staff-nav-icon">
        ▧
    </span>

    Đơn hàng

</a>



            {{-- TRẢ HÀNG --}}
            <span class="staff-nav-link disabled">

                <span class="staff-nav-icon">
                    ↩
                </span>

                Trả hàng / Hoàn tiền

            </span>



            {{-- CHAT --}}
            <a
                href="{{ route('staff.chat.index') }}"
                class="staff-nav-link
                    {{ request()->routeIs('staff.chat.*') ? 'active' : '' }}"
            >

                <span class="staff-nav-icon">
                    ◌
                </span>

                Chat

            </a>



            {{-- REVIEW --}}
            <span class="staff-nav-link disabled">

                <span class="staff-nav-icon">
                    ☆
                </span>

                Đánh giá sản phẩm

            </span>


        </nav>



        {{-- =====================================================
             SIDEBAR BOTTOM
        ====================================================== --}}
        <div class="staff-sidebar-bottom">


            <a
                href="{{ route('home') }}"
                class="staff-nav-link"
            >

                <span class="staff-nav-icon">
                    🌿
                </span>

                Về trang chủ

            </a>



            <div class="staff-user">


                <div class="staff-user-name">

                    {{
                        auth()->user()->name
                    }}

                </div>


                <div class="staff-user-role">
                    Tài khoản Staff
                </div>


            </div>



            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf


                <button
                    type="submit"
                    class="
                        staff-nav-link
                        staff-logout
                    "
                >

                    <span class="staff-nav-icon">
                        ↪
                    </span>

                    Đăng xuất

                </button>


            </form>


        </div>


    </aside>



    {{-- =========================================================
         MAIN
    ========================================================== --}}
    <main class="staff-main">


        {{-- TOPBAR --}}
        <header class="staff-topbar">


            <div class="staff-topbar-title">
                Nhân viên MiniGrove
            </div>


            <div class="staff-topbar-slogan">
                Nhỏ một khu rừng, lớn một thế giới.
            </div>


        </header>



        {{-- CONTENT --}}
        <section class="staff-content">


            {{-- SUCCESS --}}
            @if (session('success'))

                <div class="staff-alert staff-alert-success">

                    {{
                        session('success')
                    }}

                </div>

            @endif



            {{-- ERROR --}}
            @if (session('error'))

                <div class="staff-alert staff-alert-error">

                    {{
                        session('error')
                    }}

                </div>

            @endif



            @yield('content')


        </section>


    </main>


</div>


</body>

</html>