@extends('public.layout.base')

@section('page_title'){{ __('Improving Emergency Preparedness and Response') }}@endsection

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
                <h1 class="text-dark fw-bold fs-16r text-uppercase">Improving Emergency Preparedness and Response</h1>
            </div>
            <div class="col-lg-6"></div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <p class="mb-0 text-justify">
                    The objective of this component is to improve the capabilities of government entities and first responders to predict, prepare for, and
                    respond to disasters. Involves establishing a State Emergency Operations Centre (SEOC) with a Centralized Incident Command System to
                    streamline coordination, reviewing and strengthening multi-agency institutional frameworks, and conducting community awareness
                    campaigns for disaster risk management (DRM). Additionally, efforts are directed towards improving hydromet and early warning systems by
                    enhancing forecast accuracy, establishing a comprehensive multi-hazard EWS for timely alerts, developing tailored hydromet tools for key
                    stakeholders, and conducting training sessions for DRM officials and communities. Strengthening the State Disaster Response Force (SDRF)
                    includes constructing outdoor training facilities, providing essential search and rescue equipment, and offering comprehensive training on
                    equipment handling and maintenance.
                </p>
            </div>
            <div class="col-lg-6">
                <img class="img-fluid rounded" src="{{ asset('assets/img/emergency-preparedness.webp') }}" >
            </div>
        </div>
    </section>
@endsection
