<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-shield-alt text-primary"
            title="Assign Users to Compliance & Projects"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Safeguards'],
                ['label' => 'New Assignment']
            ]"
        />

        <!-- Errors -->
        @if ($errors->any())
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" dismissible>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-plus-circle me-2"></i> New Assignment
                </h5>
                <a href="{{ route('admin.user-safeguard-subpackage.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.user-safeguard-subpackage.store') }}" method="POST">
                    @csrf

                    <!-- Users -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Users</label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Choose one or more users</small>
                            <button type="button" class="btn btn-sm btn-outline-primary select-all" data-target="user_ids">
                                <i class="fas fa-check-double me-1"></i> Select All
                            </button>
                        </div>
                        <div class="row">
                            @foreach ($users as $user)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" id="user-{{ $user->id }}" class="form-check-input user_ids">
                                        <label for="user-{{ $user->id }}" class="form-check-label">
                                            {{ $user->name }} ({{ $user->username }})
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Safeguard Compliances -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Safeguard Compliances</label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Choose one or more compliances</small>
                            <button type="button" class="btn btn-sm btn-outline-primary select-all" data-target="safeguard_compliance_ids">
                                <i class="fas fa-check-double me-1"></i> Select All
                            </button>
                        </div>
                        <div class="row">
                            @foreach ($safeguardCompliances as $comp)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" name="safeguard_compliance_ids[]" value="{{ $comp->id }}" id="comp-{{ $comp->id }}" class="form-check-input safeguard_compliance_ids">
                                        <label for="comp-{{ $comp->id }}" class="form-check-label">
                                            {{ $comp->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sub Package Projects -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Sub Package Projects</label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Choose one or more projects</small>
                            <button type="button" class="btn btn-sm btn-outline-primary select-all" data-target="sub_package_project_ids">
                                <i class="fas fa-check-double me-1"></i> Select All
                            </button>
                        </div>
                        <div class="row">
                            @foreach ($subPackageProjects as $project)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" name="sub_package_project_ids[]" value="{{ $project->id }}" id="project-{{ $project->id }}" class="form-check-input sub_package_project_ids">
                                        <label for="project-{{ $project->id }}" class="form-check-label">
                                            {{ $project->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Select All Script -->
    <script>
        document.querySelectorAll('.select-all').forEach(button => {
            button.addEventListener('click', function () {
                const targetClass = this.getAttribute('data-target');
                const checkboxes = document.querySelectorAll('.' + targetClass);

                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);

                this.innerHTML = allChecked 
                    ? '<i class="fas fa-check-double me-1"></i> Select All'
                    : '<i class="fas fa-times-circle me-1"></i> Deselect All';
            });
        });
    </script>
</x-app-layout>
