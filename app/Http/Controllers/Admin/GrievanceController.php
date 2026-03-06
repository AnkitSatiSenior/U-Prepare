<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Grievance, GrievanceLog, GrievanceAssignment, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Storage};

class GrievanceController extends Controller
{
    /**
     * Define the storage disk and base path centrally to enforce DRY principles.
     */
    private const STORAGE_DISK = 's3';
    private const BASE_LOG_PATH = 'grievance_logs';

    /**
     * Display grievances with declarative filters, pagination & stats.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $rolesAllowedAll = [1, 2, 7, 9];
        $canSeeAll = in_array($user->role_id, $rolesAllowedAll) || $request->boolean('show_all');

        // Main Query with declarative scopes
        $query = Grievance::query()
            ->when(!$canSeeAll, function ($q) use ($user) {
                $q->whereHas('assignments', fn($sq) => $sq->where('assigned_to', $user->id));
            })
            ->when($request->search, fn($q, $search) => $q->where('full_name', 'like', "%{$search}%"))
            ->when($request->district, fn($q, $district) => $q->where('district', $district))
            ->when($request->related_to, fn($q, $related) => $q->where('grievance_related_to', $related))
            ->when($request->status && $request->status !== 'total', fn($q) => $q->where('status', $request->status))
            ->when(!$request->status, fn($q) => $q->where('status', 'pending')) // Default status
            ->when($request->year, fn($q, $year) => $q->whereYear('created_at', $year))
            ->when($request->month, fn($q, $month) => $q->whereMonth('created_at', $month));

        $grievances = $query->with(['assignments.assignedUser'])->latest()->get();

        // Stats Query
        $statsQuery = Grievance::query()
            ->when(!$canSeeAll, function ($q) use ($user) {
                $q->whereHas('assignments', fn($sq) => $sq->where('assigned_to', $user->id));
            });

        $stats = [
            'total'    => $statsQuery->count(),
            'pending'  => (clone $statsQuery)->where('status', 'pending')->count(),
            'resolved' => (clone $statsQuery)->where('status', 'resolved')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
        ];

        // Dropdowns
        $districts = Grievance::distinct()->pluck('district')->filter()->toArray();
        $relatedToOptions = Grievance::distinct()->pluck('grievance_related_to')->filter()->toArray();

        return view('admin.grievances.index', array_merge(compact('grievances', 'districts', 'relatedToOptions'), $stats));
    }

    /**
     * Show grievance details.
     */
    public function show(Request $request, $grievance_no)
    {
        $user = auth()->user();
        $rolesAllowedAll = [1, 2, 7, 9];
        $canSeeAll = in_array($user->role_id, $rolesAllowedAll) || $request->boolean('show_all');

        $grievance = Grievance::with(['logs.user', 'assignments.assignedUser', 'assignments.assignedByUser'])
            ->where('grievance_no', $grievance_no)
            ->when(!$canSeeAll, function ($q) use ($user) {
                $q->whereHas('assignments', fn($sq) => $sq->where('assigned_to', $user->id));
            })
            ->firstOrFail();

        $users = User::all();

        return view('admin.grievances.show', compact('grievance', 'users'));
    }

    /**
     * Store a grievance log to S3.
     */
    public function storeLog(Request $request, $grievance_id)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'remark'   => 'nullable|string',
            'type'     => 'nullable|in:preliminary,final,log',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:25120',
        ]);

        try {
            $log = DB::transaction(function () use ($request, $grievance_id) {
                $logType = $request->type ?? 'log';
                $path = null;

                if ($request->hasFile('document')) {
                    $folder = self::BASE_LOG_PATH . "/{$logType}";
                    $path = $request->file('document')->store($folder, self::STORAGE_DISK);
                }

                return GrievanceLog::create([
                    'grievance_id' => $grievance_id,
                    'user_id'      => auth()->id(),
                    'type'         => $logType,
                    'title'        => $request->title,
                    'remark'       => $request->remark,
                    'document'     => $path,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Log added successfully.',
                'log'     => $log->load('user'),
            ]);
        } catch (\Exception $e) {
            Log::error('S3 Store Log Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save log or upload document.'], 500);
        }
    }

    /**
     * Update a grievance log and manage S3 file rotation.
     */
    public function updateLog(Request $request, $id)
    {
        $request->validate([
            'title'                    => 'required|string|max:255',
            'remark'                   => 'nullable|string',
            'preliminary_action_taken' => 'nullable|string|max:500',
            'final_action_taken'       => 'nullable|string|max:500',
            'document'                 => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:25120',
            'created_at'               => 'nullable|date',
        ]);

        try {
            $log = DB::transaction(function () use ($request, $id) {
                $log = GrievanceLog::findOrFail($id);
                $path = $log->document;

                if ($request->hasFile('document')) {
                    // 1. Delete old S3 document to prevent orphaned files
                    $this->deleteFromS3($log->document);

                    // 2. Upload new document to S3
                    $folder = self::BASE_LOG_PATH . "/{$log->type}";
                    $path = $request->file('document')->store($folder, self::STORAGE_DISK);
                }

                $log->update([
                    'title'                    => $request->title,
                    'remark'                   => $request->remark,
                    'preliminary_action_taken' => $request->preliminary_action_taken,
                    'final_action_taken'       => $request->final_action_taken,
                    'document'                 => $path,
                    'created_at'               => $request->filled('created_at') ? $request->created_at : $log->created_at,
                ]);

                return $log;
            });

            return response()->json([
                'success' => true,
                'message' => 'Log updated successfully.',
                'log'     => $log->load('user'),
            ]);
        } catch (\Exception $e) {
            Log::error('S3 Update Log Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update log.'], 500);
        }
    }

    /**
     * Delete a grievance log and clean up S3.
     */
    public function destroyLog($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $log = GrievanceLog::findOrFail($id);
                
                // Delete file from S3 before deleting record
                $this->deleteFromS3($log->document);

                $log->delete();
            });

            return response()->json(['success' => true, 'message' => 'Log deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('S3 Delete Log Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete log.'], 500);
        }
    }

    /**
     * Store assignment.
     */
    public function storeAssignment(Request $request, $grievance_id)
    {
        // ... (Logic remains unchanged, already well-structured)
        $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
            'department'  => 'required|string|max:255',
        ]);

        try {
            $assignment = DB::transaction(function () use ($request, $grievance_id) {
                $assignedUser = User::findOrFail($request->assigned_to);
                $assignedBy = auth()->user();

                $assignment = GrievanceAssignment::create([
                    'grievance_id' => $grievance_id,
                    'assigned_to'  => $assignedUser->id,
                    'assigned_by'  => $assignedBy->id,
                    'department'   => $request->department,
                ]);

                GrievanceLog::create([
                    'grievance_id' => $grievance_id,
                    'user_id'      => $assignedBy->id,
                    'title'        => 'Grievance Assigned',
                    'remark'       => "Assigned to {$assignedUser->name} (Dept: {$request->department}) by {$assignedBy->name}",
                ]);

                return $assignment;
            });

            return response()->json(['success' => true, 'message' => 'Assignment added successfully.', 'assignment' => $assignment->load(['assignedUser', 'assignedByUser'])]);
        } catch (\Exception $e) {
            Log::error('Error storing assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add assignment.'], 500);
        }
    }

    /**
     * Update assignment.
     */
    public function updateAssignment(Request $request, $id)
    {
        // ... (Logic remains unchanged, already well-structured)
        $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
            'department'  => 'required|string|max:255',
        ]);

        try {
            $assignment = DB::transaction(function () use ($request, $id) {
                $assignment = GrievanceAssignment::findOrFail($id);
                $oldAssignedUser = User::find($assignment->assigned_to);
                $newAssignedUser = User::findOrFail($request->assigned_to);
                $updatedBy = auth()->user();

                $assignment->update([
                    'assigned_to' => $newAssignedUser->id,
                    'department'  => $request->department,
                ]);

                GrievanceLog::create([
                    'grievance_id' => $assignment->grievance_id,
                    'user_id'      => $updatedBy->id,
                    'title'        => 'Grievance Assignment Updated',
                    'remark'       => "Reassigned from {$oldAssignedUser->name} to {$newAssignedUser->name} (Dept: {$request->department}) by {$updatedBy->name}",
                ]);

                return $assignment;
            });

            return response()->json(['success' => true, 'message' => 'Assignment updated successfully.', 'assignment' => $assignment->load(['assignedUser', 'assignedByUser'])]);
        } catch (\Exception $e) {
            Log::error('Error updating assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update assignment.'], 500);
        }
    }

    /**
     * Delete assignment.
     */
    public function destroyAssignment($id)
    {
        // ... (Logic remains unchanged, already well-structured)
        try {
            DB::transaction(function () use ($id) {
                $assignment = GrievanceAssignment::findOrFail($id);
                $deletedUser = User::find($assignment->assigned_to);
                $deletedBy = auth()->user();

                $assignment->delete();

                GrievanceLog::create([
                    'grievance_id' => $assignment->grievance_id,
                    'user_id'      => $deletedBy->id,
                    'title'        => 'Grievance Assignment Deleted',
                    'remark'       => "Assignment removed (User: {$deletedUser->name}, Dept: {$assignment->department}) by {$deletedBy->name}",
                ]);
            });

            return response()->json(['success' => true, 'message' => 'Assignment deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete assignment.'], 500);
        }
    }

    /**
     * Update Grievance Status.
     */
    public function updateStatus(Request $request, $id)
    {
        // ... (Logic remains unchanged, already well-structured)
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
                    'user_id'      => auth()->id(),
                    'title'        => 'Grievance Status Updated',
                    'remark'       => "Status changed from '{$oldStatus}' to '{$newStatus}' by " . auth()->user()->name . ($request->remark ? " | Remark: {$request->remark}" : ''),
                ]);

                return $grievance;
            });

            return response()->json(['success' => true, 'message' => 'Grievance status updated.', 'grievance' => $grievance]);
        } catch (\Exception $e) {
            Log::error('Error updating status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update status.'], 500);
        }
    }

    /**
     * Helper method to safely delete files from S3.
     */
    private function deleteFromS3(?string $path): void
    {
        if (!empty($path) && Storage::disk(self::STORAGE_DISK)->exists($path)) {
            Storage::disk(self::STORAGE_DISK)->delete($path);
        }
    }
}