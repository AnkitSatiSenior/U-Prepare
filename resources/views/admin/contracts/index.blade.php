<x-app-layout>
    <div class="container-fluid">

        <!-- ✅ Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" title="Contracts Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Contracts'],
            ]" />

        <!-- ✅ Flash Messages -->
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            </div>
        @endif

        <!-- ✅ Summary Cards -->
        <div class="row mb-4">
            <!-- Total Contracts -->
            <div class="col-md-3">
                <a href="{{ route('admin.contracts.index', ['filter' => 'total_contracts']) }}"
                    class="text-decoration-none">
                    <div
                        class="card shadow-sm border-0 h-100 {{ $filter === 'total_contracts' || is_null($filter) ? 'bg-primary text-white' : 'bg-light' }}">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6>Total Contracts</h6>
                                <h4>{{ $counts['total_contracts'] }}</h4>
                            </div>
                            <i class="fas fa-file-contract fa-2x"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Active Securities -->
            <div class="col-md-3">
                <a href="{{ route('admin.contracts.index', ['filter' => 'active']) }}" class="text-decoration-none">
                    <div
                        class="card shadow-sm border-0 h-100 {{ $filter === 'active' ? 'bg-success text-white' : 'bg-light' }}">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6>Active Securities</h6>
                                <h4>{{ $counts['active'] }}</h4>
                            </div>
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Expired Securities -->
            <div class="col-md-3">
                <a href="{{ route('admin.contracts.index', ['filter' => 'expired']) }}" class="text-decoration-none">
                    <div
                        class="card shadow-sm border-0 h-100 {{ $filter === 'expired' ? 'bg-danger text-white' : 'bg-light' }}">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6>Expired Securities</h6>
                                <h4>{{ $counts['expired'] }}</h4>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- To Be Expired Securities -->
            <div class="col-md-3">
                <a href="{{ route('admin.contracts.index', ['filter' => 'expiring_soon']) }}"
                    class="text-decoration-none">
                    <div
                        class="card shadow-sm border-0 h-100 {{ $filter === 'expiring_soon' ? 'bg-warning text-dark' : 'bg-light' }}">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6>To Be Expired Securities</h6>
                                <h4>{{ $counts['expiring_soon'] }}</h4>
                            </div>
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>



        <!-- ✅ Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Contracts Overview
                </h5>

            </div>

            <div class="card-body">
                <x-admin.data-table id="package-projects-table" :headers="[
                    'Package',
                    'Category',
                    'Sanction Budget (₹)',
                    'District',
                    'Procurement',
                    'Contracts',
                    'Securities',
                    'Actions',
                ]" :excel="true" :print="true"
                    title="Package Projects Export" searchPlaceholder="Search package projects..."
                    resourceName="package-projects" :pageLength="10">
                    @foreach ($packageProjects as $project)
                        <tr>
                            <!-- Package Name & Number -->
                            <td style="max-width: 250px;">

                                @if ($project->contracts->isNotEmpty())
                                    @foreach ($project->contracts as $contract)
                                        <a href="{{ route('admin.contracts.show', $contract) }}"
                                            title="{{ $project->package_name }}"
                                            class="fw-bold text-primary text-truncate d-block">

                                            {{ $project->package_name }}
                                        </a>
                                    @endforeach
                                @else
                                    <a href="{{ route('admin.package-projects.show', $project->id) }}"
                                        title="{{ $project->package_name }}"
                                        class="fw-bold text-primary text-truncate d-block">
                                        {{ $project->package_name }}
                                    </a>
                                @endif
                                </a>
                                <span class="text-muted small d-block text-truncate"
                                    title="{{ $project->package_number }}">
                                    <i class="fas fa-hashtag"></i> {{ $project->package_number }}
                                </span>

                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    <span class="badge bg-{{ $project->dec_approved ? 'warning' : 'secondary' }}">
                                        <i class="fas fa-check-circle"></i> DEC:
                                        {{ $project->dec_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                    <span class="badge bg-{{ $project->hpc_approved ? 'info' : 'secondary' }}">
                                        <i class="fas fa-check-circle"></i> HPC:
                                        {{ $project->hpc_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                    @if ($project->department?->name)
                                        <span class="badge bg-success text-white">
                                            <i class="fas fa-building"></i> {{ $project->department->name }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Category -->
                            <td style="max-width: 150px;">
                                @if ($project->category?->name)
                                    <span class="fw-bold text-truncate d-block">{{ $project->category->name }}</span>
                                @endif
                                @if ($project->subCategory?->name)
                                    <span class="text-muted small d-block">({{ $project->subCategory->name }})</span>
                                @endif
                            </td>

                            <!-- Sanction Budget -->
                            <td class="align-middle text-success fw-bold">
                                {{ formatPriceToCR($project->estimated_budget_incl_gst) }}
                            </td>

                            <!-- Location -->
                            <td style="max-width: 150px;">
                                <div>{{ $project->district?->name ?? 'N/A' }}</div>
                                @if ($project->block?->name)
                                    <div class="text-muted small">Block: {{ $project->block->name }}</div>
                                @endif
                            </td>

                            <!-- Procurement -->
                            <td class="align-middle">
                                @if ($project->procurementDetail)
                                    <span class="badge bg-success mb-1"><i class="fas fa-check-circle"></i>
                                        Completed</span>
                                    <div>Method: {{ $project->procurementDetail->method_of_procurement }}</div>
                                    <div>Type: {{ $project->procurementDetail->typeOfProcurement?->name }}</div>
                                @else
                                    <span class="badge bg-warning"><i class="fas fa-exclamation-circle"></i>
                                        Pending</span>
                                @endif
                            </td>

                            <!-- Contracts -->
                            <td class="align-middle">
                                @if ($project->contracts->isNotEmpty())
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-info dropdown-toggle w-100" type="button"
                                            data-toggle="dropdown">
                                            {{ $project->contracts->count() }} Contracts
                                        </button>
                                        <ul class="dropdown-menu shadow-sm p-3">
                                            @foreach ($project->contracts as $contract)
                                                <li
                                                    class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                    <!-- Contract Number -->
                                                    <div class="text-truncate" style="max-width: 150px;">
                                                        <h6 class="mb-0 text-primary">
                                                            <i class="fas fa-hashtag me-1"></i>
                                                            {{ $contract->contract_number }}
                                                        </h6>
                                                    </div>

                                                    <!-- Contract Value -->
                                                    <div>
                                                        <h5 class="mb-0 fw-bold text-end">
                                                            {{ formatPriceToCR($contract->contract_value, 2) }}
                                                        </h5>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>

                                    </div>
                                @else
                                    <span class="badge bg-secondary">No Contracts</span>
                                @endif
                            </td>

                            <!-- Securities -->
                            <td>
                                @php $hasSecurity = false; @endphp

                                @foreach ($project->contracts as $contract)
                                    @php
                                        $activeCount = $contract->active_securities?->count() ?? 0;
                                        $expiredCount = $contract->expired_securities?->count() ?? 0;
                                    @endphp

                                    @if ($activeCount > 0)
                                        @php $hasSecurity = true; @endphp
                                        <span class="badge bg-success me-1">
                                            Active: {{ $activeCount }}
                                        </span>
                                    @endif

                                    @if ($expiredCount > 0)
                                        @php $hasSecurity = true; @endphp
                                        <span class="badge bg-danger me-1">
                                            Expired: {{ $expiredCount }}
                                        </span>
                                    @endif
                                @endforeach

                                @if (!$hasSecurity)
                                    <span class="badge bg-warning text-dark">
                                        No Security
                                    </span>
                                @endif
                            </td>


                            <!-- Actions -->
                            <td class="align-middle text-center">
                                @if ($project->contracts->isNotEmpty())
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.contracts.show', $contract) }}"
                                            class="btn btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.contracts.edit', $contract) }}"
                                            class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.contracts.destroy', $contract) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"
                                                onclick="return confirm('Are you sure?')" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <a href="{{ route('admin.contracts.securities.index', $contract) }}"
                                        class="btn btn-sm btn-outline-success mt-1" title="Manage Securities">
                                        <i class="fas fa-shield-alt"></i>
                                    </a>
                                @else
                                    <a href="{{ route('admin.contracts.create', ['package_project_id' => $project->id]) }}"
                                        class="btn btn-sm btn-outline-primary" title="Create Contract">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
