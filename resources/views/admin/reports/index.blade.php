<x-app-layout>
    <div class="container-fluid">

        <!-- 🧭 Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" title="Package Wise Progress"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Reports'],
            ]" />

        <!-- 🔍 Filters -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form id="filterForm" method="GET" action="{{ route('admin.reports.index') }}"
                    class="row g-3 align-items-end">

                    <!-- Department -->
                    <div class="col-md-3">
                        <label for="department_id" class="form-label fw-semibold text-muted mb-1">Department</label>
                        <select name="department_id" id="department_id" class="form-select select2">
                            <option value="">All</option>
                            @foreach ($departmentsList as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Department -->
                    <div class="col-md-3">
                        <label for="sub_department_id" class="form-label fw-semibold text-muted mb-1">Sub
                            Department</label>
                        <select name="sub_department_id" id="sub_department_id" class="form-select select2">
                            <option value="">All</option>
                            @foreach ($subDepartments as $subDept)
                                <option value="{{ $subDept->id }}"
                                    {{ request('sub_department_id') == $subDept->id ? 'selected' : '' }}>
                                    {{ $subDept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- District -->
                    <div class="col-md-3">
                        <label for="district_id" class="form-label fw-semibold text-muted mb-1">District</label>
                        <select name="district_id" id="district_id" class="form-select select2">
                            <option value="">All</option>
                            @foreach ($districts as $dist)
                                <option value="{{ $dist->id }}"
                                    {{ request('district_id') == $dist->id ? 'selected' : '' }}>
                                    {{ $dist->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary w-50">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- 📊 Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Package Wise Progress Report
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark">
                        Total Projects: {{ $stats['total_projects'] ?? 0 }}
                    </span>
                    <span class="badge bg-light text-dark">
                        Departments: {{ $stats['total_departments'] ?? 0 }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                <x-admin.data-table id="package-projects-table" :headers="[
                    '#',
                    'Package No.',
                    'Sub Projects',
                    'Category',
                    'Sub Category',
                    'District',
                    'Contract No',
                    'Contractor Name',
                    'Contract Value',
                    'Procurement Method',
                    'Commencement Date',
                    'Avg Physical Progress (%)',
                    'Avg Financial Progress (%)',
                    'Total Financial Used (Finacle)',
                ]" :excel="true" :print="true"
                    resourceName="Reports" :pageLength="10">
                    @forelse ($packageProjects as $index => $project)
                        @php
                            // Avg Physical Progress
                            $avgPhysical = round($project->subProjects->avg('physical_progress_percentage'), 2);

                            // Avg Financial Progress
                            $avgFinancial = round($project->subProjects->avg('financial_progress_percentage'), 2);

                            // Total Finacle Used
                            $totalFinacle = $project->subProjects->sum('total_finance_amount');

                            // Avg Expenditure
                            $avgFinacle = $project->subProjects->avg('total_finance_amount');
                        @endphp


                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <!-- Package Number -->
                            <td title="{{ $project->package_name }}">
                                @if ($project->contracts->isNotEmpty())
                                    <a href="{{ route('admin.reports.show', $project->contracts->first()->id) }}"
                                        class="text-primary text-decoration-none">
                                        {{ $project->package_number }}
                                    </a>
                                @else
                                    {{ $project->package_number ?? 'N/A' }}
                                @endif
                            </td>

                            <!-- Sub Projects -->
                            <td style="min-width: 400px !important;width:450px !important">
                                @if ($project->subProjects->isNotEmpty())
                                    <ul class="ps-3 mb-0" style="list-style-type: circle;">
                                        @foreach ($project->subProjects as $sub)
                                            <li class="small mb-1">{{ $sub->name ?? 'Unnamed' }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="badge bg-light-danger text-white">No Sub Projects</span>
                                @endif
                            </td>

                            <!-- Category / Sub Category / District -->
                            <td>{{ $project->category->name ?? 'N/A' }}</td>
                            <td>{{ $project->subCategory->name ?? 'N/A' }}</td>
                            <td>{{ $project->district->name ?? 'N/A' }}</td>

                            <!-- Contracts -->
                            <td>{{ $project->contracts->pluck('contract_number')->join(', ') ?: 'N/A' }}</td>
                            <td>{{ $project->contracts->pluck('contractor.company_name')->join(', ') ?: 'N/A' }}</td>
                            <td>
                                @php $totalValue = $project->contracts->sum('contract_value'); @endphp
                                {{ $totalValue ? formatPriceToCR($totalValue) : 'N/A' }}
                            </td>

                            <!-- Procurement -->
                            <td>{{ $project->procurementDetail->method_of_procurement ?? 'Pending' }}</td>

                            <!-- Commencement Date -->
                            <td>
                                @php $firstContract = $project->contracts->first(); @endphp
                                {{ $firstContract && $firstContract->commencement_date
                                    ? \Carbon\Carbon::parse($firstContract->commencement_date)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <!-- Progress -->
                            <td>{{ $avgPhysical }}%</td>

                            <td>{{ $avgFinancial }}%</td>

                            <td>
                                {{ $totalFinacle > 0 ? formatPriceToCR($totalFinacle) : '0' }}
                            </td>


                        </tr>
                    @empty

                    @endforelse
                </x-admin.data-table>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });
        });
    </script>


</x-app-layout>
