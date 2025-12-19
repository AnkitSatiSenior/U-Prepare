@foreach (['dec', 'hpc'] as $approval)
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-check-circle me-2 text-primary"></i>
                    {{ strtoupper($approval) }} Approval
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="{{ $approval }}_approved" value="0">
                            <input class="form-check-input" type="checkbox" name="{{ $approval }}_approved"
                                id="{{ $approval }}_approved" value="1" @checked(old("{$approval}_approved", $packageProject->{$approval . '_approved'} ?? false))>
                            <label class="form-check-label" for="{{ $approval }}_approved">Approved</label>
                        </div>
                    </div>

                    <div class="col-md-5 mb-3">
                        <label for="{{ $approval }}_approval_date" class="form-label">Approval Date</label>
                        <input type="date" class="form-control" id="{{ $approval }}_approval_date"
                            name="{{ $approval }}_approval_date"
                            value="{{ old("{$approval}_approval_date", optional($packageProject->{$approval . '_approval_date'})->format('Y-m-d') ?? '') }}">
                    </div>

                    <div class="col-md-5 mb-3">
                        <label for="{{ $approval }}_letter_number" class="form-label">Letter Number</label>
                        <input type="text" class="form-control" id="{{ $approval }}_letter_number"
                            name="{{ $approval }}_letter_number"
                            value="{{ old("{$approval}_letter_number", $packageProject->{$approval . '_letter_number'} ?? '') }}">
                    </div>

                    <div class="offset-2 col-md-10 mb-3">
                        <label for="{{ $approval }}_document_path" class="form-label">Approval Document
                            (PDF)</label>
                        <input type="file" class="form-control" id="{{ $approval }}_document_path"
                            name="{{ $approval }}_document_path" accept=".pdf">

                        @if (isset($packageProject) && $packageProject->{$approval . '_document_path'})
                            <div class="mt-2">
                                <a href="{{ Storage::url($packageProject->{$approval . '_document_path'}) }}"
                                    target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-pdf me-1"></i> View Current Document
                                </a>
                            </div>
                        @endif
                        <small class="text-muted">Max 2MB PDF file</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
