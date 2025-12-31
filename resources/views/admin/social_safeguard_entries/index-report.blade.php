<x-app-layout>
    <div class="container py-5">

        <h2 class="mb-4 text-primary fw-bold">
            {{ $subProject->name }} — {{ $compliance->name }} Safeguard Entries
        </h2>

        @php
            $selectedPhase = $compliance->contractionPhases->firstWhere('id', $phase_id);
            $selectedDateFormatted = \Carbon\Carbon::parse($selectedDate)->format('d M, Y');
        @endphp

        {{-- Flash messages --}}
        @if (session()->has('message'))
            <div class="alert alert-{{ session('status') }} alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter Form --}}
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
                const phaseId = document.getElementById('phase-id').value || 0;
                const dateOfEntry = document.getElementById('date-of-entry').value;

                let urlTemplate =
                    "{{ route('admin.report.indexReport', ['project_id' => 'PROJECT_ID', 'compliance_id' => 'COMPLIANCE_ID', 'phase_id' => 'PHASE_ID']) }}";

                urlTemplate = urlTemplate
                    .replace('PROJECT_ID', projectId)
                    .replace('COMPLIANCE_ID', complianceId)
                    .replace('PHASE_ID', phaseId);

                urlTemplate += `?date_of_entry=${dateOfEntry}`;
                window.location.href = urlTemplate;
            });
        </script>

        {{-- Entries Table --}}
        @if ($entries->isNotEmpty())
            <div class="table-responsive mt-4">
                <x-admin.data-table 
                    id="report-view-table" 
                    :headers="['SL No', 'Item', 'Yes/No', 'Remarks', 'Validity', 'Date of Entry', 'Files']" 
                    :excel="true" 
                    :print="true" 
                    :pageLength="50" 
                    searchPlaceholder="Search items..."
                    title="{{ $subProject->name . ' — ' . $compliance->name . ' Safeguard Entries' . ($selectedPhase ? ' (Phase: ' . $selectedPhase->name . ')' : '') . ' — (Date: ' . $selectedDateFormatted . ')' }}">

                    @php
                        $allSlNos = $entries->pluck('sl_no')->toArray();
                    @endphp

                    @foreach ($entries as $entry)
                        @php
                            $isParent = collect($allSlNos)->contains(fn($sl) => Str::startsWith($sl, $entry->sl_no . '.'));
                            $level = substr_count($entry->sl_no, '.');
                            $social = $entry->social;
                            $filesExist = $social && !empty($social->photos_documents_case_studies);
                        @endphp

                        <tr class="{{ $isParent ? 'table-primary fw-bold' : '' }}">
                            {{-- SL No --}}
                            <td>{{ $entry->sl_no }}</td>

                            {{-- Item --}}
                            <td class="text-start" style="padding-left: {{ $level * 20 }}px;">
                                {{ $entry->definedSafeguard->item_description ?? $entry->item_description  ?? '-' }}
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
                            <td>{{ $isParent ? '-' : ($social->remarks ?? '-') }}</td>

                            {{-- Validity --}}
                            <td>
                                @if ($isParent)
                                    -
                                @elseif ($entry->is_validity && $social?->validity_date)
                                    {{ \Carbon\Carbon::parse($social->validity_date)->format('d M Y') }}
                                @elseif (!$entry->is_validity)
                                    N/A
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Date of Entry --}}
                            <td>{{ $isParent ? '-' : ($social?->date_of_entry ? \Carbon\Carbon::parse($social->date_of_entry)->format('d M Y') : '-') }}</td>

                            {{-- Files --}}
                            <td class="{{ $isParent ? 'bg-light' : ($filesExist ? 'bg-light-success' : 'bg-light-danger') }}">
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
                No entries found for the selected filters.
            </div>
        @endif

        {{-- Upload Modal --}}
        <x-upload-modal />

    </div>

    {{-- JS Scripts --}}
    <x-upload-js-2 :subProjectId="$subProject->id" :complianceId="$compliance->id" :phaseId="$phase_id" />
</x-app-layout>
