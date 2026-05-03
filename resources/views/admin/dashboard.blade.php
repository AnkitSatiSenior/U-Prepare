<x-app-layout>
    <div class="container py-4">


        {{-- ======================
        Dashboard Stats Cards
        ======================= --}}
        <div class="row g-4 dashboard-kpi-row">
            {{-- Total Contracts --}}
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-start border-4 border-primary h-100">
                    <a href="{{ route('admin.contracts.index', ['filter' => 'total_contracts']) }}"
                        class="text-decoration-none">
                        <div class="card-body text-center">
                            <div class="text-muted fw-bold mb-2 fs-5">Total Contracts</div>
                            <div class="fs-4 fw-bold text-primary">
                                {{ $contractsStatus['total'] }}
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Signed Contracts --}}
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-start border-4 border-success h-100">
                    <a href="{{ route('admin.contracts.index', ['filter' => 'signed_contracts']) }}"
                        class="text-decoration-none">
                        <div class="card-body text-center">
                            <div class="text-muted fw-bold mb-2 fs-5">Signed Contracts</div>
                            <div class="fs-4 fw-bold text-success">
                                {{ $contractsStatus['signed'] }}
                                @isset($contractsStatus['signed_percentage'])
                                <span class="fw-normal">({{ $contractsStatus['signed_percentage'] }}%)</span>
                                @endisset
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Commencement Given --}}
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-start border-4 border-warning h-100">
                    <a href="{{ route('admin.contracts.index', ['filter' => 'commencement']) }}"
                        class="text-decoration-none">
                        <div class="card-body text-center">
                            <div class="text-muted fw-bold mb-2 fs-5">Commencement Given</div>
                            <div class="fs-4 fw-bold text-warning">
                                {{ $contractsStatus['commencement'] }}
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Pending Contracts --}}
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-start border-4 border-danger h-100">
                    <div class="card-body text-center">
                        <div class="text-muted fw-bold mb-2 fs-5">Pending Contracts</div>
                        <div class="fs-4 fw-bold text-danger">
                            {{ $contractsStatus['pending'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================
        Charts Section
        ======================= --}}
        <div class="row mt-5 g-4">
            {{-- Departments Budget --}}
            <div class="col-12 col-md-6">
                <x-admin.chart-card id="departments_budget" title="Department-wise Budget Status"
                    :headers="['Department', 'Budget (CR)']" :rows="$departmentsBudget['rows']"
                    :labels="$departmentsBudget['labels']" :data="$departmentsBudget['data']" type="pie" />



            </div>

            {{-- Package Components Budget --}}
            <div class="col-12 col-md-6">
                <x-admin.chart-card id="components_budget" title="Package Components Budget"
                    :headers="['Component', 'Proposed Allocation']" :rows="$components
                        ->map(
                            fn($c) => [
                                [
                                    'text' => $c->name,
                                    'url' => route('admin.package-projects.index', ['package_component_id' => $c->id]),
                                ],
                                formatPriceToCR($c->budget ?? 0), // ✅ Pretty for table
                            ],
                        )
                        ->toArray()" :labels="$components->pluck('name')->toArray()" :data="$components
                        ->pluck('budget') // ✅ Raw numbers for chart
                        ->map(fn($v) => (float) $v ?? 0)
                        ->toArray()" type="pie" />
            </div>

            <div class="col-12 col-md-6">
                <x-admin.chart-card id="departments_projects" title="Department Contract Overview"
                    :headers="$departmentContractOverview['headers']"
                    :rows="$departmentContractOverview['rows']"
                    :labels="$departmentContractOverview['labels']"
                    :data="$departmentContractOverview['data']"
                    :datasets="$departmentContractOverview['datasets']" />
            </div>

            <div class="col-12 col-md-6">
                <x-admin.chart-card id="type_of_procurement_chart" title="Type of Contracts Distribution"
                    :headers="['Procurement Type', 'No. of Packages']" :rows="$procurementPie['rows']"
                    :labels="$procurementPie['labels']" :data="$procurementPie['data']" type="pie" />
            </div>

            <div class="col-12 col-md-6">
                <x-admin.chart-card id="departments_physical_progress" title="Department-wise Physical Progress"
                    :headers="$departmentsPhysicalChart['headers']"
                    :rows="$departmentsPhysicalChart['rows']"
                    :labels="$departmentsPhysicalChart['labels']"
                    :data="$departmentsPhysicalChart['data']"
                    type="pie" />
            </div>

            <div class="col-12 col-md-6">
                <x-admin.chart-card id="departments_financial_progress" title="Department-wise Financial Progress"
                    :headers="$departmentsFinancialChart['headers']"
                    :rows="$departmentsFinancialChart['rows']"
                    :labels="$departmentsFinancialChart['labels']"
                    :data="$departmentsFinancialChart['data']"
                    type="pie" />
            </div>
        </div>

        {{-- ======================
        Procurement Tables
        ======================= --}}
        <div class="card shadow-sm mt-5">
            <div class="card-header border-bottom">
                <ul class="nav nav-tabs card-header-tabs" id="statsTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tab-all" data-bs-toggle="tab" data-bs-target="#content-all"
                            type="button" role="tab">All <br><small class="text-muted">Type of
                                Procurement</small></button>
                    </li>
                    @foreach ($subCategories as $subCat)
                    <li class="nav-item">
                        <button class="nav-link" id="tab-{{ $subCat['id'] }}" data-bs-toggle="tab"
                            data-bs-target="#content-{{ $subCat['id'] }}" type="button" role="tab">
                            {{ $subCat['name'] }} <br>
                            <small class="text-muted">{{ $subCat['category_name'] ?? 'No Category' }}</small>
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body tab-content">
                {{-- Global Procurement Stats --}}
                <div class="tab-pane fade show active" id="content-all" role="tabpanel">
                    <x-admin.data-table :headers="[
                                'ID',
                                'No. Packages',
                                'Type of Contract',
                                'LOA Issued',
                                'Contract Pending',
                                'Contract Signed',
                                'Commencement Given',
                                'To be Rebid',
                            ]" id="type-of-procurement-table" :excel="true" :print="true" :pageLength="10">
                        @foreach ($typeOfProcurementTable as $type)
                        <tr>
                            <td>{{ $type['id'] }}</td>
                            <td>{{ $type['procurement_details_count'] }}</td>
                            <td>{{ $type['name'] }}</td>
                            <td>{{ $type['loa_issued_count'] }}</td>
                            <td>{{ $type['contract_pending_count'] }}</td>
                            <td>{{ $type['signed_contracts_count'] }}</td>
                            <td>{{ $type['commencement_given_count'] }}</td>
                            <td>{{ $type['rebid_count'] }}</td>
                        </tr>
                        @endforeach
                    </x-admin.data-table>

                </div>

                {{-- SubCategories --}}
                @foreach ($subCategoryProcurementTable as $subCat)
                <div class="tab-pane fade" id="content-{{ $subCat['id'] }}" role="tabpanel">
                    <x-admin.data-table :headers="[
                                'ID',
                                'No. Packages',
                                'Procurement Type',
                                'LOA Issued',
                                'Contract Pending',
                                'Contract Signed',
                                'Commencement Given',
                                'To be Rebid',
                            ]" :id="'subcat-' . $subCat['id']" :excel="true" :print="true" :pageLength="5">
                        @forelse ($subCat['procurement_types'] as $ptype)
                        <tr>
                            <td>{{ $ptype['id'] }}</td>
                            <td>{{ $ptype['count'] }}</td>
                            <td>{{ $ptype['name'] }}</td>
                            <td>{{ $ptype['loa_issued_count'] }}</td>
                            <td>{{ $ptype['contract_pending_count'] }}</td>
                            <td>{{ $ptype['signed_contracts_count'] }}</td>
                            <td>{{ $ptype['commencement_given_count'] }}</td>
                            <td>{{ $ptype['rebid_count'] }}</td>
                        </tr>
                        @empty
                        @endforelse
                    </x-admin.data-table>
                </div>
                @endforeach

            </div>
        </div>

        {{-- ======================
        Department Category Counts
        ======================= --}}
        @php
        // Helper for formatting
        $formatCr = fn($raw) => $raw ? number_format($raw / 10000000, 2) : '-';
        $sl = 1;

        // Grand totals
        $grandPhysical = 0;
        $grandFinancialRaw = 0;
        $grandWorkOrders = 0;
        $grandWorkAmountRaw = 0;
        @endphp

        <div class="card shadow-sm mt-5">
            <div class="card-header border-bottom">
                <div class="fw-bold fs-5">Department Category Counts</div>
            </div>

            <div class="card-body">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2">Sl No.</th>
                            <th rowspan="2">Department</th>
                            <th rowspan="2">Sub-Department</th>
                            <th colspan="3">Allocation Target</th>
                            <th colspan="2">Work Order Issued</th>
                        </tr>
                        <tr>
                            <th>Category / Subcategory</th>
                            <th>Physical (No.)</th>
                            <th>Financial (Cr.)</th>
                            <th>No.</th>
                            <th>Amount (Cr.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($departmentCategorySubCategoryCounts as $dept)
                        @php
                        $subDepts = $dept['subdepartments'] ?? [];
                        if (empty($subDepts)) {
                        $subDepts = [[
                        'sub_department_name' => '-',
                        'categories' => $dept['categories'] ?? [],
                        ]];
                        }
                        @endphp

                        @foreach ($subDepts as $subDept)
                        @php
                        $rowCount = $subDept['categories'] ? $subDept['categories']->sum(fn($c) =>
                        max($c['subcategories']->count(), 1)) : 1;
                        $firstRow = true;

                        $subDeptPhysical = 0;
                        $subDeptFinancialRaw = 0;
                        $subDeptWorkOrders = 0;
                        $subDeptWorkAmountRaw = 0;
                        @endphp

                        @foreach ($subDept['categories'] ?? [] as $cat)
                        @foreach ($cat['subcategories'] ?? [] as $sub)
                        <tr>
                            @if ($firstRow)
                            <td rowspan="{{ $rowCount }}">{{ $sl++ }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $dept['department_name'] ?? '-' }}</td>
                            <td rowspan="{{ $rowCount }}">
                                {{ $subDept['sub_department_name'] }}
                            </td>
                            @php $firstRow = false; @endphp
                            @endif

                            <td>
                                {{ $cat['category_name'] ?? '-' }}
                                @if (!empty($sub['sub_category_name']) && $sub['sub_category_name'] !== 'General')
                                → {{ $sub['sub_category_name'] }}
                                @endif
                            </td>
                            <td>{{ $sub['physical_count'] ?? '-' }}</td>
                            <td>{{ $formatCr($sub['financial_total'] ?? 0) }}</td>
                            <td>{{ $sub['work_order_count'] ?? '-' }}</td>
                            <td>{{ $formatCr($sub['work_order_amount'] ?? 0) }}</td>

                        </tr>

                        @php
                        $subDeptPhysical += $sub['physical_count'] ?? 0;
                        $subDeptFinancialRaw += $sub['financial_total'] ?? 0;
                        $subDeptWorkOrders += $sub['work_order_count'] ?? 0;
                        $subDeptWorkAmountRaw += $sub['work_order_amount'] ?? 0;
                        @endphp
                        @endforeach
                        @endforeach

                        {{-- Sub-department Total --}}
                        <tr class="fw-bold table-secondary">
                            <td colspan="4" class="text-end">

                                Total ({{ $subDept['sub_department_name'] ?? '-' }})

                            </td>

                            <td>{{ $subDeptPhysical }}</td>
                            <td>{{ $formatCr($subDeptFinancialRaw) }}</td>
                            <td>{{ $subDeptWorkOrders }}</td>
                            <td>{{ $formatCr($subDeptWorkAmountRaw) }}</td>
                        </tr>

                        @php
                        $grandPhysical += $subDeptPhysical;
                        $grandFinancialRaw += $subDeptFinancialRaw;
                        $grandWorkOrders += $subDeptWorkOrders;
                        $grandWorkAmountRaw += $subDeptWorkAmountRaw;
                        @endphp
                        @endforeach
                        @endforeach

                        {{-- Grand Total --}}
                        <tr class="fw-bold table-dark">
                            <td colspan="4" class="text-end">Grand Total</td>
                            <td>{{ $grandPhysical }}</td>
                            <td>{{ $formatCr($grandFinancialRaw) }}</td>
                            <td>{{ $grandWorkOrders }}</td>
                            <td>{{ $formatCr($grandWorkAmountRaw) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ======================
        Package SubProject Progress
        ======================= --}}





        <div class="card-body">
            <div class="row mb-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Filter by Department</label>
                    <select id="departmentFilter" class="form-select shadow-sm">
                        <option value="">All Departments</option>
                        @if(isset($departmentStats))
                        @foreach($departmentStats as $dept)
                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-8 text-end mt-3 mt-md-0">
                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-columns me-1"></i> Toggle Columns
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" id="columnToggleDropdown"></ul>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="packageStatusTable"
                    class="table table-striped table-bordered table-hover w-100 align-middle">
                    <thead class="table-success text-white">
                        <tr class="text-center align-middle">
                            <th width="5%">S.No.</th>
                            <th width="15%">Department</th>
                            <th>Name of Package</th>
                            <th width="10%">Estimated Value</th>

                            @foreach($reportStatuses as $status)
                            <th>{{ $status }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <script>
            $(document).ready(function () {
        
        // Build the Columns Array
        const tableColumns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center align-middle fw-bold' },
            { data: 'department_name', name: 'department_name', className: 'fw-bold text-center align-middle' },
            { data: 'package_name', name: 'package_name', className: 'text-start align-middle' },
            { data: 'estimated_value', name: 'estimated_value', className: 'text-end align-middle fw-semibold' },
            
            @foreach($reportStatuses as $status)
            {
                name: 'status_{{ Str::slug($status) }}',
                data: 'status_{{ Str::slug($status) }}', 
                orderable: true,
                searchable: false,
                className: 'text-center align-middle',
                render: function (data, type, row) {
                    if (data === 1) {
                        if (type === 'sort' || type === 'type') return 1; 
                        return '<span class="d-none">1</span><span class="text-success" title="Achieved"><i class="fa-solid fa-circle-check fs-5"></i></span>';
                    }
                    if (type === 'sort' || type === 'type') return 0;
                    return '<span class="d-none">0</span><span class="text-muted" style="opacity:0.3;" title="Pending">○</span>';
                }
            },
            @endforeach
        ];

        const table = $('#packageStatusTable').DataTable({
            processing: true,
            serverSide: true, 
            ajax: {
                url: window.location.href, 
                type: 'GET'
            },
            columns: tableColumns,
            responsive: true,
            order: [], // Default state: no initial column sort, rely on backend collection order
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], ['5', '10', '25', '50', 'All']],
            dom:
                '<"row mb-3"<"col-md-6 d-flex align-items-center"l><"col-md-6"f>>' +
                '<"row mb-3"<"col-md-12 text-end"B>>' +
                '<"row"<"col-md-12"tr>>' +
                '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
            buttons: [
                { extend: 'excelHtml5', text: '<i class="fas fa-file-excel me-1"></i> Export to Excel', className: 'btn btn-success btn-sm shadow-sm' },
                { extend: 'print', text: '<i class="fas fa-print me-1"></i> Print Report', className: 'btn btn-primary btn-sm shadow-sm' }
            ],
            language: {
                searchPlaceholder: "Search records...",
                search: "<i class='fas fa-search'></i>",
                processing: '<i class="fas fa-spinner fa-spin fa-2x fa-fw text-primary"></i><span class="sr-only">Loading...</span>'
            },
            initComplete: function () {
                const api = this.api();

                api.columns().every(function (index) {
                    const column = this;
                    const title = $(column.header()).text().trim();
                    const checked = column.visible() ? 'checked' : '';

                    if (title && title !== 'S.No.') { // Don't allow hiding S.No.
                        $('#columnToggleDropdown').append(`
                            <li>
                                <label class="dropdown-item user-select-none" style="cursor: pointer;">
                                    <input type="checkbox" class="me-2 form-check-input column-toggle"
                                           data-column="${index}" ${checked}>
                                    ${title}
                                </label>
                            </li>
                        `);
                    }
                });

                $(document).on('change', '.column-toggle', function () {
                    const column = api.column($(this).data('column'));
                    column.visible(!column.visible());
                });
            }
        });

        // Trigger Yajra Filtering when Department Dropdown Changes
        $('#departmentFilter').on('change', function () {
            let selectedDepartment = $(this).val();
            table.column(1).search(selectedDepartment).draw();
        });
    });
        </script>
    </div>
</x-app-layout>
