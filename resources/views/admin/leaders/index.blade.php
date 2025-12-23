<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header
            icon="fas fa-user-tie text-primary"
            title="Leaders Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Leaders']
            ]"
        />

        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Leaders List
                </h5>
                <a href="{{ route('admin.leaders.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add Leader
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table
                    id="leaders-table"
                    :headers="['ID', 'Image', 'Name', 'Title', 'Status', 'Order', 'Actions']"
                    :excel="true"
                    :print="true"
                    title="Leaders Export"
                    searchPlaceholder="Search leaders..."
                    resourceName="leaders"
                    :pageLength="10"
                >
                    @foreach ($leaders as $leader)
                        <tr>
                            <td>{{ $leader->id }}</td>
                            <td>
                                @if ($leader->img)
                                    <img src="{{ asset('storage/' . $leader->img) }}"
                                         alt="Leader"
                                         class="rounded shadow-sm"
                                         width="60">
                                @endif
                            </td>
                            <td>{{ $leader->name }}</td>
                            <td>{{ $leader->title }}</td>
                            <td>
                                <span class="badge {{ $leader->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $leader->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $leader->order }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.leaders.edit', $leader) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.leaders.destroy', $leader) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i> Delete
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
