<x-app-layout>
    <div class="container py-5">

        <!-- Page Title -->
        <h2 class="mb-4 text-primary fw-bold">
            {{ $compliance->name }} - Monthly Report
            <small class="text-muted">({{ $start->format('M Y') }} → {{ $end->format('M Y') }})</small>
        </h2>

        <!-- Filter Form -->
        <form id="report-filter-form" method="GET" class="card card-body mb-4 shadow-sm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" id="start-date" name="start_date" class="form-control"
                        value="{{ request('start_date', $start->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" id="end-date" name="end_date" class="form-control"
                        value="{{ request('end_date', $end->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}"
                        required>

                </div>
                <div class="col-md-3">
                    <label class="form-label">Construction Phase</label>
                    <select name="phase_id" id="phase-id" class="form-control" required>
                        <option value="">-- Select Phase --</option>
                        @foreach ($compliance->contractionPhases as $ph)
                            <option value="{{ $ph->id }}" {{ $ph->id == $phase->id ? 'selected' : '' }}>
                                {{ $ph->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" id="apply-btn" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Apply
                    </button>
                </div>
            </div>
        </form>

        <script>
            document.getElementById('apply-btn').addEventListener('click', function() {
                const projectId = "{{ $subProject->id }}";
                const complianceId = "{{ $compliance->id }}";
                const phaseId = document.getElementById('phase-id').value;
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;

                if (!phaseId) {
                    alert('Please select a Construction Phase');
                    return;
                }

                const url =
                    `{{ url('admin') }}/${projectId}/${complianceId}/${phaseId}/report-summary?start_date=${startDate}&end_date=${endDate}`;
                window.location.href = url;
            });
        </script>

        <!-- Report Table -->
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <!-- Report Table (Using Admin DataTable Component) -->
                <x-admin.data-table id="monthly-report-table" :headers="array_merge(['SL No', 'Item'], $monthColumns)" :excel="true" :print="true"
                    title="Monthly Compliance Report" searchPlaceholder="Search items..." resourceName="report"
                    :pageLength="50">

                    @foreach ($report as $sl => $row)
                        @php
                            // Detect parent/child hierarchy
                            $isParent = collect(array_keys($report))->contains(
                                fn($childSl) => Str::startsWith($childSl, $sl . '.'),
                            );
                            $level = substr_count($sl, '.'); // To add indentation
                        @endphp

                        <tr class="{{ $isParent ? 'table-primary fw-bold' : '' }}">
                            <!-- SL No -->
                            <td>{{ $sl }}</td>

                            <!-- Item with padding for child items -->
                            <td class="text-start" style="padding-left: {{ $level * 20 }}px;">
                                {{ $row['item'] }}
                            </td>

                            <!-- Month Columns -->
                            @foreach ($monthColumns as $month)
                                @php
                                    $monthData = $row['months'][$month] ?? null;
                                    $value = $monthData['value'] ?? null;
                                @endphp

                                <td>
                                    @if ($isParent)
                                        -
                                    @elseif (is_null($monthData))
                                        -
                                    @elseif ($value === 1)
                                        <span class="text-success fw-bold">Yes</span>
                                    @elseif ($value === 0)
                                        <span class="text-danger fw-bold">No</span>
                                    @elseif (in_array($value, [2, 3]))
                                        <span class="text-secondary">N/A</span>
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
