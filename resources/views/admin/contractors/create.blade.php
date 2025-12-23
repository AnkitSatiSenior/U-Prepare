
<x-app-layout>
    <div class="container-fluid">
        <!-- Header + Breadcrumb -->
        <x-admin.breadcrumb-header
    icon="fas fa-building text-primary"
    title="Create Contractor"
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
        ['label' => 'Admin'],
        ['route' => 'admin.contractors.index', 'label' => 'Contractors'],
        ['label' => 'Create']
    ]"
/>


        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-user-plus me-2"></i>
                    Add New Contractor
                </h5>
                <a href="{{ route('admin.contractors.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Contractors
                </a>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.contractors.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Company Name -->
                        <div class="col-md-6">
                            <x-label for="company_name" value="Company Name" required />
                            <x-input id="company_name" name="company_name" 
                                value="{{ old('company_name') }}" 
                                placeholder="Enter company name" 
                                required />
                        </div>

                        <!-- Authorized Personnel -->
                        <div class="col-md-6">
                            <x-label for="authorized_personnel_name" value="Authorized Personnel" required />
                            <x-input id="authorized_personnel_name" name="authorized_personnel_name" 
                                value="{{ old('authorized_personnel_name') }}" 
                                placeholder="Enter authorized person name" 
                                required />
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-6">
                            <x-label for="phone" value="Phone Number" />
                            <x-input id="phone" name="phone" 
                                value="{{ old('phone') }}" 
                                placeholder="Enter phone number" />
                        </div>

                        <div class="col-md-6">
                            <x-label for="email" value="Email" />
                            <x-input type="email" id="email" name="email" 
                                value="{{ old('email') }}" 
                                placeholder="Enter email address" />
                        </div>

                        <!-- GST Information -->
                        <div class="col-md-6">
                            <x-label for="gst_no" value="GST Number" />
                            <x-input id="gst_no" name="gst_no" 
                                value="{{ old('gst_no') }}" 
                                placeholder="Enter GST number (29GGGGG1314R9Z6)" />
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <x-label for="address" value="Address" />
                            <textarea id="address" name="address" rows="3" 
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="Enter company address">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end border-top pt-3">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Create Contractor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
