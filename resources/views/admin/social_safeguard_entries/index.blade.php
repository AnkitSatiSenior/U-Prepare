<x-app-layout>
    <div class="container py-5">

        <h2 class="mb-4 text-primary fw-bold">
            {{ $subProject->name }} — {{ $compliance->name }} Safeguard Entries
        </h2>
        {{-- Flash messages --}}
        @if (session()->has('message'))
            <div class="alert alert-{{ session('status') }} alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter Form --}}
        <div class="col-md-2">
            <label class="form-label">Safeguard Compliance</label>
            <select name="safeguard_compliance_id" class="form-control">
                <option value="{{ $compliance->id }}">{{ $compliance->name }}</option>
            </select>
        </div>
        <div class="row mb-4">
            <input type="hidden" id="project-id" value="{{ $subProject->id }}">
            <input type="hidden" id="compliance-id" value="{{ $compliance->id }}">

            <div class="col-md-2">
                <label class="form-label">Contraction Phase</label>
                <select id="phase-id" class="form-control">
                    <option value="">-- All --</option>
                    @foreach ($compliance->contractionPhases as $phase)
                        <option value="{{ $phase->id }}" {{ $phase->id == $phase_id ? 'selected' : '' }}>
                            {{ $phase->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Date of Entry</label>
                <input type="date" id="date-of-entry" class="form-control"
                    value="{{ request('date_of_entry', now()->format('Y-m-d')) }}">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button id="filter-btn" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>

        <script>
            document.getElementById('filter-btn').addEventListener('click', function() {
                const projectId = document.getElementById('project-id').value;
                const complianceId = document.getElementById('compliance-id').value;
                const phaseId = document.getElementById('phase-id').value || 0; // optional
                const dateOfEntry = document.getElementById('date-of-entry').value;

                // Use Laravel route template with placeholders
                let urlTemplate =
                    "{{ route('admin.social_safeguard_entries.index', ['project_id' => 'PROJECT_ID', 'compliance_id' => 'COMPLIANCE_ID', 'phase_id' => 'PHASE_ID']) }}";

                // Replace placeholders with actual values
                urlTemplate = urlTemplate
                    .replace('PROJECT_ID', projectId)
                    .replace('COMPLIANCE_ID', complianceId)
                    .replace('PHASE_ID', phaseId);

                // Append date_of_entry as query string
                urlTemplate += `?date_of_entry=${dateOfEntry}`;

                window.location.href = urlTemplate;
            });
        </script>

        {{-- Entries Table --}}
        @php
            // Pre-calculate SL numbers once
            $allSlNos = $entries->pluck('sl_no')->toArray();

            // Gather all media IDs to eager load files
            $allMediaIds = $entries
                ->pluck('social.photos_documents_case_studies')
                ->flatten()
                ->filter()
                ->unique()
                ->toArray();

            $mediaFiles = \App\Models\MediaFile::whereIn('id', $allMediaIds)->get()->keyBy('id');
        @endphp

        @if ($entries->isNotEmpty())
            <form id="social-safeguard-form">
                <div class="table-responsive">
                    <x-admin.data-table id="social-safeguard-table" :headers="['SL No', 'Item', 'Yes/No', 'Remarks', 'Validity', 'Date of Entry', 'Action', 'Files']" :excel="true"
                        :paging="false" :pageLength="1000">
                        @foreach ($entries as $entry)
                            @php
                                $isParent = collect($allSlNos)->contains(
                                    fn($sl) => Str::startsWith($sl, $entry->sl_no . '.'),
                                );
                                $level = substr_count($entry->sl_no, '.');
                                $social = $entry->social ?? null;
                                $locked = $entry->is_locked ?? false;
                                $social = $entry->social;
                                $filesExist = $social && !empty($social->photos_documents_case_studies);
                                $filesExist = $social && !empty($social->photos_documents_case_studies);
                            @endphp

                            <tr class="{{ $isParent ? 'table-secondary fw-bold' : '' }}"
                                data-entry-id="{{ $entry->id }}" data-social-id="{{ $social?->id }}"
                                data-has-social="{{ $social ? 1 : 0 }}">

                                {{-- SL No --}}
                                <td>{{ $entry->sl_no }}</td>

                                {{-- Item Description --}}
                                <td class="text-start" style="padding-left: {{ $level * 20 }}px;">
                                    {{ $entry->item_description ?? '-' }}
                                </td>

                                {{-- Yes/No --}}
                                <td>
                                    @if ($isParent)
                                        <span class="text-muted">—</span>
                                    @else
                                        <select name="yes_no" class="form-control" {{ $locked ? 'disabled' : '' }}>
                                            <option value="">Select</option>
                                            <option value="1" {{ $social?->yes_no == 1 ? 'selected' : '' }}>Yes
                                            </option>
                                            <option value="0" {{ $social?->yes_no == 0 ? 'selected' : '' }}>No
                                            </option>
                                            <option value="3" {{ $social?->yes_no == 3 ? 'selected' : '' }}>N/A
                                            </option>
                                        </select>
                                    @endif
                                </td>

                                {{-- Remarks --}}
                                <td>
                                    @if ($isParent)
                                        <span class="text-muted">—</span>
                                    @else
                                        <input type="text" name="remarks" class="form-control"
                                            value="{{ $social->remarks ?? '' }}" {{ $locked ? 'readonly' : '' }}>
                                    @endif
                                </td>

                                {{-- Validity --}}
                                <td>
                                    @if ($isParent)
                                        <span class="text-muted">—</span>
                                    @elseif($entry->is_validity)
                                        <input type="date" name="validity_date" class="form-control"
                                            value="{{ $social?->validity_date?->format('Y-m-d') ?? '' }}"
                                            {{ $locked ? 'readonly' : '' }}>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                {{-- Date of Entry --}}
                                <td>
                                    @if ($isParent)
                                        <span class="text-muted">—</span>
                                    @else
                                        <input type="date" name="date_of_entry" class="form-control"
                                            value="{{ $social?->date_of_entry?->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                                            max="{{ now()->format('Y-m-d') }}" {{ $locked ? 'readonly' : '' }}>
                                    @endif
                                </td>

                                {{-- Action Buttons --}}
                                <td>
                                    @if (!$isParent && !$locked)
                                        <button type="button"
                                            class="btn btn-sm {{ $social ? 'btn-warning' : 'btn-success' }} save-row">
                                            <i class="fas {{ $social ? 'fa-edit' : 'fa-save' }}"></i>
                                            {{ $social ? 'Update' : 'Save' }}
                                        </button>
                                    @endif
                                </td>

                                <td
                                    class="{{ $isParent ? 'bg-light' : ($filesExist ? 'bg-light-success' : 'bg-light-danger') }}">
                                    @if ($isParent)
                                        <span class="text-muted">—</span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-primary open-upload-modal"
                                            data-entry-id="{{ $entry->id }}" data-social-id="{{ $social?->id }}"
                                            data-media-ids='@json($social?->photos_documents_case_studies ?? [])'>
                                            {{ $filesExist ? 'Manage Files' : 'Upload Files' }}
                                        </button>
                                    @endif
                                </td>



                            </tr>
                        @endforeach
                    </x-admin.data-table>
                </div>
            </form>
        @else
            <div class="alert alert-warning text-center">
                @if (request()->has('sub_package_project_id'))
                    No entries found for the selected filters.
                @else
                    Please select a project and date to view entries.
                @endif
            </div>
        @endif



        {{-- Upload Modal --}}
        <x-upload-modal />
        <form id="delete-file-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        <script>
            $(document).on('click', '.open-upload-modal', function() {

                const entryId = $(this).data('entry-id');
                const socialId = $(this).data('social-id');
                const mediaIds = $(this).data('media-ids') || [];

                $('#modal-entry-id').val(entryId);
                $('#modal-social-id').val(socialId);

                // Reset view table
                const tbody = $('#view-table-body');
                tbody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

                if (!mediaIds.length) {
                    tbody.html('<tr><td colspan="4" class="text-center">No files uploaded yet.</td></tr>');
                    $('#uploadModal').modal('show');
                    return;
                }

                $.get("{{ route('media-files.by-ids') }}", {
                    ids: mediaIds
                }, function(files) {

                    tbody.empty();

                    files.forEach((file, index) => {

                        const fileName = file.meta_data?.name ?? `File #${file.id}`;
                        const fileUrl = `/storage/${file.path}`;
                        const isImage = file.mime_type?.startsWith('image');

                        tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${fileName}</td>
                    <td>${file.mime_type ?? '-'}</td>
                    <td>
                        ${
                            isImage
                            ? `<img src="${fileUrl}" height="40" class="rounded">`
                            : `<a href="${fileUrl}" target="_blank">View</a>`
                        }
                    </td>
                </tr>
            `);
                    });

                });

                $('#uploadModal').modal('show');
            });
        </script>

    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".save-row").forEach(button => {
                button.addEventListener("click", async function() {
                    let row = this.closest("tr");
                    let socialId = row.dataset.socialId;
                    if (!socialId) return alert("Social entry missing.");
                    let data = new FormData();
                    data.append("yes_no", row.querySelector("[name='yes_no']").value);
                    data.append("remarks", row.querySelector("[name='remarks']").value);
                    data.append("validity_date", row.querySelector("[name='validity_date']")
                        ?.value || null);
                    data.append("date_of_entry", row.querySelector("[name='date_of_entry']")
                        .value);
                    let response = await fetch(
                        "{{ route('admin.social.update', ['id' => 'SID']) }}".replace(
                            "SID", socialId), {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: data
                        });
                    let result = await response.json();
                    if (result.status === "success") {
                        showAlert("Saved successfully!", "success");
                        if (result.locked) {
                            row.querySelectorAll("input, select").forEach(x => x.setAttribute(
                                "disabled", true));
                            this.remove();
                        }
                    } else {
                        showAlert(result.message ?? "Error saving.", "danger");
                    }
                });
            });

            function showAlert(msg, type) {
                const el = document.createElement("div");
                el.className = alert alert - $ {
                    type
                };
                el.textContent = msg;
                document.querySelector(".container").prepend(el);
                setTimeout(() => el.remove(), 2500);
            }
        });
    </script>
    {{-- JS Scripts --}}
    <x-upload-js :subProjectId="$subProject->id" :complianceId="$compliance->id" :phaseId="$phase_id" />
</x-app-layout>
