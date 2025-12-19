<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" title="Create Procurement Details"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.package-projects.index', 'label' => 'Package Projects'],
                [
                    'route' => 'admin.package-projects.show',
                    'params' => $packageProject,
                    'label' => 'Package #' . $packageProject->id,
                ],
                ['label' => 'Create Procurement'],
            ]" />

        <div class="row mb-3">
            <div class="col-md-8">
                <x-admin.package-card :packageProject="$packageProject" />
            </div>
            <div class="col-md-4">
                <x-admin.approval-details :packageProject="$packageProject" />
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the errors below:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('admin.procurement-details.store', $packageProject) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary">
                            <h5 class="mb-0  text-white h2">
                                <i class="fas fa-plus-circle me-2"></i> Procurement Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <p for="method_of_procurement" class="form-label h3">Method of Procurement *</p>
                                    <select class="form-control @error('method_of_procurement') is-invalid @enderror"
                                        id="method_of_procurement" name="method_of_procurement" required>
                                        <option value="">Select Method</option>
                                        @foreach ($methodsOfProcurement as $method)
                                            <option value="{{ $method }}"
                                                {{ old('method_of_procurement') == $method ? 'selected' : '' }}>
                                                {{ $method }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('method_of_procurement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
    <p for="type_of_procurement_id" class="form-label h3">Type of Package *</p>
    <select class="form-control @error('type_of_procurement_id') is-invalid @enderror"
        id="type_of_procurement_id" name="type_of_procurement_id" required>
        <option value="">Select Type</option>
        @foreach($typesOfProcurement as $type)
            <option value="{{ $type->id }}" 
                {{ old('type_of_procurement_id') == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
            </option>
        @endforeach
    </select>
    @error('type_of_procurement_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


                                <div class="col-md-4">
                                    <p for="publication_date" class="form-label h3">Bid Publication Date</p>
                                    <input type="date"
                                        class="form-control @error('publication_date') is-invalid @enderror"
                                        id="publication_date" name="publication_date"
                                        value="{{ old('publication_date') }}">
                                    @error('publication_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <p for="tender_fee" class="form-label h3">Tender Fee (₹)</p>
                                    <input type="number" step="0.01"
                                        class="form-control @error('tender_fee') is-invalid @enderror" id="tender_fee"
                                        name="tender_fee" value="{{ old('tender_fee') }}">
                                    @error('tender_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <p for="earnest_money_deposit" class="form-label h3">EMD Value (₹)</p>
                                    <input type="number" step="0.01"
                                        class="form-control @error('earnest_money_deposit') is-invalid @enderror"
                                        id="earnest_money_deposit" name="earnest_money_deposit"
                                        value="{{ old('earnest_money_deposit') }}">
                                    @error('earnest_money_deposit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <p for="emd_validity_days" class="form-label h3">EMD Validity <span class="h5">
                                            (in Days) </span> </p>
                                    <input type="number"
                                        class="form-control @error('emd_validity_days') is-invalid @enderror"
                                        id="emd_validity_days" name="emd_validity_days"
                                        value="{{ old('emd_validity_days') }}">
                                    @error('emd_validity_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <p for="bid_validity_days" class="form-label h3">Bid Validity <span class="h5">
                                            (in Days) </span> </p>
                                    <input type="number"
                                        class="form-control @error('bid_validity_days') is-invalid @enderror"
                                        id="bid_validity_days" name="bid_validity_days"
                                        value="{{ old('bid_validity_days') }}">
                                    @error('bid_validity_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <p for="publication_document" class="form-label h3"> Bid Publication Document</p>
                                    <input type="file"
                                        class="form-control @error('publication_document') is-invalid @enderror"
                                        id="publication_document" name="publication_document">
                                    <div class="form-text">Accepted formats: PDF, DOC, DOCX (Max: 2MB)</div>
                                    @error('publication_document')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex justify-content-end border-top pt-4">
                                <a href="{{ route('admin.package-projects.show', $packageProject) }}"
                                    class="btn btn-light me-3">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Create Procurement
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
