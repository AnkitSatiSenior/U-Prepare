<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs and Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-tag me-2 text-success"></i>Roles Management
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item">Admin</li>
                            <li class="breadcrumb-item active">Roles</li>
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
        <!-- Card with Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-success">
                    <i class="fas fa-list me-2"></i>Roles List
                </h5>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Create Role
                </a>
            </div>
            <div class="card-body">
                <x-admin.data-table :headers="['ID', 'Role Name', 'Actions']" id="roles-table" :excel="true" :print="true"
                    :pageLength="10">
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>

                                {{ $role->name }}

                            </td>
                            <td>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                        class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to delete this role?')">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
