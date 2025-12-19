<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-plus text-primary"
            title="Add Complaint Detail"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.grievance_details.index', 'label' => 'Complaint Details'],
                ['label' => 'Create']
            ]"
        />

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.grievance_details.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nature_id" class="form-label">Nature</label>
                        <select name="nature_id" class="form-control" required>
                            <option value="">Select Nature</option>
                            @foreach ($natures as $nature)
                                <option value="{{ $nature->id }}">{{ $nature->name }}</option>
                            @endforeach
                        </select>
                        @error('nature_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Detail Name</label>
                        <input type="text" name="name" class="form-control" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" required>
                        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.grievance_details.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
