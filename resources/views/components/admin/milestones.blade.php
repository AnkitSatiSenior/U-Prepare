@props(['milestones' => [], 'subProjectsData' => []])

@php
    use Carbon\Carbon;

    $monthData = []; // Month-wise cumulative data
    $tableRows = []; // Milestone-wise table

    $cumPlannedFinance = $cumAchievedFinance = 0;
    $cumPlannedPhysical = $cumAchievedPhysical = 0;

    foreach ($milestones as $ms) {
        $from = isset($ms['from']) ? Carbon::parse($ms['from']) : null;
        $to = isset($ms['to']) ? Carbon::parse($ms['to']) : null;

        $plannedFinance = $ms['plannedFinance'] ?? 0;
        $achievedFinance = $ms['achievedFinance'] ?? 0;
        $plannedPhysical = $ms['plannedPhysical'] ?? 0;
        $achievedPhysical = $ms['achievedPhysical'] ?? 0;

        // Cumulative totals
        $cumPlannedFinance += $plannedFinance;
        $cumAchievedFinance += $achievedFinance;
        $cumPlannedPhysical += $plannedPhysical;
        $cumAchievedPhysical += $achievedPhysical;

        // Milestone-wise table
        $durationText = $from && $to ? $from->format('d M Y') . ' – ' . $to->format('d M Y') : '-';
        $monthsCount = $ms['months'];

        $tableRows[] = [
            'label' => $ms['label'] ?? '-',
            'duration' => $durationText,
            'months' => $monthsCount,
            'plannedFinance' => $plannedFinance,
            'achievedFinance' => $achievedFinance,
            'plannedPhysical' => $plannedPhysical,
            'achievedPhysical' => $achievedPhysical,
        ];

        // Month-wise proportional distribution
        if ($from && $to) {
            $current = $from->copy()->startOfMonth();
            $end = $to->copy()->endOfMonth();
            $totalDays = $from->diffInDays($to) + 1;
            $today = now()->endOfMonth(); // ✅ Current month limit for achieved data

            while ($current->lte($end)) {
                $monthStart = $current->copy()->startOfMonth();
                $monthEnd = $current->copy()->endOfMonth();
                $periodStart = $from->copy()->max($monthStart);
                $periodEnd = $to->copy()->min($monthEnd);
                $daysInMonth = $periodStart->diffInDays($periodEnd) + 1;
                $ratio = $daysInMonth / $totalDays;

                $monthKey = $current->format('M Y');

                // ✅ Planned values — always distributed across full range
                $monthData[$monthKey]['plannedFinance'] =
                    ($monthData[$monthKey]['plannedFinance'] ?? 0) + $plannedFinance * $ratio;
                $monthData[$monthKey]['plannedPhysical'] =
                    ($monthData[$monthKey]['plannedPhysical'] ?? 0) + $plannedPhysical * $ratio;

                // ✅ Achieved values — only up to current month
                if ($current->lte($today)) {
                    $monthData[$monthKey]['achievedFinance'] =
                        ($monthData[$monthKey]['achievedFinance'] ?? 0) + $achievedFinance * $ratio;
                    $monthData[$monthKey]['achievedPhysical'] =
                        ($monthData[$monthKey]['achievedPhysical'] ?? 0) + $achievedPhysical * $ratio;
                }

                $current->addMonth();
            }
        }
    }
@endphp


{{-- 📌 Milestone-wise Progress --}}
<div class="row">
    <div class="col-6">
        <x-admin.card title="Milestones Wise Progres" icon="fas fa-flag-checkered" headerClass="bg-danger text-white">
            <x-admin.data-table id="milestone-table" :headers="[
                'SL No.',
                'Milestone',
                'Duration',
                'Months',
                'Planned Physical %',
                'Achieved Physical %',
                'Planned Finance %',
                'Achieved Finance %',
            ]" :excel="true" :print="true"
                :pageLength="10" resourceName="milestones">

                @foreach ($tableRows as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['label'] }}</td>
                        <td>{{ $row['duration'] }}</td>
                        <td>{{ $row['months'] }}</td>
                        <td>{{ number_format($row['plannedPhysical'] ?? 0, 2) }}%</td>
                        <td class="fw-bold text-success">{{ number_format($row['achievedPhysical'] ?? 0, 2) }}%</td>
                        <td>{{ number_format($row['plannedFinance'] ?? 0, 2) }}%</td>
                        <td class="fw-bold text-success">{{ number_format($row['achievedFinance'] ?? 0, 2) }}%</td>
                    </tr>
                @endforeach

            </x-admin.data-table>
        </x-admin.card>
    </div>
    <div class="col-6">
        <x-admin.card title="Month-wise Progress" icon="fas fa-calendar-alt" headerClass="bg-success text-white">
            <x-admin.data-table id="month-table" :headers="[
                'SL No.',
                'Month',
                'Planned Physical %',
                'Achieved Physical %',
                'Planned Finance %',
                'Achieved Finance %',
            ]" :excel="true" :print="true" :pageLength="12"
                resourceName="months">

                @foreach ($monthData as $i => $data)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $i }}</td>
                        <td>{{ number_format($data['plannedPhysical'] ?? 0, 2) }}%</td>
                        <td>{{ number_format($data['achievedPhysical'] ?? 0, 2) }}%</td>
                        <td>{{ number_format($data['plannedFinance'] ?? 0, 2) }}%</td>
                        <td>{{ number_format($data['achievedFinance'] ?? 0, 2) }}%</td>
                    </tr>
                @endforeach

            </x-admin.data-table>
        </x-admin.card>
    </div>
</div>

{{-- 📅 Month-wise Table --}}



{{-- Chart --}}
<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
        <h5 class="mb-0">📊 Progress Chart</h5>
        <div class="d-flex gap-2 align-items-center">
            <select class="form-control form-control-sm" id="chartType" onchange="drawChart()">
                <option value="LineChart">Line Chart</option>
                <option value="ColumnChart">Column Chart</option>
                <option value="BarChart">Bar Chart</option>
                <option value="SCurve">S-Curve (Smooth)</option>
            </select>
            <select class="form-control form-control-sm" id="progressType" onchange="drawChart()">
                <option value="finance">Finance</option>
                <option value="physical">Physical</option>
            </select>
            <select class="form-control form-control-sm" id="dataType" onchange="drawChart()">
                <option value="month">Month-wise</option>
                <option value="milestone">Milestone-wise</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div id="progress_chart" style="height: 600px;"></div>
    </div>
</div>

{{-- Scripts --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    $(document).ready(function() {
        $('#milestoneTable').DataTable({
            responsive: true,
            pageLength: 5,
            order: [[0, 'asc']]
        });
        $('#monthTable').DataTable({
            responsive: true,
            pageLength: 5,
            order: [[0, 'asc']]
        });
    });

    google.charts.load('current', {
        packages: ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    // Get data from Blade
    var monthData = @json($monthData);
    var milestoneData = @json($tableRows);

    function drawChart() {
        var chartDiv = document.getElementById('progress_chart');
        var chartType = document.getElementById('chartType').value;
        var progressType = document.getElementById('progressType').value;
        var dataType = document.getElementById('dataType').value;

        var data = new google.visualization.DataTable();
        data.addColumn('string', dataType === 'month' ? 'Month' : 'Milestone');
        data.addColumn('number', 'Planned');
        data.addColumn('number', 'Achieved');

        var cumulativePlanned = 0;
        var cumulativeAchieved = 0;

        // --- 1. DATA PROCESSING (Cumulative Logic) ---
        if (dataType === 'month') {
            Object.keys(monthData).forEach(function(key) {
                cumulativePlanned += progressType === 'finance' ? monthData[key].plannedFinance : monthData[key].plannedPhysical;
                cumulativeAchieved += progressType === 'finance' ? monthData[key].achievedFinance : monthData[key].achievedPhysical;

                data.addRow([key, cumulativePlanned, cumulativeAchieved]);
            });
        } else {
            milestoneData.forEach(function(ms) {
                cumulativePlanned += progressType === 'finance' ? ms.plannedFinance : ms.plannedPhysical;
                cumulativeAchieved += progressType === 'finance' ? ms.achievedFinance : ms.achievedPhysical;

                data.addRow([ms.label, cumulativePlanned, cumulativeAchieved]);
            });
        }

        // --- 2. CHART OPTIONS ---
        var options = {
            height: 600,
            // Use a transparent background so it blends with the page
            backgroundColor: 'transparent',
            legend: { position: 'top', alignment: 'center' },
            vAxis: {
                format: "#'%'",
                viewWindow: { min: 0, max: 105 }, // Slight padding at top
                gridlines: { color: '#f3f4f6' }   // Light grid lines like the beautiful example
            },
            hAxis: {
                title: dataType === 'month' ? 'Month' : 'Milestone',
                gridlines: { color: 'transparent' } // Clean look (hide vertical lines)
            },
            colors: ['#3B82F6', '#10B981'], // Tailwind Blue-500 & Green-500
            
            // "Beautiful" Styling Settings:
            curveType: (chartType === 'SCurve') ? 'function' : 'none', // Smooths the line for S-Curve
            areaOpacity: (chartType === 'SCurve') ? 0.2 : 0.0,         // Adds the "Fill" effect only for S-Curve
            lineWidth: 4,  // Thicker lines look more modern
            pointSize: 6,  // Larger points
            pointBackgroundColor: '#fff', // White center for points (cleaner look)
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out'
            }
        };

        var chart;

        // --- 3. CHART SELECTION LOGIC ---
        if (chartType === 'SCurve') {
            // S-Curve uses AreaChart to get the filled color look
            chart = new google.visualization.AreaChart(chartDiv);
        } 
        else if (chartType === 'LineChart') {
            // Standard LineChart (No fill)
            chart = new google.visualization.LineChart(chartDiv);
        } 
        else if (chartType === 'ColumnChart') {
            chart = new google.visualization.ColumnChart(chartDiv);
        } 
        else {
            chart = new google.visualization.BarChart(chartDiv);
        }

        chart.draw(data, options);
    }
</script>