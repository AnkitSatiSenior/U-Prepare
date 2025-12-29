<x-app-layout>
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <x-admin.breadcrumb-header icon="fas fa-project-diagram text-primary" title="Sub-Project Safeguard Overview"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Safeguard Overview'],
            ]" />

        {{-- Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <x-admin.data-table id="sub-project-overview-table" :headers="array_merge(
                    ['#', 'Sub-Project', 'Safeguard Exists'],
                    $safeguardCompliances->pluck('name')->toArray(),
                )" :excel="true" :print="true"
                    title="Sub-Project Safeguard Overview" searchPlaceholder="Search sub-projects..."
                    resourceName="sub-projects" :pageLength="10">

                    @foreach ($subProjects as $index => $project)
                        @if ($project->safeguard_exists)
                            {{-- ✅ ONLY show if safeguard exists --}}
                            <tr>
                                {{-- Serial --}}
                                <td>{{ $index + 1 }}</td>

                                {{-- Sub-project name --}}
                                <td class="fw-semibold">
                                    {{ $project->name }}
                                </td>

                                {{-- Safeguard Exists --}}
                                <td>
                                    <span class="badge bg-success">Yes</span>
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
                                            <a href="{{ route('admin.social_safeguard_entries.index', [
                                                'project_id' => $project->id,
                                                'compliance_id' => $compliance->id,
                                                'phase_id' => $phase,
                                                'date_of_entry' => $date,
                                            ]) }}"
                                                class="btn btn-sm {{ $done ? 'btn-warning' : 'btn-primary' }}">
                                                {{ $done ? 'Update' : 'Add' }}
                                            </a>
                                        @endif

                                        {{-- Report --}}
                                        @if (canRoute('admin.report.indexReport'))
                                            <a href="{{ route('admin.report.indexReport', [
                                                'project_id' => $project->id,
                                                'compliance_id' => $compliance->id,
                                                'phase_id' => $phase,
                                                'date_of_entry' => $date,
                                            ]) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                Report
                                            </a>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach


                </x-admin.data-table>

            </div>
        </div>
    </div>
</x-app-layout>
