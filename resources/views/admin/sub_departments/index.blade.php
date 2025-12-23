<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-sitemap text-primary"
            title="Sub Departments Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Sub Departments']
            ]"
        />

        <!-- Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif
        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible />
        @endif

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Sub Department List
                </h5>
                <a href="{{ route('admin.sub-departments.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create Sub Department
                </a>
            </div>
            <div class="card-body">
                <x-admin.data-table id="sub-departments-table"
                    :headers="['ID', 'Department', 'Name', 'Status', 'Created At', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Sub Departments Export"
                    searchPlaceholder="Search sub departments..."
                    resourceName="sub-departments"
                    :pageLength="10">
                    
                    @foreach ($subDepartments as $sub)
                        <tr>
                            <td>{{ $sub->id }}</td>
                            <td>{{ $sub->department->name }}</td>
                            <td>{{ $sub->name }}</td>
                            <td>{{ $sub->status ? 'Active' : 'Inactive' }}</td>
                            <td>{{ $sub->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.sub-departments.edit', $sub) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.sub-departments.destroy', $sub) }}" method="POST"
                                          onsubmit="return confirm('Delete this sub-department?')">
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
