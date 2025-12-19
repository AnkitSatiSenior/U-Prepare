@extends('public.layout.base')

@section('page_title'){!! request()->cookie('lang') === 'hi' ? $announcement->hin_title : $announcement->eng_title    !!}@endsection

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
           {!! request()->cookie('lang') === 'hi' ? $announcement->hin_content : $announcement->eng_content !!}
            {{-- <div class="row">
                <div class="col-12">
                    <div class="bg-theme py-2 px-3">
                        <h3 class="m-0 text-white h4">Tenders & Notices</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Tender Description</th>
                                    <th>Upload Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>01.</td>
                                    <td>Community-Based Forest Management and Carbon Credit Development</td>
                                    <td>20<sup>th</sup> Aug. 2024</td>
                                    <td>
                                        <a href="{{ asset('assets/docs/525-RFI.pdf') }}" class="text-decoration-underline" target="_blank">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>02.</td>
                                    <td>Request for Information</td>
                                    <td>21<sup>st</sup> Aug. 2024</td>
                                    <td>
                                        <a href="{{ asset('assets/docs/RFI_Community-Based-Forest-Fire-Risk-Management-and-Carbon-Finance.pdf') }}" class="text-decoration-underline" target="_blank">View</a>
                                    </td>
                                </tr>
				<tr>
				    <td>03.</td>
				    <td>Notice: Request for Quotation of Office Stationery</td>
				    <td>20<sup>th</sup> Sep. 2024</td>
				    <td>
					<a href="{{ asset('assets/docs/RFQ_Office-Stationery.pdf') }}" class="text-decoration-underline" target="_blank">View</a>
				    </td>
				</tr>
				<tr>
				    <td>04.</td>
				    <td>Request of Quotation for Procurement of Office Stationery for U-PREPARE Offices</td>
				    <td>20<sup>th</sup> Sep. 2024</td>
				    <td>
					<a href="{{ asset('assets/docs/RFQ_office-stationery-for-u_prepare-offices.pdf') }}" class="text-decoration-underline" target="_blank">View</a>
				    </td>
				</tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> --}}
        </div>
    </section>
@endsection
