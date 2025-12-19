<x-app-layout>
    <div class="container py-5">
        <h2 class="mb-4 text-primary fw-bold">Edit Work Progress</h2>

        <form action="{{ route('admin.work_progress_data.update', $workProgressData) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Project</label>
                <select name="project_id" class="form-select" required>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" 
                            {{ $workProgressData->project_id == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Component</label>
                <select name="work_component_id" class="form-select" required>
                    @foreach($components as $component)
                        <option value="{{ $component->id }}" 
                            {{ $workProgressData->work_component_id == $component->id ? 'selected' : '' }}>
                            {{ $component->work_component }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Qty/Length</label>
                <input type="text" name="qty_length" class="form-control" 
                       value="{{ $workProgressData->qty_length }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Current Stage</label>
                <input type="text" name="current_stage" class="form-control"
                       value="{{ $workProgressData->current_stage }}">
            </div>

            <div class="mb-3">
                <label class="form-label">% Progress</label>
                <input type="number" step="0.01" name="progress_percentage" class="form-control"
                       value="{{ $workProgressData->progress_percentage }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control">{{ $workProgressData->remarks }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Update
            </button>
        </form>
    </div>
</x-app-layout>
