<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-2 bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="text-secondary mb-0 h4">
                    <i class="fas fa-tasks me-2"></i>
                    Work Programs
                </h6>
                <div class="d-flex align-items-center gap-2">
                    @php
                        // 🚨 ARCHITECTURE WARNING: Business logic in view.
                        // The controller should find the correct program documents and pass a simple $headerDocuments array to the view.
                        $documentProgram = $workPrograms->first(function ($program) {
                            return !empty($program->procurement_bid_document) || !empty($program->pre_bid_minutes_document);
                        });
                    @endphp

                    @if ($documentProgram)
                        @if (!empty($documentProgram->procurement_bid_document))
                            @php
                                // S3 Resolution (Use temporaryUrl if the bucket is strictly private)
                                $bidDocUrl = \Illuminate\Support\Facades\Storage::disk('s3')->url($documentProgram->procurement_bid_document);
                            @endphp
                            <a href="{{ $bidDocUrl }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-file-pdf me-2"></i> Bid Document
                            </a>
                        @endif

                        @if (!empty($documentProgram->pre_bid_minutes_document))
                            @php
                                $preBidUrl = \Illuminate\Support\Facades\Storage::disk('s3')->url($documentProgram->pre_bid_minutes_document);
                            @endphp
                            <a href="{{ $preBidUrl }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-alt me-2"></i> Pre-Bid Minutes
                            </a>
                        @endif
                    @else
                        {{-- Fixed Invalid HTML: Was previously an <li> tag without a <ul> parent --}}
                        <span class="text-muted fst-italic">No documents uploaded.</span>
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
                            @php
                                // 🚨 ARCHITECTURE WARNING: State mutation in view.
                                // Date pipelines (Start + Days = Planned) should be calculated in a 
                                // Domain Service or API Resource, mapping to a DTO before reaching the view.
                                $firstProgram = $workPrograms->first();
                                $currentDate = $firstProgram && !empty($firstProgram->start_date)
                                    ? \Carbon\Carbon::parse($firstProgram->start_date)
                                    : \Carbon\Carbon::now();
                            @endphp

                            @foreach ($workPrograms as $i => $program)
                                @php
                                    $startDate = $currentDate->copy();
                                    $daysToAdd = (int) ($program->days ?? 0);
                                    $plannedDate = $startDate->copy()->addDays($daysToAdd);
                                    
                                    // Mutate tracker for the next iteration
                                    $currentDate = $plannedDate->copy();
                                @endphp

                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $program->name_work_program }}</td>
                                    <td>{{ $program->weightage }}%</td>
                                    <td>{{ $program->days ?? 0 }}</td>
                                    <td>{{ $startDate->format('d M Y') }}</td>
                                    <td>{{ $plannedDate->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>