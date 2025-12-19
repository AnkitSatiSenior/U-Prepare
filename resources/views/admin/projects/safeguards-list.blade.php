<x-app-layout>
    <div class="container-fluid">

        <x-admin.breadcrumb-header 
            icon="fas fa-shield-alt text-primary" 
            title="Safeguard → Phases Selector"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Safeguards'],
            ]" 
        />

        <div class="card shadow-sm mt-4 safeguard-card">
            <div class="card-header bg-white">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-filter me-2"></i> Select Safeguard to Load Phases
                </h5>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <!-- Department -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Department</label>
                        <select class="form-select department-select">
                            <option value="">Select Department</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub-Department -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Sub-Department</label>
                        <select class="form-select subdepartment-select" disabled>
                            <option value="">Select Department first</option>
                        </select>
                    </div>

                    <!-- User -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">User</label>
                        <select class="form-select user-select" disabled>
                            <option value="">Select Sub-Department first</option>
                        </select>
                    </div>

                    <!-- Project -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Project</label>
                        <select class="form-select project-select" disabled>
                            <option value="">Select Sub-Department first</option>
                        </select>
                    </div>

                    <!-- Safeguard -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Safeguard</label>
                        <select class="form-select safeguard-select">
                            <option value="">Select Safeguard</option>
                            @foreach ($safeguards as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Phases -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Phases</label>
                        <select class="form-select phase-select" disabled>
                            <option value="">Select Safeguard first</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>

