<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
         <x-admin.breadcrumb-header icon="fas fa-info-circle text-info" title=" Create New Project" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['route' => 'admin.project.index', 'label' => ' <i class=\'fas fa-project-diagram \'></i> Projects'],
            ['class' => 'active', 'label' => 'Create '],
        ]" />


        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-plus-circle me-2"></i> Project Details
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.project.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">

                        <div class="col-md-6">
                            <div class="row mb-3">
                            

                                <div class="col-md-6">
                                    <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="budget" class="form-label">Budget</label>
                                    <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror" 
                                        id="budget" name="budget" value="{{ old('budget') }}">
                                    @error('budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="d-flex justify-content-end">
                               <a href="{{ route('admin.project.index') }}" class="btn btn-info me-2">
                                    <i class="fas  fa-classic fa-solid fa-circle-left me-1"></i> Back 
                                </a>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Add New Project
                                </button>
                            </div>

                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</x-app-layout>