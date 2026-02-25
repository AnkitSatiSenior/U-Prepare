<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-file-contract text-primary"
            title="Edit Procurement Details"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
                ['route' => 'admin.package-projects.show', 'params' => $procurementDetail->package_project_id, 'label' => 'Package #' . $procurementDetail->package_project_id],
                ['label' => 'Edit Procurement']
            ]"
        />

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="fas fa-edit me-2"></i> Edit Procurement Details
                </h5>
                <p class="small text-muted mt-1 mb-0">
                    For Package: #{{ $procurementDetail->package_project_id }}
                </p>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.procurement-details.update', $procurementDetail) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">General Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="method_of_procurement" class="form-label fw-semibold">Method of Procurement <span class="text-danger">*</span></label>
                            <select class="form-select @error('method_of_procurement') is-invalid @enderror" 
                                    id="method_of_procurement" name="method_of_procurement" required>
                                <option value="">Select Method</option>
                                @foreach($methodsOfProcurement as $method)
                                    <option value="{{ $method }}" {{ old('method_of_procurement', $procurementDetail->method_of_procurement) == $method ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
                            </select>
                            @error('method_of_procurement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="type_of_procurement_id" class="form-label fw-semibold">Type of Procurement <span class="text-danger">*</span></label>
                            <select class="form-select @error('type_of_procurement_id') is-invalid @enderror" 
                                    id="type_of_procurement_id" name="type_of_procurement_id" required>
                                <option value="">Select Type</option>
                                @foreach($typesOfProcurement as $type)
                                    <option value="{{ $type->id }}" 
                                        {{ old('type_of_procurement_id', $procurementDetail->type_of_procurement_id) == $type->id ? 'selected' : '' }}>
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
                                   id="tender_fee" name="tender_fee" 
                                   value="{{ old('tender_fee', $procurementDetail->tender_fee) }}" placeholder="0.00">
                            @error('tender_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="earnest_money_deposit" class="form-label fw-semibold">EMD Amount (₹)</label>
                            <input type="number" step="0.01" class="form-control @error('earnest_money_deposit') is-invalid @enderror" 
                                   id="earnest_money_deposit" name="earnest_money_deposit" 
                                   value="{{ old('earnest_money_deposit', $procurementDetail->earnest_money_deposit) }}" placeholder="0.00">
                            @error('earnest_money_deposit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="bid_validity_days" class="form-label fw-semibold">Bid Validity (Days)</label>
                            <input type="number" class="form-control @error('bid_validity_days') is-invalid @enderror" 
                                   id="bid_validity_days" name="bid_validity_days" 
                                   value="{{ old('bid_validity_days', $procurementDetail->bid_validity_days) }}" placeholder="e.g. 90">
                            @error('bid_validity_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="emd_validity_days" class="form-label fw-semibold">EMD Validity (Days)</label>
                            <input type="number" class="form-control @error('emd_validity_days') is-invalid @enderror" 
                                   id="emd_validity_days" name="emd_validity_days" 
                                   value="{{ old('emd_validity_days', $procurementDetail->emd_validity_days) }}" placeholder="e.g. 120">
                            @error('emd_validity_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">Procurement Milestones (Dates & Documents)</h6>
                    
                    @php
                        // Helper array to generate rows dynamically keeping code DRY
                        $milestones = [
                            ['label' => 'Publication', 'date_field' => 'publication_date', 'doc_field' => 'publication_document', 'path_field' => 'publication_document_path'],
                            ['label' => 'Technical Evaluation', 'date_field' => 'technical_eval_date', 'doc_field' => 'technical_eval_document', 'path_field' => 'technical_eval_document_path'],
                            ['label' => 'Financial Evaluation', 'date_field' => 'financial_eval_date', 'doc_field' => 'financial_eval_document', 'path_field' => 'financial_eval_document_path'],
                            ['label' => 'LOA Issued', 'date_field' => 'loa_issued_date', 'doc_field' => 'loa_issued_document', 'path_field' => 'loa_issued_document_path'],
                        ];
                    @endphp

                    @foreach($milestones as $milestone)
                        <div class="row align-items-center mb-3 bg-light p-3 rounded">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label for="{{ $milestone['date_field'] }}" class="form-label fw-semibold">{{ $milestone['label'] }} Date</label>
                                <input type="date" class="form-control @error($milestone['date_field']) is-invalid @enderror" 
                                       id="{{ $milestone['date_field'] }}" name="{{ $milestone['date_field'] }}" 
                                       value="{{ old($milestone['date_field'], $procurementDetail->{$milestone['date_field']} ? $procurementDetail->{$milestone['date_field']}->format('Y-m-d') : '') }}">
                                @error($milestone['date_field'])
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="{{ $milestone['doc_field'] }}" class="form-label fw-semibold">{{ $milestone['label'] }} Document</label>
                                
                                @if($procurementDetail->{$milestone['path_field']})
                                    <div class="d-flex align-items-center mb-2">
                                        <a href="{{ Storage::url($procurementDetail->{$milestone['path_field']}) }}" 
                                           target="_blank" class="btn btn-sm btn-outline-info me-3">
                                            <i class="fas fa-eye me-1"></i> View Current
                                        </a>
                                        <span class="small text-muted">Upload a new file below to replace it.</span>
                                    </div>
                                @endif
                                
                                <input type="file" class="form-control @error($milestone['doc_field']) is-invalid @enderror" 
                                       id="{{ $milestone['doc_field'] }}" name="{{ $milestone['doc_field'] }}">
                                
                                @if(!$procurementDetail->{$milestone['path_field']})
                                    <div class="form-text small text-muted">Accepted formats: PDF, DOC, DOCX (Max: 10MB)</div>
                                @endif

                                @error($milestone['doc_field'])
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-end border-top pt-4 mt-4">
                        <a href="{{ route('admin.procurement-details.index') }}" class="btn btn-light me-2 px-4">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Update Procurement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>