<?php

namespace App\Http\Controllers\Admin;

use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Show all activity logs (without pagination)
     */
public function updateLocation(Request $request)
{
    $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    if (auth()->check()) {
        $userId = auth()->id();
        $now = now();

        // Get latest log created today within the last 10 minutes
        $log = \App\Models\ActivityLog::where('user_id', $userId)
            ->whereDate('created_at', $now->toDateString())
            ->where('created_at', '>=', $now->subMinutes(10))
            ->latest()
            ->first();

        if ($log) {
            $log->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        } else {
            // Optional: create a new log if none found in last 10 mins
            \App\Models\ActivityLog::create([
                'user_id'   => $userId,
                'action'    => 'location_update',
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'ip_address'=> $request->ip(),
                'url'       => url()->current(),
            ]);
        }
    }

    return response()->json(['success' => true]);
}


    

public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ActivityLog::with('user')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('user', fn($log) => $log->user?->name ?? 'System')
                ->addColumn('model', fn($log) => class_basename($log->model_type) . " (#{$log->model_id})")
                ->addColumn('action_badge', function ($log) {
                    $class = match ($log->action) {
                        'created' => 'bg-success',
                        'updated' => 'bg-warning text-dark',
                        'deleted' => 'bg-danger',
                        default => 'bg-info text-dark'
                    };
                    return "<span class='badge {$class}'>" . ucfirst($log->action) . "</span>";
                })
                ->addColumn('location', function ($log) {
                    if ($log->latitude && $log->longitude) {
                        // 🏗️ ARCHITECTURE FIX: Corrected Google Maps URL
                        $url = "https://www.google.com/maps?q={$log->latitude},{$log->longitude}";
                        return "<a target='_blank' href='{$url}' class='btn btn-sm btn-link p-0'>
                                    <i class='fas fa-map-marker-alt text-danger'></i> " . 
                                    number_format($log->latitude, 4) . "</a>";
                    }
                    return '<span class="text-muted">—</span>';
                })
                ->addColumn('date', fn($log) => $log->created_at->format('d M Y, h:i A'))
                ->addColumn('actions', function ($log) {
                    // 🏗️ ARCHITECTURE FIX: Pass the raw model data to a JS-friendly format
                    return '<button type="button" class="btn btn-sm btn-outline-primary view-log-btn" 
                                data-log=\'' . json_encode($log) . '\'>
                                <i class="fas fa-eye"></i> View
                            </button>';
                })
                ->rawColumns(['action_badge', 'location', 'actions'])
                ->make(true);
        }

        return view('admin.activity_logs.index');
    }


    /**
     * Delete a log if needed
     */
    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();

        return back()->with('success', 'Log deleted successfully.');
    }
}
