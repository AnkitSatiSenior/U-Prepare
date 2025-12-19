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

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->model_type) {
            $query->where('model_type', 'LIKE', "%{$request->model_type}%");
        }

        return DataTables::of($query)
            ->addIndexColumn() // SL no.
            ->addColumn('user', fn($log) => $log->user?->name ?? 'System')
            ->addColumn('model', fn($log) =>
                class_basename($log->model_type) . ' (' . $log->model_id . ')'
            )
            ->addColumn('action_badge', function ($log) {
                $class = match ($log->action) {
                    'created' => 'bg-success',
                    'updated' => 'bg-warning',
                    'deleted' => 'bg-danger',
                    default => 'bg-info'
                };

                return "<span class='badge {$class} text-dark'>"
                    . ucfirst(str_replace('_', ' ', $log->action)) .
                    "</span>";
            })
            ->addColumn('location', function ($log) {
                if ($log->latitude && $log->longitude) {
                    return '<a target="_blank" href="https://www.google.com/maps?q='
                        . $log->latitude . ',' . $log->longitude . '">'
                        . number_format($log->latitude, 5) . ', '
                        . number_format($log->longitude, 5)
                        . '</a>';
                }
                return '—';
            })
            ->addColumn('date', fn($log) =>
                $log->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A')
            )
            ->addColumn('actions', function ($log) {
                return view('admin.activity_logs.partials.actions', compact('log'))->render();
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
