<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-file-contract text-primary"
            title="Tenders Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Tenders']
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

        <!-- Tenders Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Tender List
                </h5>
                <a href="{{ route('admin.tenders.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create Tender
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table
                    id="tenders-table"
                    :headers="['ID', 'Title', 'Open Date', 'Close Date', 'File', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Tenders Export"
                    searchPlaceholder="Search tenders..."
                    resourceName="tenders"
                    :pageLength="10"
                >
                    @foreach ($tenders as $tender)
                        <tr>
                            <td>{{ $tender->id }}</td>
                            <td>
                                {{ $tender->title_en }} / {{ $tender->title_hi }}
                            </td>
                            <td>{{ formatDate($tender->open_date) }}</td>
                            <td>{{ formatDate($tender->close_date) }}</td>
                            <td>
                                @if($tender->file)
                                    <a href="{{ Storage::url($tender->file) }}" target="_blank">
                                        View
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.tenders.edit', $tender) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.tenders.destroy', $tender) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this tender?')">
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
