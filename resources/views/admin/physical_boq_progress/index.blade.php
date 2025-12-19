<x-app-layout>
    <div class="container py-5">
        <h2 class="mb-4 text-primary fw-bold">Physical Progress Update</h2>

        <div id="flash-messages"></div>

        {{-- Project Info --}}
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Selected Project</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Project Name:</label>
                        <input type="text" class="form-control"
                            value="{{ optional($subProject)->name ?? 'No project selected' }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Select Date:</label>
                        <input type="date" class="form-control" id="progress_date" value="{{ $selectedDate }}">
                    </div>
                </div>
            </div>
        </div>

        @if ($subProject && $boqEntries->isNotEmpty())
            <form id="physical-progress-form">
                @csrf
                <input type="hidden" name="sub_package_project_id" value="{{ $subProject->id }}">
                <input type="hidden" name="progress_date" value="{{ $selectedDate }}">

                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th rowspan="2">S.No.</th>
                                <th rowspan="2">Item</th>
                                <th rowspan="2">Unit</th>
                                <th rowspan="2">BOQ Qty</th>
                                <th rowspan="2">Rate (₹)</th>
                                <th rowspan="2">BOQ Amount (₹)</th>
                                <th colspan="2">Since Previous</th>
                                <th colspan="2">Current Day
                                    ({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }})</th>
                                <th colspan="2">Up to Date</th>
                            </tr>
                            <tr>
                                <th>Qty</th>
                                <th>Amount (₹)</th>
                                <th>Qty</th>
                                <th>Amount (₹)</th>
                                <th>Qty</th>
                                <th>Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($boqEntries as $parentSlNo => $entries)
                                @php $parentEntry = $entries->firstWhere('sl_no', $parentSlNo); @endphp
                                @if ($parentEntry->qty > 0)
                                    <tr class="table-primary fw-bold" data-entry-id="{{ $parentEntry->id }}">
                                        <td>{{ $parentSlNo }}</td>
                                        <td class="text-start">{{ $parentEntry->item_description }}</td>
                                        <td>{{ $parentEntry->unit }}</td>
                                        <td class="text-end boq-qty">{{ $parentEntry->qty }}</td>
                                        <td class="text-end">{{ formatPrice($parentEntry->rate) }}</td>
                                        <td class="text-end">{{ formatPrice($parentEntry->amount) }}</td>

                                        <td class="prev-qty">
                                            {{ $physicalProgress[$parentEntry->id]->since_previous->qty }}</td>
                                        <td class="prev-amt">
                                            {{ $physicalProgress[$parentEntry->id]->since_previous->amount }}</td>

                                        <td>
                                            <input type="hidden" name="entries[{{ $parentEntry->id }}][boq_entry_id]"
                                                value="{{ $parentEntry->id }}">
                                            <input type="number" step="0.001" min="0"
                                                name="entries[{{ $parentEntry->id }}][current_day][qty]"
                                                class="form-control qty-input" data-rate="{{ $parentEntry->rate }}"
                                                data-entry-id="{{ $parentEntry->id }}"
                                                data-upto="{{ $physicalProgress[$parentEntry->id]->up_to_date->qty }}"
                                                value="{{ $physicalProgress[$parentEntry->id]->current_day->qty }}">
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="entries[{{ $parentEntry->id }}][current_day][amount]"
                                                class="form-control amount-input"
                                                value="{{ $physicalProgress[$parentEntry->id]->current_day->amount }}"
                                                readonly>
                                        </td>

                                        <td class="upto-qty">{{ $physicalProgress[$parentEntry->id]->up_to_date->qty }}
                                        </td>
                                        <td class="upto-amt">
                                            {{ $physicalProgress[$parentEntry->id]->up_to_date->amount }}</td>
                                    </tr>

                                    {{-- Child Entries --}}
                                    @foreach ($entries as $entry)
                                        @if ($entry->sl_no != $parentSlNo && $entry->qty > 0)
                                            <tr data-entry-id="{{ $entry->id }}">
                                                <td class="ps-4">{{ $entry->sl_no }}</td>
                                                <td class="text-start">{{ $entry->item_description }}</td>
                                                <td>{{ $entry->unit }}</td>
                                                <td class="text-end boq-qty">{{ $entry->qty }}</td>
                                                <td class="text-end">{{ formatPrice($entry->rate) }}</td>
                                                <td class="text-end">{{ formatPrice($entry->amount) }}</td>

                                                <td class="prev-qty">
                                                    {{ $physicalProgress[$entry->id]->since_previous->qty }}</td>
                                                <td class="prev-amt">
                                                    {{ $physicalProgress[$entry->id]->since_previous->amount }}</td>

                                                <td>
                                                    <input type="hidden"
                                                        name="entries[{{ $entry->id }}][boq_entry_id]"
                                                        value="{{ $entry->id }}">
                                                    <input type="number" step="0.001" min="0"
                                                        name="entries[{{ $entry->id }}][current_day][qty]"
                                                        class="form-control qty-input" data-rate="{{ $entry->rate }}"
                                                        data-entry-id="{{ $entry->id }}"
                                                        data-upto="{{ $physicalProgress[$entry->id]->up_to_date->qty }}"
                                                        value="{{ $physicalProgress[$entry->id]->current_day->qty }}">
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="entries[{{ $entry->id }}][current_day][amount]"
                                                        class="form-control amount-input"
                                                        value="{{ $physicalProgress[$entry->id]->current_day->amount }}"
                                                        readonly>
                                                </td>

                                                <td class="upto-qty">
                                                    {{ $physicalProgress[$entry->id]->up_to_date->qty }}</td>
                                                <td class="upto-amt">
                                                    {{ $physicalProgress[$entry->id]->up_to_date->amount }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>

                        <tfoot class="table-secondary fw-bold">
                            @php
                                $allEntries = collect($boqEntries)->flatten();

                                // Amounts
                                $totalBoqAmt = $allEntries->sum(fn($e) => (float) ($e->amount ?? 0));
                                $totalPrevAmt = $allEntries->sum(
                                    fn($e) => (float) ($physicalProgress[$e->id]->since_previous->amount ?? 0),
                                );
                                $totalCurrentAmt = $allEntries->sum(
                                    fn($e) => (float) ($physicalProgress[$e->id]->current_day->amount ?? 0),
                                );
                                $totalUptoAmt = $allEntries->sum(
                                    fn($e) => (float) ($physicalProgress[$e->id]->up_to_date->amount ?? 0),
                                );

                                // GST Calculations (18%)
                                $boqGst = round($totalBoqAmt * 0.18, 2);
                                $boqGrandTotal = round($totalBoqAmt + $boqGst, 2);

                                $uptoGst = round($totalUptoAmt * 0.18, 2);
                                $uptoGrandTotal = round($totalUptoAmt + $uptoGst, 2);
                            @endphp

                            {{-- BOQ Total --}}


                            {{-- Progress Totals --}}
                            <tr>
                                <td colspan="5" class="text-end">Total  Amount (₹)</td>
                                <td colspan="1" class="text-end text-black">
                                Total BOQ Amount : {{ formatPrice($totalBoqAmt) }}
                                </td>
                                <td colspan="2" class="text-end text-black">
                                    Since Previous: {{ formatPrice($totalPrevAmt) }}
                                </td>
                                <td colspan="2" class="text-end text-black">
                                    Current Day: {{ formatPrice($totalCurrentAmt) }}
                                </td>
                                <td colspan="2" class="text-end text-black">
                                    Up to Date: {{ formatPrice($totalUptoAmt) }}
                                </td>
                            </tr>

                            {{-- GST --}}
                            <tr>
                                <td colspan="5" class="text-end">Add 18% GST (₹)</td>
                                <td colspan="1"> {{ formatPrice($boqGst) }}</td>
                                <td colspan="6" class="text-end text-black">
                                    {{ formatPrice($uptoGst) }}
                                </td>
                            </tr>

                            {{-- Grand Total --}}
                            <tr class="table-dark">
                                <td colspan="5" class="text-end">Grand Total (Incl. GST) (₹)</td>
                                <td>{{ formatPrice($boqGrandTotal) }}</td>
                                <td colspan="7" class="text-end fw-bold">
                                    {{ formatPrice($uptoGrandTotal) }}
                                </td>
                            </tr>
                        </tfoot>



                    </table>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Progress
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- Styles --}}
    <style>
        .table-primary td {
            background-color: #e0f7fa;
        }

        .table-secondary th {
            background-color: #b2ebf2;
        }

        .ps-4 {
            padding-left: 2rem !important;
        }
    </style>

    {{-- Scripts --}}
    <script>
        // Redirect on date change
        document.getElementById('progress_date').addEventListener('change', function() {
            const selectedDate = this.value;
            const projectId = "{{ $subProject->id }}";
            window.location.href = "{{ route('admin.physical_boq_progress.index') }}?sub_package_project_id=" +
                projectId + "&date=" + selectedDate;
        });

        // Auto-calculate Amount & Validate BOQ qty
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input')) {
                const row = e.target.closest('tr');
                const boqQty = parseFloat(row.querySelector('.boq-qty').textContent) || 0;
                const uptoQty = parseFloat(e.target.dataset.upto) || 0;
                const prevQty = parseFloat(row.querySelector('.prev-qty').textContent) || 0;
                const enteredQty = parseFloat(e.target.value) || 0;

                const totalQty = uptoQty + enteredQty; // New total progress

                if (totalQty > boqQty) {
                    alert("Entered quantity exceeds BOQ Qty (" + boqQty + ")");
                    e.target.value = 0;
                    row.querySelector('.amount-input').value = 0;
                    return;
                }

                const rate = parseFloat(e.target.dataset.rate) || 0;
                const amountInput = row.querySelector('.amount-input');
                if (amountInput) {
                    amountInput.value = (enteredQty * rate).toFixed(2);
                }

                row.dataset.changed = 'true';
            }
        });

        // AJAX submit for changed rows
        document.getElementById('physical-progress-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const changedRows = Array.from(form.querySelectorAll('tr[data-changed="true"]'));
            if (!changedRows.length) {
                alert('No changes to save.');
                return;
            }

            const formData = new FormData();
            formData.append('sub_package_project_id', form.querySelector('[name="sub_package_project_id"]').value);
            formData.append('progress_date', form.querySelector('[name="progress_date"]').value);

            changedRows.forEach(row => {
                const entryId = row.querySelector('.qty-input').dataset.entryId;
                const qty = row.querySelector('.qty-input').value;
                const amount = row.querySelector('.amount-input').value;

                formData.append(`entries[${entryId}][boq_entry_id]`, entryId);
                formData.append(`entries[${entryId}][current_day][qty]`, qty);
                formData.append(`entries[${entryId}][current_day][amount]`, amount);
            });

            fetch("{{ route('admin.boqentry.save-physical-progress') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    const toastContainer = document.querySelector('.toast-container');

                    // Create toast element
                    const toastEl = document.createElement('div');
                    toastEl.className =
                        `toast align-items-center text-white bg-${data.status === 'success' ? 'success' : 'danger'} border-0`;
                    toastEl.role = 'alert';
                    toastEl.ariaLive = 'assertive';
                    toastEl.ariaAtomic = 'true';
                    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${data.message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

                    toastContainer.appendChild(toastEl);

                    // Show Bootstrap toast
                    const bsToast = new bootstrap.Toast(toastEl, {
                        delay: 3000
                    }); // auto hide in 3 sec
                    bsToast.show();

                    // Remove from DOM after hidden
                    toastEl.addEventListener('hidden.bs.toast', () => {
                        toastEl.remove();
                    });

                    // Clear changed rows only if success
                    if (data.status === 'success') {
                        changedRows.forEach(row => row.dataset.changed = '');
                    }
                })

                .catch(console.error);
        });
    </script>
</x-app-layout>
