{{-- resources/views/admin/financial_progress_update/create.blade.php --}}
<x-app-layout>
    <div class="container py-5">

        {{-- Breadcrumb Header --}}
        <x-admin.breadcrumb-header 
            icon="fas fa-plus-circle text-primary" 
            :title="'Add Financial Progress Update — ' . e($subProject->name)" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i> Dashboard'], 
                ['route' => 'admin.financial-progress-updates.index', 'params' => ['sub_package_project_id' => $subProject->id ], 'label' => 'Financial Progress Updates'], 
                ['label' => 'Add New']
            ]" 
        />

        {{-- Flash Messages --}}
        @if(session('status') && session('message'))
            <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show mb-4">
                {!! session('message') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <strong>Whoops!</strong> Please fix the errors below:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <form 
                    action="{{ route('admin.financial-progress-updates.store') }}" 
                    method="POST" 
                    enctype="multipart/form-data" 
                    novalidate
                >
                    @csrf

                    {{-- Hidden Project ID --}}
                    <input type="hidden" name="project_id" value="{{ $subProject->id }}">

                    {{-- Finance Amount --}}
                    <div class="mb-3">
                        <label for="finance_amount" class="form-label fw-semibold">
                            Finance Amount (₹) <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="finance_amount" 
                            id="finance_amount" 
                            class="form-control @error('finance_amount') is-invalid @enderror"
                            placeholder="Enter finance amount" 
                            value="{{ old('finance_amount') }}" 
                            required
                        >
                        @error('finance_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Number of Bills --}}
                    <div class="mb-3">
                        <label for="no_of_bills" class="form-label fw-semibold">
                            Number of Bills <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="no_of_bills" 
                            id="no_of_bills" 
                            class="form-control @error('no_of_bills') is-invalid @enderror"
                            placeholder="Enter number of bills" 
                            value="{{ old('no_of_bills') }}" 
                            required
                        >
                        @error('no_of_bills')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Bill Serial Numbers --}}
                    <div class="mb-3">
                        <label for="bill_serial_no" class="form-label fw-semibold">
                            Bill Serial Numbers (Optional)
                        </label>
                        <input 
                            type="text" 
                            name="bill_serial_no" 
                            id="bill_serial_no" 
                            class="form-control @error('bill_serial_no') is-invalid @enderror"
                            placeholder="Example: 123, 124, 125" 
                            value="{{ old('bill_serial_no') }}"
                        >
                        <small class="text-muted">Enter multiple bill numbers separated by commas.</small>
                        @error('bill_serial_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit Date --}}
                    <div class="mb-3">
                        <label for="submit_date" class="form-label fw-semibold">
                            Submit Date <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="submit_date" 
                            id="submit_date" 
                            class="form-control @error('submit_date') is-invalid @enderror"
                            value="{{ old('submit_date') }}" 
                            required
                        >
                        @error('submit_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Upload Payment Slips --}}
                    <div class="mb-3">
                        <label for="media" class="form-label fw-semibold">
                            Upload Payment Slips
                        </label>
                        <input 
                            type="file" 
                            name="media[]" 
                            id="media" 
                            class="form-control @error('media') is-invalid @enderror"
                            multiple
                        >
                        <small class="text-muted d-block">You can upload multiple files (JPG, JPEG, PNG, PDF, max 2MB each).</small>
                        @error('media')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('media.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Save Financial Progress
                        </button>
                        <a 
                            href="{{ route('admin.financial-progress-updates.index', ['sub_package_project_id' => $subProject->id]) }}" 
                            class="btn btn-secondary"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
