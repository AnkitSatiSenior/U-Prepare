<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumbs & Header -->
        <x-admin.breadcrumb-header icon="fas fa-boxes text-primary" title="Edit Package Project" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
            ['label' => 'Edit'],
        ]" />

        <!-- Alerts -->
        @foreach (['success', 'error'] as $msg)
            @if (session($msg))
                <div class="alert alert-{{ $msg === 'success' ? 'success' : 'danger' }} alert-dismissible fade show mb-3"
                    role="alert">
                    <i class="fas fa-{{ $msg === 'success' ? 'check-circle' : 'exclamation-circle' }} me-2"></i>
                    {{ session($msg) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Whoops!</strong> There were some problems with your input.
                <ul class="mt-2 mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Edit Form -->
        <form action="{{ route('admin.package-projects.update', $packageProject) }}" method="POST"
            enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            @method('PUT')

            <!-- Project Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-edit me-2"></i>Edit Package Project Details</h5>
                    <a href="{{ route('admin.package-projects.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">

                    <div class="row g-3">
                        <!-- Category -->
                        <div class="col-md-3">
                            <x-bootstrap.dropdown name="package_category_id" label="Package Category" :items="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                                :selected="old('package_category_id', $packageProject->package_category_id)" placeholder="Select Category" />
                        </div>

                        <!-- Sub Category -->
                        <div class="col-md-3">
                            <label for="package_sub_category_id" class="form-label">Sub Category</label>
                            <select name="package_sub_category_id" id="package_sub_category_id" class="form-control">
                                <option value="">Select Sub Category</option>
                                @foreach ($subCategories as $subCategory)
                                    <option value="{{ $subCategory->id }}"
                                        data-category="{{ $subCategory->category_id }}" @selected(old('package_sub_category_id', $packageProject->package_sub_category_id) == $subCategory->id)>
                                        {{ $subCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Department -->
                        <div class="col-md-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-control">
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id', $packageProject->department_id) == $department->id)>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sub Department -->
                        <div class="col-md-3">
                            <label for="sub_department_id" class="form-label">Sub Department</label>
                            <select name="sub_department_id" id="sub_department_id" class="form-control">
                                <option value="">Select Sub Department</option>
                                @foreach ($subDepartments as $subDept)
                                    <option value="{{ $subDept->id }}"
                                        data-department="{{ $subDept->department_id }}" @selected(old('sub_department_id', $packageProject->sub_department_id) == $subDept->id)>
                                        {{ $subDept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Component -->
                        <div class="col-md-3">
                            <x-bootstrap.dropdown name="package_component_id" label="Package Component"
                                :items="$components
                                    ->map(fn($c) => ['value' => $c->id, 'label' => $c->name])
                                    ->toArray()" :selected="old('package_component_id', $packageProject->package_component_id)" placeholder="Select Component" />
                        </div>
                    </div>

                    <!-- Text Inputs -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-12">
                            <label for="package_name" class="form-label">Package Name <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="package_name" name="package_name" required>{{ old('package_name', $packageProject->package_name) }}</textarea>
                            <div class="invalid-feedback">Package name is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="package_number" class="form-label">Package Number <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="package_number" name="package_number"
                                value="{{ old('package_number', $packageProject->package_number) }}" required>
                            <div class="invalid-feedback">Package number is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="estimated_budget_incl_gst" class="form-label">Estimated Budget (₹) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="estimated_budget_incl_gst"
                                name="estimated_budget_incl_gst"
                                value="{{ old('estimated_budget_incl_gst', $packageProject->estimated_budget_incl_gst) }}"
                                required>
                            <div class="invalid-feedback">Valid budget is required.</div>
                        </div>
                        <!-- Safeguard Exists Checkbox -->

                        <div class="col-md-4">
                            <div class="form-check form-switch mt-2">
                                <p class="form-label h5">Safeguard Exists</p>

                                <!-- Hidden input ensures a value is always sent -->
                                <input type="hidden" name="safeguard_exists" value="0">

                                <input class="form-check-input" type="checkbox" name="safeguard_exists"
                                    id="safeguard_exists" value="1" @checked(old('safeguard_exists', $packageProject->safeguard_exists))>
                                <label class="form-check-label" for="safeguard_exists">Yes</label>
                            </div>
                            <small class="text-muted">Check if safeguards exist for this package project.</small>
                        </div>



                    </div>


                    @include('admin.package-projects.partials.location-fields', [
                        'packageProject' => $packageProject,
                        'districts' => $districts,
                        'blocks' => $blocks,
                        'constituencies' => $constituencies,
                        'assembly' => $assembly,
                    ])
                    <!-- Location Card -->
                    <div class="row g-3">


                        <!-- DEC & HPC Cards -->
                        @include('admin.package-projects.partials.approval-fields', [
                            'packageProject' => $packageProject,
                        ])
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-end border-top pt-4">
                        <a href="{{ route('admin.package-projects.index') }}" class="btn btn-outline-secondary me-3">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Package Project
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const catSelect = document.getElementById('package_category_id');
            const subCatSelect = document.getElementById('package_sub_category_id');
            const deptSelect = document.getElementById('department_id');
            const subDeptSelect = document.getElementById('sub_department_id');

            deptSelect.addEventListener('change', () => filterOptions(deptSelect, subDeptSelect, 'department'));
            catSelect.addEventListener('change', () => filterOptions(catSelect, subCatSelect, 'category'));

            // ✅ apply filters on load (important for edit form)
            filterOptions(deptSelect, subDeptSelect, 'department');
            filterOptions(catSelect, subCatSelect, 'category');
        });

        document.addEventListener('DOMContentLoaded', function() {
            const deptSelect = document.getElementById('department_id');
            const subDeptSelect = document.getElementById('sub_department_id');
            const catSelect = document.getElementById('package_category_id');
            const subCatSelect = document.getElementById('package_sub_category_id');

            function filterOptions(parentSelect, childSelect, dataAttr) {
                const selected = parentSelect.value;
                Array.from(childSelect.options).forEach(opt => {
                    if (!opt.value) return; // keep default
                    opt.style.display = (opt.dataset[dataAttr] === selected) ? 'block' : 'none';
                });
                if (childSelect.value && childSelect.selectedOptions[0].style.display === 'none') {
                    childSelect.value = '';
                }
            }

            deptSelect.addEventListener('change', () => filterOptions(deptSelect, subDeptSelect, 'department'));
            catSelect.addEventListener('change', () => filterOptions(catSelect, subCatSelect, 'category'));

            // Run on load
            filterOptions(deptSelect, subDeptSelect, 'department');
            filterOptions(catSelect, subCatSelect, 'category');

            // Bootstrap validation
            (function() {
                'use strict';
                var forms = document.querySelectorAll('.needs-validation');
                Array.prototype.slice.call(forms).forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            })();
        });
    </script>
</x-app-layout>
