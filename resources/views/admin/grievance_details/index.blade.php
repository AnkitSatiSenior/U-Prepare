<x-app-layout>
    <div class="container-fluid">

        <x-admin.breadcrumb-header
            icon="fas fa-list text-primary"
            title="Complaint Details Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Complaint Details']
            ]"
        />

        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Complaint Details List
                </h5>
                <a href="{{ route('admin.grievance_details.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add Detail
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="details-table"
                    :headers="['ID','Nature','Name','Slug','Actions']"
                    :excel="true"
                    :print="true"
                    title="Complaint Details Export"
                    searchPlaceholder="Search details..."
                    resourceName="details"
                    :pageLength="10"
                >
                    @foreach ($details as $detail)
                        <tr>
                            <td>{{ $detail->id }}</td>
                            <td>{{ $detail->nature->name }}</td>
                            <td>{{ $detail->name }}</td>
                            <td>{{ $detail->slug }}</td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.grievance_details.edit', $detail) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.grievance_details.destroy', $detail) }}" method="POST"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> Delete
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
