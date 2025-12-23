<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
    icon="fas fa-folder text-primary"
    :title="isset($projectsCategory) ? 'Edit Project Category' : 'Create Project Category'"
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
        ['label' => 'Admin'],
        ['route' => 'admin.projects-category.index', 'label' => 'Project Categories'],
        ['label' => isset($projectsCategory) ? 'Edit' : 'Create']
    ]"
/>


        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas {{ isset($projectsCategory) ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i> {{ isset($projectsCategory) ? 'Edit' : 'Create' }} Category Details
                </h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ isset($projectsCategory) ? route('admin.projects-category.update', $projectsCategory->id) : route('admin.projects-category.store') }}">
                    @csrf
                    @if(isset($projectsCategory)) @method('PUT') @endif

                    <div class="row mb-3">
                        <!-- Category Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name', $projectsCategory->name ?? '') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="1" {{ old('status', $projectsCategory->status ?? '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $projectsCategory->status ?? '') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Methods of Procurement -->
                        <div class="col-md-12">
                            <label for="methods" class="form-label">Methods of Procurement</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('methods_of_procurement') is-invalid @enderror" 
                                       id="methods" name="methods_of_procurement[]" 
                                       value="{{ old('methods_of_procurement.0', isset($projectsCategory) ? implode(', ', $projectsCategory->methods_of_procurement ?? []) : '') }}" 
                                       placeholder="e.g. Open Tender, Limited Tender">
                                <span class="input-group-text">
                                    <i class="fas fa-list-alt"></i>
                                </span>
                            </div>
                            <small class="text-muted">Separate multiple methods with commas</small>
                            @error('methods_of_procurement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('admin.projects-category.index') }}" class="btn btn-light me-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> {{ isset($projectsCategory) ? 'Update' : 'Save' }} Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>