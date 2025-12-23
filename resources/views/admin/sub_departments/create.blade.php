<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-sitemap text-primary"
            title="Create Sub Department"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.sub-departments.index', 'label' => 'Sub Departments'],
                ['label' => 'Create']
            ]"
        />

        <!-- Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif
        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible />
        @endif

        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-plus-circle me-2"></i> Create Sub Department
                </h5>
            </div>
            <div class="card-body">

                
                <form action="{{ route('admin.sub-departments.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-control" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Sub Department Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="status">Active</label>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> Create
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
