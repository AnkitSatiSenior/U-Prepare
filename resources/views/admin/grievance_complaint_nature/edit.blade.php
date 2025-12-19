<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-edit text-primary"
            title="Edit Complaint Nature"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Complaint Nature'],
                ['label' => 'Edit']
            ]"
        />

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.grievance-complaint-nature.update', $grievanceComplaintNature) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $grievanceComplaintNature->name) }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $grievanceComplaintNature->slug) }}" required>
                        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync-alt me-1"></i> Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
