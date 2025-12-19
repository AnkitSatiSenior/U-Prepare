<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-tasks text-primary" title="Project Assignments" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['label' => 'Assignments'],
        ]" />
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            </div>
        @endif
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Assignments List
                </h5>
                <a href="{{ route('admin.package-project-assignments.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Assign Project
                </a>
            </div>
            <div class="card-body">
                <x-admin.data-table id="projects-table" :headers="['#', 'Package Number', 'Project', 'Assignments Count', 'Assignments Details', 'Actions']" :excel="true" :print="true"
                    title="Projects Export" searchPlaceholder="Search projects..." resourceName="projects"
                    :pageLength="10">

                    @foreach ($projects as $project)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $project->package_number ?? 'N/A' }}</td>
                            <td style="max-width: 350px;">
                                <a href="{{ route('admin.package-projects.show', $project->id) }}"
                                    class="badge bg-light text-dark text-truncate d-inline-block text-decoration-none"
                                    style="max-width: 100%; cursor: pointer;"
                                    title="{{ $project->package_name ?? 'N/A' }}">
                                    {{ $project->package_name ?? 'N/A' }}
                                </a>
                            </td>

                            {{-- Count of Assignments --}}
                            <td>
                                <span class="badge bg-primary">
                                    {{ $project->assignments->count() }}
                                </span>
                            </td>

                            {{-- Hover/Click Dropdown of Details --}}
                            <td>
                                @if ($project->assignments->count() > 0)
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-expanded="false">
                                            View Details
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach ($project->assignments as $assignment)
                                                <li class="dropdown-item">
                                                    <strong>Assigned To:</strong>
                                                    {{ $assignment->assignee->name ?? 'N/A' }} <br>
                                                    <strong>Assigned By:</strong>
                                                    {{ $assignment->assigner->name ?? 'N/A' }}
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <span class="badge bg-warning text-dark">Not Assigned</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="d-flex gap-2">
                                {{-- Assign Button --}}
                                <a href="{{ route('admin.package-project-assignments.create', ['project_id' => $project->id]) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Assign
                                </a>

                                {{-- View Tree Button --}}
                                <a href="{{ route('admin.package-project-assignments.report') }}?project_id={{ $project->id }}"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-sitemap me-1"></i> View Tree
                                </a>
                            </td>

                        </tr>
                    @endforeach
                </x-admin.data-table>

            </div>
        </div>
    </div>
</x-app-layout>
