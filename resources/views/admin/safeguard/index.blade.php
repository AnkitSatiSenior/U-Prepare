<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-shield-alt text-primary"
            title="Safeguard Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Safeguard Entries']
            ]"
        />

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

        <!-- Safeguard Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Safeguard Entries
                </h5>
            </div>

            <div class="card-body">
                <x-admin.data-table
                    id="safeguard-table"
                    :headers="['SL No', 'Item Description', 'Compliance', 'Phase', 'Validity','Total', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Safeguard Export"
                    searchPlaceholder="Search safeguard entries..."
                    resourceName="safeguards"
                    :pageLength="10"
                >
                    @foreach ($entries as $entry)
                        <tr>
                            <td>{{ $entry->sl_no }}</td>
                            <td>{{ $entry->item_description }} (
                                @if($entry->is_major_head)
                                    <span class="badge bg-success d-inline-flex align-items-center text-white">
                                        <i class="fas fa-flag me-1"></i> Major Head
                                    </span>
                                @else
                                    <span class="badge bg-secondary d-inline-flex align-items-center text-white">
                                        <i class="fas fa-flag me-1"></i> Normal
                                    </span>
                                @endif)</td>
                            <td>{{ $entry->safeguardCompliance->name ?? 'N/A' }} </td>
                            <td>{{ $entry->contractionPhase->name ?? 'N/A' }}</td>

                            <!-- Validity -->
                            <td>
                                <span class="badge {{ $entry->is_validity ? 'bg-success' : 'bg-danger' }}">
                                    {{ $entry->is_validity ? 'Yes' : 'No' }}
                                </span>
                            </td>


                            <!-- Total Entries -->
                            <td>
                                <span class="badge bg-info">
                                    {{ $entry->total_entries }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.safeguard-global.edit', $entry->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.safeguard-global.destroy', $entry->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete all related entries?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt me-1"></i> Delete All
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
