<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb Header -->
        <x-admin.breadcrumb-header 
            icon="fas fa-boxes text-success" 
            title="Type of Procurements Management" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                ['label' => 'Admin'], 
                ['label' => 'Type of Procurements']
            ]" 
        />

        <!-- Success/Error Messages -->
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
                <h5 class="mb-0 text-success">
                    <i class="fas fa-list me-2"></i>Type of Procurements List
                </h5>
                <a href="{{ route('admin.type-of-procurements.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Create Type
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table :headers="['ID', 'Name', 'Description', 'Actions']" id="procurements-table" :excel="true" :print="true" :pageLength="10">
                    @foreach ($procurements as $procurement)
                        <tr @if($procurement->trashed()) class="table-danger" @endif>
                            <td>{{ $procurement->id }}</td>
                            <td>{{ $procurement->name }}</td>
                            <td>{{ $procurement->description ?? '-' }}</td>
                            <td>
                                <div class="d-flex justify-content-end">
                                    @if(!$procurement->trashed())
                                        <a href="{{ route('admin.type-of-procurements.edit', $procurement) }}" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.type-of-procurements.destroy', $procurement) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash-alt me-1"></i> Delete
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.type-of-procurements.restore', $procurement->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Restore this item?')">
                                                <i class="fas fa-undo me-1"></i> Restore
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
