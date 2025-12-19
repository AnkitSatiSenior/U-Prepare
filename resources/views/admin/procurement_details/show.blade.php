<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
    icon="fas fa-file-contract text-primary"
    title="Procurement Details"
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
        ['label' => 'Admin'],
        ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
        ['route' => 'admin.package-projects.show', 'params' => $procurementDetail->package_project_id, 'label' => 'Package #' . $procurementDetail->package_project_id],
        ['label' => 'Procurement Details']
    ]"
/>


        <!-- Package Project Details Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ $procurementDetail->packageProject->package_name }} ({{ $procurementDetail->packageProject->package_number }})
                </h5>
                <div>
                    <a href="{{ route('admin.package-projects.edit', $procurementDetail->packageProject) }}" class="btn btn-sm btn-outline-primary me-2">
                        <i class="fas fa-edit me-1"></i> Edit Package
                    </a>
                    <a href="{{ route('admin.procurement-details.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Basic Information Section -->
                <div class="mb-5">
                    <h6 class="mb-3 text-muted border-bottom pb-2">
                        <i class="fas fa-info-circle me-2"></i> Basic Information
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Project</label>
                            <p class="form-control-static">
                                {{ $procurementDetail->packageProject->project->name ?? 'N/A' }}
                                @if($procurementDetail->packageProject->project)
                                    (Budget: ₹{{ number_format($procurementDetail->packageProject->project->budget, 2) }})
                                @endif
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Package Number</label>
                            <p class="form-control-static">{{ $procurementDetail->packageProject->package_number }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Estimated Budget</label>
                            <p class="form-control-static">₹{{ number_format($procurementDetail->packageProject->estimated_budget_incl_gst, 2) }}</p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Created At</label>
                            <p class="form-control-static">{{ $procurementDetail->packageProject->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Classification Section -->
                <div class="mb-5">
                    <h6 class="mb-3 text-muted border-bottom pb-2">
                        <i class="fas fa-tags me-2"></i> Classification
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Category</label>
                            <p class="form-control-static">{{ $procurementDetail->packageProject->category->name ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Sub Category</label>
                            <p class="form-control-static">{{ $procurementDetail->packageProject->subCategory->name ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Department</label>
                            <p class="form-control-static">{{ $procurementDetail->packageProject->department->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Location Information -->
                <div class="mb-5">
                    <h6 class="mb-3 text-muted border-bottom pb-2">
                        <i class="fas fa-map-marker-alt me-2"></i> Location Information
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">District</label>
                            <p class="form-control-static">{{ $procurementDetail->packageProject->district->name ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Block</label>
                            <p class="form-control-static">{{ $procurementDetail->packageProject->block->name ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Vidhan Sabha</label>
                            <p class="form-control-static">{{ $procurementDetail->packageProject->vidhanSabha->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Approval Status Section -->
                <div class="row">
                    <!-- DEC Approval -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle me-2 {{ $procurementDetail->packageProject->dec_approved ? 'text-success' : 'text-secondary' }}"></i> 
                                    DEC Approval Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Status</label>
                                    <p class="form-control-static">
                                        @if($procurementDetail->packageProject->dec_approved)
                                            <span class="badge bg-success text-white">Approved</span>
                                        @else
                                            <span class="badge bg-secondary text-white">Pending</span>
                                        @endif
                                    </p>
                                </div>
                                
                                @if($procurementDetail->packageProject->dec_approved)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Approval Date</label>
                                        <p class="form-control-static">{{ formatDate($procurementDetail->packageProject->dec_approval_date) ?? 'N/A' }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Letter Number</label>
                                        <p class="form-control-static">{{ $procurementDetail->packageProject->dec_letter_number ?? 'N/A' }}</p>
                                    </div>
                                    
                                    @if($procurementDetail->packageProject->dec_document_path)
                                        <div class="mb-0">
                                            <label class="form-label text-muted">Approval Document</label>
                                            <p class="form-control-static">
                                                <a href="{{ Storage::url($procurementDetail->packageProject->dec_document_path) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-file-pdf me-1"></i> View Document
                                                </a>
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- HPC Approval -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle me-2 {{ $procurementDetail->packageProject->hpc_approved ? 'text-success' : 'text-secondary' }}"></i> 
                                    HPC Approval Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Status</label>
                                    <p class="form-control-static">
                                        @if($procurementDetail->packageProject->hpc_approved)
                                            <span class="badge bg-success text-white">Approved</span>
                                        @else
                                            <span class="badge bg-secondary text-white">Pending</span>
                                        @endif
                                    </p>
                                </div>
                                
                                @if($procurementDetail->packageProject->hpc_approved)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Approval Date</label>
                                        <p class="form-control-static">{{ formatDate($procurementDetail->packageProject->hpc_approval_date) ?? 'N/A' }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Letter Number</label>
                                        <p class="form-control-static">{{ $procurementDetail->packageProject->hpc_letter_number ?? 'N/A' }}</p>
                                    </div>
                                    
                                    @if($procurementDetail->packageProject->hpc_document_path)
                                        <div class="mb-0">
                                            <label class="form-label text-muted">Approval Document</label>
                                            <p class="form-control-static">
                                                <a href="{{ Storage::url($procurementDetail->packageProject->hpc_document_path) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-file-pdf me-1"></i> View Document
                                                </a>
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Procurement Details Section -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-file-contract me-2"></i> Procurement Details
                </h5>
                <div>
                    <a href="{{ route('admin.procurement-details.index') }}" class="btn btn-sm btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Package
                    </a>
                    <a href="{{ route('admin.procurement-details.edit', $procurementDetail) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Procurement
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="mb-3 text-muted border-bottom pb-2">
                                <i class="fas fa-info-circle me-2"></i> Basic Information
                            </h6>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <p class="small text-muted mb-1">Method of Procurement</p>
                                    <p class="fw-semibold">{{ $procurementDetail->method_of_procurement ?? 'Not specified' }}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <p class="small text-muted mb-1">Type of Procurement</p>
                                    <p class="fw-semibold">{{ $procurementDetail->typeOfProcurement?->name ?? '-' }}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <p class="small text-muted mb-1">Publication Date</p>
                                    <p class="fw-semibold">
                                        @if($procurementDetail->publication_date)
                                            {{ formatDate($procurementDetail->publication_date) }}
                                        @else
                                            Not specified
                                        @endif
                                    </p>
                                </div>
                                <div class="col-6 mb-3">
                                    <p class="small text-muted mb-1">Bid Opening Date</p>
                                    <p class="fw-semibold">
                                        @if($procurementDetail->bid_opening_date)
                                            {{ formatDate($procurementDetail->bid_opening_date) }}
                                        @else
                                            Not specified
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="mb-3 text-muted border-bottom pb-2">
                                <i class="fas fa-money-bill-wave me-2"></i> Financial Details
                            </h6>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <p class="small text-muted mb-1">Tender Fee</p>
                                    <p class="fw-semibold">
                                        @if($procurementDetail->tender_fee)
                                            ₹{{ number_format($procurementDetail->tender_fee, 2) }}
                                        @else
                                            Not specified
                                        @endif
                                    </p>
                                </div>
                                <div class="col-6 mb-3">
                                    <p class="small text-muted mb-1">EMD Amount</p>
                                    <p class="fw-semibold">
                                        @if($procurementDetail->earnest_money_deposit)
                                            ₹{{ number_format($procurementDetail->earnest_money_deposit, 2) }}
                                        @else
                                            Not specified
                                        @endif
                                    </p>
                                </div>
                                <div class="col-6 mb-3">
                                    <p class="small text-muted mb-1">Bid Validity</p>
                                    <p class="fw-semibold">
                                        @if($procurementDetail->bid_validity_days)
                                            {{ $procurementDetail->bid_validity_days }} days
                                        @else
                                            Not specified
                                        @endif
                                    </p>
                                </div>
                                <div class="col-6 mb-3">
                                    <p class="small text-muted mb-1">EMD Validity</p>
                                    <p class="fw-semibold">
                                        @if($procurementDetail->emd_validity_days)
                                            {{ $procurementDetail->emd_validity_days }} days
                                        @else
                                            Not specified
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($procurementDetail->publication_document_path)
                <div class="border-top pt-4 mt-4">
                    <h6 class="mb-3 text-muted border-bottom pb-2">
                        <i class="fas fa-file-alt me-2"></i> Documents
                    </h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf text-danger me-3 fa-2x"></i>
                        <div>
                            <p class="mb-1 fw-semibold">Publication Document</p>
                            <a href="{{ Storage::url($procurementDetail->publication_document_path) }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Additional Notes -->
                @if($procurementDetail->packageProject->notes)
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="mb-3 text-muted border-bottom pb-2">
                            <i class="fas fa-sticky-note me-2"></i> Additional Notes
                        </h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($procurementDetail->packageProject->notes)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>