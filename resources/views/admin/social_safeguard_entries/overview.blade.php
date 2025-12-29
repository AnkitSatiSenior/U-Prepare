<x-app-layout>
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <x-admin.breadcrumb-header
            icon="fas fa-project-diagram text-primary"
            title="Sub-Project Safeguard Overview"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Safeguard Overview'],
            ]"
        />

        {{-- Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <x-admin.data-table
                    id="sub-project-overview-table"
                    :headers="array_merge(
                        ['#', 'Sub-Project', 'Safeguard Exists'],
                        $safeguardCompliances->pluck('name')->toArray()
                    )"
                    :excel="true"
                    :print="true"
                    title="Sub-Project Safeguard Overview"
                    searchPlaceholder="Search sub-projects..."
                    resourceName="sub-projects"
                    :pageLength="10"
                >

                    @foreach ($subProjects as $index => $project)
                        <tr>
                            {{-- Serial --}}
                            <td>{{ $index + 1 }}</td>

                            {{-- Sub-project name --}}
                            <td class="fw-semibold">
                                {{ $project->name }}
                            </td>

                            {{-- Safeguard Exists --}}
                            <td>
                                @if ($project->safeguard_exists)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>

                            {{-- Compliance-wise actions --}}
                            @foreach ($safeguardCompliances as $compliance)
                                @php
                                    $done = $statusMap[$project->id][$compliance->id] ?? false;

                                    $phase = $compliance->contractionPhases->first()?->id ?? 1;
                                @endphp

                                <td class="text-nowrap">

                                    {{-- Add / Update --}}
                                    @if (canRoute('admin.social_safeguard_entries.index'))
                                        <a
                                            href="{{ route('admin.social_safeguard_entries.index', [
                                                'project_id' => $project->id,
                                                'compliance_id' => $compliance->id,
                                                'phase_id' => $phase,
                                                'date_of_entry' => $date,
                                            ]) }}"
                                            class="btn btn-sm {{ $done ? 'btn-warning' : 'btn-primary' }}"
                                        >
                                            {{ $done ? 'Update' : 'Add' }}
                                        </a>
                                    @endif

                                    {{-- Report Entry --}}
                                    @if (canRoute('admin.report.indexReport'))
                                        <a
                                            href="{{ route('admin.report.indexReport', [
                                                'project_id' => $project->id,
                                                'compliance_id' => $compliance->id,
                                                'phase_id' => $phase,
                                                'date_of_entry' => $date,
                                            ]) }}"
                                            class="btn btn-sm btn-outline-warning"
                                        >
                                            Report
                                        </a>
                                    @endif

                                    {{-- View Report --}}
                                    @if (canRoute('admin.social_safeguard_entries.report'))
                                        <a
                                            href="{{ route('admin.social_safeguard_entries.report', [
                                                'project_id' => $project->id,
                                                'compliance_id' => $compliance->id,
                                            ]) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            View
                                        </a>
                                    @endif

                                    {{-- Detailed Report --}}
                                    @if (canRoute('admin.social_safeguard_entries.report_details'))
                                        <a
                                            href="{{ route('admin.social_safeguard_entries.report_details', [
                                                'project_id' => $project->id,
                                                'compliance_id' => $compliance->id,
                                            ]) }}"
                                            class="btn btn-sm btn-outline-secondary"
                                        >
                                            Details
                                        </a>
                                    @endif

                                    {{-- Gallery --}}
                                    @if (canRoute('admin.social_safeguard.gallery'))
                                        <a
                                            href="{{ route('admin.social_safeguard.gallery', [
                                                'sub_package_project_id' => $project->id,
                                                'safeguard_compliance_id' => $compliance->id,
                                                'contraction_phase_id' => $phase,
                                            ]) }}"
                                            class="btn btn-sm btn-outline-info"
                                        >
                                            Gallery
                                        </a>
                                    @endif

                                    {{-- Entry Checking --}}
                                    @if (canRoute('admin.safeguard_entries.index'))
                                        <a
                                            href="{{ route('admin.safeguard_entries.index', [
                                                'sub_package_project_id' => $project->id,
                                                'safeguard_compliance_id' => $compliance->id,
                                            ]) }}"
                                            class="btn btn-sm btn-outline-dark"
                                        >
                                            Entry Check
                                        </a>
                                    @endif

                                    {{-- Monthly Summary --}}
                                    @if (canRoute('admin.report.summary'))
                                        <a
                                            href="{{ route('admin.report.summary', [
                                                'project_id' => $project->id,
                                                'compliance_id' => $compliance->id,
                                                'phase_id' => $phase,
                                            ]) }}"
                                            class="btn btn-sm btn-outline-success"
                                        >
                                            <i class="fa fa-chart-bar"></i>
                                        </a>
                                    @endif

                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                </x-admin.data-table>

            </div>
        </div>
    </div>
</x-app-layout>
