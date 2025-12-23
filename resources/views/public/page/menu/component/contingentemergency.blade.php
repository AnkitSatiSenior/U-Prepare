@extends('public.layout.base')

@section('page_title'){{ __('Contingent Emergency Response Component') }}@endsection

@section('header_styles')
    <style>
        h1 {
            font-size: 1.6rem;
            text-transform: uppercase;
        }
    </style>
@endsection

@section('page_content')
    <section class="container-xxl py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-dark fw-bold mb-3">Contingent Emergency Response Component</h1>

                <p class="mb-0 text-justify fw-bold">
                    Serves as a crucial mechanism designed to swiftly reallocate resources in response to eligible crises or emergencies. Its primary
                    objective is to provide immediate support by redirecting credit proceeds from other components as needed, ensuring a rapid and
                    effective response to unforeseen events or disasters.
                </p>
            </div>
        </div>
    </section>
@endsection
