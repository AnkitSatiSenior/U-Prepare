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
        $('#milestoneTable').DataTable({ responsive: true, pageLength: 5, order: [[0, 'asc']] });
        $('#monthTable').DataTable({ responsive: true, pageLength: 5, order: [[0, 'asc']] });
    });

    google.charts.load('current', { packages: ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    var monthData = @json($monthData);
    var milestoneData = @json($tableRows);

    function drawChart() {
        var chartDiv = document.getElementById('progress_chart');
        var chartType = document.getElementById('chartType').value;
        var progressType = document.getElementById('progressType').value;
        var dataType = document.getElementById('dataType').value;

        var data = new google.visualization.DataTable();
        data.addColumn('string', dataType === 'month' ? 'Month' : 'Milestone');
        data.addColumn('number', 'Planned (Ideal S-Curve)'); // Renamed for clarity
        data.addColumn('number', 'Achieved (Actual)');

        // --- 1. PREPARE DATA ---
        var labels = [];
        var achievedValues = [];
        var totalPlanned = 0;
        
        // Extract raw data first
        if (dataType === 'month') {
            Object.keys(monthData).forEach(function(key) {
                labels.push(key);
                // Calculate Total Planned Budget/Physical for the S-Curve Ceiling
                totalPlanned += progressType === 'finance' ? monthData[key].plannedFinance : monthData[key].plannedPhysical;
                // Store actual achieved values
                achievedValues.push(progressType === 'finance' ? monthData[key].achievedFinance : monthData[key].achievedPhysical);
            });
        } else {
            milestoneData.forEach(function(ms) {
                labels.push(ms.label);
                totalPlanned += progressType === 'finance' ? ms.plannedFinance : ms.plannedPhysical;
                achievedValues.push(progressType === 'finance' ? ms.achievedFinance : ms.achievedPhysical);
            });
        }

        // --- 2. GENERATE CURVES ---
        var cumulativeAchieved = 0;
        var totalPoints = labels.length;
        // Sigmoid Steepness (0.5 is standard, higher = steeper middle)
        var k = 0.5; 
        var midpoint = totalPoints / 2;

        for (var i = 0; i < totalPoints; i++) {
            var plannedSValue;

            if (chartType === 'SCurve') {
                // --- THE MAGIC: SIGMOID FORMULA ---
                // This forces the "Planned" line into a perfect S-shape
                // Formula: Y = Total / (1 + e^(-k * (x - midpoint)))
                // We normalize it so it starts near 0 and ends at totalPlanned
                
                // 1. Calculate raw sigmoid (0 to 1)
                var sigmoid = 1 / (1 + Math.exp(-k * (i - midpoint)));
                
                // 2. Scale to Total Planned Amount
                // We adjust the formula slightly to ensure it hits ~0% at start and ~100% at end
                var minSigmoid = 1 / (1 + Math.exp(-k * (0 - midpoint)));
                var maxSigmoid = 1 / (1 + Math.exp(-k * ((totalPoints - 1) - midpoint)));
                var normalizedSigmoid = (sigmoid - minSigmoid) / (maxSigmoid - minSigmoid);
                
                plannedSValue = totalPlanned * normalizedSigmoid;

            } else {
                // Standard Linear Accumulation (Old Way)
                // We approximate this for the 'Linear' view or use specific monthly data if available
                // For this example, we'll just distribute linearly if not S-Curve
                plannedSValue = (totalPlanned / totalPoints) * (i + 1);
            }

            // Accumulate Achieved (Real Data)
            cumulativeAchieved += achievedValues[i];

            // Convert to Percentage for the Chart
            var plannedPercent = (plannedSValue / totalPlanned) * 100;
            var achievedPercent = (cumulativeAchieved / totalPlanned) * 100;

            data.addRow([
                labels[i], 
                plannedPercent, 
                achievedPercent
            ]);
        }

        // --- 3. CHART OPTIONS ---
        var options = {
            height: 600,
            backgroundColor: 'transparent',
            legend: { position: 'top', alignment: 'center' },
            vAxis: {
                title: '% Complete',
                format: "#'%'",
                viewWindow: { min: 0, max: 105 },
                gridlines: { color: '#f3f4f6' }
            },
            hAxis: {
                title: dataType === 'month' ? 'Month' : 'Milestone',
                gridlines: { color: 'transparent' },
                textStyle: { fontSize: 11 }
            },
            colors: ['#3B82F6', '#10B981'], // Blue (Planned), Green (Achieved)
            
            // Visual Styles
            curveType: 'function', // Always smooth
            areaOpacity: (chartType === 'SCurve') ? 0.2 : 0.0,
            lineWidth: 4,
            pointSize: 5,
            pointBackgroundColor: '#fff',
            animation: { startup: true, duration: 1000, easing: 'out' }
        };

        var chart;
        if (chartType === 'SCurve') {
            chart = new google.visualization.AreaChart(chartDiv);
        } else {
            chart = new google.visualization.LineChart(chartDiv);
            options.curveType = 'none'; // Straight lines for non-S-Curve
        }

        chart.draw(data, options);
    }
</script>