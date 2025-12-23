<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield('page_title') | {{ config('app.name') }}</title>

    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
    <link rel="shortcut icon" href="/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="U-Prepare" />
    <link rel="manifest" href="/favicon/site.webmanifest" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap">

    <link rel="stylesheet" href="{{ asset('asset/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">


    <!-- Vendor CSS Files -->
    <link rel="stylesheet" href="{{ asset('asset/vendors/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">

    <!-- Template Main CSS File -->
    <link rel="stylesheet" href="/assets/public/css/styles.css?ver=1.1.4" title="main">
    <link rel="stylesheet" href="/assets/public/css/home.css?ver=1.1.5">
   
    <style>
        main {
            min-height: calc(100vh - 335px);
        }
    </style>
</head>

<body>
    @include('public.layout.inc.header')

    <main id="main" class="d-flex flex-column">
        {{ $slot }}
    </main>

    @include('public.layout.inc.footer')

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Vendor JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cash/8.1.5/cash.min.js"
        integrity="sha512-rUmgzTTLW3Ncifitwec3TMK8qQJK17vYU7r7RjMnXfg2SoEWvVWn7doqhtB8j0KTIHIr3iv4CfOXXEJbPFll6Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Template Main JS File -->


    <script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js"></script>
    <script>
        (function() {
            $('.mobile-nav-toggle').on('click', function() {
                $(this).toggleClass('bi-x');
                $(this).parent().toggleClass('navbar-mobile');
            });

            $('nav.navbar ul li.dropdown').on('click', function() {
                let $tarul = $(this).find('ul');

                if (!$tarul.hasClass('dropdown-active')) {
                    $(this).parent().find('.dropdown-active').removeClass('dropdown-active');
                }

                $(this).find('ul').toggleClass('dropdown-active');
            })

            let mainss = null;
            let bodycr = null;

            $('header .zoom > .inc').on('click', function(e) {
                if (mainss && bodycr) {
                    docZoom(1);
                } else {
                    setZCR(1)
                }
            });

            $('nav.navbar .search').on('click', function(e) {
                e.stopPropagation();
                $(this).next('.sinp-box').toggleClass('d-none');
            });

            $('body').on('click', function(e) {
                if (!e.target.classList.contains('sinp-box') && !e.target.classList.contains('search')) {
                    $('nav.navbar .sinp-box').addClass('d-none');
                }
            });

            $('header .zoom > .dec').on('click', function(e) {
                if (mainss && bodycr) {
                    docZoom(0);
                } else {
                    setZCR(0)
                }
            });

            $('a[href="#"]').on('click', function(e) {
                e.preventDefault();
            });

            function setZCR(zf) {
                mainss = getStyleSheet('main');

                if (mainss) {
                    bodycr = findCSSRule(mainss, 'body');

                    if (bodycr) {
                        docZoom(zf);
                    }
                }
            }

            function docZoom(typ) {
                let czv = parseFloat(bodycr.style.zoom);

                czv = typ ? (czv + 0.1) : (czv - 0.1);

                czv = (czv > 2) ? 2 : ((czv < 0.5) ? 0.5 : czv);


                bodycr.style.zoom = czv;
            }

            function getStyleSheet(id) {
                for (var i = 0; i < document.styleSheets.length; i++) {
                    var sheet = document.styleSheets[i];

                    if (sheet.title == id) {
                        return sheet;
                    }
                }
            }

            function findCSSRule(ss, rule) {
                let index = 0;

                for (let i = 0; i < ss.cssRules.length; i++) {
                    if (ss.cssRules[i].selectorText == rule) {
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
                    } else {
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

            var homeSlider = tns({
                items: 1,
                slideBy: 'page',
                autoplay: true,
                container: '.hero-slider',
                navPosition: 'bottom',
                autoplayButton: false,
                autoplayButtonOutput: false,
                controlsText: [
                    '<span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>',
                    '<span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>'
                ]
            });

            var ppsSlider = tns({
                nav: 0,
                items: 1,
                speed: 1000,
                slideBy: 'page',
                autoplay: true,
                controls: false,
                container: '.pps-slider',
                navPosition: 'bottom',
                autoplayButton: false,
                autoplayButtonOutput: false,
            });

            var compSlider = tns({
                items: 4,
                slideBy: 'page',
                autoplay: true,
                controls: false,
                container: '.components-slider',
                responsive: {
                    0: {
                        items: 1
                    },
                    767: {
                        items: 2
                    },
                    900: {
                        items: 3
                    },
                    1100: {
                        items: 4
                    }
                },
                navPosition: 'bottom',
                autoplayButton: false,
                autoplayButtonOutput: false,
            })

            var vidSlider = tns({
                items: 4,
                slideBy: 'page',
                autoplay: true,
                controls: false,
                container: '.videos-slider',
                responsive: {
                    0: {
                        items: 1
                    },
                    767: {
                        items: 2
                    },
                    900: {
                        items: 3
                    },
                    1100: {
                        items: 4
                    }
                },
                navPosition: 'bottom',
                autoplayButton: false,
                autoplayButtonOutput: false,
            })
        })()
    </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (!toggleBtn || !password) return;

    toggleBtn.addEventListener('click', function () {
        const icon = toggleBtn.querySelector('i');
        if (!icon) return;

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});
</script>

</body>

</html>
