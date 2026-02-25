<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" title="Create Procurement Details"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
                [
                    'route' => 'admin.package-projects.show',
                    'params' => $packageProject,
                    'label' => 'Package #' . $packageProject->id,
                ],
                ['label' => 'Create Procurement'],
            ]" />

        <div class="row mb-3">
            <div class="col-md-8 mb-3 mb-md-0">
                <x-admin.package-card :packageProject="$packageProject" />
            </div>
            <div class="col-md-4">
                <x-admin.approval-details :packageProject="$packageProject" />
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 border-start border-danger border-4">
                <strong><i class="fas fa-exclamation-circle me-1"></i> Whoops!</strong> Please fix the errors below:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white pt-3 pb-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-plus-circle me-2"></i> Create Procurement Details
                </h5>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('admin.procurement-details.store', $packageProject) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">General Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="method_of_procurement" class="form-label fw-semibold">Method of Procurement <span class="text-danger">*</span></label>
                            <select class="form-select @error('method_of_procurement') is-invalid @enderror"
                                id="method_of_procurement" name="method_of_procurement" required>
                                <option value="">Select Method</option>
                                @foreach ($methodsOfProcurement as $method)
                                    <option value="{{ $method }}" {{ old('method_of_procurement') == $method ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
                            </select>
                            @error('method_of_procurement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="type_of_procurement_id" class="form-label fw-semibold">Type of Package <span class="text-danger">*</span></label>
                            <select class="form-select @error('type_of_procurement_id') is-invalid @enderror"
                                id="type_of_procurement_id" name="type_of_procurement_id" required>
                                <option value="">Select Type</option>
                                @foreach($typesOfProcurement as $type)
                                    <option value="{{ $type->id }}" {{ old('type_of_procurement_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type_of_procurement_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">Financials & Validities</h6>
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="tender_fee" class="form-label fw-semibold">Tender Fee (₹)</label>
                            <input type="number" step="0.01" class="form-control @error('tender_fee') is-invalid @enderror" 
                                   id="tender_fee" name="tender_fee" value="{{ old('tender_fee') }}" placeholder="0.00">
                            @error('tender_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="earnest_money_deposit" class="form-label fw-semibold">EMD Value (₹)</label>
                            <input type="number" step="0.01" class="form-control @error('earnest_money_deposit') is-invalid @enderror"
                                   id="earnest_money_deposit" name="earnest_money_deposit" value="{{ old('earnest_money_deposit') }}" placeholder="0.00">
                            @error('earnest_money_deposit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="bid_validity_days" class="form-label fw-semibold">Bid Validity <small class="text-muted">(Days)</small></label>
                            <input type="number" class="form-control @error('bid_validity_days') is-invalid @enderror"
                                   id="bid_validity_days" name="bid_validity_days" value="{{ old('bid_validity_days') }}" placeholder="e.g. 90">
                            @error('bid_validity_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="emd_validity_days" class="form-label fw-semibold">EMD Validity <small class="text-muted">(Days)</small></label>
                            <input type="number" class="form-control @error('emd_validity_days') is-invalid @enderror"
                                   id="emd_validity_days" name="emd_validity_days" value="{{ old('emd_validity_days') }}" placeholder="e.g. 120">
                            @error('emd_validity_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">Procurement Milestones (Dates & Documents)</h6>
                    
                    @php
                        // Helper array to generate the new fields dynamically keeping code DRY
                        $milestones = [
                            ['label' => 'Publication', 'date_field' => 'publication_date', 'doc_field' => 'publication_document'],
                            ['label' => 'Technical Evaluation', 'date_field' => 'technical_eval_date', 'doc_field' => 'technical_eval_document'],
                            ['label' => 'Financial Evaluation', 'date_field' => 'financial_eval_date', 'doc_field' => 'financial_eval_document'],
                            ['label' => 'LOA Issued', 'date_field' => 'loa_issued_date', 'doc_field' => 'loa_issued_document'],
                        ];
                    @endphp

                    @foreach($milestones as $milestone)
                        <div class="row align-items-center mb-3 bg-light p-3 rounded border border-light">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label for="{{ $milestone['date_field'] }}" class="form-label fw-semibold">{{ $milestone['label'] }} Date</label>
                                <input type="date" class="form-control @error($milestone['date_field']) is-invalid @enderror" 
                                       id="{{ $milestone['date_field'] }}" name="{{ $milestone['date_field'] }}" 
                                       value="{{ old($milestone['date_field']) }}">
                                @error($milestone['date_field'])
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="{{ $milestone['doc_field'] }}" class="form-label fw-semibold">{{ $milestone['label'] }} Document</label>
                                <input type="file" class="form-control @error($milestone['doc_field']) is-invalid @enderror" 
                                       id="{{ $milestone['doc_field'] }}" name="{{ $milestone['doc_field'] }}">
                                <div class="form-text small text-muted">Accepted formats: PDF, DOC, DOCX (Max: 10MB)</div>
                                @error($milestone['doc_field'])
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-end border-top pt-4 mt-4">
                        <a href="{{ route('admin.package-projects.show', $packageProject) }}" class="btn btn-light me-3 px-4 shadow-sm border">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Save Procurement Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>