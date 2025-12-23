@extends('public.layout.base')

@section('page_title'){{ __('Enhancing Infrastructure Resilience') }}@endsection

@section('header_styles')
    <style>
        .fs-18r {
            font-size: 1.8rem;
        }

        img.rounded {
            border-radius: 15px !important;
        }
    </style>
@endsection

@section('page_content')
    {{-- <section class="container-fluid p-0">
        <div class="hero-img">
            <img class="w-100 h-100" src="{{ asset('assets/public/img/enhancing-infra-resilience-hero.webp') }}" alt="">
            <div class="caption">
                <h1 class="mb-3 fw-bold text-white">Enhancing Infrastructure Resilience</h1>
                <p class="mb-0 text-white text-justify">
                    Aims to fortify critical infrastructure against climate and disaster risks by enhancing road infrastructure resilience,
                    improving health service facilities' readiness, and establishing disaster shelters in vulnerable areas. Measures include
                    reinforcing bridges, implementing bioengineering solutions to mitigate landslides, retrofitting health centers for
                    earthquake and fire safety, and constructing energy-efficient disaster shelters along major routes. These efforts align
                    with the project's objective of integrating resilience into infrastructure planning to better withstand environmental
                    hazards in Uttarakhand.
                </p>
            </div>
        </div>
    </section> --}}

    <section class="container-xxl">
        <div class="row">
            <div class="col-lg-6">
                <h1 class="fs-18r fw-bold text-uppercase">Enhancing Infrastructure Resilience</h1>
            </div>
            <div class="col-lg-6"></div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <p class="mb-0 text-justify">
                    Aims to fortify critical infrastructure against climate and disaster risks by enhancing road infrastructure resilience,
                    improving health service facilities' readiness, and establishing disaster shelters in vulnerable areas. Measures include
                    reinforcing bridges, implementing bioengineering solutions to mitigate landslides, retrofitting health centers for
                    earthquake and fire safety, and constructing energy-efficient disaster shelters along major routes. These efforts align
                    with the project's objective of integrating resilience into infrastructure planning to better withstand environmental
                    hazards in Uttarakhand.
                </p>
            </div>
            <div class="col-lg-6">
                <img class="img-fluid rounded" src="{{ asset('assets/img/infra-resilience.webp') }}" >
            </div>
        </div>
    </section>
@endsection
