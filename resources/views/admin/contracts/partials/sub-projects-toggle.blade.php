<!-- Multiple sub-projects toggle -->
<div class="col-md-6">
    <label class="form-label">Multiple Sub-projects?</label>
    <div>
        <label class="form-check form-check-inline">
            <input type="radio" name="has_multiple_sub_projects" value="yes" class="form-check-input"
                {{ old('has_multiple_sub_projects', $contract->count_sub_project > 1 ? 'yes' : 'no') === 'yes' ? 'checked' : '' }}>
            Yes
        </label>
        <label class="form-check form-check-inline">
            <input type="radio" name="has_multiple_sub_projects" value="no" class="form-check-input"
                {{ old('has_multiple_sub_projects', $contract->count_sub_project > 1 ? 'yes' : 'no') === 'no' ? 'checked' : '' }}>
            No
        </label>
    </div>
</div>

<!-- Single sub-project inputs -->
<div class="col-12 single-sub">
    <label class="form-label">Sub Project Name</label>
    <input type="text" name="sub_project_name" class="form-control"
        value="{{ old('sub_project_name', optional($contract->project)->package_name) }}">
</div>
<div class="col-12 single-sub">
    <label class="form-label">Sub Project Contract Value</label>
    <input type="number" step="0.01" name="sub_project_contract_value" class="form-control"
        value="{{ old('sub_project_contract_value', $contract->contract_value) }}">
</div>

<!-- Container for dynamically generated sub-projects -->
<div id="multiSubProjects" class="row g-3 multi-sub" style="display:none;"></div>

@push('scripts')
<script>
    function toggleSubFields() {
        const isMulti = document.querySelector('input[name="has_multiple_sub_projects"][value="yes"]').checked;
        const multiSubContainer = document.getElementById('multiSubProjects');
        const contractValue = parseFloat(document.querySelector('input[name="contract_value"]').value) || 0;

        document.querySelectorAll('.single-sub').forEach(el => el.style.display = isMulti ? 'none' : 'block');
        multiSubContainer.style.display = isMulti ? 'flex' : 'none';

        if (isMulti) {
            let count = parseInt(prompt("Enter number of sub-projects:"), 10);
            if (isNaN(count) || count < 2) count = 2;

            // Clear old inputs
            multiSubContainer.innerHTML = '';

            // Distribute contract value equally as default
            let defaultValue = contractValue > 0 ? (contractValue / count).toFixed(2) : '';

            for (let i = 1; i <= count; i++) {
                let nameField = `
                    <div class="col-md-6">
                        <label class="form-label">Sub Project ${i} Name</label>
                        <input type="text" name="multi_sub_projects[${i}][name]" class="form-control" required>
                    </div>
                `;
                let valueField = `
                    <div class="col-md-6">
                        <label class="form-label">Sub Project ${i} Contract Value</label>
                        <input type="number" step="0.01" name="multi_sub_projects[${i}][value]" class="form-control" value="${defaultValue}" required>
                    </div>
                `;
                multiSubContainer.insertAdjacentHTML('beforeend', nameField + valueField);
            }
        }
    }

    document.querySelectorAll('input[name="has_multiple_sub_projects"]').forEach(el => {
        el.addEventListener('change', toggleSubFields);
    });

    // Initialize state on load
    toggleSubFields();
</script>
@endpush
