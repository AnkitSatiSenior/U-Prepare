<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-edit" title="Edit Funding Agency" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => 'Home'],
            ['route' => 'admin.funding-agency.index', 'label' => 'Agencies'],
            ['label' => 'Edit Agency']
        ]" />

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom fw-bold text-dark">
                        Update Agency Details: {{ $fundingAgency->name }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.funding-agency.update', $fundingAgency) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Agency Full Name *</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name', $fundingAgency->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Agency Short Code</label>
                                <input type="text" name="code" class="form-control" 
                                    value="{{ old('code', $fundingAgency->code) }}" placeholder="e.g. WB, ADB">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Agency Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ $fundingAgency->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$fundingAgency->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.funding-agency.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-info text-white px-4">Update Agency</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>