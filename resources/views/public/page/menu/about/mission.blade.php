@extends('public.layout.base')

@section('page_title'){{ __('Mission and Vision') }}@endsection

@section('header_styles')
    <style>
        section .hero-img {
            width: 100%;
            position: relative;
            padding-bottom: calc(100% * (6/16));
        }

        .hero-img > img {
            position: absolute;
            object-fit: fill;
        }

        .hero-img .overlay {
            width: 100%;
            height: 100%;
            opacity: 0.25;
            position: absolute;
            background: black;
        }

        .hero-img > .caption {
            bottom: 20%;
            position: absolute;
            max-width: 56%;
            padding-left: 60px;
        }

        .hero-img > .caption h1 {
            color: white;
            font-size: 2.3rem;
            text-transform: uppercase;
        }

        .mission-content h2 > span {
            color: var(--color-theme);
        }

        .mission-content h1 {

        }
    </style>
@endsection

@section('page_content')
    <section class="container-fluid p-0">
        <div class="hero-img">
            <img class="w-100 h-100" src="{{ asset('assets/img/hero/mission-and-vision.webp') }}" alt="Mission and Vision">
            <div class="overlay"></div>
            <div class="caption">
                <h1 class="fw-bold">Empowering RESILIENT FUTURES</h1>
                <p class="mb-0 text-white">Building resilient Uttarakhand infrastructure against natural disasters for safer, sustainable, and empowered communities with improved emergency responses.</p>
            </div>
        </div>
    </section>

    {{--
    <section class="mission-content container-xl py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="fw-bold mb-3">
                    <span>VISION</span>
                </h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <p style="height: 320px"></p>
            </div>
        </div>
    </section>
    --}}
@endsection
