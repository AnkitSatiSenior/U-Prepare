{{-- resources/views/admin/physical_epc_progress/create.blade.php --}}
<x-app-layout>
    <div class="container py-5">
        <h2 class="mb-4 fw-bold text-primary">
            <i class="fas fa-plus-circle me-2"></i> Add Physical EPC Progress
        </h2>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the errors below:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.physical_epc_progress.store') }}" method="POST" enctype="multipart/form-data" id="progressForm">
            @csrf

            {{-- Hidden sub_package_project_id --}}
            <input type="hidden" name="sub_package_project_id" value="{{ request('sub_package_project_id') }}">

            {{-- EPC Activity Dropdown --}}
            <div class="mb-3">
                <label for="epcentry_data_id" class="form-label">Activity Name & Stage Name <span class="text-danger">*</span></label>
                <select name="epcentry_data_id" id="epcentry_data_id" class="form-control" required>
                    <option value="">-- Select Activity --</option>
                    @foreach ($epcEntries as $entry)
                        <option value="{{ $entry->id }}" data-percent="{{ $entry->percent }}" data-amount="{{ $entry->amount ?? 0 }}">
                            {{ $entry->activity_name }} - {{ $entry->stage_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Percent --}}
            <div class="mb-3">
                <label for="percent" class="form-label">Percent (%)</label>
                <input type="number" step="0.01" name="percent" id="percent" class="form-control" placeholder="Enter percent" value="{{ old('percent') }}" required>
                <small id="percentHelp" class="text-muted"></small>
            </div>

            {{-- Amount --}}
            {{-- <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" placeholder="Calculated amount" value="{{ old('amount') }}" readonly>
            </div> --}}

            {{-- Items --}}
            <div class="mb-3">
                <label for="items" class="form-label">Items</label>
                <textarea name="items" id="items" rows="3" class="form-control" placeholder="Enter items description" required>{{ old('items') }}</textarea>
            </div>

            {{-- Progress Submitted Date --}}
            <div class="mb-3">
                <label for="progress_submitted_date" class="form-label">Progress Submitted Date</label>
                <input type="date" name="progress_submitted_date" id="progress_submitted_date" class="form-control" value="{{ old('progress_submitted_date') }}" required>
            </div>

            {{-- Images --}}
            <div class="mb-3">
                <label for="images" class="form-label">Upload Images</label>
                <input type="file" name="images[]" id="images" class="form-control" multiple>
                <small class="text-muted">You can upload multiple images (JPG, JPEG, PNG, max 2MB each).</small>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i> Save Progress
            </button>
            <a href="{{ route('admin.physical_epc_progress.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>

    <script>
        const epcEntries = @json($epcEntries);
        const progressSums = @json($progressSums);
        let selectedEntryId = null;

        function updatePercentAndAmountLimits() {
            const select = document.getElementById('epcentry_data_id');
            selectedEntryId = select.value;
            if (!selectedEntryId) {
                document.getElementById('percentHelp').textContent = '';
                document.getElementById('percent').max = 100;
                document.getElementById('percent').value = '';
                document.getElementById('amount').value = '';
                return;
            }

            const epcEntry = epcEntries.find(e => e.id == selectedEntryId);
            const totalPercentAllowed = epcEntry ? parseFloat(epcEntry.percent) : 100;
            const totalAmountAllowed = parseFloat(epcEntry ? epcEntry.amount ?? 0 : 0);

            const currentSum = parseFloat(progressSums[selectedEntryId] ?? 0);

            const remainingPercent = Math.max(totalPercentAllowed - currentSum, 0);

            document.getElementById('percentHelp').textContent = `Remaining percent allowed: ${remainingPercent.toFixed(2)}%`;
            document.getElementById('percent').max = remainingPercent;

            // Clamp percent input to remainingPercent
            let currentPercent = parseFloat(document.getElementById('percent').value) || 0;
            if (currentPercent > remainingPercent) {
                document.getElementById('percent').value = remainingPercent.toFixed(2);
                currentPercent = remainingPercent;
            }

            const calculatedAmount = (currentPercent / 100) * totalAmountAllowed;
            document.getElementById('amount').value = calculatedAmount.toFixed(2);
        }

        document.getElementById('epcentry_data_id').addEventListener('change', updatePercentAndAmountLimits);
        document.getElementById('percent').addEventListener('input', () => {
            if (!selectedEntryId) return;

            const epcEntry = epcEntries.find(e => e.id == selectedEntryId);
            const totalAmountAllowed = parseFloat(epcEntry ? epcEntry.amount ?? 0 : 0);

            let percentVal = parseFloat(document.getElementById('percent').value) || 0;
            let maxPercent = parseFloat(document.getElementById('percent').max) || 100;

            if (percentVal > maxPercent) {
                percentVal = maxPercent;
                document.getElementById('percent').value = maxPercent;
            }
            const calculatedAmount = (percentVal / 100) * totalAmountAllowed;
            document.getElementById('amount').value = calculatedAmount.toFixed(2);
        });

        // Initialize on page load
        updatePercentAndAmountLimits();
    </script>
</x-app-layout>
