@php
    /** @var \App\Models\Project|null $project */
    $project = $project ?? null;
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold text-primary">
        <i class="fas fa-info-circle me-2"></i>1. Core Project Information
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Project Name *</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $project?->name) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Short Name *</label>
                <input type="text" name="project_short_name" class="form-control" required value="{{ old('project_short_name', $project?->project_short_name) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Funding Agency *</label>
                <select name="funding_agency_id" class="form-select" required>
                    <option value="">Select Agency</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}" @selected((string)old('funding_agency_id', $project?->funding_agency_id) === (string)$agency->id)>
                            {{ $agency->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Department Name *</label>
                <input type="text" name="department_name" class="form-control" required value="{{ old('department_name', $project?->department_name ?? 'U-PREPARE') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Loan Signing Date *</label>
                <input type="date" name="loan_signing_date" class="form-control" required value="{{ old('loan_signing_date', $project?->loan_signing_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Start Date *</label>
                <input type="date" name="start_date" class="form-control" required value="{{ old('start_date', $project?->start_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Scheduled Closure Date *</label>
                <input type="date" name="scheduled_closure_date" class="form-control" required value="{{ old('scheduled_closure_date', $project?->scheduled_closure_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Donor Currency (e.g. USD) *</label>
                <input type="text" name="donor_currency" class="form-control" placeholder="USD" required value="{{ old('donor_currency', $project?->donor_currency) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Financial Year *</label>
                <input type="text" name="financial_year" class="form-control" placeholder="2025-26" required value="{{ old('financial_year', $project?->financial_year) }}">
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-light fw-bold text-success">
        <i class="fas fa-money-bill-wave me-2"></i>2. Financial Outlays (Original)
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Project Cost (INR) *</label>
                <input type="number" step="0.01" name="budget" class="form-control" required value="{{ old('budget', $project?->budget) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Project Outlay (INR) *</label>
                <input type="number" step="0.01" name="outlay_inr" class="form-control" required value="{{ old('outlay_inr', $project?->outlay_inr) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Project Outlay (Donor) *</label>
                <input type="number" step="0.01" name="outlay_donor_currency" class="form-control" required value="{{ old('outlay_donor_currency', $project?->outlay_donor_currency) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Donor Share (INR) *</label>
                <input type="number" step="0.01" name="donor_share_inr" class="form-control" required value="{{ old('donor_share_inr', $project?->donor_share_inr) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Donor Share (Donor) *</label>
                <input type="number" step="0.01" name="donor_share_donor_currency" class="form-control" required value="{{ old('donor_share_donor_currency', $project?->donor_share_donor_currency) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">State Share (INR) *</label>
                <input type="number" step="0.01" name="state_share_inr" class="form-control" required value="{{ old('state_share_inr', $project?->state_share_inr) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">State Share (Donor) *</label>
                <input type="number" step="0.01" name="state_share_donor_currency" class="form-control" required value="{{ old('state_share_donor_currency', $project?->state_share_donor_currency) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Other Share (INR) *</label>
                <input type="number" step="0.01" name="other_share_inr" class="form-control" required value="{{ old('other_share_inr', $project?->other_share_inr) }}">
            </div>
        </div>
    </div>
</div>

@php
    $isRevisedValue = old('is_revised', $project?->is_revised ? '1' : '0');
@endphp
<div class="card shadow-sm mb-4 border-warning">
    <div class="card-header bg-warning-subtle fw-bold">
        <i class="fas fa-history me-2"></i>3. Revised Project Status
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Is the project revised? *</label>
                <select name="is_revised" class="form-select" id="revised_toggle" required>
                    <option value="0" @selected((string)$isRevisedValue === '0')>No</option>
                    <option value="1" @selected((string)$isRevisedValue === '1')>Yes</option>
                </select>
            </div>
        </div>
        <div id="revised_fields" style="display: {{ (string)$isRevisedValue === '1' ? 'block' : 'none' }};">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-danger fw-bold">Revised Closure Date</label>
                    <input type="date" name="revised_closure_date" class="form-control" value="{{ old('revised_closure_date', $project?->revised_closure_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-danger fw-bold">Revised Outlay (INR)</label>
                    <input type="number" step="0.01" name="revised_outlay_inr" class="form-control" value="{{ old('revised_outlay_inr', $project?->revised_outlay_inr) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-danger fw-bold">Revised Outlay (Donor)</label>
                    <input type="number" step="0.01" name="revised_outlay_donor_currency" class="form-control" value="{{ old('revised_outlay_donor_currency', $project?->revised_outlay_donor_currency) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-danger fw-bold">Revised Donor Share (INR)</label>
                    <input type="number" step="0.01" name="revised_donor_share_inr" class="form-control" value="{{ old('revised_donor_share_inr', $project?->revised_donor_share_inr) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-danger fw-bold">Revised Donor Share (Donor)</label>
                    <input type="number" step="0.01" name="revised_donor_share_donor_currency" class="form-control" value="{{ old('revised_donor_share_donor_currency', $project?->revised_donor_share_donor_currency) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-danger fw-bold">Revised State Share (INR)</label>
                    <input type="number" step="0.01" name="revised_state_share_inr" class="form-control" value="{{ old('revised_state_share_inr', $project?->revised_state_share_inr) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-danger fw-bold">Revised State Share (Donor)</label>
                    <input type="number" step="0.01" name="revised_state_share_donor_currency" class="form-control" value="{{ old('revised_state_share_donor_currency', $project?->revised_state_share_donor_currency) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-danger fw-bold">Revised Other Share (INR)</label>
                    <input type="number" step="0.01" name="revised_other_share_inr" class="form-control" value="{{ old('revised_other_share_inr', $project?->revised_other_share_inr) }}">
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $isDliValue = old('is_dli_based', $project?->is_dli_based ? '1' : '0');
@endphp
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light fw-bold">
        <i class="fas fa-bullseye me-2"></i>4. Implementation Type (DLI / Non-DLI)
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Is this a DLI based project? *</label>
                <select name="is_dli_based" class="form-select" id="dli_toggle" required>
                    <option value="0" @selected((string)$isDliValue === '0')>No</option>
                    <option value="1" @selected((string)$isDliValue === '1')>Yes</option>
                </select>
            </div>
        </div>

        <div id="dli_fields" class="mt-3" style="display: {{ (string)$isDliValue === '1' ? 'block' : 'none' }};">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">DLI Target Count</label>
                    <input type="number" step="1" name="dli_target_count" id="dli_target_count" class="form-control" value="{{ old('dli_target_count', $project?->dli_target_count) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">DLI Amount Target (Donor)</label>
                    <input type="number" step="0.01" name="dli_amount_target_donor" id="dli_amount_target_donor" class="form-control" value="{{ old('dli_amount_target_donor', $project?->dli_amount_target_donor) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">DLI Amount Target (INR)</label>
                    <input type="number" step="0.01" name="dli_amount_target_inr" id="dli_amount_target_inr" class="form-control" value="{{ old('dli_amount_target_inr', $project?->dli_amount_target_inr) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">TA Amount Reimb. Target (Donor)</label>
                    <input type="number" step="0.01" name="ta_amount_reimb_target_donor" id="ta_amount_reimb_target_donor" class="form-control" value="{{ old('ta_amount_reimb_target_donor', $project?->ta_amount_reimb_target_donor) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">TA Amount Reimb. Target (INR)</label>
                    <input type="number" step="0.01" name="ta_amount_reimb_target_inr" id="ta_amount_reimb_target_inr" class="form-control" value="{{ old('ta_amount_reimb_target_inr', $project?->ta_amount_reimb_target_inr) }}">
                </div>
            </div>
        </div>

        <div id="non_dli_fields" class="mt-3" style="display: {{ (string)$isDliValue === '0' ? 'block' : 'none' }};">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Target Expenditure (Donor) *</label>
                    <input type="number" step="0.01" name="target_expenditure_donor" id="target_expenditure_donor" class="form-control" value="{{ old('target_expenditure_donor', $project?->target_expenditure_donor) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Target Expenditure (INR) *</label>
                    <input type="number" step="0.01" name="target_expenditure_inr" id="target_expenditure_inr" class="form-control" value="{{ old('target_expenditure_inr', $project?->target_expenditure_inr) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Reimbursement Target (Donor) *</label>
                    <input type="number" step="0.01" name="reimbursement_target_donor" id="reimbursement_target_donor" class="form-control" value="{{ old('reimbursement_target_donor', $project?->reimbursement_target_donor) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Reimbursement Target (INR) *</label>
                    <input type="number" step="0.01" name="reimbursement_target_inr" id="reimbursement_target_inr" class="form-control" value="{{ old('reimbursement_target_inr', $project?->reimbursement_target_inr) }}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold">
        <i class="fas fa-map-marker-alt me-2"></i>5. Description & Locations
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Districts (Locations) *</label>
                <select name="implementation_locations[]" class="form-select select2" multiple required>
                    @php $currentLocs = old('implementation_locations', $project?->implementation_locations ?? []); @endphp
                    @foreach($districts as $district)
                        <option value="{{ $district }}" @selected(in_array($district, $currentLocs ?? [], true))>
                            {{ $district }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">You can select multiple districts.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Project Objectives *</label>
                <textarea name="objective" class="form-control" rows="4" required>{{ old('objective', $project?->objective) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Project Components *</label>
                <textarea name="components" class="form-control" rows="4" required>{{ old('components', $project?->components) }}</textarea>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const revisedToggle = document.getElementById('revised_toggle');
        const revisedFields = document.getElementById('revised_fields');
        if (revisedToggle && revisedFields) {
            revisedToggle.addEventListener('change', function () {
                revisedFields.style.display = this.value == '1' ? 'block' : 'none';
            });
        }

        const dliToggle = document.getElementById('dli_toggle');
        const dliFields = document.getElementById('dli_fields');
        const nonDliFields = document.getElementById('non_dli_fields');

        const dliInputIds = [
            'dli_target_count',
            'dli_amount_target_donor',
            'dli_amount_target_inr',
            'ta_amount_reimb_target_donor',
            'ta_amount_reimb_target_inr',
        ];
        const nonDliInputIds = [
            'target_expenditure_donor',
            'target_expenditure_inr',
            'reimbursement_target_donor',
            'reimbursement_target_inr',
        ];

        function setInputsEnabledAndRequired(inputIds, { enabled, required }) {
            inputIds.forEach((id) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.disabled = !enabled;
                if (required) el.setAttribute('required', 'required');
                else el.removeAttribute('required');
            });
        }

        function syncDliUi() {
            if (!dliToggle || !dliFields || !nonDliFields) return;
            const isDli = dliToggle.value == '1';
            dliFields.style.display = isDli ? 'block' : 'none';
            nonDliFields.style.display = isDli ? 'none' : 'block';

            // Only enable/require the relevant section inputs
            setInputsEnabledAndRequired(dliInputIds, { enabled: isDli, required: isDli });
            setInputsEnabledAndRequired(nonDliInputIds, { enabled: !isDli, required: !isDli });
        }

        if (dliToggle && dliFields && nonDliFields) {
            dliToggle.addEventListener('change', syncDliUi);
            syncDliUi();
        }
    })();
</script>
