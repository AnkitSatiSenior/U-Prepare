@extends('public.layout.base')

@section('page_title'){{ __('Mission and Vision') }}@endsection

@section('header_styles')
    <style>
        .pl-38px {
            padding-left: 38px;
        }

        section.team h2 {
            font-size: 1.8rem;
            text-transform: uppercase;
        }

        figure.team-mem {
            width: 280px;
            height: 280px;
        }

        figure.team-mem > img {
            width: 100%;
            height: 100%;
            object-fit: fill;
        }

        figure.team-mem+.caption > h4 {
            font-size: 1.4rem;
        }

        figure.team-mem+.caption > h5 {
            color: var(--color-theme);
            font-size: 1.1rem;
        }
    </style>
@endsection

@section('page_content')
    <section class="team container-xl py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center fw-bold mb-3 text-dark">Meet Our Team</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h4 class="fw-bold pl-38px">Directorate</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 d-flex flex-column align-items-center justify-content-center">
                <figure class="team-mem mb-2">
                    <img src="{{ asset('assets/public/img/cm_shri-pushkar-singh-dhami.webp') }}">
                </figure>
                <div class="caption text-center">
                    <h4 class="fw-bold">Shri Pushkar Singh Dhami</h4>
                    <h5 class="fw-bold">(Hon'ble Chief Minister, Uttarakhand)</h5>
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column align-items-center justify-content-center">
                <figure class="team-mem mb-2">
                    <img src="{{ asset('assets/public/img/chief-secretary_smt-radha-raturi.webp') }}">
                </figure>
                <div class="caption text-center">
                    <h4 class="fw-bold">Smt. Radha Raturi</h4>
                    <h5 class="fw-bold">(Chief Secretary)</h5>
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column align-items-center justify-content-center">
                <figure class="team-mem mb-2">
                    <img src="{{ asset('assets/public/img/secretary-dm_shri-vinod-kumar-suman.webp') }}">
                </figure>
                <div class="caption text-center">
                    <h4 class="fw-bold">Shri Vinod Kumar Suman</h4>
                    <h5 class="fw-bold">(Secretary, Disaster Management)</h5>
                </div>
            </div>
        </div>
    </section>
@endsection
