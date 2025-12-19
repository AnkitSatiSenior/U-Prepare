<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PackageProject;
use App\Models\SubPackageProject;
use App\Models\WorkProgressData;
use App\Models\ContractionPhase;
use App\Models\Contract;
use App\Models\AlreadyDefinedWorkProgress;
use App\Models\PhysicalBoqProgress;
use App\Models\SubDepartment;
use App\Models\GeographyDistrict;
use App\Helpers\MilestoneHelper;
use App\Services\ReportService;
use App\Models\FinancialProgressUpdate;
use App\Models\SafeguardCompliance;
use App\Models\SafeguardEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }
   public function index(Request $request)
{
    // Filters
    $filters = $request->only(['department_id', 'sub_department_id', 'district_id']);

    $departments = Department::query()
        ->withProjectAndContractStats()
        ->withFinancialStats()
        ->with([
            'projects' => function ($q) use ($filters) {
                $q->select(
                        'id',
                        'package_category_id',
                        'package_sub_category_id',
                        'district_id',
                        'estimated_budget_incl_gst',
                        'department_id',
                        'sub_department_id',
                        'package_name',
                        'package_number',
                        'dec_approved'
                    )
                    ->with([
                        'contracts.contractor:id,company_name',
                        'contracts:id,project_id,contract_value,contract_number,commencement_date',
                        'procurementDetail',
                        'category',
                        'subCategory',
                        'district',
                        'subProjects:id,project_id,name,contract_value',
                        'subProjects.financialProgressUpdates:id,project_id,finance_amount'
                    ])
                    ->when($filters['department_id'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
                    ->when($filters['sub_department_id'] ?? null, fn ($q, $v) => $q->where('sub_department_id', $v))
                    ->when($filters['district_id'] ?? null, fn ($q, $v) => $q->where('district_id', $v));
            }
        ])
        ->get();

    // Flatten projects
    $packageProjects = $departments->flatMap(fn ($d) => $d->projects)->unique('id')->values();

    // Stats
    $stats = [
        'total_departments' => $departments->count(),
        'total_projects' => $departments->sum('projects_count'),
        'signed_contracts' => $departments->sum('signed_contracts_count'),
        'total_contract_value' => $departments->sum('contracts_sum_contract_value'),
        'total_finance' => $departments->sum('financials_sum_finance_amount'),
    ];

    // Dropdowns
    $departmentsList = Department::orderBy('name')->get(['id', 'name']);
    $subDepartments  = SubDepartment::orderBy('name')->get(['id', 'name']);
    $districts       = GeographyDistrict::orderBy('name')->get(['id', 'name']);

    return view(
        'admin.reports.index',
        compact(
            'departments',
            'departmentsList',
            'subDepartments',
            'districts',
            'stats',
            'packageProjects'
        )
    );
}


    public function subPackageProjectsSummaryReport(Request $request)
    {
        $filters = $request->only(['department_id', 'package_project_id']);

        // Departments and package projects for filters
        $departments = Department::orderBy('name')->get(['id', 'name']);
        $packageProjects = PackageProject::orderBy('package_name')->get(['id', 'package_number', 'package_name']);

        // Fetch sub-projects with relationships
        $subProjectsQuery = SubPackageProject::with(['packageProject:id,package_number,package_name,department_id', 'packageProject.department:id,name', 'contract:id,project_id,contract_number,contract_value,contractor_id', 'contract.contractor:id,company_name', 'financialProgressUpdates', 'epcEntries.physicalEpcProgresses', 'boqEntries.physicalBoqProgresses', 'packageProject.procurementDetail.typeOfProcurement']);

        // Apply filters
        if (!empty($filters['department_id'])) {
            $subProjectsQuery->whereHas('packageProject', fn($q) => $q->where('department_id', $filters['department_id']));
        }
        if (!empty($filters['package_project_id'])) {
            $subProjectsQuery->where('project_id', $filters['package_project_id']);
        }

        $subProjects = $subProjectsQuery->orderBy('name')->get();

        // Group sub-projects by package project to calculate avg progress
        $processed = $subProjects
            ->groupBy('project_id')
            ->map(function ($subs) {
                $project = $subs->first()->packageProject;

                // Avg Financial Progress
                $avgFinancial = $subs
                    ->map(function ($sp) {
                        $contractValue = $sp->contract->contract_value ?? 0;
                        $financeTotal = $sp->financialProgressUpdates->sum('finance_amount');
                        return $contractValue > 0 ? ($financeTotal / $contractValue) * 100 : 0;
                    })
                    ->avg();

                // Avg Physical Progress
                $avgPhysical = $subs
                    ->map(function ($sp) {
                        $procurementType = strtolower(optional($sp->packageProject->procurementDetail)->typeOfProcurement->name ?? 'epc');
                        $contractValue = $sp->contract->contract_value ?? 0;

                        if ($contractValue === 0) {
                            return 0;
                        }

                        if ($procurementType === 'epc') {
                            $epcPlanned = $sp->epcEntries->sum('amount');
                            $epcExecuted = $sp->physicalEpcProgresses->sum('amount');
                            return $epcPlanned > 0 ? ($epcExecuted / $epcPlanned) * 100 : 0;
                        } else {
                            // BOQ
                            $boqPlanned = $sp->boqEntries->sum('qty');
                            $boqExecuted = $sp->physicalBoqProgresses->sum('qty');
                            return $boqPlanned > 0 ? ($boqExecuted / $boqPlanned) * 100 : 0;
                        }
                    })
                    ->avg();

                // Format sub-projects
                $subProjectsList = $subs
                    ->map(
                        fn($sp) => [
                            'id' => $sp->id,
                            'name' => $sp->name,
                            'contract_number' => optional($sp->contract)->contract_number,
                            'contract_value' => $sp->contract->contract_value ?? 0,
                        ],
                    )
                    ->values();

                return [
                    'package_project_id' => $project->id,
                    'package_number' => $project->package_number,
                    'package_name' => $project->package_name,
                    'sub_projects' => $subProjectsList,
                    'avg_financial_progress' => round($avgFinancial, 2),
                    'avg_physical_progress' => round($avgPhysical, 2),
                ];
            })
            ->values();

        return view('admin.reports.sub-package-summary', [
            'subProjectsData' => $processed,
            'departments' => $departments,
            'packageProjects' => $packageProjects,
            'filter' => $filters,
        ]);
    }

    public function reportSummary(int $project_id, int $compliance_id, int $phase_id, Request $request)
    {
        $subProject = SubPackageProject::findOrFail($project_id);
        $compliance = SafeguardCompliance::findOrFail($compliance_id);

        $start = $request->filled('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfYear();
        $end = $request->filled('end_date') ? Carbon::parse($request->input('end_date')) : now();

        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        $phase = ContractionPhase::findOrFail($phase_id);
        $isOneTime = $phase->is_one_time;

        $entries = SafeguardEntry::with(['socialSafeguardEntries'])
            ->where('sub_package_project_id', $project_id)
            ->where('safeguard_compliance_id', $compliance_id)
            ->where('contraction_phase_id', $phase_id)
            ->orderBy('sl_no')
            ->get();

        $report = [];

        foreach ($entries as $entry) {
            $sl = $entry->sl_no ?? 'N/A';
            $item = $entry->item_description ?? 'N/A';

            if (!isset($report[$sl])) {
                $report[$sl] = ['item' => $item, 'months' => []];
            }

            foreach ($entry->socialSafeguardEntries as $social) {
                if (!$social->date_of_entry) {
                    continue;
                }

                $cursor = Carbon::parse($social->date_of_entry)->startOfMonth();
                $monthEnd = $isOneTime ? $end->copy()->endOfMonth() : $cursor->copy()->endOfMonth();
                $value = in_array($social->yes_no, [1, 3]) ? 1 : 0;

                while ($cursor <= $monthEnd) {
                    $monthKey = $cursor->format('M-Y');
                    if (!isset($report[$sl]['months'][$monthKey])) {
                        $report[$sl]['months'][$monthKey] = ['value' => $value];
                    }
                    if (!$isOneTime) {
                        break;
                    }
                    $cursor->addMonth();
                }
            }
        }

        // Fill month columns
        $monthColumns = [];
        $cursor = $start->copy();
        while ($cursor <= $end) {
            $monthColumns[] = $cursor->format('M-Y');
            $cursor->addMonth();
        }

        return view('admin.social_safeguard_entries.report_summary', compact('subProject', 'compliance', 'report', 'monthColumns', 'start', 'end', 'phase', 'phase_id'));
    }
    private function processSubProjectData($sp, $request)
    {
        $procurementType = optional($sp->packageProject->procurementDetail)->typeOfProcurement->name ?? 'EPC';
        $financeTotal = $sp->financialProgressUpdates->sum('financeamount');
        $contractValue = $sp->contractvalue ?? 0;
        $financePercent = $contractValue > 0 ? round(($financeTotal / $contractValue) * 100, 2) : 0.0;

        // Physical progress
        $physicalPercent = 0;
        if (strtolower($procurementType) === 'epc') {
            $epcSum = $sp->epcEntries->sum('amount');
            $physicalSum = $sp->physicalEpcProgresses->sum('amount');
            $physicalPercent = $epcSum > 0 ? round(($physicalSum / $epcSum) * 100, 2) : 0.0;
        } else {
            // BOQ
            $plannedQty = $sp->boqEntries->sum('qty');
            $executedQty = $sp->allPhysicalBoqProgresses()->sum('qty');
            $physicalPercent = $plannedQty > 0 ? round(($executedQty / $plannedQty) * 100, 2) : 0.0;
        }

        $safeguards = $this->calculateSafeguardProgress($sp, $request);

        return [
            'id' => $sp->id,
            'name' => $sp->name,
            'package_number' => optional($sp->packageProject)->package_number ?? 'NA',
            'package_name' => optional($sp->packageProject)->package_name ?? 'NA',
            'department' => optional(optional($sp->packageProject)->department)->name ?? 'NA',
            'contract_number' => optional($sp->contract)->contract_number ?? 'NA',
            'contractor' => optional(optional($sp->contract)->contractor)->company_name ?? 'NA',
            'contract_value' => $contractValue,
            'finance_percent' => $financePercent,
            'physical_percent' => $physicalPercent,
            'safeguards' => $safeguards,
        ];
    }
    private function calculateSafeguardProgress(SubPackageProject $sp, Request $request): array
    {
        $compliances = DB::table('safeguard_entries')->join('safeguard_compliances', 'safeguard_entries.safeguard_compliance_id', '=', 'safeguard_compliances.id')->where('safeguard_entries.sub_package_project_id', $sp->id)->select('safeguard_compliances.id', 'safeguard_compliances.name', 'safeguard_compliances.contraction_phase_ids')->distinct()->get();

        $progress = [];

        foreach ($compliances as $compliance) {
            $allowedPhaseIds = collect(json_decode($compliance->contraction_phase_ids, true) ?? [])
                ->map(fn($id) => (int) $id)
                ->toArray();

            $phases = ContractionPhase::whereIn('id', $allowedPhaseIds)
                ->orderBy('id')
                ->get(['id', 'name', 'is_one_time']);

            $phaseReports = $this->calculatePhasesProgress($sp, $compliance->id, $phases, $request);

            $overallTotal = collect($phaseReports)->sum('total');
            $overallDone = collect($phaseReports)->sum('done');
            $overallPercent = $overallTotal > 0 ? round(($overallDone / $overallTotal) * 100, 2) : 0.0;

            $progress[] = [
                'id' => $compliance->id,
                'compliance' => $compliance->name,
                'phases' => $phaseReports,
                'overallPercent' => $overallPercent,
            ];
        }

        return $progress;
    }
    public function packagesSummaryReport(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $packages = PackageProject::withCount(['subProjects'])
            ->with(['procurementDetail.typeOfProcurement', 'workPrograms', 'contracts', 'subProjects.epcEntries', 'subProjects.physicalEpcProgresses', 'subProjects.boqEntries', 'subProjects.physicalBoqProgresses'])
            ->latest()
            ->get();

        $data = $packages->map(function ($pkg) {
            $hasEntries = $pkg->subProjects->contains(fn($sub) => $sub->has_entry_data);
            $hasPhysical = $pkg->subProjects->contains(fn($sub) => $sub->has_physical_progress);

            return [
                'id' => $pkg->id,
                'package_number' => $pkg->package_number ?? 'N/A',
                'package_name' => $pkg->package_name ?? 'N/A',
                'status' => $pkg->status ?? 'N/A',
                'sub_projects' => $pkg->sub_projects_count,
                'has_workprogram' => $pkg->workPrograms->isNotEmpty() ? 'Yes' : 'No',
                'procurement' => $pkg->procurementDetail ? $pkg->procurementDetail->typeOfProcurement->name ?? 'Done' : 'Not Done',
                'has_contract' => $pkg->contracts->isNotEmpty() ? 'Yes' : 'No',
                'has_entry_data' => $hasEntries ? 'Yes' : 'No',
                'has_physical_progress' => $hasPhysical ? 'Yes' : 'No',
            ];
        });

        // --- Summary counts remain same (use full $data) ---
        $summary = [
            'total_packages' => $data->count(),
            'with_workprogram' => $data->where('has_workprogram', 'Yes')->count(),
            'without_workprogram' => $data->where('has_workprogram', 'No')->count(),
            'with_procurement' => $data->where('procurement', '!=', 'Not Done')->count(),
            'without_procurement' => $data->where('procurement', 'Not Done')->count(),
            'with_contracts' => $data->where('has_contract', 'Yes')->count(),
            'without_contracts' => $data->where('has_contract', 'No')->count(),
            'with_entry' => $data->where('has_entry_data', 'Yes')->count(),
            'without_entry' => $data->where('has_entry_data', 'No')->count(),
            'with_physical' => $data->where('has_physical_progress', 'Yes')->count(),
            'without_physical' => $data->where('has_physical_progress', 'No')->count(),
        ];

        // --- Apply filter only for display ---
        $displayData = match ($filter) {
            'workprogram' => $data->where('has_workprogram', 'Yes'),
            'no_workprogram' => $data->where('has_workprogram', 'No'),
            'procurement' => $data->where('procurement', '!=', 'Not Done'),
            'no_procurement' => $data->where('procurement', 'Not Done'),
            'contracts' => $data->where('has_contract', 'Yes'),
            'no_contracts' => $data->where('has_contract', 'No'),
            'entry' => $data->where('has_entry_data', 'Yes'),
            'no_entry' => $data->where('has_entry_data', 'No'),
            'physical' => $data->where('has_physical_progress', 'Yes'),
            'no_physical' => $data->where('has_physical_progress', 'No'),
            default => $data,
        };

        return view('admin.reports.packages-summary', [
            'data' => $displayData,
            'summary' => $summary,
            'filter' => $filter,
        ]);
    }
    private function calculatePhasesProgress(SubPackageProject $sp, int $complianceId, $phases, Request $request): array
    {
        [$startDate, $endDate, $monthsInRange] = $this->resolveDateRange($request);

        $phaseReports = [];

        foreach ($phases as $phase) {
            $childIds = DB::table('safeguard_entries')->where('sub_package_project_id', $sp->id)->where('safeguard_compliance_id', $complianceId)->where('contraction_phase_id', $phase->id)->where('sl_no', 'like', '%.%')->pluck('id')->toArray();

            $childCount = count($childIds);

            if ($childCount === 0) {
                $phaseReports[] = [
                    'id' => $phase->id,
                    'phase' => $phase->name,
                    'total' => 0,
                    'done' => 0,
                    'percent' => 0.0,
                ];
                continue;
            }

            if ($phase->is_one_time) {
                $totalForPhase = $childCount;
                $doneForPhase = DB::table('social_safeguard_entries')->whereIn('safeguard_entry_id', $childIds)->where('sub_package_project_id', $sp->id)->where('social_compliance_id', $complianceId)->where('contraction_phase_id', $phase->id)->count();
            } else {
                $totalForPhase = $childCount * $monthsInRange;
                $doneForPhase = DB::table('social_safeguard_entries')
                    ->whereIn('safeguard_entry_id', $childIds)
                    ->where('sub_package_project_id', $sp->id)
                    ->where('social_compliance_id', $complianceId)
                    ->where('contraction_phase_id', $phase->id)
                    ->whereBetween('date_of_entry', [$startDate, $endDate])
                    ->count();
            }

            $percent = $totalForPhase > 0 ? round(($doneForPhase / $totalForPhase) * 100, 2) : 0.0;

            $phaseReports[] = [
                'id' => $phase->id,
                'phase' => $phase->name,
                'total' => $totalForPhase,
                'done' => $doneForPhase,
                'percent' => $percent,
            ];
        }

        return $phaseReports;
    }
    private function resolveDateRange(Request $request): array
    {
        $start = Carbon::parse($request->input('start_date', now()->startOfMonth()));
        $end = Carbon::parse($request->input('end_date', now()));

        if ($end->gt(now())) {
            $end = now();
        }
        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        $monthsInRange = $end->year * 12 + $end->month - ($start->year * 12 + $start->month) + 1;
        $monthsInRange = max(1, $monthsInRange);

        return [$start->format('Y-m-d'), $end->format('Y-m-d'), $monthsInRange];
    }
    public function contractRegisterReport(Request $request)
    {
        $contracts = Contract::with([
            'project.department',
            'contractor',
            'subProjects.packageProject',
            'subProjects.contract.project.procurementDetail.typeOfProcurement',
            'subProjects.contract.contractor',
        ])->get();

        $contractsData = collect();

        foreach ($contracts as $contract) {
            foreach ($contract->subProjects as $sp) {

                // ✅ SAME safeguard logic
                $safeguards = $this->normalizeSafeguards($sp);

                $contractsData->push([
                    'id' => $sp->id,
                    'package_number' => $sp->packageProject->package_number ?? 'N/A',
                    'contract_number' => $contract->contract_number ?? 'N/A',
                    'commencement_date' => $contract->commencement_date
                        ? \Carbon\Carbon::parse($contract->commencement_date)->format('d-m-Y')
                        : 'N/A',
                    'completion_date' => $contract->revised_completion_date
                        ? \Carbon\Carbon::parse($contract->revised_completion_date)->format('d-m-Y')
                        : ($contract->initial_completion_date
                            ? \Carbon\Carbon::parse($contract->initial_completion_date)->format('d-m-Y')
                            : 'N/A'),
                    'contract_value' => $sp->contract_value ?? 0,
                    'contractor' => $contract->contractor->company_name ?? 'N/A',
                    'department' => $contract->project->department->name ?? 'N/A',
                    'name' => $sp->name ?? 'N/A',
                    'finance_percent' => $sp->financial_progress_percentage,
                    'physical_percent' => $sp->physical_progress_percentage,
                    'safeguards' => $safeguards,
                ]);
            }
        }

        // ✅ SAME header builder logic
        $compliancePhaseHeaders = collect($contractsData)
            ->flatMap(
                fn($row) =>
                collect($row['safeguards'])->flatMap(
                    fn($sg) =>
                    collect($sg['phases'])->map(
                        fn($ph) => ($sg['compliance'] ?? '') . ' – ' . $ph['phase']
                    )
                )
            )
            ->unique()
            ->values();

        return view('admin.reports.contract-register', [
            'contractsData' => $contractsData,
            'compliancePhaseHeaders' => $compliancePhaseHeaders,
        ]);
    }


  public function subProjectsReport(Request $request)
{
    $filters = $request->only([
        'department_id',
        'sub_department_id',
        'district_id',
        'package_component_id'
    ]);

    $subProjects = SubPackageProject::with([
            'packageProject',
            'contract.project.procurementDetail.typeOfProcurement',
            'contract.contractor',
            'boqEntries.physicalBoqProgresses',
            'epcEntries.physicalEpcProgresses'
        ])
        ->whereHas('packageProject', fn ($q) => $q->applyFilters($filters))
        ->get();

    $subProjectsData = $subProjects->map(function ($sp) {

        // 🔹 Safeguard progress
        $rawSafeguards = $sp->socialSafeguardProgress();

        $safeguards = collect($rawSafeguards)
            ->map(function ($val, $cid) {
                return [
                    'id' => (int) $cid,
                    'compliance' => $val['compliance'] ?? 'N/A',
                    'phases' => collect($val['phases'] ?? [])
                        ->map(fn ($ph) => [
                            'id' => $ph['id'] ?? null,
                            'phase' => $ph['phase'] ?? 'N/A',
                            'percent' => (float) ($ph['percent'] ?? 0),
                        ])
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
            'contractValue' => $sp->contract_value,

            // ✅ PROCUREMENT TYPE (THIS WAS MISSING)
            'type_of_procurement_name' => $sp->type_of_procurement_name,

            // ✅ Physical & Finance Progress
            'physicalPercent' => $sp->physical_progress_percentage,
            'financePercent' => $sp->financial_progress_percentage,

            'safeguards' => $safeguards,
        ];
    });

    // 🔹 Build table headers (Compliance – Phase)
    $compliancePhaseHeaders = collect($subProjectsData)
        ->flatMap(fn ($sp) =>
            collect($sp['safeguards'])->flatMap(fn ($sg) =>
                collect($sg['phases'])->map(
                    fn ($ph) => $sg['compliance'] . ' – ' . $ph['phase']
                )
            )
        )
        ->unique()
        ->values();

    // Dropdowns
    $departments = Department::orderBy('name')->get(['id', 'name']);
    $subDepartments = SubDepartment::orderBy('name')->get(['id', 'name']);
    $districts = GeographyDistrict::orderBy('name')->get(['id', 'name']);

    return view(
        'admin.reports.subprojects',
        compact(
            'subProjectsData',
            'compliancePhaseHeaders',
            'departments',
            'subDepartments',
            'districts'
        )
    );
}

    private function normalizeSafeguards($sp): array
    {
        $rawSafeguards = $sp->socialSafeguardProgress();

        return collect($rawSafeguards)
            ->map(function ($val, $cid) {
                return [
                    'id' => (int) $cid,
                    'compliance' => $val['compliance'] ?? ($val['name'] ?? null),
                    'phases' => collect($val['phases'] ?? [])
                        ->map(function ($ph) {
                            return [
                                'id' => $ph['id'] ?? null,
                                'phase' => $ph['phase'] ?? ($ph['name'] ?? null),
                                'percent' => isset($ph['percent']) ? (float) $ph['percent'] : 0.0,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }

    public function show($id, Request $request)
{
    $contract = Contract::with([
        'project.procurementDetail.typeOfProcurement',
        'contractor',
        'subProjects',
        'updates',
    ])->findOrFail($id);

    $procurementType = strtolower(trim(
        $contract->project->procurementDetail->typeOfProcurement->name ?? 'epc'
    ));

    // ================= Sub-Projects Data =================
    $subProjectsData = $contract->subProjects->map(function (SubPackageProject $sp) use ($procurementType) {

        $finance = $this->calculateFinanceProgress($sp->id, $sp->contract_value);
        $physical = $this->calculatePhysicalProgress($sp, $procurementType);

        // 🔑 Work Progress
        $workProgress = WorkProgressData::with(['user', 'workComponent'])
            ->where('project_id', $sp->id)
            ->orderBy('date_of_entry', 'asc')
            ->get()
            ->map(fn($wp) => [
                'id' => $wp->id,
                'component' => $wp->workComponent->work_component ?? 'N/A',
                'current_stage' => $wp->current_stage ?? null,
                'progress' => $wp->progress_percentage ?? 0,
                'date_of_entry' => optional($wp->date_of_entry)->format('d-m-Y'),
                'user' => $wp->user->name ?? 'System',
            ]);

        return [
            'id' => $sp->id,
            'name' => $sp->name,
            'workProgress' => $workProgress,

            'contractValue' => $sp->contract_value,

            'financePercent' => $finance['percent'],
            'financeEntries' => $finance['submissions'],
            'financeLastDate' => $finance['last_date'],

            'physicalPercent' => $physical['percent'],
            'physicalEntries' => $physical['submissions'],
            'physicalLastDate' => $physical['last_date'],

            // ✅ SAME safeguard logic as all reports
            'safeguards' => $this->normalizeSafeguards($sp),
        ];
    });

    // ================= Finance Entries =================
    $allFinanceEntries = $this->reportService->flattenFinanceEntries($subProjectsData);

    // ================= Compliance Headers =================
    $allCompliances = $subProjectsData
        ->pluck('safeguards')
        ->flatten(1)
        ->pluck('compliance')
        ->unique()
        ->values();

    $compliancePhaseHeaders = $subProjectsData
        ->flatMap(fn($sp) =>
            collect($sp['safeguards'])->flatMap(fn($sg) =>
                collect($sg['phases'])->map(
                    fn($ph) => ($sg['compliance'] ?? '') . ' – ' . $ph['phase']
                )
            )
        )
        ->unique()
        ->values();

    // ================= Milestones =================
    $milestones = [];
    if ($contract->commencement_date && $contract->revised_completion_date) {
        $milestones = MilestoneHelper::generateAmendedWithProgress($contract, $subProjectsData);
    }

    // ================= Format Dates =================
    $this->formatContractAttributes($contract);

    return view('admin.reports.show', compact(
        'contract',
        'subProjectsData',
        'allCompliances',
        'compliancePhaseHeaders',
        'milestones',
        'allFinanceEntries'
    ));
}

    public function showSubProject($contractId, $subProjectId, Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | Load Contract + Relations
    |--------------------------------------------------------------------------
    */
        $contract = Contract::with(['project.procurementDetail.typeOfProcurement', 'contractor', 'updates', 'subProjects.boqEntries.physicalBoqProgresses', 'subProjects.epcEntries.physicalEpcProgresses', 'subProjects.financialProgressUpdates', 'subProjects.workProgressData.workComponent', 'subProjects.workProgressData.user'])->findOrFail($contractId);

        $sp = $contract->subProjects()->findOrFail($subProjectId);

        /*
    |--------------------------------------------------------------------------
    | Finance & Physical Progress
    |--------------------------------------------------------------------------
    */
        $finance = $this->calculateFinanceProgress($sp->id, $sp->contract_value);
        $physical = $this->calculatePhysicalProgress($sp, strtolower($sp->type_of_procurement_name ?? 'epc'));

        /*
    |--------------------------------------------------------------------------
    | Work Progress (Grouped)
    |--------------------------------------------------------------------------
    */
        $existingEntries = $sp->workProgressData->groupBy('work_component_id')->map(function ($items) {
            $last = $items->sortByDesc('id')->first();

            return (object) [
                'total_progress' => min(100, $items->sum('progress_percentage')),
                'last_entry' => $last,
                'last_stage' => $last->current_stage ?? null,
                'last_remarks' => $last->remarks ?? null,
                'images' => $last->images ?? [], // add images array
            ];
        });

        $components = AlreadyDefinedWorkProgress::with('workService')->get();

        /*
    |--------------------------------------------------------------------------
    | SAFEGUARDS (SINGLE SOURCE OF TRUTH)
    |--------------------------------------------------------------------------
    */
        $rawSafeguards = $sp->socialSafeguardProgress(null, $request->filled('start_date') ? Carbon::parse($request->start_date) : null, $request->filled('end_date') ? Carbon::parse($request->end_date) : null);

        // Normalize EXACTLY like subProjectsReport()
        $safeguards = collect($rawSafeguards)
            ->map(function ($val, $cid) {
                return [
                    'id' => (int) $cid,
                    'compliance' => $val['compliance'],
                    'phases' => collect($val['phases'])
                        ->map(
                            fn($ph) => [
                                'id' => $ph['id'],
                                'phase' => $ph['phase'],
                                'percent' => (float) $ph['percent'],
                            ],
                        )
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();

        /*
    |--------------------------------------------------------------------------
    | Safeguard Table Headers (Same as Report)
    |--------------------------------------------------------------------------
    */
        $compliancePhaseHeaders = collect($safeguards)->flatMap(fn($sg) => collect($sg['phases'])->pluck('phase'))->unique()->values();

        /*
    |--------------------------------------------------------------------------
    | Sub-Project Payload
    |--------------------------------------------------------------------------
    */
        $subProjectData = [
            'id' => $sp->id,
            'name' => $sp->name,
            'contractValue' => $sp->contract_value,
            'financePercent' => $finance['percent'],
            'financeEntries' => $finance['submissions'],
            'financeLastDate' => $finance['last_date'],
            'physicalPercent' => $physical['percent'],
            'physicalEntries' => $physical['submissions'],
            'physicalLastDate' => $physical['last_date'],
            'components' => $components,
            'existingEntries' => $existingEntries,
            'safeguards' => $safeguards,
        ];

        /*
    |--------------------------------------------------------------------------
    | Milestones
    |--------------------------------------------------------------------------
    */
        $milestones = [];

        if ($contract->commencement_date && $contract->revised_completion_date) {
            $milestones = MilestoneHelper::generateAmendedWithProgress($contract, collect([$subProjectData]));
        }

        /*
    |--------------------------------------------------------------------------
    | Format Contract Attributes
    |--------------------------------------------------------------------------
    */
        $this->formatContractAttributes($contract);

        /*
    |--------------------------------------------------------------------------
    | Return View
    |--------------------------------------------------------------------------
    */
        return view('admin.reports.subproject-show', compact('contract', 'subProjectData', 'compliancePhaseHeaders', 'milestones'));
    }

    private function generateAmendedMilestonesWithProgress(Contract $contract, $subProjectsData)
    {
        $startDate = Carbon::parse($contract->commencement_date)->startOfDay();
        $newEndDate = Carbon::parse($contract->revised_completion_date)->endOfDay();

        $latestUpdate = $contract->updates()->latest('changed_at')->first();
        $oldEndDate = $latestUpdate ? Carbon::parse($latestUpdate->old_initial_completion_date)->endOfDay() : Carbon::parse($contract->initial_completion_date)->endOfDay();

        // Split into 3 milestones based on old completion
        $milestones = $this->splitOldMilestones($startDate, $oldEndDate);

        // Extend last milestone if contract amended
        if ($latestUpdate && $newEndDate->gt($oldEndDate)) {
            $lastIndex = count($milestones) - 1;
            $milestones[$lastIndex]['to'] = $newEndDate;
            $milestones[$lastIndex]['months'] = round($milestones[$lastIndex]['from']->floatDiffInMonths($newEndDate) + 1, 2);
            $milestones[$lastIndex]['label'] = 'Extended ' . $milestones[$lastIndex]['label'];
        }

        // Planned vs Achieved
        $splits = config('milestones.default_finance_split', [20, 30, 50]);

        foreach ($milestones as $i => &$ms) {
            $ms['plannedFinance'] = $splits[$i] ?? 0;
            $ms['plannedPhysical'] = $ms['plannedFinance'];

            $ms['achievedFinance'] = $this->calculateMilestoneProgress($subProjectsData, $ms['from'], $ms['to'], 'finance');
            $ms['achievedPhysical'] = $this->calculateMilestoneProgress($subProjectsData, $ms['from'], $ms['to'], 'physical');
        }

        return $milestones;
    }

    private function calculateMilestoneProgress($subProjectsData, $start, $end, $type = 'finance')
    {
        $totalContract = $subProjectsData->sum('contractValue');
        $totalAmount = 0;

        foreach ($subProjectsData as $sp) {
            $entries = $type === 'finance' ? $sp['financeEntries'] ?? collect() : $sp['physicalEntries'] ?? collect();

            foreach ($entries as $entry) {
                $subDate = !empty($entry['date']) ? Carbon::parse($entry['date']) : null;
                if ($subDate && $subDate->between($start, $end)) {
                    $totalAmount += $entry['amount'] ?? 0;
                }
            }
        }

        return $totalContract > 0 ? round(($totalAmount / $totalContract) * 100, 2) : 0.0;
    }

    private function splitOldMilestones(Carbon $start, Carbon $end)
    {
        $totalDays = $start->diffInDays($end) + 1;
        $baseDays = intdiv($totalDays, 3);
        $extra = $totalDays % 3;

        $segments = [];
        $cur = $start->copy();

        for ($i = 0; $i < 3; $i++) {
            $days = $baseDays + ($i < $extra ? 1 : 0);
            $segEnd = $cur->copy()->addDays($days - 1);

            $segments[] = [
                'label' => 'M' . ($i + 1),
                'from' => $cur->copy(),
                'to' => $segEnd->copy(),
                'months' => round($cur->floatDiffInMonths($segEnd) + 1, 2),
            ];

            $cur = $segEnd->copy()->addDay();
        }

        return $segments;
    }
    private function calculateFinancePercent(int $projectId, float $contractValue): float
    {
        $financeTotal = FinancialProgressUpdate::where('project_id', $projectId)->sum('finance_amount');
        return $contractValue > 0 ? round(($financeTotal / $contractValue) * 100, 2) : 0.0;
    }
    private function calculatePhysicalProgress(SubPackageProject $sp, string $type): array
    {
        if ($type === 'epc') {
            $updates = $sp
                ->physicalEpcProgresses()
                ->with('epcEntryData') // eager load
                ->orderBy('progress_submitted_date', 'ASC')
                ->get();

            $submissions = $updates
                ->map(function ($u) {
                    $entry = $u->epcEntryData;
                    return [
                        'percent' => round($u->percent, 2), // round to 2 decimals
                        'date' => Carbon::parse($u->progress_submitted_date)->format('d-m-Y'),
                        'item_description' => trim(($entry->activity_name ? $entry->activity_name . ' - ' : '') . ($entry->stage_name ?? '')),
                    ];
                })
                ->filter(fn($entry) => isset($entry['percent']) && $entry['percent'] > 0) // <-- remove zero percent entries
                ->values(); // reset array keys

            // Overall EPC physical percent
            $totalAmount = $updates->sum('amount');
            $percent = $sp->contract_value > 0 ? round(($totalAmount / $sp->contract_value) * 100, 2) : 0.0;
        } else {
            // BOQ
            $updates = $sp->physicalBoqProgresses()->with('boqEntryData')->orderBy('progress_submitted_date', 'ASC')->get();

            $submissions = $updates
                ->map(function ($u) {
                    $entry = $u->boqEntryData;
                    return [
                        'amount' => round($u->amount, 2),
                        'date' => Carbon::parse($u->progress_submitted_date)->format('d-m-Y'),
                        'item_description' => trim(($entry->sl_no ? $entry->sl_no . ' - ' : '') . ($entry->item_description ?? '')),
                    ];
                })
                ->filter(fn($entry) => isset($entry['amount']) && $entry['amount'] > 0) // optional: filter zero amount entries
                ->values();

            $totalAmount = $updates->sum('amount');
            $totalWithGST = $totalAmount * 1.18;
            $percent = $sp->contract_value > 0 ? round(($totalWithGST / $sp->contract_value) * 100, 2) : 0.0;
        }

        return [
            'percent' => $percent,
            'submissions' => $submissions,
            'last_date' => $submissions->last()['date'] ?? null,
        ];
    }

    private function calculateFinanceProgress(int $projectId, float $contractValue): array
    {
        $updates = FinancialProgressUpdate::where('project_id', $projectId)->select('finance_amount', 'submit_date')->orderBy('submit_date')->get();

        $total = $updates->sum('finance_amount');
        $percent = $contractValue > 0 ? round(($total / $contractValue) * 100, 2) : 0.0;

        return [
            'percent' => $percent,
            'submissions' => $updates->map(
                fn($u) => [
                    'amount' => $u->finance_amount,
                    'date' => $u->submit_date,
                ],
            ),
            'last_date' => optional($updates->last())->submit_date,
        ];
    }
    private function calculatePhysicalPercentForType(SubPackageProject $sp, string $type): float
    {
        $value = 0;
        if ($type === 'epc') {
            $value = $sp->physicalEpcProgresses()->sum('physical_epc_progress.amount');
        } elseif ($type === 'item-rate') {
            $value = $sp->physicalBoqProgresses()->sum('physical_boq_progress.amount');
        }

        return $sp->contract_value > 0 ? round(($value / $sp->contract_value) * 100, 2) : 0.0;
    }
    private function formatContractAttributes(Contract $contract): void
    {
        $contract->formatted_signing_date = optional($contract->signing_date)?->format('d M Y') ?? 'N/A';
        $contract->formatted_commencement_date = optional($contract->commencement_date)?->format('d M Y') ?? 'N/A';
        $contract->formatted_initial_completion_date = optional($contract->initial_completion_date)?->format('d M Y') ?? 'N/A';
        $contract->formatted_revised_completion_date = optional($contract->revised_completion_date)?->format('d M Y') ?? 'N/A';
    }
}
