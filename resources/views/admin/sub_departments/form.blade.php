@php
    $isEdit = isset($subDepartment);
@endphp

<form action="{{ $isEdit ? route('admin.sub-departments.update', $subDepartment->id) : route('admin.sub-departments.store') }}" method="POST">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="department_id" class="form-label">Department</label>
        <select name="department_id" id="department_id" class="form-control" required>
            <option value="">Select Department</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ ($isEdit && $subDepartment->department_id == $dept->id) ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Sub Department Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ $isEdit ? $subDepartment->name : old('name') }}" required>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ ($isEdit && $subDepartment->status) || old('status') ? 'checked' : '' }}>
        <label class="form-check-label" for="status">Active</label>
    </div>

    <button type="submit" class="btn btn-success">{{ $isEdit ? 'Update' : 'Create' }}</button>
</form>
