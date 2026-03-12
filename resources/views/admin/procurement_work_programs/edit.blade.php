<x-app-layout>
    <div class="container-fluid">

        <x-admin.breadcrumb-header icon="fas fa-edit text-primary" title="Edit Procurement Work Programs"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i> Dashboard'],
                ['label' => 'Procurement Work Programs'],
            ]" />

        <div id="alerts"></div>

        <div class="mb-3 row">
            <label for="sharedStartDate" class="col-sm-2 col-form-label fw-semibold">Shared Start Date</label>
            <div class="col-sm-4">
                <input type="date" id="sharedStartDate" class="form-control"
                    value="{{ $workPrograms->first() ? \Carbon\Carbon::parse($workPrograms->first()->start_date)->format('Y-m-d') : date('Y-m-d') }}">
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <a href="{{ route('admin.procurement-details.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <button id="addRow" class="btn btn-success"><i class="fas fa-plus-circle me-1"></i> Add Row</button>
            </div>
        </div>

        <form
            action="{{ route('admin.procurement-work-programs.upload-documents', [$procurementWorkProgram->package_project_id, $procurementWorkProgram->procurement_details_id]) }}"
            method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label fw-semibold">Procurement Bid Document</label>
                <div class="col-sm-4">
                    <input type="file" name="procurement_bid_document" accept=".pdf,.doc,.docx,.jpg,.png"
                        class="form-control">
                </div>
                <div class="col-sm-6">
                    @if (!empty($procurementWorkProgram->procurement_bid_document))
                        @php
                            // 🏗️ ARCHITECTURE FIX: Resolve strictly from the S3 disk.
                            $bidDocUrl = \Illuminate\Support\Facades\Storage::disk('s3')->url($procurementWorkProgram->procurement_bid_document);
                        @endphp
                        
                        <a href="{{ $bidDocUrl }}" target="_blank" rel="noopener noreferrer" class="me-3 btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i> View Document
                        </a>
                    @else
                        <span class="text-muted fst-italic"><i class="fas fa-info-circle me-1"></i> No document uploaded.</span>
                    @endif
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                <label class="col-sm-2 col-form-label fw-semibold">Pre-Bid Minutes Document</label>
                <div class="col-sm-4">
                    <input type="file" name="pre_bid_minutes_document" accept=".pdf,.doc,.docx,.jpg,.png"
                        class="form-control">
                </div>
                <div class="col-sm-6">
                    @if (!empty($procurementWorkProgram->pre_bid_minutes_document))
                        @php
                            // 🏗️ ARCHITECTURE FIX: Resolve strictly from the S3 disk.
                            $preBidDocUrl = \Illuminate\Support\Facades\Storage::disk('s3')->url($procurementWorkProgram->pre_bid_minutes_document);
                        @endphp
                        
                        <a href="{{ $preBidDocUrl }}" target="_blank" rel="noopener noreferrer" class="me-3 btn btn-sm btn-outline-success">
                            <i class="fas fa-external-link-alt me-1"></i> View Document
                        </a>
                    @else
                        <span class="text-muted fst-italic"><i class="fas fa-info-circle me-1"></i> No document uploaded.</span>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Upload Documents
                    </button>
                </div>
            </div>
        </form>

        <div class="card shadow-sm mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Work Program Details</strong>
                <small class="text-muted">Package: <span
                        class="fw-semibold">{{ $procurementWorkProgram->package_project_id ?? '' }}</span>,
                    Procurement: <span
                        class="fw-semibold">{{ $procurementWorkProgram->procurement_details_id ?? '' }}</span></small>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="min-width: 250px;">Name of Work Program</th>
                            <th style="width:140px;">Weightage (%)</th>
                            <th style="width:120px;">Days</th>
                            <th style="width:160px;">Planned Date</th>
                            <th style="width:160px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="workProgramTable">
                        @foreach ($workPrograms as $program)
                            <tr data-id="{{ $program->id }}">
                                <td>
                                    <input type="text" name="name_work_program" class="form-control name-input"
                                        value="{{ $program->name_work_program }}" required>
                                </td>
                                <td>
                                    <input type="number" name="weightage" step="0.01" min="0" max="100"
                                        class="form-control weightage-input text-end"
                                        value="{{ $program->weightage }}">
                                </td>
                                <td>
                                    <input type="number" name="days" min="0" class="form-control days-input"
                                        value="{{ $program->days }}">
                                </td>
                                <td>
                                    <input type="date" name="planned_date" class="form-control planned-date-input"
                                        value="{{ optional($program->planned_date)->format('Y-m-d') }}">
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="actions">
                                        <button type="button" class="btn btn-primary btn-sm save-row"
                                            data-action="{{ route('admin.procurement-work-programs.update-single', $program->id) }}">
                                            <i class="fas fa-save"></i> Save
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm delete-row"
                                            data-action="{{ route('admin.procurement-work-programs.destroy', $program->id) }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>

                                    <input type="hidden" class="package_project_id"
                                        value="{{ $program->package_project_id }}">
                                    <input type="hidden" class="procurement_details_id"
                                        value="{{ $program->procurement_details_id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($workPrograms->isEmpty())
                    <div class="text-center py-4 text-muted">No work programs found for this package / procurement.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="toastContainer"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === CONSTANTS & ELEMENTS ===
            const csrfToken = "{{ csrf_token() }}";
            const sharedStartDateInput = document.getElementById('sharedStartDate');
            const tableBody = document.getElementById('workProgramTable');
            const toastContainer = document.getElementById('toastContainer');

            // === 1. DATE CALCULATION LOGIC (Waterfall/Sequential) ===
            function recalculateAllDates() {
                let currentStartDate = sharedStartDateInput.value;

                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const daysInput = row.querySelector('.days-input');
                    const plannedDateInput = row.querySelector('.planned-date-input');

                    if (!daysInput || !plannedDateInput) return;

                    const days = parseInt(daysInput.value, 10);

                    if (!isNaN(days) && currentStartDate) {
                        const base = new Date(currentStartDate);
                        // Add Days to current start date
                        base.setDate(base.getDate() + days);

                        // Format YYYY-MM-DD
                        const plannedDate = base.toISOString().slice(0, 10);
                        plannedDateInput.value = plannedDate;

                        // The planned date of this row becomes the start date of the NEXT row
                        currentStartDate = plannedDate;
                    } else {
                        plannedDateInput.value = '';
                        // If calculation breaks, next rows can't calculate
                        currentStartDate = ''; 
                    }
                });
            }

            // Listen for Global Start Date changes
            sharedStartDateInput.addEventListener('input', recalculateAllDates);

            // === 2. ROW HANDLERS ===
            function attachRowListeners(row) {
                const daysInput = row.querySelector('.days-input');
                const nameInput = row.querySelector('.name-input');
                const weightageInput = row.querySelector('.weightage-input');

                // Recalculate everything when "Days" changes in any row
                if (daysInput) {
                    daysInput.addEventListener('input', recalculateAllDates);
                }

                // AJAX Save
                const saveBtn = row.querySelector('.save-row');
                if (saveBtn) saveBtn.addEventListener('click', () => handleSaveRow(row, saveBtn.dataset.action));

                // AJAX Delete
                const deleteBtn = row.querySelector('.delete-row');
                if (deleteBtn) deleteBtn.addEventListener('click', () => handleDeleteRow(row, deleteBtn.dataset.action));

                // Validation Styling
                [nameInput, weightageInput].forEach(el => {
                    if (!el) return;
                    el.addEventListener('input', () => {
                        if (el.name === 'weightage') {
                            const val = parseFloat(el.value);
                            el.classList.toggle('is-invalid', isNaN(val) || val < 0 || val > 100);
                        } else if (el.name === 'name_work_program') {
                            el.classList.toggle('is-invalid', !el.value.trim());
                        }
                    });
                });
            }

            // Initialize existing rows
            tableBody.querySelectorAll('tr').forEach(attachRowListeners);
            
            // Run calculation immediately on page load to ensure consistency
            recalculateAllDates();

            // === 3. ADD ROW LOGIC ===
            document.getElementById('addRow').addEventListener('click', function() {
                // Find IDs from existing rows or use defaults if table is empty
                let packageProjectId = '';
                let procurementDetailsId = '';
                
                const existingRow = tableBody.querySelector('tr');
                if (existingRow) {
                    packageProjectId = existingRow.querySelector('.package_project_id').value;
                    procurementDetailsId = existingRow.querySelector('.procurement_details_id').value;
                } else {
                    // Fallback if table is empty (You might want to pass these from PHP to JS variables)
                    packageProjectId = "{{ $procurementWorkProgram->package_project_id ?? '' }}";
                    procurementDetailsId = "{{ $procurementWorkProgram->procurement_details_id ?? '' }}";
                }

                const tempId = 'temp-' + Date.now();

                const newRowHtml = `
            <tr data-id="${tempId}">
                <td><input type="text" name="name_work_program" class="form-control name-input" value=""></td>
                <td><input type="number" name="weightage" step="0.01" min="0" max="100" 
                           class="form-control weightage-input text-end" value=""></td>
                <td><input type="number" name="days" min="0" class="form-control days-input" value="0"></td>
                <td><input type="date" name="planned_date" class="form-control planned-date-input" value=""></td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary btn-sm save-row">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button type="button" class="btn btn-danger btn-sm delete-row">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                    <input type="hidden" class="package_project_id" value="${packageProjectId}">
                    <input type="hidden" class="procurement_details_id" value="${procurementDetailsId}">
                </td>
            </tr>
        `;
                tableBody.insertAdjacentHTML('beforeend', newRowHtml);
                attachRowListeners(tableBody.querySelector(`tr[data-id="${tempId}"]`));
                recalculateAllDates(); // Recalculate to set the date for new row
                showToast('Row added. Fill details and click Save.', 'Info', true);
            });

            // === 4. UTILITIES (Toast & Validation) ===
            function showToast(message, title = '', isSuccess = true, timeout = 3000) {
                const id = 'toast-' + Date.now();
                const toastHtml = `
            <div id="${id}" class="toast align-items-center text-bg-${isSuccess ? 'success' : 'danger'} border-0 mb-2" 
                 role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${timeout}">
              <div class="d-flex">
                <div class="toast-body">
                  ${title ? `<strong>${title}</strong><br>` : ''}${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
              </div>
            </div>`;
                toastContainer.insertAdjacentHTML('beforeend', toastHtml);
                const toastEl = document.getElementById(id);
                const bsToast = new bootstrap.Toast(toastEl);
                bsToast.show();
                toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
            }

            function displayAlertHtml(html) {
                document.getElementById('alerts').innerHTML = html;
                setTimeout(() => {
                    document.getElementById('alerts').innerHTML = '';
                }, 7000);
            }

            function extractValidationErrors(json) {
                let msgs = [];
                if (json.errors) {
                    Object.values(json.errors).forEach(arr => {
                        msgs = msgs.concat(arr);
                    });
                } else if (json.message) {
                    msgs.push(json.message);
                }
                return msgs;
            }

            function buildAlertHtml(messages = []) {
                if (!messages.length) return '';
                const li = messages.map(m => `<li>${escapeHtml(m)}</li>`).join('');
                return `<div class="alert alert-danger"><h6 class="mb-2">Validation error</h6><ul class="mb-0">${li}</ul></div>`;
            }

            function escapeHtml(unsafe) {
                return String(unsafe ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // === 5. AJAX ACTIONS ===
            function handleSaveRow(row, actionUrl) {
                const id = row.dataset.id;
                const name = row.querySelector('.name-input').value.trim();
                const weightage = row.querySelector('.weightage-input').value;
                const days = row.querySelector('.days-input').value || 0;
                const planned_date = row.querySelector('.planned-date-input').value || null;
                const packageProjectId = row.querySelector('.package_project_id').value;
                const procurementDetailsId = row.querySelector('.procurement_details_id').value;

                if (!name) {
                    showToast('Name is required.', 'Validation', false);
                    return;
                }
                const w = parseFloat(weightage);
                if (isNaN(w) || w < 0 || w > 100) {
                    showToast('Weightage must be between 0 and 100.', 'Validation', false);
                    return;
                }

                // Detect if it's a NEW row (no DB ID yet)
                let url = actionUrl;
                let postData = {
                    _token: csrfToken,
                    name_work_program: name,
                    weightage: weightage,
                    days: days,
                    planned_date: planned_date
                };

                if (id.startsWith('temp-')) {
                    url = "{{ route('admin.procurement-work-programs.store-single') }}";
                    postData.package_project_id = packageProjectId;
                    postData.procurement_details_id = procurementDetailsId;
                } else {
                    postData._method = 'PUT';
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: postData,
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message || 'Saved successfully', 'Success', true);

                            if (id.startsWith('temp-') && response.data?.id) {
                                row.dataset.id = response.data.id;
                                // Update action URLs for the saved row
                                row.querySelector('.save-row').dataset.action =
                                    "{{ url('admin/procurement-work-programs/update-single') }}/" + response.data.id;
                                row.querySelector('.delete-row').dataset.action =
                                    "{{ url('admin/procurement-work-programs') }}/" + response.data.id;
                            }
                        } else {
                            showToast(response.message || 'Unable to save', 'Error', false);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON) {
                            displayAlertHtml(buildAlertHtml(extractValidationErrors(xhr.responseJSON)));
                        } else {
                            showToast(xhr.responseJSON?.message || 'Server error.', 'Error', false);
                        }
                    }
                });
            }

            function handleDeleteRow(row, actionUrl) {
                const id = row.dataset.id;
                if (id.startsWith('temp-')) {
                    row.remove();
                    recalculateAllDates(); // Recalculate after remove
                    showToast('Unsaved row removed.', 'Info', true);
                    return;
                }

                if (!confirm('Are you sure you want to delete this work program?')) return;

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success ?? true) {
                            row.remove();
                            recalculateAllDates(); // Recalculate after delete
                            showToast(response.message || 'Deleted successfully.', 'Success', true);
                        } else {
                            showToast(response.message || 'Could not delete', 'Error', false);
                        }
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Server error.', 'Error', false);
                    }
                });
            }
        });
    </script>
</x-app-layout>