<x-app-layout>
    <div class="container-fluid py-4">

        <!-- ==================== Breadcrumb ==================== -->
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" :title="'Package Contract Details | ' . $contract->contract_number" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['label' => 'Contracts', 'route' => 'admin.contracts.index'],
            ['label' => 'Show'],
        ]" />
        <ul class="nav nav-tabs justify-content-start mb-4 border-0" id="subProjectTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="details-tab" data-bs-toggle="tab" data-bs-target="#details"
                    type="button" role="tab" aria-controls="details" aria-selected="true">
                    <i class="fas fa-cube me-1"></i> Package-Project Details
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
                    <!-- ==================== Package Card ==================== -->
                    <div class="row mb-2">
                        <div class="col-12">
                            <x-admin.package-card :packageProject="$contract->project" />
                        </div>
                    </div>

                    <div class="row g-4">

                        <!-- ==================== Left Column ==================== -->
                        <div class="col-lg-4 col-md-5">
                            <div class="mb-4">
                                <x-admin.approval-details :packageProject="$contract->project" />
                            </div>
                            <div class="mb-4">
                                <x-admin.contract-details :contract="$contract" />
                            </div>
                            <div class="mb-4">
                                <x-admin.contractor-info :contractor="$contract->contractor" />
                            </div>
                            <div class="mb-4">
                                <x-admin.contract-history :updates="$contract->updates" />
                            </div>
                        </div>

                        <!-- ==================== Right Column ==================== -->
                        <div class="col-lg-8 col-md-7">
                            @if ($contract->project?->procurementDetail)
                                <div class="mb-1">
                                    <x-admin.procurement-details :procurementDetail="$contract->project->procurementDetail" />
                                </div>
                            @endif

                            <div class="mb-1">
                                <x-admin.work-program :workPrograms="$contract->project->workPrograms" />
                            </div>

                            <!-- ==================== Sub-Projects Table ==================== -->
                            <div class="mb-1">
                                <x-admin.card title="Sub-Projects ({{ $contract->subProjects->count() }})"
                                    icon="fas fa-layer-group" headerClass="bg-primary text-white">
                                    @if ($contract->subProjects->isEmpty())
                                        <p class="text-muted fst-italic mb-0">No sub-projects found.</p>
                                    @else
                                        @php
                                            $avgFinance = round(collect($subProjectsData)->avg('financePercent'), 2);
                                            $avgPhysical = round(collect($subProjectsData)->avg('physicalPercent'), 2);
                                        @endphp

                                        <table id="sub-projects-table" class="table table-bordered table-striped">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="w5p">#</th>
                                                    <th class="w50p">Name</th>
                                                    <th class="w50p">Gallery</th>
                                                    <th class="w10p">Contract (₹)</th>
                                                    <th class="w20p">Finance </th>
                                                    <th class="w10p">Physical </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($subProjectsData as $i => $sp)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td class="text" style="max-width:160px;"
                                                            title="{{ $sp['name'] }}">
                                                            {{ $sp['name'] }}
                                                        </td>
                                                        <td class="text" style="max-width:160px;"
                                                            title="{{ $sp['name'] }}">
                                                            <a href="{{ route('admin.sub-projects.documents', $sp['id']) }}"
                                                                class="btn btn-sm btn-primary">
                                                                View Documents
                                                            </a>
                                                        </td>
                                                        <td class="text-end">₹{{ number_format($sp['contractValue']) }}
                                                        </td>

                                                        {{-- Finance Progress --}}
                                                        <td>
                                                            <span
                                                                class="fw-bold text-success">{{ $sp['financePercent'] }}%</span>
                                                            @if (!empty($sp['financeLastDate']))
                                                                <br>
                                                                <small class="text-muted">
                                                                    Last Updated On :
                                                                    {{ \Carbon\Carbon::parse($sp['financeLastDate'])->format('d M Y') }}
                                                                </small>
                                                            @endif
                                                        </td>

                                                        {{-- Physical Progress --}}
                                                        <td>
                                                            <span
                                                                class="fw-bold text-info">{{ $sp['physicalPercent'] }}%</span>
                                                            @if (!empty($sp['physicalLastDate']))
                                                                <br>
                                                                <small class="text-muted">
                                                                    Last:
                                                                    {{ \Carbon\Carbon::parse($sp['physicalLastDate'])->format('d M Y') }}
                                                                </small>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                            {{-- ✅ Summary Row --}}
                                            <tfoot>
                                                <tr class="table-secondary fw-bold">
                                                    <td colspan="4" class="text-end">Overall Completion:</td>
                                                    <td>{{ $avgFinance }}%</td>
                                                    <td>{{ $avgPhysical }}%</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @endif
                                </x-admin.card>
                            </div>

                            <!-- ==================== Physical Entries ==================== -->
                            <div class="mb-1 ">
                                <x-admin.physical-entries :subProjectsData="$subProjectsData" />
                            </div>

                            <!-- ==================== Finance Entries ==================== -->
                            <div class="mb-1">
                                <x-admin.finance-entries :subProjectsData="$subProjectsData" />
                            </div>

                        </div>

                        <!-- ==================== Milestones ==================== -->
                        <div class="col-12 mb-4">
                            @if ($contract->commencement_date && !empty($milestones))
                                <x-admin.milestones :milestones="$milestones" :subProjectsData="$subProjectsData" />
                            @else
                                <h4 class="text-white fst-italic mb-0 bg-danger">
                                    Milestones not available — missing commencement date, revised completion date, or
                                    milestone
                                    data.
                                </h4>
                            @endif
                        </div>
                        <div class="col-12 mb-4">
                            <x-admin.work-progress-data :subProjectsData="$subProjectsData" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="remarks" role="tabpanel" aria-labelledby="remarks-tab">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="text-primary mb-0"><i class="fas fa-comments me-2"></i> Package-Project Remarks</h5>
                    </div>
                    <div class="card-body">
                        <!-- ✅ Add Remark Form -->
                        <form id="remarkForm" class="mb-3">
                            @csrf
                           <input type="hidden" name="project_id" value="{{ $contract->project->id }}">
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
            const projectId = "{{ $contract->project->id }}";

            // Load remarks
            async function loadRemarks() {
                remarkTable.innerHTML =
                    `<tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>`;
                try {
                    const res = await fetch(
                        `{{ route('admin.project-subproject-links.index') }}?project_id=${projectId}`);
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
