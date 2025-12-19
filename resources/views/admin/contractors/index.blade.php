
<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs and Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2 text-success"></i>Contractors Management
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item">Admin</li>
                            <li class="breadcrumb-item active">Contractors</li>
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
                    <i class="fas fa-list me-2"></i>Contractors List
                </h5>
                <a href="{{ route('admin.contractors.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add Contractor
                </a>
            </div>
            <div class="card-body">
               
                    <x-admin.data-table :headers="['ID', 'Company Name', 'Authorized Personnel', 'Phone', 'Email', 'GST No', 'Actions']" 
                                      id="contractors-table" 
                                      :excel="true" 
                                      :print="true"
                                      :pageLength="10">
                        @foreach ($contractors as $contractor)
                            <tr>
                                <td>{{ $contractor->id }}</td>
                                <td>{{ $contractor->company_name }}</td>
                                <td>{{ $contractor->authorized_personnel_name }}</td>
                                <td>{{ $contractor->phone }}</td>
                                <td>{{ $contractor->email }}</td>
                                <td>{{ $contractor->gst_no }}</td>
                                <td>
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('admin.contractors.show', $contractor) }}" 
                                           class="btn btn-sm btn-outline-info me-2">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                        <a href="{{ route('admin.contractors.edit', $contractor) }}" 
                                           class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.contractors.destroy', $contractor) }}" method="POST">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this contractor?')">
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
