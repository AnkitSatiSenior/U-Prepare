<x-app-layout>
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-file-contract text-primary me-2"></i> Create Contract
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.contracts.index') }}">Contracts</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Error Alerts --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-plus-circle me-2"></i> New Contract Details
                </h5>
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.contracts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">

                        {{-- Basic Info --}}
                        <div class="col-md-6">
                            <label class="form-label">Contract Number <span class="text-danger">*</span></label>
                            <input type="text" name="contract_number"
                                class="form-control @error('contract_number') is-invalid @enderror"
                                value="{{ old('contract_number') }}" required>
                            @error('contract_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Project <span class="text-danger">*</span></label>


                            <select name="project_id" class="form-control @error('project_id') is-invalid @enderror"
                                required>
                                <option value="">Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" @selected(old('project_id', $selectedPackageProjectId->id) == $project->id)>
                                        {{ $project->package_name }} ({{ $project->package_number }})
                                    </option>
                                @endforeach
                            </select>

                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Contract Value (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="contract_value"
                                class="form-control @error('contract_value') is-invalid @enderror"
                                value="{{ old('contract_value') }}" required>
                            @error('contract_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Security Deposit (₹)</label>
                            <input type="number" step="0.01" min="0" name="security" class="form-control"
                                value="{{ old('security') }}">
                        </div>

                        {{-- Dates --}}
                        @foreach ([
        'signing_date' => 'Signing Date',
        'commencement_date' => 'Commencement Date',
        'initial_completion_date' => 'Initial Completion Date',
        'revised_completion_date' => 'Revised Completion Date',
        'actual_completion_date' => 'Actual Completion Date',
    ] as $field => $label)
                            <div class="col-md-4">
                                <label class="form-label">{{ $label }}</label>
                                <input type="date" name="{{ $field }}" class="form-control"
                                    value="{{ old($field) }}">
                            </div>
                        @endforeach
                        {{-- End Dates --}}
                        {{-- Contract Document --}}
                        <div class="col-12">
                            <label class="form-label">Contract Document</label>
                            <input type="file" name="contract_document_file" class="form-control"
                                accept=".pdf,.doc,.docx,.xls,.xlsx">
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX, XLS, XLSX (Max: 5MB)</small>
                        </div>

                        {{-- Contractor Info --}}
                        <div class="col-12 mt-4">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user-tie me-2"></i> Contractor Information
                                    </h6>
                                </div>
                                <div class="card-body row g-3">

                                    {{-- Existing Contractor Select --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Select Existing Contractor</label>
                                        <select name="contractor_id" class="form-control">
                                            <option value="">-- Select Contractor --</option>
                                            @foreach ($contractors as $contractor)
                                                <option value="{{ $contractor->id }}" @selected(old('contractor_id') == $contractor->id)>
                                                    {{ $contractor->company_name }}
                                                    ({{ $contractor->gst_no ?? 'No GST' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Info alert --}}
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            If not listed, fill details below.
                                        </div>
                                    </div>

                                    {{-- Contractor manual fields --}}
                                    @foreach ([
        'company_name' => 'Company Name',
        'authorized_personnel_name' => 'Authorized Personnel',
        'phone' => 'Phone',
        'email' => 'Email',
        'gst_no' => 'GST Number',
        'address' => 'Address',
    ] as $field => $label)
                                        <div class="{{ $field === 'address' ? 'col-12' : 'col-md-4' }}">
                                            <label class="form-label">{{ $label }}</label>
                                            @if ($field === 'address')
                                                <textarea name="contractor[{{ $field }}]" class="form-control">{{ old("contractor.$field") }}</textarea>
                                            @else
                                                <input type="text" name="contractor[{{ $field }}]"
                                                    class="form-control" value="{{ old("contractor.$field") }}">
                                            @endif
                                        </div>
                                    @endforeach

                                    {{-- Multiple sub-projects toggle --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Multiple Sub-projects?</label>
                                        <div>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" name="has_multiple_sub_projects" value="yes"
                                                    class="form-check-input"
                                                    {{ old('has_multiple_sub_projects') === 'yes' ? 'checked' : '' }}>
                                                Yes
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" name="has_multiple_sub_projects" value="no"
                                                    class="form-check-input"
                                                    {{ old('has_multiple_sub_projects', 'no') === 'no' ? 'checked' : '' }}>
                                                No
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Single sub-project inputs --}}
                                    <div class="col-12 single-sub">
                                        <label class="form-label">Sub Project Name</label>
                                        <input type="text" name="sub_project_name" class="form-control"
                                            value="{{ old('sub_project_name') }}">
                                    </div>
                                    <div class="col-12 single-sub">
                                        <label class="form-label">Sub Project Contract Value</label>
                                        <input type="number" step="0.01" name="sub_project_contract_value"
                                            class="form-control" value="{{ old('sub_project_contract_value') }}">
                                    </div>

                                    {{-- Latitude and Longitude for single sub-project --}}
                                    <div class="col-md-6 single-sub">
                                        <label class="form-label">Latitude (optional)</label>
                                        <input type="number" step="0.0000001" min="-90" max="90"
                                            name="lat" class="form-control" value="{{ old('lat') }}">
                                    </div>
                                    <div class="col-md-6 single-sub">
                                        <label class="form-label">Longitude (optional)</label>
                                        <input type="number" step="0.0000001" min="-180" max="180"
                                            name="long" class="form-control" value="{{ old('long') }}">
                                    </div>

                                    {{-- Container for dynamically generated multiple sub-projects --}}
                                    <div id="multiSubProjects" class="row g-3 multi-sub" style="display:none;"></div>
                                    {{-- ENd Container for dynamically generated multiple sub-projects --}}



                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form buttons --}}
                    <div class="mt-4 d-flex justify-content-end border-top pt-3">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Create Contract
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSubFields() {
            const isMulti = document.querySelector('input[name="has_multiple_sub_projects"][value="yes"]').checked;
            const multiSubContainer = document.getElementById('multiSubProjects');
            const contractValue = parseFloat(document.querySelector('input[name="contract_value"]').value) || 0;

            // Show/hide single sub-project fields
            document.querySelectorAll('.single-sub').forEach(el => {
                el.style.display = isMulti ? 'none' : 'block';
            });

            // Show/hide multiple sub-project container
            multiSubContainer.style.display = isMulti ? 'flex' : 'none';

            if (isMulti) {
                let count = parseInt(prompt("Enter number of sub-projects:"), 10);
                if (isNaN(count) || count < 2) count = 2;

                // Clear old inputs
                multiSubContainer.innerHTML = '';

                // Distribute contract value equally as default
                let defaultValue = contractValue > 0 ? (contractValue / count).toFixed(2) : '';

                // Generate sub-project inputs
                for (let i = 1; i <= count; i++) {
                    let nameField = `
                                                        <div class="col-md-4">
                                                            <label class="form-label">Sub Project ${i} Name</label>
                                                            <input type="text" name="multi_sub_projects[${i}][name]" class="form-control" required>
                                                        </div>
                                                    `;
                    let valueField = `
                                                        <div class="col-md-4">
                                                            <label class="form-label">Sub Project ${i} Contract Value</label>
                                                            <input type="number" step="0.01" name="multi_sub_projects[${i}][value]" class="form-control" value="${defaultValue}" required>
                                                        </div>
                                                    `;
                    let latField = `
                                                        <div class="col-md-2">
                                                            <label class="form-label">Latitude (optional)</label>
                                                            <input type="number" step="0.0000001" min="-90" max="90" name="multi_sub_projects[${i}][lat]" class="form-control">
                                                        </div>
                                                    `;
                    let longField = `
                                                        <div class="col-md-2">
                                                            <label class="form-label">Longitude (optional)</label>
                                                            <input type="number" step="0.0000001" min="-180" max="180" name="multi_sub_projects[${i}][long]" class="form-control">
                                                        </div>
                                                    `;

                    multiSubContainer.insertAdjacentHTML('beforeend', nameField + valueField + latField + longField);
                }
            }
        }

        // Attach event listeners to radio buttons
        document.querySelectorAll('input[name="has_multiple_sub_projects"]').forEach(el => {
            el.addEventListener('change', toggleSubFields);
        });

        // Initialize toggle state on page load
        document.addEventListener('DOMContentLoaded', function() {
            if ({{ old('has_multiple_sub_projects', 'no') === 'yes' ? 'true' : 'false' }}) {
                toggleSubFields();
            }
        });
    </script>
</x-app-layout>
