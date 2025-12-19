<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs and Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-shield-alt me-2 text-success"></i>
                        Assign Routes to Role
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item">Admin</li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.role_routes.index') }}">Role Routes</a></li>
                            <li class="breadcrumb-item active">Assign</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the errors below.
                <ul>
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-success"><i class="fas fa-plus-circle me-2"></i> Assign Role Routes</h5>
                <a href="{{ route('admin.role_routes.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.role_routes.store') }}" method="POST">
                    @csrf

                    <!-- Select Role -->
                    <div class="mb-3">
                        <label for="role_id" class="form-label fw-bold">Select Role</label>
                        <select name="role_id" id="role_id" class="form-control" required {{ isset($selectedRole) ? 'readonly disabled' : '' }}>
                            <option value="">-- Choose Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ (isset($selectedRole) && $selectedRole->id == $role->id) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(isset($selectedRole))
                            <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">
                        @endif
                    </div>

                    <!-- Custom Accordion -->
                    <div class="custom-accordion">
                        @php
                            $groupedRoutes = [
                                'Index Routes' => [],
                                'Create Routes' => [],
                                'Edit Routes' => [],
                                'Delete Routes' => [],
                                'Show Routes' => [],
                                'Others' => [],
                            ];
                            foreach($routes as $route) {
                                if (Str::endsWith($route, 'index')) {
                                    $groupedRoutes['Index Routes'][] = $route;
                                } elseif (Str::endsWith($route, 'create') || Str::contains($route, '.create')) {
                                    $groupedRoutes['Create Routes'][] = $route;
                                } elseif (Str::endsWith($route, 'edit') || Str::contains($route, '.edit')) {
                                    $groupedRoutes['Edit Routes'][] = $route;
                                } elseif (Str::endsWith($route, 'destroy') || Str::contains($route, '.destroy') || Str::contains($route, 'delete')) {
                                    $groupedRoutes['Delete Routes'][] = $route;
                                } elseif (Str::endsWith($route, 'show') || Str::contains($route, '.show')) {
                                    $groupedRoutes['Show Routes'][] = $route;
                                } else {
                                    $groupedRoutes['Others'][] = $route;
                                }
                            }
                        @endphp

                        @foreach($groupedRoutes as $group => $groupRoutes)
                            <div class="accordion-item">
                                <div class="accordion-header" data-accordion-toggle>
                                    {{ $group }} ({{ count($groupRoutes) }})
                                    <span class="accordion-arrow">+</span>
                                </div>
                                <div class="accordion-body">
                                    @if(count($groupRoutes))
                                        <table class="table table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">
                                                        <input type="checkbox" class="select-all-group" data-group="{{ Str::slug($group) }}">
                                                    </th>
                                                    <th>Route Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($groupRoutes as $route)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox"
                                                                name="route_names[]"
                                                                value="{{ $route }}"
                                                                class="route-checkbox group-{{ Str::slug($group) }}"
                                                                {{ in_array($route, old('route_names', $selectedRoutes ?? [])) ? 'checked' : '' }}>
                                                        </td>
                                                        <td>{{ $route }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-muted">No routes found.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end border-top pt-3 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Assignments
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Accordion Script -->
    <style>
        .custom-accordion .accordion-item { border: 1px solid #ddd; border-radius: 6px; margin-bottom: 8px; }
        .custom-accordion .accordion-header { padding: 10px 15px; background: #f8f9fa; cursor: pointer; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
        .custom-accordion .accordion-header:hover { background: #e9ecef; }
        .custom-accordion .accordion-body { display: none; padding: 10px; background: #fff; }
        .custom-accordion .accordion-item.active .accordion-body { display: block; }
        .accordion-arrow { font-weight: bold; transition: transform 0.2s; }
        .accordion-item.active .accordion-arrow { transform: rotate(45deg); }
    </style>

    <script>
        document.querySelectorAll('[data-accordion-toggle]').forEach(header => {
            header.addEventListener('click', function() {
                const item = this.parentElement;
                item.classList.toggle('active');
            });
        });

        // Group select-all checkboxes
        document.querySelectorAll('.select-all-group').forEach(selectAll => {
            selectAll.addEventListener('change', function(e) {
                const group = e.target.dataset.group;
                document.querySelectorAll('.group-' + group).forEach(cb => cb.checked = e.target.checked);
            });
        });
    </script>
</x-app-layout>
