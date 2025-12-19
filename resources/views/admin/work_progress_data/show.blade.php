<x-app-layout>
    <div class="container-fluid">

        <x-admin.breadcrumb-header
            icon="fas fa-eye text-primary"
            title="Project Progress Details"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['route' => 'admin.work_progress_data.index', 'label' => 'Work Progress'],
                ['label' => $project->name]
            ]"
        />

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-project-diagram me-2"></i>
                    Project: {{ $project->name }}
                </h5>
                <a href="{{ route('admin.work_progress_data.create', ['sub_package_project_id' => $project->id]) }}" 
                   class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Progress
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="progress-table"
                    :headers="[
                        'ID', 'Component', 'Qty/Length', 'Stage', '% Progress',
                        'Remarks', 'Date', 'Added By', 'Actions'
                    ]"
                    :excel="true"
                    :print="true"
                    title="Project Progress Export"
                    searchPlaceholder="Search progress..."
                    resourceName="work_progress_data"
                    :pageLength="10"
                >
                    @foreach ($project->workProgressData as $data)
                        <tr>
                            <td>{{ $data->id }}</td>
                            <td>{{ $data->workComponent->work_component ?? '-' }}</td>
                            <td>{{ $data->qty_length ?? '-' }}</td>
                            <td>{{ $data->current_stage ?? '-' }}</td>
                            <td>{{ $data->progress_percentage }} %</td>
                            <td>{{ $data->remarks ?? '-' }}</td>
                            <td>{{ $data->date_of_entry?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $data->user->name ?? 'System' }}</td>
                            <td class="text-center">
                                <!-- Delete Button -->
                                <form 
                                    action="{{ route('admin.work_progress_data.destroy', $data->id) }}" 
                                    method="POST" 
                                    onsubmit="return confirm('Are you sure you want to delete this progress entry?');"
                                    class="d-inline"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('admin.work_progress_data.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
