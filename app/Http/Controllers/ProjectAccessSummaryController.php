<?php

namespace App\Http\Controllers;

use App\Models\PackageProject;
use App\Models\SubPackageProject;
use App\Models\SubDepartment;
use App\Models\User;
use App\Models\SafeguardCompliance;
use App\Models\SafeguardEntry;
use App\Models\Department;
use Carbon\Carbon;
use App\Models\ContractionPhase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectAccessSummaryController extends Controller
{public function dynamicComplianceReport(Request $request)
{
    /** --------------------------------------------------------
     * Read Request Inputs
     * -------------------------------------------------------- */
    $departmentId     = $request->department_id;
    $subDepartmentId  = $request->sub_department_id;
    $complianceId     = $request->compliance_id;
    $phaseId          = $request->phase_id;
    $itemDesc         = $request->item_description;

    $start = $request->filled('start_date')
        ? Carbon::parse($request->start_date)
        : now()->startOfYear();

    $end = $request->filled('end_date')
        ? Carbon::parse($request->end_date)
        : now();

    // Swap invalid dates
    if ($start->gt($end)) {
        [$start, $end] = [$end, $start];
    }

    /** --------------------------------------------------------
     * Dropdown: Departments & Sub-Departments
     * -------------------------------------------------------- */
    $departments = Department::orderBy('name')->get();

    $subDepartments = $departmentId
        ? SubDepartment::where('department_id', $departmentId)->orderBy('name')->get()
        : collect();


    /** --------------------------------------------------------
     * Dropdown: Compliance & Phases
     * -------------------------------------------------------- */
    $compliances = SafeguardCompliance::with('contractionPhases')
        ->orderBy('name')
        ->get();

    $phases = $complianceId
        ? ($compliances->find($complianceId)->contractionPhases ?? collect())
        : collect();


    /** --------------------------------------------------------
     * Dropdown: Item Descriptions
     * -------------------------------------------------------- */
    $itemsDropdown = ($complianceId && $phaseId)
        ? SafeguardEntry::where('safeguard_compliance_id', $complianceId)
            ->where('contraction_phase_id', $phaseId)
            ->select('item_description')
            ->distinct()
            ->pluck('item_description')
        : collect();


    /** --------------------------------------------------------
     * Fetch All Entries (Filters applied)
     * -------------------------------------------------------- */
   $projectIds = PackageProject::where('department_id', $departmentId)
    ->where('sub_department_id', $subDepartmentId)
    ->pluck('id');

$subPackageIds = SubPackageProject::whereIn('project_id', $projectIds)
    ->pluck('id');

$entries = SafeguardEntry::query()
    ->with([
        'subPackageProject.packageProject',
        'contractionPhase',
        'socialSafeguardEntries'
    ])

    // Filter by compliance
    ->when($complianceId, fn($q) =>
        $q->where('safeguard_compliance_id', $complianceId)
    )

    // Filter by phase
    ->when($phaseId, fn($q) =>
        $q->where('contraction_phase_id', $phaseId)
    )

    // Filter by item description
    ->when($itemDesc, fn($q) =>
        $q->where('item_description', $itemDesc)
    )

    // Filter by date range
    ->when($start && $end, fn($q) =>
        $q->whereBetween('created_at', [$start, $end])
    )

    // ⭐ Filter using department + sub-department through relationships
    ->whereHas('subPackageProject.packageProject', function ($q) use ($departmentId, $subDepartmentId) {
        $q->when($departmentId, fn($x) => $x->where('department_id', $departmentId))
          ->when($subDepartmentId, fn($x) => $x->where('sub_department_id', $subDepartmentId));
    })

    ->get();




    /** --------------------------------------------------------
     * Maps & Unique Lists
     * -------------------------------------------------------- */
    $entryMap = [];

    foreach ($entries as $entry) {
        $key = $entry->sl_no . '|' . $entry->item_description . '|' . optional($entry->subPackageProject)->id;
        $entryMap[$key] = $entry;
    }

    // Unique Sub-Packages
    $subPackages = $entries
        ->pluck('subPackageProject')
        ->filter()
        ->unique('id')
        ->mapWithKeys(function ($subPkg) {
            $pkgNumber = optional($subPkg->packageProject)->package_number
                ?? 'SUB-' . $subPkg->id;
            return [$pkgNumber => $subPkg];
        });

    /** Unique item groups */
    $items = $entries
        ->map(fn ($e) => [
            'sl_no' => $e->sl_no,
            'item_description' => $e->item_description,
            'group_key' => $e->sl_no . ' - ' . $e->item_description,
        ])
        ->unique('group_key');


    /** --------------------------------------------------------
     * Build Report Matrix
     * -------------------------------------------------------- */
    $report = [];

    foreach ($items as $item) {
        $sampleEntry = $entries->first(fn ($e) =>
            $e->sl_no == $item['sl_no'] &&
            $e->item_description == $item['item_description']
        );

        $isParent = $sampleEntry->is_parent ?? 0;

        $row = [
            'sl_no' => $item['sl_no'],
            'item_description' => $item['item_description'],
            'is_parent' => $isParent,
        ];

        // Parent row → no yes/no columns
        if ($isParent == 1) {
            foreach ($subPackages as $pkgNumber => $pkg) {
                $row[$pkgNumber] = null;
            }
            $report[] = $row;
            continue;
        }

        // Child rows → fill yes/no values
        foreach ($subPackages as $pkgNumber => $subPkg) {
            $key = $item['sl_no'] . '|' . $item['item_description'] . '|' . $subPkg->id;

            $entry = $entryMap[$key] ?? null;

            if ($entry) {
                // Yes only if social entries have yes_no = 1 or 3
                $value = $entry->socialSafeguardEntries->contains(
                    fn ($s) => in_array($s->yes_no, [1, 3])
                ) ? 1 : 0;

                $row[$pkgNumber] = $value;
            } else {
                $row[$pkgNumber] = null;
            }
        }

        $report[] = $row;
    }


    /** --------------------------------------------------------
     * Return View
     * -------------------------------------------------------- */
    return view('admin.social_safeguard_entries.dynamic_report-2', compact(
        'departments', 'subDepartments',
        'compliances', 'phases', 'itemsDropdown',
        'report', 'subPackages',
        'departmentId', 'subDepartmentId',
        'complianceId', 'phaseId', 'itemDesc',
        'start', 'end'
    ));
}



    /**
     * Load all SubPackageProjects with their parent project (optimized)
     */
    public function index(Request $request): View
    {
        $subPackageProjects = SubPackageProject::with(['packageproject:id,package_name,package_number,department_id', 'packageproject.department:id,name'])
            ->latest()
            ->get();

        return view('admin.projects.subpackage-index', compact('subPackageProjects'));
    }

    /**
     * Summary of a specific Package + SubPackage (optimized eager loading)
     */
    public function summary($packageProjectId, $subPackageProjectId)
    {
        $package = PackageProject::with('department:id,name', 'assignments.assignee.role.routes')->findOrFail($packageProjectId);

        $sub = SubPackageProject::findOrFail($subPackageProjectId);

        // Load all assigned users with related sub-department, role, safeguards, and phases
        $assignedUsers = User::whereIn('id', function ($q) use ($subPackageProjectId) {
            $q->select('user_id')->from('user_safeguard_subpackages')->where('sub_package_project_id', $subPackageProjectId);
        })
            ->whereNotNull('sub_department_id')
            ->with([
                'subDepartment:id,name',
                'role.routes:id,route_name',
                'safeguardSubpackages' => function ($q) use ($subPackageProjectId) {
                    $q->where('sub_package_project_id', $subPackageProjectId)->with(['safeguardCompliance:id,name', 'safeguardCompliance.contractionPhases:id,name']);
                },
            ])
            ->get();

        $result = [
            'package_project' => $package->title ?? $package->package_name,
            'sub_package_project' => $sub->title ?? $sub->name,
            'department' => $package->department->name ?? 'N/A',
            'assigned_by' => optional($package->assignments->first()?->assigner)->name ?? 'N/A',
            'assigned_users' => $assignedUsers->map(function ($user) {
                $safeguards = $user->safeguardSubpackages->map(function ($item) {
                    return [
                        'name' => $item->safeguardCompliance->name ?? 'N/A',
                        'phases' => $item->safeguardCompliance->contractionPhases->pluck('name'),
                    ];
                });

                return [
                    'user_name' => $user->name,
                    'sub_department' => $user->subDepartment->name ?? 'N/A',
                    'role' => $user->role->name ?? 'N/A',
                    'routes' => $user->role->routes->pluck('route_name')->toArray(),
                    'safeguard_permissions' => $safeguards,
                ];
            }),
        ];

        return view('admin.projects.access-summary', ['data' => $result]);
    }

    /**
     * Show safeguard dropdown view
     */
    public function getSafeguardsWithPhases()
    {
        $safeguards = SafeguardCompliance::select('id', 'name')->get();
        $departments = \App\Models\Department::select('id', 'name')->get();

        return view('admin.projects.safeguards-list', compact('safeguards', 'departments'));
    }

    /**
     * Get sub-departments by department
     */
    public function getSubDepartmentsByDepartment($departmentId)
    {
        $subDepartments = SubDepartment::where('department_id', $departmentId)->select('id', 'name')->get();

        return response()->json($subDepartments);
    }

    /**
     * Get phases by safeguard
     */
    public function getPhases($id)
    {
        $phases = SafeguardCompliance::with('contractionPhases:id,name')->findOrFail($id)->contractionPhases->map(fn($phase) => ['id' => $phase->id, 'name' => $phase->name]);

        return response()->json($phases);
    }

    /**
     * Get users by sub-department
     */
    public function getUsersBySubDepartment($subDepartmentId)
    {
        $users = User::where('sub_department_id', $subDepartmentId)->select('id', 'name')->get();

        return response()->json($users);
    }

    /**
     * Get package projects assigned to a user
     */
    public function getPackageProjectsByUser($userId)
    {
        $projects = PackageProject::whereHas('assignments', function ($q) use ($userId) {
            $q->where('assigned_to', $userId);
        })
            ->select('id', 'package_name', 'package_number')
            ->get();

        return response()->json($projects);
    }
    public function getUsersAndProjectsBySubDepartment($subDepartmentId)
    {
        // Get all users in this sub-department
        $users = User::where('sub_department_id', $subDepartmentId)->select('id', 'name')->get();

        // Get all package project IDs assigned to these users
        $userIds = $users->pluck('id');

        $projects = PackageProject::whereHas('assignments', function ($q) use ($userIds) {
            $q->whereIn('assigned_to', $userIds);
        })
            ->select('id', 'package_name', 'package_number')
            ->distinct() // ensures no duplicates
            ->get();

        return response()->json([
            'users' => $users,
            'projects' => $projects,
        ]);
    }
    public function getUsersAndProjectsByDepartment($departmentId)
    {
        // Get all users in this department
        $users = User::where('department_id', $departmentId)->select('id', 'name')->get();

        // Get all package projects assigned to these users
        $userIds = $users->pluck('id');

        $projects = PackageProject::whereHas('assignments', function ($q) use ($userIds) {
            $q->whereIn('assigned_to', $userIds);
        })
            ->select('id', 'package_name', 'package_number')
            ->distinct()
            ->get();

        return response()->json([
            'users' => $users,
            'projects' => $projects,
        ]);
    }
    public function getSubpackge($projectID)
    {
        $subPackageProjects = SubPackageProject::where('project_id', $projectID)->select('id', 'name');

        return response()->json([
            'subpackageprojects' => $subPackageProjects,
        ]);
    }
  
}
