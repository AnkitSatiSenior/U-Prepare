<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Models\PackageProject;
use App\Models\PackageStatus;

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

        return [
            // General Stats
            'departments' => $this->repo->getDepartmentsStats($scope),
            'departmentsPhysicalProgress' => $this->repo->getDepartmentsPhysicalProgress($scope),
            'departmentsFinancialProgress' => $this->repo->getDepartmentsFinancialProgress($scope),
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

            'scope' => $scope,
        ];
    }
    public function getPackageMatrixReport($user): array
    {
        // 1. Resolve Scope
        $scope = $this->resolveScope($user);

        // 2. Fetch all ACTIVE dynamic statuses, ordered correctly
        $activeStatuses = PackageStatus::where('is_active', true)
            ->orderBy('order_by')
            ->pluck('name')
            ->toArray();

        // 3. Build the query
        $query = PackageProject::query()
            ->select('id', 'package_name', 'estimated_budget_incl_gst', 'status', 'department_id')
            ->with(['department:id,name', 'activityLogs'])
            ->when($scope !== 'all', fn($q) => $q->where('department_id', $scope));

        $packages = $query->get();

        // 4. Map the data
        $mappedPackages = $packages->map(function ($package) {
            $achievedStatuses = [];

            // Add current status
            if (!empty($package->status)) {
                $achievedStatuses[] = $package->status;
            }

            // Extract historical statuses from logs safely
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
                'estimated_value' => $package->estimated_budget_incl_gst,
                'current_status'  => $package->status,
                'history'         => array_values(array_unique(array_filter($achievedStatuses))),
            ];
        });

        // Return a structured array with both the Columns (Statuses) and the Rows (Packages)
        return [
            'statuses' => $activeStatuses,
            'packages' => $mappedPackages,
        ];
    }
}
