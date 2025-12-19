<x-app-layout>
    <div class="container-fluid py-4">

        <!-- ==================== Breadcrumb ==================== -->
        <x-admin.breadcrumb-header icon="fas fa-layer-group text-primary" :title="'Sub-Project Details | ' . $subProjectData['name']" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['label' => 'Contracts', 'route' => 'admin.contracts.index'],
            ['label' => 'Contract Report'],
            ['label' => 'Sub-Project'],
        ]" />

        <!-- ==================== Tabs ==================== -->
        <ul class="nav nav-tabs justify-content-start mb-4 border-0" id="subProjectTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="details-tab" data-bs-toggle="tab" data-bs-target="#details"
                    type="button" role="tab" aria-controls="details" aria-selected="true">
                    <i class="fas fa-cube me-1"></i> Sub-Project Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="remarks-tab" data-bs-toggle="tab" data-bs-target="#remarks"
                    type="button" role="tab" aria-controls="remarks" aria-selected="false">
                    <i class="fas fa-comments me-1"></i> Remarks
                </button>
            </li>
        </ul>

        <!-- ==================== Tab Content ==================== -->
        <div class="tab-content" id="subProjectTabsContent">

            <!-- ========== DETAILS TAB ========== -->
            <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                <div class="row g-4">

                    <!-- 🔹 Sub-Project Info -->
                    <div class="col-12">
                        <x-admin.card title="Sub-Project Info" icon="fas fa-cube"
                            headerClass="bg-primary text-white h2">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-semibold h4"><i class="fas fa-tag text-primary me-2"></i>Name</span>
                                    <span class="fw-bold h4">{{ $subProjectData['name'] ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-semibold h4"><i
                                            class="fas fa-money-bill-wave text-success me-2"></i>Contract Value</span>
                                    <span
                                        class="fw-bold text-success h4">₹{{ number_format($subProjectData['contractValue'] ?? 0, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-semibold h4">
                                        <i class="fas fa-tasks text-info me-2"></i>
                                        <a href="#physical-section"
                                            class="text-info text-decoration-none scroll-link">Physical Progress</a>
                                    </span>
                                    <a href="#physical-section">
                                        <span
                                            class="fw-bold text-info h4">{{ $subProjectData['physicalPercent'] ?? 0 }}%</span>
                                    </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-semibold h4">
                                        <i class="fas fa-coins text-warning me-2"></i>
                                        <a href="#finance-section"
                                            class="text-warning text-decoration-none scroll-link">Finance Progress</a>
                                    </span>
                                    <a href="#finance-section">
                                        <span
                                            class="fw-bold text-warning h4">{{ $subProjectData['financePercent'] ?? 0 }}%</span>
                                    </a>
                                </li>
                            </ul>
                        </x-admin.card>
                    </div>

                    <!-- 🔹 Work Progress -->
                    <div class="col-lg-6 col-6">

                        <!-- Contract Details -->
                        <x-admin.contract-details :contract="$contract" />

                        <!-- Contractor Info -->

                    </div>
                    <div class="col-lg-6 col-6">
                        <x-admin.contractor-info :contractor="$contract->contractor" />
                    </div>
                    <div class="col-12">
                        <x-admin.work-progress-data :subProjectsData="collect([$subProjectData])" />
                    </div>


                    <!-- ==================== Finance Entries ==================== -->

                    <div class="col-lg-6 col-md-12" id="finance-section">
                        <x-admin.finance-entries :subProjectsData="collect([$subProjectData])" />
                    </div>

                    <!-- ==================== Physical Entries ==================== -->
                    <div class="col-lg-6 col-md-12" id="physical-section">
                        <x-admin.physical-entries :subProjectsData="collect([$subProjectData])" />
                    </div>


                    <!-- ==================== Safeguards Compliance ==================== -->
                    <div class="col-12">
                        <x-admin.card title="Safeguards Compliance" icon="fas fa-check"
                            headerClass="bg-warning fw-bold text-white">

                            <x-admin.data-table id="safeguards-table" :headers="array_merge(['#', 'Compliance'], $compliancePhaseHeaders->toArray())" :excel="true"
                                :print="true" :pageLength="10" resourceName="safeguards">
                                @foreach ($subProjectData['safeguards'] as $sg)
                                    @php
                                        // phase => percent map
                                        $phaseMap = collect($sg['phases'])->pluck('percent', 'phase')->toArray();
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sg['compliance'] }}</td>

                                        @foreach ($compliancePhaseHeaders as $phaseName)
                                            <td>{{ $phaseMap[$phaseName] ?? 0 }}%</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </x-admin.data-table>
                        </x-admin.card>


                    </div>

                    <!-- ==================== Milestones ==================== -->
                    <div class="col-12">
                        @if ($contract->commencement_date && !empty($milestones))
                            <x-admin.milestones :milestones="$milestones" :subProjectsData="collect([$subProjectData])" />
                        @else
                            <h5 class="text-white fst-italic mb-0">
                                Milestones not available.
                            </h5>
                        @endif

                    </div>
                </div>
            </div>

            <!-- ========== REMARKS TAB ========== -->
            <div class="tab-pane fade" id="remarks" role="tabpanel" aria-labelledby="remarks-tab">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="text-primary mb-0"><i class="fas fa-comments me-2"></i> Sub-Project Remarks</h5>
                    </div>
                    <div class="card-body">
                        <!-- ✅ Add Remark Form -->
                        <form id="remarkForm" class="mb-3">
                            @csrf
                            <input type="hidden" name="subproject_id" value="{{ $subProjectData['id'] }}">
                            <div class="input-group">
                                <textarea name="remark" class="form-control form-control-lg" placeholder="Type your remark..." required></textarea>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-1"></i> Send
                                </button>
                            </div>
                        </form>

                        <!-- ✅ Alert -->
                        <div id="remarkAlert" class="alert alert-success d-none py-2 mb-3 text-center fw-semibold">
                            ✅ Remark saved successfully!
                        </div>

                        <!-- ✅ Remarks List -->
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Remark</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="remarkTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const remarkTable = document.getElementById('remarkTableBody');
            const remarkForm = document.getElementById('remarkForm');
            const alertBox = document.getElementById('remarkAlert');
            const subProjectId = "{{ $subProjectData['id'] }}";

            // Load remarks
            async function loadRemarks() {
                remarkTable.innerHTML =
                    `<tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>`;
                try {
                    const res = await fetch(
                        `{{ route('admin.project-subproject-links.index') }}?subproject_id=${subProjectId}`);
                    const json = await res.json();
                    remarkTable.innerHTML = '';

                    if (!json.data?.length) {
                        remarkTable.innerHTML =
                            `<tr><td colspan="4" class="text-center text-muted fst-italic">No remarks yet.</td></tr>`;
                        return;
                    }

                    json.data.forEach((r, i) => {
                        remarkTable.insertAdjacentHTML('beforeend', `
                            <tr>
                                <td>${i + 1}</td>
                                <td><strong>${r.user_name}</strong></td>
                                <td>${r.remark}</td>
                                <td><small class="text-muted">${r.created_at}</small></td>
                            </tr>
                        `);
                    });
                } catch (e) {
                    remarkTable.innerHTML =
                        `<tr><td colspan="4" class="text-danger text-center">Failed to load remarks.</td></tr>`;
                }
            }

            // Load on tab show
            document.querySelector('#remarks-tab').addEventListener('shown.bs.tab', loadRemarks);

            // Submit form
            remarkForm.addEventListener('submit', async e => {
                e.preventDefault();
                const formData = new FormData(remarkForm);

                const res = await fetch(`{{ route('admin.project-subproject-links.store') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    alertBox.classList.remove('d-none');
                    remarkForm.reset();
                    loadRemarks();
                    setTimeout(() => alertBox.classList.add('d-none'), 2000);
                }
            });
        });
    </script>

    <style>
        .nav-tabs .nav-link {
            border: none;
            font-size: 1.1rem;
            color: #6c757d;
            transition: all 0.2s ease;
        }

        .nav-tabs .nav-link.active {
            background: #0d6efd;
            color: #fff !important;
            border-radius: 8px;
            box-shadow: 0 0 6px rgba(13, 110, 253, 0.4);
        }

        .nav-tabs .nav-link:hover {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
    </style>
</x-app-layout>
