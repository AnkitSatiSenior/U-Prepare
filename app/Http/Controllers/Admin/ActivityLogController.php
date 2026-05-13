<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class ActivityLogController extends Controller
{
    /**
     * Handle location updates securely.
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $now = now();

        try {
            // Find an existing log within the last 10 minutes to prevent database bloating
            $log = ActivityLog::where('user_id', $userId)
                ->where('action', 'location_update')
                ->where('created_at', '>=', $now->copy()->subMinutes(10))
                ->latest()
                ->first();

            if ($log) {
                $log->update([
                    'latitude'  => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                ]);
            } else {
                ActivityLog::create([
                    'user_id'    => $userId,
                    'action'     => 'location_update',
                    'latitude'   => $validated['latitude'],
                    'longitude'  => $validated['longitude'],
                    'ip_address' => $request->ip(),
                    'url'        => url()->current(),
                ]);
            }

            return response()->json(['success' => true]);
            
        } catch (Exception $e) {
            Log::error('Failed to update activity log location', [
                'user_id' => $userId,
                'error'   => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }

    /**
     * Show all activity logs for DataTables
     */
  /**
     * Show all activity logs for DataTables
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            // 1. EXACT DB SCHEMA MAPPING: Include 'url' and 'changes'
            $query = ActivityLog::select([
                'id', 'user_id', 'action', 'model_type', 'model_id', 
                'latitude', 'longitude', 'url', 'changes', 'created_at'
            ])
            ->with(['user:id,name'])
            ->latest();

            // Apply filters
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            if ($request->filled('model_type')) {
                $query->where('model_type', 'LIKE', "%{$request->model_type}%");
            }

            try {
                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('user', fn($log) => $log->user?->name ?? 'System')
                    ->addColumn('model', fn($log) =>
                        class_basename($log->model_type) . ($log->model_id ? ' (' . $log->model_id . ')' : '')
                    )
                    ->addColumn('action_badge', function ($log) {
                        $class = match ($log->action) {
                            'created' => 'bg-success',
                            'updated' => 'bg-warning',
                            'deleted' => 'bg-danger',
                            default   => 'bg-info'
                        };

                        $actionFormatted = ucfirst(str_replace('_', ' ', $log->action));
                        return "<span class='badge {$class} text-dark'>{$actionFormatted}</span>";
                    })
                    ->addColumn('location', function ($log) {
                        if ($log->latitude && $log->longitude) {
                            $lat = number_format((float) $log->latitude, 5);
                            $lng = number_format((float) $log->longitude, 5);
                            return sprintf(
                                '<a target="_blank" href="https://www.google.com/maps?q=%s,%s">%s, %s</a>',
                                $log->latitude, $log->longitude, $lat, $lng
                            );
                        }
                        return '—';
                    })
                    // 2. PREVENT DATATABLES CRASH: Handle null URLs gracefully
                    ->addColumn('url', function($log) {
                        return $log->url 
                            ? '<a href="'. htmlspecialchars($log->url) .'" target="_blank" class="text-truncate" style="max-width: 150px; display: inline-block;">'. htmlspecialchars($log->url) .'</a>' 
                            : '<span class="text-muted">N/A</span>';
                    })
                    ->addColumn('date', fn($log) =>
                        $log->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A')
                    )
                    ->addColumn('actions', function ($log) {
                        return view('admin.activity_logs.partials.actions', compact('log'))->render();
                    })
                    // Explicitly declare raw columns so HTML renders properly
                    ->rawColumns(['action_badge', 'location', 'url', 'actions'])
                    ->make(true);
                    
            } catch (Exception $e) {
                Log::error('DataTables Exception in ActivityLog', ['error' => $e->getMessage()]);
                return response()->json([
                    'draw'            => intval($request->draw),
                    'recordsTotal'    => 0,
                    'recordsFiltered' => 0,
                    'data'            => [],
                    'error'           => 'An error occurred while fetching data.'
                ]);
            }
        }

        return view('admin.activity_logs.index');
    }

    /**
     * Delete a log if needed
     */
    public function destroy(ActivityLog $activityLog)
    {
        try {
            $activityLog->delete();
            return back()->with('success', 'Log deleted successfully.');
        } catch (Exception $e) {
            Log::error('Failed to delete activity log', ['log_id' => $activityLog->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to delete the log.');
        }
    }
}