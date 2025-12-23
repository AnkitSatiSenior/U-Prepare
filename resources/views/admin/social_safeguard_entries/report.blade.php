<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-chart-line text-success" title="Safeguard Report" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['label' => 'Safeguard Report'],
        ]" />

        <!-- Report Summary -->
        <div class="mb-4">
            <div class="alert alert-info shadow-sm rounded-lg h3">
                <strong>Project:</strong> {{ $subProject->name }} <br>
                <strong>Compliance:</strong> {{ $compliance->name }} <br>
                {{-- <strong>Overall Progress:</strong>
                {{ $overallDone }}/{{ $overallTotal }}
                ({{ $overallPercent }}%) --}}
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET"
                    action="{{ route('admin.social_safeguard_entries.report', [$subProject->id, $compliance->id]) }}"
                    class="form-inline gap-2">

                    <label class="mr-2">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                        min="{{ $contract?->commencement_date ?? $subProject->start_date?->format('Y-m-d') }}"
                        max="{{ now()->format('Y-m-d') }}" class="form-control mr-2">

                    <label class="mr-2">End Date:</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                        min="{{ $startDate }}" max="{{ now()->format('Y-m-d') }}" class="form-control mr-2">

                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const startDateInput = document.getElementById("start_date");
                        const endDateInput = document.getElementById("end_date");

                        // Set today's date
                        const today = new Date().toISOString().split("T")[0];

                        // Ensure end date min = start date
                        startDateInput.addEventListener("change", function() {
                            endDateInput.min = this.value;
                            if (endDateInput.value < this.value) {
                                endDateInput.value = this.value; // auto adjust if invalid
                            }
                        });

                        // Prevent user from selecting future date
                        startDateInput.setAttribute("max", today);
                        endDateInput.setAttribute("max", today);
                    });
                </script>

            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <x-admin.data-table id="safeguard-report-table" :headers="['#', 'Phase', 'Total', 'Done', '%']" :excel="true" :print="true"
                    title="Safeguard Report Summary" searchPlaceholder="Search phases..." resourceName="phases"
                    :pageLength="10">

                    @foreach ($phaseReports as $index => $report)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $report['phase'] }}</td>
                            <td>{{ $report['total'] }}</td>
                            <td>{{ $report['done'] }}</td>
                            <td>{{ $report['percent'] }}%</td>
                        </tr>
                    @endforeach

                    {{-- <tr class="font-bold bg-light">
                        <td>Overall</td>
                        <td class="border-none"></td>
                        <td>{{ $overallTotal }}</td>
                        <td>{{ $overallDone }}</td>
                        <td>{{ $overallPercent }}%</td>
                    </tr> --}}
                </x-admin.data-table>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header font-bold">Completion by Phase (Pie)</div>
                    <div class="card-body">
                        <canvas id="phasePieChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header font-bold">Done vs Total (Bar)</div>
                    <div class="card-body">
                        <canvas id="phaseBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const phaseLabels = @json(array_column($phaseReports, 'phase'));
        const phasePercents = @json(array_column($phaseReports, 'percent'));
        const phaseTotals = @json(array_column($phaseReports, 'total'));
        const phaseDones = @json(array_column($phaseReports, 'done'));

        // Pie chart (percent completion)
        new Chart(document.getElementById('phasePieChart'), {
            type: 'pie',
            data: {
                labels: phaseLabels,
                datasets: [{
                    data: phasePercents,
                    backgroundColor: ['#4CAF50', '#FFC107', '#F44336', '#2196F3', '#9C27B0'],
                }]
            }
        });

        // Bar chart (Done vs Total)
        new Chart(document.getElementById('phaseBarChart'), {
            type: 'bar',
            data: {
                labels: phaseLabels,
                datasets: [{
                        label: 'Done',
                        data: phaseDones,
                        backgroundColor: '#4CAF50'
                    },
                    {
                        label: 'Total',
                        data: phaseTotals,
                        backgroundColor: '#2196F3'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</x-app-layout>
