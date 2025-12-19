<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs and Header -->
        <x-admin.breadcrumb-header 
            icon="fas fa-bars text-primary" 
            title="Navigation Menu" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                ['label' => 'Admin'], 
                ['label' => 'Navigation Menu']
            ]" 
        />

        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible icon="fas fa-check-circle" />
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible icon="fas fa-exclamation-triangle" />
                </div>
            </div>
        @endif

        <!-- Main Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list-ol me-2"></i> Navigation Items
                </h5>
                <a href="{{ route('admin.navbar-items.create') }}" class="btn btn-primary btn-sm rounded-pill">
                    <i class="fas fa-plus-circle me-1"></i> Add New Item
                </a>
            </div>
            
            <div class="card-body">
                <div class="alert alert-info bg-light-info border-info text-dark mb-4">
                    <i class="fas fa-info-circle me-2"></i> Drag items using the <i class="fas fa-arrows-alt text-dark mx-1"></i> handle to reorder the navigation menu.
                </div>

                <ul id="sortable" class="list-group list-group-flush">
                    @foreach($navbarItems as $item)
                        @include('admin.navbar.partials.item', ['item' => $item])
                    @endforeach
                    
                    @if($navbarItems->isEmpty())
                        <li class="list-group-item">
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h5>No navigation items found</h5>
                                <p class="mb-0">Start by adding your first menu item</p>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

 
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script>
            $(function() {
                $("#sortable").sortable({
                    handle: ".drag-handle",
                    opacity: 0.7,
                    placeholder: "sortable-placeholder bg-light-primary",
                    tolerance: "pointer",
                    start: function(e, ui) {
                        ui.placeholder.height(ui.item.height());
                    },
                    update: function(event, ui) {
                        var order = $(this).sortable('toArray', {attribute: 'data-id'});
                        $.post("{{ route('admin.navbar-items.update-order') }}", {
                            order: order,
                            _token: "{{ csrf_token() }}"
                        }).fail(function() {
                            Toast.fire({
                                icon: 'error',
                                title: 'Failed to save new order'
                            });
                        });
                    }
                });
                
                // Disable sorting if empty
                @if($navbarItems->isEmpty())
                    $("#sortable").sortable("disable");
                @endif
            });
        </script>

        <style>
            .sortable-placeholder {
                border: 2px dashed #6777ef;
                background-color: rgba(103, 119, 239, 0.1);
                margin-bottom: 10px;
            }
            .drag-handle {
                cursor: move;
                color: #6777ef;
            }
            .list-group-item {
                transition: all 0.3s ease;
            }
            .list-group-item:hover {
                background-color: #f8f9fa;
                transform: translateX(5px);
            }
        </style>
  
</x-app-layout>