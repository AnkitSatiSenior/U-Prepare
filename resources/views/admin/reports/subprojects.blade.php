<x-app-layout>
    <div class="container-fluid py-4">
        <x-admin.breadcrumb-header icon="fas fa-layer-group text-primary" title="All Sub-Projects Report"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Reports', 'route' => 'admin.reports.index'],
                ['label' => 'Sub-Projects'],
            ]" />

        <div class="card shadow-sm mb-4">
            {{-- ==================== FILTER BAR ==================== --}}
            <div class="card-header bg-light border-bottom py-3">
                <form id="filterForm" method="GET" class="row g-3 align-items-end">
                    {{-- Department --}}
                    <div class="col-md-3">
                        <label for="department_id" class="form-label mb-0 fw-semibold text-muted">Department</label>
                        <select name="department_id" id="department_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sub Department --}}
                    <div class="col-md-3">
                        <label for="sub_department_id"
                            class="form-label mb-0 fw-semibold text-muted">Sub-Department</label>
                        <select name="sub_department_id" id="sub_department_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($subDepartments as $subDept)
                                <option value="{{ $subDept->id }}"
                                    {{ request('sub_department_id') == $subDept->id ? 'selected' : '' }}>
                                    {{ $subDept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- District --}}
                    <div class="col-md-3">
                        <label for="district_id" class="form-label mb-0 fw-semibold text-muted">District</label>
                        <select name="district_id" id="district_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($districts as $dist)
                                <option value="{{ $dist->id }}"
                                    {{ request('district_id') == $dist->id ? 'selected' : '' }}>
                                    {{ $dist->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter button --}}
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                        <a href="{{ route('admin.reports.subprojects') }}" class="btn btn-secondary flex-fill">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- ==================== DATA TABLE ==================== --}}
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="text-secondary h2 mb-0">
                    Sub-Projects ({{ $subProjectsData->count() }})
                </h6>

                {{-- Toggle Button --}}
                <button id="toggle-images-btn" class="btn btn-sm btn-outline-primary">
                    Show Images
                </button>
            </div>

            <div class="card-body">
                <x-admin.data-table id="all-sub-projects-table" :headers="array_merge(
                    ['#', 'Package No.', 'Name', 'Contract Value (₹)', 'Physical Progress', 'Finance Progress'],
                    $compliancePhaseHeaders->toArray(),
                )" :excel="true" :print="true"
                    :pageLength="10" :resourceName="'sub-projects'">

                    @foreach ($subProjectsData as $i => $sp)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td title="{{ $sp['name'] }}" style="cursor:pointer">
                                @if (!empty($sp['package_number']) && !empty($sp['contract_id']) && !empty($sp['id']))
                                    <a href="{{ route('admin.contracts.subprojects.show', ['contract' => $sp['contract_id'], 'subProject' => $sp['id']]) }}"
                                        class="text-primary">
                                        {{ $sp['package_number'] }}
                                    </a>
                                @else
                                    {{ $sp['package_number'] ?? 'N/A' }}
                                @endif
                            </td>

                            <td title="{{ $sp['name'] }}" style="min-width: 400px!important;width: 450px!important">
                                {{ $sp['name'] }}
                            </td>
                            <td class="text-end">₹{{ $sp['contractValue'] }}</td>

                            <td class="{{ $sp['physicalPercent'] > 0 ? 'bg-success text-white' : 'bg-danger text-white' }} text-center"
                                style="cursor: pointer">

                                {{-- If ITEM-WISE (BOQ), make it clickable --}}
                                @if (strtolower($sp['type_of_procurement_name'] ?? '') === 'item-rate')
                                    <a href="{{ url('/admin/physical_boq_progress_get') }}?sub_package_project_id={{ $sp['id'] }}"
                                        class="text-white text-decoration-underline fw-bold">
                                        {{ $sp['physicalPercent'] }}%
                                    </a>
                                @elseif (strtolower($sp['type_of_procurement_name'] ?? '') === 'epc')
                                    <a href="{{ url('/admin/physical-epc-report') }}?sub_package_project_id={{ $sp['id'] }}"
                                        class="text-white text-decoration-underline fw-bold">
                                        {{ $sp['physicalPercent'] }}%
                                    </a>
                                @else
                                    {{ $sp['physicalPercent'] }}%
                                @endif

                                <br>

                                <small>
                                    ({{ strtoupper($sp['type_of_procurement_name'] ?? 'N/A') }})
                                </small>

                            </td>


                            <td
                                class="{{ $sp['financePercent'] > 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                {{ $sp['financePercent'] }}%
                            </td>

                            {{-- Safeguard compliances & phases --}}
                            @foreach ($compliancePhaseHeaders as $header)
                                @php
                                    [$compName, $phaseName] = explode(' – ', $header, 2);
                                    $comp = collect($sp['safeguards'])->firstWhere('compliance', $compName);
                                    $phase = $comp ? collect($comp['phases'])->firstWhere('phase', $phaseName) : null;
                                    $percent = $phase['percent'] ?? null;
                                @endphp
                                <td data-sub-id="{{ $sp['id'] }}" data-comp-id="{{ $comp['id'] ?? '' }}"
                                    data-phase-id="{{ $phase['id'] ?? '' }}"
                                    class="text-center {{ $percent > 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                    {{ $percent !== null ? $percent . '%' : '0%' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>

    {{-- ==================== JS: Toggle Images ==================== --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById("toggle-images-btn");
            toggleBtn.dataset.showing = "false";

            toggleBtn.addEventListener("click", function() {
                setTimeout(() => { // delay to ensure table DOM exists
                    const isShowing = toggleBtn.dataset.showing === "true";
                    const tdElements = document.querySelectorAll("td[data-sub-id]");

                    if (!isShowing) {
                        tdElements.forEach(td => {
                            const subId = td.dataset.subId;
                            const compId = td.dataset.compId;
                            const phaseId = td.dataset.phaseId;
                            if (subId) {
                                const link = document.createElement("a");
                                link.href =
                                    `/admin/social-safeguard/gallery?sub_package_project_id=${subId}` +
                                    (compId ? `&safeguard_compliance_id=${compId}` : '') +
                                    (phaseId ? `&contraction_phase_id=${phaseId}` : '');
                                link.textContent = "Images";
                                link.className = "btn btn-sm btn-light mt-1 image-link";
                                td.appendChild(link);
                            }
                        });
                        toggleBtn.textContent = "Hide Images";
                        toggleBtn.dataset.showing = "true";
                    } else {
                        document.querySelectorAll(".image-link").forEach(link => link.remove());
                        toggleBtn.textContent = "Show Images";
                        toggleBtn.dataset.showing = "false";
                    }
                }, 300); // 300ms delay
            });
        });
    </script>
</x-app-layout>
