@extends('public.layout.base')

@section('page_title'){{ __('Project Management') }}@endsection

@section('header_styles')
    <style>
        h1 {
            font-size: 1.8rem;
            text-transform: uppercase;
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
                <h1 class="text-dark fw-bold mb-3">Project Management</h1>
            </div>
            <div class="col-lg-6"></div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <p class="mb-0 text-justify">
                    Facilitate effective project management and fostering knowledge sharing. It ensures day-to-day coordination by supporting various aspects
                    like financial management, procurement, environmental and social management, communication, monitoring and evaluation, and
                    stakeholder engagement. Additionally, it aims to establish a Lighthouse Uttarakhand platform to disseminate lessons learned from the
                    project, enhance capacity building, and promote the exchange of knowledge and experiences with other states in India through institutional
                    partnerships.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/img/project-management.jpeg') }}" class="img-fluid rounded">
            </div>
        </div>
    </section>
@endsection
