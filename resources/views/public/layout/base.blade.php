<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">

        <title>@yield('page_title') | {{ config('app.name') }}</title>

        <meta content="" name="description">
        <meta content="" name="keywords">

        <!-- Favicons -->
        <link href="assets/img/favicon.png" rel="icon">
        <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap">
        {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"> --}}
        {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap"> --}}

        <!-- Vendor CSS Files -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        @yield('vendor_stylesheets')

        <!-- Template Main CSS File -->
        <link rel="stylesheet" href="{{  asset('assets/css/style.css') }}?ver=1.1.4" title="main">
        @yield('header_stylesheets')

        @yield('header_styles')
	<style>main{min-height: calc(100vh - 435px);}</style>
    </head>

    <body>
        @include('public.layout.inc.header')

        <main id="main" class="d-flex flex-column">
            @yield('page_content')
        </main>

        @include('public.layout.inc.footer')

        <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
            <i class="bi bi-arrow-up-short"></i>
        </a>

        <!-- Vendor JS Files -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        @if(Route::currentRouteName() == 'public.grievance.register')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @else
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cash/8.1.5/cash.min.js" integrity="sha512-rUmgzTTLW3Ncifitwec3TMK8qQJK17vYU7r7RjMnXfg2SoEWvVWn7doqhtB8j0KTIHIr3iv4CfOXXEJbPFll6Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @endif

        <!-- Template Main JS File -->
        {{-- <script src="{{ asset('web/assets/js/main.js') }}"></script> --}}

        @yield('footer_scripts')

        <script>
            (function() {
                $('.mobile-nav-toggle').on('click', function() {
                    $(this).toggleClass('bi-x');
                    $(this).parent().toggleClass('navbar-mobile');
                });

                $('nav.navbar ul li.dropdown').on('click', function() {
                    let $tarul = $(this).find('ul');

                    if(!$tarul.hasClass('dropdown-active')) {
                        $(this).parent().find('.dropdown-active').removeClass('dropdown-active');
                    }

                    $(this).find('ul').toggleClass('dropdown-active');
                })

                let mainss = null;
                let bodycr = null;

                $('header .zoom > .inc').on('click', function(e) {
                    if(mainss && bodycr) {
                        docZoom(1);
                    }else {
                        setZCR(1)
                    }
                });

                $('nav.navbar .search').on('click', function(e) {
                    e.stopPropagation();
                    $(this).next('.sinp-box').toggleClass('d-none');
                });

                $('body').on('click', function(e) {
                    if(!e.target.classList.contains('sinp-box') && !e.target.classList.contains('search')) {
                        $('nav.navbar .sinp-box').addClass('d-none');
                    }
                });

                $('header .zoom > .dec').on('click', function(e) {
                    if(mainss && bodycr) {
                        docZoom(0);
                    }else {
                        setZCR(0)
                    }
                });

                $('a[href="#"]').on('click', function(e) {
                    e.preventDefault();
                });

                function setZCR(zf) {
                    mainss = getStyleSheet('main');

                    if(mainss) {
                        bodycr = findCSSRule(mainss, 'body');

                        if(bodycr) {
                            docZoom(zf);
                        }
                    }
                }

                function docZoom(typ) {
                    let czv  = parseFloat(bodycr.style.zoom);

                    czv = typ ? (czv + 0.1) : (czv - 0.1);

                    czv = (czv > 2) ? 2 : ( (czv < 0.5) ? 0.5 : czv );


                    bodycr.style.zoom = czv;
                }

                function getStyleSheet(id) {
                    for(var i=0; i<document.styleSheets.length; i++) {
                        var sheet = document.styleSheets[i];

                        if(sheet.title == id) {
                            return sheet;
                        }
                    }
                }

                function findCSSRule(ss, rule) {
                    let index = 0;

                    for(let i=0; i<ss.cssRules.length; i++) {
                        if(ss.cssRules[i].selectorText == rule) {
                            index = i;
                            break;
                        }
                    }

                    return ss.cssRules[index];
                }

                const onscroll = (el, listener) => {
                    el.addEventListener('scroll', listener)
                }

                /**
                 * Back to top button
                 */
                let backtotop = $('.back-to-top');
                if (backtotop.length) {
                    const toggleBacktotop = () => {
                        if (window.scrollY > 100) {
                            backtotop.get(0).classList.add('active')
                        }else {
                            backtotop.get(0).classList.remove('active')
                        }
                    }

                    window.addEventListener('load', toggleBacktotop)
                    onscroll(document, toggleBacktotop)
                }

                backtotop.on('click', function(e) {
                    e.preventDefault();
                    let elementPos = $('body').get(0).offsetTop

                    window.scrollTo({
                        top: elementPos,
                        behavior: 'smooth'
                    })
                });

                @yield('inpage_scripts')
            })()
        </script>

        @yield('script')
    </body>
</html>
