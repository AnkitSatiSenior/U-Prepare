<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb and Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-info"></i> EPC Entries
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.already_define_epc.index') }}">EPC Entries</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

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

        <!-- Card with Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-info">
                    <i class="fas fa-list me-2"></i> EPC Entries List
                </h5>
                <a href="{{ route('admin.already_define_epc.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add New Entry
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table :headers="['ID', 'Work Service', 'SL No', 'Activity Name', 'Stage Name', 'Percent', 'Actions']" id="epc-entries-table" :excel="true" :print="true"
                    :pageLength="10">
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->workService->name ?? 'N/A' }}</td>
                            <td>{{ $item->sl_no }}</td>
                            <td>{{ $item->activityName->name ?? 'N/A' }}</td>
                            <td>{{ $item->stage_name }}</td>
                            <td>{{ number_format($item->percent, 2) }}%</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.already_define_epc.show', $item) }}"
                                        class="btn btn-sm btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.already_define_epc.edit', $item) }}"
                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.already_define_epc.destroy', $item) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this entry?');"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
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
