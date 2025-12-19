<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header 
            icon="fas fa-clipboard-check text-primary" 
            title="Safeguard Entries Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Safeguard Entries'],
            ]" 
        />

        <!-- Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible class="mb-3" />
        @endif
        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible class="mb-3" />
        @endif

        <!-- Sub-Projects Data Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 h4">
                    <i class="fas fa-project-diagram text-primary me-2"></i> Sub-Projects List
                </h5>
            </div>
            <div class="card-body">
                <x-admin.data-table 
                    id="sub-projects-table"
                    :headers="['#', 'Package Name', 'Status', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Sub-Projects Export"
                    searchPlaceholder="Search sub-projects..."
                    resourceName="sub-projects"
                    :pageLength="10"
                >
                    @foreach($subProjects as $index => $project)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $project->name }}</td>
                            <td>
                                @if($projectsStatus[$project->id])
                                    <span class="badge bg-success">✔ Done</span>
                                @else
                                    <span class="badge bg-danger">✖ Not Done</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.social_safeguard_entries.index', ['sub_package_project_id' => $project->id]) }}" 
                                   class="btn btn-sm btn-primary" title="View Entries">
                                    <i class="fas fa-eye"></i> Entries
                                </a>
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
