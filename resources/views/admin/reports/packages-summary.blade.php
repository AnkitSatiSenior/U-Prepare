<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" title="Packages Summary Report"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Reports'],
                ['label' => 'Packages Summary'],
            ]" />

        <!-- Summary Cards -->
        <div class="row mb-4">

            <!-- Total -->
            <x-admin.report-card color="primary" title="Total Packages" :count="$summary['total_packages']" :route="route('admin.reports.packages-summary', ['filter' => 'all'])" />

            <!-- Work Program -->
            <x-admin.report-card color="success" title="With Work Program" :count="$summary['with_workprogram']" :route="route('admin.reports.packages-summary', ['filter' => 'workprogram'])" />
            <x-admin.report-card color="danger" title="Without Work Program" :count="$summary['without_workprogram']" :route="route('admin.reports.packages-summary', ['filter' => 'no_workprogram'])" />

            <!-- Procurement -->
            <x-admin.report-card color="warning" title="With Procurement" :count="$summary['with_procurement']" :route="route('admin.reports.packages-summary', ['filter' => 'procurement'])" />
            <x-admin.report-card color="secondary" title="Without Procurement" :count="$summary['without_procurement']" :route="route('admin.reports.packages-summary', ['filter' => 'no_procurement'])" />

            <!-- Contracts -->
            <x-admin.report-card color="info" title="With Contracts" :count="$summary['with_contracts']" :route="route('admin.reports.packages-summary', ['filter' => 'contracts'])" />

            <!-- Entry Data -->
            <x-admin.report-card color="success" title="With Entry Data" :count="$summary['with_entry']" :route="route('admin.reports.packages-summary', ['filter' => 'entry'])" />

            <!-- Physical Progress -->
            <x-admin.report-card color="success" title="With Physical Progress" :count="$summary['with_physical']" :route="route('admin.reports.packages-summary', ['filter' => 'physical'])" />
        </div>


        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Packages Summary Report
                </h5>
            </div>

            <div class="card-body">
                <x-admin.data-table id="packages-summary-table" :headers="[
                    '#',
                    'Package Number',
                    'Package Name',
                    'Status',
                    'Sub-Packages',
                    'Work Program',
                    'Procurement',
                    'Contracts',
                    'Entry Data (EPC/BOQ)',
                ]" :excel="true" :print="true"
                    resourceName="Reports" :pageLength="10">
                    @foreach ($data as $i => $pkg)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $pkg['package_number'] }}</td>
                            <td>{{ $pkg['package_name'] }}</td>

                            <!-- Status -->
                            <td
                                class="
                                @if (empty($pkg['status']) || $pkg['status'] === 'N/A') bg-danger text-white
                                @else bg-success text-white @endif
                            ">
                                {{ $pkg['status'] ?? 'N/A' }}
                            </td>

                            <!-- Sub-Packages -->
                            <td
                                class="
                                @if (empty($pkg['sub_projects']) || $pkg['sub_projects'] == 0 || $pkg['sub_projects'] === 'N/A') bg-danger text-white
                                @else bg-success text-white @endif
                            ">
                                {{ $pkg['sub_projects'] }}
                            </td>

                            <!-- Work Program -->
                            <td
                                class="
                                @if ($pkg['has_workprogram'] === 'Yes') bg-success text-white
                                @else bg-danger text-white @endif
                            ">
                                {{ $pkg['has_workprogram'] }}
                            </td>

                            <!-- Procurement -->
                            <td
                                class="
                                @if ($pkg['procurement'] === 'Not Done' || $pkg['procurement'] === 'N/A') bg-danger text-white
                                @else bg-success text-white @endif
                            ">
                                {{ $pkg['procurement'] }}
                            </td>

                            <!-- Contracts -->
                            <td
                                class="
                                @if ($pkg['has_contract'] === 'Yes') bg-success text-white
                                @else bg-danger text-white @endif
                            ">
                                {{ $pkg['has_contract'] }}
                            </td>
                            <!-- Entry Data -->
                            <td
                                class="{{ $pkg['has_entry_data'] === 'Yes' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                {{ $pkg['has_entry_data'] }}
                            </td>



                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
