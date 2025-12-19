<x-app-layout>
    <div class="container-fluid">
        <!-- ✅ Breadcrumb Header -->
        <x-admin.breadcrumb-header 
            icon="fas fa-file-contract text-primary" 
            title="Edit Contract"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.contracts.index', 'label' => 'Contracts'],
                ['label' => 'Edit'],
            ]" 
        />

        <!-- ✅ Error Alerts -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- ✅ Main Card -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-edit me-2"></i> Update Contract Details
                </h5>
            <a href="{{ route('admin.contracts.edit', ['contract' => $contract->id]) }}" 
   class="btn btn-sm btn-outline-secondary">
    <i class="fas fa-arrow-left me-1"></i> Back
</a>

            </div>

            <div class="card-body">
                <form action="{{ route('admin.sub-projects.update-multiple') }}" method="POST">
                    @csrf

                    {{-- ✅ Linked Sub-Projects --}}
                    @if (isset($subProjects) && $subProjects->count())
                        <div class="col-12">
                            <div class="card border mt-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-diagram-project me-2"></i> Linked Sub-Projects
                                        ({{ $subProjects->count() }})
                                    </h6>
                                    <small class="text-muted">
                                        You can review or update contract values here
                                    </small>
                                </div>

                                <div class="card-body table-responsive p-0">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:5%">#</th>
                                                <th>Sub-Project Name</th>
                                                <th>Contract Value (₹)</th>
                                                <th>Latitude</th>
                                                <th>Longitude</th>
                                                <th>Procurement Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($subProjects as $index => $sp)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <input 
                                                            type="text" 
                                                            name="sub_projects[{{ $sp->id }}][name]"
                                                            class="form-control form-control-sm"
                                                            value="{{ old("sub_projects.$sp->id.name", $sp->name) }}">
                                                    </td>
                                                    <td>
                                                        <input 
                                                            type="number" 
                                                            step="0.01" 
                                                            min="0"
                                                            name="sub_projects[{{ $sp->id }}][contract_value]"
                                                            class="form-control form-control-sm"
                                                            value="{{ old("sub_projects.$sp->id.contract_value", $sp->contract_value) }}">
                                                    </td>
                                                    <td>
                                                        <input 
                                                            type="number" 
                                                            step="0.0000001" 
                                                            min="-90" 
                                                            max="90"
                                                            name="sub_projects[{{ $sp->id }}][lat]"
                                                            class="form-control form-control-sm"
                                                            value="{{ old("sub_projects.$sp->id.lat", $sp->lat) }}">
                                                    </td>
                                                    <td>
                                                        <input 
                                                            type="number" 
                                                            step="0.0000001" 
                                                            min="-180" 
                                                            max="180"
                                                            name="sub_projects[{{ $sp->id }}][long]"
                                                            class="form-control form-control-sm"
                                                            value="{{ old("sub_projects.$sp->id.long", $sp->long) }}">
                                                    </td>
                                                    <td>
                                                        {{ $sp->procurementDetail->typeOfProcurement->name ?? '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-save me-1"></i> Save Sub-Projects
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
