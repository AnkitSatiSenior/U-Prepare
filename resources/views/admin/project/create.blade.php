<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-plus text-primary" title="Create EAP Project" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => 'Home'],
            ['route' => 'admin.project.index', 'label' => 'Projects'],
            ['label' => 'Create'],
        ]" />

        <form action="{{ route('admin.project.store') }}" method="POST">
            @csrf
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>1. Core Project Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Project Name *</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Short Name *</label>
                            <input type="text" name="project_short_name" class="form-control" required value="{{ old('project_short_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Funding Agency *</label>
                            <select name="funding_agency_id" class="form-select" required>
                                <option value="">Select Agency</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department Name *</label>
                            <input type="text" name="department_name" class="form-control" value="U-PREPARE" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Loan Signing Date</label>
                            <input type="date" name="loan_signing_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Scheduled Closure Date</label>
                            <input type="date" name="scheduled_closure_date" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold text-success"><i class="fas fa-money-bill-wave me-2"></i>2. Initial Financial Outlay</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Donor Currency (e.g. USD)</label>
                            <input type="text" name="donor_currency" class="form-control" placeholder="USD">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Project Outlay (INR)</label>
                            <input type="number" step="0.01" name="outlay_inr" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Project Outlay (Donor)</label>
                            <input type="number" step="0.01" name="outlay_donor_currency" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Donor Share (INR)</label>
                            <input type="number" step="0.01" name="donor_share_inr" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-warning">
                <div class="card-header bg-warning-subtle fw-bold"><i class="fas fa-history me-2"></i>3. Revised Project Status</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Has the project been revised?</label>
                            <select name="is_revised" class="form-select" id="revised_toggle">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div id="revised_fields" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Revised Closure Date</label>
                                <input type="date" name="revised_closure_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Revised Outlay (INR)</label>
                                <input type="number" step="0.01" name="revised_outlay_inr" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold"><i class="fas fa-map-marker-alt me-2"></i>4. Implementation Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Districts (Locations) *</label>
                            <select name="implementation_locations[]" class="form-select select2" multiple required>
                                @foreach($districts as $district)
                                    <option value="{{ $district }}">{{ $district }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project Objectives</label>
                            <textarea name="objective" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project Components</label>
                            <textarea name="components" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5 d-flex justify-content-end">
                <a href="{{ route('admin.project.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-5">Save Project Information</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('revised_toggle').addEventListener('change', function() {
            document.getElementById('revised_fields').style.display = this.value == '1' ? 'block' : 'none';
        });
    </script>
</x-app-layout>