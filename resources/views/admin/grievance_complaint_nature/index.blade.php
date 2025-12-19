<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-list text-primary"
            title="Complaint Nature Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Complaint Nature']
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
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Complaint Nature List
                </h5>
                <a href="{{ route('admin.grievance-complaint-nature.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add Complaint Nature
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="nature-table"
                    :headers="['ID','Name','Slug','Actions']"
                    :excel="true"
                    :print="true"
                    title="Complaint Nature Export"
                    searchPlaceholder="Search complaint nature..."
                    resourceName="grievance-complaint-nature"
                    :pageLength="10"
                >
                    @foreach ($natures as $nature)
                        <tr>
                            <td>{{ $nature->id }}</td>
                            <td>{{ $nature->name }}</td>
                            <td>{{ $nature->slug }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.grievance-complaint-nature.edit', $nature) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.grievance-complaint-nature.destroy', $nature) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this item?')">
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
