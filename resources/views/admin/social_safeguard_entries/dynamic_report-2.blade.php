<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-clipboard-check text-primary"
            title="Compliance Report"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Reports'],
                ['label' => 'Compliance Report'],
            ]"
        />

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-filter me-2"></i> Filter Report
                </h5>
            </div>

            <div class="card-body">
                <form method="GET" id="filterForm">

                    <div class="row g-3">

                        <!-- Department -->
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select" id="departmentSelect">
                                <option value="">-- Select Department --</option>
                                @foreach ($departments as $d)
                                    <option value="{{ $d->id }}" {{ $d->id == $departmentId ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sub-Department -->
                        <div class="col-md-3">
                            <label class="form-label">Sub Department</label>
                            <select name="sub_department_id" class="form-select" id="subDepartmentSelect">
                                <option value="">-- Select Sub Dept --</option>
                                @foreach ($subDepartments as $sd)
                                    <option value="{{ $sd->id }}" {{ $sd->id == $subDepartmentId ? 'selected' : '' }}>
                                        {{ $sd->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

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
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Item Description -->
                        <div class="col-md-3 d-none">
                            <label class="form-label">Item Description</label>
                            <select name="item_description" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Select Item --</option>
                                @foreach ($itemsDropdown as $i)
                                    <option value="{{ $i }}" {{ $i == $itemDesc ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-md-3">
                            <label class="form-label">From - To</label>
                            <div class="d-flex gap-2">
                                <input type="date" name="start_date"
                                       value="{{ $start->format('Y-m-d') }}"
                                       class="form-control">

                                <input type="date" name="end_date"
                                       value="{{ $end->format('Y-m-d') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Apply Filter
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-table me-2"></i> Compliance Report
                </h5>
            </div>

            <div class="card-body">

                @if (!empty($report) && count($subPackages))

                    <x-admin.data-table
                        id="compliance-report-table"
                        :headers="array_merge(['SL No', 'Item Description'], array_keys($subPackages->toArray()))"
                        :excel="true"
                        :print="true"
                        title="Compliance Report Export"
                        searchPlaceholder="Search report..."
                        resourceName="Compliance Report"
                        :pageLength="25"
                    >
                        @foreach ($report as $row)
                            <tr class="{{ $row['is_parent'] ? 'table-primary' : '' }}">
                                <td>{{ $row['sl_no'] }}</td>

                                <td>
                                    {!! $row['is_parent']
                                        ? '<strong>' . $row['item_description'] . '</strong>'
                                        : $row['item_description'] !!}
                                </td>

                                @foreach ($subPackages as $pkgNumber => $subPkg)
                                    @php $value = $row[$pkgNumber] ?? null; @endphp
                                    <td class="text-center">
                                        @if ($value === 1)
                                            <span>Yes</span>
                                        @elseif ($value === 0)
                                            <span>No</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </x-admin.data-table>

                @else
                    <p class="text-muted text-center">No data found for selected filters.</p>
                @endif

            </div>
        </div>

    </div>

    <!-- AJAX for Sub Department -->
    <script>
        document.getElementById('departmentSelect').addEventListener('change', function () {

            const deptId = this.value;
            const subDeptSelect = document.getElementById('subDepartmentSelect');

            subDeptSelect.innerHTML = `<option value="">Loading...</option>`;

            if (!deptId) {
                subDeptSelect.innerHTML = `<option value="">-- Select Sub Dept --</option>`;
                return;
            }

            fetch(`/api/get-sub-departments/${deptId}`)
                .then(res => res.json())
                .then(data => {
                    subDeptSelect.innerHTML = `<option value="">-- Select Sub Dept --</option>`;
                    data.forEach(item => {
                        subDeptSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                });
        });
    </script>

</x-app-layout>
