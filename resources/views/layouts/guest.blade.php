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

    {{-- Application Styles --}}
    <link rel="stylesheet" href="/assets/public/css/styles.css?ver=1.1.4" title="main">
    <link rel="stylesheet" href="/assets/public/css/home.css?ver=1.1.5">
    <link rel="stylesheet" href="/assets/public/css/guest-layout.css?ver=1.0.0">
    @yield('header_stylesheets')
    @stack('styles')
    @yield('header_styles')
</head>

<body class="guest-body antialiased">
    <a class="skip-link" href="#main">{{ __('Skip to main content') }}</a>

    @include('public.layout.inc.header')

    <main id="main" class="site-main d-flex flex-column" tabindex="-1">
        {{ $slot }}
    </main>

    @include('public.layout.inc.footer')

    <button id="backToTop"
        class="back-to-top btn btn-primary d-flex align-items-center justify-content-center"
        aria-label="{{ __('Scroll to top') }}">
        <i class="bi bi-arrow-up-short" aria-hidden="true"></i>
    </button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backtotop = document.getElementById('backToTop');
            if (backtotop) {
                const toggleBacktotop = () => {
                    backtotop.classList.toggle('active', window.scrollY > 100);
                };
                toggleBacktotop();
                window.addEventListener('scroll', toggleBacktotop);
                backtotop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
            }

            document.addEventListener('click', function(e) {
                if (e.target.closest('.mobile-nav-toggle')) {
                    const btn = e.target.closest('.mobile-nav-toggle');
                    const isOpen = btn.parentElement.classList.toggle('navbar-mobile');
                    btn.classList.toggle('bi-x', isOpen);
                    btn.setAttribute('aria-expanded', String(isOpen));
                }

                if (e.target.closest('nav.navbar.navbar-mobile ul li.dropdown > a')) {
                    e.preventDefault();
                    const trigger = e.target.closest('nav.navbar.navbar-mobile ul li.dropdown > a');
                    const dropdown = trigger.closest('li.dropdown');
                    const menu = dropdown.querySelector('ul');
                    if (menu) {
                        const isOpen = menu.classList.toggle('dropdown-active');
                        trigger.setAttribute('aria-expanded', String(isOpen));
                    }
                }

                if (e.target.closest('.search')) {
                    e.stopPropagation();
                    const searchBox = document.querySelector('.sinp-box');
                    if (searchBox) searchBox.classList.toggle('d-none');
                } else if (!e.target.closest('.sinp-box')) {
                    const searchBox = document.querySelector('.sinp-box');
                    if (searchBox) searchBox.classList.add('d-none');
                }
            });

            let currentZoom = 1;
            const updateZoom = (delta) => {
                currentZoom = Math.min(Math.max(currentZoom + delta, 0.5), 2);
                document.body.style.zoom = currentZoom;
            };

            document.addEventListener('click', function(e) {
                if (e.target.closest('.zoom > .inc')) {
                    e.preventDefault();
                    updateZoom(0.1);
                }
                if (e.target.closest('.zoom > .dec')) {
                    e.preventDefault();
                    updateZoom(-0.1);
                }
            });

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
                        gutter: 16,
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

            @yield('inpage_scripts')
        });
    </script>
    @yield('footer_scripts')
    @stack('scripts')
</body>

</html>
