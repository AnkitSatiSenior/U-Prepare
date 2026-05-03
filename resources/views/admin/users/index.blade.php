<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-users text-primary"
            title="Users Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Users']
            ]"
        />

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="mb-1 text-primary">
                            <i class="fas fa-address-book me-2"></i>User Directory
                        </h5>
                        <p class="text-muted mb-0 small">Server-side user listing with search, ordering, pagination, and export-ready structure.</p>
                    </div>

                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Create User
                    </a>
                </div>
            </div>

            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table id="users-table" class="table table-striped table-hover align-middle w-100 users-index-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Sub Department</th>
                                <th>Designation</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>District</th>
                                <th>Date of Birth</th>
                                <th>Date of Joining</th>
                                <th>Qualification</th>
                                <th>Total Work Experience</th>
                                <th>Area of Expertise</th>
                                <th>Procurement Support</th>
                                <th>Research / Citation</th>
                                <th>Previous Experience</th>
                                <th>Email Verified At</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .users-index-table thead th {
            white-space: nowrap;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .users-index-table tbody td {
            white-space: nowrap;
            vertical-align: middle;
        }

        .dataTables_wrapper .dataTables_filter input {
            min-width: 240px;
            border-radius: 0.5rem;
        }

        .dataTables_wrapper .dataTables_length select {
            min-width: 80px;
            border-radius: 0.5rem;
        }
    </style>

    @section('script')
        <script>
            $(function () {
                $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    order: [[1, 'desc']],
                    ajax: "{{ route('admin.users.data') }}",
                    searchDelay: 400,
                    language: {
                        search: '',
                        searchPlaceholder: 'Search users by any visible field',
                        lengthMenu: 'Show _MENU_ users',
                        info: 'Showing _START_ to _END_ of _TOTAL_ users',
                        infoEmpty: 'No users found',
                        processing: 'Loading users...'
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
                        { data: 'id', name: 'id' },
                        { data: 'profile_photo', name: 'profile_photo_path', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'name', name: 'name' },
                        { data: 'username', name: 'username' },
                        { data: 'email', name: 'email' },
                        { data: 'phone_no', name: 'phone_no', defaultContent: 'N/A' },
                        { data: 'role_name', name: 'role_name', defaultContent: 'N/A' },
                        { data: 'department_name', name: 'department_name', defaultContent: 'N/A' },
                        { data: 'sub_department_name', name: 'sub_department_name', defaultContent: 'N/A' },
                        { data: 'designation_title', name: 'designation_title', defaultContent: 'N/A' },
                        { data: 'gender', name: 'gender', defaultContent: 'N/A' },
                        { data: 'status', name: 'status', className: 'text-center' },
                        { data: 'district', name: 'district', defaultContent: 'N/A' },
                        { data: 'dob', name: 'dob' },
                        { data: 'date_of_joining', name: 'date_of_joining' },
                        { data: 'qualification', name: 'qualification', defaultContent: 'N/A' },
                        { data: 'total_work_experience', name: 'total_work_experience', defaultContent: 'N/A' },
                        { data: 'area_of_expertise', name: 'area_of_expertise', defaultContent: 'N/A' },
                        { data: 'procurement_support', name: 'procurement_support', defaultContent: 'N/A' },
                        { data: 'research_publication_citation', name: 'research_publication_citation', defaultContent: 'N/A' },
                        { data: 'previous_experience', name: 'previous_experience', defaultContent: 'N/A' },
                        { data: 'email_verified_at', name: 'email_verified_at' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'updated_at', name: 'updated_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                    ],
                    columnDefs: [
                        { targets: [16, 18, 19, 21], className: 'text-wrap' }
                    ]
                });
            });
        </script>
    @endsection
</x-app-layout>
