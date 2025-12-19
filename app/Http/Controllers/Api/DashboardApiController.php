<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuthToken;
use App\Services\DashboardService;
use App\Models\SubPackageProject;
use App\Models\DashboardRole;

class DashboardApiController extends Controller
{
    protected $dashboard;
    protected $webDashboard;

    public function __construct(DashboardService $dashboard)
    {
        $this->dashboard = $dashboard;

        // same dashboard controller as Web
        $this->webDashboard = new \App\Http\Controllers\DashboardController($dashboard);
    }

    public function dashboard(Request $request)
    {
        // ============================================================
        // 1️⃣ AUTHENTICATION
        // ============================================================
        $rawToken = $request->bearerToken();

        if (!$rawToken) {
            return response()->json(['success' => false, 'message' => 'Authorization token missing'], 401);
        }

        $token = AuthToken::with('user')->where('token', $rawToken)->first();

        if (!$token || !$token->user) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $user = $token->user;


        // ============================================================
        // 2️⃣ LOAD MAIN DASHBOARD DATA (FROM SERVICE)
        // ============================================================
        $data = $this->dashboard->getDashboardData($user);


        // ============================================================
        // 3️⃣ ROLE-BASED DEPARTMENT ACCESS
        // ============================================================
        $roleAccess = DashboardRole::where('role_id', $user->role_id)->first();
        $scope = 'all';

        // Department 8 → always full access
        if ($user->department_id != 8) {
            if ($roleAccess && $roleAccess->department === 'department') {
                $scope = $user->department_id; // only own department
            }
        }

        $departmentsStats = $this->webDashboard->getDepartmentsStatsOther($scope);


        // ============================================================
        // 4️⃣ DEPARTMENT CONTRACT OVERVIEW
        // ============================================================
        $departmentOverview = $departmentsStats->map(function ($d) {

            $projects = $d->projects_count ?? 0;
            $contracts = $d->signed_contracts_count ?? 0;

            $budget = floatval($d->budget ?? 0);
            $signed = floatval($d->total_contract_value ?? 0);
            $remaining = max($budget - $signed, 0);

            return [
                'department_id' => $d->id,
                'department' => $d->name,
                'total_projects' => $projects,
                'total_contracts_signed' => $contracts,

                // Convert to Crores
                'total_amount_allocated_cr' => round($budget / 10000000, 2),
                'contract_signed_cr'       => round($signed / 10000000, 2),
                'contract_to_be_signed_cr' => round($remaining / 10000000, 2),
            ];
        });


        // ============================================================
        // 5️⃣ PROCUREMENT SUMMARY
        // ============================================================
        $typeOfProcurement = collect($data['typeOfProcurement'] ?? [])->map(function ($type) {
            return [
                'id' => $type['id'],
                'name' => $type['name'],
                'procurement_details_count' => $type['procurement_details_count'],
                'loa_issued_count' => $type['loa_issued_count'],
            ];
        });

        $procurementPie = [
            'rows' => $typeOfProcurement->map(function ($t) {
                return [
                    [
                        'text' => $t['name'],
                        'url' => url("/admin/package-projects?type_of_procurement_id={$t['id']}")
                    ],
                    $t['procurement_details_count']
                ];
            }),
            'labels' => $typeOfProcurement->pluck('name'),
            'data'   => $typeOfProcurement->pluck('procurement_details_count'),
        ];


        // ============================================================
        // 6️⃣ PHYSICAL PROGRESS
        // ============================================================
        $departmentsPhysicalProgress = collect($data['departmentsPhysicalProgress'] ?? [])->map(function ($d) {
            return [
                'name' => $d['name'],
                'avg_progress' => round(floatval($d['avg_progress']), 2),
            ];
        });


        // ============================================================
        // 7️⃣ FINANCIAL PROGRESS
        // ============================================================
        $departmentsFinancialProgress = collect($data['departmentsFinancialProgress'] ?? [])->map(function ($d) {
            return [
                'id' => $d['id'],
                'name' => $d['name'],
                'budget' => $d['budget'],
                'total_contract' => $d['total_contract'],
                'total_finance' => $d['total_finance'],
                'pending_finance' => $d['pending_finance'],
                'contract_cr' => $d['contract_cr'],
                'finance_cr' => $d['finance_cr'],
                'pending_cr' => $d['pending_cr'],
                'finance_percentage' => $d['finance_percentage'],
            ];
        });


        // ============================================================
        // 8️⃣ SUB-PROJECTS SUMMARY
        // ============================================================
        $filters = $request->only([
            'department_id',
            'sub_department_id',
            'district_id',
            'package_component_id'
        ]);

         // --------------------- Sub-Projects Report ---------------------
        $filters = $request->only(['department_id', 'sub_department_id', 'district_id', 'package_component_id']);

        $subProjects = SubPackageProject::with(['packageProject', 'contract.project.procurementDetail.typeOfProcurement', 'contract.contractor', 'boqEntries.physicalBoqProgresses', 'epcEntries.physicalEpcProgresses'])
            ->whereHas('packageProject', fn($q) => $q->applyFilters($filters))
            ->get();

        $subProjectsData = $subProjects->map(function ($sp) {
            $rawSafeguards = $sp->socialSafeguardProgress();

            $safeguards = collect($rawSafeguards)
                ->map(function ($val, $cid) {
                    return [
                        'id' => (int) $cid,
                        'compliance' => $val['compliance'] ?? ($val['name'] ?? null),
                        'phases' => collect($val['phases'] ?? [])
                            ->map(
                                fn($ph) => [
                                    'id' => $ph['id'] ?? null,
                                    'phase' => $ph['phase'] ?? ($ph['name'] ?? null),
                                    'percent' => isset($ph['percent']) ? (float) $ph['percent'] : 0.0,
                                ],
                            )
                            ->values()
                            ->toArray(),
                    ];
                })
                ->values()
                ->toArray();

            return [
                'id' => $sp->id,
                'contract_id' => $sp->contract->id ?? null,
                'name' => $sp->name,
                'package_number' => $sp->packageProject->package_number ?? 'N/A',
                'contract_value' => floatval($sp->contract_value ?? 0),
                'finance_percent' => method_exists($sp, 'getFinancialProgressPercentageAttribute') ? $sp->getFinancialProgressPercentageAttribute() : (method_exists($sp, 'financialProgressPercentage') ? $sp->financialProgressPercentage() : 0),
                'physical_percent' => method_exists($sp, 'getPhysicalProgressPercentageAttribute') ? $sp->getPhysicalProgressPercentageAttribute() : (method_exists($sp, 'physicalProgressPercentage') ? $sp->physicalProgressPercentage() : 0),
                'safeguards' => $safeguards,
            ];
        });

        // Count of sub-projects
        $subProjectsCount = $subProjectsData->count();

        $compliancePhaseHeaders = collect($subProjectsData)->flatMap(fn($sp) => collect($sp['safeguards'])->flatMap(fn($sg) => collect($sg['phases'])->pluck('phase')->map(fn($ph) => ($sg['compliance'] ?? '') . ' – ' . $ph)))->unique()->values();



        // ============================================================
        // 9️⃣ FINAL RESPONSE
        // ============================================================
        return response()->json([
            'success' => true,
            'message' => 'Dashboard summary loaded',
            'data' => [
                'department_contract_overview'      => $departmentOverview,
                'type_of_contracts_distribution'    => $procurementPie,
                'department_wise_physical_progress' => $departmentsPhysicalProgress,
                'department_wise_financial_progress'=> $departmentsFinancialProgress,
                'sub_projects_count'                => $subProjectsCount,
                'sub_projects'                      => $subProjectsData,
            ],
        ]);
    }
}
