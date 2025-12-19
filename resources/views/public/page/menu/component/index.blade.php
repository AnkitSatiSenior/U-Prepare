@extends('public.layout.base')

@if(request()->cookie('lang') === 'hi')
    @section('page_title'){{ $data->hin_title }}@endsection
@else
    @section('page_title'){{ $data->eng_title }}@endsection
@endif

@section('header_styles')
    <style>
        h1 {
            font-size: 1.8rem;
            text-transform: uppercase;
        }

        img.rounded {
            border-radius: 15px !important;
        }
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
    @if($data->banner)
        <section class="container-fluid p-0">
            <div class="hero-img">
                <img class="w-100 h-100" src="{{ asset($data->banner) }}" alt="{{ request()->cookie('lang') === 'hi' ? $data->banner_hin_title : $data->banner_eng_title }}">
                <div class="overlay"></div>
                <div class="caption">
                    <h1 class="fw-bold">{{ request()->cookie('lang') === 'hi' ? $data->banner_hin_title : $data->banner_eng_title }}</h1>
                    <p class="mb-0 text-white">{!! request()->cookie('lang') === 'hi' ? $data->banner_hin_description : $data->banner_eng_description !!}</p>
                </div>
            </div>
        </section>
    @endif

    <section class="container-xxl py-5">
        <div class="row">
            <div class="col-lg-6">
                <h1 class="text-dark fw-bold mb-3">{{ request()->cookie('lang') === 'hi' ? $data->page_hin_title : $data->page_eng_title }}</h1>
            </div>

            <div class="col-lg-6"></div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <p class="mb-0 text-justify">
                    {!! request()->cookie('lang') === 'hi' ? $data->hin_content : $data->eng_content !!}
                </p>
            </div>

            <div class="col-lg-6">
                <img  src="{{ asset($data->image) }}" class="img-fluid rounded">
            </div>
        </div>
    </section>
@endsection
