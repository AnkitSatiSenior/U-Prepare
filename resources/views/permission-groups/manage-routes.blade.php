<x-app-layout>

    <div class="container-fluid">

        <x-admin.breadcrumb-header 
            icon="fas fa-link text-primary" 
            title="Manage Routes" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['route' => 'admin.permission.groups.index', 'label' => 'Permission Groups'],
                ['label' => 'Assign Routes'],
            ]" 
        />

        <div class="card shadow-sm">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                
                <div>
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-shield-alt me-2"></i> Assign Routes to:
                        <strong>{{ $group->name }}</strong>
                    </h5>
                    <small class="text-secondary">Total Available Routes: <strong>{{ $routeCount }}</strong></small>
                </div>

                <!-- Back Button -->
                <a href="{{ route('admin.permission.groups.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>

            </div>

            <div class="card-body">

                <form action="{{ route('admin.permission.groups.routes.save', $group->id) }}" method="POST">
                    @csrf

                    <!-- Search + Actions -->
                    <div class="row mb-3">

                        <!-- Search -->
                        <div class="col-md-6">
                            <input 
                                type="text" 
                                id="searchRoute" 
                                class="form-control" 
                                placeholder="Search route..."
                            >
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-6 text-end">
                            <button type="button" id="checkAll" class="btn btn-sm btn-success me-2">
                                <i class="fas fa-check-square me-1"></i> Select All
                            </button>

                            <button type="button" id="uncheckAll" class="btn btn-sm btn-danger">
                                <i class="fas fa-square me-1"></i> Unselect All
                            </button>
                        </div>

                    </div>

                    <!-- Routes List -->
                    <div class="border p-3 rounded" style="max-height: 500px; overflow-y: auto;">

                        @foreach ($allRoutes as $route)
                            <div class="form-check mb-2 route-item">
                                <input 
                                    type="checkbox" 
                                    name="routes[]" 
                                    value="{{ $route }}" 
                                    class="form-check-input route-checkbox"
                                    id="route_{{ $loop->index }}"
                                    @checked($group->routes->pluck('route_name')->contains($route))
                                >

                                <label class="form-check-label" for="route_{{ $loop->index }}">
                                    {{ $route }}
                                </label>
                            </div>
                        @endforeach

                    </div>

                    <!-- Save -->
                    <div class="mt-4 text-end">
                        <button class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Routes
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <!-- JS -->
    <script>
        // Live Search
        document.getElementById('searchRoute').addEventListener('keyup', function () {
            let value = this.value.toLowerCase();

            document.querySelectorAll('.route-item').forEach(item => {
                let text = item.innerText.toLowerCase();
                item.style.display = text.includes(value) ? '' : 'none';
            });
        });

        // Select All
        document.getElementById('checkAll').addEventListener('click', function () {
            document.querySelectorAll('.route-checkbox').forEach(cb => cb.checked = true);
        });

        // Unselect All
        document.getElementById('uncheckAll').addEventListener('click', function () {
            document.querySelectorAll('.route-checkbox').forEach(cb => cb.checked = false);
        });
    </script>

</x-app-layout>
