<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-chart-line text-primary"
            title="Work Progress"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Work Progress']
            ]"
        />

        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Sub Package Projects
                </h5>
               
            </div>

            <div class="card-body">
                <x-admin.data-table
                    id="work-progress-table"
                    :headers="['ID', 'Project', 'Total Components', 'Latest Progress (%)', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Work Progress Export"
                    searchPlaceholder="Search project..."
                    :pageLength="10"
                >
                    @foreach ($projects as $project)
                        @php
                            $latestAvg = $project->workProgressData->avg('progress_percentage') ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->workProgressData->count() }}</td>
                            <td>{{ number_format($latestAvg, 2) }} %</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.work_progress_data.create', ['sub_package_project_id' => $project->id]) }}" 
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-edit me-1"></i> Update Progress
                                    </a>

                                    <a href="{{ route('admin.work_progress_data.show', $project->id) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
