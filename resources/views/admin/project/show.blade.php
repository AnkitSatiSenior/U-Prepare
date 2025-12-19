<x-app-layout>
    <div class="container-fluid">
          <!-- Breadcrumb -->
         <x-admin.breadcrumb-header icon="fas fa-info-circle text-info" title="Projects" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['route' => 'admin.project.index', 'label' => ' <i class=\'fas fa-project-diagram \'></i> Projects'],
            ['class' => 'active', 'label' => 'Create '],
        ]" />
        

                    

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <p class="mb-0 h3">
                    <i class="fas fa-info-circle me-2"></i> Project Name :  {{ $project->name }}
                </p>
                <div>
                    <a href="{{ route('admin.project.edit', $project) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <p class="text-muted h2">Project Name</p>
                            <p class="h3">{{ $project->name }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3"> 
                            <p class="text-muted h2">Project Budget Allocated </p>
                            <p class="h3">{{ $project->budget ? '₹' . number_format($project->budget, 2) : 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
                
                {{-- <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-muted">Created At</h6>
                            <p>{{ $project->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-muted">Last Updated</h6>
                            <p>{{ $project->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div> --}}
                
                <div class="d-flex justify-content-end mt-4">
                
                    <a href="{{ route('admin.project.index') }}" class="btn btn-info me-2">
                    <i class="fas  fa-classic fa-solid fa-circle-left me-1"></i> Back to View Projects
                </a>
 
                </div>
            </div>
        </div>
    </div>
</x-app-layout>