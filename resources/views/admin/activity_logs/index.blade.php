<x-app-layout>
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <x-admin.breadcrumb-header
            icon="fas fa-clipboard-list text-primary"
            title="Activity Logs"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'System'],
                ['label' => 'Activity Logs'],
            ]"
        />

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
                        <input
                            type="text"
                            name="model_type"
                            class="form-control"
                            placeholder="Model type"
                        >

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
                    <table
                        class="table table-bordered table-striped align-middle w-100"
                        id="activity-log-table"
                    >
                        <thead class="table-light">
                            <tr>
                                <th width="60">SL</th>
                                <th>User</th>
                                <th>Model</th>
                                <th width="120">Action</th>
                                <th width="160">Location</th>
                                <th>URL</th>
                                <th width="170">Date</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>


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
                {
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'user',
                    name: 'user.name'
                },
                {
                    data: 'model',
                    name: 'model_type'
                },
                {
                    data: 'action_badge',
                    name: 'action',
                    orderable: false
                },
                {
                    data: 'location',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'url',
                    name: 'url'
                },
                {
                    data: 'date',
                    name: 'created_at'
                },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        {{-- Reload table on filter --}}
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });

    });
</script>
