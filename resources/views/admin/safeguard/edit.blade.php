<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-edit text-warning" title="Edit Safeguard Entry" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['route' => 'admin.safeguard-global.index', 'label' => 'Safeguard Entries'],
            ['label' => 'Edit'],
        ]" />

        <!-- Alerts -->
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            </div>
        @endif

        <!-- Edit Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-warning">
                    <i class="fas fa-edit me-2"></i> Update Entry (Bulk)
                </h5>
                <small class="text-muted">This will update all entries in this group.</small>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.safeguard-global.update', $entry->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- SL No -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">SL No</label>
                            <input type="text" name="sl_no" class="form-control"
                                value="{{ old('sl_no', $entry->sl_no) }}" required>
                        </div>

                        <!-- Validity -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Is Validity</label>
                            <select name="is_validity" class="form-select">
                                <option value="1" {{ old('is_validity', $entry->is_validity) ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ !old('is_validity', $entry->is_validity) ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <!-- Major Head Checkbox -->
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="is_major_head" id="is_major_head"
                                    value="1" {{ old('is_major_head', $entry->is_major_head) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_major_head">
                                    Is Major Head?
                                </label>
                            </div>
                        </div>

                        <!-- Compliance (readonly) -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Compliance</label>
                            <input type="text" class="form-control"
                                value="{{ $entry->safeguardCompliance->name ?? 'N/A' }}" readonly>
                        </div>

                        <!-- Phase (readonly) -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phase</label>
                            <input type="text" class="form-control"
                                value="{{ $entry->contractionPhase->name ?? 'N/A' }}" readonly>
                        </div>

                        <!-- Item Description -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Item Description</label>
                            <textarea name="item_description" rows="3" class="form-control" required>{{ old('item_description', $entry->item_description) }}</textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.safeguard-global.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update All Related
                        </button>
                    </div>
                </form>
            </div>
            <!-- List where this safeguard entry is used -->
<div class="card shadow-sm mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-info">
            <i class="fas fa-project-diagram me-2"></i>
            Sub-Package Usage
        </h5>
        <small class="text-muted">This safeguard entry exists in the following sub-packages:</small>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Sub-Package Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupEntries as $g)
                    <tr>
                        <td>{{ $g->id }}</td>
                        <td>{{ $g->subPackageProject->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

        </div>
    </div>
</x-app-layout>
