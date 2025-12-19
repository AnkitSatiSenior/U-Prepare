<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-tasks text-primary" title="Work Progress Entry" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['label' => 'Work Progress'],
        ]" />

        <!-- Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        <!-- Project Name -->
        @if ($project)
            <div class="alert alert-info fw-bold mb-3">
                Project: {{ $project->name }}
            </div>
        @endif

        <form action="{{ route('admin.work_progress_data.store') }}" method="POST">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id ?? '' }}">

            <!-- Data Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list me-2"></i> Work Components for Project
                    </h5>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Work Service</th>
                                <th>Component</th>
                                <th>Type/Details</th>
                                <th>Side/Location</th>
                                <th>Date of Entry</th>
                                <th>Qty/Length</th>
                                <th>Stage</th>
                                <th>% Progress</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($components as $component)
                                @php
                                    $entryData = $existingEntries->get($component->id);
                                    $totalProgress = $entryData->total_progress ?? 0;
                                    $lastEntry = $entryData->last_entry ?? null;
                                    $remaining = 100 - $totalProgress;
                                @endphp
                                <tr>
                                    <td>{{ $component->id }}</td>
                                    <td>{{ $component->workService->name ?? '-' }}</td>
                                    <td>{{ $component->work_component }}</td>
                                    <td>{{ $component->type_details }}</td>
                                    <td>{{ $component->side_location }}</td>

                                    @if ($totalProgress < 100)
                                        <!-- Date of Entry -->
                                        <td>
                                            <input type="date" name="updates[{{ $component->id }}][date_of_entry]"
                                                class="form-control" value="{{ now()->toDateString() }}">
                                        </td>

                                        <!-- Qty/Length -->
                                        <td>
                                            <input type="number" step="0.01"
                                                name="updates[{{ $component->id }}][qty_length]" class="form-control"
                                                placeholder="Qty/Length">
                                        </td>

                                        <!-- Stage -->
                                        <td>
                                            <input type="text" name="updates[{{ $component->id }}][current_stage]"
                                                class="form-control" placeholder="Enter Stage">
                                        </td>


                                        <!-- Progress % -->
                                        <td>
                                            <input type="number" step="0.01" max="{{ $remaining }}"
                                                name="updates[{{ $component->id }}][progress_percentage]"
                                                class="form-control progress-input"
                                                placeholder="Max {{ $remaining }}%">
                                        </td>

                                        <!-- Remarks -->
                                        <td>
                                            <textarea name="updates[{{ $component->id }}][remarks]" class="form-control" rows="2" placeholder="Remarks"></textarea>
                                        </td>
                                    @else
                                        <td colspan="5">
                                            <span class="badge bg-success">Completed</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Progress Data
                    </button>
                </div>
            </div>
        </form>
    </div>


</x-app-layout>
