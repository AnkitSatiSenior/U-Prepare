<?php

namespace App\Services;

use App\Repositories\DashboardRepository;


use App\Models\PackageProject;
use App\Models\ActivityLog;

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
    public function getPackageMatrixReport($departmentId = null)
    {
        // 1. Fetch the packages you want to report on
        $query = PackageProject::query()
            ->select('id', 'package_name', 'estimated_budget_incl_gst', 'status')
            ->with('activityLogs'); // Eager load the logs to prevent N+1 queries

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $packages = $query->get();

        // 2. Map the data to include historical statuses
        return $packages->map(function ($package) {
            $achievedStatuses = [];

            // Always add the current status
            if ($package->status) {
                $achievedStatuses[] = $package->status;
            }

            // Loop through logs to find historical statuses
            foreach ($package->activityLogs as $log) {
                $changes = $log->changes;

                // Check the 'new' status string in the JSON payload
                if (isset($changes['new']['status'])) {
                    $achievedStatuses[] = $changes['new']['status'];
                }
                
                // Check the 'old' status just in case
                if (isset($changes['old']['status'])) {
                    $achievedStatuses[] = $changes['old']['status'];
                }
            }

            return [
                'id'              => $package->id,
                'package_name'    => $package->package_name,
                'estimated_value' => $package->estimated_budget_incl_gst,
                'current_status'  => $package->status,
                // Use array_unique to remove duplicates, and array_values to reset keys
                'history'         => array_values(array_unique($achievedStatuses)), 
            ];
        });
    }
}