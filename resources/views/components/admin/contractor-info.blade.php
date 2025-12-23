<x-admin.card title="Contractor Information" icon="fas fa-user-tie" headerClass="bg-secondary text-white h4">
    <x-slot:headerRight>
        @if (!empty($contractor->is_verified) && $contractor->is_verified)
            <span class="badge bg-success px-3 py-2 text-white rounded-pill">
                <i class="fas fa-check-circle me-1"></i> Verified
            </span>
        @else
            <span class="badge bg-success px-3 py-2 text-white rounded-pill">
                <i class="fas fa-check-circle me-1"></i> Verified
            </span>
        @endif
    </x-slot:headerRight>

    <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <p class="mb-0 d-flex align-items-center h3">
                <i class="fas fa-building text-primary me-2"></i> &nbsp; Company Name
            </p>
            <span class="fw-bold h4"> {{ $contractor->company_name ?? 'N/A' }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <p class="mb-0 d-flex align-items-center h3">
                <i class="fas fa-receipt text-warning me-2"></i> &nbsp; GST Number
            </p>
            <span class="fw-bold h4"> {{ $contractor->gst_no ?? 'N/A' }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <p class="mb-0 d-flex align-items-center h3">
                <i class="fas fa-envelope text-info me-2"></i> &nbsp; Email
            </p>
            <span class="fw-bold h4"> {{ $contractor->email ?? 'N/A' }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <p class="mb-0 d-flex align-items-center h3">
                <i class="fas fa-phone text-success me-2"></i> &nbsp; Phone
            </p>
            <span class="fw-bold h4"> {{ $contractor->phone ?? 'N/A' }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-start">
            <p class="mb-0 d-flex align-items-center h3">
                <i class="fas fa-map-marker-alt text-danger me-2 mt-1"></i> &nbsp; Address
            </p>
            <span class="fw-bold h4"> {{ $contractor->address ?? 'N/A' }}</span>
        </li>
    </ul>
</x-admin.card>
