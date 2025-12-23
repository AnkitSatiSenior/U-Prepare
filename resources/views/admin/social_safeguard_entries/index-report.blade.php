<x-app-layout>
    <div class="container py-5">

        <h2 class="mb-4 text-primary fw-bold">
            {{ $subProject->name }} — {{ $compliance->name }} Safeguard Entries
        </h2>
        @php
            $selectedPhase = $compliance->contractionPhases->firstWhere('id', request('phase_id'));
            $selectedDate = request('date_of_entry');
        @endphp
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
                    "{{ route('admin.report.indexReport', ['project_id' => 'PROJECT_ID', 'compliance_id' => 'COMPLIANCE_ID', 'phase_id' => 'PHASE_ID']) }}";

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
            {{-- READ-ONLY REPORT TABLE --}}
            <div class="table-responsive mt-4">

                @php
                    $selectedPhase = $compliance->contractionPhases->firstWhere('id', $phase_id);
                    $selectedDateFormatted = \Carbon\Carbon::parse($selectedDate)->format('d M, Y');
                @endphp

                <x-admin.data-table id="report-view-table" :headers="['SL No', 'Item', 'Yes/No', 'Remarks', 'Validity', 'Date of Entry', 'Files']" :excel="true" :print="true"
                    :pageLength="50" searchPlaceholder="Search items..."
                    title="{{ $subProject->name .
                        ' — ' .
                        $compliance->name .
                        ' Safeguard Entries' .
                        ($selectedPhase ? ' (Phase: ' . $selectedPhase->name . ')' : '') .
                        ($selectedDate ? ' — (Date: ' . $selectedDateFormatted . ')' : '') }}">

                    @php
                        $allSlNos = $entries->pluck('sl_no')->toArray();
                    @endphp

                    @foreach ($entries as $entry)
                        @php
                            $isParent = collect($allSlNos)->contains(
                                fn($sl) => Str::startsWith($sl, $entry->sl_no . '.'),
                            );
                            $level = substr_count($entry->sl_no, '.');
                            $social = $entry->social;
                            $filesExist = $social && !empty($social->photos_documents_case_studies);
                        @endphp

                        <tr class="{{ $isParent ? 'table-primary fw-bold' : '' }}">

                            {{-- SL No --}}
                            <td>{{ $entry->sl_no }}</td>

                            {{-- Item --}}
                            <td class="text-start" style="padding-left: {{ $level * 20 }}px;">
                                {{ $entry->definedSafeguard->item_description }}
                            </td>

                            {{-- Yes / No --}}
                            <td>
                                @if ($isParent)
                                    -
                                @elseif ($social?->yes_no === 1)
                                    <span class="text-success fw-bold">Yes</span>
                                @elseif ($social?->yes_no === 0)
                                    <span class="text-danger fw-bold">No</span>
                                @elseif ($social?->yes_no === 3)
                                    <span class="text-muted">N/A</span>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Remarks --}}
                            <td>
                                {{ $isParent ? '-' : $social->remarks ?? '-' }}
                            </td>

                            {{-- Validity --}}
                            <td>
                                @if ($isParent)
                                    -
                                @elseif ($entry->is_validity && $social?->validity_date)
                                    {{ $social->validity_date->format('d M Y') }}
                                @elseif (!$entry->is_validity)
                                    N/A
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Date of Entry --}}
                            <td>
                                @if ($isParent)
                                    -
                                @else
                                    {{ $social?->date_of_entry?->format('d M Y') ?? '-' }}
                                @endif
                            </td>

                            {{-- Files --}}
                            <td
                                class="uploaded-files
                            @if ($isParent) bg-light
                            @elseif($filesExist) bg-light-success
                            @else bg-light-danger @endif">

                                @if ($isParent)
                                    <span class="text-muted">—</span>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary open-upload-modal mt-1"
                                        data-entry-id="{{ $entry->id }}" data-social-id="{{ $social?->id }}">
                                        {{ $filesExist ? 'Manage Files' : 'Upload File' }}
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @endforeach

                </x-admin.data-table>

            </div>
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
        <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-upload"></i> Media Manager</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        {{-- Tabs --}}
                        <ul class="nav nav-tabs" id="uploadTab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link d-none " id="upload-tab" data-bs-toggle="tab"
                                    data-bs-target="#upload" type="button">Upload</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link active" id="view-tab" data-bs-toggle="tab"
                                    data-bs-target="#view" type="button">View</button>
                            </li>
                        </ul>

                        {{-- Tab content --}}
                        <div class="tab-content mt-3">
                            {{-- Upload Tab --}}
                            <div class="tab-pane fade " id="upload">
                                <form id="upload-form">
                                    <input type="hidden" name="entry_id" id="modal-entry-id">
                                    <input type="hidden" name="social_id" id="modal-social-id">

                                    <table class="table table-bordered d-none" id="upload-table">
                                        <thead>
                                            <tr>
                                                <th>File Name</th>
                                                <th>Size</th>
                                                <th>Type</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>

                                    <input type="file" name="media_files[]" multiple class="form-control mb-3"
                                        id="file-input">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="fas fa-cloud-upload-alt"></i> Upload</button>
                                </form>
                            </div>

                            {{-- View Tab --}}
                            <div class="tab-pane fade show active" id="view">
                                <table class="table table-bordered" id="view-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>File Name</th>
                                            <th>Type</th>
                                            <th>Preview</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view-table-body">
                                        <tr>
                                            <td colspan="4" class="text-center">No files uploaded yet.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


    </div>

    {{-- JS Scripts --}}
    <x-upload-js-2 :subProjectId="$subProject->id" :complianceId="$compliance->id" :phaseId="$phase_id" />
</x-app-layout>
