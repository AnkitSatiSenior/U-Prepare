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
            <div class="table-responsive overflow-hidden">
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
                                    <button type="button" class="btn btn-sm btn-primary open-upload-modal " data-entry-id="{{ $entry->id }}" data-social-id="{{ $social?->id }}" data-media-ids='@json($social?->photos_documents_case_studies ?? [])'>
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

        <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fas fa-images text-primary"></i> Media Manager</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">Upload Files</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="view-tab" data-bs-toggle="tab" data-bs-target="#view" type="button">View Uploaded</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="upload">
                                <form id="upload-form">
                                    <input type="hidden" id="modal-social-id">
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Select Files</label>
                                        <input type="file" multiple class="form-control" id="file-input" accept="image/*,application/pdf,.doc,.docx">
                                    </div>

                                    <div class="table-responsive d-none" id="upload-table-container">
                                        <table class="table table-bordered table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 60px;">Preview</th>
                                                    <th>File Name</th>
                                                    <th style="width: 40%;">Remark</th>
                                                    <th style="width: 100px;">Size</th>
                                                    <th style="width: 80px;" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="upload-table-body"></tbody>
                                        </table>
                                    </div>

                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-primary d-none" id="upload-btn">
                                            <i class="fas fa-cloud-upload-alt me-1"></i> Upload All Files
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="view">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th style="width: 60px;">Preview</th>
                                                <th>File Name</th>
                                                <th>Remark</th>
                                                <th style="width: 120px;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="view-table-body">
                                            <tr><td colspan="5" class="text-center text-muted">No files uploaded yet.</td></tr>
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
            
            const uploadModalEl = document.getElementById('uploadModal');
            const uploadModal = new bootstrap.Modal(uploadModalEl);
            const fileInput = document.getElementById('file-input');
            const uploadTableContainer = document.getElementById('upload-table-container');
            const uploadTableBody = document.getElementById('upload-table-body');
            const uploadBtn = document.getElementById('upload-btn');
            const uploadForm = document.getElementById('upload-form');
            const viewTableBody = document.getElementById('view-table-body');
            const viewTabBtn = document.getElementById('view-tab');
            
            let selectedFiles = [];

            const showAlert = (msg, type) => {
                const alertEl = document.createElement("div");
                alertEl.className = `alert alert-${type} alert-dismissible fade show shadow-sm position-fixed top-0 start-50 translate-middle-x mt-4`;
                alertEl.style.zIndex = 1060;
                alertEl.innerHTML = `${msg} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                document.body.appendChild(alertEl);
                setTimeout(() => alertEl.remove(), 4000);
            };

            document.getElementById('filter-btn')?.addEventListener('click', () => {
                const projectId = document.getElementById('project-id').value;
                const complianceId = document.getElementById('compliance-id').value;
                const phaseId = document.getElementById('phase-id').value || 0;
                const dateOfEntry = document.getElementById('date-of-entry').value;

                const urlTemplate = "{{ route('admin.social_safeguard_entries.index', ['project_id' => '__PROJ__', 'compliance_id' => '__COMP__', 'phase_id' => '__PHASE__']) }}"
                    .replace('__PROJ__', projectId)
                    .replace('__COMP__', complianceId)
                    .replace('__PHASE__', phaseId);

                window.location.href = `${urlTemplate}?date_of_entry=${encodeURIComponent(dateOfEntry)}`;
            });

            document.body.addEventListener('click', async (e) => {
                const saveBtn = e.target.closest('.save-row');
                if (saveBtn) {
                    const row = saveBtn.closest("tr");
                    const { url, method } = saveBtn.dataset;
                    const socialId = row.dataset.socialId;

                    if (method === 'UPDATE' && (!socialId || socialId === '0')) {
                        return showAlert("Social entry missing for update.", "warning");
                    }

                    const data = new FormData();
                    data.append("yes_no", row.querySelector("[name='yes_no']")?.value || "");
                    data.append("remarks", row.querySelector("[name='remarks']")?.value || "");
                    data.append("validity_date", row.querySelector("[name='validity_date']")?.value || "");
                    data.append("date_of_entry", row.querySelector("[name='date_of_entry']")?.value || "");

                    const originalHtml = saveBtn.innerHTML;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    saveBtn.disabled = true;

                    try {
                        const response = await fetch(url, {
                            method: "POST",
                            headers: { "X-CSRF-TOKEN": csrfToken },
                            body: data
                        });

                        const result = await response.json();
                        if (result.status === "success") {
                            showAlert("Saved successfully!", "success");
                            if (result.locked) {
                                row.querySelectorAll("input, select").forEach(el => el.disabled = true);
                                saveBtn.remove();
                            } else {
                                row.dataset.socialId = result.social_id || socialId;
                                saveBtn.dataset.method = 'UPDATE';
                                saveBtn.classList.replace('btn-success', 'btn-warning');
                                saveBtn.innerHTML = '<i class="fas fa-edit"></i> Update';
                            }
                        } else {
                            showAlert(result.message || "Error saving data.", "danger");
                            saveBtn.innerHTML = originalHtml;
                        }
                    } catch (error) {
                        showAlert("An unexpected error occurred.", "danger");
                        saveBtn.innerHTML = originalHtml;
                    } finally {
                        saveBtn.disabled = false;
                    }
                }

                const openModalBtn = e.target.closest('.open-upload-modal');
                if (openModalBtn) {
                    document.getElementById('modal-social-id').value = openModalBtn.dataset.socialId;
                    const mediaIds = JSON.parse(openModalBtn.dataset.mediaIds || "[]");
                    
                    selectedFiles = [];
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

            fileInput.addEventListener('change', (e) => {
                selectedFiles = Array.from(e.target.files);
                renderUploadTable();
            });

            uploadTableBody.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-file');
                if (removeBtn) {
                    const index = parseInt(removeBtn.dataset.index, 10);
                    selectedFiles.splice(index, 1);
                    renderUploadTable();
                }
            });

            const renderUploadTable = () => {
                uploadTableBody.innerHTML = '';
                
                if (selectedFiles.length === 0) {
                    uploadTableContainer.classList.add('d-none');
                    uploadBtn.classList.add('d-none');
                    fileInput.value = ""; 
                    return;
                }

                uploadTableContainer.classList.remove('d-none');
                uploadBtn.classList.remove('d-none');

                selectedFiles.forEach((file, index) => {
                    const isImage = file.type.startsWith('image/');
                    const previewUrl = isImage ? URL.createObjectURL(file) : null;
                    const previewHtml = isImage 
                        ? `<img src="${previewUrl}" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;">` 
                        : `<div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-file-alt text-muted"></i></div>`;

                    uploadTableBody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${previewHtml}</td>
                            <td class="text-truncate" style="max-width: 200px;" title="${file.name}">${file.name}</td>
                            <td>
                                <input type="text" id="remark-${index}" class="form-control form-control-sm" placeholder="Add a remark...">
                            </td>
                            <td class="small text-muted">${(file.size / 1024).toFixed(1)} KB</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-file" data-index="${index}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            };

            uploadForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const socialId = document.getElementById('modal-social-id').value;
                
                if (!socialId || socialId === '0') {
                    return showAlert('Please save the entry row first before uploading files.', 'warning');
                }

                const formData = new FormData();
                formData.append('social_id', socialId);

                selectedFiles.forEach((file, index) => {
                    formData.append(`media_files[${index}]`, file);
                    const remarkInput = document.getElementById(`remark-${index}`);
                    formData.append(`remarks[${index}]`, remarkInput ? remarkInput.value : '');
                });

                const originalBtnHtml = uploadBtn.innerHTML;
                uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Uploading...';
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
                        selectedFiles = [];
                        renderUploadTable();
                        
                        const currentMediaIds = data.files.map(f => f.id);
                        document.querySelector(`tr[data-social-id="${socialId}"] .open-upload-modal`).dataset.mediaIds = JSON.stringify(currentMediaIds);
                        
                        loadViewTable(currentMediaIds);
                        bootstrap.Tab.getOrCreateInstance(viewTabBtn).show();
                    } else {
                        showAlert(data.message || 'Upload failed.', 'danger');
                    }
                } catch (error) {
                    showAlert('An unexpected error occurred during upload.', 'danger');
                } finally {
                    uploadBtn.innerHTML = originalBtnHtml;
                    uploadBtn.disabled = false;
                }
            });

            const loadViewTable = async (mediaIds) => {
                viewTableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></td></tr>';

                if (!mediaIds || mediaIds.length === 0) {
                    viewTableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No files uploaded yet.</td></tr>';
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
                            : `<div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-file-alt text-muted"></i></div>`;
                        
                        const deleteBtnHtml = canDeleteFiles 
                            ? `<button type="button" class="btn btn-sm btn-outline-danger delete-file" data-id="${file.id}"><i class="fas fa-trash"></i></button>` 
                            : '';

                        viewTableBody.insertAdjacentHTML('beforeend', `
                            <tr data-id="${file.id}">
                                <td>${index + 1}</td>
                                <td>${previewHtml}</td>
                                <td class="text-truncate" style="max-width: 200px;" title="${fileName}">${fileName}</td>
                                <td class="text-muted small">${file.remark || '-'}</td>
                                <td class="text-center">
                                    <a href="${file.url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    ${deleteBtnHtml}
                                </td>
                            </tr>
                        `);
                    });
                } catch (error) {
                    viewTableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load media files.</td></tr>';
                }
            };
        });
    </script>
  
</x-app-layout>