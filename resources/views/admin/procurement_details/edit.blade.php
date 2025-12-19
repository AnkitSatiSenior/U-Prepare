<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
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


        <!-- Main Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-edit me-2"></i> Edit Procurement Details
                </h5>
                <div class="small text-muted mt-1">
                    For Package: #{{ $procurementDetail->package_project_id }}
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.procurement-details.update', $procurementDetail) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="method_of_procurement" class="form-label">Method of Procurement *</label>
                            <select class="form-control @error('method_of_procurement') is-invalid @enderror" 
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
    <label for="type_of_procurement_id" class="form-label">Type of Procurement *</label>
    <select class="form-control @error('type_of_procurement_id') is-invalid @enderror" 
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

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="publication_date" class="form-label">Publication Date</label>
                            <input type="date" class="form-control @error('publication_date') is-invalid @enderror" 
                                   id="publication_date" name="publication_date" 
                                   value="{{ old('publication_date', $procurementDetail->publication_date ? $procurementDetail->publication_date->format('Y-m-d') : '') }}">
                            @error('publication_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="tender_fee" class="form-label">Tender Fee (₹)</label>
                            <input type="number" step="0.01" class="form-control @error('tender_fee') is-invalid @enderror" 
                                   id="tender_fee" name="tender_fee" 
                                   value="{{ old('tender_fee', $procurementDetail->tender_fee) }}">
                            @error('tender_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="earnest_money_deposit" class="form-label">EMD (₹)</label>
                            <input type="number" step="0.01" class="form-control @error('earnest_money_deposit') is-invalid @enderror" 
                                   id="earnest_money_deposit" name="earnest_money_deposit" 
                                   value="{{ old('earnest_money_deposit', $procurementDetail->earnest_money_deposit) }}">
                            @error('earnest_money_deposit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="bid_validity_days" class="form-label">Bid Validity (Days)</label>
                            <input type="number" class="form-control @error('bid_validity_days') is-invalid @enderror" 
                                   id="bid_validity_days" name="bid_validity_days" 
                                   value="{{ old('bid_validity_days', $procurementDetail->bid_validity_days) }}">
                            @error('bid_validity_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="emd_validity_days" class="form-label">EMD Validity (Days)</label>
                            <input type="number" class="form-control @error('emd_validity_days') is-invalid @enderror" 
                                   id="emd_validity_days" name="emd_validity_days" 
                                   value="{{ old('emd_validity_days', $procurementDetail->emd_validity_days) }}">
                            @error('emd_validity_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="publication_document" class="form-label">Publication Document</label>
                        @if($procurementDetail->publication_document_path)
                            <div class="mb-2">
                                <a href="{{ Storage::url($procurementDetail->publication_document_path) }}" 
                                   target="_blank" class="btn btn-sm btn-outline-info me-2">
                                    <i class="fas fa-eye me-1"></i> View Current Document
                                </a>
                                <span class="small text-muted">Upload new file to replace</span>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('publication_document') is-invalid @enderror" 
                               id="publication_document" name="publication_document">
                        <div class="form-text">Accepted formats: PDF, DOC, DOCX (Max: 2MB)</div>
                        @error('publication_document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end border-top pt-4">
                        <a href="{{ route('admin.procurement-details.show', $procurementDetail) }}" class="btn btn-light me-3">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Procurement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>