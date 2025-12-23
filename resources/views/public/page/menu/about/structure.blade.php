@extends('public.layout.base')

@section('page_title'){{ __('Objective') }}@endsection

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

        .hero-img > .caption {
            bottom: 20%;
            position: absolute;
            max-width: 52%;
            padding-left: 60px;
        }

        .hero-img > .caption h1 {
            color: white;
            font-size: 1.8rem;
            text-transform: uppercase;
        }

        .structure-content .head > h2 span:first-child {
            color: var(--color-tblue);
        }

        .structure-content .head h2 {
            position: absolute;
            font-size: 1.8rem;
            max-width: 220px;
        }

        .ver-bar {
            width: 1px;
            border: 1px solid #4b4b4b;
            height: 70%;
        }
    </style>
@endsection

@section('page_content')
    <section class="container-fluid p-0">
        <div class="hero-img">
            <img class="w-100 h-100" src="{{ asset('assets/img/hero/organisation-structure.webp') }}" alt="">
            <div class="caption">
                <h1 class="fw-bold">PREPARING FOR TOMORROW</h1>
                <p class="mb-0 text-white">We are designing resilient frameworks for disaster preparedness and recovery, ensuring sustainable community safety.</p>
            </div>
        </div>
    </section>

    <section class="structure-content container-xl py-5">
        <div class="row mb-4">
            <div class="col-12 head text-center">
                <h2 class="text-dark fw-bold mb-3"><span>ORGANISATION</span> STRUCTURE</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center">
                <img src="{{ asset('assets/public/img/project-structure-hierarchy.webp') }}" />
            </div>
        </div>
    </section>
@endsection
