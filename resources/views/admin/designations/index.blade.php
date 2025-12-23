<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
    icon="fas fa-id-card-alt text-primary"
    title="Designations Management"
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
        ['label' => 'Admin'],
        ['label' => 'Designations']
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

        <!-- Designations Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Designation List
                </h5>
                <a href="{{ route('admin.designations.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create Designation
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table id="designations-table" :headers="['ID', 'Title', 'Created At', 'Actions']" :excel="true" :print="true"
                    title="Designations Export" searchPlaceholder="Search designations..." resourceName="designations"
                    :pageLength="10">
                    @foreach ($designations as $designation)
                        <tr>
                            <td>{{ $designation->id }}</td>
                            <td>{{ $designation->title }}</td>
                            <td>{{ $designation->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.designations.edit', $designation) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.designations.destroy', $designation) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this designation?')">
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
