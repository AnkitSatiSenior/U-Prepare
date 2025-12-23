<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb Header -->
        <x-admin.breadcrumb-header 
            icon="fas fa-boxes text-success" 
            title="Create Type of Procurement" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                ['route' => 'admin.type-of-procurements.index', 'label' => 'Type of Procurements'], 
                ['label' => 'Create']
            ]" 
        />

        <!-- Card Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-success">
                    <i class="fas fa-plus-circle me-2"></i>Create Type of Procurement
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.type-of-procurements.store') }}" method="POST">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <x-label for="name" value="Name" />
                        <x-input id="name" name="name" type="text" class="form-control" :value="old('name')" required autofocus />
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <x-label for="description" value="Description" />
                        <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.type-of-procurements.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-success">Create</button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</x-app-layout>
