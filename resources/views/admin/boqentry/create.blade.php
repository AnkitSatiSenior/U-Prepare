<x-app-layout>
    <div class="container py-5">
        <h2>Create New BOQ Entry</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.boqentry.store') }}">
            @csrf

            <div class="mb-3">
                <label for="sub_package_project_id" class="form-label">Sub Package Project</label>
                <select name="sub_package_project_id" id="sub_package_project_id" class="form-control" required>
                    <option value="">-- Select Project --</option>
                    @foreach ($subProjects as $project)
                        <option value="{{ $project->id }}" {{ (old('sub_package_project_id', $selectedProjectId ?? '') == $project->id) ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="sl_no" class="form-label">Sl. No.</label>
                <input type="text" name="sl_no" id="sl_no" value="{{ old('sl_no') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="item_description" class="form-label">Item Description</label>
                <input type="text" name="item_description" id="item_description" value="{{ old('item_description') }}" class="form-control">
            </div>

            <div class="mb-3">
                <label for="unit" class="form-label">Unit</label>
                <input type="text" name="unit" id="unit" value="{{ old('unit') }}" class="form-control">
            </div>

            <div class="mb-3">
                <label for="qty" class="form-label">Quantity</label>
                <input type="number" step="any" name="qty" id="qty" value="{{ old('qty') }}" class="form-control">
            </div>

            <div class="mb-3">
                <label for="rate" class="form-label">Rate</label>
                <input type="number" step="any" name="rate" id="rate" value="{{ old('rate') }}" class="form-control">
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" step="any" name="amount" id="amount" value="{{ old('amount') }}" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Create BOQ Entry</button>
            <a href="{{ route('admin.boqentry.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-app-layout>
