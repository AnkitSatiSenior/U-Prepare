<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header 
            icon="fas fa-lock text-primary" 
            title="Contract Security Types Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Contract Security Types'],
            ]" 
        />

        <!-- Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible class="mb-3" />
        @endif
        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible class="mb-3" />
        @endif

        <!-- Table Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h4">
                    <i class="fas fa-list me-2 text-primary"></i> Contract Security Types List
                </h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                    <i class="fas fa-plus-circle me-1"></i> Add New Type
                </button>
            </div>

            <!-- DataTable -->
            <div class="card-body">
                <x-admin.data-table 
                    id="contract-security-types-table"
                    :headers="['#ID', 'Type Name', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Contract Security Types Export"
                    searchPlaceholder="Search types..."
                    resourceName="contract-security-types"
                    :pageLength="10">

                    @forelse ($types as $type)
                        <tr id="row-{{ $type->id }}">
                            <td>{{ $type->id }}</td>
                            <td class="type-name fw-semibold">{{ $type->name }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Edit -->
                                    <button class="btn btn-sm btn-warning edit-btn"
                                            data-id="{{ $type->id }}"
                                            data-name="{{ $type->name }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.contract-security-types.destroy', $type->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        
                    @endforelse
                </x-admin.data-table>
            </div>
        </div>
    </div>

    <!-- Add Type Modal -->
    <div class="modal fade" id="addTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.contract-security-types.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add New Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter type name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Type Modal -->
    <div class="modal fade" id="editTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editTypeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Edit Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type Name</label>
                            <input type="text" name="name" id="editTypeName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script>
        // Open Edit Modal and set values
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;

                document.getElementById('editTypeForm').action = "/admin/contract-security-types/" + id;
                document.getElementById('editTypeName').value = name;

                let editModal = new bootstrap.Modal(document.getElementById('editTypeModal'));
                editModal.show();
            });
        });
    </script>
</x-app-layout>
