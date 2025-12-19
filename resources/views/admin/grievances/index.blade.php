<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-file-alt text-primary" title="Grievance Management" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['label' => 'Grievances'],
        ]" />

        <!-- Custom Styles -->
        <style>
            /* Base grievance card */
            .grievance-card {
                transition: all 0.3s ease-in-out;
                border-left-width: 5px !important;
                border-radius: 12px;
                overflow: hidden;
                cursor: pointer;
                background-color: #fff;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }

            .grievance-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
            }

            /* Variants */
            .grievance-card-primary {
                border-left-color: #0d6efd !important;
            }

            .grievance-card-warning {
                border-left-color: #ffc107 !important;
            }

            .grievance-card-success {
                border-left-color: #198754 !important;
            }

            .grievance-card-danger {
                border-left-color: #dc3545 !important;
            }

            /* Active state */
            .grievance-card.active {
                background-color: #f8f9fa !important;
                border-left-width: 8px !important;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.12);
            }

            .grievance-card h6 {
                color: #6c757d;
                font-weight: 500;
                letter-spacing: 0.3px;
            }

            .grievance-card h4 {
                font-weight: 700;
                color: #212529;
            }
        </style>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4 text-center">

            <div class="col-md-3">
                <a href="{{ route('admin.grievances.index', ['status' => 'total']) }}" class="text-decoration-none">
                    <div
                        class="grievance-card grievance-card-primary {{ request('status') == 'total' ? 'active' : '' }}">
                        <div class="card-body py-4">
                            <h6 class="text-muted mb-1 h3">Total Grievances</h6>
                            <h4>{{ $total }}</h4>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('admin.grievances.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div
                        class="grievance-card grievance-card-warning {{ request('status') == 'pending' || !request('status') ? 'active' : '' }}">
                        <div class="card-body py-4">
                            <h6 class="text-muted mb-1 h3">Pending Grievances</h6>
                            <h4>{{ $pending }}</h4>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('admin.grievances.index', ['status' => 'resolved']) }}" class="text-decoration-none">
                    <div
                        class="grievance-card grievance-card-success {{ request('status') == 'resolved' ? 'active' : '' }}">
                        <div class="card-body py-4">
                            <h6 class="text-muted mb-1 h3">Resolved Grievances</h6>
                            <h4>{{ $resolved }}</h4>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('admin.grievances.index', ['status' => 'rejected']) }}" class="text-decoration-none">
                    <div
                        class="grievance-card grievance-card-danger {{ request('status') == 'rejected' ? 'active' : '' }}">
                        <div class="card-body py-4">
                            <h6 class="text-muted mb-1 h3"> Rejected Grievances</h6>
                            <h4>{{ $rejected }}</h4>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.grievances.index') }}" class="row g-3">
                    <!-- Search -->
                    <div class="col-md-3">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search by name...">
                    </div>

                    <!-- District -->
                    <div class="col-md-2">
                        <select name="district" class="form-control">
                            <option value="">Select District</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district }}"
                                    {{ request('district') == $district ? 'selected' : '' }}>
                                    {{ $district }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Related To -->
                    <div class="col-md-2">
                        <select name="related_to" class="form-control">
                            <option value="">Related to</option>
                            @foreach ($relatedToOptions as $option)
                                <option value="{{ $option }}"
                                    {{ request('related_to') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">Select Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>

                    <!-- Year -->
                    <div class="col-md-1">
                        <select name="year" class="form-control">
                            <option value="">Year</option>
                            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="col-md-1">
                        <select name="month" class="form-control">
                            <option value="">Month</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('M') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Grievances DataTable -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h4">
                    <i class="fas fa-table me-2"></i> Grievances List
                </h5>
            </div>
            <div class="card-body">
                <x-admin.data-table id="grievances-table" :headers="[
                    '#',
                    'Grievance No.',
                    'Related To',
                    'Department',
                    'Status',
                    'Submitted On',
                    'Resolved At',
                    'Assigned To',
                    'Time Taken to Resolve',
                ]" :excel="true" :print="true"
                    title="Grievances Export" searchPlaceholder="Search grievances..." resourceName="grievances"
                    :pageLength="10">
                    @foreach ($grievances as $grievance)
                        <tr>
                            <!-- Index -->
                            <td>{{ $loop->iteration }}</td>

                            <!-- Grievance No -->
                            <td>
                                <a href="{{ route('admin.grievances.show', ['grievance_no' => $grievance->grievance_no]) }}"
                                    style="color: blue;">
                                    <strong>GR{{ $grievance->grievance_no }}</strong>
                                </a>
                            </td>

                            <!-- Related To -->
                            <td>{{ $grievance->grievance_related_to }}</td>

                            <!-- Department -->
                            <td>{{ $grievance->project ?? '—' }}</td>

                            <!-- Status -->
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'in-progress' => 'info',
                                        'resolved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$grievance->status] ?? 'secondary' }}">
                                    {{ ucfirst($grievance->status) }}
                                </span>
                            </td>

                            <!-- Submitted On -->
                            <td>{{ $grievance->created_at->format('d M, Y') }}</td>

                            <!-- Resolved At -->
                            <td>
                                @if ($grievance->status === 'resolved' && $grievance->updated_at)
                                    {{ $grievance->updated_at->format('d M, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($grievance->assignments->isNotEmpty())
                                    {{ $grievance->assignments->pluck('assignedUser.name')->join(', ') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <!-- 🕒 Days to Resolve -->
                            <td>
                                @if ($grievance->status === 'resolved' && $grievance->updated_at)
                                    @php
                                        $created = \Carbon\Carbon::parse($grievance->created_at);
                                        $updated = \Carbon\Carbon::parse($grievance->updated_at);

                                        // Get integer days (always whole number)
                                        $daysTaken = (int) $created->diffInDays($updated);

                                        // Badge color by duration
                                        if ($daysTaken <= 2) {
                                            $badgeColor = 'success';
                                        } elseif ($daysTaken <= 7) {
                                            $badgeColor = 'warning';
                                        } else {
                                            $badgeColor = 'danger';
                                        }
                                    @endphp

                                    <span class="badge bg-{{ $badgeColor }} text-white px-3 py-2 fs-6 shadow-sm">
                                        {{ $daysTaken }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white px-3 py-2 fs-6">—</span>
                                @endif
                            </td>


                        </tr>
                    @endforeach
                </x-admin.data-table>

            </div>
        </div>
    </div>
</x-app-layout>
