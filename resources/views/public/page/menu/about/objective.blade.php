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
            max-width: 60%;
            padding-left: 60px;
        }

        .hero-img > .caption h1 {
            color: white;
            font-size: 2.2rem;
            text-transform: uppercase;
        }

        .objectives-content .head > p {
            font-size: 0.8rem;
        }

        .objectives-content .head > p span:first-child {
            color: var(--color-tblue);
        }

        .objectives-content .head h2 {
            font-size: 1.8rem;
        }

        .objectives-content ul li {
            display: flex;
            font-size: 1.2rem;
            align-items: center;
        }

        .objectives-content ul li > img {
            width: 16px;
        }

        .objectives-content .img-box img {
            height: 320px;
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
            <img class="w-100 h-100" src="{{ asset('assets/img/hero/objectives.webp') }}" alt="Objectives">
            <div class="overlay"></div>
            <div class="caption">
                <h1 class="fw-bold">Resilience in Action</h1>
                <p class="mb-0 text-white">Dedicated to empower communities with innovative disaster readiness and recovery strategies. we aim to create afuture where every community can withstand quickly recover from disasters, fostering a culture of preparedness and resilience.</p>
            </div>
        </div>
    </section>

    <section class="objectives-content container-xl py-5">
        <div class="row mb-4">
            <div class="col-12 head text-center">
                <p class="fw-bold mb-1">
                    <span>Resilient Uttarakhand,</span>
                    <span class="text-dark">Safe Uttarakhand</span>
                </p>
                <h2 class="text-dark fw-bold mb-3">OBJECTIVES</h2>
            </div>
        </div>

        {{--
        <div class="row">
            <div class="col-lg-5">
                <p class="mb-0 text-justify">
                    The project in Uttarakhand aims to significantly bolster the state's
                    resilience to climate-related and natural disasters. The primary
                    objectives include enhancing the resilience of critical public
                    infrastructure, such as roads, bridges, and schools, to withstand the
                    impacts of climate change and natural disasters. This involves
                    retrofitting and upgrading existing structures as well as incorporating
                    resilient designs in new constructions. The project also seeks to
                    strengthen disaster risk management (DRM) capacity by improving
                    early warning systems, emergency response capabilities, and disaster
                    preparedness among local communities and authorities.
                </p>
            </div>
            <div class="col-lg-2 d-flex align-items-center justify-content-center">
                <p class="mb-0 ver-bar"></p>
            </div>
            <div class="col-lg-5">
                <p class="mb-0 text-justify">
                    Furthermore, it emphasizes improving forest and fire management
                    practices to reduce the risk and impact of wildfires, which are
                    becoming increasingly frequent and severe due to climate change. By
                    focusing on these areas, the project aims to foster inclusive growth
                    and community resilience, ensuring that all segments of society,
                    particularly vulnerable groups, benefit from enhanced safety and
                    sustainable development practices. Overall, the project aspires to
                    create a more resilient and adaptive Uttarakhand, capable of
                    effectively managing and mitigating the risks associated with climate
                    change and natural disasters.
                </p>
            </div>
        </div>
        --}}

        <div class="row">
            <div class="col-lg-7">
                <ul class="h-100 list-unstyled d-flex flex-column justify-content-between">
                    <li>
                        <img class="me-2" src="{{ asset('assets/img/icons/right-arrow-short.png') }}" >
                        <div>To prioritize climate and disaster-resilient infrastructure and include resilience in infrastructure development.</div>
                    </li>
                    <li>
                        <img class="me-2" src="{{ asset('assets/img/icons/right-arrow-short.png') }}" >
                        <div>To strengthen the ability of Uttarakhand's government institutions and first responders to forecast, plan for, and respond to catastrophes.</div>
                    </li>
                    <li>
                        <img class="me-2" src="{{ asset('assets/img/icons/right-arrow-short.png') }}" >
                        <div>To improve the Uttarakhand government and local communities' ability to prevent and control forest and general fires.</div>
                    </li>
                </ul>
            </div>
            <div class="col-lg-5">
                <div class="img-box text-end">
                    <img class="img-fluid" src="{{ asset('assets/img/objectives.webp') }}">
                </div>
            </div>
        </div>
    </section>
@endsection
