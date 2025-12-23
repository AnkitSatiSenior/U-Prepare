@props(['subProjectId', 'complianceId', 'phaseId'])

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const canDeleteFiles = @json(canRoute('admin.media.destroy'));
        const fileInput = document.getElementById('file-input');
        const uploadTableBody = document.querySelector('#upload-table tbody');
        const viewTableBody = document.getElementById('view-table-body');
        const uploadForm = document.getElementById('upload-form');
        const deleteForm = document.getElementById('delete-file-form');
        const deleteRouteTemplate = @json(route('admin.media.destroy', ':id'));

        // ---------- Helper: Get file icon ----------
        const getIcon = filename => {
            const ext = filename.split('.').pop().toLowerCase();
            const icons = {
                pdf: 'far fa-file-pdf text-danger',
                doc: 'far fa-file-word text-primary',
                docx: 'far fa-file-word text-primary',
                xls: 'far fa-file-excel text-success',
                xlsx: 'far fa-file-excel text-success',
                jpg: 'far fa-file-image text-warning',
                jpeg: 'far fa-file-image text-warning',
                png: 'far fa-file-image text-warning'
            };
            return icons[ext] ?? 'far fa-file';
        };

        // ---------- Show selected files before upload ----------
        fileInput.addEventListener('change', () => {
            uploadTableBody.innerHTML = '';
            Array.from(fileInput.files).forEach((file, index) => {
                uploadTableBody.innerHTML += `
                <tr>
                    <td>${file.name}</td>
                    <td>${(file.size / 1024).toFixed(2)} KB</td>
                    <td>${file.type || '-'}</td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-file" data-index="${index}">Remove</button></td>
                </tr>`;
            });
            document.getElementById('upload-table').classList.toggle('d-none', !fileInput.files.length);
        });

        // ---------- Remove selected file ----------
        uploadTableBody.addEventListener('click', e => {
            if (!e.target.classList.contains('remove-file')) return;
            const idx = e.target.dataset.index;
            const dt = new DataTransfer();
            Array.from(fileInput.files).filter((_, i) => i != idx).forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
            e.target.closest('tr').remove();
            if (!uploadTableBody.children.length) document.getElementById('upload-table').classList.add(
                'd-none');
        });

        // ---------- Open modal & populate existing files ----------
        document.querySelectorAll('.open-upload-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                const entryId = btn.dataset.entryId;
                const socialId = btn.dataset.socialId;

                if (!socialId) {
                    alert('Please save the entry before uploading files.');
                    btn.closest('tr').querySelector('.save-row')?.focus();
                    return;
                }

                document.getElementById('modal-entry-id').value = entryId;
                document.getElementById('modal-social-id').value = socialId;

                viewTableBody.innerHTML = '';
                const uploadedFiles = btn.closest('tr').querySelectorAll(
                    '.uploaded-files ul li');
                if (uploadedFiles.length) {
                    uploadedFiles.forEach(li => {
                        const name = li.querySelector('a').innerText;
                        const path = li.querySelector('a').href;
                        const icon = li.querySelector('i').className;
                        const ext = name.split('.').pop().toUpperCase();
                        const id = li.dataset.id;
                        viewTableBody.innerHTML += `
                        <tr data-id="${id}">
                            <td>${name}</td>
                            <td>${id || '-'}</td>
                            <td>${ext}</td>
                            <td>
                                <a href="${path}" target="_blank" class="btn btn-sm btn-primary me-2">
                                    <i class="${icon}"></i> View
                                </a>
                                ${canDeleteFiles && id ? `<button type="button" class="btn btn-sm btn-danger delete-file" data-id="${id}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>` : ''}
                            </td>
                        </tr>`;
                    });
                } else {
                    viewTableBody.innerHTML =
                        `<tr><td colspan="4" class="text-center">No files uploaded yet.</td></tr>`;
                }

                new bootstrap.Modal(document.getElementById('uploadModal')).show();
            });
        });

        // ---------- Save main row / entry ----------
        document.querySelectorAll(".save-row").forEach(btn => {
            btn.addEventListener("click", async () => {
                const row = btn.closest("tr");
                const entryId = row.dataset.entryId; // already_define_safeguard_already_define_safeguard_entry_id
                const hasSocial = row.dataset.hasSocial; // 0 = insert, 1 = update
                const socialId = row.dataset.socialId;

                // Determine URL: save or update
                const url = hasSocial == "1" ?
                    "{{ route('admin.social.update', ':id') }}".replace(":id", socialId) :
                    "{{ route('admin.social_safeguard_entries.save') }}";

                const data = new FormData();

                // Required fields
                data.append("already_define_safeguard_entry_id", entryId);
                data.append("sub_package_project_id", "{{ $subProjectId }}");
                data.append("social_compliance_id", "{{ $complianceId }}");
                data.append("contraction_phase_id", "{{ $phaseId }}");

                // User inputs
                const yesNo = row.querySelector('[name="yes_no"]')?.value ?? "";
                const remarks = row.querySelector('[name="remarks"]')?.value ?? "";
                const validityDate = row.querySelector('[name="validity_date"]')?.value ??
                    "";
                const dateOfEntry = row.querySelector('[name="date_of_entry"]')?.value ??
                "";

                data.append("yes_no", yesNo);
                data.append("remarks", remarks);
                data.append("validity_date", validityDate);
                data.append("date_of_entry", dateOfEntry);

                // Handle file uploads
                const fileInputs = row.querySelectorAll(
                    '[name="photos_documents_case_studies[]"]');
                fileInputs.forEach(input => {
                    if (input.files.length) {
                        for (let i = 0; i < input.files.length; i++) {
                            data.append("photos_documents_case_studies[]", input
                                .files[i]);
                        }
                    }
                });

                try {
                    const res = await fetch(url, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]').content
                        },
                        body: data
                    });

                    const result = await res.json();

                    if (result.status === "success") {
                        alert("Saved Successfully!");
                        // Optionally: update row without reload
                        window.location.reload();
                    } else {
                        alert(result.message || "Failed to save.");
                    }

                } catch (e) {
                    console.error(e);
                    alert("Error saving entry.");
                }
            });
        });


        // ---------- Upload files via modal ----------
uploadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const entryId = document.getElementById('modal-entry-id')?.value;
    const socialId = document.getElementById('modal-social-id')?.value;

    // Stop silently if entry not saved
    if (!entryId || !socialId) return;

    const row = document.querySelector(`tr[data-entry-id="${entryId}"]`);
    const formData = new FormData(uploadForm);

    // Append required backend fields
    formData.append('social_id', socialId);
    formData.append('sub_package_project_id', "{{ $subProjectId }}");
    formData.append('safeguard_compliance_id', "{{ $complianceId }}");
    formData.append('contraction_phase_id', "{{ $phaseId }}");

    try {
        const res = await fetch("{{ route('admin.media_files.upload') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute('content')
            },
            body: formData
        });

        if (!res.ok) return;

        const data = await res.json();

        // ✅ Handle success ONLY
        if (data.status === 'success') {

            /* ============================
               SAFE FILE LIST RENDERING
            ============================ */
            if (row) {
                const uploadedFilesContainer =
                    row.querySelector('.uploaded-files');

                if (uploadedFilesContainer) {
                    let rowUl = uploadedFilesContainer.querySelector('ul');

                    if (!rowUl) {
                        rowUl = document.createElement('ul');
                        rowUl.className = 'list-unstyled mb-0';
                        uploadedFilesContainer.innerHTML = '';
                        uploadedFilesContainer.appendChild(rowUl);
                    }

                    data.files?.forEach(file => {
                        const icon = getIcon(file.name);
                        rowUl.insertAdjacentHTML('beforeend', `
                            <li data-id="${file.id}">
                                <i class="${icon}"></i>
                                <a href="${file.url}" target="_blank">
                                    ${file.name}
                                </a>
                            </li>
                        `);
                    });
                }
            }

            /* ============================
               SAFE VIEW TABLE UPDATE
            ============================ */
            if (typeof viewTableBody !== 'undefined' && viewTableBody) {
                data.files?.forEach(file => {
                    const icon = getIcon(file.name);
                    viewTableBody.insertAdjacentHTML('beforeend', `
                        <tr data-id="${file.id}">
                            <td>${file.name}</td>
                            <td>${file.id}</td>
                            <td>${file.name.split('.').pop().toUpperCase()}</td>
                            <td>
                                <a href="${file.url}" target="_blank">
                                    <i class="${icon}"></i> View
                                </a>
                            </td>
                        </tr>
                    `);
                });
            }

            /* ============================
               CLOSE MODAL SAFELY
            ============================ */
            const modalEl = document.getElementById('uploadModal');
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                modalInstance?.hide();
            }

            /* ============================
               SUCCESS ALERT (GUARANTEED)
            ============================ */
            const alertBox = document.createElement('div');
            alertBox.className =
                'alert alert-success text-center position-fixed top-0 start-50 translate-middle-x mt-3';
            alertBox.style.zIndex = '9999';
            alertBox.style.width = '420px';
            alertBox.innerHTML = `<strong>✅ ${data.message}</strong>`;

            document.body.appendChild(alertBox);

            setTimeout(() => {
                alertBox.remove();
                window.location.reload();
            }, 2000);
        }

    } catch (err) {
        console.error('Upload JS Error:', err);
    }
});


        // ---------- Delete file ----------
        viewTableBody.addEventListener('click', e => {
            const btn = e.target.closest('.delete-file');
            if (!btn || !canDeleteFiles) return;

            const fileId = btn.dataset.id;
            if (!fileId) return;
            if (!confirm('Are you sure you want to delete this file?')) return;

            deleteForm.action = deleteRouteTemplate.replace(':id', fileId);
            deleteForm.submit();
        });
    });
</script>
