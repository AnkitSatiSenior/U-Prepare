<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-hand-holding-usd text-success" title="Funding Agencies" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => 'Home'],
            ['label' => 'Funding Agencies']
        ]" />

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Master Agencies</h5>
                <a href="{{ route('admin.funding-agency.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add Agency</a>
            </div>
            <div class="card-body">
                <x-admin.data-table :headers="['ID', 'Name', 'Code', 'Status', 'Actions']">
                    @foreach($agencies as $agency)
                        <tr>
                            <td>{{ $agency->id }}</td>
                            <td>{{ $agency->name }}</td>
                            <td>{{ $agency->code ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $agency->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $agency->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.funding-agency.edit', $agency) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>