<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EscalationLog;
use App\Models\SubPackageProject;
use App\Services\Escalation\EscalationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class EscalationLogController extends Controller
{
    /**
     * Display a listing of the escalation logs.
     */
    public function index(Request $request): View
    {
        // Principal Tip: Limit the results for log tables to prevent memory crashes.
        // We fetch the latest 1000 logs. Your frontend DataTables will handle the pagination.
        $logs = EscalationLog::with(['escalatable', 'compliance'])
            ->latest() // Shorthand for orderBy('created_at', 'desc')
            ->limit(1000)
            ->get();

        return view('admin.escalation_logs.index', compact('logs'));
    }

    /**
     * Optional Bonus: Manually trigger the Escalation Engine from the UI.
     * This injects the EscalationService properly so you can run it via a button click.
     */
    public function triggerEngine(EscalationService $service): RedirectResponse
    {
        try {
            $projects = SubPackageProject::all();

            foreach ($projects as $project) {
                $service->processSubProject($project);
            }

            return redirect()
                ->route('admin.escalation_logs.index')
                ->with('success', 'Escalation Engine executed successfully. Logs have been updated.');

        } catch (\Exception $e) {
            Log::error('Manual Escalation Trigger Failed: ' . $e->getMessage());

            return redirect()
                ->route('admin.escalation_logs.index')
                ->with('error', 'Escalation Engine encountered an error: ' . $e->getMessage());
        }
    }
}