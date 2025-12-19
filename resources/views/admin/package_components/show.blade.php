{{-- resources/views/admin/package_components/show.blade.php --}}
<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header 
            icon="fas fa-eye text-primary" 
            title="View Package Component" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                ['label' => 'Admin'],
                ['route' => 'admin.package-components.index', 'label' => 'Package Components'],
                ['label' => 'Details']
            ]"  
        /> 

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="fw-bold mb-3">{{ $packageComponent->name }}</h4>

                <p class="mb-2">
                    <strong class="text-dark">Budget:</strong>
                    <span class="text-success">₹{{ number_format($packageComponent->budget ?? 0, 2) }}</span>
                </p>

                <p class="mb-3">
                    <strong class="text-dark">Description:</strong><br>
                    <span class="text-muted">
                        {{ $packageComponent->description ?: 'No description provided.' }}
                    </span>
                </p>

                <p class="text-muted mb-1">
                    <i class="fas fa-calendar-plus"></i>
                    Created At: {{ $packageComponent->created_at?->format('d M, Y H:i') }}
                </p>
                <p class="text-muted mb-3">
                    <i class="fas fa-calendar-check"></i>
                    Updated At: {{ $packageComponent->updated_at?->format('d M, Y H:i') }}
                </p>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.package-components.edit', $packageComponent->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.package-components.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
