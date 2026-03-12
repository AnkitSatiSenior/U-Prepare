<x-admin.card title="Contract Amendment" icon="fas fa-file-contract" headerClass="bg-success text-white fw-bold">

    @if ($updates->isEmpty())
    <p class="text-muted mb-0">No updates have been recorded for this contract.</p>
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
            'Update Document',
        ]" :excel="true" :print="true" title="Contract Update History Export" searchPlaceholder="Search history..."
        resourceName="contract-updates" :pageLength="10">
        @foreach ($updates as $index => $update)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                ₹{{ $update->old_contract_value ? number_format($update->old_contract_value, 2) : '—' }}
            </td>
            <td>
                ₹{{ $update->new_contract_value ? number_format($update->new_contract_value, 2) : '—' }}
            </td>
            <td>{{ $update->old_initial_completion_date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $update->new_initial_completion_date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $update->old_actual_completion_date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $update->new_actual_completion_date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $update->changed_at?->format('d M Y H:i') }}</td>
            <td class="align-middle text-center">
                @if(!empty($update->update_document))
                @php
                $documentUrl = \Illuminate\Support\Facades\Storage::disk('s3')->url($update->update_document);
                @endphp

                <a href="{{ $documentUrl }}" target="_blank" rel="noopener noreferrer"
                    class="btn btn-sm btn-outline-primary shadow-sm fw-bold">
                    <i class="fas fa-external-link-alt me-1"></i> View Document
                </a>
                @else
                <span class="badge bg-light text-muted border px-2 py-1">
                    <i class="fas fa-minus me-1"></i> No File
                </span>
                @endif
            </td>
        </tr>
        @endforeach
    </x-admin.data-table>
    @endif
</x-admin.card>