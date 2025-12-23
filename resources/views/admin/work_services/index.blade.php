<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs and Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-cogs me-2 text-success"></i> Work Services Management
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item">Admin</li>
                            <li class="breadcrumb-item active">Work Services</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

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
                    <i class="fas fa-list me-2"></i> Work Services List
                </h5>
                <a href="{{ route('admin.work_services.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add Work Service
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table :headers="['ID', 'Name', 'Department', 'Created At', 'Actions']" id="work-services-table" :excel="true" :print="true" :pageLength="10">
                    @foreach ($workServices as $service)
                        <tr>
                            <td>{{ $service->id }}</td>
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->department->name ?? '-' }}</td>
                            <td>{{ $service->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.work_services.edit', $service) }}" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.work_services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this work service?')">
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

                {{ $workServices->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
