<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-shield-alt text-success"
            title="User Safeguard Assignments"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Safeguards']
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

        <!-- Page Header Actions (outside the table card) -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.user-safeguard-subpackage.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus-circle me-1"></i> New Assignment
            </a>
            <button type="submit" form="bulk-delete-form" class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure you want to delete selected assignments?')">
                <i class="fas fa-trash-alt me-1"></i> Bulk Delete
            </button>
        </div>

        <!-- Assignments Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-success">
                    <i class="fas fa-list me-2"></i> Assignments List
                </h5>
            </div>

            <div class="card-body">
                <form id="bulk-delete-form" action="{{ route('admin.user-safeguard-subpackage.bulk-destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
<input type="checkbox" id="select-all">
                    <x-admin.data-table 
                        id="user-safeguard-table"
                        :headers="[
                            '#',
                            'ID', 'User', 'Safeguard Compliance', 'Sub Package Project',
                            'Assigned At', 'Actions'
                        ]"
                        :excel="true"
                        :print="true"
                        title="User Safeguard Assignments Export"
                        searchPlaceholder="Search assignments..."
                        resourceName="assignments"
                        :pageLength="10"
                    >
                        @foreach ($assignments as $assignment)
                            <tr>
                                <td>
                                    <input type="checkbox" name="ids[]" value="{{ $assignment->id }}" class="select-row">
                                </td>
                                <td>{{ $assignment->id }}</td>
                                <td>
                                    <span class="badge bg-light text-primary">
                                        {{ $assignment->user->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-info">
                                        {{ $assignment->safeguardCompliance->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
    <span class="badge bg-light text-dark">
        {{ $assignment->subPackageProject->name ?? '—' }}
    </span>

    @if($assignment->subPackageProject)
        <a href="{{ route('admin.user-safeguard-subpackage.tree') }}?sub_package_id={{ $assignment->subPackageProject->id }}" 
           class="btn btn-sm btn-outline-secondary ms-2">
            <i class="fas fa-sitemap me-1"></i> View Tree
        </a>
    @endif
</td>

                                <td>
                                    <span class="badge bg-light text-secondary">
                                        {{ $assignment->created_at->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('admin.user-safeguard-subpackage.destroy', $assignment->id) }}" 
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this assignment?')">
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
                </form>
            </div>
        </div>
    </div>

    <script>
        // ✅ Select all toggle
        document.getElementById('select-all')?.addEventListener('change', function(e) {
            document.querySelectorAll('.select-row').forEach(cb => cb.checked = e.target.checked);
        });
    </script>
</x-app-layout>
