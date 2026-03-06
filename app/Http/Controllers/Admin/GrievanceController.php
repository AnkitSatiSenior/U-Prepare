<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Grievance, GrievanceLog, GrievanceAssignment, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Storage};

/**
 * Principal Architect Note: 
 * We use 's3' as the explicit disk to ensure consistency across environments.
 * All file deletions and uploads are wrapped in database transactions to prevent orphaned files.
 */
class GrievanceController extends Controller
{
    private const S3_DISK = 's3';
    private const LOG_FOLDER = 'grievance_logs';

    /**
     * Display grievances with filters.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $rolesAllowedAll = [1, 2, 7, 9];

        $query = Grievance::query();

        // Security: Role-based data scoping
        if (!in_array($user->role_id, $rolesAllowedAll) && !$request->boolean('show_all')) {
            $query->whereHas('assignments', fn($q) => $q->where('assigned_to', $user->id));
        }

        // Apply Filters
        $query->when($request->search, fn($q) => $q->where('full_name', 'like', "%{$request->search}%"))
              ->when($request->district, fn($q) => $q->where('district', $request->district))
              ->when($request->related_to, fn($q) => $q->where('grievance_related_to', $request->related_to))
              ->when($request->status && $request->status !== 'total', fn($q) => $q->where('status', $request->status))
              ->when(!$request->status, fn($q) => $q->where('status', 'pending')) // Default
              ->when($request->year, fn($q) => $q->whereYear('created_at', $request->year))
              ->when($request->month, fn($q) => $q->whereMonth('created_at', $request->month));

        $grievances = $query->with(['assignments.assignedUser'])->latest()->get();

        // Dashboard Stats
        $statsQuery = Grievance::query();
        if (!in_array($user->role_id, $rolesAllowedAll) && !$request->boolean('show_all')) {
            $statsQuery->whereHas('assignments', fn($q) => $q->where('assigned_to', $user->id));
        }

        $stats = [
            'total'    => $statsQuery->count(),
            'pending'  => (clone $statsQuery)->where('status', 'pending')->count(),
            'resolved' => (clone $statsQuery)->where('status', 'resolved')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
        ];

        $districts = Grievance::distinct()->pluck('district')->filter()->toArray();
        $relatedToOptions = Grievance::distinct()->pluck('grievance_related_to')->filter()->toArray();

        return view('admin.grievances.index', array_merge($stats, [
            'grievances' => $grievances,
            'districts' => $districts,
            'relatedToOptions' => $relatedToOptions
        ]));
    }

    /**
     * Store a grievance log with S3 upload.
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
            return DB::transaction(function () use ($request, $grievance_id) {
                $logType = $request->type ?? 'log';
                $path = null;

                if ($request->hasFile('document')) {
                    // Upload to S3 directly
                    $path = $request->file('document')->store(
                        self::LOG_FOLDER . "/{$logType}", 
                        self::S3_DISK
                    );
                }

                $log = GrievanceLog::create([
                    'grievance_id' => $grievance_id,
                    'user_id'      => auth()->id(),
                    'type'         => $logType,
                    'title'        => $request->title,
                    'remark'       => $request->remark,
                    'document'     => $path,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Log added successfully.',
                    'log'     => $log->load('user'),
                ]);
            });
        } catch (\Exception $e) {
            Log::error("S3 Upload Failed: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Storage Error.'], 500);
        }
    }

    /**
     * Update a grievance log & Rotate S3 files.
     */
    public function updateLog(Request $request, $id)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:25120',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $log = GrievanceLog::findOrFail($id);
                
                if ($request->hasFile('document')) {
                    // 1. Delete old file from S3 if it exists
                    if ($log->document && Storage::disk(self::S3_DISK)->exists($log->document)) {
                        Storage::disk(self::S3_DISK)->delete($log->document);
                    }

                    // 2. Upload new file to S3
                    $log->document = $request->file('document')->store(
                        self::LOG_FOLDER . "/{$log->type}", 
                        self::S3_DISK
                    );
                }

                $log->fill($request->only(['title', 'remark', 'preliminary_action_taken', 'final_action_taken']));
                
                if ($request->filled('created_at')) {
                    $log->created_at = $request->created_at;
                }

                $log->save();

                return response()->json(['success' => true, 'message' => 'Log updated successfully.']);
            });
        } catch (\Exception $e) {
            Log::error("S3 Update Failed: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Update Failed.'], 500);
        }
    }

    /**
     * Delete log and remove S3 file.
     */
    public function destroyLog($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $log = GrievanceLog::findOrFail($id);
                
                // Cleanup S3 storage before deleting DB record
                if ($log->document && Storage::disk(self::S3_DISK)->exists($log->document)) {
                    Storage::disk(self::S3_DISK)->delete($log->document);
                }

                $log->delete();
            });

            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }
    }

    /**
     * Status Update with Automatic Logging.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in-progress,resolved,rejected',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $grievance = Grievance::findOrFail($id);
            $oldStatus = $grievance->status;

            $grievance->update(['status' => $request->status]);

            GrievanceLog::create([
                'grievance_id' => $grievance->id,
                'user_id'      => auth()->id(),
                'title'        => 'Status Changed',
                'remark'       => "Moved from '{$oldStatus}' to '{$request->status}'",
            ]);

            return response()->json(['success' => true, 'message' => 'Status updated.']);
        });
    }
}