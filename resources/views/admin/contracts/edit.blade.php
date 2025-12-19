<x-app-layout>
    <div class="container-fluid">
        <!-- ✅ Breadcrumb Header -->
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" title="Edit Contract" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['route' => 'admin.contracts.index', 'label' => 'Contracts'],
            ['label' => 'Edit'],
        ]" />

        <!-- ✅ Error Alerts -->
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

        <!-- ✅ Edit Form Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-edit me-2"></i> Update Contract Details
                </h5>
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.contracts.update', $contract->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- ✅ Basic Info --}}
                        <div class="col-md-6">
                            <label class="form-label">Contract Number <span class="text-danger">*</span></label>
                            <input type="text" name="contract_number"
                                class="form-control @error('contract_number') is-invalid @enderror"
                                value="{{ old('contract_number', $contract->contract_number) }}" required>
                            @error('contract_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-control @error('project_id') is-invalid @enderror"
                                required disabled>
                                <option value="">Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" @selected(old('project_id', $contract->project_id) == $project->id)>
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
                                value="{{ old('contract_value', $contract->contract_value) }}" required>
                            @error('contract_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Security Deposit (₹)</label>
                            <input type="number" step="0.01" min="0" name="security" class="form-control"
                                value="{{ old('security', $contract->security) }}">
                        </div>

                        {{-- ✅ Important Dates --}}
                      @foreach ([
    'signing_date' => 'Signing Date',
    'commencement_date' => 'Commencement Date',
    'initial_completion_date' => 'Initial Completion Date',
    'revised_completion_date' => 'Revised Completion Date',
    'actual_completion_date' => 'Actual Completion Date',
] as $field => $label)
    <div class="col-md-4">
        <label class="form-label">{{ $label }}</label>

        <input type="date"
               name="{{ $field }}"
               class="form-control"
               value="{{ old($field, optional($contract->$field)->format('Y-m-d')) }}">
    </div>
@endforeach

                        {{-- ✅ Contract Document --}}
                        <div class="col-12">
                            <label class="form-label">Contract Document</label>
                            <input type="file" name="contract_document_file" class="form-control"
                                accept=".pdf,.doc,.docx,.xls,.xlsx">
                            @if ($contract->contract_document)
                                <small class="text-primary btn">
                                    Current:
                                    <a href="{{ asset('storage/' . $contract->contract_document) }}"
                                        target="_blank">View</a>
                                </small>
                            @endif
                        </div>

                        {{-- ✅ Update Document (Conditional) --}}
                        <div class="col-12">
                            <label class="form-label">
                                Update Document <span id="updateDocRequired" class="text-danger"
                                    style="display:none;">*</span>
                            </label>
                            <input type="file" name="update_document_file" id="update_document_file"
                                class="form-control @error('update_document_file') is-invalid @enderror"
                                accept=".pdf,.doc,.docx,.xls,.xlsx">
                            @error('update_document_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Required only if Contract Value / Initial Completion Date / Actual Completion Date
                                changes.
                            </small>
                        </div>

                        {{-- ✅ Contractor Info --}}
                        <div class="col-12 mt-4">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user-tie me-2"></i> Contractor Information
                                    </h6>
                                </div>

                                <div class="card-body row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Select Existing Contractor</label>
                                        <select name="contractor_id" class="form-control">
                                            <option value="">-- Select Contractor --</option>
                                            @foreach ($contractors as $contractor)
                                                <option value="{{ $contractor->id }}" @selected(old('contractor_id', $contract->contractor_id) == $contractor->id)>
                                                    {{ $contractor->company_name }}
                                                    ({{ $contractor->gst_no ?? 'No GST' }})
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

                                    {{-- ✅ Contractor Manual Fields --}}
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
                                                <textarea name="contractor[{{ $field }}]" class="form-control">{{ old("contractor.$field", optional($contract->contractor)->$field) }}</textarea>
                                            @else
                                                <input type="text" name="contractor[{{ $field }}]"
                                                    class="form-control"
                                                    value="{{ old("contractor.$field", optional($contract->contractor)->$field) }}">
                                            @endif
                                        </div>
                                    @endforeach

                                    {{-- ✅ Sub-Project Settings --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Multiple Sub-projects?</label>
                                        <div>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" name="has_multiple_sub_projects" value="yes"
                                                    class="form-check-input"
                                                    {{ old('has_multiple_sub_projects', $contract->count_sub_project > 1 ? 'yes' : 'no') === 'yes' ? 'checked' : '' }}>
                                                Yes
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" name="has_multiple_sub_projects" value="no"
                                                    class="form-check-input"
                                                    {{ old('has_multiple_sub_projects', $contract->count_sub_project > 1 ? 'yes' : 'no') === 'no' ? 'checked' : '' }}>
                                                No
                                            </label>
                                        </div>
                                    </div>

                                    {{-- ✅ Single Sub-project Inputs --}}
                                </div>
                                <div class="col-12 single-sub">
                                    <label class="form-label">Sub Project Name</label>
                                    <input type="text" name="sub_project_name" class="form-control"
                                        value="{{ old('sub_project_name', optional($contract->project)->package_name) }}">
                                </div>
                                <div class="mt-4 d-flex justify-content-end border-top pt-3">
                                    <a href="{{ route('admin.contracts.index') }}"
                                        class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Contract
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>


    @if (isset($subProjects) && $subProjects->count())
        <div class="col-12 mt-4">
            <div class="card border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-diagram-project me-2"></i> Linked Sub-Projects
                        ({{ $subProjects->count() }})
                    </h6>

                    <a href="{{ route('admin.contracts.edit-sub-packages', $contract->id) }}"
                        class="btn btn-primary me-2">
                        <i class="fas fa-pencil me-1"></i>
                        Edit Linked Sub-Packages
                    </a>
                </div>

                <div class="card-body table-responsive p-0">

                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:5%">#</th>
                                <th>Sub-Project Name</th>
                                <th>Contract Value (₹)</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Procurement Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subProjects as $index => $sp)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <textarea class="form-control form-control-sm bg-light text-muted" rows="2" disabled>{{ old("sub_projects.$sp->id.name", $sp->name) }}</textarea>
                                    </td>

                                    <td>
                                        <input type="number" step="0.01" min="0"
                                            name="sub_projects[{{ $sp->id }}][contract_value]"
                                            class="form-control form-control-sm"
                                            value="{{ old("sub_projects.$sp->id.contract_value", $sp->contract_value) }}"
                                            disabled>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0000001" min="-90" max="90"
                                            name="sub_projects[{{ $sp->id }}][lat]"
                                            class="form-control form-control-sm"
                                            value="{{ old("sub_projects.$sp->id.lat", $sp->lat) }}" disabled>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0000001" min="-180" max="180"
                                            name="sub_projects[{{ $sp->id }}][long]"
                                            class="form-control form-control-sm"
                                            value="{{ old("sub_projects.$sp->id.long", $sp->long) }}" disabled>
                                    </td>
                                    <td>
                                        {{ $sp->procurementDetail->typeOfProcurement->name ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- ✅ Scripts --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // === Handle "Update Document" Requirement ===
            const oldValues = {
                contract_value: "{{ $contract->contract_value }}",
                initial_completion_date: "{{ $contract->initial_completion_date }}",
                actual_completion_date: "{{ $contract->actual_completion_date }}"
            };
            const updateDocInput = document.getElementById("update_document_file");
            const updateDocRequiredMark = document.getElementById("updateDocRequired");

            function checkIfChanged() {
                const changed =
                    document.querySelector("input[name='contract_value']").value !== oldValues.contract_value ||
                    document.querySelector("input[name='initial_completion_date']").value !== oldValues
                    .initial_completion_date ||
                    document.querySelector("input[name='actual_completion_date']").value !== oldValues
                    .actual_completion_date;

                updateDocInput.toggleAttribute("required", changed);
                updateDocRequiredMark.style.display = changed ? "inline" : "none";
            }

            ["contract_value", "initial_completion_date", "actual_completion_date"].forEach(name => {
                const el = document.querySelector(`input[name='${name}']`);
                el.addEventListener("input", checkIfChanged);
                el.addEventListener("change", checkIfChanged);
            });
            checkIfChanged();

            // === Handle Multiple Sub-Projects Toggle ===
            function toggleSubFields() {
                const isMulti = document.querySelector('input[name="has_multiple_sub_projects"][value="yes"]')
                    .checked;
                const multiSubContainer = document.getElementById("multiSubProjects");
                const contractValue = parseFloat(document.querySelector("input[name='contract_value']").value) || 0;

                document.querySelectorAll(".single-sub").forEach(el => el.style.display = isMulti ? "none" :
                    "block");
                multiSubContainer.style.display = isMulti ? "flex" : "none";

                if (isMulti) {
                    let count = parseInt(prompt("Enter number of sub-projects:"), 10);
                    if (isNaN(count) || count < 2) count = 2;

                    multiSubContainer.innerHTML = "";
                    const defaultValue = contractValue > 0 ? (contractValue / count).toFixed(2) : "";

                    for (let i = 1; i <= count; i++) {
                        multiSubContainer.insertAdjacentHTML("beforeend", `
                            <div class="col-md-6">
                                <label class="form-label">Sub Project ${i} Name</label>
                                <input type="text" name="multi_sub_projects[${i}][name]" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sub Project ${i} Contract Value</label>
                                <input type="number" step="0.01" name="multi_sub_projects[${i}][value]" class="form-control" value="${defaultValue}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Latitude (optional)</label>
                                <input type="number" step="0.0000001" min="-90" max="90" name="multi_sub_projects[${i}][lat]" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitude (optional)</label>
                                <input type="number" step="0.0000001" min="-180" max="180" name="multi_sub_projects[${i}][long]" class="form-control">
                            </div>
                        `);
                    }
                }
            }

            document.querySelectorAll('input[name="has_multiple_sub_projects"]').forEach(el => {
                el.addEventListener("change", toggleSubFields);
            });

            toggleSubFields();
        });
    </script>
</x-app-layout>
