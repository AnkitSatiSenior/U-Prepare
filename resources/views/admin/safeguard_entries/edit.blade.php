<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4 fw-bold text-primary">Edit Safeguard Entry</h2>

        <form action="{{ route('admin.safeguard_entries.update', $safeguardEntry->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Sub Package Project</label>
                <select name="sub_package_project_id" class="form-control" required>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $safeguardEntry->sub_package_project_id == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Safeguard Compliance</label>
                <select name="safeguard_compliance_id" class="form-control" required>
                    @foreach($compliances as $compliance)
                        <option value="{{ $compliance->id }}" {{ $safeguardEntry->safeguard_compliance_id == $compliance->id ? 'selected' : '' }}>
                            {{ $compliance->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Construction Phase</label>
                <select name="contraction_phase_id" class="form-control" required>
                    @foreach($phases as $phase)
                        <option value="{{ $phase->id }}" {{ $safeguardEntry->contraction_phase_id == $phase->id ? 'selected' : '' }}>
                            {{ $phase->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">SL No</label>
                <input type="text" name="sl_no" class="form-control" value="{{ old('sl_no', $safeguardEntry->sl_no) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Item Description</label>
                <textarea name="item_description" class="form-control">{{ old('item_description', $safeguardEntry->item_description) }}</textarea>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_validity" class="form-check-input" id="is_validity" value="1" {{ old('is_validity', $safeguardEntry->is_validity) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_validity">Required</label>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('admin.safeguard_entries.index', ['sub_package_project_id' => $safeguardEntry->sub_package_project_id]) }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-app-layout>
