<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-users text-primary"
            title="Users Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Users']
            ]"
        />

        <!-- Alerts -->
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

        <!-- Users Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> User List
                </h5>
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create User
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="users-table"
                    :headers="[
                        'ID', 'Photo', 'Name','Username', 'Email', 'Role',
                        'Department', 'Sub Department', 'Designation',
                        'Status', 'Actions'
                    ]"
                    :excel="true"
                    :print="true"
                    title="Users Export"
                    searchPlaceholder="Search users..."
                    resourceName="users"
                    :pageLength="10"
                >
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>

                            <!-- Profile Photo -->
                            <td>
                                <img src="{{ $user->profile_photo_url }}" 
                                     alt="Photo" 
                                     class="rounded-circle"
                                     width="40" height="40">
                            </td>

                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>

                            <!-- Role -->
                            <td>
                                <span class="badge bg-light text-primary">
                                    {{ $user->role->name ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Department -->
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $user->department->name ?? '—' }}
                                </span>
                            </td>

                            <!-- Sub Department -->
                            <td>
                                <span class="badge bg-light text-info">
                                    {{ $user->subDepartment->name ?? '—' }}
                                </span>
                            </td>

                            <!-- Designation -->
                            <td>
                                <span class="badge bg-light text-secondary">
                                    {{ $user->designation->title ?? '—' }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td>
                                <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.users.destroy', $user) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
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
