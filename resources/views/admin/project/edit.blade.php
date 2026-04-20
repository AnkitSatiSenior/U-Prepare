<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-edit text-primary" title="Edit EAP Project" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => 'Home'],
            ['route' => 'admin.project.index', 'label' => 'Projects'],
            ['label' => 'Edit Project'],
        ]" />
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <form action="{{ route('admin.project.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>1. Core Project Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Project Name *</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $project->name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Short Name *</label>
                            <input type="text" name="project_short_name" class="form-control" required value="{{ old('project_short_name', $project->project_short_name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Funding Agency *</label>
                            <select name="funding_agency_id" class="form-select" required>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ $project->funding_agency_id == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="department_name" class="form-control" value="{{ old('department_name', $project->department_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Loan Signing Date</label>
                            <input type="date" name="loan_signing_date" class="form-control" value="{{ old('loan_signing_date', $project->loan_signing_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Scheduled Closure Date</label>
                            <input type="date" name="scheduled_closure_date" class="form-control" value="{{ old('scheduled_closure_date', $project->scheduled_closure_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-warning">
                <div class="card-header bg-warning-subtle fw-bold">
                    <i class="fas fa-history me-2"></i>2. Revised Project Status
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Is the project revised?</label>
                            <select name="is_revised" class="form-select" id="revised_toggle">
                                <option value="0" {{ !$project->is_revised ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $project->is_revised ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                    <div id="revised_fields" style="display: {{ $project->is_revised ? 'block' : 'none' }};">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-danger fw-bold">Revised Closure Date</label>
                                <input type="date" name="revised_closure_date" class="form-control" value="{{ old('revised_closure_date', $project->revised_closure_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-danger fw-bold">Revised Outlay (INR)</label>
                                <input type="number" step="0.01" name="revised_outlay_inr" class="form-control" value="{{ old('revised_outlay_inr', $project->revised_outlay_inr) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold"><i class="fas fa-map-marker-alt me-2"></i>3. Implementation Locations</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Select Project Districts *</label>
                            <select name="implementation_locations[]" class="form-select select2" multiple required>
                                @php $currentLocs = $project->implementation_locations ?? []; @endphp
                                @foreach($districts as $district)
                                    <option value="{{ $district }}" {{ in_array($district, $currentLocs) ? 'selected' : '' }}>
                                        {{ $district }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">You can select multiple districts.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5 d-flex justify-content-between">
                <a href="{{ route('admin.project.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
                <button type="submit" class="btn btn-primary px-5 shadow">
                    <i class="fas fa-save me-1"></i> Update Project Information
                </button>
            </div>
        </form>
    </div>

    <script>
        // Toggle Revised Section
        document.getElementById('revised_toggle').addEventListener('change', function() {
            document.getElementById('revised_fields').style.display = this.value == '1' ? 'block' : 'none';
        });
    </script>
</x-app-layout>