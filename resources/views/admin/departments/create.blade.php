<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row mb-4">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-building text-primary me-2"></i>
                    {{ isset($department) ? 'Edit Department' : 'Create New Department' }}
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.departments.index') }}">Departments</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ isset($department) ? 'Edit' : 'Create' }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Department Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas {{ isset($department) ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i>
                    {{ isset($department) ? 'Edit Department Details' : 'Add New Department' }}
                </h5>
            </div>

            <div class="card-body">
                <form method="POST"
                      action="{{ isset($department) ? route('admin.departments.update', $department) : route('admin.departments.store') }}">
                    @csrf
                    @if (isset($department))
                        @method('PUT')
                    @endif

                    <div class="row mb-3">
                        <!-- Department Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Department Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $department->name ?? '') }}"
                                   placeholder="Enter department name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Budget -->
                        <div class="col-md-6">
                            <label for="budget" class="form-label">Budget</label>
                            <input type="number"
                                   step="0.01"
                                   class="form-control @error('budget') is-invalid @enderror"
                                   id="budget"
                                   name="budget"
                                   value="{{ old('budget', $department->budget ?? '') }}"
                                   placeholder="Enter budget (optional)">
                            @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            {{ isset($department) ? 'Update Department' : 'Create Department' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
