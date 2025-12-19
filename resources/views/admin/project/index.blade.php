<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
         <x-admin.breadcrumb-header 
                icon="fas fa-info-circle text-info" 
                title="Projects" 
                :breadcrumbs="[
                    ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                    ['route' => 'admin.project.index', 'label' => 'Projects'], 
                    ['label' => 'View Projects']
                ]"  /> 



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

        <!-- Projects Table -->
        <div class="card shadow-sm">
            
           
            
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h4">
                    <i class="fas fa-list me-2"></i> Add New External Aided Projects  
                </h5>
                <a href="{{ route('admin.project.create') }}" class="h3 btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add Project
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table id="projects-table" :headers="['ID', 'Name', 'Budget (₹)', 'Created At', 'Actions']" :excel="true" :print="true"
                    title="Projects Export" searchPlaceholder="Search projects..." resourceName="projects"
                    :pageLength="10">
                    @foreach ($projects as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>
                                @if($project->budget)
                                    ₹{{ number_format($project->budget / 10000000, 2) }} CR
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $project->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.project.show', $project) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    <a href="{{ route('admin.project.edit', $project) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.project.destroy', $project) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this project?')">
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