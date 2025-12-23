<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-boxes text-primary" title="Package Projects Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Package Projects'],
            ]" />

        <!-- Success/Error Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible class="mb-3" />
        @endif
        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible class="mb-3" />
        @endif

        <!-- Table Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h4">
                    <i class="fas fa-list me-2 text-primary"></i> Package Projects List
                </h5>
                <a href="{{ route('admin.package-projects.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create Package Project
                </a>
            </div>

            <!-- Filters -->
            <div class="card-body border-bottom">
                <form method="GET" action="{{ route('admin.package-projects.index') }}">
                    <div class="row g-3 align-items-end">

                        <!-- Department -->
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department_id" class="form-control">
                                <option value="">All Departments</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- District -->
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label fw-semibold">District</label>
                            <select name="district_id" class="form-control">
                                <option value="">All Districts</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}"
                                        {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">All Categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Component -->
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label fw-semibold">Component</label>
                            <select name="package_component_id" class="form-control">
                                <option value="">All Components</option>
                                @foreach ($components as $comp)
                                    <option value="{{ $comp->id }}"
                                        {{ request('package_component_id') == $comp->id ? 'selected' : '' }}>
                                        {{ $comp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label fw-semibold">Search</label>
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by Name / Number" value="{{ request('search') }}">
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-4 col-lg-3 d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.package-projects.index') }}" class="btn btn-secondary flex-fill">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>


            <!-- Table -->
            <div class="card-body">
                <x-admin.data-table id="package-projects-table" :headers="[
                    'Package',
                    'Category',
                    'Sanction Budge (₹)',
                    'District',
                    'Procurement',
                    'Contracts',
                    'Status',
                    'Actions',
                ]" :excel="true" :print="true"
                    title="Package Projects Export" searchPlaceholder="Search package projects..."
                    resourceName="package-projects" :pageLength="10">

                    @foreach ($packageProjects as $project)
                        <tr>
                            <!-- Package -->
                           <td>
    <div class="d-flex flex-column">
        <!-- Package Name -->
        <a href="{{ route('admin.package-projects.show', $project->id) }}"
           class="fw-bold text-primary text-truncate mb-1"
           style="max-width: 450px;"
           title="{{ $project->package_name }}">
            {{ $project->package_name }}
        </a>

        <!-- Package Number -->
        <small class="text-muted mb-2">
            <i class="fas fa-hashtag me-1"></i> {{ $project->package_number }}
        </small>

        <!-- Badges -->
        <div class="d-flex flex-wrap gap-1 mb-1">
            <!-- DEC Approval -->
            <span class="badge bg-{{ $project->dec_approved ? 'warning' : 'secondary' }} text-dark">
                <i class="fas fa-check-circle me-1"></i> DEC: {{ $project->dec_approved ? 'Approved' : 'Pending' }}
            </span>

            <!-- HPC Approval -->
            <span class="badge bg-{{ $project->hpc_approved ? 'info' : 'secondary' }}">
                <i class="fas fa-check-circle me-1"></i> HPC: {{ $project->hpc_approved ? 'Approved' : 'Pending' }}
            </span>

            <!-- Department -->
            @if ($project->department?->name)
                <span class="badge bg-success text-white">
                    <i class="fas fa-building me-1"></i> {{ $project->department->name }}
                </span>
            @endif

            <!-- Safeguard Exists -->
            <span class="badge bg-{{ $project->safeguard_exists ? 'info' : 'secondary' }}">
                <i class="fas fa-shield-alt me-1"></i> Safeguard: {{ $project->safeguard_exists ? 'Yes' : 'No' }}
            </span>
        </div>
    </div>
</td>

                            <!-- Category -->
                            <td>
                                @if ($project->category?->name)
                                    <span class="fw-bold">
                                        <i class="fas fa-tags"></i> {{ $project->category->name }}
                                    </span>
                                @endif
                                @if ($project->subCategory?->name)
                                    ({{ $project->subCategory->name }})
                                @endif
                            </td>

                            <!-- Budget -->
                            <td class="align-middle">
                                <div class="fw-bold text-success">
                                    {{ formatPriceToCR($project->estimated_budget_incl_gst) }}
                                </div>
                            </td>

                            <!-- District / Block -->
                            <td>
                                <div class="fw-semibold">
                                    {{ $project->district?->name ?? 'N/A' }}
                                </div>
                                @if ($project->block?->name)
                                    <div class="small text-muted">
                                        <i class="fas fa-map-marker-alt"></i> Block: {{ $project->block->name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Procurement -->
                            <td class="align-middle">
                                @if ($project->procurementDetail)
                                    <span class="badge bg-success text-dark">
                                        <i class="fas fa-exclamation-circle"></i> Completed
                                    </span>
                                    <div class="fw-semibold">
                                        Method: {{ $project->procurementDetail->method_of_procurement }}
                                    </div>
                                    <div class="fw-semibold">
                                        Type: {{ $project->procurementDetail->typeOfProcurement?->name }}
                                    </div>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-circle"></i> Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Contracts -->
                            <td class="align-middle">
                                @if ($project->contracts->isNotEmpty())
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-info dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            {{ $project->contracts->count() }} Contract(s)
                                        </button>
                                        <ul class="dropdown-menu shadow-sm p-2" style="min-width: 300px;">
                                            @foreach ($project->contracts as $contract)
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span class="fw-bold">Contract No</span>
                                                    <span>{{ $contract->contract_number }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span class="fw-bold">Contract Value</span>
                                                    <span>₹ {{ number_format($contract->contract_value, 2) }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span class="fw-bold">Firm</span>
                                                    <span>{{ $contract->contractor?->company_name }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span class="fw-bold">Sub-Projects</span>
                                                    <span>{{ $contract->subProjects->count() }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle"></i> No Contracts
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td>

                                <span class="badge bg-success">
                                    <i class="fas fa-circle-check"></i> {{ $project->status }}
                                </span>

                            </td>

                            <!-- Actions -->
                            <td class="align-middle">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.package-projects.show', $project->id) }}"
                                        class="btn btn-sm btn-info text-white" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.package-projects.edit', $project->id) }}"
                                        class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.package-projects.destroy', $project->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this package project?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>

    <!-- Tooltips -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el);
            });
        });
    </script>
</x-app-layout>
