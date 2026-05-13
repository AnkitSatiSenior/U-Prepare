<x-app-layout>
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <x-admin.breadcrumb-header icon="fas fa-clipboard-list text-primary" title="Activity Logs" :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'System'],
                ['label' => 'Activity Logs'],
            ]" />

        {{-- Alerts --}}
        @if (session('success'))
        <div class="row mb-3">
            <div class="col-md-12">
                <x-alert type="success" :message="session('success')" dismissible />
            </div>
        </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list me-2"></i> Activity Logs
                    </h5>

                    {{-- Filters (Ajax based) --}}
                    <form id="filter-form" class="d-flex gap-2 flex-wrap">
                        <input type="text" name="model_type" class="form-control" placeholder="Model type">

                        <select name="action" class="form-select">
                            <option value="">All Actions</option>
                            <option value="created">Created</option>
                            <option value="updated">Updated</option>
                            <option value="deleted">Deleted</option>
                        </select>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle w-100" id="activity-log-table">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">SL</th>
                                <th width="15%">User</th>
                                <th width="15%">Model</th>
                                <th width="10%">Action</th>
                                <th width="15%">Location</th>
                                <th width="15%">URL</th>
                                <th width="15%">Date</th>
                                <th width="10%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- SYSTEM MODAL: Activity Log Details --}}
    <div class="modal fade" id="viewLogModal" tabindex="-1" aria-labelledby="viewLogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-primary" id="viewLogModalLabel">
                        <i class="fas fa-search me-2"></i> Activity Payload Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <strong>Target Model:</strong> <span id="modal-model-type" class="badge bg-secondary"></span>
                        <strong class="ms-3">Action:</strong> <span id="modal-action" class="badge bg-info text-dark"></span>
                    </div>
                    <hr>
                    <h6>Data Payload:</h6>
                    {{-- Preformatted block for JSON syntax highlighting --}}
                    <pre class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code id="modal-payload-data"></code></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function () {
        const table = $('#activity-log-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            order: [[6, 'desc']], // Date column
            ajax: {
                url: "{{ route('admin.activity_logs.index') }}",
                data: function (d) {
                    d.action = $('select[name="action"]').val();
                    d.model_type = $('input[name="model_type"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'user', name: 'user.name' },
                { data: 'model', name: 'model_type' },
                { data: 'action_badge', name: 'action', orderable: false },
                { data: 'location', name: 'location', orderable: false, searchable: false },
                // Required to prevent the DataTables 'unknown parameter url' crash
                { data: 'url', name: 'url', orderable: false }, 
                { data: 'date', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });

        // Event Delegation for View Action Modal
        $('#activity-log-table tbody').on('click', '.view-log-btn', function () {
            const button = $(this);
            
            // Extract the changes JSON
            const rawPayload = button.attr('data-payload');
            const modelType = button.data('model');
            const actionType = button.data('action');

            $('#modal-model-type').text(modelType || 'N/A');
            $('#modal-action').text(actionType || 'Unknown');

            const codeBlock = $('#modal-payload-data');
            
            try {
                // Handle empty or missing payload
                if (!rawPayload || rawPayload === 'null' || rawPayload === '[]' || rawPayload === '{}' || rawPayload === '""') {
                    codeBlock.html('<i>No changes recorded for this action.</i>');
                } else {
                    // This will parse the nested {"new": {...}} or {"old": {...}} objects
                    let parsedData = JSON.parse(rawPayload);
                    
                    // If the database stored a stringified JSON string inside the JSON (double encoding),
                    // we parse it a second time to ensure it expands nicely in the UI.
                    if (typeof parsedData === 'string') {
                        parsedData = JSON.parse(parsedData);
                    }
                    
                    // Format with 4-space indentation for readability
                    codeBlock.text(JSON.stringify(parsedData, null, 4));
                }
            } catch (e) {
                // Graceful fallback if data isn't pure JSON
                codeBlock.text(rawPayload || 'Unparseable data.');
                console.warn("Failed to parse log payload JSON:", e);
            }

            const modal = new bootstrap.Modal(document.getElementById('viewLogModal'));
            modal.show();
        });
    });
</script>
</x-app-layout>