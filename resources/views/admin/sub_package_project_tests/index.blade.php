<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-vial text-primary"
            title="Sub Package Project Tests"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Sub Package Project Tests']
            ]"
        />

        <!-- Alerts -->
        @if(session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            </div>
        @endif

        <!-- Tests Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Tests for "{{ $subProject->name }}"
                </h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus-circle me-1"></i> Add Test
                </button>
            </div>

            <div class="card-body">
                <x-admin.data-table
                    id="tests-table"
                    :headers="['ID', 'Test Name', 'Test Type', 'Actions']"
                    :pageLength="10"
                    :excel="true"
                    :print="true"
                    title="Tests Export"
                >
                    @foreach($tests as $test)
                        <tr id="row-{{ $test->id }}">
                            <td>{{ $test->id }}</td>
                            <td>{{ $test->test_name }}</td>
                            <td>{{ $test->testType->name ?? $test->test_type }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-outline-warning editBtn" data-id="{{ $test->id }}">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger deleteBtn" data-id="{{ $test->id }}">
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
                <input type="hidden" name="sub_package_project_id" value="{{ $subProject->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Test</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Test Type</label>
                            <select name="test_type_id" class="form-control" required>
                                <option value="">-- Select Test Type --</option>
                                @foreach(\App\Models\SubPackageProjectTestType::all() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Test Name</label>
                            <input type="text" name="test_name" class="form-control" required>
                        </div>
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
                        <h5 class="modal-title">Edit Test</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Test Type</label>
                            <select name="test_type_id" class="form-control" id="editTestType" required>
                                <option value="">-- Select Test Type --</option>
                                @foreach(\App\Models\SubPackageProjectTestType::all() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Test Name</label>
                            <input type="text" name="test_name" class="form-control" id="editTestName" required>
                        </div>
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
        // Add
        $('#addForm').submit(function(e){
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: "{{ route('admin.sub_package_project_tests.store') }}",
                type: "POST",
                data: form.serialize(),
                success: function(res){
                    location.reload();
                },
                error: function(err){
                    $('#addError').text(err.responseJSON.errors.test_name || err.responseJSON.errors.test_type_id);
                }
            });
        });

        // Edit
        $('.editBtn').click(function(){
            let id = $(this).data('id');
            $.get("{{ url('admin/sub_package_project_tests') }}/" + id + "/edit", function(data){
                $('#editTestType').val(data.test_type_id);
                $('#editTestName').val(data.test_name);
                $('#editForm').attr('action', "{{ route('admin.sub_package_project_tests.update', '__id') }}".replace('__id', id));
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
                    $('#editError').text(err.responseJSON.errors.test_name || err.responseJSON.errors.test_type_id);
                }
            });
        });

        // Delete
        $('.deleteBtn').click(function(){
            if(!confirm('Are you sure?')) return;
            let id = $(this).data('id');
            $.ajax({
                url: "{{ url('admin/sub_package_project_tests') }}/" + id,
                type: "POST",
                data: {_method: 'DELETE', _token: '{{ csrf_token() }}'},
                success: function(res){
                    $('#row-' + id).remove();
                    alert(res.success);
                }
            });
        });
    </script>
</x-app-layout>
