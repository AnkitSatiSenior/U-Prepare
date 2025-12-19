<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-project-diagram text-primary" title="Create Procurement Work Programs"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i> Dashboard'],
                ['label' => 'Procurement Work Programs'],
            ]" />
        <div class="row mb-3">

            <div class="col-md-8">

                <x-admin.package-card :packageProject="$selectedPackageProject" />

            </div>

            <div class="col-md-4">
                <x-admin.approval-details :packageProject="$selectedPackageProject" />
            </div>
            <div class="col-md-4">
                @if (!empty($procurementDetailsForm))
                    <x-admin.procurement-details :procurementDetail="$procurementDetailsForm" />
                @else
                    <div class="alert alert-warning d-flex justify-content-between align-items-center">
                        <span>First, you need to complete Procurement Details for this project.</span>
                        <a href="{{ route('admin.procurement-details.create', ['packageProject' => $selectedPackageProjectId]) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Create Procurement Details
                        </a>
                    </div>
                @endif
            </div>


      

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
   
            <div class="col-md-8 mt-3">
                <div class="card shadow-sm">
                    <!-- Card Header -->
                    <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white h2">
                            <i class="fas fa-plus-circle me-2"></i> Work Program Details
                        </h5>
                        <button type="button" class="btn btn-success btn-sm" id="addRow">
                            <i class="fas fa-plus-circle me-1"></i> Add Row
                        </button>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('admin.procurement-work-programs.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Shared Start Date -->
                        <div class="row m-4">
                            <label for="sharedStartDate" class="col-sm-2 col-form-label fw-semibold">
                                Shared Start Date
                            </label>
                            <div class="col-sm-4">
                                <input type="date" name="start_date[]" id="sharedStartDate" class="form-control"
                                    value="{{ old('start_date.0') }}" required>
                            </div>
                        </div>

                        <!-- Hidden Package Project ID -->
                        <input type="hidden" name="package_project_id[]"
                            value="{{ old('package_project_id.0', $selectedPackageProjectId) }}">

                        <!-- File Uploads -->
                        <div class="m-4">
                            <label class="form-label">Pre Bid Document</label>
                            <input type="file" name="pre_bid_minutes_document[]" class="form-control"
                                accept=".pdf,.doc,.docx,.jpg,.png">
                        </div>

                        <div class="m-4">
                            <label class="form-label">Bid Document</label>
                            <input type="file" name="procurement_bid_document[]" class="form-control"
                                accept=".pdf,.doc,.docx,.jpg,.png">
                        </div>

                        <!-- Procurement Details -->
                        @if (!empty($procurementDetails))
                            <input type="hidden" name="procurement_details_id[]" value="{{ $procurementDetails->id }}">
                        @endif

                        <!-- Work Program Table -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Name of Work Program</th>
                                            <th>Weightage (%)</th>
                                            <th>Days</th>
                                            <th class="d-none">Start Date</th>
                                            <th>Planned Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="workProgramTable">
                                        @php
                                            $defaultPrograms = [
                                                'Preparation and Approval of Bid Document and Estimate',
                                                'Publication of Bids',
                                                'Opening of Technical Bids',
                                                'Technical Evaluation',
                                                'Notification of Award',
                                                'Contract',
                                            ];
                                            $oldPrograms = old('name_work_program', $defaultPrograms);
                                        @endphp

                                        @foreach ($oldPrograms as $i => $program)
                                            <tr>
                                                <td>
                                                    <input type="text" name="name_work_program[]"
                                                        class="form-control" value="{{ $program }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="weightage[]" step="0.01"
                                                        class="form-control" value="{{ old('weightage.' . $i) }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="days[]" class="form-control days-input"
                                                        value="{{ old('days.' . $i) }}">
                                                </td>
                                                <td class="d-none">
                                                    <input type="date" name="start_date[]" class="form-control"
                                                        value="{{ old('start_date.' . $i) }}">
                                                </td>
                                                <td>
                                                    <input type="date" name="planned_date[]"
                                                        class="form-control planned-date-input"
                                                        value="{{ old('planned_date.' . $i) }}">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm removeRow">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Submit -->
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save All
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const addRowBtn = document.getElementById('addRow');
                const tableBody = document.getElementById('workProgramTable');
                const sharedStartDateInput = document.getElementById('sharedStartDate');

                function recalculateAllDates() {
                    let currentStartDate = sharedStartDateInput.value;
                    Array.from(tableBody.querySelectorAll('tr')).forEach(row => {
                        const startDateInput = row.querySelector('input[name="start_date[]"]');
                        const daysInput = row.querySelector('.days-input');
                        const plannedDateInput = row.querySelector('.planned-date-input');
                        const days = parseInt(daysInput.value);

                        startDateInput.value = currentStartDate || '';

                        if (!isNaN(days) && currentStartDate) {
                            const base = new Date(currentStartDate);
                            base.setDate(base.getDate() + days);
                            const plannedDate = base.toISOString().slice(0, 10);
                            plannedDateInput.value = plannedDate;
                            currentStartDate = plannedDate;
                        } else {
                            plannedDateInput.value = '';
                            currentStartDate = '';
                        }
                    });
                }

                addRowBtn.addEventListener('click', () => {
                    const firstRow = tableBody.querySelector('tr');
                    const newRow = firstRow.cloneNode(true);
                    newRow.querySelectorAll('input').forEach(input => input.value = '');
                    tableBody.appendChild(newRow);
                    recalculateAllDates();
                });

                tableBody.addEventListener('click', e => {
                    if (e.target.closest('.removeRow')) {
                        const rows = tableBody.querySelectorAll('tr');
                        if (rows.length > 1) {
                            e.target.closest('tr').remove();
                            recalculateAllDates();
                        } else alert('At least one row is required.');
                    }
                });

                tableBody.addEventListener('input', e => {
                    if (e.target.classList.contains('days-input')) recalculateAllDates();
                });

                sharedStartDateInput.addEventListener('input', recalculateAllDates);
                recalculateAllDates();
            });
        </script>
</x-app-layout>
