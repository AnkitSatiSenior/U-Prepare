@props(['data'])

<div class="col-md-12 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <span>S-Curve (Cumulative Project Budget)</span>
        </div>
        <div class="card-body">
            <div id="s-curve-chart" style="height:300px;"></div>
        </div>
    </div>
</div>

<script>
    var sCurveData = @json($data);
    var options = {
        chart: { type: 'line', height: 300 },
        series: [{ name: 'Cumulative Budget', data: sCurveData.map(d => d.cumulative_budget) }],
        xaxis: { categories: sCurveData.map(d => d.date) },
        tooltip: {
            y: { formatter: function(val){ return '$' + val.toLocaleString(); } }
        }
    };
    var chart = new ApexCharts(document.querySelector("#s-curve-chart"), options);
    chart.render();
</script>

