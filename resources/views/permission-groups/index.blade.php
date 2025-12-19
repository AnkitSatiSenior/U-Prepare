<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-lock text-primary" title="Permission Groups" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['label' => 'Permissions'],
        ]" />

        <!-- Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible />
        @endif

        <!-- Permission Groups Table -->
        <div class="card shadow-sm">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-layer-group me-2"></i> Permission Groups
                </h5>

                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                    <i class="fas fa-plus-circle me-1"></i> Create Group
                </button>
            </div>

            <div class="card-body">

                <x-admin.data-table id="permission-groups-table" :headers="['ID', 'Group Name', 'Description', 'Routes Count', 'Actions']" :excel="true" :print="true"
                    title="Permission Groups Export" searchPlaceholder="Search permission groups..."
                    resourceName="Permission Groups" :pageLength="10">

                    @foreach ($groups as $group)
                        <tr>
                            <td>{{ $group->id }}</td>

                            <td>
                                <strong class="text-primary">{{ $group->name }}</strong>
                            </td>

                            <td title="{{ trim(strip_tags($group->description)) }}" style="cursor: pointer;">
                                @if ($group->description)
                                    <span>
                                        {{ \Illuminate\Support\Str::limit($group->description, 150) }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>



                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $group->routes->count() }} Routes
                                </span>
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">

                                    <!-- Manage Routes (NEW separate page SCREEN) -->
                                    <a href="{{ route('admin.permission.groups.routes', $group->id) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Manage Route">
                                        <i class="fas fa-route me-1"></i> Manage Routes
                                    </a>
                                    <!-- View Modal Button -->
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#viewGroupModal-{{ $group->id }}" title="view">
                                        <i class="fas fa-eye me-1"></i> View
                                    </button>

                                    <!-- Edit Modal -->
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editGroupModal-{{ $group->id }}" title="edit">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>

                                    <!-- Delete -->
                                    <form method="POST"
                                        action="{{ route('admin.permission.groups.delete', $group->id) }}"
                                        onsubmit="return confirm('Delete this permission group?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="DELETE">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        <div class="modal fade" id="editGroupModal-{{ $group->id }}">
                            <div class="modal-dialog">
                                <form method="POST"
                                    action="{{ route('admin.permission.groups.update', $group->id) }}">
                                    @csrf

                                    <div class="modal-content">

                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Edit Permission Group</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Group Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $group->name }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control">{{ $group->description }}</textarea>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-primary">Update</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- View Group Modal -->
                        <div class="modal fade" id="viewGroupModal-{{ $group->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title">Permission Group Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>Group Name:</strong>
                                            <div class="text-primary">{{ $group->name }}</div>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Description:</strong>
                                            <div class="text-muted">
                                                {{ $group->description ?: '—' }}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Total Assigned Routes:</strong>
                                            <span class="badge bg-secondary text-white">
                                                {{ $group->routes->count() }}
                                            </span>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach

                </x-admin.data-table>

            </div>
        </div>

    </div>

    <!-- Create Group Modal -->
    <div class="modal fade" id="createGroupModal">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.permission.groups.store') }}">
                @csrf

                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Create Permission Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="Ex: BOQ Module">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" placeholder="Short description"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">Create</button>
                    </div>

                </div>

            </form>
        </div>
    </div>

</x-app-layout>
