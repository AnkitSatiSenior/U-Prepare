<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header 
            icon="fas fa-shield-alt text-primary" 
            title="Social Safeguard Summary Report" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Reports'],
                ['label' => 'Social Safeguards'],
            ]" 
        />

        <!-- Filter Form -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="date" name="start_date" class="form-control"
                               value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="end_date" class="form-control"
                               value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Report Overview
                </h5>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="social-safeguard-table"
                    :headers="array_merge(
                        ['#','Package No.','Sub Project','Contract','Contractor'],
                        $compliancePhaseHeaders->toArray()
                    )"
                    :excel="true" 
                    :print="true" 
                    resourceName="Social Safeguards" 
                    :pageLength="10"
                >
                    @foreach ($summaryData as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['package_number'] }}</td>
                            <td>{{ $row['sub_project_name'] }}</td>
                            <td>{{ $row['contract_number'] }}</td>
                            <td>{{ $row['contractor'] }}</td>

                            @foreach ($compliancePhaseHeaders as $header)
                                @php
                                    $parts = explode(' – ', $header);
                                    $complianceName = $parts[0];
                                    $phaseName = $parts[1] ?? '';
                                    $phaseData = collect($row['safeguards'])
                                        ->firstWhere('compliance', $complianceName)['phases']
                                        ?? [];
                                    $percent = collect($phaseData)->firstWhere('phase', $phaseName)['percent'] ?? 0;
                                @endphp
                                <td>{{ $percent }}%</td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
