{{-- Hidden input to send contract_id --}}
<input type="hidden" name="contract_id" value="{{ $contract->id }}">

{{-- Display contract info --}}
<p><strong>Contract:</strong> {{ $contract->project?->package_name ?? $contract->contract_number }} 
    (Value: ₹{{ number_format($contract->contract_value, 2) }})
</p>

{{-- Security Type --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Security Name</label>
    <select name="security_type_id" class="form-control" required>
        <option value="">-- Select Security Name --</option>
        @foreach ($types as $type)
            <option value="{{ $type->id }}" 
                {{ old('security_type_id', $contractSecurity?->security_type_id ?? '') == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- Security Form --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Form of Security</label>
    <select name="security_form_id" class="form-control" required>
        <option value="">-- Select Form of Security --</option>
        @foreach ($forms as $form)
            <option value="{{ $form->id }}" 
                {{ old('security_form_id', $contractSecurity?->security_form_id ?? '') == $form->id ? 'selected' : '' }}>
                {{ $form->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- Issue & Expiry Dates --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Issue Start Date</label>
        <input type="date" name="issue_date" 
               value="{{ old('issue_date', $contractSecurity?->issue_date ?? '') }}" 
               class="form-control">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Issue End Date</label>
        <input type="date" name="issued_end_date" 
               value="{{ old('issued_end_date', $contractSecurity?->issued_end_date ?? '') }}" 
               class="form-control">
    </div>
</div>

{{-- Security Number --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Security Number</label>
    <input type="text" name="security_number" 
           value="{{ old('security_number', $contractSecurity?->security_number ?? '') }}" 
           class="form-control">
</div>

{{-- Bank Name / Issuing Authority --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Issuing Authority</label>
    <input type="text" name="bank_name" 
           value="{{ old('bank_name', $contractSecurity?->bank_name ?? '') }}" 
           class="form-control">
</div>

{{-- Value --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Amount (₹)</label>
    <input type="number" step="0.01" name="value" 
           value="{{ old('value', $contractSecurity?->value ?? '') }}" 
           class="form-control">
</div>

{{-- File Upload --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Upload Security Documents (PDF/Image)</label>
    <input type="file" name="file_path" class="form-control">
    
    @if (!empty($contractSecurity?->file_path))
        <p class="mt-1">Current File:
            <a href="{{ Storage::url($contractSecurity->file_path) }}" target="_blank" class="text-primary">
                <i class="fas fa-file"></i> View
            </a>
        </p>
    @endif
</div>
