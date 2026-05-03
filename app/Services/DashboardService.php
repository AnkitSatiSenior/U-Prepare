<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Models\PackageProject;
use App\Models\PackageStatus;
use Illuminate\Support\Collection;

class DashboardService
{
    protected $repo;

    public function __construct(DashboardRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Decide the scope based on user role/department
     */
    public function resolveScope($user)
    {
        if ($user->department_id == 8) {
            return 'all'; // super department
        }

        if ($user->department_id) {
            return $user->department_id; // always scope to user’s department
        }

        return 'all'; // fallback
    }

    /**
     * Get complete dashboard data for given scope
     */
    public function getDashboardData($user)
    {
        $scope = $this->resolveScope($user);
        $departmentStats = $this->getDepartmentOverviewStats($scope);
        $departmentsPhysicalProgress = $this->repo->getDepartmentsPhysicalProgress($scope);
        $departmentsFinancialProgress = $this->repo->getDepartmentsFinancialProgress($scope);

        return [
            // General Stats
            'departments' => $this->repo->getDepartmentsStats($scope),
            'departmentStats' => $departmentStats,
            'departmentsPhysicalProgress' => $departmentsPhysicalProgress,
            'departmentsFinancialProgress' => $departmentsFinancialProgress,
            'components' => $this->repo->getComponents(),
            'contracts' => $this->repo->getContracts($scope),
            'contractsStatus' => $this->repo->getContractsStatus($scope),
            'typeOfProcurement' => $this->repo->getTypeOfProcurementStats($scope),
            'subCategories' => $this->repo->getSubCategoryStats($scope),
            'packageProjectsSubProjectStats' => $this->repo->getPackageProjectsSubProjectStats($scope),

            // Category Counts
            'departmentCategoryCounts' => $this->repo->getDepartmentCategoryCounts($scope),
            'departmentCategorySubCategoryCounts' => $this->repo->getDepartmentCategorySubCategoryCounts($scope),

            // Package Status Stats (New Addition)
            'packageStatusStats' => $this->repo->getPackageStatusStats($scope),

            // Budget Data
            'departmentsBudget' => $this->repo->getDepartmentsBudget($scope),
            'componentsBudget' => $this->repo->getPackageComponentsBudget($scope),

            // Pie Charts
            'financialProgressPie' => $this->repo->getDepartmentsFinancialProgressPie($scope),
            'physicalProgressPie' => $this->repo->getDepartmentsPhysicalProgressPie($scope),
            'procurementPie' => $this->repo->getProcurementTypeDistributionPie($scope),

            // Tables
            'typeOfProcurementTable' => $this->repo->getTypeOfProcurementTableData($scope),
            'subCategoryProcurementTable' => $this->repo->getSubCategoryProcurementTableData($scope),
            'departmentContractOverview' => $this->buildDepartmentContractOverview($departmentStats, $scope),
            'departmentsPhysicalChart' => $this->buildPhysicalProgressChart($departmentsPhysicalProgress),
            'departmentsFinancialChart' => $this->buildFinancialProgressChart($departmentsFinancialProgress, $scope),

            'scope' => $scope,
        ];
    }

    public function getDepartmentOverviewStats(string|int $scope = 'all'): Collection
    {
        return $this->repo->getDepartmentOverviewStats($scope);
    }

    public function getPackageMatrixReport($user): array
    {
        $scope = $this->resolveScope($user);

        $activeStatuses = PackageStatus::where('is_active', true)
            ->orderBy('order_by')
            ->pluck('name')
            ->toArray();

        $query = PackageProject::query()
            ->select('id', 'package_name', 'estimated_budget_incl_gst', 'status', 'department_id')
            ->with(['department:id,name', 'activityLogs'])
            ->when($scope !== 'all', fn($q) => $q->where('department_id', $scope));

        $packages = $query->get();

        $mappedPackages = $packages->map(function ($package) {
            $achievedStatuses = [];

            if (!empty($package->status)) {
                $achievedStatuses[] = $package->status;
            }

            if ($package->activityLogs) {
                foreach ($package->activityLogs as $log) {
                    $changes = is_string($log->changes) ? json_decode($log->changes, true) : $log->changes;

                    if (is_array($changes)) {
                        $newStatus = $changes['new']['status'] ?? $changes['attributes']['status'] ?? null;
                        if ($newStatus) $achievedStatuses[] = $newStatus;

                        $oldStatus = $changes['old']['status'] ?? null;
                        if ($oldStatus) $achievedStatuses[] = $oldStatus;
                    }
                }
            }

            return [
                'id'              => $package->id,
                'package_name'    => $package->package_name,
                'department_name' => $package->department->name ?? 'N/A',
                'estimated_value' => (float) ($package->estimated_budget_incl_gst ?? 0),
                'current_status'  => $package->status ?? 'N/A',
                'history'         => array_values(array_unique(array_filter($achievedStatuses))),
            ];
        });

        return [
            'statuses' => $activeStatuses,
            'packages' => $mappedPackages,
        ];
    }

    private function buildDepartmentContractOverview(Collection $departmentStats, string|int $scope): array
    {
        if ($departmentStats->isEmpty()) {
            return [
                'headers' => [
                    'Department',
                    'Total No. of Projects',
                    'Total No. of Contracts Signed',
                    'Total Amount Allocated',
                    'Contract Signed Value',
                    'Contract to be Signed',
                ],
                'rows' => [],
                'labels' => [],
                'data' => [],
                'datasets' => [],
            ];
        }

        $headers = [
            'Department',
            'Total No. of Projects',
            'Total No. of Contracts Signed',
            'Total Amount Allocated',
            'Contract Signed Value',
            'Contract to be Signed',
        ];

        if ($scope === 'all') {
            return [
                'headers' => $headers,
                'rows' => $departmentStats->map(function ($department) {
                    $budget = (float) ($department->budget ?? 0);
                    $contractValue = (float) ($department->total_contract_value ?? 0);
                    $remaining = max($budget - $contractValue, 0);
                    $signedPercentage = $budget > 0 ? round(($contractValue / $budget) * 100, 2) : 0;
                    $remainingPercentage = $budget > 0 ? round(($remaining / $budget) * 100, 2) : 0;

                    return [
                        [
                            'text' => $department->name,
                            'url' => route('admin.package-projects.index', [
                                'department_id' => $department->id,
                                'has_contract' => 1,
                            ]),
                        ],
                        $department->projects_count ?? 0,
                        $department->signed_contracts_count ?? 0,
                        formatPriceToCR($budget),
                        formatPriceToCR($contractValue) . " ({$signedPercentage}%)",
                        formatPriceToCR($remaining) . " ({$remainingPercentage}%)",
                    ];
                })->toArray(),
                'labels' => $departmentStats->pluck('name')->toArray(),
                'data' => [],
                'datasets' => [[
                    'label' => 'Total Contract Value (CR)',
                    'data' => $departmentStats
                        ->map(fn ($department) => round(((float) ($department->total_contract_value ?? 0)) / 10000000, 2))
                        ->toArray(),
                ]],
            ];
        }

        $department = $departmentStats->first();
        $budget = (float) ($department->budget ?? 0);
        $contractValue = (float) ($department->total_contract_value ?? 0);
        $remaining = max($budget - $contractValue, 0);
        $signedPercentage = $budget > 0 ? round(($contractValue / $budget) * 100, 2) : 0;
        $remainingPercentage = $budget > 0 ? round(($remaining / $budget) * 100, 2) : 0;

        return [
            'headers' => $headers,
            'rows' => [[
                [
                    'text' => $department->name,
                    'url' => route('admin.package-projects.index', [
                        'department_id' => $department->id,
                        'has_contract' => 1,
                    ]),
                ],
                $department->projects_count ?? 0,
                $department->signed_contracts_count ?? 0,
                formatPriceToCR($budget),
                formatPriceToCR($contractValue) . " ({$signedPercentage}%)",
                formatPriceToCR($remaining) . " ({$remainingPercentage}%)",
            ]],
            'labels' => ['Contract Signed (CR)', 'Contract to be Signed (CR)'],
            'data' => [
                round($contractValue / 10000000, 2),
                round($remaining / 10000000, 2),
            ],
            'datasets' => [],
        ];
    }

    private function buildPhysicalProgressChart(Collection $departmentsPhysicalProgress): array
    {
        return [
            'headers' => ['Department', 'Avg Physical Progress %'],
            'rows' => $departmentsPhysicalProgress
                ->map(fn ($department) => [$department['name'], ($department['avg_progress'] ?? 0) . '%'])
                ->toArray(),
            'labels' => $departmentsPhysicalProgress->pluck('name')->toArray(),
            'data' => $departmentsPhysicalProgress->pluck('avg_progress')->toArray(),
        ];
    }

    private function buildFinancialProgressChart(Collection $departmentsFinancialProgress, string|int $scope): array
    {
        if ($scope === 'all') {
            return [
                'headers' => ['Department', 'Finance Progress', 'Finance %'],
                'rows' => $departmentsFinancialProgress
                    ->map(fn ($department) => [
                        $department['name'],
                        formatPriceToCR($department['total_finance'] ?? 0),
                        ($department['finance_percentage'] ?? 0) . '%',
                    ])
                    ->toArray(),
                'labels' => $departmentsFinancialProgress->pluck('name')->toArray(),
                'data' => $departmentsFinancialProgress
                    ->map(fn ($department) => $department['finance_cr'] ?? 0)
                    ->toArray(),
            ];
        }

        $department = $departmentsFinancialProgress->first();

        return [
            'headers' => ['Department', 'Budget', 'Contract Signed', 'Financial Expenditure', 'Finance Pending', 'Finance %'],
            'rows' => $departmentsFinancialProgress
                ->map(fn ($item) => [
                    $item['name'],
                    formatPriceToCR($item['budget'] ?? 0),
                    formatPriceToCR($item['total_contract'] ?? 0),
                    formatPriceToCR($item['total_finance'] ?? 0),
                    formatPriceToCR($item['pending_finance'] ?? 0),
                    ($item['finance_percentage'] ?? 0) . '%',
                ])
                ->toArray(),
            'labels' => ['Financial Expenditure (CR)', 'Finance Pending (CR)'],
            'data' => [
                $department['finance_cr'] ?? 0,
                $department['pending_cr'] ?? 0,
            ],
        ];
    }
}
