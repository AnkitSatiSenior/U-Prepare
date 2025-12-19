<x-app-layout>
    <div class="container py-4">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-shield-alt text-success" title="Edit Safeguard Entry" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['route' => 'admin.already-define-safeguards.index', 'label' => 'Safeguard Entries'],
            ['label' => 'Edit Entry'],
        ]" />

        <!-- Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        @if ($errors->any())
            <x-alert type="danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-success"><i class="fas fa-edit me-2"></i> Edit Safeguard Entry</h5>
            </div>
            <div class="card-body">

                <form action="{{ route('admin.already-define-safeguards.update', $entry->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Compliance -->
                    <div class="mb-3">
                        <label class="form-label">Safeguard Compliance <span class="text-danger">*</span></label>
                        <select name="safeguard_compliance_id" class="form-select" required>
                            <option value="">-- Select Compliance --</option>
                            @foreach ($compliances as $compliance)
                                <option value="{{ $compliance->id }}"
                                    {{ $entry->safeguard_compliance_id == $compliance->id ? 'selected' : '' }}>
                                    {{ $compliance->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Construction Phase -->
                    <div class="mb-3">
                        <label class="form-label">Construction Phase <span class="text-danger">*</span></label>
                        <select name="contraction_phase_id" class="form-select" required>
                            <option value="">-- Select Phase --</option>
                            @foreach ($phases as $phase)
                                <option value="{{ $phase->id }}"
                                    {{ $entry->contraction_phase_id == $phase->id ? 'selected' : '' }}>
                                    {{ $phase->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category (Optional) -->
                    <div class="mb-3">
                        <label class="form-label">Category (Optional)</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- None --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $entry->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SL No -->
                    <div class="mb-3">
                        <label class="form-label">SL No</label>
                        <input type="text" name="sl_no" class="form-control"
                            value="{{ old('sl_no', $entry->sl_no) }}">
                    </div>

                    <!-- Order By -->
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <input type="number" name="order_by" class="form-control"
                            value="{{ old('order_by', $entry->order_by) }}">
                    </div>

                    <!-- Item Description -->
                    <div class="mb-3">
                        <label class="form-label">Item Description</label>
                        <textarea name="item_description" class="form-control" rows="3">{{ old('item_description', $entry->item_description) }}</textarea>
                    </div>

                    <!-- Is Validity -->
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_validity" class="form-check-input" id="is_validity"
                            value="1" {{ old('is_validity', $entry->is_validity) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_validity">Validity Required</label>
                    </div>

                    <!-- Is Major Head -->
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_major_head" class="form-check-input" id="is_major_head"
                            value="1" {{ old('is_major_head', $entry->is_major_head) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_major_head">Major Head</label>
                    </div>
                    <!-- Is Parent -->
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_parent" class="form-check-input" id="is_parent" value="1"
                            {{ old('is_parent', $entry->is_parent) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_parent">Is Parent Entry</label>
                    </div>


                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Update</button>
                        <a href="{{ route('admin.already-define-safeguards.index') }}" class="btn btn-secondary"><i
                                class="fas fa-arrow-left me-1"></i> Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
