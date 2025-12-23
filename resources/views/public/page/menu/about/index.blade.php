@extends('public.layout.base')

@section('page_title'){{ __('About Us') }}@endsection

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

        .hero-img > h1 {
            color: white;
            bottom: 20%;
            position: absolute;
            font-size: 3.2rem;
            max-width: 64%;
            line-height: 1.5;
            padding-left: 60px;
            text-transform: uppercase;
        }

        .about-content h1,
        .about-content h6 {
            font-weight: bold;
            text-transform: uppercase;
        }

        .about-content h6 {
            font-size: 0.7em;
        }

        .about-content h1 span {
            color: var(--color-theme);
        }

        .about-content .rmbs::after,
        .about-content .rmbs::before {
            width: 5rem;
            margin: 0 1rem;
            content: "";
            display: inline-block;
            transform: translateY(-0.4rem);
            border-top: .12rem solid black;
        }
    </style>
@endsection

@section('page_content')
    <section class="container-fluid p-0">
        <div class="hero-img">
            <img class="w-100 h-100" src="{{ asset('assets/img/hero/about-u-prepare.webp') }}" alt="">
            <div class="overlay"></div>
            <h1 class="fw-bold m-0">Uttarakhand Disaster Preparedness And Resilience Project</h1>
        </div>
    </section>

    <section class="about-content container-fluid py-5">
        <div class="row">
            <div class="col-12 text-center">
                <h6>Driving Change in Disaster Management</h6>
                <h1 class="mb-3">
                    About <span>U</span>-Prepare
                </h1>

                <p class="text-justify">
                    The Uttarakhand Disaster Preparedness & Resilience Project (U-PREPARE) is a critical initiative to bolster disaster resilience and
                    preparedness in the state of Uttarakhand, typically supported by the World Bank. The project focuses on assessing and mitigating the
                    unique risks posed by natural disasters, which are prevalent in the region, including floods, landslides, earthquakes, and more. The project
                    typically involves a multi-faceted approach, including a thorough risk assessment to identify vulnerabilities and hazards specific to the
                    region. One significant aspect is the development of resilient infrastructure, capable of withstanding the forces of nature or minimizing
                    their adverse effects. Additionally, the implementation and improvement of early warning systems are crucial components, aiding in timely
                    alerts and coordinated responses during emergencies. Capacity building and policy advocacy are integral parts of the project, empowering
                    local authorities and communities to effectively manage disasters and advocate for policies prioritizing disaster resilience and
                    preparedness at various levels. The project will support the recovery in terms of River protection works, Road Protection works (Slopes),
                    Reconstruction of Bridges, and strengthening the State Disaster Response Force.
                </p>

                <span class="rmbs">
                    <a href="#" class="btn btn-theme">Read More</a>
                </span>
            </div>
        </div>
    </section>
@endsection
