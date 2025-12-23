@props([
    'id' => 'data-table',
    'headers' => [],
    'rows' => [],
    'excel' => true,
    'print' => true,
    'pageLength' => 10,
    'lengthMenu' => [5, 10, 25, 50, -1],
    'lengthMenuLabels' => ['5', '10', '25', '50', 'All'],
    'title' => 'Data Export',
    'searchPlaceholder' => 'Search...',
    'resourceName' => 'entries',
])

<div class="row mb-2">
    <div class="col-12 d-flex justify-content-end">
        <!-- Columns dropdown -->
        <div class="dropdown me-1">
            <button class="btn btn-dark btn-sm dropdown-toggle" type="button" id="{{ $id }}-columns-btn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-columns me-1"></i> Columns
            </button>
            <ul class="dropdown-menu dropdown-menu-end" id="{{ $id }}-columns-dropdown"></ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table id="{{ $id }}" class="table table-striped table-bordered" style="width:100%">
                <thead class="table-success">
                    <tr>
                        @foreach ($headers as $header)
                            <th class="text-center align-middle text-white">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
#{{ $id }} tbody td {
    vertical-align: middle;
}
</style>

<script>
$(document).ready(function() {
    const tableId = '#{{ $id }}';
    let actionColumnIndex = -1;

    $(tableId + ' thead th').each(function(index) {
        if ($(this).text().trim().toLowerCase() === 'action') {
            actionColumnIndex = index;
        }
    });

    // Buttons for export
    let buttons = [];
    @if($excel)
    buttons.push({
        extend: 'excelHtml5',
        text: '<i class="fas fa-file-excel me-1"></i> Excel',
        className: 'btn btn-success btn-sm',
        title: '{{ $title }}',
        exportOptions: { columns: actionColumnIndex === -1 ? ':visible' : ':not(:eq(' + actionColumnIndex + '))' }
    });
    @endif

    @if($print)
    buttons.push({
        extend: 'print',
        text: '<i class="fas fa-print me-1"></i> Print',
        className: 'btn btn-primary btn-sm',
        title: '{{ $title }}',
        exportOptions: { columns: actionColumnIndex === -1 ? ':visible' : ':not(:eq(' + actionColumnIndex + '))' }
    });
    @endif

    // Initialize DataTable
    const table = $(tableId).DataTable({
        dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row mb-2"<"col-sm-12"B>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row mt-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        buttons: buttons,
        responsive: true,
        order: [],
        pageLength: {{ $pageLength }},
        lengthMenu: [@json($lengthMenu), @json($lengthMenuLabels)],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "{{ $searchPlaceholder }}",
            lengthMenu: "Show _MENU_ {{ $resourceName }}",
            info: "Showing _START_ to _END_ of _TOTAL_ {{ $resourceName }}",
            infoEmpty: "No {{ $resourceName }} available",
            paginate: {
                previous: '<i class="fas fa-angle-left"></i>',
                next: '<i class="fas fa-angle-right"></i>'
            }
        },
        columnDefs: [
            { targets: actionColumnIndex, orderable: false, className: 'text-center' },
            { targets: '_all', className: 'align-middle' }
        ],
        initComplete: function() {
    const dropdown = $('#package-projects-table-columns-dropdown');
    const table = this.api(); // DataTable instance

    table.columns().every(function(index) {
        const col = this;
        const title = $(col.header()).text();
        // Skip action column
        if (title.toLowerCase() === 'action') return;

        const checked = col.visible() ? 'checked' : '';
        dropdown.append(`
            <li>
                <label class="dropdown-item">
                    <input type="checkbox" class="column-toggle me-2" data-column="${index}" ${checked}>
                    ${title}
                </label>
            </li>
        `);
    });

    // Column toggle event
    $('.column-toggle').on('change', function() {
        const column = table.column($(this).data('column'));
        column.visible(!column.visible());
    });
}

    });
});
</script>
