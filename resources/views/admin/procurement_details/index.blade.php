<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" title="Procurement Details"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
                ['label' => 'Procurement Details'],
            ]" />

        <!-- Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h4">
                    <i class="fas fa-list me-2"></i> Procurement Status by Package Project
                </h5>
                
            </div>



            <div class="card-body">
                <x-admin.data-table id="package-projects-table" :headers="[
                    'Package',
                    'Category',
                    'Sanction Budge (₹)',
                    'District',
                    'Procurement',
                    'Contracts',
                    'Status',
                    'Actions',
                ]" :excel="true" :print="true"
                    title="Package Projects Export" searchPlaceholder="Search package projects..."
                    resourceName="package-projects" :pageLength="10">

                    @foreach ($packageProjects as $project)
                        <tr>
                            <!-- Package Name & Number -->
                            <td>

                                <div class="d-flex flex-column">
                                    <!-- Main Title -->
                                    <a href="{{ route('admin.package-projects.show', $project->id) }}"
                                        title="{{ $project->package_name }}"
                                        class="fw-bold text-primary mb-1 text-truncate" style="max-width: 400px;">
                                        {{ $project->package_name }}
                                    </a>

                                    <!-- Package Number -->
                                    <span class="text-muted small mb-1">
                                        <i class="fas fa-hashtag"></i> {{ $project->package_number }}
                                    </span>

                                    <div class="d-flex flex-wrap gap-1 mb-1">

                                        <span class="badge bg-{{ $project->dec_approved ? 'warning' : 'secondary' }}">
                                            <i class="fas fa-check-circle"></i> DEC:
                                            {{ $project->dec_approved ? 'Approved' : 'Pending' }}
                                        </span>


                                        <span class="badge bg-{{ $project->hpc_approved ? 'info' : 'secondary' }}">
                                            <i class="fas fa-check-circle"></i> HPC:
                                            {{ $project->hpc_approved ? 'Approved' : 'Pending' }}
                                        </span>


                                        <!-- Implementation Agency -->
                                        @if ($project->department?->name)
                                            <span class="badge bg-success text-white">
                                                <i class="fas fa-building"></i> {{ $project->department->name }}
                                            </span>
                                        @endif
                                    </div>

                            </td>

                            <!-- Category / Department -->
                            <td>
                                @if ($project->category?->name)
                                    <span class="font-weight-bold"> <i class="fas fa-tags"></i>
                                        {{ $project->category->name }} </span>
                                @endif

                                @if ($project->subCategory?->name)
                                    ( {{ $project->subCategory->name }} )
                                @endif

                            </td>

                            <!-- Sanction Budget -->
                            <td class="align-middle">
                                <div class="fw-bold text-success">
                                    {{ formatPriceToCR($project->estimated_budget_incl_gst) }}
                                </div>
                            </td>

                            <!-- Location -->
                            <td>
                                <div class="fw-semibold">
                                    {{ $project->district?->name ?? 'N/A' }}
                                </div>

                                @if ($project->block?->name)
                                    <div class="small text-muted">
                                        BLock : <i class="fas fa-map-marker-alt"></i> {{ $project->block->name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Procurement -->
                            <td class="align-middle">
                                @if ($project->procurementDetail)
                                    <span class="badge bg-success text-dark">
                                        <i class="fas fa-exclamation-circle"></i> Completed
                                    </span>

                                    <div class="fw-semibold">
                                        Method : {{ $project->procurementDetail->method_of_procurement }}
                                    </div>

                                    <div class="fw-semibold">
                                        Type : {{ $project->procurementDetail->typeOfProcurement?->name }}
                                    </div>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-circle"></i> Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Contracts -->
                            <td class="align-middle">
                                @if ($project->contracts->isNotEmpty())
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-info dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $project->contracts->count() }} Contract(s)
                                        </button>
                                        <ul class="dropdown-menu shadow-sm p-2" style="min-width: 300px;">
                                            @foreach ($project->contracts as $contract)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <p class="mb-0 d-flex align-items-cente h5">
                                                        <i class="fas fa-hashtag text-primary me-2"></i> Contract No
                                                    </p>
                                                    <span class="fw-bold h6">{{ $contract->contract_number }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <p class="mb-0 d-flex align-items-cente h5">
                                                        <i class="fas text-primary me-2"> ₹ </i> Contract Value
                                                    </p>
                                                    <span class="fw-bold h6"> ₹
                                                        {{ number_format($contract->contract_value, 2) }} </span>
                                                </li>
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <p class="mb-0 d-flex align-items-cente h5">
                                                        <i class="fas fa-industry text-primary me-2"></i> Firm Name
                                                    </p>
                                                    @if ($contract->contractor)
                                                        <span class="fw-bold h6">
                                                            {{ $contract->contractor->company_name }} </span>
                                                    @endif
                                                </li>
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <p class="mb-0 d-flex align-items-cente h5">
                                                        <i class="fas fa-boxes  text-primary me-2"></i> Sub - Projects
                                                    </p>
                                                    @if ($contract->subProjects->isNotEmpty())
                                                        <span class="fw-bold h6">
                                                            {{ $contract->subProjects->count() }} </span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle"></i> No Contracts
                                    </span>
                                @endif
                            </td>

                            <!-- Approvals & Status -->
                            <td>
                                <div class="d-flex flex-column gap-1">
                                     <span class="badge bg-success">
{{ $project->status }}
                                     </span>
                                    
                                </div>
                            </td>

                            <!-- Actions -->
                            <td>
                                <span class="text-muted" title="Work Program">Work Program</span>
                               <div class="d-flex align-center justify-center gap-1">
                                    <!-- View Button -->
                                    @if ($project->has_work_program)
                                        <a href="{{ route('admin.procurement-work-programs.edit-by-package', [
                                            'package_project_id' => $project->id,
                                            'procurement_details_id' => optional($project->procurementDetail)->id,
                                        ]) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit Work Program">
                                            <i class="fas fa-edit"></i> 
                                        </a> <a
                                            href="{{ route('admin.procurement-work-programs.show.pack', [
                                                'package_project_id' => $project->id,
                                                'procurement_details_id' => optional($project->procurementDetail)->id,
                                            ]) }}"
                                            class="btn btn-sm btn-outline-secondary" title="  View Work Program">
                                            <i class="fas fa-eye"></i> 
                                        </a>
                                    @else
                                        <a href="{{ route('admin.procurement-work-programs.create', [
                                            'package_project_id' => $project->id,
                                            'procurement_details_id' => optional($project->procurementDetail)->id,
                                        ]) }}"
                                            class="btn btn-sm btn-outline-success" title="  Add Work Program">
                                             <i class="fas fa-plus"></i>
                                        </a>
                                    @endif
</div>
<br/>
<span class="text-muted" title="Procurement Details ">Procurement Details</span>
 <div class="d-flex align-center justify-center gap-1">
                                    @if ($project->procurementDetail)
                                        <a href="{{ route('admin.procurement-details.show', $project->procurementDetail) }}"
                                            class="btn btn-sm btn-outline-info" title=" View Procurement">
                                            <i class="fas fa-eye"></i> 
                                        </a>
                                        <a href="{{ route('admin.procurement-details.edit', $project->procurementDetail) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit Procurement">
                                            <i class="fas fa-edit"></i> 
                                        </a>
                                    @else
                                        <a href="{{ route('admin.procurement-details.create', $project) }}"
                                            class="btn btn-sm btn-outline-success" title="Create Procurement">
                                            <i class="fas fa-plus"></i> 
                                        </a>
                                    @endif

</div>


                                
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>

        </div>
    </div>
</x-app-layout>
