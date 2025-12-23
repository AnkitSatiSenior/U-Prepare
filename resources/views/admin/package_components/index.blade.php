{{-- resources/views/admin/package_components/index.blade.php --}}
<x-app-layout>
    <div class="container-fluid">
        
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header 
            icon="fas fa-cogs text-primary" 
            title="Package Components Management" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                ['label' => 'Admin'],
                ['label' => 'Package Components']
            ]"  
        /> 

        <!-- Success/Error Alerts -->
        @if (session('success'))
            <x-alert type="success" :message="session('success')" dismissible class="mb-3" />
        @endif

        @if (session('error'))
            <x-alert type="danger" :message="session('error')" dismissible class="mb-3" />
        @endif

        <!-- Table Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h4">
                    <i class="fas fa-list me-2"></i> Package Components List
                </h5>
                <a href="{{ route('admin.package-components.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create Component
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="package-components-table" 
                    :headers="['Name', 'Budget (₹)', 'Created At', 'Actions']" 
                    :excel="true" 
                    :print="true" 
                    title="Package Components Export" 
                    searchPlaceholder="Search components..." 
                    resourceName="package-components" 
                    :pageLength="10"
                >
                    @foreach ($components as $component)
                        <tr>
                            <!-- Name -->
                            <td class="align-middle fw-semibold">
                                {{ $component->name }}
                            </td>

                            <!-- Budget -->
                            <td class="align-middle text-success">

                                {{ formatPriceToCR($component->budget ) }}

                                

                            </td>

                            <!-- Created At -->
                            <td class="align-middle">
                                {{ $component->created_at?->format('d M, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="align-middle">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.package-components.show', $component->id) }}" 
                                       class="btn btn-sm btn-info text-white" 
                                       title="View Details"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.package-components.edit', $component->id) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="Edit"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.package-components.destroy', $component->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this component?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Delete"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash-alt"></i>
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

    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</x-app-layout>
