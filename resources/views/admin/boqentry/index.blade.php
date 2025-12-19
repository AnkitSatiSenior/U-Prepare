<x-app-layout>
    <div class="container py-5">
        <h2 class="mb-4 text-primary fw-bold">BOQ Entries Management</h2>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Project Selection Card --}}
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0">Selected Project</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <label class="form-label">Project Name:</label>
                        <input type="text" class="form-control"
                            value="{{ optional($subProject)->name ?? 'No project selected' }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        {{-- Excel Upload Card --}}
        {{-- Excel Upload Card --}}
        @if ($selectedProjectId && $boqEntries->isEmpty())
            <div class="card shadow mb-5">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0">Import BOQ from Excel</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.boqentry.upload') }}" enctype="multipart/form-data"
                        class="mb-0">
                        @csrf
                        <input type="hidden" name="sub_package_project_id" value="{{ $selectedProjectId }}">

                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <label for="excel_file" class="form-label">Excel File:</label>
                                <input type="file" name="excel_file" id="excel_file" class="form-control"
                                    accept=".xlsx,.xls,.csv" required>
                                <div class="form-text">Upload Excel file with BOQ data</div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-file-import me-1"></i> Import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif


        @if ($subProject)
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary">
                    <i class="fas fa-list-alt me-2"></i> BOQ for: <strong>{{ $subProject->name }}</strong>
                </h3>

                @if ($selectedProjectId)
                    <a href="{{ route('admin.boqentry.create', ['sub_package_project_id' => $selectedProjectId]) }}"
                        class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> Add New BOQ Entry
                    </a>
                @endif
            </div>

            @if ($boqEntries->isNotEmpty())
                <form id="bulkDeleteForm">
                    @csrf
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                            <i class="fas fa-trash-alt me-1"></i> Delete Selected
                        </button>
                        <div>
                            <label>
                                <input type="checkbox" id="selectAllCheckbox"> Select All
                            </label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped ">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 3%;"><input type="checkbox" id="selectAllHeader"></th>
                                    <th width="10%">Sl. No.</th>
                                    <th>Item Description</th>
                                    <th width="8%">Unit</th>
                                    <th width="8%">Qty.</th>
                                    <th width="10%">Rate (₹)</th>
                                    <th width="12%">Amount (₹)</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($boqEntries as $parentSlNo => $entries)
                                    @php
                                        $parentEntry = $entries->firstWhere('sl_no', $parentSlNo);
                                    @endphp
                                    <tr class="table-primary fw-bold">
                                        <td><input type="checkbox" class="entryCheckbox" name="ids[]"
                                                value="{{ $parentEntry->id }}"></td>
                                        <td>{{ $parentSlNo }}</td>
                                        <td>{{ $parentEntry->item_description }}</td>
                                        <td>{{ $parentEntry->unit }}</td>
                                        <td>{{ $parentEntry->qty }}</td>
                                        <td class="text-end">{{ formatPrice($parentEntry->rate) }}</td>
                                        <td class="text-end">{{ formatPrice($parentEntry->amount) }}</td>
                                        <td style="vertical-align: middle;">
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('admin.boqentry.edit', $parentEntry->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.boqentry.destroy', $parentEntry->id) }}"
                                                    class="d-inline-block"
                                                    onsubmit="return confirm('Delete this entry and all its sub-entries?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" type="submit">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach ($entries as $entry)
                                        @if ($entry->sl_no != $parentSlNo)
                                            <tr>
                                                <td><input type="checkbox" class="entryCheckbox" name="ids[]"
                                                        value="{{ $entry->id }}"></td>
                                                <td class="ps-4">{{ $entry->sl_no }}</td>
                                                <td>{{ $entry->item_description }}</td>
                                                <td>{{ $entry->unit }}</td>
                                                <td>{{ $entry->qty }}</td>
                                                <td class="text-end">{{ formatPrice($entry->rate) }}</td>
                                                <td class="text-end">{{ formatPrice($entry->amount) }}</td>
                                                <td style="vertical-align: middle;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ route('admin.boqentry.edit', $entry->id) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <form method="POST"
                                                            action="{{ route('admin.boqentry.destroy', $entry->id) }}"
                                                            class="d-inline-block"
                                                            onsubmit="return confirm('Delete this entry and all its sub-entries?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger" type="submit">
                                                                <i class="fas fa-trash-alt"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>


                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                        const selectAllHeader = document.getElementById('selectAllHeader');
                        const checkboxes = document.querySelectorAll('.entryCheckbox');
                        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

                        function updateBulkDeleteButton() {
                            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                            bulkDeleteBtn.disabled = !anyChecked;
                        }

                        // Set all checkboxes checked/unchecked and update bulk button
                        function setAllCheckboxes(checked) {
                            checkboxes.forEach(cb => cb.checked = checked);
                            bulkDeleteBtn.disabled = !checked;
                            selectAllCheckbox.checked = checked;
                            selectAllHeader.checked = checked;
                        }

                        selectAllCheckbox.addEventListener('change', function() {
                            setAllCheckboxes(this.checked);
                        });

                        selectAllHeader.addEventListener('change', function() {
                            setAllCheckboxes(this.checked);
                        });

                        checkboxes.forEach(cb => {
                            cb.addEventListener('change', function() {
                                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                                selectAllCheckbox.checked = allChecked;
                                selectAllHeader.checked = allChecked;
                                updateBulkDeleteButton();
                            });
                        });

                        updateBulkDeleteButton();
                    });
                </script>
            @endif

        @endif
    </div>

    <script>
        $(document).ready(function() {
            const bulkDeleteBtn = $('#bulkDeleteBtn');
            const checkboxes = $('.entryCheckbox');
            const selectAllCheckbox = $('#selectAllCheckbox');
            const selectAllHeader = $('#selectAllHeader');

            function updateBulkDeleteButton() {
                bulkDeleteBtn.prop('disabled', $('.entryCheckbox:checked').length === 0);
            }

            function setAllCheckboxes(checked) {
                checkboxes.prop('checked', checked);
                bulkDeleteBtn.prop('disabled', !checked);
                selectAllCheckbox.prop('checked', checked);
                selectAllHeader.prop('checked', checked);
            }

            selectAllCheckbox.on('change', function() {
                setAllCheckboxes(this.checked);
            });

            selectAllHeader.on('change', function() {
                setAllCheckboxes(this.checked);
            });

            checkboxes.on('change', function() {
                const allChecked = checkboxes.length === $('.entryCheckbox:checked').length;
                selectAllCheckbox.prop('checked', allChecked);
                selectAllHeader.prop('checked', allChecked);
                updateBulkDeleteButton();
            });

            updateBulkDeleteButton();

            bulkDeleteBtn.on('click', function() {
                const selectedIds = $('.entryCheckbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    alert('Please select entries to delete.');
                    return;
                }

                if (!confirm('Are you sure you want to delete the selected entries?')) {
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.boqentry.bulk-delete') }}",
                    type: 'POST',
                    data: {
                        ids: selectedIds,
                        _method: 'DELETE',
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        alert(response.message || 'Selected entries deleted successfully.');
                        // Reload page or remove rows from DOM:
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error deleting entries: ' + (xhr.responseJSON?.message || xhr
                            .statusText));
                    }
                });
            });
        });
    </script>



    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-primary td {
            background-color: #e7f5ff;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .table-striped tbody tr.table-primary:nth-of-type(odd) {
            background-color: #e7f5ff;
        }

        .card-header {
            border-radius: 0.375rem 0.375rem 0 0 !important;
        }
    </style>

</x-app-layout>
