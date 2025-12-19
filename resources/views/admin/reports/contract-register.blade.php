<x-app-layout>
    <div class="container-fluid py-4">
        <x-admin.breadcrumb-header icon="fas fa-file-contract text-primary" title="Contract Register" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['label' => 'Admin'],
            ['label' => 'Reports', 'route' => 'admin.reports.index'],
            ['label' => 'Contract Register'],
        ]" />

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h6 class="text-secondary h2 mb-0">
                    Contracts ({{ $contractsData->count() }})
                </h6>
            </div>

            <div class="card-body">
                <x-admin.data-table id="contract-register-table" :headers="array_merge(
                    [
                        '#',
                        'Package No.',
                        'Contract No.',
                        'Commencement Date',
                        'Completion Date',
                        'Contract Value (₹)',
                        'Contractor',
                        'Department',
                        'Name',
                        'Finance Progress',
                        'Physical Progress',
                    ],
                    $compliancePhaseHeaders->toArray(),
                )" :excel="true" :print="true"
                    :pageLength="10" :resourceName="'contracts'">

                    @foreach ($contractsData as $i => $sp)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $sp['package_number'] }}</td>
                            <td>{{ $sp['contract_number'] }}</td>
                            <td>{{ $sp['commencement_date'] }}</td>
                            <td>{{ $sp['completion_date'] }}</td>
                            <td class="text-end">{{ formatPriceToCR($sp['contract_value']) }}</td>
                            <td>{{ $sp['contractor'] }}</td>
                            <td>{{ $sp['department'] }}</td>
                            <td style="max-width:150px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                                title="{{ $sp['name'] }}">
                                {{ $sp['name'] }}
                            </td>

                            <td
                                class="{{ $sp['finance_percent'] > 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                {{ $sp['finance_percent'] }}%
                            </td>
                            <td
                                class="{{ $sp['physical_percent'] > 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                {{ $sp['physical_percent'] }}%
                            </td>

                            @foreach ($compliancePhaseHeaders as $header)
                                @php
                                    [$compName, $phaseName] = explode(' – ', $header, 2);
                                    $comp = collect($sp['safeguards'])->firstWhere('compliance', $compName);
                                    $phase = $comp ? collect($comp['phases'])->firstWhere('phase', $phaseName) : null;
                                    $percent = $phase['percent'] ?? null;
                                @endphp
                                <td
                                    class="text-center {{ $percent > 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                    {{ $percent !== null ? $percent . '%' : '0%' }}

                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end">
                                <strong>
                                    {{ formatPriceToCR($contractsData->sum('contract_value')) }}
                                </strong>
                            </td>
                            <td colspan="{{ 4 + $compliancePhaseHeaders->count() }}"></td>
                        </tr>
                    </tfoot>
                </x-admin.data-table>

            </div>
        </div>
    </div>
</x-app-layout>
