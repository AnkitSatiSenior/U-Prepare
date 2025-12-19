<x-app-layout>
    <div class="container-fluid py-4">

        <!-- 🔹 Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary"
            title="Package Contract Details | {{ $contract->contract_number }}" :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Contracts', 'route' => 'admin.contracts.index'],
                ['label' => 'Show'],
            ]" />

        <!-- 🔹 Package Card -->
        <x-admin.package-card :packageProject="$contract->project" />

        <!-- 🔹 Tabs Navigation -->
        <ul class="nav nav-tabs justify-content-start mb-4 border-0" id="packageTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="details-tab" data-bs-toggle="tab" data-bs-target="#details"
                    type="button" role="tab" aria-controls="details" aria-selected="true">
                    <i class="fas fa-box-open me-1"></i> Package Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="remarks-tab" data-bs-toggle="tab" data-bs-target="#remarks"
                    type="button" role="tab" aria-controls="remarks" aria-selected="false">
                    <i class="fas fa-comments me-1"></i> Package Remarks
                </button>
            </li>
        </ul>

        <!-- 🔹 Tabs Content -->
        <div class="tab-content" id="packageTabsContent">
            <!-- 🧩 Package Details -->
            <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                <div class="row mt-3 mb-4">
                    <!-- Left Column -->
                    <div class="col-md-4">
                        <x-admin.approval-details :packageProject="$contract->project" />
                        <x-admin.contract-details :contract="$contract" />
                        <x-admin.contractor-info :contractor="$contract->contractor" />
                        <x-admin.contract-history :updates="$contract->updates" />
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-8">
                        @if ($contract->project && $contract->project->procurementDetail)
                            <x-admin.procurement-details :procurementDetail="$contract->project->procurementDetail" />
                        @endif

                        <x-admin.work-program :workPrograms="$contract->project->workPrograms" />

                        <!-- Sub-Projects Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white border-0">
                                <h6 class="text-secondary mb-0 h4">
                                    <i class="fas fa-layer-group me-2"></i>
                                    Sub-Projects ({{ $contract->count_sub_project }})
                                </h6>
                            </div>
                            <div class="card-body">
                                @if ($contract->subProjects->isEmpty())
                                    <p class="text-muted fst-italic">No sub-projects found.</p>
                                @else
                                    <x-admin.data-table id="sub-projects-table"
                                        :headers="['#', 'Name', 'Contract Value (₹)', 'Actions']" :excel="true"
                                        :print="true" :pageLength="10" :resourceName="'sub-projects'">
                                        @foreach ($subProjectsData as $i => $sp)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $sp['name'] }}</td>
                                                <td class="text-end">₹{{ $sp['contractValue'] }}</td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach ($sp['actions'] as $action)
                                                            <a href="{{ $action['route'] }}"
                                                                class="btn btn-sm {{ $action['class'] }} d-flex align-items-center gap-1">
                                                                <i class="{{ $action['icon'] }}"></i>
                                                                <span>{{ $action['label'] }}</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </x-admin.data-table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 💬 Package Remarks -->
            <div class="tab-pane fade" id="remarks" role="tabpanel" aria-labelledby="remarks-tab">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="text-primary mb-0"><i class="fas fa-comments me-2"></i> Project Remarks</h5>
                    </div>

                    <div class="card-body">
                        <!-- Add Remark Form -->
                        <form id="remarkForm" class="mb-3">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $contract->project->id }}">
                            <div class="input-group">
                                <textarea name="remark" class="form-control form-control-lg"
                                    placeholder="Type your remark..." required></textarea>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-1"></i> Send
                                </button>
                            </div>
                        </form>

                        <!-- Alert -->
                        <div id="remarkAlert"
                            class="alert alert-success d-none py-2 mb-3 text-center fw-semibold">
                            ✅ Remark saved successfully!
                        </div>

                        <!-- Remarks Table -->
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
        </div> <!-- /tab-content -->
    </div> <!-- /container -->

    <script>
        const remarkTable = document.getElementById('remarkTableBody');
        const CURRENT_USER_ID = {{ auth()->id() ?? 'null' }};

        // Load remarks whenever tab becomes active
        const remarksTab = document.getElementById('remarks-tab');
        remarksTab.addEventListener('shown.bs.tab', () => loadRemarks());

        async function loadRemarks() {
            const projectId = {{ $contract->project->id }};
            remarkTable.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>`;

            try {
                const res = await fetch(`{{ route('admin.project-subproject-links.index') }}?project_id=${projectId}`);
                const json = await res.json();
                remarkTable.innerHTML = '';

                if (!json.data?.length) {
                    remarkTable.innerHTML = `<tr><td colspan="4" class="text-center text-muted fst-italic">No remarks yet.</td></tr>`;
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
            } catch (err) {
                remarkTable.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Failed to load remarks.</td></tr>`;
                console.error(err);
            }
        }

        // Handle remark form submission
        document.getElementById('remarkForm').addEventListener('submit', async e => {
            e.preventDefault();
            const formData = new FormData(e.target);

            const res = await fetch('{{ route('admin.project-subproject-links.store') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            const data = await res.json();
            const alertBox = document.getElementById('remarkAlert');

            if (data.success || data.status) {
                alertBox.classList.remove('d-none');
                e.target.reset();
                await loadRemarks();
                setTimeout(() => alertBox.classList.add('d-none'), 2000);
            }
        });
    </script>

    <style>
        /* ✅ Tab styling */
        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-radius: 0.5rem 0.5rem 0 0;
            color: #6c757d;
            background: #f8f9fa;
            margin-left: 0.5rem;
            transition: all 0.2s ease-in-out;
        }

        .nav-tabs .nav-link.active {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.3);
        }

        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            background: #e7f1ff;
        }

        .tab-content {
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-app-layout>
