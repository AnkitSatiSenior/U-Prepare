<x-app-layout>
    <div class="container py-5">
        <h2 class="mb-4 text-primary fw-bold">
            <i class="fas fa-project-diagram me-2"></i> EPC Entry Data Management
        </h2>

        {{-- Success and Error Alerts --}}
        @foreach (['success' => 'success', 'error' => 'danger'] as $type => $class)
            @if (session($type))
                <div class="alert alert-{{ $class }} alert-dismissible fade show shadow-sm" role="alert">
                    {{ session($type) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Selected Project --}}
        @if ($subProject)
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-clipboard-list me-2"></i> Selected Project
                </div>
                <div class="card-body">
                    <input type="text" class="form-control bg-light" value="{{ $subProject->name }}" readonly>
                </div>
            </div>
        @endif

        {{-- Filter Form --}}
        @if ($epcEntries->isEmpty())
            <form method="GET" action="{{ route('admin.epcentry_data.index') }}" class="row g-3 mb-4 align-items-end">
                <div class="col-md-4">
                    <label for="work_service_id" class="form-label fw-bold">Work Service</label>
                    <select name="work_service_id" id="work_service_id" class="form-control shadow-sm" onchange="this.form.submit()">
                        <option value="">-- Select Work Service --</option>
                        @foreach($workservices as $ws)
                            <option value="{{ $ws->id }}" {{ $selectedWorkServiceId == $ws->id ? 'selected' : '' }}>
                                {{ $ws->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-none">
                    <label for="sub_package_project_id" class="form-label fw-bold">Sub Project</label>
                    <select name="sub_package_project_id" id="sub_package_project_id" class="form-control shadow-sm" onchange="this.form.submit()">
                        <option value="">-- Select Sub Project --</option>
                        @foreach($subProjects as $sp)
                            <option value="{{ $sp->id }}" {{ $selectedProjectId == $sp->id ? 'selected' : '' }}>
                                {{ $sp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                        <i class="fas fa-filter me-1"></i> Apply Filter
                    </button>
                </div>
            </form>
        @endif

        {{-- Store All Button --}}
        @if ($epcEntries->isEmpty() && $epcentrydefine->isNotEmpty() && $selectedProjectId)
            <form method="POST" action="{{ route('admin.epcentry_data.storeFromDefined') }}" class="mb-4">
                @csrf
                <input type="hidden" name="sub_package_project_id" value="{{ $selectedProjectId }}">
                <input type="hidden" name="work_service_id" value="{{ $selectedWorkServiceId }}">
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-save me-1"></i> Store All Entries
                </button>
            </form>
        @endif

        {{-- Warnings --}}
        @if (!empty($warnings))
            <div class="alert alert-warning shadow-sm" role="alert">
                <ul class="mb-0">
                    @foreach ($warnings as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Actions --}}
        @if ($selectedProjectId)
            <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                <a href="{{ route('admin.epcentry_data.create', ['sub_package_project_id' => $selectedProjectId]) }}" class="btn btn-success shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add New Entry
                </a>
                <button type="button" class="btn btn-danger shadow-sm" id="bulkDeleteBtn" disabled>
                    <i class="fas fa-trash-alt me-1"></i> Delete Selected
                </button>
            </div>
        @endif

        {{-- Already Defined EPC Entries --}}
        @if ($epcentrydefine->isNotEmpty())
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-list-ul me-2"></i> Already Defined EPC Entries
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Sl No.</th>
                                <th>Work Service</th>
                                <th>Activity Name</th>
                                <th>Stage Name</th>
                                <th>Item Description</th>
                                <th>Percent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($epcentrydefine as $item)
                                <tr>
                                    <td>{{ $item->sl_no ?? '-' }}</td>
                                    <td>{{ $item->workService?->name ?? '-' }}</td>
                                    <td>{{ $item->activityName?->name ?? '-' }}</td>
                                    <td>{{ $item->stage_name ?? '-' }}</td>
                                    <td>{{ $item->item_description ?? '-' }}</td>
                                    <td>{{ $item->percent !== null ? $item->percent.'%' : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- EPC Entries Table --}}
        @if ($epcEntries->isNotEmpty())
            <form id="bulkDeleteForm">@csrf
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th><input type="checkbox" id="selectAllHeader"></th>
                                <th>Sl No.</th>
                                <th>Activity Name</th>
                                <th>Stage Name</th>
                                <th>Item Description</th>
                                <th>Percent</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($epcEntries as $groupItems)
                                @foreach ($groupItems as $entry)
                                    <tr>
                                        <td><input type="checkbox" class="entryCheckbox" name="ids[]" value="{{ $entry->id }}"></td>
                                        <td>{{ $entry->sl_no }}</td>
                                        <td>{{ $entry->activity_name }}</td>
                                        <td>{{ $entry->stage_name ?? '-' }}</td>
                                        <td>{{ $entry->item_description ?? '-' }}</td>
                                        <td>{{ $entry->percent !== null ? $entry->percent.'%' : '-' }}</td>
                                        <td>{{ number_format($entry->amount, 2) }}</td>
                                        <td class="d-flex gap-2">
                                            <a href="{{ route('admin.epcentry_data.edit', $entry->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.epcentry_data.destroy', $entry->id) }}" onsubmit="return confirm('Delete this entry?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @else
            <div class="alert alert-info shadow-sm">No EPC entries found for this project.</div>
        @endif
    </div>

    {{-- Bulk Delete Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const checkboxes = document.querySelectorAll('.entryCheckbox');
            const selectAll = document.getElementById('selectAllHeader');

            function updateBtnState() {
                const anyChecked = Array.from(checkboxes).some(chk => chk.checked);
                bulkDeleteBtn.disabled = !anyChecked;
            }

            selectAll.addEventListener('change', function () {
                checkboxes.forEach(chk => chk.checked = selectAll.checked);
                updateBtnState();
            });

            checkboxes.forEach(chk => {
                chk.addEventListener('change', function () {
                    selectAll.checked = Array.from(checkboxes).every(chk => chk.checked);
                    updateBtnState();
                });
            });

            bulkDeleteBtn.addEventListener('click', function () {
                const ids = Array.from(checkboxes)
                    .filter(chk => chk.checked)
                    .map(chk => chk.value);

                if (!ids.length || !confirm('Delete selected entries?')) return;

                fetch("{{ route('admin.epcentry_data.bulkDestroy') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ ids, _method: 'DELETE' }),
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Failed to delete selected entries.');
                    }
                }).catch(() => alert('Failed to delete selected entries.'));
            });
        });
    </script>
</x-app-layout>
