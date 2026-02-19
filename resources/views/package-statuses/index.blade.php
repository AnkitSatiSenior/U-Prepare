<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-tasks text-primary"
            title="Package Status Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Package Statuses']
            ]"
        />

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

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Status List
                </h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus-circle me-1"></i> Create Status
                </button>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="package-statuses-table"
                    :headers="['Order', 'Name', 'Status', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Package Statuses Export"
                    searchPlaceholder="Search statuses..."
                    resourceName="package-statuses"
                    :pageLength="10"
                >
                    @foreach ($statuses as $status)
                        <tr>
                            <td>{{ $status->order_by }}</td>
                            
                            <td class="fw-medium">{{ $status->name }}</td>
                            
                            <td>
                                <span class="badge {{ $status->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $status->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal"
                                        data-id="{{ $status->id }}"
                                        data-name="{{ $status->name }}"
                                        data-order="{{ $status->order_by }}"
                                        data-active="{{ $status->is_active }}">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>

                                    <form action="{{ route('admin.package-statuses.destroy', $status->id) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this status?');">
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

    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.package-statuses.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title text-primary"><i class="fas fa-plus-circle me-2"></i>Add New Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Completed" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Order By</label>
                            <input type="number" name="order_by" class="form-control" value="0" required>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="createActive" checked>
                            <label class="form-check-label" for="createActive">
                                Is Active?
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Status</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title text-primary"><i class="fas fa-edit me-2"></i>Edit Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Name</label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Order By</label>
                            <input type="number" name="order_by" id="editOrder" class="form-control" required>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editActive">
                            <label class="form-check-label" for="editActive">
                                Is Active?
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-btn');
            const editForm = document.getElementById('editForm');
            const editName = document.getElementById('editName');
            const editOrder = document.getElementById('editOrder');
            const editActive = document.getElementById('editActive');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Get data from the button
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const order = this.getAttribute('data-order');
                    const isActive = this.getAttribute('data-active') === '1';

                    // Set form action dynamically based on your admin route prefix
                    editForm.action = `/admin/package-statuses/${id}`;

                    // Populate modal fields
                    editName.value = name;
                    editOrder.value = order;
                    editActive.checked = isActive;
                });
            });
        });
    </script>
</x-app-layout>