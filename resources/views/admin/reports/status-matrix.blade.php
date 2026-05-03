<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-table-cells-large text-primary"
            title="Package Status Matrix"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Reports'],
                ['label' => 'Status Matrix'],
            ]"
        />

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="mb-1 text-primary">Work Status for Packages</h5>
                        <p class="text-muted mb-0 small">Track every package against the current project workflow stages.</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-columns me-1"></i>Columns
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" id="statusMatrixColumnToggle"></ul>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="statusMatrixTable" class="table table-striped table-bordered w-100 align-middle">
                        <thead class="table-light">
                            <tr class="text-center align-middle">
                                <th>S.No.</th>
                                <th>Package Name</th>
                                <th>Department</th>
                                <th>Estimated Value</th>
                                @foreach ($reportStatuses as $status)
                                    <th>{{ $status }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @section('script')
        <script>
            $(function () {
                const tableColumns = [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center fw-bold' },
                    { data: 'package_name', name: 'package_name', className: 'text-start' },
                    { data: 'department_name', name: 'department_name', className: 'text-start' },
                    { data: 'estimated_value', name: 'estimated_value', className: 'text-end fw-semibold' },
                    @foreach ($reportStatuses as $status)
                    {
                        data: 'status_{{ Str::slug($status) }}',
                        name: 'status_{{ Str::slug($status) }}',
                        searchable: false,
                        className: 'text-center',
                        render: function (data, type) {
                            if (type === 'sort' || type === 'type') {
                                return data === 1 ? 1 : 0;
                            }

                            return data === 1
                                ? '<span class="text-success" title="Achieved"><i class="fa-solid fa-circle-check fs-5"></i></span>'
                                : '<span class="text-muted" style="opacity:.35" title="Pending">○</span>';
                        }
                    },
                    @endforeach
                ];

                const table = $('#statusMatrixTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: window.location.href,
                        type: 'GET'
                    },
                    columns: tableColumns,
                    order: [],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    dom:
                        '<"row mb-3"<"col-md-6"l><"col-md-6"f>>' +
                        '<"row mb-3"<"col-md-12 text-end"B>>' +
                        '<"row"<"col-md-12"tr>>' +
                        '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
                    buttons: [
                        { extend: 'excelHtml5', text: '<i class="fas fa-file-excel me-1"></i>Export', className: 'btn btn-success btn-sm' },
                        { extend: 'print', text: '<i class="fas fa-print me-1"></i>Print', className: 'btn btn-primary btn-sm' }
                    ],
                    language: {
                        searchPlaceholder: 'Search packages...',
                        search: "<i class='fas fa-search'></i>",
                        processing: '<i class="fas fa-spinner fa-spin fa-2x fa-fw text-primary"></i><span class="sr-only">Loading...</span>'
                    },
                    initComplete: function () {
                        const api = this.api();

                        api.columns().every(function (index) {
                            const column = this;
                            const title = $(column.header()).text().trim();
                            const checked = column.visible() ? 'checked' : '';

                            if (title !== 'S.No.') {
                                $('#statusMatrixColumnToggle').append(`
                                    <li>
                                        <label class="dropdown-item user-select-none" style="cursor:pointer;">
                                            <input type="checkbox" class="me-2 form-check-input status-column-toggle" data-column="${index}" ${checked}>
                                            ${title}
                                        </label>
                                    </li>
                                `);
                            }
                        });

                        $(document).on('change', '.status-column-toggle', function () {
                            const column = api.column($(this).data('column'));
                            column.visible(!column.visible());
                        });
                    }
                });
            });
        </script>
    @endsection
</x-app-layout>
