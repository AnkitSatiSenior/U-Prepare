
<x-app-layout>
    <div class="container py-5">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-file-invoice-dollar text-primary" :title="'Update Project Progress'" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i> Dashboard'],
            ['label' => 'Update Progress'],
        ]" />

        <!-- Flash Messages -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif
        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible />
        @endif

        <!-- Project Progress Table -->
        <div class="card shadow-lg border-0 mb-4">
            <div class="card-header bg-gradient d-flex justify-content-between align-items-center"
                style="background: linear-gradient(90deg, #0d6efd, #0dcaf0);">
                <h5 class="mb-0 text-white fw-bold">
                    <i class="fas fa-chart-line me-2"></i> Project Progress Updates
                </h5>
            </div>

            <div class="card-body">
                <x-admin.data-table :headers="[
                    'Sub Project',
                    'Contract Value',
                    'Financial Progress',
                    'Physical Progress',
                    'Tests / Reports',
                    'Actions',
                ]" id="progressTable" :excel="true" :print="true"
                    :pageLength="10">
                    @foreach ($subProjects as $progress)
                        <tr class="align-middle">

                            <!-- Sub Project -->
                            <td>
                                <span class="fw-bold text-dark">
                                    <i class="fas fa-folder-open text-primary me-1"></i>
                                    {{ $progress->name }}
                                    {{ $progress->packageProject?->package_number }}
                                </span>
                            </td>

                            <!-- Contract Value -->
                            <td>{{ formatPriceToCR($progress->contract_value) }}</td>

                            <!-- Financial Progress -->
                            <td>
                                <div title="{{ $progress->financial_progress_percentage }}% Financial"
                                    class="progress shadow-sm"
                                    style="height: 22px; border-radius: 12px;cursor: pointer;">
                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated fw-bold"
                                        role="progressbar"
                                        style="width: {{ $progress->financial_progress_percentage }}%;"
                                        aria-valuenow="{{ $progress->financial_progress_percentage }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                        {{ round($progress->financial_progress_percentage, 2) }}%
                                    </div>
                                </div>
                            </td>

                            <!-- Physical Progress -->
                            <td>
                                @if ($progress->physical_progress_percentage > 0)
                                    <div title="{{ $progress->physical_progress_percentage }}% Physical"
                                        class="progress shadow-sm"
                                        style="height: 22px; border-radius: 12px;cursor: pointer;">
                                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated fw-bold"
                                            role="progressbar"
                                            style="width: {{ $progress->physical_progress_percentage }}%;"
                                            aria-valuenow="{{ $progress->physical_progress_percentage }}"
                                            aria-valuemin="0" aria-valuemax="100">
                                            {{ round($progress->physical_progress_percentage, 2) }}%
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">No Data</span>
                                @endif
                            </td>

                            <!-- Tests / Reports -->
                            <!-- Tests / Reports -->
                            <td>
                                @php
                                    $totalTests = $progress->tests()->count();
                                    $completedTests = $progress->tests()->has('report')->count();
                                    $pendingTests = $totalTests - $completedTests;
                                @endphp

                                @if ($totalTests > 0)
                                    @if ($completedTests > 0)
                                        <span class="badge bg-success">{{ $completedTests }} Completed</span>
                                    @endif
                                    @if ($pendingTests > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $pendingTests }}
                                            Pending</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">No Test</span>
                                @endif
                            </td>


                            <!-- Actions -->
                            <!-- Actions -->
                            <td>
    <div class="d-flex flex-wrap gap-2">

        {{-- Financial Update --}}
        @if (canRoute('admin.financial-progress-updates.index'))
            <a href="{{ route('admin.financial-progress-updates.index', ['sub_package_project_id' => $progress->id]) }}"
                class="btn btn-sm btn-success shadow-sm d-flex align-items-center px-3 py-2"
                data-bs-toggle="tooltip" title="Update Financial Progress">
                <i class="fas fa-money-bill-wave me-1"></i> Financial
            </a>
        @endif

        {{-- Physical Update --}}
        @if ($progress->type_of_procurement_name === 'EPC' && canRoute('admin.physical_epc_progress.index'))
            <a href="{{ route('admin.physical_epc_progress.index', ['sub_package_project_id' => $progress->id]) }}"
                class="btn btn-sm btn-primary shadow-sm d-flex align-items-center px-3 py-2"
                data-bs-toggle="tooltip" title="Update EPC Progress">
                <i class="fas fa-hard-hat me-1"></i> EPC
            </a>
        @elseif (canRoute('admin.physical_boq_progress.index'))
            <a href="{{ route('admin.physical_boq_progress.index', ['sub_package_project_id' => $progress->id]) }}"
                class="btn btn-sm btn-info shadow-sm d-flex align-items-center px-3 py-2"
                data-bs-toggle="tooltip" title="Update BOQ Progress">
                <i class="fas fa-list-alt me-1"></i> BOQ
            </a>
        @endif

        {{-- Test Management --}}
        @if (canRoute('admin.sub_package_project_tests.index'))
            <a href="{{ route('admin.sub_package_project_tests.index', $progress->id) }}"
                class="btn btn-sm btn-warning shadow-sm d-flex align-items-center px-3 py-2"
                data-bs-toggle="tooltip" title="Manage Tests">
                <i class="fas fa-vial me-1"></i> Create Tests
            </a>
        @endif

        {{-- Safeguard --}}
        <a href="{{ route('admin.social_safeguard_entries.index', [
                'project_id' => $progress->id,
                'compliance_id' => 1,
                'phase_id' => 1,
            ]) }}"
            class="btn btn-sm btn-warning shadow-sm d-flex align-items-center px-3 py-2"
            data-bs-toggle="tooltip" title="Manage Safeguard">
            <i class="fas fa-shield-alt me-1"></i> Safeguard
        </a>

        {{-- Test Reports --}}
        @if (canRoute('admin.sub_package_project_test_reports.index'))
            <a href="{{ route('admin.sub_package_project_test_reports.index', $progress->id) }}"
                class="btn btn-sm btn-info shadow-sm d-flex align-items-center px-3 py-2"
                data-bs-toggle="tooltip" title="Submit/View Test Reports">
                <i class="fas fa-file-alt me-1"></i> Test Reports
            </a>
        @endif

        {{-- Upload EPC Images --}}
        @if (canRoute('admin.work_progress_data.uploadImagesToLastProgress'))
            <button type="button"
                class="btn btn-sm btn-success shadow-sm d-flex align-items-center px-3 py-2 upload-image-btn"
                data-bs-toggle="modal"
                data-bs-target="#uploadImageModal"
                data-project-id="{{ $progress->id }}"
                title="Upload EPC Images">
                <i class="fas fa-image me-1"></i> Upload Images
            </button>
        @endif

        {{-- Work Progress Gallery --}}
        @if (canRoute('admin.work_progress.gallery'))
            <a href="{{ route('admin.work_progress.gallery', ['projectId' => $progress->id]) }}"
                class="btn btn-sm btn-outline-primary shadow-sm d-flex align-items-center px-3 py-2"
                data-bs-toggle="tooltip" title="View Work Progress Gallery">
                <i class="fas fa-images me-1"></i> View Gallery
            </a>
        @endif

    </div>
</td>



                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>

    </div>
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" action="{{ route('admin.work_progress_data.uploadImagesToLastProgress') }}" 
              enctype="multipart/form-data" 
              class="modal-content">
            @csrf
            <input type="hidden" name="project_id" id="modal_project_id">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="uploadImageModalLabel">
                    <i class="fas fa-upload me-2"></i> Upload Work Progress Images
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="work_component_id" class="form-label">Select Work Component <span class="text-danger">*</span></label>
                    <select name="work_component_id" id="work_component_id" class="form-control" required>
                        <option value="">-- Select Component --</option>
                        @foreach ($components as $component)
                            <option value="{{ $component->id }}">
                                 {{ $component->work_component }} - {{ $component->type_details }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description / Comment</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Add optional description or remarks..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Select Images:</label>
                    <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png" class="form-control" required>
                    <small class="text-muted">You can upload multiple images (max 2MB each).</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle me-1"></i> Upload
                </button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.upload-image-btn').forEach(button => {
        button.addEventListener('click', function() {
            const projectId = this.getAttribute('data-project-id');
            document.getElementById('modal_project_id').value = projectId;
        });
    });
});
</script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el) });

            // Handle upload modal route selection
            const uploadModal = document.getElementById('uploadImageModal');
            const uploadForm = document.getElementById('uploadImageForm');

            uploadModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const projectId = button.getAttribute('data-project-id');
                const type = button.getAttribute('data-type');
                
                uploadModal.querySelector('#modal_project_id').value = projectId;

                // Dynamically change form action
                if (type === 'epc') {
                    uploadForm.action = "{{ route('admin.physical_epc_progress.upload_images') }}";
                } else if (type === 'boq') {
                    uploadForm.action = "{{ route('admin.physical_boq_progress.upload_images.boq') }}";
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

</x-app-layout>
