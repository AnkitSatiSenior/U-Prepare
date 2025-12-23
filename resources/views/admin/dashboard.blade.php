<x-app-layout>
    <div class="container py-4">

        <div class="mt-4">
            @if (auth()->user() && is_null(auth()->user()->password_updated_at))
                <div class="alert alert-warning alert-dismissible fade show m-0 p-0 d-flex justify-content-between align-items-center"
                    role="alert">
                    <div class="flex-grow-1 px-2 py-2">
                        <strong>⚠️ Security Notice:</strong> Please update your password in your
                        <a href="{{ route('profile.show') }}" class="alert-link">Profile</a>.
                        If you have already updated, you can ignore this message.
                    </div>
                    <button type="button" class="btn btn-close p-2 me-2" data-bs-dismiss="alert" aria-label="Close">
                        <i class="fa fa-window-close" aria-hidden="true"></i>
                    </button>
                </div>
            @endif

        </div>
        {{-- ======================
            Dashboard Stats Cards
        ======================= --}}
        <div class="row g-4 d-none">
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
    <x-admin.chart-card
    id="departments_budget"
    title="Department-wise Budget Status"
    :headers="['Department', 'Budget (CR)']"
    :rows="$departmentsBudget['rows']"
    :labels="$departmentsBudget['labels']"
    :data="$departmentsBudget['data']"
    type="pie"
/>



            </div>

            {{-- Package Components Budget --}}
            <div class="col-12 col-md-6">
                <x-admin.chart-card id="components_budget" title="Package Components Budget" :headers="['Component', 'Proposed Allocation']"
                    :rows="$components
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
@php
function toCR($value)
{
    return round(($value ?? 0) / 10000000, 2); // 1 CR = 1 Crore
}

// Get department stats
$departmentsStats = getDepartmentsStats($scope ?? 'all');

if (($scope ?? 'all') === 'all') {
    // All departments: show total contract value only
    $rows = $departmentsStats->map(fn($d) => [
        [
            'text' => $d->name,
            'url' => route('admin.package-projects.index', [
                'department_id' => $d->id,
                'has_contract' => 1,
            ]),
        ],
        $d->projects_count ?? 0,
        $d->signed_contracts_count ?? 0,
        toCR($d->budget ?? 0),
        $d->budget > 0
            ? toCR($d->total_contract_value ?? 0) . ' (' . round(($d->total_contract_value / $d->budget) * 100, 2) . '%)'
            : toCR(0) . ' (0%)',
        $d->budget > 0
            ? toCR(max(($d->budget ?? 0) - ($d->total_contract_value ?? 0), 0)) . ' (' .
              round((max(($d->budget ?? 0) - ($d->total_contract_value ?? 0), 0) / $d->budget) * 100, 2) . '%)'
            : toCR(0) . ' (0%)',
    ]);

    $labels = $departmentsStats->pluck('name');
    $datasets = [
        [
            'label' => 'Total Contract Value (CR)',
            'data' => $departmentsStats->map(fn($d) => toCR($d->total_contract_value ?? 0)),
        ],
    ];
} else {
    // Single department: show pie of used vs remaining budget
    $dept = $departmentsStats->first();

    $used = $dept->total_contract_value ?? 0;
    $remaining = max(($dept->budget ?? 0) - $used, 0);

    $rows = [
        [
            [
                'text' => $dept->name,
                'url' => route('admin.package-projects.index', [
                    'department_id' => $dept->id,
                    'has_contract' => 1,
                ]),
            ],
            $dept->projects_count ?? 0,
            $dept->signed_contracts_count ?? 0,
            toCR($dept->budget ?? 0),
            toCR($used) . ' (' . round(($used / ($dept->budget ?: 1)) * 100, 2) . '%)',
            toCR($remaining) . ' (' . round(($remaining / ($dept->budget ?: 1)) * 100, 2) . '%)',
        ],
    ];

    $labels = ['Contract Signed (CR)', 'Remaining Budget (CR)'];
    $datasets = [
        [
            'label' => $dept->name,
            'data' => [toCR($used), toCR($remaining)],
        ],
    ];
}
@endphp

<x-admin.chart-card 
    id="departments_projects" 
    title="Department Contract Overview" 
    :headers="($scope ?? 'all') === 'all' 
        ? ['Department', 'Total No. of Projects', 'Total No. of Contracts Signed', 'Total Amount Allocated (CR)', 'Contract Signed Value (CR)', 'Contract to be Signed (CR)']
        : ['Department', 'Total No. of Projects', 'Total No. of Contracts Signed', 'Total Amount Allocated (CR)', 'Contract Signed Value (CR)', 'Contract to be Signed (CR)']"
    :rows="$rows"
    :labels="$labels"
    :datasets="$datasets"
/>

            </div>





            {{-- Department-wise Financial Progress --}}
            <div class="col-12 col-md-6">

 <x-admin.chart-card 
        id="type_of_procurement_chart" 
        title="Type of Contracts Distribution"
        :headers="['Procurement Type', 'No. of Packages']"
        :rows="$procurementPie['rows']"
        :labels="$procurementPie['labels']"
        :data="$procurementPie['data']"
        type="pie" 
    />
           </div>

            {{-- Department-wise Physical Progress --}}
            <div class="col-12 col-md-6">
                <x-admin.chart-card id="departments_physical_progress" title="Department-wise Physical Progress"
                    :headers="['Department', 'Avg Physical Progress %']" :rows="$departmentsPhysicalProgress
                        ->map(fn($d) => [$d['name'], $d['avg_progress'] . '%'])
                        ->toArray()" :labels="$departmentsPhysicalProgress->pluck('name')->toArray()" :data="$departmentsPhysicalProgress->pluck('avg_progress')->toArray()" type="pie" />
            </div>

            {{-- Type of Procurement --}}
            <div class="col-12 col-md-6">
   
        <x-admin.chart-card 
    id="departments_financial_progress"
    title="Department-wise Financial Progress"

    :headers="($scope ?? 'all') === 'all'
        ? ['Department', 'Finance Progress (CR)', 'Finance %']
        : ['Department', 'Budget (CR)', 'Contract Signed (CR)', 'Financial Expenditure (CR)', 'Finance Pending (CR)', 'Finance %']"

    :rows="($scope ?? 'all') === 'all'
        ? $departmentsFinancialProgress
            ->map(fn($d) => [
                $d['name'],
                formatPriceToCR($d['total_finance'] ?? 0),
                ($d['finance_percentage'] ?? 0) . '%',
            ])
            ->toArray()
        : $departmentsFinancialProgress
            ->map(fn($d) => [
                $d['name'],
                formatPriceToCR($d['budget'] ?? 0),
                formatPriceToCR($d['total_contract'] ?? 0),
                formatPriceToCR($d['total_finance'] ?? 0),
                formatPriceToCR($d['pending_finance'] ?? 0),
                ($d['finance_percentage'] ?? 0) . '%',
            ])
            ->toArray()"

    :labels="($scope ?? 'all') === 'all'
        ? $departmentsFinancialProgress->pluck('name')->toArray()
        : ['Financial Expenditure (CR)', 'Finance Pending (CR)']"

    :data="($scope ?? 'all') === 'all'
        ? $departmentsFinancialProgress->map(fn($d) => $d['finance_cr'] ?? 0)->toArray()
        : $departmentsFinancialProgress
            ->map(fn($d) => [
                $d['finance_cr'] ?? 0,
                $d['pending_cr'] ?? 0,
            ])
            ->first()"

    type="pie"
/>

</div>

        </div>

        {{-- ======================
            Procurement Tables
        ======================= --}}
        <div class="card shadow-sm mt-5">
            <div class="card-header border-bottom">
                <ul class="nav nav-tabs card-header-tabs" id="statsTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tab-all" data-bs-toggle="tab"
                            data-bs-target="#content-all" type="button" role="tab">All <br><small
                                class="text-muted">Type of Procurement</small></button>
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
                    <x-admin.data-table 
                            :headers="[
                                'ID',
                                'No. Packages',
                                'Type of Contract',
                                'LOA Issued',
                                'Contract Pending',
                                'Contract Signed',
                                'Commencement Given',
                                'To be Rebid',
                            ]" 
                            id="type-of-procurement-table" 
                            :excel="true"
                            :print="true" 
                            :pageLength="10">
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
                        <x-admin.data-table 
                            :headers="[
                                'ID',
                                'No. Packages',
                                'Procurement Type',
                                'LOA Issued',
                                'Contract Pending',
                                'Contract Signed',
                                'Commencement Given',
                                'To be Rebid',
                            ]" 
                            :id="'subcat-' . $subCat['id']" 
                            :excel="true" 
                            :print="true"
                            :pageLength="5">
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
                    <th colspan="2">Allocation Target</th>
                    <th colspan="2">Work Order Issued</th>
                </tr>
                <tr>
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
                            $rowCount = $subDept['categories'] ? $subDept['categories']->sum(fn($c) => max($c['subcategories']->count(), 1)) : 1;
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
                                            {{ $subDept['sub_department_name']  }}
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
                            <td colspan="3" class="text-end">
                            
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
                    <td colspan="3" class="text-end">Grand Total</td>
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
        <div class="card shadow-sm mt-5">
            <div class="card-header border-bottom">
                <div class="fw-bold fs-5">Package SubProject Progress</div>
            </div>
            <div class="card-body">
                <x-admin.data-table :headers="[
                    'Package No.',
                    'Package Name',
                    'Avg Financial Progress %',
                    'Avg Physical Progress %',
                    'Total SubProjects',
                    'Gallery',
                ]" id="subprojects-table" :excel="true" :print="true"
                    :pageLength="10">
                    @foreach ($packageProjectsSubProjectStats as $stats)
                        <tr>
                            <td>{{ $stats['package_number'] }}</td>
                            <td class="truncate-cell" title="{{ $stats['package_name'] }}">
                                {{ $stats['package_name'] }}
                            </td>
                            <td
                                class="{{ ($stats['avg_financial_progress'] ?? 0) == 0 ? 'bg-light-danger' : 'bg-light-success' }}">
                                {{ $stats['avg_financial_progress'] ?? 0 }}%
                            </td>
                            <td
                                class="{{ ($stats['avg_physical_progress'] ?? 0) == 0 ? 'bg-light-danger' : 'bg-light-success' }}">
                                {{ $stats['avg_physical_progress'] ?? 0 }}%
                            </td>
                            <td>{{ $stats['total_subprojects'] }}</td>
                            <td>
                                <a href="{{ route('admin.package-projects.documents', $stats['id']) }}"
                                    class="btn btn-sm btn-primary">
                                    View Gallery
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>

    </div>
</x-app-layout>
