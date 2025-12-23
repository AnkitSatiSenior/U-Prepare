@props([
    'id' => 'chart_table',
    'title' => 'Chart & Table',
    'headers' => [],
    'rows' => [],
    'labels' => [],
    'data' => [], // ✅ old single dataset
    'datasets' => [], // ✅ new multiple dataset support
    'chartTypes' => ['PieChart', 'BarChart', 'ColumnChart', 'LineChart'],
    'excel' => true,
    'print' => true,
    'pageLength' => 10,
    'lengthMenu' => [5, 10, 25, 50, -1],
    'lengthMenuLabels' => ['5', '10', '25', '50', 'All'],
    'searchPlaceholder' => 'Search...',
    'resourceName' => 'entries',
])


<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                <h5 class="mb-0 h1">{{ $title }}</h5>
                <select class="form-control form-control-sm w-auto bg-light text-dark"
                    onchange="drawChart_{{ $id }}(this.value)">
                    @foreach ($chartTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Chart -->
                    <div class="col-md-12 mb-4">
                        <div id="{{ $id }}_chart" style="height:400px;"></div>
                    </div>

                    <!-- Data Table Component -->
                    <div class="col-md-12">
                       <x-admin.data-table :id="$id . '_table'" :headers="$headers" :excel="$excel" :print="$print"
    :pageLength="$pageLength" :lengthMenu="$lengthMenu" :lengthMenuLabels="$lengthMenuLabels"
    :title="$title" :searchPlaceholder="$searchPlaceholder" :resourceName="$resourceName">
    @foreach ($rows as $row)
        <tr>
            @foreach ($row as $col)
                <td 
                    @if(is_numeric($col))
                        class="{{ ($col == 0 || $col === '0%') ? 'bg-light-danger ' : 'bg-light-success ' }}"
                    @endif
                >
                    @if (is_array($col) && isset($col['url']))
    <a href="{{ $col['url'] }}" class="text-primary fw-bold">
        {{ $col['text'] }}
    </a>
@elseif(is_array($col))
    {{ $col['text'] ?? '' }}
@else
    {{ $col }}
@endif

                </td>
            @endforeach
        </tr>
    @endforeach
</x-admin.data-table>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Charts -->
<script type="text/javascript">
    google.charts.load('current', { packages: ['corechart'] });
    google.charts.setOnLoadCallback(init_{{ $id }});

    let data_{{ $id }};
    let datasets_{{ $id }} = @json($datasets);
    let singleData_{{ $id }} = @json($data);
    let labels_{{ $id }} = @json($labels);

    function init_{{ $id }}() {
        if (!labels_{{ $id }}.length) return;

        let headers = ['Department']; // first column (x-axis)
        let rows = [];

        if (datasets_{{ $id }}.length) {
            // ✅ multiple datasets
            headers.push(...datasets_{{ $id }}.map(ds => ds.label));

            labels_{{ $id }}.forEach((label, i) => {
                let row = [label];
                datasets_{{ $id }}.forEach(ds => {
                    row.push(parseFloat(ds.data[i] ?? 0));
                });
                rows.push(row);
            });
        } else {
            // ✅ fallback to single dataset
            headers.push('Value');
            rows = labels_{{ $id }}.map((label, i) => [label, parseFloat(singleData_{{ $id }}[i] ?? 0)]);
        }

        data_{{ $id }} = google.visualization.arrayToDataTable([headers, ...rows]);
        drawChart_{{ $id }}('{{ $chartTypes[0] ?? 'ColumnChart' }}');
    }

    function drawChart_{{ $id }}(chartType) {
        if (!data_{{ $id }}) return;

        const options = {
            title: '{{ $title }}',
            width: '100%',
            height: 400,
            legend: { position: 'top', maxLines: 3 },
            isStacked: datasets_{{ $id }}.length > 1 // stack only when multiple datasets
        };

        let chart;
        switch (chartType) {
            case 'PieChart':
                chart = new google.visualization.PieChart(document.getElementById('{{ $id }}_chart'));
                options.pieHole = 0.3;
                break;
            case 'BarChart':
                chart = new google.visualization.BarChart(document.getElementById('{{ $id }}_chart'));
                break;
            case 'ColumnChart':
                chart = new google.visualization.ColumnChart(document.getElementById('{{ $id }}_chart'));
                break;
            case 'LineChart':
                chart = new google.visualization.LineChart(document.getElementById('{{ $id }}_chart'));
                break;
        }
        chart.draw(data_{{ $id }}, options);
    }
</script>
