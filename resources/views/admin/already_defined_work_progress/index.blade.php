<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-tasks text-primary"
            title="Predefined Work Components"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Work Components']
            ]"
        />

        <!-- Alerts -->
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Work Components List
                </h5>
                <a href="{{ route('admin.already_defined_work_progress.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add Component
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="work-components-table"
                    :headers="['ID', 'Work Service', 'Component', 'Type/Details', 'Side/Location', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Work Components Export"
                    searchPlaceholder="Search components..."
                   
                    :pageLength="10"
                >
                    @foreach ($workProgress as $component)
                        <tr>
                            <td>{{ $component->id }}</td>
                            <td>{{ $component->workService->name ?? '-' }}</td>
                            <td>{{ $component->work_component }}</td>
                            <td>{{ $component->type_details }}</td>
                            <td>{{ $component->side_location }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.already_defined_work_progress.edit', $component) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.already_defined_work_progress.destroy', $component) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this component?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
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
</x-app-layout>
