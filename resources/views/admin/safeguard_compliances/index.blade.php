<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-shield-alt text-primary"
            title="Safeguard Compliances Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Safeguard Compliances']
            ]"
        />

        <!-- Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif
        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible />
        @endif

        <!-- Table -->
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Safeguard Compliance List
                </h5>
                <a href="{{ route('admin.safeguard-compliances.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create Safeguard Compliance
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="safeguard-compliances-table"
                    :headers="['ID', 'Name', 'Role', 'Contraction Phases', 'Created At', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Safeguard Compliances Export"
                    searchPlaceholder="Search compliances..."
                    resourceName="safeguard-compliances"
                    :pageLength="10"
                >
                    @foreach ($compliances as $compliance)
                        <tr>
                            <td>{{ $compliance->id }}</td>
                            <td>{{ $compliance->name }}</td>

                            <!-- Role column -->
                            <td>
                                @if($compliance->role)
                                    <span class="badge bg-info text-dark">{{ $compliance->role->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <!-- Contraction Phases column -->
                            <td>
                                @if (!empty($compliance->contraction_phase_ids))
                                    @php
                                        // Preload phases to avoid N+1 queries
                                        $phases = \App\Models\ContractionPhase::whereIn('id', $compliance->contraction_phase_ids)->pluck('name')->toArray();
                                    @endphp
                                    @foreach($phases as $phaseName)
                                        <span class="badge bg-success me-1">{{ $phaseName }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <td>{{ $compliance->created_at->format('M d, Y h:i A') }}</td>

                            <!-- Actions -->
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.safeguard-compliances.edit', $compliance) }}" 
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.safeguard-compliances.destroy', $compliance) }}" method="POST"
                                        onsubmit="return confirm('Delete this safeguard compliance?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
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
