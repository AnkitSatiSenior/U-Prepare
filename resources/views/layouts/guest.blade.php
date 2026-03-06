<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO & Canonical --}}
    <title>@yield('page_title') | {{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', 'Professional Preparation Platform')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicons --}}
    <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
    <link rel="manifest" href="/favicon/site.webmanifest" />

    {{-- 1. Performance: Preconnect & Preload --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="preload" href="{{ asset('asset/vendors/bootstrap/dist/css/bootstrap.min.css') }}" as="style">
    <link rel="preload" href="/assets/public/css/styles.css?ver=1.1.4" as="style">

    {{-- 2. Fonts (Non-blocking load) --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">

    {{-- 3. CSS Vendors --}}
    <link rel="stylesheet" href="{{ asset('asset/vendors/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">

    {{-- 4. Main Application Styles --}}
    <link rel="stylesheet" href="/assets/public/css/styles.css?ver=1.1.4" title="main">
    <link rel="stylesheet" href="/assets/public/css/home.css?ver=1.1.5">

    <style>
        main {
            min-height: calc(100vh - 335px);
        }

        .back-to-top {
            visibility: hidden;
            opacity: 0;
            transition: all 0.4s;
            z-index: 999;
        }

        .back-to-top.active {
            visibility: visible;
            opacity: 1;
        }

        /* Professional fix for zoom logic */
        body {
            transition: zoom 0.2s ease-in-out;
        }
    </style>
</head>

<body class="antialiased">
    @include('public.layout.inc.header')

    <main id="main" class="d-flex flex-column">
        {{ $slot }}
    </main>

    @include('public.layout.inc.footer')

    {{-- Back to Top Button --}}
    <button id="backToTop"
        class="back-to-top btn btn-primary position-fixed bottom-0 end-0 m-4 d-flex align-items-center justify-content-center"
        style="width: 44px; height: 44px; border-radius: 50%;" aria-label="Scroll to top">
        <i class="bi bi-arrow-up-short" style="font-size: 1.5rem;"></i>
    </button>

    {{-- 5. Optimized Scripts (Deferred) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Back to Top Logic
            const backtotop = document.getElementById('backToTop');
            if (backtotop) {
                const toggleBacktotop = () => {
                    window.scrollY > 100 ? backtotop.classList.add('active') : backtotop.classList.remove('active');
                };
                window.addEventListener('scroll', toggleBacktotop);
                backtotop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
            }

            // 2. Navigation & UI Logic (Delegated for performance)
            document.addEventListener('click', function(e) {
                // Mobile Nav Toggle
                if (e.target.closest('.mobile-nav-toggle')) {
                    const btn = e.target.closest('.mobile-nav-toggle');
                    btn.classList.toggle('bi-x');
                    btn.parentElement.classList.toggle('navbar-mobile');
                }

                // Dropdowns
                if (e.target.closest('nav.navbar ul li.dropdown')) {
                    const dropdown = e.target.closest('nav.navbar ul li.dropdown');
                    dropdown.querySelector('ul').classList.toggle('dropdown-active');
                }

                // Search Box Toggle
                if (e.target.closest('.search')) {
                    e.stopPropagation();
                    const searchBox = document.querySelector('.sinp-box');
                    if (searchBox) searchBox.classList.toggle('d-none');
                } else if (!e.target.closest('.sinp-box')) {
                    const searchBox = document.querySelector('.sinp-box');
                    if (searchBox) searchBox.classList.add('d-none');
                }
            });

            // 3. Accessibility Zoom Logic (Refactored for efficiency)
            let currentZoom = 1;
            const updateZoom = (delta) => {
                currentZoom = Math.min(Math.max(currentZoom + delta, 0.5), 2);
                document.body.style.zoom = currentZoom;
            };

            document.addEventListener('click', function(e) {
                if (e.target.closest('.zoom > .inc')) updateZoom(0.1);
                if (e.target.closest('.zoom > .dec')) updateZoom(-0.1);
            });

            // 4. Slider Initializations (Wrapped in try-catch to prevent page crashes)
            try {
                if (document.querySelector('.hero-slider')) {
                    tns({
                        container: '.hero-slider',
                        items: 1,
                        slideBy: 'page',
                        autoplay: true,
                        navPosition: 'bottom',
                        autoplayButtonOutput: false,
                        controlsText: ['<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>']
                    });
                }

                const commonSliderConfig = {
                    slideBy: 'page',
                    autoplay: true,
                    controls: false,
                    navPosition: 'bottom',
                    autoplayButtonOutput: false,
                };

                if (document.querySelector('.components-slider')) {
                    tns({
                        ...commonSliderConfig,
                        container: '.components-slider',
                        items: 4,
                        responsive: { 0: { items: 1 }, 767: { items: 2 }, 1100: { items: 4 } }
                    });
                }

                if (document.querySelector('.videos-slider')) {
                    tns({
                        ...commonSliderConfig,
                        container: '.videos-slider',
                        items: 4,
                        responsive: { 0: { items: 1 }, 767: { items: 2 }, 1100: { items: 4 } }
                    });
                }
            } catch (error) {
                console.warn("Slider initialization failed:", error);
            }
        });
    </script>
</body>

</html>