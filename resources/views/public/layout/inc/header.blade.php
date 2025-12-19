<!-- ======= Header ======= -->
<header id="header" class="">
    @include('public.layout.inc.header.topbar')

    <section class="main-nav p-0">
        <div class="container-fluid d-flex align-items-center justify-content-between py-3">
            <h1 class="logo">
                <a href="{{ route('welcome.default') }}">
                    <img src="{{ asset('assets/img/updated-logo.png') }}" >
                </a>
            </h1>
        </div>

        @include('public.layout.inc.header.navbar')
    </section>
</header>
<!-- End Header -->
