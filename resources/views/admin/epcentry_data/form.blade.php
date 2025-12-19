@csrf
<div class="mb-3">
    <label class="form-label">Sub Package Project ID</label>
    <input type="number" name="sub_package_project_id" class="form-control"
        value="{{ old('sub_package_project_id', $epcentry_data->sub_package_project_id ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">SL No</label>
    <input type="text" name="sl_no" class="form-control"
        value="{{ old('sl_no', $epcentry_data->sl_no ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Item Description</label>
    <textarea name="item_description" class="form-control" required>{{ old('item_description', $epcentry_data->item_description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Percent</label>
    <input type="number" step="0.01" name="percent" class="form-control"
        value="{{ old('percent', $epcentry_data->percent ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Amount</label>
    <input type="number" step="0.01" name="amount" class="form-control"
        value="{{ old('amount', $epcentry_data->amount ?? '') }}">
</div>

<button type="submit" class="btn btn-primary">Save</button>
<a href="{{ route('admin.epcentry_data.index') }}" class="btn btn-secondary">Cancel</a>
