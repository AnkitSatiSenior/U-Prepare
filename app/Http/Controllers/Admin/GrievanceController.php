<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Grievance, GrievanceLog, GrievanceAssignment, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; 


class GrievanceController extends Controller
{
    /**
     * Display grievances with filters, pagination & stats.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Grievance::query();

        // ✅ Roles allowed to see all grievances
        $rolesAllowedAll = [1, 2, 7, 9];

        // ✅ Role-based restriction
        if (!in_array($user->role_id, $rolesAllowedAll) && !$request->boolean('show_all')) {
            $query->whereHas('assignments', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }

        // ✅ Filters
        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }
        if ($request->filled('related_to')) {
            $query->where('grievance_related_to', $request->related_to);
        }

        // ✅ Default: show only "pending" grievances unless status filter is applied
        if ($request->filled('status')) {
            if ($request->status !== 'total') {
                // 👈 handle 'total' case
                $query->where('status', $request->status);
            }
        } else {
            $query->where('status', 'pending'); // default
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // ✅ Fetch data
        $grievances = $query->with(['assignments.assignedUser'])->latest()->get();


        // ✅ Stats (respect same visibility rules, not filtered by user’s filters)
        $statsQuery = Grievance::query();
        if (!in_array($user->role_id, $rolesAllowedAll) && !$request->boolean('show_all')) {
            $statsQuery->whereHas('assignments', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }

        $total = $statsQuery->count();
        $pending = (clone $statsQuery)->where('status', 'pending')->count();
        $resolved = (clone $statsQuery)->where('status', 'resolved')->count();
        $rejected = (clone $statsQuery)->where('status', 'rejected')->count();

        // ✅ Filter dropdown options
        $districts = Grievance::distinct()->pluck('district')->filter()->toArray();
        $relatedToOptions = Grievance::distinct()->pluck('grievance_related_to')->filter()->toArray();

        return view('admin.grievances.index', compact('grievances', 'total', 'pending', 'resolved', 'rejected', 'districts', 'relatedToOptions'));
    }

    /**
     * Show grievance details with logs & assignments.
     */
    public function show(Request $request, $grievance_no)
    {
        $user = auth()->user();

        $query = Grievance::with(['logs.user', 'assignments.assignedUser', 'assignments.assignedByUser'])->where('grievance_no', $grievance_no);

        // ✅ Roles allowed to see all grievances
        $rolesAllowedAll = [1, 2, 7, 9];

        // ✅ Restrict others to only their assigned grievances
        if (!in_array($user->role_id, $rolesAllowedAll) && !$request->boolean('show_all')) {
            $query->whereHas('assignments', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }

        $grievance = $query->firstOrFail();

        $users = User::all();

        return view('admin.grievances.show', compact('grievance', 'users'));
    }

    /**
     * Store a grievance log.
     */
    public function storeLog(Request $request, $grievance_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'remark' => 'nullable|string',
            'type' => 'nullable|in:preliminary,final,log',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:25120',
        ]);

        try {
            $log = DB::transaction(function () use ($request, $grievance_id) {
                $path = null;
                $logType = $request->type ?? 'log';

                if ($request->hasFile('document')) {
                    $folder = "grievance_logs/{$logType}";
                    $path = $request->file('document')->store($folder, 'public');
                }

                return GrievanceLog::create([
                    'grievance_id' => $grievance_id,
                    'user_id' => auth()->id(),
                    'type' => $logType,
                    'title' => $request->title,
                    'remark' => $request->remark,
                    'document' => $path,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Log added successfully.',
                'log' => $log->load('user'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing grievance log: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a grievance log.
     */
public function updateLog(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'remark' => 'nullable|string',
        'preliminary_action_taken' => 'nullable|string|max:500',
        'final_action_taken' => 'nullable|string|max:500',
        'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:25120',
        'created_at' => 'nullable|date', // allow updating created_at
    ]);

    try {
        $log = DB::transaction(function () use ($request, $id) {
            $log = GrievanceLog::findOrFail($id);
            $path = $log->document; // Keep old path by default

            // Handle new document upload
            if ($request->hasFile('document')) {
                $folder = "grievance_logs/{$log->type}";
                $newPath = $request->file('document')->store($folder, 'public');

                // Delete old document if exists
                if (!empty($log->document) && \Storage::disk('public')->exists($log->document)) {
                    \Storage::disk('public')->delete($log->document);
                }

                $path = $newPath; // Assign new path
            }

            // Update all fields, including optional created_at
            $updateData = [
                'title' => $request->title,
                'remark' => $request->remark,
                'preliminary_action_taken' => $request->preliminary_action_taken,
                'final_action_taken' => $request->final_action_taken,
                'document' => $path,
            ];

            // Update created_at if provided
            if ($request->filled('created_at')) {
                $updateData['created_at'] = $request->created_at;
            }

            $log->update($updateData);

            return $log;
        });

        return response()->json([
            'success' => true,
            'message' => 'Log updated successfully.',
            'log' => $log->load('user'),
        ]);
    } catch (\Exception $e) {
        \Log::error('Error updating grievance log: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update log: ' . $e->getMessage(),
        ], 500);
    }
}



    /**
     * Delete a grievance log.
     */
    public function destroyLog($id)
    {
        try {
            $log = GrievanceLog::findOrFail($id);
            $log->delete();

            return response()->json(['success' => true, 'message' => 'Log deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting grievance log: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete log.'], 500);
        }
    }

    /**
     * Store assignment (and log it).
     */
    public function storeAssignment(Request $request, $grievance_id)
    {
        $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
            'department' => 'required|string|max:255',
        ]);

        try {
            $assignment = DB::transaction(function () use ($request, $grievance_id) {
                $assignedUser = User::findOrFail($request->assigned_to);
                $assignedBy = auth()->user();

                $assignment = GrievanceAssignment::create([
                    'grievance_id' => $grievance_id,
                    'assigned_to' => $assignedUser->id,
                    'assigned_by' => $assignedBy->id,
                    'department' => $request->department,
                ]);

                GrievanceLog::create([
                    'grievance_id' => $grievance_id,
                    'user_id' => $assignedBy->id,
                    'title' => 'Grievance Assigned',
                    'remark' => "Assigned to {$assignedUser->name} (Dept: {$request->department}) by {$assignedBy->name}",
                ]);

                return $assignment;
            });

            return response()->json([
                'success' => true,
                'message' => 'Assignment added successfully.',
                'assignment' => $assignment->load(['assignedUser', 'assignedByUser']),
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing grievance assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add assignment.'], 500);
        }
    }

    /**
     * Update assignment (and log it).
     */
    public function updateAssignment(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
            'department' => 'required|string|max:255',
        ]);

        try {
            $assignment = DB::transaction(function () use ($request, $id) {
                $assignment = GrievanceAssignment::findOrFail($id);

                $oldAssignedUser = User::find($assignment->assigned_to);
                $oldDepartment = $assignment->department;

                $newAssignedUser = User::findOrFail($request->assigned_to);
                $updatedBy = auth()->user();

                $assignment->update([
                    'assigned_to' => $newAssignedUser->id,
                    'department' => $request->department,
                ]);

                GrievanceLog::create([
                    'grievance_id' => $assignment->grievance_id,
                    'user_id' => $updatedBy->id,
                    'title' => 'Grievance Assignment Updated',
                    'remark' => "Reassigned from {$oldAssignedUser->name} (Dept: {$oldDepartment})
                                 to {$newAssignedUser->name} (Dept: {$request->department}) by {$updatedBy->name}",
                ]);

                return $assignment;
            });

            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully.',
                'assignment' => $assignment->load(['assignedUser', 'assignedByUser']),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating grievance assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update assignment.'], 500);
        }
    }

    /**
     * Delete assignment (and log it).
     */
    public function destroyAssignment($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $assignment = GrievanceAssignment::findOrFail($id);
                $deletedUser = User::find($assignment->assigned_to);
                $deletedDept = $assignment->department;
                $deletedBy = auth()->user();
                $grievanceId = $assignment->grievance_id;

                $assignment->delete();

                GrievanceLog::create([
                    'grievance_id' => $grievanceId,
                    'user_id' => $deletedBy->id,
                    'title' => 'Grievance Assignment Deleted',
                    'remark' => "Assignment removed (User: {$deletedUser->name}, Dept: {$deletedDept}) by {$deletedBy->name}",
                ]);
            });

            return response()->json(['success' => true, 'message' => 'Assignment deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting grievance assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete assignment.'], 500);
        }
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in-progress,resolved,rejected',
            'remark' => 'nullable|string|max:500',
        ]);

        try {
            $grievance = DB::transaction(function () use ($request, $id) {
                $grievance = Grievance::findOrFail($id);
                $oldStatus = $grievance->status;
                $newStatus = $request->status;

                $grievance->update(['status' => $newStatus]);

                GrievanceLog::create([
                    'grievance_id' => $grievance->id,
                    'user_id' => auth()->id(),
                    'title' => 'Grievance Status Updated',
                    'remark' => "Status changed from '{$oldStatus}' to '{$newStatus}' by " . auth()->user()->name . ($request->remark ? " | Remark: {$request->remark}" : ''),
                ]);

                return $grievance;
            });

            return response()->json([
                'success' => true,
                'message' => 'Grievance status updated successfully.',
                'grievance' => $grievance,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating grievance status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update status.'], 500);
        }
    }
}
