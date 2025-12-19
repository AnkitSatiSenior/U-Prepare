<?php

namespace App\Repositories;

use App\Models\{Department, PackageComponent, PackageProject, Contract, TypeOfProcurement, SubCategory};
use Illuminate\Support\Facades\Auth;

class DashboardRepository
{
    public function getTypeOfProcurementTableData($scope = 'all')
    {
        return TypeOfProcurement::with(['procurementDetails.packageProject.contracts'])
            ->when($scope !== 'all', fn($q) => $q->whereHas('procurementDetails.packageProject', fn($p) => $p->where('department_id', $scope)))
            ->get()
            ->map(
                fn($type) => [
                    'id' => $type->id,
                    'name' => $type->name,
                    'procurement_details_count' => $type->procurementDetails->count(),
                    'loa_issued_count' => $type->procurementDetails->filter(fn($d) => $d->packageProject?->contracts->whereNotNull('signing_date')->count())->count(),
                    'contract_pending_count' => $type->procurementDetails->filter(fn($d) => $d->packageProject?->contracts->whereNull('signing_date')->count())->count(),
                    'signed_contracts_count' => $type->procurementDetails->filter(fn($d) => $d->packageProject?->contracts->whereNotNull('signing_date')->count())->count(),
                    'commencement_given_count' => $type->procurementDetails->filter(fn($d) => $d->packageProject?->contracts->whereNotNull('commencement_date')->count())->count(),
                    'rebid_count' => $type->procurementDetails->filter(fn($d) => $d->packageProject?->status === PackageProject::STATUS_REBID)->count(),
                ],
            );
    }

    public function getSubCategoryProcurementTableData($scope = 'all')
    {
        return SubCategory::with(['projects.procurementDetail.packageProject.contracts', 'category'])
            ->when($scope !== 'all', fn($q) => $q->whereHas('projects', fn($p) => $p->where('department_id', $scope)))
            ->get()
            ->map(
                fn($subCat) => [
                    'id' => $subCat->id,
                    'name' => $subCat->name,
                    'category_name' => $subCat->category?->name ?? 'No Category',
                    'procurement_types' => $subCat->projects
                        ->groupBy(fn($p) => $p->procurementDetail?->typeOfProcurement?->id)
                        ->map(
                            fn($projects, $ptypeId) => [
                                'id' => $ptypeId,
                                'name' => $projects->first()->procurementDetail?->typeOfProcurement?->name ?? 'Pending For Procurement',
                                'count' => $projects->count(),
                                'loa_issued_count' => $projects->filter(fn($p) => $p->contracts->whereNotNull('signing_date')->count())->count(),
                                'contract_pending_count' => $projects->filter(fn($p) => $p->contracts->whereNull('signing_date')->count())->count(),
                                'signed_contracts_count' => $projects->filter(fn($p) => $p->contracts->whereNotNull('signing_date')->count())->count(),
                                'commencement_given_count' => $projects->filter(fn($p) => $p->contracts->whereNotNull('commencement_date')->count())->count(),
                                'rebid_count' => $projects->filter(fn($p) => $p->status === PackageProject::STATUS_REBID)->count(),
                            ],
                        )
                        ->values(),
                ],
            );
    }

    // 1. Departments Budget (Pie + Table)

    public function getDepartmentsBudget($scope = 'all')
    {
        $departments = Department::select('id', 'name', 'budget')->get();
        $totalBudget = $departments->sum(fn($d) => (float) ($d->budget ?? 0));

        if ($scope === 'all') {
            // Case: show all departments
            return [
                'rows' => $departments
                    ->map(
                        fn($dept) => [
                            [
                                'text' => $dept->name ?? 'N/A',
                                'url' => route('admin.package-projects.index', ['department_id' => $dept->id]),
                            ],
                            formatPriceToCR($dept->budget ?? 0),
                        ],
                    )
                    ->toArray(),
                'labels' => $departments->pluck('name')->filter()->values()->toArray(),
                'data' => $departments->pluck('budget')->map(fn($b) => (float) ($b ?? 0))->toArray(),
                'total' => $totalBudget,
            ];
        }

        // Case: only one department
        $dept = $departments->firstWhere('id', $scope);

        if (!$dept) {
            return ['rows' => [], 'labels' => [], 'data' => [], 'total' => $totalBudget];
        }

        $remainingBudget = $totalBudget - (float) $dept->budget;

        return [
            'rows' => [
                [
                    [
                        'text' => $dept->name ?? 'N/A',
                        'url' => route('admin.package-projects.index', ['department_id' => $dept->id]),
                    ],
                    formatPriceToCR($dept->budget ?? 0),
                ],
                [['text' => 'Remaining Departments', 'url' => '#'], formatPriceToCR($remainingBudget)],
            ],
            'labels' => [$dept->name, 'Remaining Departments'],
            'data' => [(float) $dept->budget, $remainingBudget],
            'total' => $totalBudget,
        ];
    }

    // 2. Package Components Budget (Pie + Table)
    public function getPackageComponentsBudget()
    {
        $components = PackageComponent::select('id', 'name', 'budget')->get();

        return [
            'rows' => $components
                ->map(
                    fn($c) => [
                        [
                            'text' => $c->name ?? 'N/A',
                            'url' => route('admin.package-projects.index', ['package_component_id' => $c->id]),
                        ],
                        formatPriceToCR($c->budget ?? 0),
                    ],
                )
                ->toArray(),
            'labels' => $components->pluck('name')->filter()->values()->toArray(),
            'data' => $components->pluck('budget')->map(fn($b) => (float) ($b ?? 0))->toArray(),
        ];
    }

    // 3. Department Contract Pie (for single department)
    public function getDepartmentContractPie($departmentId)
    {
        $dept = Department::with(['projects.contracts'])->findOrFail($departmentId);

        $used = $dept->projects->flatMap->contracts->sum('contract_value');
        $remaining = max(($dept->budget ?? 0) - $used, 0);

        return [
            'labels' => ['Contract Signed (CR)', 'Remaining Budget (CR)'],
            'data' => [round($used / 10000000, 2), round($remaining / 10000000, 2)],
        ];
    }

    // 4. Departments Financial Progress Pie
    public function getDepartmentsFinancialProgressPie()
    {
        return Department::with(['projects.subProjects'])
            ->get()
            ->map(function ($d) {
                $totalContract = $d->projects->sum('estimated_budget_incl_gst');
                $totalFinance = $d->projects->flatMap->subProjects->sum('total_finance_amount');

                return [
                    'name' => $d->name,
                    'value' => round($totalFinance / 10000000, 2), // CR
                    'percentage' => $totalContract > 0 ? round(($totalFinance / $totalContract) * 100, 2) : 0,
                ];
            });
    }

    // 5. Departments Physical Progress Pie
    public function getDepartmentsPhysicalProgressPie()
    {
        return Department::with(['projects.subProjects'])
            ->get()
            ->map(function ($d) {
                $avg = $d->projects->flatMap->subProjects->avg('physical_progress_percentage');

                return [
                    'name' => $d->name,
                    'value' => round($avg ?? 0, 2),
                ];
            });
    }

    // 6. Procurement Type Distribution Pie
    public function getProcurementTypeDistributionPie($scope = 'all')
    {
        $query = TypeOfProcurement::with(['procurementDetails.packageProject.contracts']);

        if ($scope !== 'all') {
            $query->whereHas('procurementDetails.packageProject', fn($q) => $q->where('department_id', $scope));
        }

        $types = $query->get();

        $rows = $types
            ->map(
                fn($type) => [
                    [
                        'text' => $type->name,
                        'url' => route('admin.package-projects.index', ['type_of_procurement_id' => $type->id]),
                    ],
                    $type->procurementDetails->count() ?? 0,
                ],
            )
            ->toArray();

        $labels = $types->pluck('name')->toArray();
        $data = $types->pluck('procurementDetails')->map(fn($d) => $d->count() ?? 0)->toArray();

        return compact('rows', 'labels', 'data');
    }

    public function getDepartmentsStats($scope = 'all')
    {
        $query = Department::with(['projects.contracts']);
        $allDepartments = $query->get();

        if ($scope === 'all') {
            return $allDepartments->map(function ($dept) {
                $dept->total_contract_value = $dept->projects->flatMap->contracts->sum('contract_value');
                return $dept;
            });
        }

        $department = $allDepartments->firstWhere('id', $scope);
        $deptTotalContract = $department?->projects->flatMap->contracts->sum('contract_value') ?? 0;

        return collect([
            (object) [
                'id' => $scope,
                'name' => $department?->name ?? 'Unknown',
                'budget' => $department?->budget ?? 0,
                'total_contract_value' => $deptTotalContract,
                'is_summary' => false,
            ],
            (object) [
                'id' => 'remaining',
                'name' => 'Remaining Departments',
                'budget' => $allDepartments->sum('budget') - ($department?->budget ?? 0),
                'total_contract_value' => $allDepartments->flatMap->projects->flatMap->contracts->sum('contract_value') - $deptTotalContract,
                'is_summary' => true,
            ],
        ]);
    }

    public function getDepartmentsPhysicalProgress($scope = 'all')
    {
        $query = Department::with(['projects.subProjects']);
        if ($scope !== 'all') {
            $query->where('id', $scope);
        }

        return $query->get()->map(function ($d) {
            $allSubProjects = $d->projects->flatMap->subProjects;
            return [
                'name' => $d->name,
                'avg_progress' => round($allSubProjects->avg('physical_progress_percentage') ?? 0, 2),
            ];
        });
    }

    public function getDepartmentsFinancialProgress($scope = 'all')
    {
        $query = \App\Models\Department::with([
            'projects.contracts' => fn($q) => $q->whereNull('deleted_at'), // skip soft-deleted contracts
            'projects.subProjects', // finance data
        ]);

        if ($scope !== 'all') {
            $query->where('id', $scope);
        }

        return $query->get()->map(function ($d) {
            $budget = (float) ($d->budget ?? 0);

            $totalContract = (float) $d->projects->flatMap->contracts->sum(fn($c) => (float) ($c->contract_value ?? 0));

            $totalFinance = (float) $d->projects->flatMap->subProjects->sum(fn($sp) => (float) ($sp->total_finance_amount ?? 0));

            $pendingFinance = max($totalContract - $totalFinance, 0);

            return [
                'id' => $d->id,
                'name' => $d->name,
                'budget' => $budget,
                'total_contract' => $totalContract,
                'total_finance' => $totalFinance,
                'pending_finance' => $pendingFinance,
                'contract_cr' => round($totalContract / 10000000, 2),
                'finance_cr' => round($totalFinance / 10000000, 2),
                'pending_cr' => round($pendingFinance / 10000000, 2),
                'finance_percentage' => $budget > 0 ? round(($totalFinance / $budget) * 100, 2) : 0,
            ];
        });
    }

    public function getComponents()
    {
        return PackageComponent::select('id', 'name', 'budget')->get();
    }

    public function getContracts($scope = 'all')
    {
        return Contract::withBasicRelations()
            ->whereNull('deleted_at')
            ->when($scope !== 'all', fn($q) => $q->whereHas('project', fn($p) => $p->where('department_id', $scope)))
            ->get();
    }

    public function getContractsStatus($scope = 'all')
    {
        $contracts = Contract::whereNull('deleted_at')
            ->when($scope !== 'all', fn($q) => $q->whereHas('project', fn($p) => $p->where('department_id', $scope)))
            ->get();

        $total = $contracts->count();

        return [
            'total' => $total,
            'signed' => $contracts->whereNotNull('signing_date')->count(),
            'commencement' => $contracts->whereNotNull('commencement_date')->count(),
            'pending' => $contracts->whereNull('signing_date')->count(),
            'rebid' => PackageProject::when($scope !== 'all', fn($q) => $q->where('department_id', $scope))
                ->where('status', PackageProject::STATUS_REBID)
                ->count(),
            'signed_percentage' => $total > 0 ? round(($contracts->whereNotNull('signing_date')->count() / $total) * 100, 2) : 0,
        ];
    }

    public function getTypeOfProcurementStats($scope = 'all')
    {
        return TypeOfProcurement::with(['procurementDetails.packageProject.contracts'])
            ->when($scope !== 'all', fn($q) => $q->whereHas('procurementDetails.packageProject', fn($p) => $p->where('department_id', $scope)))
            ->get()
            ->map(
                fn($type) => [
                    'id' => $type->id,
                    'name' => $type->name,
                    'procurement_details_count' => $type->procurementDetails->count(),
                    'loa_issued_count' => $type->procurementDetails->filter(fn($d) => $d->packageProject?->contracts->whereNotNull('signing_date')->count())->count(),
                ],
            );
    }

    public function getSubCategoryStats($scope = 'all')
    {
        return SubCategory::with('category:id,name')
            ->with(['projects.procurementDetail.typeOfProcurement', 'projects.contracts'])
            ->when($scope !== 'all', fn($q) => $q->whereHas('projects', fn($p) => $p->where('department_id', $scope)))
            ->get()
            ->map(
                fn($subCat) => [
                    'id' => $subCat->id,
                    'name' => $subCat->name,
                    'category_name' => $subCat->category?->name,
                    'total_projects' => $subCat->projects->count(),
                ],
            )
            ->filter(fn($subCat) => $subCat['total_projects'] > 0);
    }

    public function getPackageProjectsSubProjectStats($scope = 'all')
    {
        return PackageProject::with('subProjects')
            ->when($scope !== 'all', fn($q) => $q->where('department_id', $scope))
            ->get()
            ->map(
                fn($p) => [
                    'package_name' => $p->package_name,
                    'package_number' => $p->package_number,
                    'id' => $p->id,
                    'total_subprojects' => $p->subProjects->count(),
                    'avg_physical_progress' => round($p->subProjects->avg('physical_progress_percentage') ?? 0, 2),
                    'avg_financial_progress' => round($p->subProjects->avg('financial_progress_percentage') ?? 0, 2),
                ],
            );
    }

    public function getDepartmentCategoryCounts($scope = 'all')
    {
        $query = Department::with(['projects.category']);

        if ($scope !== 'all') {
            $query->where('id', $scope);
        }

        $departments = $query->get();

        // Collect all categories used
        $allCategories = $departments->flatMap(fn($d) => $d->projects->map(fn($p) => $p->category))->filter()->unique('id')->values();

        $result = $departments->map(function ($dept) use ($allCategories) {
            $counts = [];

            foreach ($allCategories as $cat) {
                $counts[$cat->id] = $dept->projects->where('package_category_id', $cat->id)->count();
            }

            return [
                'department_id' => $dept->id,
                'department_name' => $dept->name,
                'counts' => $counts,
            ];
        });

        return [
            'departments' => $result,
            'categories' => $allCategories,
        ];
    }

    public function getDepartmentCategorySubCategoryCounts($scope = 'all')
    {
        $query = Department::with(['projects.category', 'projects.subCategory', 'projects.subDepartment', 'projects.contracts']);

        if ($scope !== 'all') {
            $query->where('id', $scope);
        }

        $departments = $query->get();

        $result = $departments->map(function ($dept) {
            // --- Department-level categories ---
            $departmentCategories = $dept->projects
                ->groupBy('package_category_id')
                ->map(function ($catProjects, $catId) {
                    $categoryName = optional($catProjects->first()->category)->name ?? 'Unknown';

                    $subcategories = $catProjects
                        ->groupBy('package_sub_category_id')
                        ->map(function ($subProjects, $subId) {
                            $subName = optional($subProjects->first()->subCategory)->name ?? 'General';

                            return [
                                'sub_category_id' => $subId,
                                'sub_category_name' => $subName,
                                'physical_count' => $subProjects->count(),
                                'financial_total' => $subProjects->sum('estimated_budget_incl_gst'),
                                'work_order_count' => $subProjects->filter(fn($sp) => $sp->contracts->isNotEmpty())->count(),
                                'work_order_amount' => $subProjects->flatMap->contracts->sum('contract_value'),
                            ];
                        })
                        ->values();

                    return [
                        'category_id' => $catId,
                        'category_name' => $categoryName,
                        'total_physical' => $catProjects->count(),
                        'total_financial' => $catProjects->sum('estimated_budget_incl_gst'),
                        'total_work_orders' => $catProjects->whereNotNull('work_order_date')->count(),
                        'total_work_amount' => $catProjects->sum('work_order_amount'),
                        'subcategories' => $subcategories,
                    ];
                })
                ->values();

            // --- Sub-department-level categories ---
            $subDepartments = $dept->projects
                ->groupBy(fn($p) => $p->sub_department_id ?? 0)
                ->map(function ($subDeptProjects, $subDeptId) {
                    $subDeptName = optional($subDeptProjects->first()->subDepartment)->name ?? 'General';

                    $categories = $subDeptProjects
                        ->groupBy('package_category_id')
                        ->map(function ($catProjects, $catId) {
                            $categoryName = optional($catProjects->first()->category)->name ?? 'Unknown';

                            $subcategories = $catProjects
                                ->groupBy('package_sub_category_id')
                                ->map(function ($subProjects, $subId) {
                                    $subName = optional($subProjects->first()->subCategory)->name ?? 'General';

                                    return [
                                        'sub_category_id' => $subId,
                                        'sub_category_name' => $subName,
                                        'physical_count' => $subProjects->count(),
                                        'financial_total' => $subProjects->sum('estimated_budget_incl_gst'),
                                        'work_order_count' => $subProjects->filter(fn($sp) => $sp->contracts->isNotEmpty())->count(),
                                        'work_order_amount' => $subProjects->flatMap->contracts->sum('contract_value'),
                                    ];
                                })
                                ->values();

                            return [
                                'category_id' => $catId,
                                'category_name' => $categoryName,
                                'total_physical' => $catProjects->count(),
                                'total_financial' => $catProjects->sum('estimated_budget_incl_gst'),
                                'total_work_orders' => $catProjects->whereNotNull('work_order_date')->count(),
                                'total_work_amount' => $catProjects->sum('work_order_amount'),
                                'subcategories' => $subcategories,
                            ];
                        })
                        ->values();

                    return [
                        'sub_department_id' => $subDeptId,
                        'sub_department_name' => $subDeptName,
                        'categories' => $categories,
                    ];
                })
                ->values();

            return [
                'department_id' => $dept->id,
                'department_name' => $dept->name,
                'categories' => $departmentCategories,
                'subdepartments' => $subDepartments,
            ];
        });

        return $result;
    }
}
