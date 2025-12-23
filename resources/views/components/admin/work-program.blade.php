<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-2 bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="text-secondary mb-0 h4">
                    <i class="fas fa-tasks me-2"></i>
                    Work Programs
                </h6>
                <div class="d-flex">
                    @php $found = false; @endphp

                    @foreach ($workPrograms as $program)
                        @if ($program->procurement_bid_document || $program->pre_bid_minutes_document)
                            @if ($program->procurement_bid_document)
                                <span class="mb-2">
                                    <a href="{{ asset('storage/' . $program->procurement_bid_document) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-file-pdf me-2"></i> Bid Document
                                    </a>
                                </span>
                                @php $found = true; @endphp
                            @endif

                            @if ($program->pre_bid_minutes_document)
                                <span class="mb-2">
                                    <a href="{{ asset('storage/' . $program->pre_bid_minutes_document) }}"
                                        target="_blank" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-alt me-2"></i> Pre-Bid Minutes
                                    </a>
                                </span>
                                @php $found = true; @endphp
                            @endif
                            @break

                            {{-- stop after first program that has either/both --}}
                        @endif
                    @endforeach

                    @if (!$found)
                        <li class="text-muted fst-italic">No documents uploaded.</li>
                    @endif
                </div>
            </div>

            <div class="card-body py-0">
                @if ($workPrograms->isEmpty())
                    <div class="alert alert-info mb-0">
                        No work programs found for this Package & Procurement Detail.
                    </div>
                @else
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Weightage (%)</th>
                                <th>Days</th>
                                <th>Start Date</th>
                                <th>Planned Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workPrograms as $i => $program)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $program->name_work_program }}</td>
                                    <td>{{ $program->weightage }}%</td>
                                    <td>{{ $program->days ?? 'N/A' }}</td>
                                    <td>
                                        {{ $program->start_date ? \Carbon\Carbon::parse($program->start_date)->format('d M Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $program->planned_date ? \Carbon\Carbon::parse($program->planned_date)->format('d M Y') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>   
                @endif
            </div>
        </div>
    </div>
</div>
