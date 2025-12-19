<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-vial text-primary"
            title="Sub Package Project Test Types"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Test Types']
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

        <!-- Test Types Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Test Types List
                </h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus-circle me-1"></i> Add Test Type
                </button>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="test-types-table"
                    :headers="['ID', 'Name', 'Actions']"
                    :excel="false"
                    :print="false"
                    title="Test Types Export"
                    searchPlaceholder="Search test types..."
                    resourceName="test_types"
                    :pageLength="10"
                >
                    @foreach ($testTypes as $testType)
                        <tr id="row-{{ $testType->id }}">
                            <td>{{ $testType->id }}</td>
                            <td class="name">{{ $testType->name }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-outline-warning editBtn" data-id="{{ $testType->id }}">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger deleteBtn" data-id="{{ $testType->id }}">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="addForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Test Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="name" class="form-control" placeholder="Name" required>
                        <div class="text-danger mt-1" id="addError"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Test Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="name" class="form-control" id="editName" required>
                        <div class="text-danger mt-1" id="editError"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- AJAX Script -->
    <script>
        // Add Test Type
        $('#addForm').submit(function(e){
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: "{{ route('admin.sub_package_project_test_types.store') }}",
                type: "POST",
                data: form.serialize(),
                success: function(res){
                    location.reload();
                },
                error: function(err){
                    $('#addError').text(err.responseJSON.errors.name[0]);
                }
            });
        });

        // Edit Test Type
        $('.editBtn').click(function(){
            let id = $(this).data('id');
            $.get("{{ route('admin.sub_package_project_test_types.index') }}/" + id + "/edit", function(data){
                $('#editName').val(data.name);
                $('#editForm').attr('action', "{{ route('admin.sub_package_project_test_types.index') }}/" + id);
                $('#editModal').modal('show');
            });
        });

        $('#editForm').submit(function(e){
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                success: function(res){
                    location.reload();
                },
                error: function(err){
                    $('#editError').text(err.responseJSON.errors.name[0]);
                }
            });
        });

        // Delete Test Type
        $('.deleteBtn').click(function(){
            if(!confirm('Are you sure?')) return;
            let id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.sub_package_project_test_types.index') }}/" + id,
                type: "POST",
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(res){
                    $('#row-' + id).remove();
                    alert(res.success);
                }
            });
        });
    </script>
</x-app-layout>
