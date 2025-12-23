<x-app-layout>
    <div class="container-fluid py-4">
        @php
            $project = $workProgram?->packageProject;
            $procDetail = $workProgram?->procurementDetail;
        @endphp

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-layer-group text-primary"
            title="Procurement Work Program Details | {{ $project->package_name ?? 'N/A' }}"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Procurement Details', 'route' => 'admin.procurement-details.index'],
                ['label' => 'Show'],
            ]" />

        <!-- Package Project Card -->
        @if($project)
            <x-admin.package-card :packageProject="$project" />
        @endif

        <div class="row mt-4 mb-4">
            <div class="col-md-4">
                <x-admin.procurement-details :procurementDetail="$procDetail"/>

                <!-- Procurement Details Card -->
               
                <!-- Documents (from the first work program, if present) -->
               
            </div>
            <div class="col-md-8">
                <x-admin.work-program :workPrograms="$workPrograms" />
            </div>

            <!-- Work Programs Table -->
        </div>
    </div>
</x-app-layout>
