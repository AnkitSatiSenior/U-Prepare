<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs and Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-tag me-2 text-success"></i>
                        Edit Role
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item">Admin</li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            </div>
        @endif
        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-success">
                    <i class="fas fa-edit me-2"></i>
                    Edit Role: {{ $role->name }}
                </h5>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Roles
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-label for="name" value="Role Name" required />
                            <x-input type="text" name="name" id="name"
                                class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ old('name', $role->name) }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end border-top pt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
