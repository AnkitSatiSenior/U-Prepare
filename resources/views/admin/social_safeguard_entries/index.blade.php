<x-app-layout>
    <div class="container py-5">
        <h2 class="mb-4 text-primary fw-bold">
            {{ $subProject->name }} — {{ $compliance->name }} Safeguard Entries
        </h2>

        @if (session()->has('message'))
            <div class="alert alert-{{ session('status') }} alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4 align-items-end">
            <input type="hidden" id="project-id" value="{{ $subProject->id }}">
            <input type="hidden" id="compliance-id" value="{{ $compliance->id }}">

            <div class="col-md-3">
                <label class="form-label fw-semibold">Safeguard Compliance</label>
                <select class="form-select" disabled>
                    <option value="{{ $compliance->id }}">{{ $compliance->name }}</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="phase-id" class="form-label fw-semibold">Contraction Phase</label>
                <select id="phase-id" class="form-select">
                    <option value="">-- All --</option>
                    @foreach ($compliance->contractionPhases as $phase)
                        <option value="{{ $phase->id }}" @selected($phase->id == $phase_id)>
                            {{ $phase->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="date-of-entry" class="form-label fw-semibold">Date of Entry</label>
                <input type="date" id="date-of-entry" class="form-control" value="{{ request('date_of_entry', now()->format('Y-m-d')) }}">
            </div>

            <div class="col-md-3">
                <button id="filter-btn" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </div>

        @php
            $allSlNos = $entries->pluck('sl_no')->toArray();
        @endphp

        @if ($entries->isNotEmpty())
            <div class="table-responsive">
                <x-admin.data-table id="social-safeguard-table" :headers="['SL No', 'Item', 'Yes/No', 'Remarks', 'Validity', 'Date of Entry', 'Action', 'Files']" :excel="true" :paging="false" :pageLength="1000">
                    @foreach ($entries as $entry)
                        @php
                            $isParent = collect($allSlNos)->contains(fn($sl) => Str::startsWith($sl, $entry->sl_no . '.'));
                            $level = substr_count($entry->sl_no, '.');
                            $social = $entry->social;
                            $locked = $entry->is_locked ?? false;
                            $filesExist = $social && !empty($social->photos_documents_case_studies);
                        @endphp

                        <tr class="{{ $isParent ? 'table-secondary fw-bold' : '' }}" data-entry-id="{{ $entry->id }}" data-social-id="{{ $social?->id }}" data-has-social="{{ $social ? 1 : 0 }}">
                            <td class="align-middle">{{ $entry->sl_no }}</td>

                            <td class="text-start align-middle" style="padding-left: {{ $level * 20 }}px;">
                                {{ $entry->item_description ?? '-' }}
                            </td>

                            <td class="align-middle">
                                @if ($isParent)
                                    <span class="text-muted">—</span>
                                @else
                                    <select name="yes_no" class="form-select form-select-sm" @disabled($locked)>
                                        <option value="">Select</option>
                                        <option value="1" @selected($social?->yes_no === 1)>Yes</option>
                                        <option value="0" @selected($social?->yes_no === 0)>No</option>
                                        <option value="3" @selected($social?->yes_no === 3)>N/A</option>
                                    </select>
                                @endif
                            </td>

                            <td class="align-middle">
                                @if ($isParent)
                                    <span class="text-muted">—</span>
                                @else
                                    <input type="text" name="remarks" class="form-control form-control-sm" value="{{ $social->remarks ?? '' }}" @readonly($locked)>
                                @endif
                            </td>

                            <td class="align-middle">
                                @if ($isParent)
                                    <span class="text-muted">—</span>
                                @elseif($entry->is_validity)
                                    <input type="date" name="validity_date" class="form-control form-control-sm" value="{{ $social?->validity_date?->format('Y-m-d') ?? '' }}" @readonly($locked)>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <td class="align-middle">
                                @if ($isParent)
                                    <span class="text-muted">—</span>
                                @else
                                    <input type="date" name="date_of_entry" class="form-control form-control-sm" value="{{ $social?->date_of_entry?->format('Y-m-d') ?? now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" @readonly($locked)>
                                @endif
                            </td>

                            <td class="align-middle">
                                @if (!$isParent && !$locked)
                                    @php
                                        $isUpdateMode = $social && $phase_id != 2;
                                        $actionUrl = $isUpdateMode ? route('admin.social.update', ['id' => $social->id]) : route('admin.social_safeguard_entries.save');
                                    @endphp
                                    <button type="button" class="btn btn-sm {{ $isUpdateMode ? 'btn-warning' : 'btn-success' }} save-row" data-url="{{ $actionUrl }}" data-method="{{ $isUpdateMode ? 'UPDATE' : 'STORE' }}">
                                        <i class="fas {{ $isUpdateMode ? 'fa-edit' : 'fa-save' }}"></i>
                                        {{ $isUpdateMode ? 'Update' : 'Save' }}
                                    </button>
                                @endif
                            </td>

                            <td class="align-middle {{ $isParent ? 'bg-light' : ($filesExist ? 'bg-light-success' : 'bg-light-danger') }}">
                                @if ($isParent)
                                    <span class="text-muted">—</span>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-primary open-upload-modal" data-entry-id="{{ $entry->id }}" data-social-id="{{ $social?->id }}" data-media-ids='@json($social?->photos_documents_case_studies ?? [])'>
                                        <i class="fas {{ $filesExist ? 'fa-folder-open' : 'fa-upload' }}"></i>
                                        {{ $filesExist ? 'Manage' : 'Upload' }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        @else
            <div class="alert alert-warning text-center">
                {{ request()->has('sub_package_project_id') ? 'No entries found for the selected filters.' : 'Please select a project and date to view entries.' }}
            </div>
        @endif

        {{-- MEDIA UPLOAD MODAL --}}
        <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold text-dark"><i class="fas fa-images text-primary me-2"></i> Media Manager</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="px-4 pt-3 border-bottom">
                            <ul class="nav nav-tabs border-0" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active fw-medium" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">Upload Files</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link fw-medium" id="view-tab" data-bs-toggle="tab" data-bs-target="#view" type="button">View Uploaded</button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content p-4">
                            <div class="tab-pane fade show active" id="upload">
                                <form id="upload-form">
                                    <input type="hidden" id="modal-social-id">
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold text-secondary small text-uppercase">Select Media Files</label>
                                        <input type="file" multiple class="form-control" id="file-input" accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx">
                                    </div>

                                    <div class="table-responsive d-none border rounded" id="upload-table-container">
                                        <table class="table  align-middle mb-0">
                                            <thead class=" text-secondary small">
                                                <tr>
                                                    <th style="width: 60px;" class="text-center text-white">Preview</th>
                                                    <th class="text-white">File Name</th>
                                                    <th style="width: 40%;" class="text-white">Remark</th>
                                                    <th style="width: 100px;" class="text-white">Size</th>
                                                    <th style="width: 60px;" class="text-center text-white">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="upload-table-body"></tbody>
                                        </table>
                                    </div>

                                    <div class="text-end mt-4 d-none" id="upload-action-container">
                                        <button type="submit" class="btn btn-primary px-4" id="upload-btn">
                                            <i class="fas fa-cloud-upload-alt me-2"></i> Upload All Files
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="view">
                                <div class="table-responsive border rounded">
                                    <table class="table align-middle mb-0">
                                        <thead class="text-secondary small">
                                            <tr>
                                                <th style="width: 50px;" class="text-center text-white">#</th>
                                                <th style="width: 60px;" class="text-center text-white">Preview</th>
                                                <th class="text-white">File Name</th>
                                                <th class="text-white">Remark</th>
                                                <th style="width: 120px;" class="text-center text-white">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="view-table-body">
                                            <tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 text-light"></i><br>No files uploaded yet.</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="delete-file-form" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const canDeleteFiles = @json(canRoute('admin.media.destroy'));
            const deleteRouteTemplate = @json(route('admin.media.destroy', ':id'));
            const uploadEndpoint = @json(route('admin.media_files.upload')); 

            // 1. FILTER LOGIC
            document.getElementById('filter-btn')?.addEventListener('click', () => {
                const projectId = document.getElementById('project-id').value;
                const complianceId = document.getElementById('compliance-id').value;
                const phaseId = document.getElementById('phase-id').value || 0;
                const dateOfEntry = document.getElementById('date-of-entry').value;

                let urlTemplate = "{{ route('admin.social_safeguard_entries.index', ['project_id' => 'PROJECT_ID', 'compliance_id' => 'COMPLIANCE_ID', 'phase_id' => 'PHASE_ID']) }}";
                urlTemplate = urlTemplate.replace('PROJECT_ID', projectId).replace('COMPLIANCE_ID', complianceId).replace('PHASE_ID', phaseId);
                
                window.location.href = `${urlTemplate}?date_of_entry=${encodeURIComponent(dateOfEntry)}`;
            });

            // 2. ALERT HELPER
            const showAlert = (msg, type) => {
                const el = document.createElement("div");
                el.className = `alert alert-${type} shadow-sm`;
                el.textContent = msg;
                document.querySelector(".container").prepend(el);
                setTimeout(() => el.remove(), 4000);
            };

            // 3. ROW SAVING LOGIC (Fixed Backend Validation Mapping)
            document.querySelectorAll(".save-row").forEach(button => {
                button.addEventListener("click", async function() {
                    let row = this.closest("tr");
                    let actionUrl = this.dataset.url;
                    let actionMethod = this.dataset.method;

                    let socialId = row.dataset.socialId;
                    if (actionMethod === 'UPDATE' && (!socialId || socialId === '0')) {
                        return showAlert("Social entry missing for update.", "warning");
                    }

                    let data = new FormData();
                    data.append("yes_no", row.querySelector("[name='yes_no']").value);
                    data.append("remarks", row.querySelector("[name='remarks']").value);
                    
                    let validityDateInput = row.querySelector("[name='validity_date']");
                    data.append("validity_date", validityDateInput ? validityDateInput.value : '');
                    data.append("date_of_entry", row.querySelector("[name='date_of_entry']").value);

                    // ✅ FIX: Appending the required backend validation fields
                    data.append("already_define_safeguard_entry_id", row.dataset.entryId);
                    data.append("sub_package_project_id", document.getElementById('project-id').value);
                    data.append("social_compliance_id", document.getElementById('compliance-id').value);
                    data.append("contraction_phase_id", document.getElementById('phase-id').value);

                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    this.disabled = true;

                    try {
                        let response = await fetch(actionUrl, {
                            method: "POST",
                            headers: { "X-CSRF-TOKEN": csrfToken },
                            body: data
                        });

                        let result = await response.json();

                        if (result.status === "success") {
                            showAlert("Saved successfully!", "success");
                            
                            if (actionMethod === 'STORE') {
                                row.dataset.socialId = result.social_id || socialId;
                                this.dataset.method = 'UPDATE';
                                this.classList.replace('btn-success', 'btn-warning');
                                this.innerHTML = '<i class="fas fa-edit"></i> Update';
                                
                                const uploadBtn = row.querySelector('.open-upload-modal');
                                if (uploadBtn) uploadBtn.dataset.socialId = result.social_id || socialId;
                            } else {
                                this.innerHTML = originalHtml;
                            }

                            if (result.locked) {
                                row.querySelectorAll("input, select").forEach(x => x.setAttribute("disabled", true));
                                this.remove();
                            }
                        } else {
                            // Extract validation errors if present
                            let errorMsg = result.message ?? "Error saving.";
                            if (result.errors) {
                                errorMsg = Object.values(result.errors).flat().join(" | ");
                            }
                            showAlert(errorMsg, "danger");
                            this.innerHTML = originalHtml;
                        }
                    } catch (error) {
                        showAlert("An unexpected error occurred.", "danger");
                        this.innerHTML = originalHtml;
                    } finally {
                        this.disabled = false;
                    }
                });
            });

            // 4. MEDIA UPLOAD LOGIC
            const uploadModalEl = document.getElementById('uploadModal');
            const uploadModal = new bootstrap.Modal(uploadModalEl);
            const fileInput = document.getElementById('file-input');
            const uploadTableContainer = document.getElementById('upload-table-container');
            const uploadTableBody = document.getElementById('upload-table-body');
            const uploadActionContainer = document.getElementById('upload-action-container');
            const uploadBtn = document.getElementById('upload-btn');
            const uploadForm = document.getElementById('upload-form');
            const viewTableBody = document.getElementById('view-table-body');
            
            let fileState = [];

            const syncRemarks = () => {
                document.querySelectorAll('.remark-input').forEach(input => {
                    const index = parseInt(input.dataset.index, 10);
                    if (fileState[index]) fileState[index].remark = input.value;
                });
            };

            const formatBytes = (bytes, decimals = 1) => {
                if (!+bytes) return '0 B';
                const k = 1024, dm = decimals < 0 ? 0 : decimals, sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
            };

            const renderUploadTable = () => {
                uploadTableBody.innerHTML = '';
                
                if (fileState.length === 0) {
                    uploadTableContainer.classList.add('d-none');
                    uploadActionContainer.classList.add('d-none');
                    fileInput.value = ''; 
                    return;
                }

                uploadTableContainer.classList.remove('d-none');
                uploadActionContainer.classList.remove('d-none');

                fileState.forEach((item, index) => {
                    const { file, remark } = item;
                    const isImage = file.type.startsWith('image/');
                    const previewUrl = isImage ? URL.createObjectURL(file) : null;
                    
                    const previewHtml = isImage 
                        ? `<img src="${previewUrl}" class="rounded border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">` 
                        : `<div class="bg-light border rounded d-flex align-items-center justify-content-center text-secondary shadow-sm" style="width: 40px; height: 40px;"><i class="fas fa-file-alt"></i></div>`;

                    uploadTableBody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td class="text-center">${previewHtml}</td>
                            <td class="text-truncate fw-medium text-dark" style="max-width: 200px;" title="${file.name}">${file.name}</td>
                            <td>
                                <input type="text" class="form-control form-control-sm remark-input bg-light" data-index="${index}" value="${remark}" placeholder="Add an optional remark...">
                            </td>
                            <td class="small text-muted">${formatBytes(file.size)}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-light text-danger remove-file border-0" data-index="${index}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            };

            fileInput.addEventListener('change', (e) => {
                syncRemarks();
                const newFiles = Array.from(e.target.files).map(file => ({ file, remark: '' }));
                fileState = [...fileState, ...newFiles];
                renderUploadTable();
            });

            uploadTableBody.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-file');
                if (removeBtn) {
                    syncRemarks();
                    const index = parseInt(removeBtn.dataset.index, 10);
                    
                    if (fileState[index].file.type.startsWith('image/')) {
                        const img = uploadTableBody.querySelector(`tr:nth-child(${index + 1}) img`);
                        if (img) URL.revokeObjectURL(img.src);
                    }
                    
                    fileState.splice(index, 1);
                    renderUploadTable();
                }
            });

            document.body.addEventListener('click', (e) => {
                const openModalBtn = e.target.closest('.open-upload-modal');
                if (openModalBtn) {
                    const socialId = openModalBtn.dataset.socialId;
                    
                    if (!socialId || socialId === '0' || socialId === '') {
                        showAlert('Please save the entry row first before uploading files.', 'warning');
                        return;
                    }

                    document.getElementById('modal-social-id').value = socialId;
                    const mediaIds = JSON.parse(openModalBtn.dataset.mediaIds || "[]");
                    
                    fileState = [];
                    renderUploadTable();
                    fileInput.value = "";
                    
                    loadViewTable(mediaIds);
                    bootstrap.Tab.getOrCreateInstance(document.getElementById('upload-tab')).show();
                    uploadModal.show();
                }

                const deleteBtn = e.target.closest('.delete-file');
                if (deleteBtn) {
                    if (!confirm('Are you sure you want to delete this file?')) return;
                    const form = document.getElementById('delete-file-form');
                    form.action = deleteRouteTemplate.replace(':id', deleteBtn.dataset.id);
                    form.submit();
                }
            });

            uploadForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                syncRemarks();

                const socialId = document.getElementById('modal-social-id').value;
                if (!socialId || fileState.length === 0) return;

                const formData = new FormData();
                formData.append('social_id', socialId);

                fileState.forEach((item, index) => {
                    formData.append(`media_files[${index}]`, item.file);
                    formData.append(`remarks[${index}]`, item.remark);
                });

                const originalBtnHtml = uploadBtn.innerHTML;
                uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
                uploadBtn.disabled = true;

                try {
                    const response = await fetch(uploadEndpoint, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        showAlert('Files uploaded successfully!', 'success');
                        
                        fileState = [];
                        renderUploadTable();
                        
                        const currentMediaIds = data.files.map(f => f.id);
                        document.querySelector(`tr[data-social-id="${socialId}"] .open-upload-modal`).dataset.mediaIds = JSON.stringify(currentMediaIds);
                        
                        loadViewTable(currentMediaIds);
                        bootstrap.Tab.getOrCreateInstance(document.getElementById('view-tab')).show();
                    } else {
                        showAlert(data.message || 'Upload failed.', 'danger');
                    }
                } catch (error) {
                    showAlert('An unexpected network error occurred.', 'danger');
                } finally {
                    uploadBtn.innerHTML = originalBtnHtml;
                    uploadBtn.disabled = false;
                }
            });

            const loadViewTable = async (mediaIds) => {
                viewTableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></td></tr>';

                if (!mediaIds || mediaIds.length === 0) {
                    viewTableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 text-light"></i><br>No files uploaded yet.</td></tr>';
                    return;
                }

                try {
                    const url = new URL("{{ route('media-files.by-ids') }}", window.location.origin);
                    mediaIds.forEach(id => url.searchParams.append('ids[]', id));

                    const response = await fetch(url);
                    const files = await response.json();
                    viewTableBody.innerHTML = '';

                    files.forEach((file, index) => {
                        const fileName = file.meta_data?.name ?? `File #${file.id}`;
                        const isImage = file.mime_type?.startsWith('image');
                        const previewHtml = isImage 
                            ? `<img src="${file.url}" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;">` 
                            : `<div class="bg-light border rounded d-flex align-items-center justify-content-center text-secondary" style="width: 40px; height: 40px;"><i class="fas fa-file-alt"></i></div>`;
                        
                        const deleteBtnHtml = canDeleteFiles 
                            ? `<button type="button" class="btn btn-sm btn-outline-danger border-0 delete-file" data-id="${file.id}"><i class="fas fa-trash"></i></button>` 
                            : '';

                        viewTableBody.insertAdjacentHTML('beforeend', `
                            <tr data-id="${file.id}">
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${previewHtml}</td>
                                <td class="text-truncate fw-medium text-dark" style="max-width: 200px;" title="${fileName}">${fileName}</td>
                                <td class="text-muted small">${file.remark || '-'}</td>
                                <td class="text-center">
                                    <a href="${file.url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light text-primary me-1 border-0">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    ${deleteBtnHtml}
                                </td>
                            </tr>
                        `);
                    });
                } catch (error) {
                    viewTableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Failed to load media files.</td></tr>';
                }
            };
        });
    </script>
   
</x-app-layout>