<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-layer-group text-primary" title="Procurement Work Programs"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Work Programs'],
            ]" />


        <!-- Flash Messages -->
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

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-table me-2"></i> Package Projects Overview
                </h5>
            </div>

            <div class="card-body">
                <x-admin.data-table id="work-programs-table" :headers="['#', 'Package Name', 'Procurement Status', 'Work Program Status', 'Actions']" :excel="true" :print="true"
                    title="Procurement Work Programs Export" searchPlaceholder="Search packages..."
                    resourceName="procurement-work-programs" :pageLength="10">

                    @foreach ($packageProjects as $index => $project)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                {{ $project->package_name }}
                                <br>
                                <small class="text-muted">{{ $project->package_number }}</small>
                            </td>

                            <td>
                                @if ($project->procurementDetail)
                                    <span class="badge bg-success">Done</span>
                                @else
                                    <span class="badge bg-warning text-dark">Not Done</span>
                                @endif
                            </td>

                            <td>
                                @if ($project->has_work_program)
                                    <span class="badge bg-info">Added</span>
                                @else
                                    <span class="badge bg-secondary">Not Added</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    @if ($project->has_work_program)
                                        <a href="{{ route('admin.procurement-work-programs.edit-by-package', [
                                            'package_project_id' => $project->id,
                                            'procurement_details_id' => optional($project->procurementDetail)->id,
                                        ]) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a> <a
                                            href="{{ route('admin.procurement-work-programs.show.pack', [
                                                'package_project_id' => $project->id,
                                                'procurement_details_id' => optional($project->procurementDetail)->id,
                                            ]) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                    @else
                                        <a href="{{ route('admin.procurement-work-programs.create', [
                                            'package_project_id' => $project->id,
                                            'procurement_details_id' => optional($project->procurementDetail)->id,
                                        ]) }}"
                                            class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-plus me-1"></i> Add
                                        </a>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @endforeach

                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
