<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4 fw-bold text-primary">Import Safeguard Entries</h2>

        <form action="{{ route('admin.safeguard_entries.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Sub Package Project</label>
                <select name="sub_package_project_id" class="form-control" required>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Select File (XLSX / CSV)</label>
                <input type="file" name="file" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Import</button>
            <a href="{{ route('admin.safeguard_entries.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-app-layout>
