<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-boxes text-primary" title="Package Projects Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Packages', 'route' => 'admin.package-projects.index'],
                ['label' => 'Create Package'],
            ]" />

        <!-- Session Alerts -->
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Whoops!</strong> There were some problems with your input.
                        <ul class="mt-2 mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <form action="{{ route('admin.package-projects.store') }}" method="POST" enctype="multipart/form-data"
            class="needs-validation" novalidate>
            @csrf

            <!-- Basic Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="fas fa-plus-circle me-2"></i>Create Package</h5>
                    <a href="{{ route('admin.package-projects.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
                <div class="card-body">

                    <div class="row g-3">
                        <!-- Category -->
                        <div class="col-md-3">
                            <label class="form-label h6">Package Category</label>
                            <select name="package_category_id" id="package_category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('package_category_id') == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sub Category -->
                        <div class="col-md-3">
                            <label class="form-label h6">Sub Category</label>
                            <select name="package_sub_category_id" id="package_sub_category_id" class="form-control">
                                <option value="">Select Sub Category</option>
                                @foreach ($subCategories as $subCategory)
                                    <option value="{{ $subCategory->id }}"
                                        data-category="{{ $subCategory->category_id }}" @selected(old('package_sub_category_id') == $subCategory->id)>
                                        {{ $subCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Department -->
                        <div class="col-md-3">
                            <label class="form-label h6">Department</label>
                            <select name="department_id" id="department_id" class="form-control">
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id', $packageProject->department_id ?? '') == $department->id)>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sub Department -->
                        <div class="col-md-3">
                            <label class="form-label h6">Sub Department</label>
                            <select name="sub_department_id" id="sub_department_id" class="form-control">
                                <option value="">Select Sub Department</option>
                                @foreach ($subDepartments as $subDept)
                                    <option value="{{ $subDept->id }}"
                                        data-department="{{ $subDept->department_id }}" @selected(old('sub_department_id', $packageProject->sub_department_id ?? '') == $subDept->id)>
                                        {{ $subDept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <!-- Package Component -->
                        <div class="col-md-6">
                            <label class="form-label h6">Package under Component</label>
                            <select name="package_component_id" id="package_component_id" class="form-control">
                                <option value="">Select Package Component</option>
                                @foreach ($components as $component)
                                    <option value="{{ $component->id }}" @selected(old('package_component_id') == $component->id)>
                                        {{ $component->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Safeguard Exists -->
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="safeguard_exists" value="0">
                                <input class="form-check-input" type="checkbox" name="safeguard_exists"
                                    id="safeguard_exists" value="1" @checked(old('safeguard_exists', true))>
                                <label class="form-check-label" for="safeguard_exists">Safeguard Exists</label>
                                <small class="d-block text-muted">Check if safeguards exist for this package
                                    project.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <!-- Package Name -->
                        <div class="col-md-6">
                            <label class="form-label h6">Package Name <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="package_name" name="package_name" required>{{ old('package_name') }}</textarea>
                            <div class="invalid-feedback">Please provide a package name.</div>
                        </div>

                        <!-- Package Number -->
                        <div class="col-md-3">
                            <label class="form-label h6">Package Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="package_number" name="package_number"
                                value="{{ old('package_number') }}" required>
                            <div class="invalid-feedback">Please provide a package number.</div>
                        </div>

                        <!-- Sanctioned Cost -->
                        <div class="col-md-3">
                            <label class="form-label h6">Sanctioned Cost (₹) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="estimated_budget_incl_gst"
                                name="estimated_budget_incl_gst" value="{{ old('estimated_budget_incl_gst') }}"
                                required>
                            <div class="invalid-feedback">Please provide a valid budget amount.</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- DEC & HPC Approvals Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- DEC Approval -->
                        <div class="col-md-6">
                            <div class="card border-light mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary"><i class="fas fa-check-circle me-2"></i>DEC Approval
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input type="hidden" name="dec_approved" value="0">
                                        <input class="form-check-input" type="checkbox" name="dec_approved"
                                            id="dec_approved" value="1">
                                        <label class="form-check-label" for="dec_approved">Approved</label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Approval Date</label>
                                        <input type="date" class="form-control" name="dec_approval_date"
                                            value="{{ old('dec_approval_date') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Letter Number</label>
                                        <input type="text" class="form-control" name="dec_letter_number"
                                            value="{{ old('dec_letter_number') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Approval Document (PDF)</label>
                                        <input type="file" class="form-control" name="dec_document_path"
                                            accept=".pdf">
                                        <small class="text-muted">Max 2MB PDF file</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- HPC Approval -->
                        <div class="col-md-6">
                            <div class="card border-light mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary"><i class="fas fa-check-circle me-2"></i>HPC Approval
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input type="hidden" name="hpc_approved" value="0">
                                        <input class="form-check-input" type="checkbox" name="hpc_approved"
                                            id="hpc_approved" value="1">
                                        <label class="form-check-label" for="hpc_approved">Approved</label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Approval Date</label>
                                        <input type="date" class="form-control" name="hpc_approval_date"
                                            value="{{ old('hpc_approval_date') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Letter Number</label>
                                        <input type="text" class="form-control" name="hpc_letter_number"
                                            value="{{ old('hpc_letter_number') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Approval Document (PDF)</label>
                                        <input type="file" class="form-control" name="hpc_document_path"
                                            accept=".pdf">
                                        <small class="text-muted">Max 2MB PDF file</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end mb-4">
                <button type="reset" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Create Package Project
                </button>
            </div>
        </form>


        <!-- Bootstrap Validation Script -->

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('package_category_id');
            const subCategorySelect = document.getElementById('package_sub_category_id');
            const deptSelect = document.getElementById('department_id');
            const subDeptSelect = document.getElementById('sub_department_id');

            function filterOptions(parentSelect, childSelect, dataAttr) {
                const selected = parentSelect.value;
                Array.from(childSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    opt.style.display = (opt.dataset[dataAttr] === selected) ? 'block' : 'none';
                });
                if (childSelect.selectedOptions[0]?.style.display === 'none') {
                    childSelect.value = '';
                }
            }

            categorySelect.addEventListener('change', () => filterOptions(categorySelect, subCategorySelect,
                'category'));
            deptSelect.addEventListener('change', () => filterOptions(deptSelect, subDeptSelect, 'department'));

            // Run on load
            filterOptions(categorySelect, subCategorySelect, 'category');
            filterOptions(deptSelect, subDeptSelect, 'department');
        });

        // Bootstrap validation
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', e => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</x-app-layout>
