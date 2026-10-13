@extends('layouts.customer')


@section(
    'title',
    'MiniGrove - Nhỏ một khu rừng, lớn một thế giới'
)


@section('content')


{{-- HERO --}}
<section class="hero">

    <div class="customer-container">

        <div class="hero-box hero-banner-box">

            {{-- Multi-Banner Carousel Visual --}}
            <div class="hero-visual hero-banner-visual hero-carousel" role="region" aria-roledescription="carousel" aria-label="Bộ sưu tập Banner MiniGrove">
                <div class="hero-carousel-track" id="hero-carousel-track">
                    {{-- Clone Slide 4 (for seamless infinite loop backward) --}}
                    <div class="hero-slide hero-slide-clone hero-slide-4" data-track-index="0" aria-hidden="true" tabindex="-1">
                        <img
                            src="{{ asset('images/banner/banner4.png') }}"
                            alt=""
                            class="hero-banner-img hero-slide-img"
                            width="2148"
                            height="732"
                            loading="lazy"
                        >
                    </div>

                    {{-- Real Slide 1 --}}
                    <div class="hero-slide hero-slide-1 active" data-track-index="1" data-real-index="0" aria-hidden="false">
                        <img
                            src="{{ asset('images/banner/banner.png') }}"
                            alt="MiniGrove Terrarium Banner 1"
                            class="hero-banner-img hero-slide-img"
                            width="2149"
                            height="732"
                            fetchpriority="high"
                        >
                    </div>

                    {{-- Real Slide 2 --}}
                    <div class="hero-slide hero-slide-2" data-track-index="2" data-real-index="1" aria-hidden="true">
                        <img
                            src="{{ asset('images/banner/banner2.png') }}"
                            alt="MiniGrove Terrarium Banner 2"
                            class="hero-banner-img hero-slide-img"
                            width="2149"
                            height="732"
                            loading="lazy"
                        >
                    </div>

                    {{-- Real Slide 3 --}}
                    <div class="hero-slide hero-slide-3" data-track-index="3" data-real-index="2" aria-hidden="true">
                        <img
                            src="{{ asset('images/banner/banner3.png') }}"
                            alt="MiniGrove Terrarium Banner 3"
                            class="hero-banner-img hero-slide-img"
                            width="2149"
                            height="732"
                            loading="lazy"
                        >
                    </div>

                    {{-- Real Slide 4 --}}
                    <div class="hero-slide hero-slide-4" data-track-index="4" data-real-index="3" aria-hidden="true">
                        <img
                            src="{{ asset('images/banner/banner4.png') }}"
                            alt="MiniGrove Terrarium Banner 4"
                            class="hero-banner-img hero-slide-img"
                            width="2148"
                            height="732"
                            loading="lazy"
                        >
                    </div>

                    {{-- Clone Slide 1 (for seamless infinite loop forward) --}}
                    <div class="hero-slide hero-slide-clone hero-slide-1" data-track-index="5" aria-hidden="true" tabindex="-1">
                        <img
                            src="{{ asset('images/banner/banner.png') }}"
                            alt=""
                            class="hero-banner-img hero-slide-img"
                            width="2149"
                            height="732"
                            loading="lazy"
                        >
                    </div>
                </div>

                {{-- Unified Gradient Overlay --}}
                <div class="hero-banner-overlay" aria-hidden="true"></div>

                {{-- Previous / Next Controls --}}
                <button
                    type="button"
                    class="hero-carousel-btn hero-carousel-prev"
                    id="hero-carousel-prev"
                    aria-label="Ảnh trước"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button
                    type="button"
                    class="hero-carousel-btn hero-carousel-next"
                    id="hero-carousel-next"
                    aria-label="Ảnh tiếp theo"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>

                {{-- Pagination Dots --}}
                <div class="hero-carousel-dots" id="hero-carousel-dots" role="tablist" aria-label="Chọn slide banner">
                    <button type="button" class="hero-dot active" data-slide-to="0" role="tab" aria-selected="true" aria-current="true" aria-label="Chuyển đến banner 1"></button>
                    <button type="button" class="hero-dot" data-slide-to="1" role="tab" aria-selected="false" aria-label="Chuyển đến banner 2"></button>
                    <button type="button" class="hero-dot" data-slide-to="2" role="tab" aria-selected="false" aria-label="Chuyển đến banner 3"></button>
                    <button type="button" class="hero-dot" data-slide-to="3" role="tab" aria-selected="false" aria-label="Chuyển đến banner 4"></button>
                </div>
            </div>

            {{-- Editorial Text Overlay Content --}}
            <div class="hero-content hero-banner-content">

                <div class="hero-eyebrow">
                    <span class="hero-eyebrow-accent" aria-hidden="true"></span>
                    <span>WELCOME TO MINIGROVE</span>
                </div>

                <h1 class="hero-title">
                    Nhỏ một khu rừng,<br>
                    lớn một thế giới.
                </h1>

                <p class="hero-text">
                    Khám phá những hệ sinh thái mini
                    được chăm chút từng chi tiết,
                    hoặc tự tay tạo nên khu rừng
                    mang dấu ấn riêng của bạn.
                </p>

                <div class="hero-actions">

                    <a
                        href="{{ route('products.index') }}"
                        class="
                            hero-button
                            hero-button-light
                            hero-button-primary
                        "
                    >
                        <span>Khám phá sản phẩm</span>
                        <svg class="hero-button-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>

                    <a
                        href="{{ route('studio.index') }}"
                        class="
                            hero-button
                            hero-button-outline
                        "
                    >
                        <span>Tự thiết kế Studio</span>
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- CATEGORY --}}
<section
    class="home-section reveal-on-scroll"
    id="categories"
>

    <div class="customer-container">


        <div class="section-heading category-section-heading">

            <div class="category-heading-left">

                <div class="section-eyebrow">
                    <span class="category-eyebrow-accent" aria-hidden="true"></span>
                    <span>KHÁM PHÁ THEO CHỦ ĐỀ</span>
                </div>

                <h2>
                    Danh mục MiniGrove
                </h2>

            </div>

            <div class="category-heading-right">

                <p>
                    Mỗi thế giới nhỏ mang một câu chuyện riêng.
                </p>

            </div>

        </div>


        <div class="category-grid" id="categoryGrid">

            @forelse ($categories as $category)

                <a
                    href="{{
                        route(
                            'products.index',
                            [
                                'category' => $category->slug
                            ]
                        )
                    }}"
                    class="
                        category-card
                        {{ $category->image
                            ? 'has-image'
                            : 'no-image' }}
                        {{ $loop->iteration > 5 ? 'category-card--extra' : '' }}
                    "
                    @if ($loop->iteration > 5)
                        aria-hidden="true"
                        tabindex="-1"
                    @endif
                >

                    @if ($category->image)

                        <img
                            class="category-image"
                            src="{{
                                asset(
                                    'storage/'
                                    . $category->image
                                )
                            }}"
                            alt="{{ $category->name }}"
                            loading="lazy"
                        >

                        <div class="category-overlay" aria-hidden="true"></div>

                    @else

                        <div class="category-fallback" aria-hidden="true">
                            <svg class="category-fallback-motif" width="120" height="120" viewBox="0 0 120 120" fill="none">
                                <path d="M60 20C40 40 30 65 30 90C55 90 80 80 100 60C100 35 85 20 60 20Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M60 20V90" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-dasharray="3 3"/>
                                <path d="M45 55C55 58 60 65 60 65" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                <path d="M75 45C65 48 60 55 60 55" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                        </div>

                        <div class="category-overlay" aria-hidden="true"></div>

                    @endif


                    <div class="category-content">

                        <div class="category-name">
                            {{ $category->name }}
                        </div>

                        <div class="category-count">
                            <span class="category-count-accent" aria-hidden="true"></span>
                            <span>{{ $category->active_products_count }} sản phẩm</span>
                        </div>

                    </div>

                </a>

            @empty

                <div class="home-empty">
                    Chưa có danh mục đang hoạt động.
                </div>

            @endforelse

        </div>

        @if ($categories->count() > 5)
            <div class="category-toggle-wrap">
                <button
                    type="button"
                    class="category-toggle-btn"
                    id="categoryToggleBtn"
                    aria-expanded="false"
                    aria-controls="categoryGrid"
                >
                    <span class="category-toggle-text">Xem thêm danh mục</span>
                    <svg
                        class="category-toggle-icon"
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
        @endif

    </div>

</section>


{{-- FEATURED PRODUCTS --}}
<section
    class="home-section reveal-on-scroll"
    id="products"
>

    <div class="customer-container">


        <div class="section-heading featured-section-heading">

            <div class="featured-heading-left">

                <div class="section-eyebrow">
                    <span class="featured-eyebrow-accent" aria-hidden="true"></span>
                    <span>ĐƯỢC YÊU THÍCH</span>
                </div>

                <h2>
                    Sản phẩm nổi bật
                </h2>

            </div>

            <div class="featured-heading-right">

                <p>
                    Những hệ sinh thái MiniGrove được lựa chọn dành cho bạn.
                </p>

            </div>

        </div>


        <div class="featured-products-grid product-grid">

            @forelse (
                $featuredProducts
                as $product
            )

                <div class="featured-product-slot {{ $loop->first ? 'featured-product-slot--focal' : '' }}">

                    @include(
                        'partials.product-card',
                        [
                            'product' => $product
                        ]
                    )

                </div>

            @empty

                <div class="home-empty">

                    Chưa có sản phẩm nổi bật.

                </div>

            @endforelse

        </div>

    </div>

</section>


{{-- STUDIO --}}
<section class="studio-promo reveal-on-scroll" id="studio">
    <div class="customer-container">
        <div class="studio-panel">
            {{-- Real Terrarium Background Image Layer & Editorial Gradient Overlay --}}
            <div class="studio-bg-layer" aria-hidden="true">
                <img
                    src="{{ asset('images/studio/anh1.png') }}"
                    alt=""
                    class="studio-bg-img"
                    width="1672"
                    height="941"
                    loading="lazy"
                >
                <div class="studio-bg-overlay"></div>
            </div>

            <div class="studio-grid">
                {{-- Left: Workshop Narrative & Process --}}
                <div class="studio-info">
                    <div class="studio-eyebrow">
                        <span class="studio-eyebrow-badge">STUDIO MINIGROVE</span>
                        <span class="studio-eyebrow-divider" aria-hidden="true"></span>
                        <span class="studio-eyebrow-tag">TRẢI NGHIỆM THIẾT KẾ</span>
                    </div>

                    <h2 class="studio-heading">
                        Tạo khu rừng<br>của riêng bạn
                    </h2>

                    <p class="studio-desc">
                        Tự tay lựa chọn từ phôi bình kính, lớp nền sinh học đến từng nhánh rêu, phiến đá và tán cây nhiệt đới. Studio tự động kiểm tra tương thích sinh thái và tính giá trực tiếp cho thiết kế của bạn.
                    </p>

                    {{-- 6-step real process indicators --}}
                    <div class="studio-process" aria-label="Quy trình 6 bước thiết kế terrarium">
                        <div class="studio-process-label">
                            <span class="studio-process-num" aria-hidden="true">01—06</span>
                            <span>Quy trình sáng tạo 6 bước</span>
                        </div>
                        <div class="studio-steps-grid">
                            <div class="studio-step-chip">
                                <span class="step-idx">01</span>
                                <span class="step-name">Bình kính</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">02</span>
                                <span class="step-name">Cây &amp; Rêu</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">03</span>
                                <span class="step-name">Đất nền</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">04</span>
                                <span class="step-name">Lũa &amp; Đá</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">05</span>
                                <span class="step-name">Phụ kiện</span>
                            </div>
                            <div class="studio-step-chip">
                                <span class="step-idx">06</span>
                                <span class="step-name">Phong cách</span>
                            </div>
                        </div>
                    </div>

                    <div class="studio-action">
                        <a
                            href="{{ route('studio.index') }}"
                            class="studio-button"
                        >
                            <span class="studio-btn-text">Bắt đầu thiết kế</span>
                            <span class="studio-btn-arrow" aria-hidden="true">→</span>
                        </a>
                        <span class="studio-note">Khám phá xưởng terrarium trực tuyến</span>
                    </div>
                </div>

                {{-- Right: Focal area showcasing real terrarium background --}}
                <div class="studio-focal-area" aria-hidden="true"></div>
            </div>
        </div>
    </div>
</section>


{{-- NEW PRODUCTS --}}
<section
    class="home-section reveal-on-scroll"
    id="new-products"
>

    <div class="customer-container">


        <div class="section-heading new-products-heading">

            <div class="new-heading-left">

                <div class="section-eyebrow">
                    <span class="new-eyebrow-accent" aria-hidden="true"></span>
                    <span>VỪA CẬP NHẬT</span>
                </div>

                <h2>
                    Sản phẩm mới
                </h2>

            </div>

            <div class="new-heading-right">

                <p>
                    Khám phá những MiniGrove mới nhất tại cửa hàng.
                </p>

            </div>

        </div>


        <div class="new-products-grid product-grid">

            @forelse (
                $newProducts
                as $product
            )

                <div class="new-product-slot">

                    @include(
                        'partials.product-card',
                        [
                            'product' => $product
                        ]
                    )

                </div>

            @empty

                <div class="home-empty">
                    Chưa có sản phẩm.
                </div>

            @endforelse

        </div>

    </div>

</section>


@push('scripts')
<script>
(function () {
    'use strict';
    if (typeof window === 'undefined' || !('IntersectionObserver' in window)) return;

    // Fail-safe: activate reveal CSS only when IntersectionObserver is supported & active
    document.documentElement.classList.add('js-motion-ready');

    var revealElements = document.querySelectorAll('.reveal-on-scroll');
    if (!revealElements.length) return;

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.08,
        rootMargin: '0px 0px -40px 0px'
    });

    revealElements.forEach(function (el) {
        observer.observe(el);
    });
})();

// Hero Multi-Banner Horizontal Sliding Carousel Controller
(function () {
    'use strict';

    var carousel = document.querySelector('.hero-carousel');
    if (!carousel) return;

    var track = document.getElementById('hero-carousel-track');
    if (!track) return;

    var realSlides = track.querySelectorAll('.hero-slide:not(.hero-slide-clone)');
    var dots = carousel.querySelectorAll('.hero-dot');
    var prevBtn = document.getElementById('hero-carousel-prev');
    var nextBtn = document.getElementById('hero-carousel-next');
    var heroBox = document.querySelector('.hero-banner-box');
    var totalRealSlides = realSlides.length;

    if (totalRealSlides <= 1) return;

    // Track has 6 slides:
    // 0: Clone Slide 4
    // 1: Real Slide 1 (initial active)
    // 2: Real Slide 2
    // 3: Real Slide 3
    // 4: Real Slide 4
    // 5: Clone Slide 1
    var currentTrackIndex = 1;
    var timer = null;
    var intervalMs = 2000; // Autoplay delay: exactly 2000ms
    var isHovered = false;
    var isFocused = false;
    var isAnimating = false;
    var animationFallbackTimer = null;
    var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Mark as ready for JS transitions
    carousel.classList.add('js-carousel-ready');

    function updateRealSlideState(realIndex) {
        realSlides.forEach(function (slide, i) {
            var isActive = (i === realIndex);
            slide.classList.toggle('active', isActive);
            slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        });

        dots.forEach(function (dot, i) {
            var isSelected = (i === realIndex);
            dot.classList.toggle('active', isSelected);
            dot.setAttribute('aria-selected', isSelected ? 'true' : 'false');
            if (isSelected) {
                dot.setAttribute('aria-current', 'true');
            } else {
                dot.removeAttribute('aria-current');
            }
        });
    }

    function moveToTrackIndex(targetIndex, instant) {
        if (isAnimating && !instant) return;

        // Determine corresponding real slide index (0..3)
        var realIndex = 0;
        if (targetIndex === 0) {
            realIndex = totalRealSlides - 1; // Clone 4 maps to real slide 4 (index 3)
        } else if (targetIndex === totalRealSlides + 1) {
            realIndex = 0; // Clone 1 maps to real slide 1 (index 0)
        } else {
            realIndex = targetIndex - 1;
        }

        updateRealSlideState(realIndex);

        if (instant || prefersReducedMotion) {
            track.style.transition = 'none';
            currentTrackIndex = targetIndex;
            track.style.transform = 'translate3d(' + (-targetIndex * 100) + '%, 0, 0)';
            void track.offsetHeight; // Force reflow
            track.style.transition = '';

            // Handle clone boundary immediately if reduced motion
            if (currentTrackIndex === totalRealSlides + 1) {
                currentTrackIndex = 1;
                track.style.transform = 'translate3d(-100%, 0, 0)';
            } else if (currentTrackIndex === 0) {
                currentTrackIndex = totalRealSlides;
                track.style.transform = 'translate3d(' + (-totalRealSlides * 100) + '%, 0, 0)';
            }
            isAnimating = false;
            return;
        }

        isAnimating = true;
        currentTrackIndex = targetIndex;
        track.style.transform = 'translate3d(' + (-targetIndex * 100) + '%, 0, 0)';

        // Safety fallback timer (850ms) in case transitionend event is missed
        clearTimeout(animationFallbackTimer);
        animationFallbackTimer = setTimeout(function () {
            if (isAnimating) {
                handleTransitionEnd();
            }
        }, 850);
    }

    function handleTransitionEnd() {
        clearTimeout(animationFallbackTimer);
        if (!isAnimating) return;

        // If moved forward onto Clone Slide 1 (index 5) -> seamlessly snap to Real Slide 1 (index 1)
        if (currentTrackIndex === totalRealSlides + 1) {
            track.style.transition = 'none';
            currentTrackIndex = 1;
            track.style.transform = 'translate3d(-100%, 0, 0)';
            void track.offsetHeight; // Force reflow
            track.style.transition = '';
        }
        // If moved backward onto Clone Slide 4 (index 0) -> seamlessly snap to Real Slide 4 (index 4)
        else if (currentTrackIndex === 0) {
            track.style.transition = 'none';
            currentTrackIndex = totalRealSlides;
            track.style.transform = 'translate3d(' + (-totalRealSlides * 100) + '%, 0, 0)';
            void track.offsetHeight; // Force reflow
            track.style.transition = '';
        }

        isAnimating = false;
    }

    // Listen for transitionend on track
    track.addEventListener('transitionend', function (e) {
        if (e.target !== track) return;
        if (e.propertyName !== 'transform') return;
        handleTransitionEnd();
    });

    function nextSlide() {
        if (isAnimating) return;
        moveToTrackIndex(currentTrackIndex + 1, false);
    }

    function prevSlide() {
        if (isAnimating) return;
        moveToTrackIndex(currentTrackIndex - 1, false);
    }

    function shouldAutoplay() {
        return !isHovered && !isFocused && !document.hidden;
    }

    function startAutoplay() {
        stopAutoplay();
        if (!shouldAutoplay()) return;
        timer = setInterval(function () {
            nextSlide();
        }, intervalMs);
    }

    function stopAutoplay() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    function resetAutoplay() {
        stopAutoplay();
        if (shouldAutoplay()) {
            startAutoplay();
        }
    }

    // Previous / Next controls
    if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (isAnimating) return;
            nextSlide();
            resetAutoplay();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (isAnimating) return;
            prevSlide();
            resetAutoplay();
        });
    }

    // Pagination dots
    dots.forEach(function (dot) {
        dot.addEventListener('click', function (e) {
            e.preventDefault();
            if (isAnimating) return;
            var targetRealIndex = parseInt(dot.getAttribute('data-slide-to'), 10);
            if (!isNaN(targetRealIndex)) {
                var targetTrack = targetRealIndex + 1;
                if (targetTrack !== currentTrackIndex) {
                    moveToTrackIndex(targetTrack, false);
                }
                resetAutoplay();
            }
        });
    });

    // Pause on hover and focus
    if (heroBox) {
        heroBox.addEventListener('mouseenter', function () {
            isHovered = true;
            stopAutoplay();
        });

        heroBox.addEventListener('mouseleave', function () {
            isHovered = false;
            if (shouldAutoplay()) {
                startAutoplay();
            }
        });

        heroBox.addEventListener('focusin', function () {
            isFocused = true;
            stopAutoplay();
        });

        heroBox.addEventListener('focusout', function (e) {
            if (heroBox.contains(e.relatedTarget)) return;
            isFocused = false;
            if (shouldAutoplay()) {
                startAutoplay();
            }
        });

        // Keyboard navigation (ArrowLeft & ArrowRight)
        heroBox.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                if (isAnimating) return;
                prevSlide();
                resetAutoplay();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                if (isAnimating) return;
                nextSlide();
                resetAutoplay();
            }
        });

        // Touch swipe handling for mobile
        var touchStartX = 0;
        var touchStartY = 0;
        var touchStartTime = 0;

        heroBox.addEventListener('touchstart', function (e) {
            if (e.touches && e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                touchStartTime = Date.now();
                stopAutoplay();
            }
        }, { passive: true });

        heroBox.addEventListener('touchend', function (e) {
            if (e.changedTouches && e.changedTouches.length === 1) {
                var touchEndX = e.changedTouches[0].clientX;
                var touchEndY = e.changedTouches[0].clientY;
                var deltaX = touchEndX - touchStartX;
                var deltaY = touchEndY - touchStartY;
                var deltaTime = Date.now() - touchStartTime;

                // Threshold 45px and predominantly horizontal movement
                if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) >= 45 && deltaTime <= 800) {
                    if (deltaX < 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                }
            }
            if (shouldAutoplay()) {
                startAutoplay();
            }
        }, { passive: true });
    }

    // Page visibility (tab switch / background)
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            stopAutoplay();
        } else {
            if (shouldAutoplay()) {
                startAutoplay();
            }
        }
    });

    window.addEventListener('blur', function () {
        stopAutoplay();
    });

    window.addEventListener('focus', function () {
        if (shouldAutoplay()) {
            startAutoplay();
        }
    });

    // Set initial position to Real Slide 1 (track index 1)
    moveToTrackIndex(1, true);
    startAutoplay();
})();

// Category Section Expand / Collapse Controller
(function () {
    'use strict';

    var toggleBtn = document.getElementById('categoryToggleBtn');
    var categoryGrid = document.getElementById('categoryGrid');
    var categoriesSection = document.getElementById('categories');

    if (!toggleBtn || !categoryGrid) return;

    var toggleText = toggleBtn.querySelector('.category-toggle-text');
    var extraCards = categoryGrid.querySelectorAll('.category-card--extra');

    toggleBtn.addEventListener('click', function () {
        var isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';

        if (!isExpanded) {
            // Expand remaining categories
            categoryGrid.classList.add('is-expanded');
            toggleBtn.setAttribute('aria-expanded', 'true');
            if (toggleText) {
                toggleText.textContent = 'Thu gọn danh mục';
            }
            extraCards.forEach(function (card) {
                card.removeAttribute('aria-hidden');
                card.removeAttribute('tabindex');
            });
        } else {
            // Check position before collapsing
            var sectionRect = categoriesSection ? categoriesSection.getBoundingClientRect() : null;
            var btnRect = toggleBtn.getBoundingClientRect();

            // Collapse to first 5 categories
            categoryGrid.classList.remove('is-expanded');
            toggleBtn.setAttribute('aria-expanded', 'false');
            if (toggleText) {
                toggleText.textContent = 'Xem thêm danh mục';
            }
            extraCards.forEach(function (card) {
                card.setAttribute('aria-hidden', 'true');
                card.setAttribute('tabindex', '-1');
            });

            // Smooth scroll back to category section header if scrolled past
            if (categoriesSection && (sectionRect.top < 30 || btnRect.top > window.innerHeight)) {
                categoriesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
})();
</script>
@endpush

@endsection