<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-user-tie text-primary"
            title="Edit Leader"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.leaders.index', 'label' => 'Leaders'],
                ['label' => 'Edit']
            ]"
        />

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-edit me-2"></i> Edit Leader
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.leaders.update', $leader) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Leader Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $leader->name) }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Designation / Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $leader->title) }}">
                        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="img" class="form-label">Profile Image</label><br>
                        @if ($leader->img)
                            <img src="{{ asset('storage/' . $leader->img) }}" alt="Leader" class="rounded mb-2" width="80"><br>
                        @endif
                        <input type="file" name="img" class="form-control">
                        @error('img') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', $leader->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $leader->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="order" class="form-label">Order</label>
                        <input type="number" name="order" class="form-control" value="{{ old('order', $leader->order) }}">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Leader
                    </button>
                    <a href="{{ route('admin.leaders.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
