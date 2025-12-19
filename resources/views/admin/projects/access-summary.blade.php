<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-project-diagram text-primary"
            title="Project Access Summary"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Projects'],
                ['label' => 'Access Summary']
            ]"
        />

        <!-- Alerts -->
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

        <!-- Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-info-circle me-2"></i> Project Details
                </h5>
            </div>

            <div class="card-body">

                <!-- Project Info -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-1">Package Project:</h6>
                    <p class="text-muted">{{ $data['package_project'] }}</p>

                    <h6 class="fw-bold mb-1">Sub Package Project:</h6>
                    <p class="text-muted">{{ $data['sub_package_project'] }}</p>

                    <h6 class="fw-bold mb-1">Assigned By:</h6>
                    <p class="text-muted">{{ $data['assigned_by'] }}</p>
                </div>

                <!-- Users Accordion -->
                <div class="custom-accordion" id="customAccordion">
                    @foreach ($data['assigned_users'] as $user)
                        <div class="acc-item">

                            <!-- HEADER -->
                            <div class="acc-header h4" onclick="toggleAcc({{ $loop->index }})">
                                <div>
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    {{ $user['user_name'] }}
                                    <span class="badge bg-primary text-white ms-2">
                                        {{ $user['role'] }}
                                    </span>
                                    <span class="badge bg-primary text-white ms-2">
                                        {{ $user['sub_department'] }}
                                    </span>
                                </div>
                                <i class="fas fa-chevron-right acc-icon"></i>
                            </div>

                            <!-- BODY -->
                            <div class="acc-body">

                                <div class="mb-3">
                                    <h6 class="fw-bold mb-2">Allowed Routes</h6>
                                    @if(count($user['routes']) > 0)
                                        <ul class="list-group mb-0">
                                            @foreach ($user['routes'] as $route)
                                                <li class="list-group-item">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    {{ $route }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">No routes assigned.</p>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold mb-2">Safeguard Permissions</h6>
                                    @if(count($user['safeguard_permissions']) > 0)
                                        @foreach ($user['safeguard_permissions'] as $sg)
                                            <div class="card mb-2">
                                                <div class="card-header bg-light fw-bold">
                                                    {{ $sg['name'] }}
                                                </div>
                                                <div class="card-body p-2">
                                                    <ul class="list-group mb-0">
                                                        @foreach ($sg['phases'] as $phase)
                                                            <li class="list-group-item">
                                                                <i class="fas fa-layer-group text-info me-2"></i>
                                                                {{ $phase }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No safeguard permissions.</p>
                                    @endif
                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
