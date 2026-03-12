<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-clipboard-list text-primary"
            title="Activity Logs"
            :breadcrumbs="[['route' => 'dashboard', 'label' => 'Home'], ['label' => 'System'], ['label' => 'Logs']]"
        />

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="fas fa-history me-2"></i> System Activity</h5>
                    <form id="filter-form" class="d-flex gap-2">
                        <input type="text" name="model_type" class="form-control form-control-sm" placeholder="Model (e.g. Leader)">
                        <select name="action" class="form-select form-select-sm">
                            <option value="">All Actions</option>
                            <option value="created">Created</option>
                            <option value="updated">Updated</option>
                            <option value="deleted">Deleted</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle w-100" id="activity-log-table">
                    <thead class="table-light">
                        <tr>
                            <th>SL</th>
                            <th>User</th>
                            <th>Model</th>
                            <th>Action</th>
                            <th>Location</th>
                            <th>URL</th>
                            <th>Date</th>
                            <th width="80">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- 🏗️ VIEW MODAL: This displays the actual log content --}}
    <div class="modal fade" id="logViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Log Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>User:</strong> <span id="log-user"></span></div>
                        <div class="col-md-6"><strong>Model:</strong> <span id="log-model"></span></div>
                    </div>
                    <div class="mb-3">
                        <h6><i class="fas fa-exchange-alt me-2"></i> Changes (Before vs After)</h6>
                        <pre id="log-changes" class="p-3 bg-light border rounded" style="max-height: 400px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
    const table = $('#activity-log-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.activity_logs.index') }}",
            data: function (d) {
                d.action = $('select[name="action"]').val();
                d.model_type = $('input[name="model_type"]').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'user', name: 'user.name'},
            {data: 'model', name: 'model_type'},
            {data: 'action_badge', name: 'action'},
            {data: 'location', orderable: false},
            {data: 'url', name: 'url'},
            {data: 'date', name: 'created_at'},
            {data: 'actions', orderable: false, searchable: false}
        ]
    });

    // 🏗️ ARCHITECTURE FIX: Handle View Button Click
    $('#activity-log-table').on('click', '.view-log-btn', function () {
        const logData = $(this).data('log');
        
        $('#log-user').text(logData.user_id ? 'User ID: ' + logData.user_id : 'System');
        $('#log-model').text(logData.model_type + ' (#' + logData.model_id + ')');
        
        // Pretty print the JSON changes
        const changes = {
            before: logData.old_values ? JSON.parse(logData.old_values) : null,
            after: logData.new_values ? JSON.parse(logData.new_values) : null
        };
        
        $('#log-changes').text(JSON.stringify(changes, null, 4));
        
        new bootstrap.Modal($('#logViewModal')).show();
    });

    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });
});
        </script>
</x-app-layout>