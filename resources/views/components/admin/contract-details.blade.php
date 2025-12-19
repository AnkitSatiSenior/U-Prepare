@props(['contract'])

<x-admin.card 
    title=" Contract Details" 
    icon="fas fa-file-contract" 
    headerClass="bg-secondary text-white h4">
    <x-slot:headerRight>
        @if($contract->is_updated)
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                <i class="fas fa-edit me-1"></i> Amended ({{ $contract->update_count }})
            </span>
        @else
            <span class="badge bg-success px-3 py-2 text-white rounded-pill">
                <i class="fas fa-check-circle me-1"></i> Original
            </span>
        @endif
    </x-slot:headerRight>

    <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-semibold h4"><i class="fas fa-hashtag text-primary me-2"></i> Contract No</span>
            <span class="fw-bold text-dark h4">{{ $contract->contract_number }}</span>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-semibold h4"><i class="fas fa-money-bill-wave text-success me-2"></i> Contract Value</span>
            <span class="fw-bold text-success h4">₹{{ number_format($contract->contract_value, 2) }}</span>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-semibold h4"><i class="fas fa-shield-alt text-warning me-2"></i> Performance Guarantee</span>
            <span class="fw-bold text-dark h4">₹{{ number_format($contract->security ?? 0, 2) }}</span>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-semibold h4"><i class="fas fa-calendar-check text-info me-2"></i> Signing Date</span>
            <span class="fw-bold h4">{{ optional($contract->signing_date)->format('d M Y') ?? 'N/A' }}</span>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-semibold h4"><i class="fas fa-calendar-day text-info me-2"></i> Commencement Date</span>
            <span class="fw-bold h4">{{ optional($contract->commencement_date)->format('d M Y') ?? 'N/A' }}</span>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-semibold h4"><i class="fas fa-calendar-alt text-info me-2"></i> Initial Completion Date</span>
            <span class="fw-bold h4">{{ optional($contract->initial_completion_date)->format('d M Y') ?? 'N/A' }}</span>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="fw-semibold h4"><i class="fas fa-calendar-plus text-info me-2"></i> Revised Completion Date</span>
            <span class="fw-bold h4">{{ optional($contract->revised_completion_date)->format('d M Y') ?? 'N/A' }}</span>
        </li>
    </ul>
</x-admin.card>
