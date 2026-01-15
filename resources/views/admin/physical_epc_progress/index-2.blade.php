<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        @if ($subPackageProjectName)
            <x-admin.breadcrumb-header icon="fas fa-tasks text-primary"
                title="Physical EPC Progress Management Of ( <strong>{{ $subPackageProjectName }}</strong> )"
                :breadcrumbs="[
                    ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                    ['route' => 'admin.physical_epc_progress.index', 'label' => 'Physical EPC Progress'],
                    ['label' => 'Manage Progress Entries'],
                ]" />
        @endif

        <!-- Alerts -->
        @foreach (['success' => 'success', 'error' => 'danger'] as $type => $class)
            @if (session($type))
                <div class="row mb-3">
                    <div class="col-md-12">
                        <x-alert type="{{ $class }}" :message="session($type)" dismissible />
                    </div>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" dismissible>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                </div>
            </div>
        @endif
        <!-- Stage Summary (Target vs Achieved) -->
      @if ($targetByActivityStage->isNotEmpty())
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Stage Summary (Target vs Achieved)
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center mb-0">
                        <thead>
                            <tr>
                                <th>Activity Name</th>
                                <th>Stage</th>
                                <th>Target (%)</th>
                                <th>Achieved (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // 1. Initialize Total Variables
                                $totalTarget = 0;
                                $totalAchieved = 0;
                            @endphp

                            @foreach ($targetByActivityStage as $data)
                                @php
                                    $key = $data->activity_name . '|' . $data->stage_name;
                                    $achieved = $achievedByActivityStage[$key]->achieved_percent ?? 0;

                                    // 2. Accumulate Totals
                                    $totalTarget += $data->target_percent;
                                    $totalAchieved += $achieved;
                                @endphp
                                <tr>
                                    <td>{{ $data->activity_name }}</td>
                                    <td>{{ $data->stage_name }}</td>
                                    <td>{{ $data->target_percent }}%</td>
                                    <td>{{ $achieved }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                        <tfoot>
                            <tr class="bg-light font-weight-bold">
                                <td colspan="2" class="text-right">Total</td>
                                <td>{{ $totalTarget }}%</td>
                                <td>{{ $totalAchieved }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
    </div>
</x-app-layout>
