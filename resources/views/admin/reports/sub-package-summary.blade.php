<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb Header -->
        <x-admin.breadcrumb-header
            icon="fas fa-chart-pie text-primary"
            title="Sub-Package Project Reports"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Reports'],
                ['label' => 'Sub-Package Summary'],
            ]"
        />

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary"><i class="fas fa-filter me-2"></i> Filter Options</h5>
            </div>
            <div class="card-body">
                <form method="GET">
                    <!-- Department & Package filters -->
                </form>
            </div>
        </div>

        <!-- Summary Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary"><i class="fas fa-list me-2"></i> Sub-Package Project Summary</h5>
            </div>

            @php
                $complianceCols = collect($subProjectsData->pluck('safeguards')->flatten(1)->unique('compliance'))->pluck('compliance')->all();
                $tableHeaders = array_merge(['#', 'Sub Project', 'Package', 'Department', 'Finance', 'Physical'], array_map(fn($c) => $c . ' Status', $complianceCols));
            @endphp

            <div class="card-body">
                <x-admin.data-table :headers="$tableHeaders" :excel="true" :print="true" resourceName="Sub-Package Summary">
                    @foreach($subProjectsData as $index => $sp)
                        <tr>
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td>{{ $sp['name'] }}</td>
                            <td>
                                <span class="fw-semibold">{{ $sp['package_number'] }}</span>
                                <span class="text-muted small d-block">{{ $sp['package_name'] }}</span>
                            </td>
                            <td>{{ $sp['department'] }}</td>
                            <td class="text-center">
                                <span class="{{ $sp['finance_percent'] < 100 ? 'text-warning' : 'text-success' }}">{{ $sp['finance_percent'] }}%</span>
                                <span class="badge {{ $sp['finance_percent'] < 100 ? 'bg-warning' : 'bg-success' }}">{{ $sp['finance_percent'] < 100 ? 'Pending' : 'Done' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="{{ $sp['physical_percent'] < 100 ? 'text-warning' : 'text-success' }}">{{ $sp['physical_percent'] }}%</span>
                                <span class="badge {{ $sp['physical_percent'] < 100 ? 'bg-warning' : 'bg-success' }}">{{ $sp['physical_percent'] < 100 ? 'Pending' : 'Done' }}</span>
                            </td>

                            <!-- Compliance Status -->
                            @foreach($complianceCols as $compliance)
                                @php $sg = collect($sp['safeguards'])->firstWhere('compliance', $compliance); @endphp
                                <td class="text-center">
                                    @if($sg)
                                        <span class="d-none">{{ $sg['overallPercent'] }}</span>
                                        <span class="{{ $sg['overallPercent'] < 100 ? 'text-warning' : 'text-success' }}">{{ $sg['overallPercent'] }}%</span>
                                        <span class="badge {{ $sg['overallPercent'] < 100 ? 'bg-warning' : 'bg-success' }}">{{ $sg['overallPercent'] < 100 ? 'Pending' : 'Done' }}</span>
                                    @else
                                        <span class="d-none">0</span>
                                        <span class="badge bg-secondary">0%</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>

        <!-- Detailed Monthly Reports -->
        @foreach($subProjectsData as $sp)
            @if(isset($reportData[$sp['id']]))
                @foreach($reportData[$sp['id']] as $complianceName => $report)
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 text-primary">
                                <i class="fas fa-calendar-alt me-2"></i> {{ $sp['name'] }} - {{ $complianceName }}
                                <small class="text-muted">({{ $start->format('M Y') }} → {{ $end->format('M Y') }})</small>
                            </h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>SL No</th>
                                        <th>Item</th>
                                        @foreach ($monthColumns as $month)
                                            <th>{{ $month }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report as $sl => $row)
                                        @php $level = substr_count($sl, '.'); @endphp
                                        <tr>
                                            <td>{{ $sl }}</td>
                                            <td class="text-start" style="padding-left: {{ $level * 20 }}px;">{{ $row['item'] }}</td>
                                            @foreach ($monthColumns as $month)
                                                @php $value = $row['months'][$month]['value'] ?? 0; @endphp
                                                <td>
                                                    @if ($value === 1)
                                                        ✅ Yes
                                                    @elseif ($value === 0)
                                                        ❌ No
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach

    </div>
</x-app-layout>
