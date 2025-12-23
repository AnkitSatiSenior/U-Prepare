<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header 
            icon="fas fa-route text-primary" 
            title="Role Routes Status" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                ['label' => 'Admin'],
                ['label' => 'Role Routes']
            ]"  
        /> 

        <!-- Success/Error Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible class="mb-3" />
        @endif

        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible class="mb-3" />
        @endif

        <!-- Table Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h4 text-primary">
                    <i class="fas fa-list me-2"></i> Roles with Route Status
                </h5>
                <a href="{{ route('admin.role_routes.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Assign Routes
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="role-routes-table" 
                    :headers="['#', 'Role', 'Status', 'Actions']" 
                    :excel="true" 
                    :print="true" 
                    title="Role Routes Export" 
                    searchPlaceholder="Search roles..." 
                    resourceName="roles" 
                    :pageLength="10"
                >
                    @forelse($roles as $key => $role)
                        <tr>
                            <!-- Sr. No -->
                            <td class="align-middle">{{ $key + 1 }}</td>

                            <!-- Role -->
                            <td class="align-middle fw-semibold">
                                {{ $role->name }}
                            </td>

                            <!-- Status -->
                            <td class="align-middle">
                                @if($role->routes->isNotEmpty())
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Done
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i> Not Assigned
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="align-middle text-center">
                                <a href="{{ route('admin.role_routes.create') }}?role_id={{ $role->id }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Assign/Update"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-edit me-1"></i> Assign/Update
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No roles found.</td>
                        </tr>
                    @endforelse
                </x-admin.data-table>
            </div>
        </div>
    </div>

    <script>
        // Enable tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(el => new bootstrap.Tooltip(el))
        });
    </script>
</x-app-layout>
