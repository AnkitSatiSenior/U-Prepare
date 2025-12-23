<x-guest-layout>
     @section('page_title', 'Grievance Status')
    <style>
        .head h1 { font-size: 1.8rem; }
        .head+hr { border: 2px solid var(--color-tblue); opacity: 1; }
        th { font-size: 0.85em; background: #95dfe6 !important; }
        .bsb-timeline-1 { --bsb-tl-color: #cfe3ff; --bsb-tl-circle-size: 18px; --bsb-tl-circle-color: #0d6ef6; --bsb-tl-circle-offset: 9px; }
        .bsb-timeline-1 .timeline { margin: 0; padding: 0; list-style: none; position: relative; }
        .bsb-timeline-1 .timeline::after { top: 0; left: 0; bottom: 0; width: 2px; margin-left: -1px; content: ""; position: absolute; background-color: var(--bsb-tl-color); }
        .bsb-timeline-1 .timeline-item { margin: 0; padding: 0; position: relative; }
        .bsb-timeline-1 .timeline-item::before { top: 0; left: calc(var(--bsb-tl-circle-offset)*-1); width: var(--bsb-tl-circle-size); height: var(--bsb-tl-circle-size); border-radius: 50%; background-color: var(--bsb-tl-circle-color); content: ""; position: absolute; z-index: 1; }
        .bsb-timeline-1 .timeline-content { padding: 0 0 2.5rem 2.5rem; }
        .bsb-timeline-1 .timeline-item:last-child .timeline-content { padding-bottom: 0; }
    </style>

    <section class="grievance-status pt-5">
        <div class="head container-xxl">
            <h1 class="text-uppercase fw-bold text-dark m-0">Grievance Status</h1>
        </div>
        <hr class="my-2" />

        <div class="container-xl pt-3">

            {{-- Success after redirect --}}
            @if(session('success'))
                <div class="alert alert-success">{!! session('success') !!}</div>
            @endif

            {{-- Search Form --}}
            <form method="POST" action="{{ route('grievances.status.check') }}">
                @csrf
                <div class="row align-items-center mb-4">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-2">
                        <label class="fw-bold text-dark m-0">Grievance No.</label>
                        <p class="small text-muted m-0">(शिकायत क्रमांक)</p>
                    </div>
                    <div class="col-lg-4">
                        <input type="text" name="grievance_no"
                               value="{{ old('grievance_no') }}"
                               class="form-control @error('grievance_no') is-invalid @enderror"
                               placeholder="e.g. GRV-XXXXXX" />
                        @error('grievance_no')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-4 text-start text-lg-center mt-3 mt-lg-0">
                        <button class="btn btn-primary px-5">Check Status</button>
                    </div>
                </div>
            </form>

            {{-- If grievance exists --}}
            @isset($grievance)
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Grievance No.</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>District</th>
                                    <th>Project</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $grievance->grievance_no }}</td>
                                    <td>{{ $grievance->full_name ?? '—' }}</td>
                                    <td>{{ $grievance->mobile ?? '—' }}</td>
                                    <td>{{ $grievance->district ?? '—' }}</td>
                                    <td>{{ $grievance->project ?? '—' }}</td>
                                    <td>{{ $grievance->department->name ?? '—' }}</td>
                                    <td><span class="badge bg-info">{{ ucfirst($grievance->status) }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Timeline --}}
                @if($grievance->logs->count())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light fw-bold">Grievance Timeline</div>
                            <div class="card-body">
                                <section class="bsb-timeline-1 py-4">
                                    <ul class="timeline">
                                        @foreach($grievance->logs as $log)
                                            <li class="timeline-item border-0">
                                                <div class="timeline-body">
                                                    <div class="timeline-content">
                                                        <div class="card border-0">
                                                            <div class="card-body p-0">
                                                                <h6 class="text-secondary mb-1">
                                                                    {{ $log->created_at->format('d M, Y H:i A') }}
                                                                    @if($log->type && $grievance->action)
                                                                        <a href="{{ asset($log->type == 'pact' ? $grievance->action->pact_doc : $grievance->action->fact_doc) }}"
                                                                           class="btn btn-sm btn-info ms-3"
                                                                           target="_blank">View Report</a>
                                                                    @endif
                                                                </h6>
                                                                <h5 class="fw-bold mb-2">{{ $log->title }}</h5>
                                                                <p class="m-0">{{ $log->remark }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endisset

        </div>
    </section>
</x-guest-layout>
