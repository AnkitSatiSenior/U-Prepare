@extends('public.layout.base')

@section('page_title'){{ __('Tenders & Notices') }}@endsection

@section('header_styles')
    <style>
        .bg-theme {
            background: var(--color-tblue);
        }
        table tr td:last-child,
        table thead th:last-child {
            text-align: center;
        }
        table > thead th {
            background-color: #f2f2f2 !important;
        }
    </style>
@endsection

@section('page_content')
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bg-theme py-2 px-3">
                        <h3 class="m-0 text-white h4">Tenders & Notices</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    {{-- <th>Tender No.</th> --}}
                                    <th>Tender Description</th>
                                    <th>Upload Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>01.</td>
                                    {{-- <td></td> --}}
                                    <td>Community-Based Forest Management and Carbon Credit Development</td>
                                    <td>20<sup>th</sup> Aug. 2024</td>
                                    <td>
                                        <a href="{{ asset('assets/docs/525-RFI.pdf') }}" class="text-decoration-underline" target="_blank">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>02.</td>
                                    {{-- <td></td> --}}
                                    <td>Request for Information</td>
                                    <td>21<sup>st</sup> Aug. 2024</td>
                                    <td>
                                        <a href="{{ asset('assets/docs/RFI.pdf') }}" class="text-decoration-underline" target="_blank">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>03.</td>
                                    <!--td></td-->
                                    <td>Request for Expression of Interest for Selection of Individual Design Consultant for Bridges</td>
                                    <td>25<sup>th</sup> Feb. 2025</td>
                                    <td>
                                        <a href="{{ asset('assets/docs/REOI_Doc.pdf') }}" class="text-decoration-underline" target="_blank">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>04.</td>
                                    <td>
                                        <div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <a href="{{ asset('assets/docs/2025/03/Short_Advertisment-PMU_&_PIU_Mar2025.pdf') }}" target="_blank">
                                                        <span style="color: #ff0000; font-size: 14pt; font-weight: bold;">Advertisement for contractual post under PMU &amp; PIU-RWD in Uttarakhand Disaster Preparedness and Resilience Project (U-PREPARE)-(World Bank Assisted) - Reference No. 1064/U-PREPARE/HR/2024/79 Dated 20/03/2025</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">(Last Date of submission of Application form - 10 April 2025 till 5:00 PM)</span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <br>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">• </span>
                                                    <a href="{{ asset('assets/docs/2025/03/Detailed_Advertisement_Mar2025.pdf') }}" target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Detail Advertisement (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">• </span>
                                                    <a href="{{ asset('assets/docs/2025/03/Application_Form_U-PREPARE_Mar2025.pdf ') }}" target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Application Form (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 12pt; font-style: italic; color: #000000; font-weight: bold;">• Job Description for post</span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">&nbsp; - </span>
                                                    <a href="{{ asset('assets/docs/2025/03/Job_Description-Social_community development_and_gender_specialist-PMU.pdf') }}" target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Social community development and gender specialist-PMU (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">&nbsp; - </span>
                                                    <a href="{{ asset('assets/docs/2025/03/Job_Description_Multi_Purpose_Worker-RWD_Mar2025.pdf ') }}" target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Multi Purpose Worker-RWD (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>20<sup>th</sup> Mar. 2025</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>05.</td>
                                    <td>
                                        <div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <a href="{{ asset('assets/docs/2025/03/Corrigendum.pdf') }}" target="_blank">
                                                        <span style="color: #ff0000; font-size: 14pt; font-weight: bold;">Corrigendum for Advertisement for contractual post under PMU & PIU's in Uttarakhand Disaster Preparedness and Resilience Project (U-PREPARE)-(World Bank Assisted) - Reference No. 1076/U-PREPARE/HR/2024/79 Dated 24/03/2025</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">(Last Date of submission of Application form - 04 April 2025 till 5:30 PM)</span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <br>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">• </span>
                                                    <a href="{{ asset('assets/docs/2025/03/corrigendum_detailed_821_Advertisement.pdf') }}" target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Detail Advertisement (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">• </span>
                                                    <a href="{{ asset('assets/docs/2025/03/corrigendum_Amended_Application_Form.pdf') }} " target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Application Form (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 12pt; font-style: italic; color: #000000; font-weight: bold;">• Job Description</span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">&nbsp; - </span>
                                                    <a href="{{ asset('assets/docs/2025/03/corrigendum_Job_Discripation_PMU_PIUs.pdf') }} " target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Multi Purpose Worker-PIU-RWD (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>26<sup>th</sup> March 2025</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>06.</td>
                                    <td>
                                        <div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <a href="{{ asset('assets/docs/2025/05/ad_multi-purpose-disaster-shelter-needs-assessment.pdf') }}" target="_blank">
                                                        <span style="color: #ff0000; font-size: 14pt; font-weight: bold;">Corrigendum for Selection of consulting firm for Multi Purpose Disaster Shelter Needs Assessment, Development Plan, Shelter Designs, and Operational Assistance in Uttarakhand State unde U-PREPARE Project (World Bank Assisted) - Reference No. 175/A-8/PIU-USDMA/U-PREPARE/2024-25 Dated 15/05/2025</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">(Last Date of submission of Application form - 20 June 2025)</span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <br>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">• </span>
                                                    <a href="{{ asset('assets/docs/2025/05/rfp_multi-purpose-disaster-shelter-needs-assessment.pdf') }}" target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Detail Advertisement (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>16<sup>th</sup> May 2025</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>07.</td>
                                    <td>
                                        <div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <a href="#">
                                                        <span style="color: #ff0000; font-size: 14pt; font-weight: bold;">You're invited to Pre-Proposal Meeting for Hiring of consultancy firm for development of Multi Purpose Disaster Shelters</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">Monday, 26 May 2025</span>
                                                <br>
                                                <span style="font-size: 13.3333px;">11:00am - 12:00pm (IST)</span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <br>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">• </span>
                                                    <a href="https://teams.live.com/meet/9347936227121?p=a8YGHhri3VvFMiGPMx" target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">https://teams.live.com/meet/9347936227121?p=a8YGHhri3VvFMiGPMx</span>
                                                    </a>
                                                    <br>
                                                    <span>Tap on the link or paste it in a browser to join.</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>25<sup>th</sup> May 2025</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>08.</td>
                                    <td>
                                        <div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <a href="{{ asset('assets/docs/2025/07/invitation_for_pre-proposal_conference-sample_demonstration_presentation_of_communication_equipment.pdf') }}" target="_blank">
                                                        <span style="color: #ff0000; font-size: 14pt; font-weight: bold;">Invitation for Demonstration of Communication Equipment for Forest Department in Uttarakhand Disaster Preparedness and Resilience Project (U-PREPARE)-(World Bank Assisted) - Reference No. 165/PIU-FOREST/SPECS/PMU/2025 Dated 01/07/2025</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">• </span>
                                                    <a href="{{ asset('assets/docs/2025/07/invitation_for_pre-proposal_conference-sample_demonstration_presentation_of_communication_equipment.pdf') }}" target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Detailed Advertisement (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <br>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 12pt; font-style: italic; color: #000000; font-weight: bold;">• Description</span>
                                            </div>
                                            <div>
                                                <span style="font-size: 13.3333px;">
                                                    <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">&nbsp; - </span>
                                                    <a href="{{ asset('assets/docs/2025/03/specification_for_pre-proposal_conference-sample_demonstration_presentation_of_communication_equipment.pdf') }} " target="_blank">
                                                        <span style="font-size: 12pt; font-style: italic; color: #0000ff; font-weight: bold;">Technical Specifications of Communication Equipments (Click here)</span>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>01<sup>st</sup> of July 2025</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
