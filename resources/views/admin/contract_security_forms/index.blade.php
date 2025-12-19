<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" title="Contract Security Forms Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Contract Security Forms'],
            ]" />

        <!-- Success/Error Alerts -->
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
                    <i class="fas fa-list me-2 text-primary"></i> Contract Security Forms List
                </h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFormModal">
                    <i class="fas fa-plus-circle me-1"></i> Add New Form
                </button>
            </div>

            <!-- Table -->
            <div class="card-body">
                <x-admin.data-table id="contract-security-forms-table"
                    :headers="['#ID', 'Form Name', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Contract Security Forms Export"
                    searchPlaceholder="Search forms..."
                    resourceName="contract-security-forms"
                    :pageLength="10">

                    @forelse ($forms as $form)
                        <tr id="row-{{ $form->id }}">
                            <td>{{ $form->id }}</td>
                            <td class="form-name fw-semibold">{{ $form->name }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Edit -->
                                    <button class="btn btn-sm btn-warning edit-btn"
                                            data-id="{{ $form->id }}"
                                            data-name="{{ $form->name }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.contract-security-forms.destroy', $form->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this form?')">
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

    <!-- Add Form Modal -->
    <div class="modal fade" id="addFormModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.contract-security-forms.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add New Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Form Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter form name" required>
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

    <!-- Edit Form Modal -->
    <div class="modal fade" id="editFormModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Edit Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Form Name</label>
                            <input type="text" name="name" id="editFormName" class="form-control" required>
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

    <script>
        // Open edit modal with values
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;

                document.getElementById('editForm').action = "/admin/contract-security-forms/" + id;
                document.getElementById('editFormName').value = name;

                let editModal = new bootstrap.Modal(document.getElementById('editFormModal'));
                editModal.show();
            });
        });
    </script>
</x-app-layout>
