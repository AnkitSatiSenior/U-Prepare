<x-app-layout>
    <div class="container-fluid">
        {{-- Breadcrumb --}}
        <x-admin.breadcrumb-header 
            icon="fas fa-users-cog text-primary" 
            title="Role Dashboards Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Role Dashboards']
            ]"  
        />

        {{-- Flash Message --}}
        @if(session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        <div class="card shadow-sm">
            {{-- Header --}}
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Role Dashboard List
                </h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addForm">
                    <i class="fas fa-plus-circle me-1"></i> Add Role Dashboard
                </button>
            </div>

            {{-- Add Form --}}
            <div id="addForm" >
                <div class="card-body border-top">
                    <form action="{{ route('admin.role_dashboards.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Role</label>
                                <select name="role_id" class="form-control" required>
                                    <option value="">-- Select Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
    <label class="form-label">Department</label>
    <select name="department" class="form-control" required>
        <option value="all">All</option>
        <option value="department">Department</option>
    </select>
</div>


                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-1"></i> Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="card-body">
                <x-admin.data-table 
                    id="role_dashboards-table"
                    :headers="['ID','Role','Department','Actions']"
                    :excel="true"
                    :print="true"
                    title="Role Dashboards Export"
                    searchPlaceholder="Search roles..."
                    resourceName="role_dashboards"
                    :pageLength="10"
                >
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->role->name }}</td>
                            <td>{{ $item->department ?? 'All' }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    {{-- Edit --}}
                                    <button class="btn btn-sm btn-outline-warning" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#editForm{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.role_dashboards.destroy', $item->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Delete this record?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- Edit Form --}}
                                <div id="editForm{{ $item->id }}" class=" mt-2">
                                    <form action="{{ route('admin.role_dashboards.update', $item->id) }}" 
                                          method="POST" 
                                          class="border rounded p-2 bg-light">
                                        @csrf 
                                        @method('PUT')
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-4">
                                                <select name="role_id" class="form-control" required>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}" {{ $role->id == $item->role_id ? 'selected' : '' }}>
                                                            {{ $role->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                              <select name="department" class="form-control" required>
    <option value="all" {{ $item->department == 'all' ? 'selected' : '' }}>All</option>
    <option value="department" {{ $item->department == 'department' ? 'selected' : '' }}>Department</option>
</select>

                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-sm btn-success w-100">
                                                    <i class="fas fa-check"></i> Update
                                                </button>
                                            </div>
                                        </div>
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
