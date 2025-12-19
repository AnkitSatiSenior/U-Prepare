@extends('public.layout.base')

@section('page_title'){{ __('Preventing and Managing Forest and General Fires') }}@endsection

@section('header_styles')
    <style>
        .fs-16r {
            font-size: 1.6rem;
        }

        img.rounded {
            border-radius: 15px !important;
        }
    </style>
@endsection

@section('page_content')
    <section class="container-xxl py-5">
        <div class="row">
            <div class="col-lg-6">
                <h1 class="text-dark fs-16r fw-bold text-uppercase">Preventing and Managing Forest and General Fires</h1>
            </div>
            <div class="col-lg-6"></div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <p class="mb-0 text-justify">
                    Focuses on enhancing capacities for preventing and managing forest and general fires. Strategies involve conducting fire risk assessments,
                    developing state-level fire management plans, upgrading firefighting capabilities with advanced technologies, and implementing
                    community-based fire prevention initiatives. Additionally, efforts target forest fire prevention and management, emphasizing early
                    detection, policy assessments, equipment procurement, and establishing incentive-based programs for sustainable forest management.
                    These measures aim to mitigate fire risks, promote biodiversity, and ensure long-term sustainability.
                </p>
            </div>
            <div class="col-lg-6">
                <img class="img-fluid rounded" src="{{ asset('assets/img/forest-fire.webp') }}" >
            </div>
        </div>
    </section>
@endsection
