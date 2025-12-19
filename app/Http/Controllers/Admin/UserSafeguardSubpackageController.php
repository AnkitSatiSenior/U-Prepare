<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSafeguardSubpackage;
use App\Models\User;
use App\Models\SafeguardCompliance;
use App\Models\SubPackageProject;
use Illuminate\Http\Request;

class UserSafeguardSubpackageController extends Controller
{
    /**
     * Display all assignments
     */
    public function index()
    {
        $assignments = UserSafeguardSubpackage::with([
            'user',
            'safeguardCompliance',
            'subPackageProject',
        ])
        ->latest()
        ->get();

        return view('admin.user_safeguard_subpackage.index', compact('assignments'));
    }
/**
 * Show tree view of User Safeguard Subpackage assignments
 */
public function assignmentTree(Request $request)
{
    // Filter by subpackage if passed
    $query = UserSafeguardSubpackage::with(['user', 'safeguardCompliance', 'subPackageProject'])->latest();

    if ($request->filled('sub_package_id')) {
        $query->where('sub_package_project_id', $request->sub_package_id);
    }

    $assignments = $query->get();

    return view('admin.user_safeguard_subpackage.tree', compact('assignments'));
}


    /**
     * Show form for creating new assignments
     */
    public function create()
    {
        $users = User::select('id', 'name','username')->get();
        $safeguardCompliances = SafeguardCompliance::select('id', 'name')->get();
        $subPackageProjects = SubPackageProject::select('id', 'name')->get();

        return view('admin.user_safeguard_subpackage.create', compact(
            'users',
            'safeguardCompliances',
            'subPackageProjects'
        ));
    }

    /**
     * Store new assignments (bulk supported, skip duplicates)
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'safeguard_compliance_ids' => 'required|array|min:1',
            'safeguard_compliance_ids.*' => 'exists:safeguard_compliances,id',
            'sub_package_project_ids' => 'required|array|min:1',
            'sub_package_project_ids.*' => 'exists:sub_package_projects,id',
        ]);

        $userIds = $request->user_ids;
        $complianceIds = $request->safeguard_compliance_ids;
        $projectIds = $request->sub_package_project_ids;

        $createdCount = 0;

        foreach ($userIds as $userId) {
            foreach ($complianceIds as $complianceId) {
                foreach ($projectIds as $projectId) {
                    $exists = UserSafeguardSubpackage::where([
                        'user_id' => $userId,
                        'safeguard_compliance_id' => $complianceId,
                        'sub_package_project_id' => $projectId,
                    ])->exists();

                    if (! $exists) {
                        UserSafeguardSubpackage::create([
                            'user_id' => $userId,
                            'safeguard_compliance_id' => $complianceId,
                            'sub_package_project_id' => $projectId,
                        ]);
                        $createdCount++;
                    }
                }
            }
        }

        return redirect()
            ->route('admin.user-safeguard-subpackage.index')
            ->with('success', $createdCount > 0 
                ? "✅ $createdCount new assignment(s) created successfully." 
                : "ℹ️ No new assignments were added (all duplicates skipped)."
            );
    }

    /**
     * Delete a single assignment (hard delete)
     */
    public function destroy(UserSafeguardSubpackage $assignment)
    {
        $assignment->forceDelete(); // ✅ hard delete

        return back()->with('success', '✅ Assignment removed permanently.');
    }

    /**
     * Bulk delete assignments (hard delete)
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:user_safeguard_subpackage,id',
        ]);

        $count = UserSafeguardSubpackage::whereIn('id', $request->ids)->forceDelete(); // ✅ hard delete

        return back()->with('success', "✅ $count assignment(s) removed permanently.");
    }
}
