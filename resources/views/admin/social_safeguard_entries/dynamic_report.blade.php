<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-clipboard-check text-primary" title="Compliance Report"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Reports'],
                ['label' => 'Compliance Report'],
            ]" />

        <!-- Alerts -->
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-filter me-2"></i> Filter Report
                </h5>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="row g-3 align-items-end">
                        <!-- Compliance -->
                        <div class="col-md-3">
                            <label class="form-label">Compliance</label>
                            <select name="compliance_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Select Compliance --</option>
                                @foreach ($compliances as $c)
                                    <option value="{{ $c->id }}" {{ $c->id == $complianceId ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Phase -->
                        <div class="col-md-3">
                            <label class="form-label">Phase</label>
                            <select name="phase_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Select Phase --</option>
                                @foreach ($phases as $p)
                                    <option value="{{ $p->id }}" {{ $p->id == $phaseId ? 'selected' : '' }}>
                                        {{ $p->name }} - {{ $p->is_one_time ?? 0 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Item -->
                        <div class="col-md-3">
                            <label class="form-label">Item Description</label>
                            <select name="item_description" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Select Item --</option>
                                @foreach ($items as $i)
                                    <option value="{{ $i }}" {{ $i == $itemDesc ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- From - To -->
                        <div class="col-md-3">
                            <label class="form-label">From - To</label>
                            <div class="d-flex gap-2">
                                <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}"
                                    class="form-control">
                                <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}"
                                    class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-table me-2"></i> Compliance Report Data
                </h5>
            </div>

            <div class="card-body">
                @if (!empty($report))
                    <x-admin.data-table id="compliance-report-table" :headers="array_merge(['Package', 'Sub-Package'], $monthColumns)" :excel="true"
                        :print="true" title="Compliance Report Export" searchPlaceholder="Search report..."
                        resourceName="compliance-report" :pageLength="25">

                        @php
                            $lastMonth = end($monthColumns);
                            $lastMonthYesTotal = 0;
                            $lastMonthTotal = 0;
                        @endphp

                        @foreach ($report as $row)
                            @php
                                $yesCount = 0;
                                $lastMonthValue = $row['months'][$lastMonth] ?? null;

                                if (!is_null($lastMonthValue)) {
                                    $lastMonthTotal++; // count only rows where last month has a value
                                    if ($lastMonthValue === 1) {
                                        $lastMonthYesTotal++;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $row['package'] }}</td>
                                <td>{{ $row['sub_package'] }}</td>

                                @foreach ($monthColumns as $month)
                                    @php
                                        $value = $row['months'][$month] ?? null;
                                        if ($value === 1) {
                                            $yesCount++;
                                        }
                                    @endphp
                                    <td>
                                        @if (is_null($value))
                                            -
                                        @elseif ($value === 1)
                                            ✅ Yes
                                        @elseif ($value === 0)
                                            ❌ No
                                        @elseif (in_array($value, [2, 3]))
                                            ⚪ N/A
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        <!-- Footer Row -->
                        <tfoot>
                            <tr>
                                <td colspan="{{ 1 + count($monthColumns) }}" class="text-end fw-bold">
                                    Total Compliance Complied ({{ $lastMonth }})
                                </td>
                                <td class="fw-bold text-success">
                                    {{ $lastMonthYesTotal }} / {{ $lastMonthTotal }}
                                </td>
                            </tr>
                        </tfoot>
                    </x-admin.data-table>
                @else
                    <div class="alert alert-info">
                        Please select filters to view the report.
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
