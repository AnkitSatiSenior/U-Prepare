<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-history text-primary" title="Contract Update History" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['route' => 'admin.contracts.index', 'label' => 'Contracts'],
            ['label' => 'History'],
        ]" />

        <!-- Flash Messages -->
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

        <!-- Contract Details -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-file-contract me-2"></i> Contract: {{ $contract->contract_number }}
                        </h5>
                        <p class="mb-1"><strong>Value:</strong> ₹{{ number_format($contract->contract_value, 2) }}</p>
                        <p class="mb-1"><strong>Signing Date:</strong>
                            {{ $contract->signing_date?->format('d M Y') ?? '—' }}</p>
                        <p class="mb-0"><strong>Completion Date:</strong>
                            {{ $contract->actual_completion_date?->format('d M Y') ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update History Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Update History
                </h5>
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Contracts
                </a>
            </div>

            <div class="card-body">
                @if ($contract->updates->isEmpty())
                    <p class="text-muted">No updates have been recorded for this contract.</p>
                @else
                    <x-admin.data-table id="contract-history-table" :headers="[
                        '#',
                        'Old Value',
                        'New Value',
                        'Old Initial Completion Date',
                        'New Initial Completion Date',
                        'Old Actual Completion Date',
                        'New Actual Completion Date',
                        'Changed At',
                    ]" :excel="true"
                        :print="true" title="Contract Update History Export" searchPlaceholder="Search history..."
                        resourceName="contract-updates" :pageLength="10">
                        @foreach ($contract->updates as $index => $update)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>₹{{ number_format($update->old_contract_value, 2) ?? '—' }}</td>
                                <td>₹{{ number_format($update->new_contract_value, 2) ?? '—' }}</td>
                                <td>{{ $update->old_initial_completion_date?->format('d M Y') ?? '—' }}</td>
                                <td>{{ $update->new_initial_completion_date?->format('d M Y') ?? '—' }}</td>
                                <td>{{ $update->old_actual_completion_date?->format('d M Y') ?? '—' }}</td>
                                <td>{{ $update->new_actual_completion_date?->format('d M Y') ?? '—' }}</td>
                                <td>{{ $update->changed_at?->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </x-admin.data-table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
