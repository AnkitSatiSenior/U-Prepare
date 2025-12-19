@extends('public.layout.base')

@section('page_title'){{ __('History') }}@endsection

@section('header_styles')
    <style>
        main h1 {
            font-size: 1.2rem;
            font-weight: bold;
            border-bottom: 6px solid var(--color-tblue);
            text-transform: uppercase;
        }

        main h1.af {
            font-size: 1.2rem;
        }

        figure {
            max-width: 94%;
            position: relative;
        }

        figure::after,
        figure::before {
            content: ' ';
            position: absolute;
            border-width: 4px;
            border-color: var(--color-tblue);
        }

        figure.right::before {
            right: 0;
            height: calc(100% + 20px);
            border-right-style: solid;
        }

        figure.right::after {
            width: calc(100% + 20px);
            bottom: 0;
            border-bottom-style: solid;
        }

        figure.left::before {
            left: 0;
            height: calc(100% + 20px);
            border-left-style: solid;
        }

        figure.left::after {
            width: calc(100% + 20px);
            bottom: 0;
            margin-left: -20px;
            border-bottom-style: solid;
        }

        figure > figcaption {
            width: 100%;
            bottom: -8%;
            position: absolute;
            font-size: 0.75em;
            text-align: center;
            font-weight: bold;
        }
    </style>
@endsection

@section('page_content')
    <section class="container-xxl">
        <div class="row align-items-end mb-5">
            <div class="col-lg-6">
                <h1 class="pb-2 mb-3">Uttarakhand Disaster Recovery Project (2014-2019)</h1>

                <p class="mb-0 text-justify">
                    The Uttarakhand Disaster Recovery Project (UDRP) was initiated in response to
                    the devastating floods and landslides that struck the Indian state of Uttarakhand
                    in June 2013. These natural calamities caused widespread destruction, resulting
                    in significant loss of life, property, and infrastructure. The UDRP, funded by the
                    World Bank and implemented by the Government of Uttarakhand, aimed to
                    restore and enhance the resilience of the affected regions. The project focused on
                    rebuilding critical infrastructure, improving disaster risk management systems,
                    and supporting the livelihoods of affected communities. It involved extensive
                    efforts in rebuilding roads, bridges, and public buildings, as well as implementing
                    measures to reduce the impact of future disasters through better planning and
                    preparedness.
                </p>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-5">
                <figure class="d-flex right m-0">
                    <img class="img-fluid" src="{{ asset('assets/public/img/history-01.webp') }}" alt="">
                    <figcaption>The agreement was signed by Onno Ruhl, Nilaya Mitash, and Rakesh Sharma.</figcaption>
                </figure>
            </div>
        </div>

        <div class="row align-items-end pt-5">
            <div class="col-lg-5 d-flex flex-column align-items-end">
                <figure class="d-flex left m-0">
                    <img class="img-fluid" src="{{ asset('assets/public/img/history-02.webp') }}" alt="">
                    <figcaption>The agreement was signed by Hisham Abdo, Sameer Kumar Khare, and Amit Negi.</figcaption>
                </figure>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-6">
                <h1 class="af pb-2 mb-3">UTTARAKHAND DISASTER RECOVERY PROJECT - AF (2019-2023)</h1>

                <p class="mb-0 text-justify">
                    The Uttarakhand Disaster Recovery Project - Additional Financing
                    (UDRP-AF) was approved in response to the need for continued
                    support and further enhancements following the initial phase of the
                    UDRP. This extension, funded again by the World Bank, was aimed at
                    scaling up the reconstruction and disaster resilience efforts initiated
                    after the catastrophic 2013 floods and landslides in Uttarakhand. The
                    additional financing focused on addressing ongoing challenges and
                    filling gaps in the recovery process, including the rehabilitation of
                    additional infrastructure, strengthening institutional capacities for
                    disaster risk management, and supporting long-term resilience
                    strategies. The UDRP-AF ensured sustained progress in rebuilding
                    efforts, improving community resilience, and enhancing the state's
                    overall capacity to manage and mitigate future disaster risks.
                </p>
            </div>
        </div>
    </section>
@endsection
