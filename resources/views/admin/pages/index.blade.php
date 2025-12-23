<x-app-layout>
    <div class="container-fluid">
        
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header 
            icon="fas fa-file-alt text-primary" 
            title="Pages Management" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                ['label' => 'Admin'],
                ['label' => 'Pages']
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
                    <i class="fas fa-list me-2"></i> All Pages
                </h5>
                <a href="{{ route('admin.pages.create.form') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create New Page
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="pages-table" 
                    :headers="['Title', 'Slug', 'Status', 'Actions']" 
                    :excel="true" 
                    :print="true" 
                    title="Pages Export" 
                    searchPlaceholder="Search pages..." 
                    resourceName="pages" 
                    :pageLength="10"
                >
                    @foreach ($pages as $page)
                        <tr>
                            <!-- Title -->
                            <td class="align-middle fw-semibold">
                                {{ $page->title }}
                            </td>

                            <!-- Slug -->
                            <td class="align-middle text-muted">
                                {{ $page->slug }}
                            </td>

                            <!-- Status -->
                            <td class="align-middle">
                                <span class="badge bg-{{ $page->status ? 'success' : 'danger' }}">
                                    {{ $page->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="align-middle">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Edit -->
                                    <a href="{{ route('admin.pages.edit.form', $page->id) }}" 
                                       class="btn btn-sm btn-warning text-white" 
                                       title="Edit"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.pages.delete', $page->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this page?')">
                                        @csrf
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
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</x-app-layout>
