<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($formMethod === 'PUT')
        @method('PUT')
    @endif

    <div class="row g-3">
        <!-- Basic Info -->
        <div class="col-md-6">
            <label class="form-label">Contract Number <span class="text-danger">*</span></label>
            <input type="text" name="contract_number"
                   class="form-control @error('contract_number') is-invalid @enderror"
                   value="{{ old('contract_number', $contract->contract_number ?? '') }}" required>
            @error('contract_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Project <span class="text-danger">*</span></label>
            <select name="project_id" class="form-control @error('project_id') is-invalid @enderror" required>
                <option value="">Select Project</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}"
                        @selected(old('project_id', $contract->project_id ?? '') == $project->id)>
                        {{ $project->package_name }} ({{ $project->package_number }})
                    </option>
                @endforeach
            </select>
            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Contract Value (₹) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="contract_value"
                   class="form-control @error('contract_value') is-invalid @enderror"
                   value="{{ old('contract_value', $contract->contract_value ?? '') }}" required>
            @error('contract_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Security Deposit (₹)</label>
            <input type="number" step="0.01" min="0" name="security" class="form-control"
                   value="{{ old('security', $contract->security ?? '') }}">
        </div>

        <!-- Dates -->
        @foreach ([
            'signing_date' => 'Signing Date',
            'commencement_date' => 'Commencement Date',
            'initial_completion_date' => 'Initial Completion Date',
            'revised_completion_date' => 'Revised Completion Date',
            'actual_completion_date' => 'Actual Completion Date'
        ] as $field => $label)
            <div class="col-md-4">
                <label class="form-label">{{ $label }}</label>
                <input type="date" name="{{ $field }}" class="form-control"
                       value="{{ old($field, $contract->$field ?? '') }}">
            </div>
        @endforeach

        <!-- Contract Document -->
        <div class="col-12">
            <label class="form-label">Contract Document</label>
            <input type="file" name="contract_document_file" class="form-control"
                   accept=".pdf,.doc,.docx,.xls,.xlsx">
            @if (!empty($contract->contract_document))
                <small class="text-muted">Current:
                    <a href="{{ asset('storage/' . $contract->contract_document) }}" target="_blank">View</a>
                </small>
            @endif
        </div>

        <!-- Contractor Info -->
        <div class="col-12 mt-4">
            <div class="card border">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i> Contractor Information</h6>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Select Existing Contractor</label>
                        <select name="contractor_id" class="form-control">
                            <option value="">-- Select Contractor --</option>
                            @foreach ($contractors as $contractor)
                                <option value="{{ $contractor->id }}"
                                    @selected(old('contractor_id', $contract->contractor_id ?? '') == $contractor->id)>
                                    {{ $contractor->company_name }} ({{ $contractor->gst_no ?? 'No GST' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            If not listed, fill details below.
                        </div>
                    </div>

                    @foreach ([
                        'company_name' => 'Company Name',
                        'authorized_personnel_name' => 'Authorized Personnel',
                        'phone' => 'Phone',
                        'email' => 'Email',
                        'gst_no' => 'GST Number',
                        'address' => 'Address'
                    ] as $field => $label)
                        <div class="{{ $field === 'address' ? 'col-12' : 'col-md-4' }}">
                            <label class="form-label">{{ $label }}</label>
                            @if ($field === 'address')
                                <textarea name="contractor[{{ $field }}]" class="form-control">{{ old("contractor.$field", optional($contract->contractor ?? null)->$field) }}</textarea>
                            @else
                                <input type="text" name="contractor[{{ $field }}]" class="form-control"
                                       value="{{ old("contractor.$field", optional($contract->contractor ?? null)->$field) }}">
                            @endif
                        </div>
                    @endforeach

                    <!-- Multiple Sub-projects toggle -->
                    @include('admin.contracts._sub_projects_toggle', ['contract' => $contract ?? null])
                </div>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="mt-4 d-flex justify-content-end border-top pt-3">
        <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary me-2">
            Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            {{ $formMethod === 'PUT' ? 'Update Contract' : 'Create Contract' }}
        </button>
    </div>
</form>
