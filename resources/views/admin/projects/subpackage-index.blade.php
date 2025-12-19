<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-project-diagram text-primary"
            title="Sub Package Projects"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Projects'],
                ['label' => 'Sub Packages']
            ]"
        />

        <!-- Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-boxes me-2"></i> Sub Package Projects
                </h5>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="subpackages-table"
                    :headers="['#', 'Package Project', 'Sub Package Project', 'Action']"
                    :excel="true"
                    :print="true"
                    title="Sub Package Projects Export"
                    searchPlaceholder="Search sub package projects..."
                    resourceName="sub-packages"
                    :pageLength="10"
                >
                    @foreach ($subPackageProjects as $index => $sub)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sub->project->name ?? $sub->packageproject->package_number ?? 'N/A' }}</td>
                            <td>{{ $sub->name ?? $sub->title }}</td>
                            <td>
                                <a href="{{ route('admin.summary', [$sub->project_id, $sub->id]) }}" class="btn btn-sm btn-primary">
                                    View Access Summary
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
