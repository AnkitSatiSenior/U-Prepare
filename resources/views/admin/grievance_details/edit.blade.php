<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-edit text-primary"
            title="Edit Complaint Detail"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.grievance_details.index', 'label' => 'Complaint Details'],
                ['label' => 'Edit']
            ]"
        />

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.grievance_details.update', $grievance_detail) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nature_id" class="form-label">Nature</label>
                        <select name="nature_id" class="form-control" required>
                            <option value="">Select Nature</option>
                            @foreach ($natures as $nature)
                                <option value="{{ $nature->id }}" 
                                    @if($grievance_detail->nature_id == $nature->id) selected @endif>
                                    {{ $nature->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('nature_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Detail Name</label>
                        <input type="text" name="name" value="{{ $grievance_detail->name }}" class="form-control" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ $grievance_detail->slug }}" class="form-control" required>
                        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.grievance_details.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
